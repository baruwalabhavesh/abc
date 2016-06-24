<?php
/**
 * @uses add new campaigns
 * @used in pages : add-user.php,apply_filter.php,apply_pointer.php,apply_pointfilter.php,compaigns.php,edit-user.php,process.php,footer.php,templates.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where_act['active']=1;
$RSCat = $objDB->Show("categories",$array_where_act);
$array =array();

  $arr_c=file(WEB_PATH.'/merchant/process.php?num_campaigns_per_month=yes&mer_id='.$_SESSION['merchant_id']);

	$json = json_decode($arr_c[0]);

            $total_campaigns= $json->total_records;
	$addcampaign_status = $json->status;
   
                   if($addcampaign_status=="false") 
                   {
                       header("Location:".WEB_PATH."/merchant/compaigns.php?action=active");
                   }
                   
                   
$array_where_user['id'] = $_SESSION['merchant_id'];
$RS_User = $objDB->Show("merchant_user", $array_where_user);
$m_parent = $RS_User->fields['merchant_parent'];
if($RS_User->fields['merchant_parent'] == 0)
{
	$array['created_by'] = $_SESSION['merchant_id'];
        $array['active'] = 1;
	$RSStore = $objDB->Show("locations", $array);
}
else
{
	$media_acc_array = array(); 
	$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id']; 
	$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array); 
	$location_val = $RSmedia->fields['location_access'];
	
	
	//$sql = "SELECT * FROM locations  WHERE id in (select l.id from locations l where l.created_by=".$m_parent ." and l.id = ".$location_val .")";
	//$RSStore = $objDB->execute_query($sql);
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


$array = array();
$array['merchant_id'] = $_SESSION['merchant_id'];
$RSGroups = $objDB->Show("merchant_groups",$array);



$pack_data1 = $json_array = array();
$pack_data = $json_array = array();
if($RS_User->fields['merchant_parent'] == 0)
{
    $pack_data['merchant_id'] = $_SESSION['merchant_id'];
    $get_pack_data = $objDB->Show("merchant_billing",$pack_data);
    $pack_data1['id'] = $get_pack_data->fields['pack_id'];
    $get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);
}
 else 
 {
     $arr=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
        
	$main_merchant_id= $json->main_merchant_id;
	
        
    $pack_data['merchant_id'] = $main_merchant_id;
    $get_pack_data = $objDB->Show("merchant_billing",$pack_data);
    $pack_data1['id'] = $get_pack_data->fields['pack_id'];
    $get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);    
 }


/* T_10 */
if(isset($_REQUEST['template_id'])){
	$array_where['id'] = $_REQUEST['template_id'];
$RS_template = $objDB->Show("campaigns_template", $array_where);
}
/* T_10 */
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Manage Campaigns</title>
<?php //require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<script type="text/javascript" src="<?php echo ASSETS_JS ?>/m/auto-clear-and-current-location.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/main.css">

<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.carouFredSel-6.2.1-packed.js"></script>

<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/prototype-1.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/prototype-base-extensions.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/prototype-date-extensions.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/detect_timezone.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/behaviour.js"></script>
<!--<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/tooltip.js"></script>-->
<?php /*?><script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/datepicker.js"></script><?php */?>



<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/datepicker.css">


<!-- T_7 -->
<script language="javascript" src="<?=ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
<!-- T_7 -->
<!--// 369-->
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery.form.js"></script>
<!--// 369-->
<script type="text/javascript" src="<?=ASSETS ?>/tinymce/tiny_mce.js"></script>

<script language="javascript">

				
/*$(function()
{		
	$('#start_date').datepick({dateFormat: 'mm-dd-yyyy'});
	$('#expiration_date').datepick({dateFormat: 'mm-dd-yyyy'});
});*/
jQuery(document).ready(function(){
	window.tinymce.dom.Event.domLoaded = true;
	tinyMCE.init({
		// General options
		//mode : "textareas",
                mode : "exact",
		elements:"description,terms_condition",
		theme : "advanced",
		plugins : "lists,searchreplace",
		valid_elements :'p,br,ul,ol,li,sub,sup',
		// Theme options
		//theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons1 : "replace,|,bullist,numlist,|,sub,sup,|,charmap",
		//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
		//theme_advanced_toolbar_location : "top",
		//theme_advanced_toolbar_align : "left",
		//theme_advanced_statusbar_location : "bottom",
		//theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "<?=ASSETS ?>/tinymce/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "<?=ASSETS ?>/tinymce/lists/template_list.js",
		external_link_list_url : "<?=ASSETS ?>/tinymce/lists/link_list.js",
		external_image_list_url : "<?=ASSETS ?>/tinymce/lists/image_list.js",
		media_external_list_url : "<?=ASSETS ?>/tinymce/lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		},
                charLimit:1200,
		setup : function(ed) {
        //peform this action every time a key is pressed
        ed.onKeyDown.add(function(ed, e) {
			var textarea = tinyMCE.activeEditor.getContent(); 
			//alert(textarea);
			var lastcontent=textarea;
			//define local variables
			var tinymax, tinylen, htmlcount;
			//manually setting our max character limit
			tinymax = ed.settings.charLimit;
			//grabbing the length of the curent editors content
			tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
			//setting up the text string that will display in the path area
			//htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
			//if the user has exceeded the max turn the path bar red.
			//alert(tinylen);
			if (tinylen+1>tinymax && e.keyCode != 8){
				e.preventDefault();
				e.stopPropagation();
				return false;
			}		
        });
		ed.onKeyUp.add(function(ed, e) {
			//alert("up");
			var textarea = tinyMCE.activeEditor.getContent(); 
			//alert(textarea);
			var lastcontent=textarea;
			//define local variables
			var tinymax, tinylen, htmlcount;
			//manually setting our max character limit
			tinymax = ed.settings.charLimit;
			//grabbing the length of the curent editors content
			tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
			//setting up the text string that will display in the path area
			//htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
			
			var l=tinymax-tinylen;
                        
                        if(tinyMCE.activeEditor.id=="description")
                            document.getElementById("desc_limit").innerHTML=l+" characters remaining | no HTML allowed";
                        if(tinyMCE.activeEditor.id=="terms_condition")
                            document.getElementById("terms_limit").innerHTML=l+" characters remaining | no HTML allowed";
		});
		}
	});
	jQuery(".textarea_loader").css("display","none");
jQuery(".textarea_container").css("display","block");
	});
      //  alert(jQuery("#description_parent").length);
 //jQuery("#description_parent").css("float",'left');
</script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap.css" />
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

</head>

<body>
     <div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div >
<!--start header--><div>

		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
		<div id="content">   <h3><?php  echo  $merchant_msg["edit-compaign"]["Field_add_campaign"]; ?></h3>
		
	<!--// 369--><form action="process.php" method="post" enctype="multipart/form-data" id="add_campaign_form">
			
			<div class="templateclass">
				<div  align="center"><font class="slider_heading" size="3">
				<?php  echo  $merchant_msg["edit-compaign"]["Field_available_template"]; ?>
				</font></div>
<!--				<div style="overflow:auto;height: 800px;">-->
                              <div class="list_carousel">
					<ul id="foo2">
					<?php
                                        /*
					$array_where_t = array();
                                        $Sql = "select * from campaigns_template where (created_by=".$_SESSION['merchant_id']." or (created_by=0 and is_default=1))";
                                        //$RS = $objDB->Conn->Execute($Sql);
					//$array_where_t['created_by'] = $_SESSION['merchant_id'];
					$RSTemplate = $objDB->Conn->Execute($Sql);
                                        
                                         */
                                        $arr_t=file(WEB_PATH.'/merchant/process.php?btnGetAllTemplateOfMerchant=yes&mer_id='.$_SESSION['merchant_id']);
                                        if(trim($arr_t[0]) == "")
                                        {
                                                unset($arr_t[0]);
                                                $arr_t = array_values($arr_t);
                                        }
                                        $json_t = json_decode($arr_t[0]);
                                        $total_records_t= $json_t->total_records;
                                        $records_array_t = $json_t->records;
					if($total_records_t > 0)
                                        {
					
                                            foreach($records_array_t as $Row)
                                            {
						
					?>
					<li>
					        <?php
						if($Row->business_logo!="")
						{
						?>  
						<img src="<?=ASSETS_IMG ?>/m/campaign/<?=$Row->business_logo?>"  class="tempate_preview_image" /><br />
						<?php } else
						{ ?>
						    
						<img src="<?=ASSETS_IMG ?>/c/Merchant_Offer.png"  class="tempate_preview_image" /><br />
						<?php }
						?>
						<input type="radio" name="template_value" class="rg"  value="<?=$Row->id?>" <?php if(isset($_REQUEST['template_id'])){ if($Row->id == $_REQUEST['template_id'] ) { echo "checked"; } } ?> /><label class="chk_align" title="<?=$Row->title?>"><?=$Row->title?></label>
					</li>
						
					<?
                                            }
					}
                                        else
					{
						echo "<br /><p>No pre-define template found</p>";
					}
					?>
					</ul>
					<div class="clearfix"></div>
					<a id="prev2" class="prev" href="#"><img src="<?=ASSETS_IMG ?>/m/pre_add_campaign.png"></img></a>
					<a id="next2" class="next" href="#"><img src="<?=ASSETS_IMG ?>/m/next_add_campaign.png"></img></a>
						
				</div>
		</div>

	
	<input type="hidden" name="hdn_redirectpath" id="hdn_redirectpath" value="<?=WEB_PATH.'/merchant/compaigns.php?action=active'?>" />
	<input type="hidden" name="hdn_transcation_fees" id="hdn_transcation_fees" value="<?php echo $get_billing_pack_data->fields['transaction_fees']; ?>" />
            <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?php if(isset($_REQUEST['template_id'])){ echo $RS_template->fields['business_logo']; } ?>" />
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
                <table width="100%"  border="0" cellspacing="2" cellpadding="2" class="cls_left" id="table_spacer">
				  <!--<tr>
				    <td colspan="2" align="center" style="color:#FF0000; "><?=$_SESSION['msg']?></td>
			      </tr>-->
                     <tr>
					<td width="21%" align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_campaign_title"]; ?>
					</td>
                                        <td width="79%" align="left"><input type="text" placeholder="Required" name="title" id="title" maxlength="76" onkeyup="changetext1()" size="50" value="<?php if(isset($_REQUEST['template_id'])){ echo $RS_template->fields['title']; } ?>" /><span class="span_c1" >Maximum 76 characters | No HTML allowed</span></td>  
                                  </tr>
                                  <tr>
					<td width="21%" align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_campaign_tag"]; ?>
					</td>
                                        <td width="79%" align="left"><input type="text" placeholder="Enter upto 3 keywords separated by commas " name="campaign_tag" id="campaign_tag"   size="50" value=""  /> <span data-toggle="tooltip" data-placement="right" class="notification_tooltip"  data-toggle="tooltip"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_Campaign_Tag"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>  
                                  </tr>
				
                               
                                 <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_deal_value"]; ?>
					</td>
					<td align="left">
					<input type="text" name="deal_value" id="deal_value" placeholder="00" size="25"/><span class="notification_tooltip"  data-toggle="tooltip" data-placement="right" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_deal_value"]; ?>" >&nbsp;&nbsp;&nbsp</span><span id="numeric">numeric, no currency symbol</span>
					</td>
				  </tr> 
				  				  
				  <tr id="tr_discount" style="display:none;" >
					<td width="20%" align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_discount_rate"]; ?>
					</td>
					<td width="20%" align="left">
                        <input type="text" name="discount" id="discount" value="" placeholder="00" size="25"/><span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_discount_rate"]; ?>">&nbsp;&nbsp;&nbsp;</span><span id="numeric">numeric</span>
					</td>
                                       
				  </tr> 
				  				  
				  <tr id="tr_saving" style="display:none;" >
					<td width="20%" align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_saving_rate"]; ?>
					</td>
					<td width="20%" align="left">
                        <input type="text" name="saving" id="saving" value="" placeholder="00" size="25"/><span  data-toggle="tooltip" data-placement="right" class="notification_tooltip"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_saving_rate"]; ?>">&nbsp;&nbsp;&nbsp;</span><span id="numeric">numeric, no currency symbol</span>
					</td>
                                       
				  </tr> 
				  <!-- start of NPE-252-19046 -->
				<!--  <tr>
					<td align="right">Template: </td>
					<td align="left">
					<select name="template_value_" id="template_value_">
						<option value="0">-Select template-</option>
					<?
					$RSTemplate = $objDB->Show("campaigns_template");
					while($Row = $RSTemplate->FetchRow()){
					?>
						<option value="<?=$Row['id']?>" <?php if(isset($_REQUEST['template_id'])){ if($Row['id'] == $_REQUEST['template_id'] ) { echo "selected"; } } ?> ><?=$Row['title']?></option>
					<?
					}
					?>
					</select>
					</td>
				</tr>-->
				  <!-- end of NPE-252-19046 -->
				  <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_category"]; ?>
					</td>
					<td align="left">
					<select name="category_id" id="category_id">
					<option value="0">Select Category</option>
					<?
					while($Row = $RSCat->FetchRow()){
					?>
						<option value="<?=$Row['id']?>"  <? if(isset($_REQUEST['template_id'])){ if($RS_template->fields['category_id'] == $Row['id']) echo "selected"; }?>><?=$Row['cat_name']?></option>
					<?
					}
					?>
					</select><span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_category"]; ?>" >&nbsp;&nbsp;&nbsp </span>
					</td>
				  </tr>
<!--				  <tr>
					<td align="right">Campaign Log : </td>
					<td align="left">
					  <input type="file" name="business_logo" id="business_logo" />
					</td>
				  </tr>-->
                                    <tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_campaign_logo"]; ?></td>
					<td align="left">
						<!-- start of  PAY-508-28033 -->
						<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
						<div class="cls_left">
									<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
									<div id="upload" title='Attach image for the campaign' >
									<span  >Browse
									</span> 
									</div>
									</div>  <div class="browse_right_content" > &nbsp;&nbsp;<span >Or select from </span><a class="mediaclass"  > media library </a>
									<span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["tooltip_upload_image"]; ?>" >&nbsp;&nbsp;&nbsp </span></div> 
			 <!-- <input type="file" name="business_logo" id="business_logo" />-->
					 <!-- end of  PAY-508-28033   -->
					</td>
				  </tr>
				    <!-- T_7 -->
				  <tr><td align="right">&nbsp; </td>
					<td>
			
						<span id="status" ></span>
						<br/>
           
						<ul id="files" >
          
						 </ul>
					</td>
				  </tr>
				  <!-- T_7 -->
				    <!-- strat of NPE-252-19046 -->
				   
				    <!-- end of NPE-252-19046 -->
                                  <!--<tr>
					<td align="right">Max Number of Coupons : </td>
					<td align="left">
					<input type="text" name="max_coupns" id="max_coupns" value="10" />

					</td>
				  </tr>-->
                    
					<!--	
                                  <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_print_coupon_description"]; ?>
					<span class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_campaign_print_description"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
					<td align="left">
                                            <textarea id="deal_detail_description" maxlength="300" onkeyup="changetextpcd()" name="deal_detail_description" rows="10" cols="40" style="width: 89%"><?php if(isset($_REQUEST['template_id'])){ echo $RS_template->fields['print_coupon_description']; } ?></textarea></br>
					<span class="span_pcd">Maximum 300 characters</span></td>
				  </tr>
				  
				    -->
                                  
                                  <tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_deal_description"]; ?>
					<span  data-toggle="tooltip" data-placement="right" class="notification_tooltip deal_desc_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_campaign_web_description"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
					<td align="left" class="textare_td" >
									<div align="center" class="textarea_loader">
					<img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="defaul_table_loader" />
					</div>
						<div class="textarea_container" style="display: none;">	
                                 <textarea id="description" name="description" rows="15" cols="80" class="table_th_90"><?php if(isset($_REQUEST['template_id'])){ echo htmlentities($RS_template->fields['description']); } ?></textarea>
						</div>
					<span id="desc_limit">Maximum 1200 characters | no HTML allowed</span>
                                        </td>
				  </tr>
                                  
                                  <tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_terms_condition"]; ?> 
					<span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_terms_condition"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
					<td align="left" class="textare_td">
					<div align="center" class="textarea_loader">
					<img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="defaul_table_loader" />
					</div>
					<div class="textarea_container" style="display: none;">	
                         
					<textarea id="terms_condition" name="terms_condition" rows="15" cols="80" class="table_th_90"><?php if(isset($_REQUEST['template_id'])){ echo htmlentities($RS_template->fields['terms_condition']); } ?></textarea>
					</div>
					<span id="terms_limit">Maximum 1200 characters | no HTML allowed</span>
                                        </td>
				  </tr>
                                  <tr>
                                    <td colspan="2">&nbsp;
                                    </td>
                                  </tr>
				  <?php
				  if($_SESSION['merchant_info']['merchant_parent'] == 0){ ?>
				  <tr>
					<td colspan="2">
						<div class="select_location_heading">
							<?php  echo  $merchant_msg["edit-compaign"]["Field_select_location"]; ?>
						</div>
					</td>
				  </tr>
				   <tr>
					<td colspan="2">
						<!--
                    	<div class="chose_store">Please select locations where you would like launch campaign </div>
                        <div class="noof_activa">Number of Activation Code<span class="notification_tooltip" title="Total number of offers for each store.">&nbsp;&nbsp;&nbsp;</span></div>
						-->
                    </td>
                  </tr>
                   <tr>
				 
					<td align="left" colspan="2">
					<table class="locationgrid" id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1" >
						<tr class="locationgrid_heading" >
							<td class="table_th_9">&nbsp;
							</td>
							<td class="table_th_63">
							<?php  echo  $merchant_msg["edit-compaign"]["Field_location"]; ?>
							</td>
							<td>
							<?php  echo  $merchant_msg["edit-compaign"]["Field_number_of_activation_code"]; ?>
							<span data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_total_offers"]; ?>">&nbsp;&nbsp;&nbsp;</span>
							</td>
						</tr>
						
					</table>
					<div class="activationcode_container" >
					<table id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1">
					
					<!--<tr>
                        	<td colspan="3">
                            	<div class="middle_border" >&nbsp;</div>
                            </td>
                    </tr> -->
					<?
					if($RSStore->RecordCount()>0){ 
					$cnt = 0;
					while($Row = $RSStore->FetchRow()){
					?>
					  <tr>
						<td class="table_th_8">
						<input type="checkbox" id="lctn_id_<?=$Row['id']?>" name="location_id[]" value="<?=$Row['id']?>" class="camp_location" /> 
						</td>
						<td class="table_th_60">
						<?php 
							//echo $Row['location_name']
							/*
							if($Row['location_name']!="")
								echo $Row['location_name']."-<br/>";
							else {
								$arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$Row['id']);
								if(trim($arr[0]) == "")
									 {
											 unset($arr[0]);
											 $arr = array_values($arr);
									 }
									 $json = json_decode($arr[0]);
								$busines_name  = $json->bus_name;
								echo $busines_name."-<br/>";
							}
							*/
						?>
						<label for="lctn_id_<?=$Row['id']?>">
							<?=$Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip']?>
						</label>
						</td>
						<!--<td width="32%" align="right"><? if ($cnt==0) echo "Number of Activation Code: " ?></td>-->
						<td class="table_th_25"> 
                                                    <input id="num_activation_code_<?=$Row['id']?>" type="textbox"  placeholder = "00"  name="num_activation_code_<?=$Row['id']?>" value="" /> 
						
						</td>
					  </tr>
					   <!--<tr>
                        	<td colspan="3">
                            	<div class="middle_border">&nbsp;</div>
                            </td>
                        </tr> -->
					  <?   $cnt++;
					  	}
					  }
					  ?>
					  
					</table>
					</div>
					</td>
				  </tr>
				  <?php  } else { ?>
				  <tr>
					
					<td align="left"></td>
				  </tr>
                                  <tr>
					<td colspan="2">
						<div class="activationcode_container_employee" >
							<?php  echo  $merchant_msg["edit-compaign"]["Field_select_location1"]; ?>
						</div>
					</td>
				  </tr>
                                  <tr>
					<td align="left" colspan="2">
					<div> 
					<table class="locationgrid1"id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1">
					<tr class="locationgrid_heading">
						
						<td class="table_th_60" style="border_bottom:1px solid #aaaaaa;">
						<span><?php  echo  $merchant_msg["edit-compaign"]["Field_location"]; ?></span>
						</td>
						<td class="">
						<span><?php  echo  $merchant_msg["edit-compaign"]["Field_number_of_activation_code"]; ?><span>
						<span data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_total_offers"]; ?>">&nbsp;&nbsp;&nbsp;</span>
						</td>
					</tr>
                      <!--                  <tr>
                                            <td colspan="2">
                                                <div class="middle_border">&nbsp;</div>
                                            </td>
                                        </tr> -->
                                        
					
					  <tr class="employee_assigned_location">
					
						<td class="employee_assigned_location" width="41%" align="left">
                        <?
				  if($total_records>0){
					foreach($records_array as $RSStore)
					{
                                            $sub_location_id = $RSStore->id;
						//echo $RSStore->location_name;
						/*
						if($RSStore->location_name!="")
							echo $RSStore->location_name."-<br/>";
						else {
							$arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$RSStore->id);
							if(trim($arr[0]) == "")
								 {
										 unset($arr[0]);
										 $arr = array_values($arr);
								 }
								 $json = json_decode($arr[0]);
							$busines_name  = $json->bus_name;
							echo $busines_name."-<br/>";
						}
						*/					
						echo $RSStore->address.", ".$RSStore->city.", ".$RSStore->state.", ".$RSStore->zip;
					}
				  }
				  ?>
						</td>
						
						<td width="17%" >
                                                <input id="num_activation_code_<?=$sub_location_id?>" type="textbox" placeholder="00" name="num_activation_code_<?=$sub_location_id?>" value=""/> 
						<!--<select name="num_activation_code[]">
							<?
							for($i=1000; $i<=2500; $i+=500){
							?>
								<option value="<?=$i?>" <? if($Row['num_activation_code'] == $i) echo "selected";?>><?=$i?></option>
							<?
							}
							?>
							
						</select> -->
						</td>
					  </tr>
					
                                        
                                        </table>
                                        </div>
                                        
                                        
                                        </td>
                                  </tr>
				  
					<input type="hidden" name="hdn_location_id" id="hdn_location_id" value="<?=$RSStore->id?>" />
				  <?php } ?>
<!--                                  <tr>
                                      <td align="right">Time Zone :</td>
                                      <td align="left">
                                          <select readonly name='timezone' id='timezone' style='width:500px'>
                                                <option value='-12:00,0'>(-12:00) International Date Line West</option>
                                                <option value='-11:00,0'>(-11:00) Midway Island, Samoa</option>
                                                <option value='-10:00,0'>(-10:00) Hawaii</option>
                                                <option value='-09:00,1'>(-09:00) Alaska</option>
                                                <option value='-08:00,1'>(-08:00) Pacific Time (US & Canada)</option>
                                                <option value='-07:00,0'>(-07:00) Arizona</option>
                                                <option value='-07:00,1'>(-07:00) Mountain Time (US & Canada)</option>
                                                <option value='-06:00,0'>(-06:00) Central America, Saskatchewan</option>
                                                <option value='-06:00,1'>(-06:00) Central Time (US & Canada), Guadalajara, Mexico city</option>
                                                <option value='-05:00,0'>(-05:00) Indiana, Bogota, Lima, Quito, Rio Branco</option>
                                                <option value='-05:00,1'>(-05:00) Eastern time (US & Canada)</option>
                                                <option value='-04:00,1'>(-04:00) Atlantic time (Canada), Manaus, Santiago</option>
                                                <option value='-04:00,0'>(-04:00) Caracas, La Paz</option>
                                                <option value='-03:30,1'>(-03:30) Newfoundland</option>
                                                <option value='-03:00,1'>(-03:00) Greenland, Brasilia, Montevideo</option>
                                                <option value='-03:00,0'>(-03:00) Buenos Aires, Georgetown</option>
                                                <option value='-02:00,1'>(-02:00) Mid-Atlantic</option>
                                                <option value='-01:00,1'>(-01:00) Azores</option>
                                                <option value='-01:00,0'>(-01:00) Cape Verde Is.</option>
                                                <option value='00:00,0'>(00:00) Casablanca, Monrovia, Reykjavik</option>
                                                <option value='00:00,1'>(00:00) GMT: Dublin, Edinburgh, Lisbon, London</option>
                                                <option value='+01:00,1'>(+01:00) Amsterdam, Berlin, Rome, Vienna, Prague, Brussels</option>
                                                <option value='+01:00,0'>(+01:00) West Central Africa</option>
                                                <option value='+02:00,1'>(+02:00) Amman, Athens, Istanbul, Beirut, Cairo, Jerusalem</option>
                                                <option value='+02:00,0'>(+02:00) Harare, Pretoria</option>
                                                <option value='+03:00,1'>(+03:00) Baghdad, Moscow, St. Petersburg, Volgograd</option>
                                                <option value='+03:00,0'>(+03:00) Kuwait, Riyadh, Nairobi, Tbilisi</option>
                                                <option value='+03:30,0'>(+03:30) Tehran</option>
                                                <option value='+04:00,0'>(+04:00) Abu Dhadi, Muscat</option>
                                                <option value='+04:00,1'>(+04:00) Baku, Yerevan</option>
                                                <option value='+04:30,0'>(+04:30) Kabul</option>
                                                <option value='+05:00,1'>(+05:00) Ekaterinburg</option>
                                                <option value='+05:00,0'>(+05:00) Islamabad, Karachi, Tashkent</option>
                                                <option value='+05:30,0'>(+05:30) Chennai, Kolkata, Mumbai, New Delhi, Sri Jayawardenepura</option>
                                                <option value='+05:45,0'>(+05:45) Kathmandu</option>
                                                <option value='+06:00,0'>(+06:00) Astana, Dhaka</option>
                                                <option value='+06:00,1'>(+06:00) Almaty, Nonosibirsk</option>
                                                <option value='+06:30,0'>(+06:30) Yangon (Rangoon)</option>
                                                <option value='+07:00,1'>(+07:00) Krasnoyarsk</option>
                                                <option value='+07:00,0'>(+07:00) Bangkok, Hanoi, Jakarta</option>
                                                <option value='+08:00,0'>(+08:00) Beijing, Hong Kong, Singapore, Taipei</option>
                                                <option value='+08:00,1'>(+08:00) Irkutsk, Ulaan Bataar, Perth</option>
                                                <option value='+09:00,1'>(+09:00) Yakutsk</option>
                                                <option value='+09:00,0'>(+09:00) Seoul, Osaka, Sapporo, Tokyo</option>
                                                <option value='+09:30,0'>(+09:30) Darwin</option>
                                                <option value='+09:30,1'>(+09:30) Adelaide</option>
                                                <option value='+10:00,0'>(+10:00) Brisbane, Guam, Port Moresby</option>
                                                <option value='+10:00,1'>(+10:00) Canberra, Melbourne, Sydney, Hobart, Vladivostok</option>
                                                <option value='+11:00,0'>(+11:00) Magadan, Solomon Is., New Caledonia</option>
                                                <option value='+12:00,1'>(+12:00) Auckland, Wellington</option>
                                                <option value='+12:00,0'>(+12:00) Fiji, Kamchatka, Marshall Is.</option>
                                                <option value='+13:00,0'>(+13:00) Nuku'alofa</option>
                                                
                                        </select> <div  id='helptext'></div>  
                                        <input type="hidden" name="time_zone_v" id="time_zone_v" value=""/>-->
<!--<script type='text/javascript'>TORBIT.dom={get:function(a){return document.getElementsByTagName(a)},gh:function(){return TORBIT.dom.get("head")[0]},ah:function(a){TORBIT.dom.gh().appendChild(a)},ce:function(a){return document.createElement(a)},gei:function(a){return document.getElementById(a)},ls:function(a,b){var c=TORBIT.dom.ce("script");c.type="text/javascript";c.src=a;if("function"==typeof b){c.onload=function(){if(!c.onloadDone){c.onloadDone=true;b()}};c.onreadystatechange=function(){if(("loaded"===c.readyState||"complete"===c.readyState)&&!c.onloadDone){c.onloadDone=true;b()}}}TORBIT.dom.ah(c)}};(function(){var a=window.TORBIT.timing={};var b=function(){if(window.performance==void 0||window.performance.timing==void 0){k(e);h(f);return}h(d)};var c=function(){var b=window.performance.timing;var c=b.navigationStart;for(var d in b){var e=b[d];if(typeof e!="number"||e==0){continue}a[d]=e-c;var f=/(.+)End$/i.exec(d);if(f){a[f[1]+"Elapsed"]=b[d]-b[f[1]+"Start"]}}};var d=function(){c();g()};var e=function(){a.or=(new Date).getTime()-TORBIT.start_time};var f=function(){a.ol=(new Date).getTime()-TORBIT.start_time;g()};var g=function(){var b="/torbit-timing.php?";for(var c in a){b+=c+"="+a[c]+"&"}if(TORBIT.fv==1)b+="fv=1&";if(TORBIT.opt==0)b+="not_opt=1&";TORBIT.dom.ls(b)};var h=function(a){if(typeof window.onload!="function"){return window.onload=a}var b=window.onload;window.onload=function(){b();a()}};var i=false;var j=function(){};var k=function(a){j=l(a);m()};var l=function(a){return function(){if(!i){i=true;a()}}};var m=function(){if(document.addEventListener){document.addEventListener("DOMContentLoaded",j,false)}else if(document.attachEvent){document.attachEvent("onreadystatechange",j);var a=false;try{a=window.frameElement==null}catch(b){}if(document.documentElement.doScroll&&a){n()}}};var n=function(){if(i){return}try{document.documentElement.doScroll("left")}catch(a){setTimeout(n,5);return}j()};b()})();TORBIT.opt=0;TORBIT.fv=1;</script></body>
                                      </td>-->
<?php   
//  $offset=(5*60*60)+(30*60); //converting 5 hours to seconds.  
//  $dateFormat="d-m-Y h:iA";    
// date("d-m-Y h:i:s", mktime(date("H")+1,date("i"),date("s"),date("n"),date("j"),date("y")))."</br>"; 
//  $timeNdate=gmdate($dateFormat, time()+$offset);
?>
<!--
<script>    
jQuery('#timezone').change(function(){
    var text=  jQuery('#timezone :selected').val();    
    jQuery('#time_zone_v').val(text);
});
</script>
                                  </tr>-->
                    <tr>
                    <td colspan="2">&nbsp;
                    </td>
                  </tr>
				  <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_date"]; ?>
					</td></td>
					<td align="left">
					
					&nbsp; &nbsp; &nbsp; &nbsp;<?php  echo  $merchant_msg["edit-compaign"]["Field_start_date"]; ?><input readonly type="text" name="start_date" class="datetimepicker_sd" id="start_date" placeholder="Required" value="">
                                        <span data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_startdate"]; ?>">&nbsp;&nbsp;&nbsp;</span>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php  echo  $merchant_msg["edit-compaign"]["Field_end_date"]; ?><input readonly type="text" name="expiration_date" class="datetimepicker_ed" id="expiration_date" placeholder="Required" value="">
					<span  data-toggle="tooltip" data-placement="right" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_expiredate"]; ?>">&nbsp;&nbsp;&nbsp;</span>
					
					</td>
					
				  </tr>
				  
				<tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_redeem_points"]; ?></td>
					<td align="left">
						<table style="width: 100%">
							<tr>
                            <td align="left" width="50%"><input type="text" placeholder="00" id="redeem_rewards" name="redeem_rewards" /><span  data-toggle="tooltip" data-placement="right"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_redeem_points"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>
							<td align="left" width="50%"><?php  echo  $merchant_msg["edit-compaign"]["Field_available_scanflip_points"]; ?>&nbsp;
								<b><?php
									$arr=file(WEB_PATH.'/merchant/process.php?get_available_points_of_login_user=yes&mer_id='.$_SESSION['merchant_id']);
									if(trim($arr[0]) == "")
									{
										unset($arr[0]);
										$arr = array_values($arr);
									}
									$json = json_decode($arr[0]);
									$total_points= $json->available_points;
			
									echo "<span class='available_point_span' >".$total_points."</span>";	
								?></b><span data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_available_points"]; ?>">&nbsp;&nbsp;&nbsp;</span>
							</td>
						</tr>			
						</table>
						
					</td>
				</tr>
                  <tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_sharing_points"]; ?></td>
					<td align="left">
						<table style="width: 100%">
							<tr>
							<td align="left" width="50%"><input type="text" placeholder="00" id="referral_rewards" name="referral_rewards" /><span data-toggle="tooltip" data-placement="right"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_sharing_points"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>
							<td align="left" width="50%">
								<?php
								if($_SESSION['merchant_info']['merchant_parent'] == 0)
								{
								?>
									<a href="javascript:void(0)" class="p_btn" ><?php  echo  $merchant_msg["edit-compaign"]["Field_purchase_scanflip_points"]; ?></a>
									<?php
										$arr=file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
										if(trim($arr[0]) == "")
										{
												unset($arr[0]);
												$arr = array_values($arr);
										}
										$json = json_decode($arr[0]);
										$total_records= $json->total_records;
										$records_array = $json->records;
										if($total_records>0)
										{
											foreach($records_array as $Row)
											{
										?>
												<span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="$<?=$Row->price?> = <?=$Row->points?><?php  echo  $merchant_msg["edit-compaign"]["Tooltip_purchase_scanflip_points"]; ?>">&nbsp;&nbsp;&nbsp;</span>
									<?php 
											}
										}
									?>
								<?php
								}
								?>
							</td>
						</tr>			
						</table>
						
					</td>
				  </tr>
                  <tr>
                   	<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_referral_customer_limitaion"]; ?></td>
					<td align="left">
						<table >
							<tr>
							<td align="left" width="50%"><input type="text" placeholder="00" id="max_no_of_sharing" name="max_no_of_sharing" /><span  data-toggle="tooltip" data-placement="right"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_referral_customer_limit"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>
                            <td align="left"> <!--
                                    <div id="point_block" style="display:none;">
                                                  <div class="head_msg">
														<?php  echo  $merchant_msg["edit-compaign"]["Field_purchase_scanflip_points"]; ?>
												 </div>
												  <div class="point_block_parent">
                                                      
														   
															<div class="err_msg_div_40">
															  <div class="purchasepointclass status_more">
																  
															  </div>
															  <div class="success_msg" >
															  </div>
															</div>
													<?php
													$arr=file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
												if(trim($arr[0]) == "")
												{
														unset($arr[0]);
														$arr = array_values($arr);
												}
												$json = json_decode($arr[0]);
												$total_records= $json->total_records;
												$records_array = $json->records;
												if($total_records>0){
																foreach($records_array as $Row)
																{	
				
													?>
													<?php }} ?>
													<div class="purchase_padding">
														<label for="txt_price">Enter Amount : $ </label>
														<input type="text" name="txt_price" id="txt_price" />
														<div id="errmessage_price" class="fielderrormessage" ></div>
														<span id="span_con_point" style="display:none" ></span>
														<label for="card_type">Select Credit Card : </label>
														<select name="card_type" id="card_type" >
															<option value="0" >Select Credit Card Type</option>
															<option value="visa" >Visa</option>
															<option value="mastercard">Master Card</option>
														</select>
														<div id="errmessage_card_type" class="fielderrormessage" ></div>
														<label for="card_number">Enter Card Number :  </label>
														<input type="text" name="card_number" id="card_number" />
														<div id="errmessage_card_number" class="fielderrormessage" ></div>
														<label for="expirydate">Expiry Date : </label>
														<select name="exp_month" id="exp_month" >
															<option value="0" >MM</option>
															<?php 
															for($em=date("m");$em<=12;$em++)
																{
															?>
															<option>
																<?php 
																	//echo $em;
																	echo str_pad($em, 2, '0', STR_PAD_LEFT);
																?>
															</option>
															<?php
															}
															?>
														</select>
														-
														<select name="exp_year" id="exp_year" >
															<option  value="0" >YYYY</option>
															<?php 
															for($ey=date("Y");$ey<=date("Y")+20;$ey++)
															{
															?>
															<option><?php echo $ey;?></option>
															<?php
															}
															?>
														</select>
														<div id="errmessage_expirydate" class="fielderrormessage" ></div>
														<label for="card_csv">Enter CSV<span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_csv"]; ?>">&nbsp;&nbsp;&nbsp;</span> : </label>
														<input type="text" name="card_csv" id="card_csv" maxlength="3"/>
														<div id="errmessage_cardcsv" class="fielderrormessage" ></div>
													</div>
													<div >
														<input type="button" name="purchase_now" id="purchase_now" value="Continue" onclick="getpurchasenow()"/>
														<input type="button" name="purchase_cancel" id="purchase_cancel" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onclick="getcancelnow()"/>
													</div>
                                                  </div>
												   <div class="point_block_parent1" style="display:none;">
																<div class="purchase_padding">
																<label>Name on credit card : </label>
																<input type="text" name="credit_card_name" id="credit_card_name" />		<div id="errmessage_credit_card_name" class="fielderrormessage" ></div>									
																<label for="billing_address">Street Address :  </label>
																<input type="text" name="billing_address" id="billing_address" value="<?php echo $_SESSION['merchant_info']['billing_address']; ?>" />		
																<div id="errmessage_billing_address" class="fielderrormessage" ></div>									
																<label for="billing_country">Country :  </label>
																<select name="billing_country" id="billing_country" >
																	<option  value="USA" country_code="US" currency_code="USD" <?php if($_SESSION['merchant_info']['billing_country'] =="USA") { echo "selected"; } ?> >USA</option>
																	<option value="Canada" country_code="CA" currency_code="CAD" <?php if($_SESSION['merchant_info']['billing_country'] =="Canada") { echo "selected"; } ?>>Canada</option>
																</select>
																<div id="errmessage_billing_country" class="fielderrormessage" ></div>									
																	<label for="billing_state">State</label>
																<select name="billing_state" id="billing_state" >
																<?php if($_SESSION['merchant_info']['billing_country'] =="Canada") { ?>
																 <option value='AB' <? if($_SESSION['merchant_info']['billing_state'] == "AB") echo "selected";?>>AB</option>
		<option value='BC' <? if($_SESSION['merchant_info']['billing_state'] == "BC") echo "selected";?>>BC</option>
		<option value='LB' <? if($_SESSION['merchant_info']['billing_state'] == "LB") echo "selected";?>>LB</option>
		<option value='MB' <? if($_SESSION['merchant_info']['billing_state'] == "MB") echo "selected";?>>MB</option>
		<option value='NB' <? if($_SESSION['merchant_info']['billing_state'] == "NB") echo "selected";?>>NB</option>
		<option value='NF' <? if($_SESSION['merchant_info']['billing_state'] == "NF") echo "selected";?>>NF</option>
		<option value='NS' <? if($_SESSION['merchant_info']['billing_state'] == "NS") echo "selected";?>>NS</option>
		<option value='NT' <? if($_SESSION['merchant_info']['billing_state'] == "NT") echo "selected";?>>NT</option>
		<option value='NU' <? if($_SESSION['merchant_info']['billing_state'] == "NU") echo "selected";?>>NU</option>
		<option value='ON' <? if($_SESSION['merchant_info']['billing_state'] == "ON") echo "selected";?>>ON</option>
		<option value='PE' <? if($_SESSION['merchant_info']['billing_state'] == "PE") echo "selected";?>>PE</option>
		<option value='PQ' <? if($_SESSION['merchant_info']['billing_state'] == "PQ") echo "selected";?>>PQ</option>
		<option value='QB' <? if($_SESSION['merchant_info']['billing_state'] == "QB") echo "selected";?>>QB</option>
		<option value='QC' <? if($_SESSION['merchant_info']['billing_state'] == "QC") echo "selected";?>>QC</option>
		<option value='SK' <? if($_SESSION['merchant_info']['billing_state'] == "SK") echo "selected";?>>SK</option>
		<option value='YT' <? if($_SESSION['merchant_info']['billing_state'] == "YT") echo "selected";?>>YT</option>
																<?php } else { ?>
															
																	<option value="AK" <? if($_SESSION['merchant_info']['billing_state'] == "AK") echo "selected";?> >AK</option>
		    <option value="AL" <? if($_SESSION['merchant_info']['billing_state'] == "AL") echo "selected";?>>AL</option>
		    <option value="AP" <? if($_SESSION['merchant_info']['billing_state'] == "AP") echo "selected";?>>AP</option>
		    <option value="AR" <? if($_SESSION['merchant_info']['billing_state'] == "AR") echo "selected";?>>AR</option>
		    <option value="AS" <? if($_SESSION['merchant_info']['billing_state'] == "AS") echo "selected";?>>AS</option>
		    <option value="AZ" <? if($_SESSION['merchant_info']['billing_state'] == "AZ") echo "selected";?>>AZ</option>
		    <option value="CA" <? if($_SESSION['merchant_info']['billing_state'] == "CA") echo "selected";?>>CA</option>
		    <option value="CO" <? if($_SESSION['merchant_info']['billing_state'] == "CO") echo "selected";?>>CO</option>
		    <option value="CT" <? if($_SESSION['merchant_info']['billing_state'] == "CT") echo "selected";?>>CT</option>
		    <option value="DC" <? if($_SESSION['merchant_info']['billing_state'] == "DC") echo "selected";?>>DC</option>
		    <option value="DE" <? if($_SESSION['merchant_info']['billing_state'] == "DE") echo "selected";?>>DE</option>
		    <option value="FL" <? if($_SESSION['merchant_info']['billing_state'] == "FL") echo "selected";?>>FL</option>
		    <option value="FM" <? if($_SESSION['merchant_info']['billing_state'] == "FM") echo "selected";?>>FM</option>
		    <option value="GA" <? if($_SESSION['merchant_info']['billing_state'] == "GA") echo "selected";?>>GA</option>
		    <option value="GS" <? if($_SESSION['merchant_info']['billing_state'] == "GS") echo "selected";?>>GS</option>
		    <option value="GU" <? if($_SESSION['merchant_info']['billing_state'] == "GU") echo "selected";?>>GU</option>
		    <option value="HI" <? if($_SESSION['merchant_info']['billing_state'] == "HI") echo "selected";?>>HI</option>
		    <option value="IA" <? if($_SESSION['merchant_info']['billing_state'] == "IA") echo "selected";?>>IA</option>
		    <option value="ID" <? if($_SESSION['merchant_info']['billing_state'] == "ID") echo "selected";?>>ID</option>
		    <option value="IL" <? if($_SESSION['merchant_info']['billing_state'] == "IL") echo "selected";?>>IL</option>
		    <option value="IN" <? if($_SESSION['merchant_info']['billing_state'] == "IN") echo "selected";?>>IN</option>
		    <option value="KS" <? if($_SESSION['merchant_info']['billing_state'] == "KS") echo "selected";?>>KS</option>
		    <option value="KY" <? if($_SESSION['merchant_info']['billing_state'] == "KY") echo "selected";?>>KY</option>
		    <option value="LA" <? if($_SESSION['merchant_info']['billing_state'] == "LA") echo "selected";?>>LA</option>
		    <option value="MA" <? if($_SESSION['merchant_info']['billing_state'] == "MA") echo "selected";?>>MA</option>
		    <option value="MD" <? if($_SESSION['merchant_info']['billing_state'] == "MD") echo "selected";?>>MD</option>
		    <option value="ME" <? if($_SESSION['merchant_info']['billing_state'] == "ME") echo "selected";?>>ME</option>
		    <option value="MH" <? if($_SESSION['merchant_info']['billing_state'] == "MH") echo "selected";?>>MH</option>
		    <option value="MI" <? if($_SESSION['merchant_info']['billing_state'] == "MI") echo "selected";?>>MI</option>
		    <option value="MN" <? if($_SESSION['merchant_info']['billing_state'] == "MN") echo "selected";?>>MN</option>
		    <option value="MO" <? if($_SESSION['merchant_info']['billing_state'] == "MO") echo "selected";?>>MO</option>
		    <option value="MP" <? if($_SESSION['merchant_info']['billing_state'] == "MP") echo "selected";?>>MP</option>
		    <option value="MS" <? if($_SESSION['merchant_info']['billing_state'] == "MS") echo "selected";?>>MS</option>
		    <option value="MT" <? if($_SESSION['merchant_info']['billing_state'] == "MT") echo "selected";?>>MT</option>
		    <option value="NC" <? if($_SESSION['merchant_info']['billing_state'] == "NC") echo "selected";?>>NC</option>
		    <option value="ND" <? if($_SESSION['merchant_info']['billing_state'] == "ND") echo "selected";?>>ND</option>
		    <option value="NE" <? if($_SESSION['merchant_info']['billing_state'] == "NE") echo "selected";?>>NE</option>
		    <option value="NH" <? if($_SESSION['merchant_info']['billing_state'] == "NH") echo "selected";?>>NH</option>
		    <option value="NJ" <? if($_SESSION['merchant_info']['billing_state'] == "NJ") echo "selected";?>>NJ</option>
		    <option value="NM" <? if($_SESSION['merchant_info']['billing_state'] == "NM") echo "selected";?>>NM</option>
		    <option value="NV" <? if($_SESSION['merchant_info']['billing_state'] == "NV") echo "selected";?>>NV</option>
		    <option value="NY" <? if($_SESSION['merchant_info']['billing_state'] == "NY") echo "selected";?>>NY</option>
		    <option value="OH" <? if($_SESSION['merchant_info']['billing_state'] == "OH") echo "selected";?>>OH</option>
		    <option value="OK" <? if($_SESSION['merchant_info']['billing_state'] == "OK") echo "selected";?>>OK</option>
		    <option value="OR" <? if($_SESSION['merchant_info']['billing_state'] == "OR") echo "selected";?>>OR</option>
		    <option value="PA" <? if($_SESSION['merchant_info']['billing_state'] == "PA") echo "selected";?>>PA</option>
		    <option value="PR" <? if($_SESSION['merchant_info']['billing_state'] == "PR") echo "selected";?>>PR</option>
		    <option value="PW" <? if($_SESSION['merchant_info']['billing_state'] == "PW") echo "selected";?>>PW</option>
		    <option value="RI" <? if($_SESSION['merchant_info']['billing_state'] == "RI") echo "selected";?>>RI</option>
		    <option value="SC" <? if($_SESSION['merchant_info']['billing_state'] == "SC") echo "selected";?>>SC</option>
		    <option value="SD" <? if($_SESSION['merchant_info']['billing_state'] == "SD") echo "selected";?>>SD</option>
		    <option value="TN" <? if($_SESSION['merchant_info']['billing_state'] == "TN") echo "selected";?>>TN</option>
		    <option value="TX" <? if($_SESSION['merchant_info']['billing_state'] == "TX") echo "selected";?>>TX</option>
		    <option value="UT" <? if($_SESSION['merchant_info']['billing_state'] == "UT") echo "selected";?>>UT</option>
		    <option value="VA" <? if($_SESSION['merchant_info']['billing_state'] == "VA") echo "selected";?>>VA</option>
		    <option value="VI" <? if($_SESSION['merchant_info']['billing_state'] == "VI") echo "selected";?>>VI</option>
		    <option value="VT" <? if($_SESSION['merchant_info']['billing_state'] == "VT") echo "selected";?>>VT</option>
		    <option value="WA" <? if($_SESSION['merchant_info']['billing_state'] == "WA") echo "selected";?>>WA</option>
		    <option value="WI" <? if($_SESSION['merchant_info']['billing_state'] == "WI") echo "selected";?>>WI</option>
		    <option value="WV" <? if($_SESSION['merchant_info']['billing_state'] == "WV") echo "selected";?>>WV</option>
		    <option value="WY" <? if($_SESSION['merchant_info']['billing_state'] == "WY") echo "selected";?>>WY</option>
															
																<?php }  ?>
																	</select>
																<div id="errmessage_billing_state" class="fielderrormessage" ></div>
																
																<label for="billing_city">City :  </label>
																<input type="text" name="billing_city" id="billing_city" value=<?php echo $_SESSION['merchant_info']['billing_city']; ?> />
																<div id="errmessage_billing_city" class="fielderrormessage" ></div>
																<label for="billing_postalcode">Postal Code :</label>
																<input type="text" maxlength="15"  name="billing_postalcode" id="billing_postalcode" value ="<?php  echo $_SESSION['merchant_info']['billing_zip']; ?>" />
																<div id="errmessage_billing_postalcode" class="fielderrormessage" ></div>
																<input type="checkbox"  name="save_billing" id="save_billing" checked />Save this billing address to my Scanflip account 
															</div>
															<div >
																<input type="button" name="Purchase" id="purchase_now1" value="<?php echo $merchant_msg['edit-compaign']['button_purchase_now'];?>" onclick="getpurchasenow1()"/>
																<input type="button" name="Cancel" id="purchase_cancel" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onclick="getcancelnow()"/>
																<input type="button" name="Back" id="purchase_back" value="<?php echo "Back" ;?>" onclick="getbacknow()"/>
															</div>
												  </div>
														<div class="point_success_div" style="display:none;">
															
															<div><input type="button" name="purchase_cancel" id="purchase_cancel" value="<?php echo "OK";?>" onclick="getcancelnow()"/></div>
														</div>
														<div class="point_error_div" style="display:none;">
															<div class="error_msg" ></div>
															<div><input type="button" name="purchase_cancel" id="purchase_cancel" value="<?php echo "OK";?>" onclick="getcancelnow()"/>&nbsp;&nbsp;<input type="button" name="Back" id="purchase_back" value="<?php echo "Back" ;?>" onclick="getbacknow()"/></div>
														</div>
									</div>
                        -->     </td>
						</tr>			
						</table>
						
					</td>
				  </tr>
<!--                                  <tr >
					<td align="right">&nbsp; </td>
					
                                  </tr>-->
                                   <tr>
				    <td align="right" valign="top">&nbsp;  </td>
                                    <td align="left" valign="top">
                                        <input type="radio" id="groupid" name="is_walkin" class="is_walkin" value="0" style="float:left;" checked onclick ="check_is_walkin(this.value)" /> <label for="groupid" class="chk_align1"> <?php  echo  $merchant_msg["edit-compaign"]["Field_group"]; ?></label>
                                        <input type="radio" id="walkinid" name="is_walkin" class="is_walkin" value="1" style="float:left;" onclick="check_is_walkin(this.value)" /><label for="walkinid" class="chk_align1"> <?php  echo  $merchant_msg["edit-compaign"]["Field_walking"]; ?> </label><span title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_walkin"]; ?>"c data-toggle="tooltip" data-placement="right"  class="notification_tooltip" >&nbsp;&nbsp;&nbsp;</span>
                                    </td>
                                   </tr>
				  <tr id="group">
				    <td align="right" valign="top"><?php  echo  $merchant_msg["edit-compaign"]["Field_camapaign_visibility"]; ?><span  data-toggle="tooltip" data-placement="right" data-html="true"   class="notification_tooltip" data-html="true" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_visibility"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
				    <td align="left" valign="top">
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
		<input type="checkbox" id="assign_group" name="assign_group" value="1" class="chk_private" />
                <label class="chk_align" for="assign_group"><?php  echo  $merchant_msg["edit-compaign"]["Field_public"]; ?></label>
	</td>
    
  </tr>
  
				  <tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_camapaign_limitation"]; ?></td>
					<td align="left">
					<input type="radio" value="1" id="one_per_customer" name="number_of_use" checked  />
					<label for= "one_per_customer" class="chk_align"><?php  echo  $merchant_msg["edit-compaign"]["Field_one_per_customer"]; ?></label>
					
					<input type="radio" value="2" id="one_per_customer_per_day" name="number_of_use" />
					<label for="one_per_customer_per_day" class="chk_align"><?php  echo  $merchant_msg["edit-compaign"]["Field_one_per_customer_per_day"]; ?></label>
					
					<input type="radio" value="3" id="multiple_use" name="number_of_use" />
					<label for="multiple_use" class="chk_align"><?php  echo  $merchant_msg["edit-compaign"]["Field_redeem_point_visit"]; ?></label>
					</td>
				  </tr>
				  <tr>
					<td align="right"><label for="new_customer_only" class="chk_align"><?php  echo  $merchant_msg["edit-compaign"]["Field_valid_new_customer"]; ?></label></td>
					<td align="left">
					<input type="checkbox" name="new_customer_only" id="new_customer_only"  /> 
					
					</td>
				  </tr>
				  
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<input type="submit" name="btnAddCampaigns" id="btnAddCampaigns" value="<?php echo $merchant_msg['index']['btn_save'];?>" onclick="mycall()" >
						<!--// 369-->
                        <script>
						function btncanCampaign()
						{                                                
                            window.location="<?=WEB_PATH?>/merchant/compaigns.php?action=active";
						}
						function mycall()
						{
							    jQuery('#description').val(tinyMCE.get('description').getContent());
							    //jQuery('#deal_detail_description').val(tinyMCE.get('deal_detail_description').getContent());
								jQuery('#terms_condition').val(tinyMCE.get('terms_condition').getContent());
						}
                        </script>
                                                <input type="submit" name="btnCanCampaigns" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanCampaign()">
                        <!--// 369-->
                        <input type="hidden" id="required_point" name="required_point" value="">
                        <input type="hidden" id="remaining_point" name="remaining_point" value="">
                                          <script type="text/javascript">
										  
										 
										    var alert_msg = "";
 function validation(filed,message)
 {
	var f="";
	if(jQuery(filed).val()=="")
	{
		 alert_msg += message;
		 f = "false";
	}
	
	return f;
 }
 
 function validation_negative(filed,message)
 {
	var f="";
       
	if(jQuery(filed).val()<0)
	{
		 alert_msg += message;
		 f = "false";
	}
	
	return f;
 }
 
 jQuery("#btnAddCampaigns").click(function(){
 
 //open_popup("Notificationloader");
 var alert_msg="<?php  echo  $merchant_msg["index"]["validating_data"]; ?>";
		var head_msg="<div class='head_msg'>Message</div>";
		var content_msg="<div class='content_msg validatingdata' style='background:white;'>"+alert_msg+"</div>";
		jQuery("#NotificationloadermainContainer").html(content_msg);
		
		jQuery("#NotificationloaderFrontDivProcessing").css("display","block");
		jQuery("#NotificationloaderBackDiv").css("display","block");
		jQuery("#NotificationloaderPopUpContainer").css("display","block");
		
											var alert_msg = "";
                                                var total_no_of_activation_code = 0;
                                                var flag="true";
                                                var flag_hdn="true";
                                                var total_no_of_locations  = 0;
                                                var numbers = /^[0-9]+$/;
                               
                                                var currencyReg = /^\$?[0-9]+(\.[0-9][0-9])?$/;
						var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
                                                
                                                //alert(flag+"1");
                                                //alert(jQuery(".camp_location").length);


                                                jQuery(".camp_location").each(function() {
													  var loc_id = jQuery(this).val();
													  if(jQuery(this).attr("checked")=="checked"){
														  total_no_of_locations = total_no_of_locations + 1;
													  }
                                                }); 
                                                 //alert(total_no_of_locations + "Total Number of locations");
                                                  if(jQuery(".camp_location").length != 0)
                                                  {
                                                      if(total_no_of_locations <= 0)
                                                     {
                                                         flag="false";
                                                          alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_at_least_one_location"]; ?></div>";
                                                     }


                                                  }

                                                   
                                                     // return false;
                                                  var min_share_point="<?php echo $get_billing_pack_data->fields['min_share_point'] ?>";
                                                  var min_reward_point="<?php echo $get_billing_pack_data->fields['min_reward_point'] ?>";
												
                                                  var referral_rewards="0";
												  
                                                      var f = validation("#title","<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_campaign_title"]; ?></div>");
                                                      if(f == 'false'){
                                                          flag = "false";
                                                           alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_campaign_title"]; ?></div>";
                                                      }
                                                      
                                                      

                                                      //flag = validation("#discount","* Enter a Discount Rate.\n");
                                                      //alert(flag+"2");
							var tag_value=jQuery("#campaign_tag").val();
                                                        if(tag_value == "")
                                                            {
                                                                //flag="true";
                                                            }
                                                            else
                                                                {
                                                                    if(hastagRef.test(tag_value))
                                                                        {
                                                                            //flag="true";
                                                                        }
                                                                        else
                                                                            {
                                                                                alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_Tag"]; ?></div>";
                                                                                flag="false";
                                                                            }
                                                                }
																
													  //alert(jQuery("#deal_value").val());
													var deal_value=jQuery("#deal_value").val();
                                                       if(jQuery("#deal_value").val()=="")
                                                      {
                                                         // alert("In If");
                                                          //flag = validation("#redeem_rewards","<div>* Please Enter Reedem Points.</div>");
                                                          alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_deal_value"]; ?></div>";
                                                          flag="false";
                                                      }
                                                      else
                                                      {
                                                              if (!currencyReg.test(jQuery("#deal_value").val())) {
                                                               alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_deal_value_proper"]; ?></div>";
                                                               flag="false";
                                                              }
                                                              else {
																var n=deal_value.indexOf("$");
																if(n!="-1")
																{
																	jQuery("#deal_value").val((jQuery("#deal_value").val()).substring(n+1, (jQuery("#deal_value").val()).length));
																}
                                                              }
																
                                                        //  alert("In Else");
                                                        //  numbers_decimal = ^\d+(\.\d{1,2})?$ ;
                                              //           var rgexp = new RegExp("^\d*([.]\d{2})?$");
                                              //        if(! jQuery("#deal_value").val().match(rgexp)){
                                              ////		    if(parseInt(jQuery("#deal_value").val())<=0)
                                              ////		    {
                                              //			//flag = validation_negative("#redeem_rewards","<div>* Reedem Point Can't Be Negative.</div>");
                                              //			alert_msg +="<div>* Please Enter Proper Deal Value.</div>";
                                              //			flag="false";
                                              //		    }

                                                      }
													 // alert(jQuery("#deal_value").val());
													 var discount=jQuery("#discount").val();
                                                       if(discount=="")
                                                      {
                                                         // alert("In If");
                                                          //flag = validation("#redeem_rewards","<div>* Please Enter Reedem Points.</div>");
                                                          alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_discount"]; ?></div>";
                                                          flag="false";
                                                      }
													  if(parseInt(discount)>100)
													  {
															alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_proper_discount"]; ?></div>";
                                                          flag="false";
													  }
													  var saving=jQuery("#saving").val();
													 if(saving=="")
                                                      {
                                                         // alert("In If");
                                                          //flag = validation("#redeem_rewards","<div>* Please Enter Reedem Points.</div>");
                                                          alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_saving"]; ?></div>";
                                                          flag="false";
                                                      }
													  
													  if(parseFloat(deal_value)==0 && (parseFloat(discount)>0 || parseFloat(saving)>0))
													  {
															alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_deal_value_proper"]; ?></div>";
                                                          flag="false";
													  }
													  if(parseFloat(deal_value)>0 && (parseFloat(discount)==0 || parseFloat(saving)==0))
													  {
															alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_proper_discount_saving"]; ?></div>";
                                                          flag="false";
													  }
													  
													  
													  var cat_id=jQuery('#category_id option:selected').val();
													 if(cat_id==0)
													 {
														flag="false";
														alert_msg+="<div><?php echo $merchant_msg['edit-compaign']['Msg_select_campaign_category']; ?></div>";
													 }
                                                      var term_box_val=jQuery('#terms_condition').val(tinyMCE.get('terms_condition').getContent());
                                                      var final_term_val=term_box_val.val();
                                                       /*
													   if(final_term_val=="")
                                                       {

                                                              alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_terms_and_condition"]; ?></div>";
                                                              flag="false";
                                                       }
													   */
														
														if(jQuery('#groupid').is(':checked'))
														{	
                                                         if (jQuery(".chk_private").is(':checked')) {

                                                              //flag="true";
                                                           } else {
                                                                   jQuery("input[name^='hdn_campaign_type1']" ).each(function( index ) {
                                                                       var hdn_campaign_type_value=jQuery(this).val();
                                                                       if(hdn_campaign_type_value == 0)
                                                                       {
                                                                               flag_hdn="false";

                                                                       }
                                                                   });
                                                                   if(flag_hdn == "false")
                                                                   {
                                                                        flag="false";
                                                                        alert_msg+="<?php  echo  $merchant_msg["edit-compaign"]["Msg_select_campaign_visibility"]; ?>";
                                                                   }
                                                                   else
                                                                   {
                                                                       //flag="true";
                                                                   }  
                                                           }
														}
														

                                                      //flag = validation("#deal_value","<div>* Please Enter Deal Value.</div>");

                                                      /*Activation code validation */
                                                      if(jQuery(".camp_location").length==0)
                                                  {


                                                      var loc_act_code1 =jQuery('input[name^="num_activation_code_"]').val();
                                                      var loc_act_code = parseInt(jQuery('input[name^="num_activation_code_"]').val());
                                                   // alert(loc_act_code+"========"+loc_act_code1.match(numbers) );
                                                   // return false;

                                                      if(loc_act_code1=="" || loc_act_code1=="NaN")
                                                      {

                                                          alert_msg += "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code"]; ?></div>";
                                                          flag="false"; 
                                                        //  return false;
                                                      }
                                                      else if(! loc_act_code1.match(numbers) || loc_act_code==0)
                                                      {
                                                          alert_msg += "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code_not_number"]; ?></div>";
                                                          flag="false";  
                                                         // return false;
                                                      }
                                                      else
                                                      {          
                                                          total_no_of_activation_code = total_no_of_activation_code + loc_act_code;           
                                                      }
                                                  }
                                                  else
                                                  {
                                                      var activation_msg = "";
														var dist_list_id_str="";
                                                      jQuery(".camp_location").each(function() {

                                                            if(jQuery(this).attr("checked")=="checked")
                                                            {

                                                                 var loc_id = jQuery(this).val();

                                                                                      // 16-09-2013

                                                                                      jQuery.ajax({
                                                                                    type: "POST",
                                                                                    url: "<?=WEB_PATH?>/merchant/process.php",
                                                                                    data: "loc_id=" + loc_id +"&check_location_active=yes",
                                                                                                                        async:false,
                                                                                    success: function(msg) {
                                                                                        var obj=jQuery.parseJSON(msg);
                                                                                                                                //alert(obj.status);
                                                                                                                                //alert(obj.message);
                                                                                                                                //alert_msg +="<div>* "+ obj.message +"</div>";
                                                                                                                                if(obj.status=="false")
                                                                                                                                {
                                                                                                                                      flag="false";
                                                                                                                                      alert_msg +="<div>* "+ obj.message +"</div>";
                                                                                                                                }
                                                                                    }
                                                                                      });

                                                                                      // 16-09-2013
																					  
																					  // 26-09-2013 check dist list active
																								
																						jQuery("ul[id^='locationgroup_"+loc_id+"']").each(function( index ) {
																							//alert(jQuery(this).attr("id"));
																							jQuery(this).find("li").each(function( index ) {
																								//alert(jQuery(this).attr("class"));
																								//alert(jQuery(this).find("input[type='checkbox']").attr("value"));
																								var dist_list_id=jQuery(this).find("input[type='checkbox']").attr("value");
																								
																								
																								if(jQuery("#chk_location_groups_"+dist_list_id).is(":checked"))
																								{
																									dist_list_id_str=dist_list_id_str+dist_list_id+",";
																									/*
																									jQuery.ajax({
																												  type: "POST",
																												  url: "<?=WEB_PATH?>/merchant/process.php",
																												  data: "dist_list_id=" + dist_list_id +"&check_dist_list_active=yes",
																												  async:false,
																												  success: function(msg) {
																													  var obj=jQuery.parseJSON(msg);
																													  //alert(obj.status);
																													  //alert(obj.message);
																													  //alert_msg +="<div>* "+ obj.message +"</div>";
																													  if(obj.status=="false")
																													  {
																														flag="false";
																														alert_msg +="<div>* "+ obj.message +"</div>";
																													  }
																												  }
																									});
																									*/
																								}
																							
																							});
																						});
																						
																						// 26-09-2013 check dist list active

                                                                 if(jQuery("#num_activation_code_"+loc_id).val()=="" || jQuery("#num_activation_code_"+loc_id).val()=="NaN")
                                                                 {
                                                                     activation_msg = "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code"]; ?></div>";                 
                                                                     //alert_msg += "<div>* Please Enter Activation Code.</div>";
                                                                     flag="false";
                                                                     return;
                                                                 }
                                                                 else if(! jQuery("#num_activation_code_"+loc_id).val().match(numbers) || jQuery("#num_activation_code_"+loc_id).val()==0 )
                                                                  {
                                                                       activation_msg = "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code_not_number"]; ?></div>";
                                                                        // alert_msg += "<div>* Please Enter Proper Activation Code.</div>";
                                                                         flag="false";
                                                                         return;
                                                                  }
                                                                  else
                                                                  {
																
                                                                          total_no_of_activation_code = total_no_of_activation_code + parseInt(jQuery("#num_activation_code_"+loc_id).val());
                                                                          //flag="false";
                                                                  }
                                                            }
                                                        });
														// 30 09 2013
														
														/*
														alert(dist_list_id_str);
														dist_list_id_str=dist_list_id_str.substr(0,dist_list_id_str.length-1);
														alert(dist_list_id_str);
														dist_list_id_str="355,356";
														*/
														dist_list_id_str=dist_list_id_str.substr(0,dist_list_id_str.length-1);
														//alert(dist_list_id_str);
														if(dist_list_id_str != "")
														{
														jQuery.ajax({
																	  type: "POST",
																	  url: "<?=WEB_PATH?>/merchant/process.php",
																	  data: "dist_list_id_str=" + dist_list_id_str +"&check_dist_list_active_bulk=yes",
																	  async:false,
																	  success: function(msg) {
																		  var obj=jQuery.parseJSON(msg);
																		  //alert(obj.status);
																		  //alert(obj.message);
																		  //alert_msg +="<div>* "+ obj.message +"</div>";
																		  if(obj.status=="false")
																		  {
																			flag="false";
																			//alert_msg +="<div>* "+ obj.message +"</div>";
																			alert_msg += obj.message ;
																		  }
																	  }
														});
														}
														// 30 09 2013
                                                        alert_msg += activation_msg;
                                                  }
                                                  /*End Activation code validation */

                                              /* $ code	*/
                                                  if(jQuery("#start_date").val()=="")
                                                  {
                                                      flag="false";
                                                           alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_start_date"]; ?></div>";
                                                  }
                                                  if(jQuery("#expiration_date").val()=="")
                                                  {
                                                      flag="false";
                                                           alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_expiration_date"]; ?></div>";
                                                  }
                                              /* End $ code	*/    
                                                  if(jQuery("#redeem_rewards").val()=="")
                                                  {
                                                      //flag = validation("#redeem_rewards","<div>* Please Enter Reedem Points.</div>");
                                                      alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_redeem_points"]; ?></div>";
                                                      flag="false";
                                                  }
                                                  else
                                                  {
                                                      numbers = /^[0-9]+$/;
                                                      //if(jQuery("#redeem_rewards").val().match(numbers)){
                                                          if(! jQuery("#redeem_rewards").val().match(numbers))
                                                              {
                                                                  //flag = validation_negative("#redeem_rewards","<div>* Reedem Point Can't Be Negative.</div>");
                                                                  alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_redeem_points_not_number"]; ?></div>";
                                                                  flag="false";
                                                              }
                                                    //  }
                                              //        else{
                                              //              alert_msg +="<div>* Reedem Point Can't Be Negative.</div>";
                                              //		    flag="false";
                                              //        }


                                                  }

                                                  if(jQuery("#referral_rewards").val()=="")
                                                  {
                                                      //flag = validation("#referral_rewards","<div>* Please Enter Sharing Points.</div>");
                                                      alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_sharing_points"]; ?></div>";
                                                      flag="false";

                                                  }
                                                  else
                                                  {
                                                      if(! jQuery("#referral_rewards").val().match(numbers))
                                                              {
                                                      //if(parseInt(jQuery("#referral_rewards").val())<=0)
                                                              //{
                                                                      //flag = validation_negative("#referral_rewards","<div>* Sharing Point Can't Be Negative.</div>");
                                                                      alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_sharing_points_not_number"]; ?></div>";
                                                                      flag="false";
                                                              }

                                                  }

                                                  //alert("referral "+flag);
                                                      //alert("referral "+alert_msg);
                                                      //alert(parseInt(jQuery("#max_no_of_sharing").val()));

                                                  if(jQuery("#max_no_of_sharing").val()=="")
                                                  {
                                                      alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_max_sharing_customers"]; ?></div>";

                                                       //flag = validation("#max_no_of_sharing","<div>* Please Enter Max Number of Sharing For New Customer Registration.</div>");
                                                       flag="false";
                                                  }
                                                  else
                                                  {
                                                      numbers = /^[0-9]+$/;
                                                     // if(jQuery("#max_no_of_sharing").val().match(numbers)){
                                                              if(! jQuery("#max_no_of_sharing").val().match(numbers))
                                                              {
                                                                  alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_max_sharing_customers_not_negative"]; ?></div>";
                                                                  flag="false";
                                                                  //flag = validation_negative("#max_no_of_sharing","<div>* Max Number of Sharing Can't Be Negative.</div>");
                                                              }
                                              //        }
                                              //        else{
                                              //             alert_msg +="<div>* Max Number of Sharing Can't Be Negative.</div>";
                                              //		    flag="false";
                                              //        }
                                                  }

                                                  //alert("noofsharing "+flag);
                                                      //alert("noofsharing "+alert_msg);

                                                      if(parseInt(jQuery("#redeem_rewards").val())==0)
                                                      {
                                                              flag="false";
                                                              alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_redeem_point_negative"]; ?></div>";

                                                      }
                                                      else if(parseInt(jQuery("#redeem_rewards").val())<min_reward_point)
                                                      {

                                                          flag="false";
                                                          alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_minimum_redeem_point"]; ?>"+ min_reward_point +"</div>";

                                                      }
                                                       if(min_share_point == "0")
                                                       {	
                                                          jQuery("#max_no_of_sharing").val(referral_rewards);
                                                       }
                                                      if(parseInt(jQuery("#referral_rewards").val()) < min_share_point)
                                                       {
                                                              flag="false";
                                                              alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_minimum_sharing_point"]; ?>"+ min_share_point +"</div>";
                                                        }






                                                  var radio_val1=jQuery("#one_per_customer");
                                                  var radio_val2=jQuery("#one_per_customer_per_day");
                                                  var radio_val3=jQuery("#multiple_use");
                                                  var new_customer_only=jQuery("#new_customer_only");

												if(radio_val1.prop('checked') == false && radio_val2.prop('checked') == false && radio_val3.prop('checked') == false)
												{
													flag="false";
													alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_select_campaign_limitation"]; ?></div>";
												}
	
                                                  if(radio_val2.prop('checked') == true)
                                                  {
                                                      if(new_customer_only.prop('checked') == true)
                                                      {
                                                              flag="false";
                                                              alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_new_customer_with_one_per_customer"]; ?></div>";
                                                      }
                                                  }

                                                  if(radio_val3.prop('checked') == true)
                                                  {
                                                      if(new_customer_only.prop('checked') == true)
                                                      {
                                                              flag="false";
                                                              alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_new_customer_with_one_per_customer"]; ?></div>";
                                                      }
                                                  }
                                                  if(new_customer_only.prop('checked') == true)
                                                  {
                                                      jQuery("#new_customer_only").val('1');
                                                  }
                                                  else
                                                  {
                                                          jQuery("#new_customer_only").val('0');
                                                  }
												/*		
												if (!currencyReg.test(jQuery("#deal_value").val())) 
												{
												   //alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_deal_value_proper"]; ?></div>";
												   flag="false";
												}
												else 
												{
													var n=deal_value.indexOf("$");
													if(n!="-1")
													{
														jQuery("#deal_value").val((jQuery("#deal_value").val()).substring(n+1, (jQuery("#deal_value").val()).length));
													}
												 }
												*/
											//	alert(jQuery("#title").val()+"====");
												 if(jQuery("#title").val()=="" || jQuery("#redeem_rewards").val()=="" || jQuery("#referral_rewards").val()=="" ||  jQuery("#max_no_of_sharing").val()=="" || jQuery("#start_date").val()=="" || jQuery("#expiration_date").val()=="")
												  {
											   //   alert("in if");
												  flag="false";
												}

                                              //    alert(alert_msg);
                                              //   alert("last "+flag+"===");
                                              //    return false;
											     if(jQuery("#redeem_rewards").val()!="" && jQuery("#redeem_rewards").val()!="0" && jQuery("#referral_rewards")!="" && jQuery("#referral_rewards")!="0" &&
                                                              jQuery("#max_no_of_sharing").val()!="")
                                                  {
                                                   //   alert("Redeem rewards"+  parseInt(jQuery("#redeem_rewards").val()) );
                                                     // alert(total_no_of_activation_code);
                                                     // alert("REferral rewards"+  parseInt(jQuery("#referral_rewards").val()) );
                                                      // alert("max_no_of_sharing"+  parseInt(jQuery("#max_no_of_sharing").val()) );
													  
													  /** cut transaction fees ***/
																//alert(jQuery("#hdn_transcation_fees").val());
																if(parseInt(jQuery("#hdn_transcation_fees").val()) != 0)
																{
																	var total1 = ( parseInt(jQuery("#redeem_rewards").val()) * total_no_of_activation_code )+ ( total_no_of_activation_code * parseInt(jQuery("#hdn_transcation_fees").val()));
																  }
																  else
																  {
																	var total1 =  parseInt(jQuery("#redeem_rewards").val()) * total_no_of_activation_code;
																  }
														      var total2 =  parseInt(jQuery("#referral_rewards").val()) * parseInt(jQuery("#max_no_of_sharing").val());
														/*** cut transaction fees **/
                                                              var block_point = total1 + total2;
															  
                                                              var purchase_point = parseInt(jQuery(".available_point_span").html());
                                                             // alert("block_point"+block_point+"===available_point_span"+purchase_point );
                                                              if(purchase_point < block_point)
                                                              {
                                                                      flag="false";
                                                                      //alert_msg+="* You Can Not Create Campaign As a Required Point Is More Than Available Point.\n  Your Required Point Is : " + block_point + "\n";
                                                                      alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_not_activate_campaign"]; ?></div>";
                                                              }
                                                              else
                                                              {
                                                                      //flag="true";
                                                                      jQuery("#required_point").val(block_point);
                                                                      var remain = purchase_point - block_point;
                                                                      jQuery("#remaining_point").val(remain);
																	 // alert(jQuery("#remaining_point").val());
                                                              }
                                                      }
                                                  if(flag=="true")
                                                  {
                                                      var status=0;
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
																status=1;  			
                                                                 window.location.href=obj.link;
                                                             }
                                                             else if(obj.approve == 0)
                                                                 {
                                                                      
                                                                      var alert_msg="Merchant Status: Blocked , Please contact Scanflip";
                                                                       var head_msg="<div class='head_msg'>Message</div>"
                                                                        var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
                                                                        var href="<?php echo WEB_PATH; ?>/merchant/logout.php";
                                                                        var footer_msg="<div><hr><a href='"+href+"' class='msg_popup_cancel_anchor'><?php echo $merchant_msg['index']['fancybox_ok'];?></a></div>";
                                                                        jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);


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
                                                                                                },
                                                                                                afterClose: function () {
                                                                                                        window.location.href=href;
                                                                                                }
            					
                                                                        });
            
                                                                        status=1;
                                                                      
                                                                 }
                                                                 else if(obj.approve == 2)
                                                                {
                                                                        var alert_msg="Merchant Status: Pending , Please contact Scanflip";
                                                                      var head_msg="<div class='head_msg'>Message</div>"
                                                                       var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
                                                                       var href="<?php echo WEB_PATH; ?>/merchant/my-account.php";
                                                                       var footer_msg="<div><hr><a href='"+href+"' class='msg_popup_cancel_anchor'><?php echo $merchant_msg['index']['fancybox_ok'];?></a></div>";
                                                                       jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);


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
                                                                                               },
                                                                                               afterClose: function () {
                                                                                                       window.location.href=href;
                                                                                               }

                                                                       });

                                                                       flaglogin=1;
																		status=1;  		
                                                                }
                                                                 else
                                                                 {
                                                                       status=0;  
                                                                 }
                                                        }
                                                      });
                                                      if(status == 1)
                                                          {
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
                                                  else 
                                                  {

													close_popup("Notificationloader");
                                                      //alert(alert_msg);
                                                      var head_msg="<div class='head_msg'>Message</div>"
                                                      var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
                                                      var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                      jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);


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

                                        
                                        
                                
                         
                    
  
    
 });
 jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
</script>
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
		?>
		<!--end of footer--></div>
	
</div>
<!-- start of image upload popup div PAY-508-28033 -->
 <div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="NotificationBackDiv" class="divBack">
                                        </div>
                                        <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left:30%;top: 11%;">
                                                
                                                                <div class="modal-close-button" style="visibility: visible;">
                                                             
                                                                    <a  tabindex="0" onclick="close_popup('Notification');" id="fancybox-close" style="display: inline;"></a>
                                                                </div>
                                                <div id="NotificationmainContainer" class="innerContainer" style="height:330px;width:600px">
                                                        <div class="main_content"> 	
                                                         <div style=" background: none repeat scroll 0 0 #222222;color: #CFCFCF !important;padding: 4px;border-radius: 10px 10px 0 0;">
								<font style="font-family: Arial,Helvetica,sans-serif;font-size: 22px;font-weight: bold;letter-spacing: 1px;line-height: 24px;margin: 0;padding: 0 0 2px;text-shadow:1px 1px 1px #DCAAA1">
									<?php  echo  $merchant_msg["edit-compaign"]["Field_add_campaign_logo"]; ?>
								</font>
							 </div>
                                                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
							<!-- -->
							<div id="media-upload-header">
								<ul id="sidemenu">
								<li id="tab-type" class="tab_from_library"><a class="current" ><?php  echo  $merchant_msg["edit-compaign"]["Field_media_library"]; ?></a></li>
								
								</ul>
							</div>
							<!-- -->
								
                                                          
							   <div style="clear: both" ></div>
							   <div style="display: none;padding-left: 13px; padding-right: 13px;" class="div_from_computer">
								<div  style="padding-top:10px;padding-bottom:10px">Add media from your computer
									
								</div>
								<div style="clear: both" ></div>
								<div style="width: 100%;height: 168px;border: dashed 1px black;display: block;" align="center">
									<div style="padding-top:20px;">
									<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
									<div id="upload" >
									<span  >Upload Photo
									</span>
									</div>
									</div>
								</div>
								<div  align="center" style="padding-top:10px">
                                                                    <input class="save_btn" type="button" name="btn_save_from_computer" id="btn_save_from_computer" onclick="save_from_computer()"  value="<?php echo $merchant_msg['index']['btn_save'];?>"/>
								</div>
							   </div>
							   <div style="display:block;padding-left: 13px; padding-right: 13px;" class="div_from_library">
								<div  style="padding-top:10px;padding-bottom:10px"><?php  echo  $merchant_msg["edit-compaign"]["Field_add_campaign_logo_media_library"]; ?></div>
								<?php
								
									$flag = true;
									$merchant_array = array();
									$merchant_array['id'] = $_SESSION['merchant_id'];
									$merchant_info = $objDB->Show("merchant_user",$merchant_array);
									if($merchant_info->fields['merchant_parent'] != 0)
									{
										
										$media_acc_array = array();
										$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];;
										$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array);
										$media_val = unserialize($RSmedia->fields['media_access']);
										if(in_array("view-use",$media_val))
										{
											$flag = true;
										}
										else{
											$flag = false;	
										}
									}
									else{
										$flag = true;
									}
									
									if($flag)
									{
				
								?>
								<div id="media_library_listing" style="width:100%;height:180px;border:1px dashed #000;overflow:auto;">
									<div style="clear: both"></div>
									<ul class="ul_image_list">
									<?php
										//$query = "select * from merchant_media where image_type='campaign' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'].") order by id desc" ;
										//$RSImages = $objDB->execute_query($query);
										 //if($RSImages->RecordCount()>0){
										//while($Row = $RSImages->FetchRow()){
                                                                                //echo WEB_PATH.'/merchant/process.php?btnGetLogosOfMerchant=yes&mer_id='.$_SESSION['merchant_id']."&img_type=campaign&mer_parent_id=".$merchant_info->fields['merchant_parent']; 
										 $arr123=file(WEB_PATH.'/merchant/process.php?btnGetLogosOfMerchant=yes&mer_id='.$_SESSION['merchant_id']."&img_type=campaign&mer_parent_id=".$merchant_info->fields['merchant_parent']."&start_index=0&num_of_records=12");
										
                                                                                //print_r($arr);
                                                                                if(trim($arr123[0]) == "")
										{
											unset($arr123[0]);
											$arr123 = array_values($arr123);
										}
										$json123 = json_decode($arr123[0]);
                                                                                //echo $json; 
										$total_records123= $json123->total_records;
										$records_array123 = $json123->records;
										//echo $json123->query_new;
                                                                                //echo $json123->query_old;
										if($total_records123>0){
											foreach($records_array123 as $Row)
											{
										?>
									
										
										<li class="li_image_list" id="li_img_<?=$Row->id;?>">
											<div>
												<img src="<?php echo ASSETS_IMG .'/m/campaign/'.$Row->image;  ?>" height="50px" width="50px" />
												<span style="vertical-align: top" id="span_img_text_<?=$Row->id;?>"><?=$Row->image?></span>
												<span style="vertical-align: top;float: right"> Use this image&nbsp;<input type="radio" name="use_image" value="<?=$Row->id?>" /></span>
											</div>
											
										</li>
										<?php }}?>
									</ul>
									
								</div>
								<div  align="center" style="padding-top:10px">
										<input type="button" class="save_btn" name="btn_save_from_library" id="btn_save_from_library" onclick="save_from_library()"  value="<?php echo $merchant_msg['index']['btn_save'];?>"/>
									</div>
							   </div>
							   <?php
									}
									else{
										?>
										<div  style="padding-top:10px;padding-bottom:10px">
											You don't have access to use media library images.
										</div>
										<?php
									}
							   ?>
                                                            </div>
                                                          
                                                        </div>
                                                 </div>
                                            </div>
                                       </div> 
  </div>
	<?php
		echo file_get_contents(WEB_PATH.'/merchant/import_media_library.php?mer_id='.$_SESSION['merchant_id'].'&img_type=campaign&start_index=0');
	?>
  </form>
 <!-- end of popup div PAY-508-28033 -->
 <script>
    function check_is_walkin(val)
    {
        $av = jQuery.noConflict();
        if(val==0)
            {
               $av ("#group").css("display","");
            }
            else
             {
                $av("#group").css("display","none");
                $av("#one_per_customer").attr("checked", "checked");
             }            
    }
	var file_path = "";
jQuery("#show_more_mediya_browse").live("click",function(){
	var cur_el= jQuery(this);
	var next_index = parseInt(jQuery(this).attr('next_index'));
	var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'show_more_media_browse=yes&next_index='+next_index+'&num_of_records='+num_of_records+"&img_type=campaign",
		async:true,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			//alert(obj.status);
			jQuery(".fancybox-inner .ul_image_list").append(obj.html);
			cur_el.attr('next_index',next_index + num_of_records);
			if(parseInt(obj.total_records)<num_of_records)
			{
				cur_el.css("display","none");
			}
		}
	});
});

jQuery(".ul_image_list li").live("click",function(){
	
	jQuery(".useradioclass").prop( "checked", false );
	var imgid=jQuery(this).attr("id").split("img_");
	imgid=imgid[1];
	//alert(imgid);
	jQuery(this).find(".useradioclass").prop( "checked", true );
	
	jQuery(".ul_image_list li").removeClass("current");
	jQuery(this).addClass("current");
	
	jQuery(".fancybox-inner .useradioclass").each(function(){
               
		if(jQuery(".fancybox-inner .useradioclass").is(":checked"))
		{
			
			jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
			jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
			jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#3C99F4 !important");
		}
		else
		{
			jQuery(".fancybox-inner #btn_save_from_library").attr("disabled",true);
			jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
			jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#ABABAB !important");
		}
		
	});
            
	
});
 
/* start of script for PAY-508-28033*/
function save_from_library()
{
        $av = jQuery.noConflict();
	 var sel_val = $av('input[name=use_image]:checked').val();
         
	 <!--// 369-->
	 if (sel_val==undefined)
	 {
	 	jQuery.fancybox.close();
	 }
	 else
	 {
		
                
		$av("#hdn_image_id").val(sel_val);
		
            var sel_src = $av(".fancybox-inner #li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
            
		
		
	       
	       $av("#hdn_image_path").val(sel_src);
               
	       /* NPE-252-19046 */
	       
	       /* NPE-252-19046 */
	       file_path = "";
	       //close_popup('Notification');
               jQuery.fancybox.close();
	       var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ sel_src +"' class='displayimg'>";
              
    
              
	       $av('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' id='' class='cancel_remove_image' onclick='rm_image_lib()' /></div></div></div>");
	 }
	 <!--// 369-->
}
function rm_image_lib()
{
	$rm = jQuery.noConflict();
	$rm("#hdn_image_path").val("");
	$rm("#hdn_image_id").val("");
	$rm('#files').html("");
}
function rm_image(id)
{
	
	$rm = jQuery.noConflict();
	
	jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'is_image_delete=yes&image_type=campaign&filename='+id,
                          async:false,
                           success:function(msg)
                           {
								$rm("#hdn_image_path").val("");
								$rm("#hdn_image_id").val("");
								$rm('#files').html("");
                           }
                           
                     });
	
}
function save_from_computer()
{
    $as = jQuery.noConflict();
	$as("#hdn_image_path").val(file_path);
	$as("#hdn_image_id").val("");
	/* NPE-252-19046 */
	
	/* NPE-252-19046 */
	
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ file_path +"' class='displayimg'>";
	$as('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' id='"+file_path+"' class='cancel_remove_image' onclick='rm_image(this.id)' /></div></div></div>");
}
$atab = jQuery.noConflict();
/* NPE-252-19046 */
$atab(document).ready(function(){
	if($atab("#hdn_image_path").val()  != ""){
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ $atab("#hdn_image_path").val() +"' class='displayimg'>";
	$atab('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' id='"+$atab("#hdn_image_path").val()+"' class='cancel_remove_image' onclick='rm_image(this.id)' /></div></div></div>");
	}
	
	$atab(".tempate_preview_image").click(function(){
	
		//alert($atab(this).next().next().val());
		
		var tempEle= $atab(this).next().next();
		tempEle.trigger("click");
		
	});
	
	$atab("input:radio[name=template_value]").click(function(){
	var t_val = $atab(this).attr("value");
			$atab.ajax({
				type: "POST",
				url: "<?=WEB_PATH?>/merchant/load_template.php",
				data: "template_id=" + t_val,
				success: function(msg) {
					
				if(t_val == "0")
				{
					//alert("come in");
					$atab('#files').html("");
					//$atab("#img_preview").attr("src","");
					$atab("#description").text("");
					 var sel_val = $atab('input[name=use_image]:checked').val();   
					var sel_src = $atab("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
					//alert(sel_src );
					//alert($atab("#description").text());
					$atab("#description").text("<p></p>");
					//alert($atab("#description").text());
                                        $atab("#deal_detail_description").text("");
					$atab("#hdn_image_path").val("");
					$atab("#img_div").css("display","none");
				}else{
						var obj = eval('('+msg+')');
				if(obj['fields'].business_logo!="")
				{
					var img_businesslog = "<?=ASSETS_IMG ?>/m/campaign/"+obj['fields'].business_logo;
				}
				else
				{
					var img_businesslog = "<?=ASSETS_IMG ?>/m/campaign/Merchant_Offer.png";
				}
				var img = "<img src='"+ img_businesslog +"' class='displayimg'>";
	$atab('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image_lib()' /></div></div></div>");
					//$atab("#img_preview").attr("src",img_businesslog);
					$atab("#description").text("");
                                         tinyMCE.activeEditor.setContent(obj['fields'].description);
					//$atab("#description").text(obj['fields'].description);
                                        $atab("#title").val(obj['fields'].title);
					$atab("#category_id").val(obj['fields'].category_id);
					//$atab("#discount").val(obj['fields'].discount);
					
					$atab("#hdn_image_path").val(obj['fields'].business_logo);
                                        $atab("#deal_detail_description").text(obj['fields'].print_coupon_description);
					$atab("#img_div").css("display","block");
				}
				}
				
			});

	});
});
$atab(function(){
		var btnUpload=$atab('#upload');
		var status=$atab('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUpload&img_type=campaign',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				if($atab('#files').children().length > 0)
				{
					$atab('#files li').detach();
				}
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
				//On completion clear the status
				/*
				var arr = response.split("|");
				
				status.text('');
				//Add uploaded file to list
				file_path = arr[1];
				save_from_computer();
                                */
                               var arr = response.split("|");
				if(arr[1]=="small")
                                {
                                    status.text(arr[0]);
                                }
                                else
                                { 
                                    status.text('');
                                    //Add uploaded file to list
                                    file_path = arr[1];
                                    save_from_computer();
                                }
			}
		});
		
	});
$atab(".tab_from_library a").click(function(){
	$atab("#sidemenu li a").each(function() {
		$atab(this).removeClass("current");
		});
	$atab(this).addClass("current");
	$atab(".div_from_library").css("display","block");
	$atab(".div_from_computer").css("display","none");
	});
$atab(".tab_from_computer a").click(function(){
	$atab("#sidemenu li a").each(function() {
		$atab(this).removeClass("current");
		});
	$atab(this).addClass("current");
	$atab(".div_from_library").css("display","none");
	$atab(".div_from_computer").css("display","block");
	});
/* NPE-252-19046 */


function close_popup(popup_name)
{
$ac = jQuery.noConflict();
	$ac("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
	$ac("#" + popup_name + "BackDiv").fadeOut(200, function () {
		 $ac("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
				$ac("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				$ac("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				$ac("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
 function open_popup(popup_name)
{
$ao = jQuery.noConflict();
	if($ao("#hdn_image_id").val()!="")
	{
		$ao('input[name=use_image][value='+$ao("#hdn_image_id").val()+']').attr("checked","checked");
	}
	$ao("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		$ao("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 $ao("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	
}
/* end of script for PAY-508-28033*/
jQuery.noConflict();
</script>
<?
$_SESSION['msg'] = "";
?>

<script language="javascript">
// 369
$abc = jQuery.noConflict();
$abc(document).ready(function() { 
    // bind form using ajaxForm 
  $abc('#add_campaign_form').ajaxForm({ 
        dataType:  'json', 
        success:   processAddCampJson 
    });
	
});
function processAddCampJson(data) {
    
	if(data.status == "true"){

		window.location.href='<?=WEB_PATH.'/merchant/compaigns.php?action=active'?>';
	}
	else
	{
		//alert(data.message);
		var head_msg="<div class='head_msg'>Message</div>"
		var content_msg="<div class='content_msg'>"+data.message+"</div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		jQuery.fancybox({
				content:jQuery('#dialog-message').html(),
				type: 'html',
				openSpeed  : 300,
                                closeSpeed  : 300,
				changeFade : 'fast',  
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
		});
		
	}
	
}
// 369
$abc(".camp_location").click(function(){
    
  t_val = $abc(this).val() ;
  if($abc(this).is(':checked'))
  {
      if($abc(".chk_private").is(':checked'))
        {
          var private_flag = 1;
        }
        else
        {
                var private_flag = 0;
        }
        
        $abc.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "location_id=" + t_val +"&getlocationwisegroup=yes&public="+private_flag,
                                      success: function(msg) {
										  
                                          $abc(".location_listing_area").append(msg);
										  
											//19-09-2013
											
											if($abc(".chk_private").is(':checked'))
											{
												//$abc(".other_group").css("display","none");
												//alert("checked");
												//$abc("ul[id^='locationgroup'] li:not(:first)").css("display","none");
												$abc("ul[id^='locationgroup']").each(function( index ) {
													//alert("hi");
													$abc(this).find("li:not(:first)").css("display","none");
												});
											}
											else
											{
												//$abc(".other_group").css("display","block");
												//alert("not checked");
												//$abc("ul[id^='locationgroup'] li:not(:first)").css("display","block");
												$abc("ul[id^='locationgroup']").each(function( index ) {
													//alert("hi");
													$abc(this).find("li:not(:first)").css("display","block");
												});
											}
											
											//19-09-2013
                                      }
        });
        
  }
  else
  {
      
      $abc("#locationid_"+$abc(this).val()).detach();
  }
});
$abc(".chk_private").click(function(){
    if($abc(this).is(':checked'))
        {
           $abc("input[type=checkbox][private_group]").each(function(){
                $abc(this).attr("checked","checked") ;
               $abc(this).attr("disabled", false);
           });
		   // 8-8-2013

		   $abc("input[type=checkbox][class=other_group]").each(function(){
                //$abc(this).attr("checked","unchecked") ;
				$abc(this).prop('checked', false);
                                $abc(this).attr("disabled", true) ;
           });
		   jQuery("input[id^='hdn_campaign_type_']" ).val("1");
                   jQuery("input[id^='hdn_campaign_type1_']" ).val("1");
		   // 8-8-2013
		   
		   //19-09-2013
		   
		   //jQuery("ul[id^='locationgroup'] li:not(:first)").css("display","none");
		   jQuery("ul[id^='locationgroup']").each(function( index ) {
				//alert("hi");
				jQuery(this).find("li:not(:first)").css("display","none");
			});
												
		   //19-09-2013
        }
        else
        {
              $abc("input[type=checkbox][private_group]").each(function(){
                $abc(this).removeAttr("checked") ;
                $abc(this).attr("disabled", false) ;
           });
           $abc("input[type=checkbox][class=other_group]").each(function(){
              
                                $abc(this).attr("disabled", false) ;
           });
		   jQuery("input[id^='hdn_campaign_type_']" ).val("0");
                   jQuery("input[id^='hdn_campaign_type1_']" ).val("0");
				   
			//19-09-2013
			
		   //jQuery("ul[id^='locationgroup'] li:not(:first)").css("display","block");
		    jQuery("ul[id^='locationgroup']").each(function( index ) {
				//alert("hi");
				jQuery(this).find("li:not(:first)").css("display","block");
			});
			
		   //19-09-2013	   
        }
});
/*
$abc(".private_group").live("click",function(){
	//alert($abc(this).parent().parent().attr("id"));
	var locationidname=$abc(this).parent().parent().attr("id").split("_");
	var locationid=locationidname[1];
    if($abc(".chk_private").is(':checked'))
    {
		if($abc(this).is(':checked'))
		{
			//alert("checked");
			jQuery("#hdn_campaign_type_"+ locationid).val("2");
		}
		else
		{
			//alert("unchecked");
			return false;
		}		
    }
	else
	{
		// 8-8-2013
		var flg=true;
		$abc(this).parent().parent().find('li .other_group').each(function( index ) {
			//alert( index + ": " + $abc(this).text() );
			//alert($abc(this).is(':checked'));
			if($abc(this).is(':checked'))
				flg=false;
			if(!flg)
				return false;
		});
		if(!flg)
			return false;
		else
			jQuery("#hdn_campaign_type_"+ locationid).val("2");
		// 8-8-2013
	}
});
*/
// 8-8-2013
$abc(".other_group").live("click",function(){
       
	var locationidname=$abc(this).parent().parent().attr("id").split("_");
        
	var locationid=locationidname[1];
       
       var oldvalue=jQuery("#hdn_campaign_type1_"+ locationid).val();
       
       var is_checkboxval=parseInt(oldvalue)+1;
       var is_not_checkboxval=parseInt(oldvalue)-1;
                if($abc(this).is(':checked'))
                {
                    
                    jQuery("#hdn_campaign_type1_"+ locationid).val(is_checkboxval);
                }
                else
                {
                       jQuery("#hdn_campaign_type1_"+ locationid).val(is_not_checkboxval);
                }
       
        
        if(jQuery("#hdn_campaign_type1_"+ locationid).val() == 0)
            {
                 $abc(this).parent().parent().find(".private_group").attr("disabled", false);
            }
            else
                {
                    $abc(this).parent().parent().find(".private_group").attr("disabled", true);
                }
	//alert($abc(this).parent().parent().get(0).tagName);
	//alert($abc(this).parent().parent().attr("id"));
	//alert($abc(this).parent().parent().find(".private_group").is(':checked'));
	if($abc(this).parent().parent().find(".private_group").is(':checked'))
	{
		//return false;
               // $abc(".other_group").attr("disabled", true);
	}
	else
	{
		jQuery("#hdn_campaign_type_"+ locationid).val("3");
	}	
});
$abc(".private_group").live("click",function(){
    var locationidname=$abc(this).parent().parent().attr("id").split("_");
        
	var locationid=locationidname[1];
        
     if($abc(".chk_private").is(':checked'))
    {    
        if($abc(this).is(':checked'))
        {
            jQuery("#hdn_campaign_type1_"+ locationid).val("1");
            jQuery("#hdn_campaign_type_"+ locationid).val("2");
             $abc("#locationgroup_"+locationid+" .other_group").attr("disabled", true);
        }
        else
        {

           return false; 
                jQuery("#hdn_campaign_type1_"+ locationid).val("0");


              // $abc("#locationgroup_"+locationid+" .other_group").attr("disabled", false);

        }
    }
    else
        {
            
            if($abc(this).is(':checked'))
            {
                jQuery("#hdn_campaign_type1_"+ locationid).val("1");
                jQuery("#hdn_campaign_type_"+ locationid).val("2");
                 $abc("#locationgroup_"+locationid+" .other_group").attr("disabled", true);
            }
            else
            {

               
                    jQuery("#hdn_campaign_type1_"+ locationid).val("0");


                   $abc("#locationgroup_"+locationid+" .other_group").attr("disabled", false);

            } 
        }
    
})
// 8-8-2013
function changetext1(){
   var v = 76 - $abc("#title").val().length;
   $abc(".span_c1").text(v+" characters remaining");
}
function changetext2(){
   var v = 8 - $abc("#discount").val().length;
   $abc(".span_c2").text(v+" characters remaining");
}
function changetextpcd(){
    var v = 300 - $abc("#deal_detail_description").val().length;
   $abc(".span_pcd").text(v+" characters remaining"); 
}
function getpointvalue1()
{
     var numbers = /^[0-9]+$/; 
    if($abc("#txt_price").val() == "")
        {
            
        }
     
  else if($abc("#txt_price").val().match(numbers))  
   { 
     $abc.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "txt_price=" + $abc("#txt_price").val() +"&getpoints=yes",
                                      success: function(msg) 
                                      {
                                          
                                          $abc("#span_con_point").text(msg);
                                           $abc(".success_msg").text("");
                                      }
        });
        }
        else
            {
                //alert("Please Enter Proper Price");
		var head_msg="<div class='head_msg'>Message</div>"
		var content_msg="<div class='content_msg'><?php echo $merchant_msg['edit-compaign']['please_enter_correct_amount'];?></div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		jQuery.fancybox({
			    content:jQuery('#dialog-message').html(),
			    type: 'html',
			    openSpeed  : 300,
			    closeSpeed  : 300,
			    changeFade : 'fast',  
			    helpers: {
				    overlay: {
				    opacity: 0.3
				    } // overlay
			    }
		});
                $abc("#span_con_point").text("");
                 $abc(".success_msg").text("");
            }
}


</script>
<!-- start datepicker -->

<!--<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.js"></script>-->

<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.ui.core.js"></script>


<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.ui.datepicker.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.ui.datepicker.css">
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.ui.theme.css">
<!-- end datepicker -->

<!--- tooltip css --->

<!--- tooltip css --->

<script type="text/javascript">
jQuery(document).ready(function(){
    

jQuery("a#compaigns").css("background-color","orange");
jQuery("#expiration_date").datetimepicker({onSelect: function(){
        
        
       
}});



jQuery("#start_date").datetimepicker({ onSelect: function(dateText,inst){
        
        var date = jQuery("#start_date").datepicker( 'getDate' );
        
         var actualDate = new Date(date);
         
         var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+30);
         
         
         
	jQuery('#expiration_date').datepicker('option', {
        
                minDate: jQuery(this).datepicker( 'getDate' ),
                //maxDate : newDate
                
        
            });
        
}});

jQuery("#walkinid").click(function(){
        if(jQuery('#walkinid').is(':checked'))
	{
	    jQuery("#one_per_customer_per_day").attr("disabled", "disabled");
	    jQuery("#multiple_use").attr("disabled", "disabled");
	}
	
});
jQuery("#groupid").click(function(){
        if(jQuery('#groupid').is(':checked'))
	{
	    jQuery("#one_per_customer_per_day").removeAttr("disabled");
	    jQuery("#multiple_use").removeAttr("disabled");
	}
	
});

jQuery(".fancybox-inner .useradioclass").live("change",function(){
             
            jQuery(".fancybox-inner .useradioclass").each(function(){
               
                if(jQuery(".fancybox-inner .useradioclass").is(":checked"))
                    {
                        
                        jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
                        jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
                        jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#3C99F4 !important");
                    }
                    else
                    {
                        jQuery(".fancybox-inner #btn_save_from_library").attr("disabled",true);
                        jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
                        jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#ABABAB !important");
                    }
            })
        });

jQuery('.notification_tooltip').tooltip({
content: function() {
        return jQuery(this).attr('title');
    },
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
	
});

jQuery('input:radio[name="number_of_use"]').change(function(){
	   //alert(jQuery(this).parent().parent().next().html());
	   //alert(jQuery(this).val());
	   if(jQuery(this).val()=="2" || jQuery(this).val()=="3")
	   {
			jQuery(this).parent().parent().next().css('display','none');
	   }
	   else
	   {
			jQuery(this).parent().parent().next().css('display','table-row');
	   }
});

jQuery('.mediaclass').click(function(){
    
        jQuery.fancybox({
                content:jQuery('#mediablock').html(),

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
    
});

});

</script>
<script type="text/javascript">
     jQuery.noConflict();
    jQuery(document).ready(function(){
	//jQuery('.list_carousel').css("height","145px");
	
	jQuery('#foo2').carouFredSel({
	    auto: false,
	    prev: '#prev2',
	    next: '#next2',
	    pagination: "#pager2",
	    mousewheel: true,
	    swipe: {
		    onMouse: true,
		    onTouch: true
	    }
	});
	jQuery('.list_carousel').css("overflow","inherit");
    });

	
jQuery("#discount").blur(function(){
	jQuery("#discount").val(jQuery("#discount").val().trim());
	var deal_value=jQuery("#deal_value").val();
	var discount=jQuery("#discount").val();
	
	var n=deal_value.indexOf("$");
	if(n!="-1")
	{
		//alert("found");
		deal_value =deal_value.substring(n+1,deal_value.length);
	}
	//alert(deal_value);
	
	if(deal_value!="0")
	{
		if(discount!="" && discount!="NaN")
		{
			var saving=(parseFloat(deal_value)*parseFloat(discount))/100;
			if(isNaN(saving))
			{
				jQuery("#saving").val("");	
			}
			else
			{
				jQuery("#saving").val(Math.round(100*saving)/100);	
			}
		}
	}
});	
jQuery("#saving").blur(function(){
	jQuery("#saving").val(jQuery("#saving").val().trim());
	var deal_value=jQuery("#deal_value").val();
	var saving=jQuery("#saving").val();
	
	var n=deal_value.indexOf("$");
	if(n!="-1")
	{
		//alert("found");
		deal_value =deal_value.substring(n+1,deal_value.length);
	}
	//alert(deal_value);
	
	if(deal_value!="0")
	{
		if(saving!="" && saving!="NaN")
		{
			var discount=(parseFloat(saving)*100)/parseFloat(deal_value);
			if(isNaN(discount))
			{
				jQuery("#discount").val("");	
			}
			else
			{
				jQuery("#discount").val(Math.round(100*discount)/100);	
			}
		}
	}
});	
jQuery("#deal_value").blur(function(){
	//alert("hi");
	jQuery("#deal_value").val(jQuery("#deal_value").val().trim());
	var deal_value=jQuery("#deal_value").val();
	var discount=jQuery("#discount").val();
	var saving=jQuery("#saving").val();
	
	var n=deal_value.indexOf("$");
	if(n!="-1")
	{
		//alert("found");
		deal_value =deal_value.substring(n+1,deal_value.length);
	}
	//alert(deal_value);															
	
	if(deal_value=="0")
	{
		jQuery("#discount").val("0");	
		jQuery("#saving").val("0");	
	}
	if(deal_value!="" && deal_value!="NaN")
	{
		if(discount!="" && discount!="NaN")
		{
			var saving=(parseFloat(deal_value)*parseFloat(discount))/100;
			if(isNaN(saving))
			{
				jQuery("#saving").val("");	
			}
			else
			{
				jQuery("#saving").val(Math.round(100*saving)/100);	
			}
		}
		
		if(saving!="" && saving!="NaN")
		{
			var discount=(parseFloat(saving)*100)/parseFloat(deal_value);
			if(isNaN(discount))
			{
				jQuery("#discount").val("");	
			}
			else
			{
				jQuery("#discount").val(Math.round(100*discount)/100);	
			}
		}
	}
	
	if(deal_value!="")
	{
		jQuery("#tr_discount").css("display","table-row");
		jQuery("#tr_saving").css("display","table-row");
		jQuery("#discount").focus();
	}
	else
	{
		jQuery("#tr_discount").css("display","none");
		jQuery("#tr_saving").css("display","none");
		jQuery("#deal_value").focus();
	}
});	
/*************** ****************************/
    jQuery(document).ready(function(){
  function add() {
    if(jQuery(this).val() === ''){
      jQuery(this).val(jQuery(this).attr('placeholder')).addClass('placeholder');
    }
  }

  function remove() {
    if(jQuery(this).val() === jQuery(this).attr('placeholder')){
      jQuery(this).val('').removeClass('placeholder');
    }
  }

  // Create a dummy element for feature detection
  if (!('placeholder' in jQuery('<input>')[0])) {

    // Select the elements that have a placeholder attribute
    jQuery('input[placeholder], textarea[placeholder]').blur(add).focus(remove).each(add);

    // Remove the placeholder text before the form is submitted
    jQuery('form').submit(function(){
      jQuery(this).find('input[placeholder], textarea[placeholder]').each(remove);
    });
  }
});
 jQuery('body').on("change","#billing_country",function () {

			var change_value=this.value;
			if(change_value == "USA")
			{
				jQuery(".fancybox-inner #billing_state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");
			   
			   
			}
			else
			{
			 	  jQuery(".fancybox-inner #billing_state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>"); 
				
			}
		  //jQuery(this).attr("selected","selected");
	    });
	
  </script>
<div class="validating_data" style="display:none;">Validating data, please wait...</div> 
</body>
</html>
