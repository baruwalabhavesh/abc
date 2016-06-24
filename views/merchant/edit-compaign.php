<?php
/**
 * @uses edit campaign
 * @used in pages : add-user.php,apply_filter.php,compaigns.php,edit-user.php,manage-marketing-material.php,reports.php,footer.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where_act['active']=1;
$RSCat = $objDB->Show("categories",$array_where_act);

//$array_where['created_by'] = $_SESSION['merchant_id'];
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("campaigns", $array_where);
$array =array();
$array_where_user['id'] = $_SESSION['merchant_id'];
$RS_User = $objDB->Show("merchant_user", $array_where_user);
$m_parent = $RS_User->fields['merchant_parent'];


if($RS_User->fields['merchant_parent'] == 0)
{
	$array['created_by'] = $_SESSION['merchant_id'];
        $array['active'] = 1;
	$RSStore = $objDB->Show("locations", $array);
	$RSStore1 = $objDB->Show("locations", $array);
}
else
{
	$media_acc_array = array(); 
	$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id']; 
	$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array); 
	$location_val = $RSmedia->fields['location_access'];

	
	//$sql = "SELECT * FROM locations WHERE active=1 and id in (select l.id from locations l where l.created_by=".$m_parent ." and l.id = ".$location_val .")";
	/*$sql = "SELECT * FROM locations WHERE active=1 and id=".$location_val;
	
	$RSStore = $objDB->execute_query($sql);
	$RSStore1 = $objDB->execute_query($sql);*/
	$RSStore = $objDB->Conn->Execute("SELECT * FROM locations WHERE active=? and id=?",array(1,$location_val));
}

$array = $location_id = array();
$array['campaign_id'] = $_REQUEST['id'];
$RSComp = $objDB->Show("campaign_location", $array);
while($Row = $RSComp->FetchRow()){
	$location_id[] = $Row['location_id'];
}
$array = array();
$array['campaign_id'] = $_REQUEST['id'];
$RSCode = $objDB->Show("activation_codes",$array);

$array = array();
$array['merchant_id'] = $_SESSION['merchant_id'];
$RSGroups = $objDB->Show("merchant_groups",$array);

/****** */
//echo WEB_PATH.'/merchant/process.php?check_campaign_active=yes&mer_id='.$_SESSION['merchant_id'].'&cid='.$_REQUEST['id'];
 $arr=file(WEB_PATH.'/merchant/process.php?check_campaign_active=yes&mer_id='.$_SESSION['merchant_id'].'&cid='.$_REQUEST['id']);
									if(trim($arr[0]) == "")
									{
										unset($arr[0]);
										$arr = array_values($arr);
									}
									$json = json_decode($arr[0]);
									$is_active= $json->total_records;
                                                                        if($is_active>0)
                                                                        {
                                                                         //  echo "active";
                                                                            $check_active="true";
                                                                        }
                                                                        else {
                                                                          // echo "inactive";
                                                                            $check_active="false";
                                                                         }
/**** */
//echo $is_active;

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

?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Edit Campaign</title>
<?php //require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/main.css">

<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.carouFredSel-6.2.1-packed.js"></script>

<!--<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/detect_timezone.js"></script>-->
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/prototype-1.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/prototype-base-extensions.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/prototype-date-extensions.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/behaviour.js"></script>
<!--<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/datepicker.js"></script>-->



<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/datepicker.css">
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/behaviors.js"></script>
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
                oninit : "postInitWork",
        valid_elements :'p,br,ul,ol,li,sub,sup',       
		// Theme options
		//theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons1 : "replace,|,bullist,numlist,|,sub,sup,|,charmap",
		//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

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
		
			if (jQuery('#'+ed.id).prop('readonly')) {
				ed.settings.readonly = true;
			}
			
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
			
			ed.onLoadContent.add(function(ed, o) {
				//alert("loaded");
				//jQuery(".textarea_loader").css("display","none");
				//jQuery(".textarea_container").css("display","block");
			});
			ed.onInit.add(function(ed) {
				//alert("added");
				//jQuery(".textarea_loader").css("display","none");
				//jQuery(".textarea_container").css("display","block");
			});


		}
	});
	jQuery(".textarea_loader").css("display","none");
	jQuery(".textarea_container").css("display","block");
	

	});
        function postInitWork()
        {
          var editor = tinyMCE.getInstanceById('terms_condition');
         // editor.getBody().style.backgroundColor = "#ECEAE9";
        }

</script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap.css" />
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
<!--<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.tooltip.css" />

<script src="<?=ASSETS_JS ?>/m/jquery.tooltip.js" type="text/javascript"></script> -->

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
		<div id="content">  <h3><?php  echo  $merchant_msg["edit-compaign"]["Field_edit_campaign"]; ?></h3>
	<!--// 369--><form action="process.php" method="post" enctype="multipart/form-data" id="edit_campaign_form" ><!-- edit_campaign_form-->
	
	<?php
	 if($check_active!="true")
	 {
	?>
	
	<div class="templateclass">
				<div  align="center">
					<font class="slider_heading" size="3"><?php  echo  $merchant_msg["edit-compaign"]["Field_available_template"]; ?></font>
				</div>
                              <div class="list_carousel">
					<ul id="foo2"> 
					<?php
                                        /*
                                        
					$array_where_t = array();
					$array_where_t['created_by'] = $_SESSION['merchant_id'];
					$RSTemplate = $objDB->Show("campaigns_template",$array_where_t);
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
					
					?> <li>
						
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
						<input type="radio" name="template_value" class="rg"   value="<?=$Row->id?>" <?php if(isset($_REQUEST['template_id'])){ if($Row->id == $_REQUEST['template_id'] ) { echo "checked"; } } ?> /> <label class="chk_align" title="<?=$Row->title?>"><?=$Row->title?></label>
					</li>
						
						
					<?
                                            }
					}else
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
	<?php
	}
	?>
	<input type="hidden" name="hdn_transcation_fees" id="hdn_transcation_fees" value="<?php echo $get_billing_pack_data->fields['transaction_fees']; ?>" />
           <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?=$RS->fields['business_logo']?>" />
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
				<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="cls_left" id="table_spacer">
				
                                <tr>
									<td width="21%" align="right">
									<?php  echo  $merchant_msg["edit-compaign"]["Field_campaign_title"]; ?>					
									<?php
										//echo urlencode($RS->fields['title']);
										//echo "</br>";
										//echo urldecode($RS->fields['title']);
														
										//echo $camp_tit;
										/*
										$camp_tit=htmlspecialchars_decode($RS->fields['title']);
										echo $camp_tit;
										*/
																		
									?>
									</td>
									<td width="79%" align="left">
										<?php
											//$camp_tit=urldecode($RS->fields['title']);
										
											// 02 10 2013
											
											//echo $RS->fields['title'];
											//echo "</br>";
											
											//$camp_tit=str_replace("''","'",$RS->fields['title']);
											$camp_tit=$RS->fields['title'];
											
											//echo $camp_tit;
											//echo "</br>";
											
											//echo htmlentities($camp_tit);
											//echo "</br>";
											
											// 02 10 2013
										?>
										<input type="text" placeholder="Please Enter Campaign Title" name="title" id="title" value="<?=htmlentities($camp_tit) ?>" maxlength="76" size="50" onkeyup="changetext1()" /><span class="span_c1">Maximum 76 characters | No HTML allowed</span>
										<input type="hidden" name="campaign_title" id="campaign_title" value="<?=htmlentities($camp_tit) ?>" />
									</td>  
                                </tr>
                              
                                <tr>
					<td width="21%" align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_campaign_tag"]; ?>
					</td>
                                        <td width="79%" align="left"><input type="text" placeholder="Enter upto 3 keywords separated by commas " name="campaign_tag" id="campaign_tag"   size="50" value="<?php echo $RS->fields['campaign_tag'] ?>" /> <span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_Campaign_Tag"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>  
                                  </tr>
	
					<tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_deal_value"]; ?>
					</td>
					<td align="left">
					<input type="text" placeholder="00" name="deal_value" id="deal_value" value="<?=$RS->fields['deal_value']?>" /><span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_deal_value"]; ?>" >&nbsp;&nbsp;&nbsp</span><span id="numeric">numeric, no currency symbol</span>
					</td>
				  </tr>
				  <tr id="tr_discount" >
					<td width="20%" align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_discount_rate"]; ?>
					</td>
					<td width="20%" align="left">
                        <input type="text" placeholder="00" name="discount" id="discount" value="<?=$RS->fields['discount']?>" placeholder="please enter discount %" size="25"/><span data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_discount_rate"]; ?>">&nbsp;&nbsp;&nbsp;</span><span id="numeric">numeric</span>
					</td>
                                       
				  </tr> 
				  				  
				  <tr id="tr_saving" >
					<td width="20%" align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_saving_rate"]; ?>
					</td>
					<td width="20%" align="left">
                        <input type="text" placeholder="00" name="saving" id="saving" value="<?=$RS->fields['saving']?>" placeholder="Please enter amount" size="25"/><span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_saving_rate"]; ?>">&nbsp;&nbsp;&nbsp;</span><span id="numeric">numeric, no currency symbol</span>
					</td>
                                       
				  </tr>
				  <!-- NPE-252-19046 -->
				  <!--  <tr>
					<td align="right">Template: </td>
					<td align="left">
					<select name="template_id" id="template_id">
						<option value="0">-Select template-</option>
					<?
					$RSTemplate = $objDB->Show("campaigns_template");
					while($Row = $RSTemplate->FetchRow()){
					?>
						<option value="<?=$Row['id']?>"><?=$Row['title']?></option>
					<?
					}
					?>
					</select>
					</td>
				</tr>-->
				    <!-- NPE-252-19046 -->
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
						<option value="<?=$Row['id']?>" <? if($RS->fields['category_id'] == $Row['id']) echo "selected";?>><?=$Row['cat_name']?></option>
					<?
					}
					?>
					</select><span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_category"]; ?>" >&nbsp;&nbsp;&nbsp </span>
					</td>
				  </tr>
<!--				  <tr>
					<td align="right">Activation Code : </td>
					<td align="left">
					  <?=$RSCode->fields['activation_code']?>
					</td>
				  </tr>
				  <tr>
					<td align="right">Business Log : </td>
					<td align="left">
					  <input type="file" name="business_logo" id="business_logo" />
					</td>
				  </tr>-->
                                  <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_campaign_logo"]; ?>
					</td>
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
									<span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["tooltip_upload_image"]; ?>" >&nbsp;&nbsp;&nbsp </span>
									</div> 
			 
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
				<!-- <tr>
					<td align="right">Max Number of Coupons : </td>
					<td align="left">
					<input type="text" name="max_coupns" id="max_coupns" value="<?=$RS->fields['max_coupns']?>" />

					</td>
				  </tr> -->
					
					<!--	
                                   <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_print_coupon_description"]; ?>
					<span class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_campaign_print_description"]; ?>">&nbsp;&nbsp;&nbsp;</span>:</td>
					<td align="left">
					<textarea id="deal_detail_description" maxlength="300" onkeyup="changetextpcd()" name="deal_detail_description" rows="10" cols="40" style="width: 89%"><?=htmlentities($RS->fields['deal_detail_description'])?></textarea><br>
					<span class="span_pcd">Maximum 300 characters</span></td>
				  </tr>
                    -->                
                                   <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_deal_description"]; ?>
					<span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip deal_desc_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_campaign_web_description"]; ?>">&nbsp;&nbsp;&nbsp;</span>:</td>
					<td align="left" class="textare_td">
					<div align="center" class="textarea_loader">
					<img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="defaul_table_loader" />
					</div>
					<div class="textarea_container" style="display: none;">	
					<textarea id="description" name="description" rows="15" cols="80" class="table_th_90"><?=htmlentities($RS->fields['description'])?></textarea>
					</div>
					<span id="desc_limit">Maximum 1200 characters | no HTML allowed</span>
                                        </td>
				  </tr>
                                  
                                  <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_terms_condition"]; ?>
					<span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_terms_condition"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
					<td align="left" class="textare_td">
					<div align="center" class="textarea_loader">
					<img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="defaul_table_loader" />
					</div>
					<div class="textarea_container" style="display: none;">	
                    <textarea id="terms_condition" name="terms_condition" rows="15" cols="80" class="table_th_90"><?=htmlentities($RS->fields['terms_condition'])?></textarea>
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
						<tr class="locationgrid_heading">
							<td class="table_th_8">&nbsp;
							</td>
							<td class="table_th_60">
							<?php  echo  $merchant_msg["edit-compaign"]["Field_location"]; ?>
							</td>
							<td class="table_th_21">
							<?php  echo  $merchant_msg["edit-compaign"]["Field_number_of_activation_code"]; ?>
							<span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_total_offers"]; ?>">&nbsp;&nbsp;&nbsp;</span>
							</td>
							<td>
							<?php  echo  $merchant_msg["edit-compaign"]["Field_activation_code_left"]; ?>
							</td>
						</tr>
						
					</table>
                    <div class="activationcode_container" >
					<table id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1" >
					
					<!--<tr>
                        	<td colspan="4">
                            	<div class="middle_border">&nbsp;</div>
                            </td>
                    </tr> -->
					<?
					if($RSStore->RecordCount()>0){
					$cnt = 0;	
					while($Row = $RSStore->FetchRow()){
					$checked = "";
					if(in_array($Row['id'], $location_id)) {
						$checked = "checked";
					}
					$arr_code = array();
					$arr_code['location_id'] = $Row['id'];
					$arr_code['campaign_id'] = $_REQUEST['id'];
					$RS_num_act_code =  $objDB->Show("campaign_location", $arr_code);
					//print_r($RS_num_act_code);
                                        
                                        if($_REQUEST['id']== $RS_num_act_code->fields['campaign_id'])
                                        {
                                           // echo $RS_num_act_code->fields['num_activation_code'];
                                        }
					?>
					  <tr>
						<td class="table_th_8">
                                                   
                                                    <input type="checkbox"  id="lctn_id_<?=$Row['id']?>" class="camp_location" name="location_id[]" value="<?=$Row['id']?>" <?=$checked?> />
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
						<?php /*?><td width="32%" align="right"><? if ($cnt==0) echo "Number of Activation Code: " ?></td><?php */?>
						<td class="table_th_19"> 
<!--                                                    <input type="textbox" name="num_activation_code[]" value="<?php echo $RS_num_act_code->fields['num_activation_code']; ?>" /> -->
                                                    <input id="num_activation_code_<?=$Row['id']?>" class="clsnum_activation_code" type="textbox" placeholder="00" name="num_activation_code_<?=$Row['id']?>" value="<?php echo $RS_num_act_code->fields['num_activation_code']; ?>" /> 
                        
						
						</td>
					  
                      <?php
                        
                        //24-1-2013
                        
			/*$remain_sql="select * from campaign_location where campaign_id =".$_REQUEST['id']." AND location_id=".$Row['id'];    
                        $RS_remain = $objDB->Conn->Execute($remain_sql);*/
			$RS_remain = $objDB->Conn->Execute("select * from campaign_location where campaign_id =? AND location_id=?",array($_REQUEST['id'],$Row['id']));

                       	$remain_val = $RS_remain->fields['used_offers'];
			
                      
                        //25-1-2013 offer left
                        
                        /*$remain_sql1="select * from campaign_location where campaign_id =".$_REQUEST['id']." AND location_id=".$Row['id'];    
                        $RS_remain1 = $objDB->Conn->Execute($remain_sql1);*/
			$RS_remain1 = $objDB->Conn->Execute("select * from campaign_location where campaign_id =? AND location_id=?",array($_REQUEST['id'],$Row['id']));

                       	$remain_val1 = $RS_remain1->fields['offers_left'];
                        /*$sql = "select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE  customer_campaign_code=".$_REQUEST['id']." and location_id=".$Row['id'].")";
						$RS_redeemed = $objDB->Conn->Execute($sql);*/
			$RS_redeemed = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE  customer_campaign_code=? and location_id=?)",array($_REQUEST['id'],$Row['id']));

                       	$total_redeem = $RS_redeemed->RecordCount();
                        ?>
                    	
                        	<td class="table_th_17">
                    			<span total_offers="<?php echo $RS_remain1->fields['num_activation_code']; ?>" offers_left="<?php echo $remain_val1; ?>" total_redeem="<?php echo $total_redeem; ?>" remaining_val="<?=$remain_val?>" class="remainig_heading" id="remaining_<?=$Row['id']?>" name="remaining_<?=$Row['id']?>">No Of Reserved Coupon : <?=$remain_val?></span>
                                <span class="remain_value"  ><?=$remain_val1?></span>
                        	</td>
                       </tr>
                       <!-- <tr>
                        	<td colspan="4">
                            	<div class="middle_border">&nbsp;</div>
                            </td>
                        </tr> -->
                        
					  <?
					  $cnt++;
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
                           <!--        <tr>
					<td colspan="2">
						<div  class="activationcode_container_employee">
							<?php  echo  $merchant_msg["edit-compaign"]["Field_select_location"]; ?>
						</div>
					</td>
				  </tr> -->
				  <tr>
                                      <td colspan="2">
					<div> 
					<table class="locationgrid" id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1" >
						<tr class="locationgrid_heading">
						<td class="table_th_8">&nbsp;
						</td>
							<td class="table_th_60">
							<?php  echo  $merchant_msg["edit-compaign"]["Field_location"]; ?>
							</td>
							<td class="table_th_21">
							<?php  echo  $merchant_msg["edit-compaign"]["Field_number_of_activation_code"]; ?>
							<span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_total_offers"]; ?>">&nbsp;&nbsp;&nbsp;</span>
							</td>
							<td>
							<?php  echo  $merchant_msg["edit-compaign"]["Field_activation_code_left"]; ?>
							</td>
						</tr>
						
					</table>
					<div class="activationcode_container" >
					<table id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1" >
					
						<tr>
						<td class="table_th_8" align="left"></td>
						<td class="table_th_51" align="left">
<!--                                                 <input type="checkbox"  class="camp_location" name="location_id[]" value="<?=$RSStore->fields['id']?>" checked style="visibility:hidden;" />           -->
						<?php 
							/*
							if($RSStore->fields['location_name']!="")
								echo $RSStore->fields['location_name']."-<br/>";
							else 
							{
								$arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$RSStore->fields['id']);
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
						
                        <?php echo $RSStore->fields['address'].", ".$RSStore->fields['city'].", ".$RSStore->fields['state'].", ".$RSStore->fields['zip']?></td>
						
						
						<td class="table_th_21">
							<?php
							$arr_code = array();
					$arr_code['location_id'] = $RSStore->fields['id'];
                                         $sub_location_id = $RSStore->fields['id'];
					$arr_code['campaign_id'] = $_REQUEST['id'];
					$RS_num_act_code =  $objDB->Show("campaign_location", $arr_code);
                                        $remain_val=$RS_num_act_code->fields['used_offers'];
                                        $remain_val1=$RS_num_act_code->fields['offers_left'];
										/*$sql = "select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE  customer_campaign_code=".$_REQUEST['id']." and location_id=".$RSStore->fields['id'].")";
						$RS_redeemed = $objDB->Conn->Execute($sql);*/
		$RS_redeemed = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE  customer_campaign_code=? and location_id=?)",array($_REQUEST['id'],$RSStore->fields['id']));

                       	$total_redeem = $RS_redeemed->RecordCount();
							?>
                                                     <input type="textbox" placeholder="00" class="clsnum_activation_code" id="num_activation_code_<?=$RSStore->fields['id']?>" name="num_activation_code_<?=$RSStore->fields['id']?>" value="<?php echo $RS_num_act_code->fields['num_activation_code']; ?>" /> 
                        </td>
						<td>
                                                    <span total_offers="<?php echo $RS_num_act_code->fields['num_activation_code']; ?>" offers_left="<?php echo $remain_val1; ?>" total_redeem="<?php echo $total_redeem; ?>" remaining_val="<?=$remain_val?>" class="remainig_heading" id="remaining_<?=$RSStore->fields['id']?>" name="remaining_<?=$RSStore->fields['id']?>">No Of Reserved Coupon : <?=$remain_val?>
                                                    </span>
                                                            <span  class="remain_value" >
								
								<?=$remain_val1?>
                                                            </span> 
						<!--<select name="num_activation_code[]">
							<?
							for($i=1000; $i<=2500; $i+=500){
							?>
								<option value="<?=$i?>" <? if($RS_num_act_code->fields['num_activation_code']== $i) echo "selected";?>><?=$i?></option>
							<?
							}
							?>
                                                        
							
						</select> -->
						</td>
					  </tr>
                                          
                                        </table>
										</div>
                                        </div>
                                      </td>
					
                                 
					
				  </tr>
					<input type="hidden" name="hdn_location_id" id="hdn_location_id" value="<?=$RSStore->fields['id']?>" />
				  <?php } ?>
<!--                                  <tr>
                                      <td align="right">Time Zone :</td>
                                      <td align="left">
                                          <?php //echo $RS->fields['timezone']?>
                                          <select readonly name='timezone' id='timezone' style='width:500px'>
                                                <option value='-11:00,0' <?php if($RS->fields['timezone']=="-11:00,0"){echo "selected=\"selected\"";} ?> >(-11:00) Midway Island, Samoa</option>
                                                <option value='-10:00,0' <?php if($RS->fields['timezone']=="-10:00,0"){echo "selected=\"selected\"";} ?> >(-10:00) Hawaii</option>
                                                <option value='-09:00,1' <?php if($RS->fields['timezone']=="-09:00,1"){echo "selected=\"selected\"";} ?> >(-09:00) Alaska</option>
                                                <option value='-08:00,1' <?php if($RS->fields['timezone']=="-08:00,1"){echo "selected=\"selected\"";} ?> >(-08:00) Pacific Time (US & Canada)</option>
                                                <option value='-07:00,0' <?php if($RS->fields['timezone']=="-07:00,0"){echo "selected=\"selected\"";} ?> >(-07:00) Arizona</option>
                                                <option value='-07:00,1' <?php if($RS->fields['timezone']=="-07:00,1"){echo "selected=\"selected\"";} ?> >(-07:00) Mountain Time (US & Canada)</option>
                                                <option value='-06:00,0' <?php if($RS->fields['timezone']=="-06:00,0"){echo "selected=\"selected\"";} ?> >(-06:00) Central America, Saskatchewan</option>
                                                <option value='-06:00,1' <?php if($RS->fields['timezone']=="-06:00,1"){echo "selected=\"selected\"";} ?> >(-06:00) Central Time (US & Canada), Guadalajara, Mexico city</option>
                                                <option value='-05:00,0' <?php if($RS->fields['timezone']=="-05:00,0"){echo "selected=\"selected\"";} ?> >(-05:00) Indiana, Bogota, Lima, Quito, Rio Branco</option>
                                                <option value='-05:00,1' <?php if($RS->fields['timezone']=="-05:00,1"){echo "selected=\"selected\"";} ?> >(-05:00) Eastern time (US & Canada)</option>
                                                <option value='-04:00,1' <?php if($RS->fields['timezone']=="-04:00,1"){echo "selected=\"selected\"";} ?> >(-04:00) Atlantic time (Canada), Manaus, Santiago</option>
                                                <option value='-04:00,0' <?php if($RS->fields['timezone']=="-04:00,0"){echo "selected=\"selected\"";} ?> >(-04:00) Caracas, La Paz</option>
                                                <option value='-03:30,1' <?php if($RS->fields['timezone']=="-03:30,1"){echo "selected=\"selected\"";} ?> >(-03:30) Newfoundland</option>
                                                <option value='-03:00,1' <?php if($RS->fields['timezone']=="-03:00,1"){echo "selected=\"selected\"";} ?> >(-03:00) Greenland, Brasilia, Montevideo</option>
                                                <option value='-03:00,0' <?php if($RS->fields['timezone']=="-03:00,0"){echo "selected=\"selected\"";} ?> >(-03:00) Buenos Aires, Georgetown</option>
                                                <option value='-02:00,1' <?php if($RS->fields['timezone']=="-02:00,1"){echo "selected=\"selected\"";} ?> >(-02:00) Mid-Atlantic</option>
                                                <option value='-01:00,1' <?php if($RS->fields['timezone']=="-01:00,1"){echo "selected=\"selected\"";} ?>>(-01:00) Azores</option>
                                                <option value='-01:00,0' <?php if($RS->fields['timezone']=="-01:00,0"){echo "selected=\"selected\"";} ?> >(-01:00) Cape Verde Is.</option>
                                                <option value='00:00,0' <?php if($RS->fields['timezone']=="00:00,0"){echo "selected=\"selected\"";} ?> >(00:00) Casablanca, Monrovia, Reykjavik</option>
                                                <option value='00:00,1' <?php if($RS->fields['timezone']=="00:00,1"){echo "selected=\"selected\"";} ?> >(00:00) GMT: Dublin, Edinburgh, Lisbon, London</option>
                                                <option value='+01:00,1' <?php if($RS->fields['timezone']=="+01:00,1"){echo "selected=\"selected\"";} ?> >(+01:00) Amsterdam, Berlin, Rome, Vienna, Prague, Brussels</option>
                                                <option value='+01:00,0' <?php if($RS->fields['timezone']=="+01:00,0"){echo "selected=\"selected\"";} ?>>(+01:00) West Central Africa</option>
                                                <option value='+02:00,1' <?php if($RS->fields['timezone']=="+02:00,1"){echo "selected=\"selected\"";} ?>>(+02:00) Amman, Athens, Istanbul, Beirut, Cairo, Jerusalem</option>
                                                <option value='+02:00,0' <?php if($RS->fields['timezone']=="+02:00,0"){echo "selected=\"selected\"";} ?>>(+02:00) Harare, Pretoria</option>
                                                <option value='+03:00,1' <?php if($RS->fields['timezone']=="+03:00,1"){echo "selected=\"selected\"";} ?>>(+03:00) Baghdad, Moscow, St. Petersburg, Volgograd</option>
                                                <option value='+03:00,0' <?php if($RS->fields['timezone']=="+03:00,0"){echo "selected=\"selected\"";} ?>>(+03:00) Kuwait, Riyadh, Nairobi, Tbilisi</option>
                                                <option value='+03:30,0' <?php if($RS->fields['timezone']=="+03:30,0"){echo "selected=\"selected\"";} ?>>(+03:30) Tehran</option>
                                                <option value='+04:00,0' <?php if($RS->fields['timezone']=="+04:00,0"){echo "selected=\"selected\"";} ?>>(+04:00) Abu Dhadi, Muscat</option>
                                                <option value='+04:00,1' <?php if($RS->fields['timezone']=="+04:00,1"){echo "selected=\"selected\"";} ?>>(+04:00) Baku, Yerevan</option>
                                                <option value='+04:30,0' <?php if($RS->fields['timezone']=="+04:30,0"){echo "selected=\"selected\"";} ?>>(+04:30) Kabul</option>
                                                <option value='+05:00,1' <?php if($RS->fields['timezone']=="+05:00,1"){echo "selected=\"selected\"";} ?>>(+05:00) Ekaterinburg</option>
                                                <option value='+05:00,0' <?php if($RS->fields['timezone']=="+05:00,0"){echo "selected=\"selected\"";} ?>>(+05:00) Islamabad, Karachi, Tashkent</option>
                                                <option value='+05:30,0' <?php if($RS->fields['timezone']=="+05:30,0"){echo "selected=\"selected\"";} ?>>(+05:30) Chennai, Kolkata, Mumbai, New Delhi, Sri Jayawardenepura</option>
                                                <option value='+05:45,0' <?php if($RS->fields['timezone']=="+05:45,0"){echo "selected=\"selected\"";} ?>>(+05:45) Kathmandu</option>
                                                <option value='+06:00,0' <?php if($RS->fields['timezone']=="+06:00,0"){echo "selected=\"selected\"";} ?>>(+06:00) Astana, Dhaka</option>
                                                <option value='+06:00,1' <?php if($RS->fields['timezone']=="+06:00,1"){echo "selected=\"selected\"";} ?>>(+06:00) Almaty, Nonosibirsk</option>
                                                <option value='+06:30,0' <?php if($RS->fields['timezone']=="+06:30,0"){echo "selected=\"selected\"";} ?>>(+06:30) Yangon (Rangoon)</option>
                                                <option value='+07:00,1' <?php if($RS->fields['timezone']=="+07:00,1"){echo "selected=\"selected\"";} ?>>(+07:00) Krasnoyarsk</option>
                                                <option value='+07:00,0' <?php if($RS->fields['timezone']=="+07:00,0"){echo "selected=\"selected\"";} ?>>(+07:00) Bangkok, Hanoi, Jakarta</option>
                                                <option value='+08:00,0' <?php if($RS->fields['timezone']=="+08:00,0"){echo "selected=\"selected\"";} ?>>(+08:00) Beijing, Hong Kong, Singapore, Taipei</option>
                                                <option value='+08:00,1' <?php if($RS->fields['timezone']=="+08:00,1"){echo "selected=\"selected\"";} ?>>(+08:00) Irkutsk, Ulaan Bataar, Perth</option>
                                                <option value='+09:00,1' <?php if($RS->fields['timezone']=="+09:00,1"){echo "selected=\"selected\"";} ?>>(+09:00) Yakutsk</option>
                                                <option value='+09:00,0' <?php if($RS->fields['timezone']=="+09:00,0"){echo "selected=\"selected\"";} ?>>(+09:00) Seoul, Osaka, Sapporo, Tokyo</option>
                                                <option value='+09:30,0' <?php if($RS->fields['timezone']=="+09:30,0"){echo "selected=\"selected\"";} ?>>(+09:30) Darwin</option>
                                                <option value='+09:30,1' <?php if($RS->fields['timezone']=="+09:30,1"){echo "selected=\"selected\"";} ?>>(+09:30) Adelaide</option>
                                                <option value='+10:00,0' <?php if($RS->fields['timezone']=="+10:00,0"){echo "selected=\"selected\"";} ?>>(+10:00) Brisbane, Guam, Port Moresby</option>
                                                <option value='+10:00,1' <?php if($RS->fields['timezone']=="+10:00,1"){echo "selected=\"selected\"";} ?>>(+10:00) Canberra, Melbourne, Sydney, Hobart, Vladivostok</option>
                                                <option value='+11:00,0' <?php if($RS->fields['timezone']=="+11:00,0"){echo "selected=\"selected\"";} ?>>(+11:00) Magadan, Solomon Is., New Caledonia</option>
                                                <option value='+12:00,1' <?php if($RS->fields['timezone']=="+12:00,1"){echo "selected=\"selected\"";} ?>>(+12:00) Auckland, Wellington</option>
                                                <option value='+12:00,0' <?php if($RS->fields['timezone']=="+12:00,0"){echo "selected=\"selected\"";} ?>>(+12:00) Fiji, Kamchatka, Marshall Is.</option>
                                                <option value='+13:00,0' <?php if($RS->fields['timezone']=="+13:00,0"){echo "selected=\"selected\"";} ?>>(+13:00) Nuku'alofa</option>
                                                
                                        </select> <div  id='helptext'></div>  
                                        <input type="hidden" name="time_zone_v" id="time_zone_v" value=""/>
<script type='text/javascript'>TORBIT.dom={get:function(a){return document.getElementsByTagName(a)},gh:function(){return TORBIT.dom.get("head")[0]},ah:function(a){TORBIT.dom.gh().appendChild(a)},ce:function(a){return document.createElement(a)},gei:function(a){return document.getElementById(a)},ls:function(a,b){var c=TORBIT.dom.ce("script");c.type="text/javascript";c.src=a;if("function"==typeof b){c.onload=function(){if(!c.onloadDone){c.onloadDone=true;b()}};c.onreadystatechange=function(){if(("loaded"===c.readyState||"complete"===c.readyState)&&!c.onloadDone){c.onloadDone=true;b()}}}TORBIT.dom.ah(c)}};(function(){var a=window.TORBIT.timing={};var b=function(){if(window.performance==void 0||window.performance.timing==void 0){k(e);h(f);return}h(d)};var c=function(){var b=window.performance.timing;var c=b.navigationStart;for(var d in b){var e=b[d];if(typeof e!="number"||e==0){continue}a[d]=e-c;var f=/(.+)End$/i.exec(d);if(f){a[f[1]+"Elapsed"]=b[d]-b[f[1]+"Start"]}}};var d=function(){c();g()};var e=function(){a.or=(new Date).getTime()-TORBIT.start_time};var f=function(){a.ol=(new Date).getTime()-TORBIT.start_time;g()};var g=function(){var b="/torbit-timing.php?";for(var c in a){b+=c+"="+a[c]+"&"}if(TORBIT.fv==1)b+="fv=1&";if(TORBIT.opt==0)b+="not_opt=1&";TORBIT.dom.ls(b)};var h=function(a){if(typeof window.onload!="function"){return window.onload=a}var b=window.onload;window.onload=function(){b();a()}};var i=false;var j=function(){};var k=function(a){j=l(a);m()};var l=function(a){return function(){if(!i){i=true;a()}}};var m=function(){if(document.addEventListener){document.addEventListener("DOMContentLoaded",j,false)}else if(document.attachEvent){document.attachEvent("onreadystatechange",j);var a=false;try{a=window.frameElement==null}catch(b){}if(document.documentElement.doScroll&&a){n()}}};var n=function(){if(i){return}try{document.documentElement.doScroll("left")}catch(a){setTimeout(n,5);return}j()};b()})();TORBIT.opt=0;TORBIT.fv=1;</script></body>
                                      </td>
<?php   
  $offset=(5*60*60)+(30*60); //converting 5 hours to seconds.  
  $dateFormat="d-m-Y h:iA";    
 date("d-m-Y h:i:s", mktime(date("H")+1,date("i"),date("s"),date("n"),date("j"),date("y")))."</br>"; 
  $timeNdate=gmdate($dateFormat, time()+$offset);
?>

<script>    
jQuery('#timezone').change(function(){
    var text=  jQuery('#timezone :selected').val();    
    jQuery('#time_zone_v').val(text);
});
</script> </tr>-->
                                 <tr>
                    <td colspan="2">&nbsp;
                    </td>
                  </tr>
				  <tr>
					<td align="right">
					<?php  echo  $merchant_msg["edit-compaign"]["Field_date"]; ?>
					</td>
					<td align="left">
					
					&nbsp; &nbsp; &nbsp; &nbsp;<?php  echo  $merchant_msg["edit-compaign"]["Field_start_date"]; ?><input readonly type="text" class="datetimepicker" name="start_date" id="start_date" value="<?=date("Y-m-d H:i:s",strtotime($RS->fields['start_date']))?>">
					 <span data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_startdate"]; ?>">&nbsp;&nbsp;&nbsp;</span>
	
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php  echo  $merchant_msg["edit-compaign"]["Field_end_date"]; ?><input type="text" readonly name="expiration_date" class="datetimepicker" id="expiration_date" value="<?=date("Y-m-d H:i:s",strtotime($RS->fields['expiration_date']))?>">
					<span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_expiredate"]; ?>">&nbsp;&nbsp;&nbsp;</span>

					</td>
				  </tr>
				  
				 
				   <!--b-->
                  <tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_redeem_points"]; ?></td>
					<td align="left">
						<table style="width: 100%">
							<tr>
							<td align="left" width="50%"><input type="text" id="redeem_rewards" name="redeem_rewards" value="<?=$RS->fields['redeem_rewards']?>"/><span  data-toggle="tooltip" data-placement="right" data-html="true"  title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_redeem_points"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>
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
                                                                 ?></b><span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_available_points"]; ?>">&nbsp;&nbsp;&nbsp;</span>
			
		
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
							<td align="left" width="50%"><input type="text" id="referral_rewards" name="referral_rewards" value="<?=$RS->fields['referral_rewards']?>"/><span  data-toggle="tooltip" data-placement="right" data-html="true" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_sharing_points"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>
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
												<span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="$<?=$Row->price?> = <?=$Row->points?><?php  echo  $merchant_msg["edit-compaign"]["Tooltip_purchase_scanflip_points"]; ?>">&nbsp;&nbsp;&nbsp;</span>
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
                        <table style="width: 100%">
                            <tr>
                            <td align="left" width="50%">
                                <input type="text" customer_limit='<?=$RS->fields['max_no_sharing']?>' placeholder="00" id="max_no_of_sharing" name="max_no_of_sharing" value="<?=$RS->fields['max_no_sharing']?>" /><span  data-toggle="tooltip" data-placement="right" data-html="true" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_referral_customer_limit"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>                              <?php
                                    /*$Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$_REQUEST['id']." and referred_customer_id<>0";
                                    $RS_shared = $objDB->Conn->Execute($Sql_shared);*/
				$RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?",array($_REQUEST['id'],0));

                                ?>
                            </td>
                            <td align="left">
                                            
                            </td>
                        </tr>			
                        </table>        
                    </td>
                 </tr>
                    
                                 
                                    <tr>
					<td align="right"><?php
                                             echo  $merchant_msg["edit-compaign"]["Field_referral_customer_left"]; 
                                    
                                    ?></td>
                                        <td>
											<span id="referral_customer_limit_left" no_of_shared='<?php echo $RS->fields['no_of_shared'] ?>' customer_limit_left='<?php echo $RS->fields['max_no_sharing']-$RS->fields['no_of_shared'] ?>'>
                                            <?php 
												echo $RS->fields['max_no_sharing']-$RS->fields['no_of_shared'];      
											//echo $RS->fields['max_no_sharing']- $RS_shared->RecordCount();
											?>
											</span>
                                        </td>
                                  </tr>
                                   <tr>
				    <td align="right" valign="top">&nbsp;  </td>
                                    <td align="left" valign="top">
                                        <input type="radio" name="is_walkin" class="is_walkin" id="groupid" value="0" style="float:left;" <?php if($RS->fields['is_walkin'] == 0){ echo "checked"; }  ?> onclick ="check_is_walkin(this.value)" /> <label class="chk_align1"> <?php  echo  $merchant_msg["edit-compaign"]["Field_group"]; ?></label>
                                        <input type="radio" name="is_walkin" class="is_walkin" id="walkinid" value="1" style="float:left;" <?php if($RS->fields['is_walkin'] == 1){ echo "checked"; }  ?> onclick="check_is_walkin(this.value)" /><label class="chk_align1"> <?php  echo  $merchant_msg["edit-compaign"]["Field_walking"]; ?></label> <span  data-toggle="tooltip" data-placement="right" data-html="true" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_walkin"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span>

                                    </td>
                                   </tr>
				  <tr id="group" style="display:<?php if($RS->fields['is_walkin'] == "1"){ echo "none"; }  else { echo "";} ?>">
				    <td align="right" valign="top"><?php  echo  $merchant_msg["edit-compaign"]["Field_camapaign_visibility"]; ?><span  data-toggle="tooltip" data-placement="right" data-html="true" class="notification_tooltip" title="<?php  echo  $merchant_msg["edit-compaign"]["Tooltip_visibility"]; ?>">&nbsp;&nbsp;&nbsp;</span> :  </td>
				    <td align="left" valign="top">
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="28%" align="left">
		<input type="checkbox" name="assign_group" id="assign_group" class="chk_private" value="1" <?php if($RS->fields['level'] == "1"){ echo "checked"; }  ?> />
		<label class="chk_align" for="assign_group"><?php  echo  $merchant_msg["edit-compaign"]["Field_public"]; ?></label>
	</td>
    <td width="*" align="left" >
        &nbsp;
		<!--<input type="radio" name="assign_group" value="2" <?php if($RS->fields['level'] == "0"){ echo "checked"; }  ?>/>
		Members  -->
	</td>
  </tr>
  
  <tr>
      <td colspan="2">
          <table class="location_listing_area" >
  <?php if($_SESSION['merchant_info']['merchant_parent'] != 0){
    
	$Sql = "select l.*,l.location_name ,l.id lid, mg.* 
	from merchant_groups mg , locations l 
	where mg.isDeleted!=1 and mg.active = 1 and l.active = 1 and l.id = mg.location_id and  mg.location_id =". $sub_location_id;
	
	if($RS->fields['level']==0)
	{
    // 28-09-2013 remove dist list with zero customers
	
   /* $Sql = "select l.*,l.location_name ,l.id lid, mg.*,(select count(*) as Cust_Total from merchant_subscribs mu where mu.group_id =mg.id and mu.user_id in (select id from customer_user cu where cu.active=1)) Total 
    from merchant_groups mg , locations l 
    where mg.isDeleted!=1 and mg.active = 1 and l.active = 1 and l.id = mg.location_id and  mg.location_id =". $sub_location_id;*/
	
$RS_2 = $objDB->Conn->Execute("select l.*,l.location_name ,l.id lid, mg.*,(select count(*) as Cust_Total from merchant_subscribs mu where mu.group_id =mg.id and mu.user_id in (select id from customer_user cu where cu.active=1)) Total 
    from merchant_groups mg , locations l 
    where mg.isDeleted!=? and mg.active = ? and l.active = ? and l.id = mg.location_id and  mg.location_id =?",array(1,1,1,$sub_location_id));
										
    // 28-09-2013 remove dist list with zero customers
	}
	else
	{
	  // 07-10-2013 remove dist list if deal is public
	
   /* $Sql = "select l.*,l.location_name ,l.id lid, mg.*,(select count(*) as Cust_Total from merchant_subscribs mu where mu.group_id =mg.id and mu.user_id in (select id from customer_user cu where cu.active=1)) Total 
    from merchant_groups mg , locations l 
    where mg.isDeleted!=1 and mg.active = 1 and l.active = 1 and l.id = mg.location_id and mg.private=1 and mg.location_id =". $sub_location_id;*/

	$RS_2 = $objDB->Conn->Execute("select l.*,l.location_name ,l.id lid, mg.*,(select count(*) as Cust_Total from merchant_subscribs mu where mu.group_id =mg.id and mu.user_id in (select id from customer_user cu where cu.active=1)) Total 
    from merchant_groups mg , locations l 
    where mg.isDeleted!=? and mg.active = ? and l.active = ? and l.id = mg.location_id and mg.private=? and mg.location_id =",array(1,1,1,1,$sub_location_id));
										
    // 07-10-2013 remove dist list if deal is public
	}
	
    $RS_2 = $objDB->Conn->Execute($Sql);
    
    
   if($RS_2->RecordCount()>0)
    {
      $temp_val=$RS_2->fields['lid'];
    ?>
       <tr id="locationid_<?php echo $RS_2->fields['lid']; ?>" name="hello">
				<td class="table_th_5">
					<img src="<?php echo ASSETS_IMG ?>/m/marker_rounded_grey.png" />
				</td>
               <td class="table_th_34">
                    <?php //echo  $RS_2->fields['location_name']; ?>
                    <?php echo $RS_2->fields['address'].", ".$RS_2->fields['city'].", ".$RS_2->fields['state'].", ".$RS_2->fields['zip']?>&nbsp;:&nbsp;
                </td>
    <td >
					<input type="hidden" name="hdn_campaign_type_<?php echo $RS_2->fields['lid']; ?>" id="hdn_campaign_type_<?php echo $RS_2->fields['lid']; ?>" value="0" />
                                        <input type="hidden" name="hdn_campaign_type1_<?php echo $RS_2->fields['lid']; ?>" id="hdn_campaign_type1_<?php echo $RS_2->fields['lid']; ?>" value="1" />
                                        
                    <ul class="locationgroup_" id="locationgroup_<?php echo $RS_2->fields['lid']; ?>">
                        <?php
                        while($Row=$RS_2->FetchRow())
                        {
							if($Row['private']==1)
							{
								 //  echo $Row['id']."==".$_REQUEST['id']."==".$_SESSION['merchant_id'];
									  $array_2 = array();
									$array_2['group_id'] = $Row['id'];
									$array_2['campaign_id'] = $_REQUEST['id'];
									//$array_2['merchant_id'] = $_SESSION['merchant_id'];
									$RSCheck = $objDB->Show("campaign_groups",$array_2);
												//print_r($RSCheck);
							?>
							 <li class="location_li">
                                <input type="checkbox" id="chk_location_groups" name="chk_location_groups[]"  <? if($RSCheck->RecordCount()>0) echo "checked";?> value="<?php echo $Row['id'] ;?>" <?php if($Row['private'] == 1){ echo  "class='private_group' private_group=private_group";}else{echo  "class='other_group'";} ?> >
                                    <?php //echo $Row['group_name']; ?>
                                Location Subscribers
                            </li>
							<?php
								$camp_type=0;
								if($RS->fields['level']==1)
								{
									$camp_type=1;
								}
								else
								{
									if($Row['private'] == 1)
									{
										$camp_type=2;
									}
									else
									{
										$camp_type=3;
									}
								}
							?>
							<script type="text/javascript">
                                                           
								jQuery("#hdn_campaign_type_<?php echo $RS_2->fields['lid'] ?>").val("<?php echo $camp_type ?>");
							</script>		
							<?php
							}
							else if($Row['private']==0) 
							{
								if($Row['Total']>0) // condition to other show dist list with customers > 0 
								{
									  //  echo $Row['id']."==".$_REQUEST['id']."==".$_SESSION['merchant_id'];
									  $array_2 = array();
									$array_2['group_id'] = $Row['id'];
									$array_2['campaign_id'] = $_REQUEST['id'];
									//$array_2['merchant_id'] = $_SESSION['merchant_id'];
									$RSCheck = $objDB->Show("campaign_groups",$array_2);
												//print_r($RSCheck);
                            ?>
                            <li class="location_li">
                                <input type="checkbox" id="chk_location_groups" name="chk_location_groups[]"  <? if($RSCheck->RecordCount()>0) echo "checked";?> value="<?php echo $Row['id'] ;?>" <?php if($Row['private'] == 1){ echo  "class='private_group' private_group=private_group";}else{echo  "class='other_group'";} ?> ><?php echo $Row['group_name']; ?>
                            </li>
							<?php
								$camp_type=0;
								if($RS->fields['level']==1)
								{
									$camp_type=1;
								}
								else
								{
									if($Row['private'] == 1)
									{
										$camp_type=2;
									}
									else
									{
										$camp_type=3;
									}
								}
							?>
							<script type="text/javascript">
                                                           
								jQuery("#hdn_campaign_type_<?php echo $RS_2->fields['lid'] ?>").val("<?php echo $camp_type ?>");
							</script>
                        <?php
								}
							}
						} 
						?>
                    </ul>
					<script type="text/javascript">
                    jQuery(document).ready(function(){
                       var checked_val= jQuery('#locationgroup_<?php echo $temp_val; ?> .other_group:checked').size();
                       if(checked_val >0)
                       {
                            jQuery("#hdn_campaign_type1_<?php echo $temp_val; ?>").val(checked_val);
                       }
                       if(jQuery('#locationgroup_<?php echo $temp_val; ?> .private_group').is(':checked'))
                       {
                           jQuery('#locationgroup_<?php echo $temp_val; ?> .other_group').attr("disabled", true);
                           jQuery('#locationgroup_<?php echo $temp_val; ?> .private_group').attr("disabled", false);
                          
                       }
                       else
                           {
                               jQuery('#locationgroup_<?php echo $temp_val; ?> .other_group').attr("disabled", false);
                               jQuery('#locationgroup_<?php echo $temp_val; ?> .private_group').attr("disabled", true);
                           }
                    });
                
            </script>
                </td>
            </tr>
			
    <?php
    }
  }
  else{
   $array12 = $location_id = array();
$array12['campaign_id'] = $_REQUEST['id'];
$RSComp = $objDB->Show("campaign_location", $array12);
while($Row12 = $RSComp->FetchRow()){
	//$location_id[] = $Row12['location_id'];
         $Sql = "select l.*,l.location_name ,l.id lid, mg.* 
         from merchant_groups mg , locations l 
         where mg.isDeleted!=1 and l.id = mg.location_id and  mg.active=1 and l.active= 1 and mg.location_id =". $Row12['location_id'];
    
	//echo $RS->fields['level'];
	
	
	if($RS->fields['level']==0)
	{
	// 28-09-2013 remove dist list with zero customers
	$Sql = "select l.*,l.location_name ,l.id lid, mg.*,(select count(*) as Cust_Total from merchant_subscribs mu where mu.group_id =mg.id and mu.user_id in (select id from customer_user cu where cu.active=1)) Total 
	from merchant_groups mg , locations l 
	where mg.isDeleted!=1 and l.id = mg.location_id and l.id =".  $Row12['location_id']." and mg.active=1";
	// 28-09-2013 remove dist list with zero customers
	//echo "if";
	}
	else
	{
	// 07-10-2013 remove dist list if deal is public
	$Sql = "select l.*,l.location_name ,l.id lid, mg.*,(select count(*) as Cust_Total from merchant_subscribs mu where mu.group_id =mg.id and mu.user_id in (select id from customer_user cu where cu.active=1)) Total 
	from merchant_groups mg , locations l 
	where mg.isDeleted!=1 and l.id = mg.location_id and l.id =".  $Row12['location_id']." and mg.active=1 and mg.private=1";
	// 07-10-2013 remove dist list if deal is public
	//echo "else";
	}
	
	
	//echo "===".$Sql."===";
    $RS_1 = $objDB->Conn->Execute($Sql);
    
    //echo "aaa==".$RS_1->RecordCount()."====".$RS_1->fields['private'];
   if($RS_1->RecordCount()>0)
    {
       $temp_val=$RS_1->fields['lid'];
      
    ?>
       <tr id="locationid_<?php echo $RS_1->fields['lid']; ?>" >
				<td class="table_th_5">
					<img src="<?php echo ASSETS_IMG ?>/m/marker_rounded_grey.png" />
				</td>
               <td class="table_th_34">
                    <?php //echo  $RS_1->fields['location_name']; ?>
                     <?php echo $RS_1->fields['address'].", ".$RS_1->fields['city'].", ".$RS_1->fields['state'].", ".$RS_1->fields['zip']?>&nbsp;:&nbsp;
                </td>
		<td >
					<input type="hidden"  name="hdn_campaign_type_<?php echo $RS_1->fields['lid']; ?>" id="hdn_campaign_type_<?php echo $RS_1->fields['lid']; ?>" value="0" />
                                        <input type="hidden"  name="hdn_campaign_type1_<?php echo $RS_1->fields['lid']; ?>" id="hdn_campaign_type1_<?php echo $RS_1->fields['lid']; ?>" value="1" />
                    <ul class="locationgroup_" id="locationgroup_<?php echo $RS_1->fields['lid']; ?>">
                        <?php
                        while($Row=$RS_1->FetchRow())
                        {
							if($Row['private']==1)
							{
								$array_1 = array();
								$array_1['group_id'] = $Row['id'];
								$array_1['campaign_id'] = $_REQUEST['id'];
								//$array_1['merchant_id'] = $_SESSION['merchant_id'];
											
								$RSCheck = $objDB->Show("campaign_groups",$array_1);
								//echo "bbb - ".$RSCheck->RecordCount();
											//$lid=$RS_1->fields['lid'];
                            ?>
								<li class="location_li" >
								<!--7-8-2013
									<input type="checkbox" id="chk_location_groups_<?php echo $Row['id']; ?>" class="chk_location_groups" name="chk_location_groups[]"  <? if($RSCheck->RecordCount()>0) echo "checked";?> value="<?php echo $Row['id'] ;?>" <?php if($Row['private'] == 1){ echo  "class='private_group' private_group=private_group";}else{echo  "class='other_group'";} ?> groupname="<?php echo $Row['group_name'];?>" ><?php echo $Row['group_name']; ?>
									7-8-2013-->
								<!--7-8-2013 remove class=chk_location_groups -->
									<!-- 
									<input type="checkbox" id="chk_location_groups_<?php echo $Row['id']; ?>" name="chk_location_groups[]" sharad="<?php echo $RSCheck->RecordCount();?>" <? if($RSCheck->RecordCount()>0) echo "checked";?> value="<?php echo $Row['id'] ;?>" <?php if($Row['private'] == 1){ echo  "class='private_group' private_group=private_group";}else{echo  "class='other_group'";} ?> groupname="<?php echo $Row['group_name'];?>" ><?php echo $Row['group_name']; ?>
									-->
								<!--7-8-2013-->
								
								<!--05-10-2013 remove dist list bydefault if private selected   -->
								
									<input type="checkbox" id="chk_location_groups_<?php echo $Row['id']; ?>" name="chk_location_groups[]" sharad="<?php echo $RSCheck->RecordCount();?>" <? if($RSCheck->RecordCount()>0) echo "checked";?> value="<?php echo $Row['id'] ;?>" <?php if($Row['private'] == 1){ echo  "class='private_group' private_group=private_group";}else{echo  "class='other_group'";} ?> groupname="<?php echo $Row['group_name'];?>" >
                                                                            <?php //echo $Row['group_name']; ?>
                                                                                  <label class="chk_align">  Location Subscribers</label>
														
								<!--05-10-2013-->
								
								</li>
								<?php
									$camp_type=0;
									if($RS->fields['level']==1)
									{
										$camp_type=1;
									}
									else
									{
										if($Row['private'] == 1 && $RSCheck->RecordCount()>0)
										{
											$camp_type=2;
										}
										else if($Row['private'] == 0 && $RSCheck->RecordCount()>0)
										{
											$camp_type=3;
										}
									}
								?>
								<?php
								if($RSCheck->RecordCount()>0)
								{
								?>
								<script type="text/javascript">
									jQuery("#hdn_campaign_type_<?php echo $Row['lid'] ?>").val("<?php echo $camp_type ?>");
		
								</script>
								<?php
								}
							}
							else if($Row['private']==0) 
							{
								if($Row['Total']>0) // condition to other show dist list with customers > 0 
								{
                          $array_1 = array();
			$array_1['group_id'] = $Row['id'];
			$array_1['campaign_id'] = $_REQUEST['id'];
			//$array_1['merchant_id'] = $_SESSION['merchant_id'];
                        
			$RSCheck = $objDB->Show("campaign_groups",$array_1);
			//echo "bbb - ".$RSCheck->RecordCount();
                        //$lid=$RS_1->fields['lid'];
                            ?>
                            <li class="location_li" >
							<!--7-8-2013
                                <input type="checkbox" id="chk_location_groups_<?php echo $Row['id']; ?>" class="chk_location_groups" name="chk_location_groups[]"  <? if($RSCheck->RecordCount()>0) echo "checked";?> value="<?php echo $Row['id'] ;?>" <?php if($Row['private'] == 1){ echo  "class='private_group' private_group=private_group";}else{echo  "class='other_group'";} ?> groupname="<?php echo $Row['group_name'];?>" ><?php echo $Row['group_name']; ?>
							    7-8-2013-->
							<!--7-8-2013 remove class=chk_location_groups -->
								<!-- 
                                <input type="checkbox" id="chk_location_groups_<?php echo $Row['id']; ?>" name="chk_location_groups[]" sharad="<?php echo $RSCheck->RecordCount();?>" <? if($RSCheck->RecordCount()>0) echo "checked";?> value="<?php echo $Row['id'] ;?>" <?php if($Row['private'] == 1){ echo  "class='private_group' private_group=private_group";}else{echo  "class='other_group'";} ?> groupname="<?php echo $Row['group_name'];?>" ><?php echo $Row['group_name']; ?>
								-->
							<!--7-8-2013-->
							
							<!--05-10-2013 remove dist list bydefault if private selected   -->
							
								<input type="checkbox" id="chk_location_groups_<?php echo $Row['id']; ?>" name="chk_location_groups[]" sharad="<?php echo $RSCheck->RecordCount();?>" <? if($RSCheck->RecordCount()>0) echo "checked";?> value="<?php echo $Row['id'] ;?>" <?php if($Row['private'] == 1){ echo  "class='private_group' private_group=private_group";}else{echo  "class='other_group'";} ?> groupname="<?php echo $Row['group_name'];?>" >
                                                                    <?php echo $Row['group_name']; ?>
													
							<!--05-10-2013-->
							
                            </li>
							<?php
								$camp_type=0;
								if($RS->fields['level']==1)
								{
									$camp_type=1;
								}
								else
								{
									if($Row['private'] == 1 && $RSCheck->RecordCount()>0)
									{
										$camp_type=2;
									}
									else if($Row['private'] == 0 && $RSCheck->RecordCount()>0)
									{
										$camp_type=3;
									}
								}
							?>
							<?php
							if($RSCheck->RecordCount()>0)
							{
							?>
							<script type="text/javascript">
								jQuery("#hdn_campaign_type_<?php echo $Row['lid'] ?>").val("<?php echo $camp_type ?>");
    
							</script>
							<?php
							}
							?>
			    <!--
					8-8-2013
			       <script type="text/javascript">
				   
				jQuery("#chk_location_groups_<?php echo $Row['id']; ?>").click(function () {
					
					
					jQuery("#locationgroup_<?php echo $RS_1->fields['lid']; ?> > li").each(function () {
                                                 
						  var value_group=jQuery("#chk_location_groups_<?php echo $Row['id']?>").attr("groupname");
						if (jQuery('#chk_location_groups_<?php echo $Row['id']?>').is(':checked'))
						{
							if(value_group == "private")
							{
								 jQuery("#locationgroup_<?php echo $RS_1->fields['lid']; ?> input:checkbox" ).attr("disabled", true);
								 jQuery("#locationgroup_<?php echo $RS_1->fields['lid']; ?> input:checkbox" ).removeAttr("checked");
								 jQuery('#chk_location_groups_<?php echo $Row['id']?>').attr('disabled',false);
								jQuery('#chk_location_groups_<?php echo $Row['id']?>').attr('checked','checked');
							}
							
						}
						else
						{
							jQuery("#locationgroup_<?php echo $RS_1->fields['lid']; ?> input:checkbox" ).attr("disabled", false);
							
						}
					});
					
					});
					
			    </script>
				8-8-2013
			    -->
                    <?php 
							}
						}
						
					} 
					?>
			
                    </ul>
		    
		 <script type="text/javascript">
                    jQuery(document).ready(function(){
                       var checked_val= jQuery('#locationgroup_<?php echo $temp_val; ?> .other_group:checked').size();
                       if(checked_val >0)
                       {
                            jQuery("#hdn_campaign_type1_<?php echo $temp_val; ?>").val(checked_val);
                       }
                       if(jQuery('#locationgroup_<?php echo $temp_val; ?> .private_group').is(':checked'))
                       {
                           jQuery('#locationgroup_<?php echo $temp_val; ?> .other_group').attr("disabled", true);
                           jQuery('#locationgroup_<?php echo $temp_val; ?> .private_group').attr("disabled", false);
                          
                       }
                       else
                           {
                               jQuery('#locationgroup_<?php echo $temp_val; ?> .other_group').attr("disabled", false);
                               jQuery('#locationgroup_<?php echo $temp_val; ?> .private_group').attr("disabled", true);
                           }
                    });
                
            </script>
		    
                </td>
            </tr>
			
    <?php
    }

    ?>
            
<?php }   
  }
  
  ?>
          </table>
      </td>
  </tr>
  <!--<tr>
    <td>&nbsp;</td>
    <td align="left">
	<?
	if($RSGroups->RecordCount()>0){
		while($Row = $RSGroups->FetchRow()){
			$array = array();
			$array['group_id'] = $Row['id'];
			$array['campaign_id'] = $_REQUEST['id'];
			$array['merchant_id'] = $_SESSION['merchant_id'];
			$RSCheck = $objDB->Show("campaign_groups",$array);

	?>	
			<input type="checkbox" name="group_id[]" value="<?=$Row['id']?>" <? if($RSCheck->RecordCount()>0) echo "checked";?> /> <?=$Row['group_name']?> <br />
	<?
		}
	}
	?>
	</td>
  </tr> -->
</table>
					</td>
			      </tr>
				  <tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_camapaign_limitation"]; ?></td>
					<td align="left"> 
					<?php  
						//echo $RS->fields['number_of_use']; 
					?>
					<input  type="radio" id="one_per_customer" name="number_of_use" value="1" <? if($RS->fields['number_of_use'] == 1) { echo "checked"; } ?> /> 
					<label class="chk_align"><?php  echo  $merchant_msg["edit-compaign"]["Field_one_per_customer"]; ?></label>
					
					<input  type="radio" id="one_per_customer_per_day" name="number_of_use" value="2" <? if($RS->fields['number_of_use'] == 2) { echo "checked"; }  ?> />
					<label class="chk_align"><?php  echo  $merchant_msg["edit-compaign"]["Field_one_per_customer_per_day"]; ?></label>
					
					<input   type="radio" id="multiple_use" name="number_of_use" value="3" <? if($RS->fields['number_of_use'] == 3) { echo "checked"; } ?> />
					<label class="chk_align"><?php  echo  $merchant_msg["edit-compaign"]["Field_redeem_point_visit"]; ?></label>
					</td>
				  </tr>
				  
				   <tr>
					<td align="right"><?php  echo  $merchant_msg["edit-compaign"]["Field_valid_new_customer"]; ?></td>
					<td align="left">
					<input type="checkbox" value="<? echo $RS->fields['new_customer']; ?>" <? if($RS->fields['new_customer'] == 1) echo "checked";?> name="new_customer_only" id="new_customer_only"  /> 
					
					</td>
				  </tr>
				   
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<input type="hidden" id="id" name="id" value="<?=$_REQUEST['id']?>">
						<input type="submit" name="btnEditCampaigns" id="btnEditCampaigns" value="<?php echo $merchant_msg['index']['btn_save'];?>" onclick="mycall()" >
                                                <!--// 369-->
                                                <script>
                                                function btncanCampaign(){                                                
                                                window.location="<?=WEB_PATH?>/merchant/compaigns.php?action=active";}
                                                function mycall()
                                                {
                                                     
                                                       jQuery('#description').val(tinyMCE.get('description').getContent());
                                                        //jQuery('#deal_detail_description').val(tinyMCE.get('deal_detail_description').getContent());
                                                        jQuery('#terms_condition').val(tinyMCE.get('terms_condition').getContent());							  
                                                }
                                                </script>
                                                <input type="submit" name="btnCanCampaigns" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanCampaign()">
                                                <input type="hidden" id="required_point" name="required_point" value="<?=$RS->fields['block_point']?>">
                                                <input type="hidden" id="block_required_point" name="block_required_point" value="<?=$RS->fields['block_point']?>">
                        						<input type="hidden" id="remaining_point" name="remaining_point" value="">
                                                <input type="hidden" id="exact_availabel_point" name="exact_availabel_point" value="">

<script type="text/javascript">
    
    
	
var alert_msg = "";
	var check_active="<?php echo $check_active;?>";
    if(check_active=="true")
    {
		jQuery(".camp_location").each(function() {
			if(jQuery(this).attr("checked")=="checked")
			{
                            jQuery(this).unbind("click");
			   jQuery(this).attr("onclick","return false");
			   jQuery(this).attr("onchange","return false");
			} 
    	});
        jQuery("#start_date").attr("disabled","true");       
        jQuery("#expiration_date").attr("disabled","true");
	jQuery("#redeem_rewards").attr("disabled","true"); 
	jQuery("#referral_rewards").attr("disabled","true"); 
	
	//02-09-2013
	
	//alert("active");
	
	jQuery("#one_per_customer").unbind("click");
	jQuery("#one_per_customer").attr("onclick","return false");
	jQuery("#one_per_customer").attr("onchange","return false");
	jQuery("#one_per_customer").attr("disabled","disabled");
	jQuery("#one_per_customer_per_day").attr("disabled","disabled");
	jQuery("#multiple_use").attr("disabled","disabled");
	
	if(jQuery("#one_per_customer").is(":checked")){
		jQuery("#one_per_customer").prop("disabled", false);
	} 
	
	jQuery("#one_per_customer_per_day").unbind("click");
	jQuery("#one_per_customer_per_day").attr("onclick","return false");
	jQuery("#one_per_customer_per_day").attr("onchange","return false");
	
	if(jQuery("#one_per_customer_per_day").is(":checked")){
		jQuery("#one_per_customer_per_day").prop("disabled", false);
	} 
	
	jQuery("#multiple_use").unbind("click");
	jQuery("#multiple_use").attr("onclick","return false");
	jQuery("#multiple_use").attr("onchange","return false");
	if(jQuery("#multiple_use").is(":checked")){ 
		jQuery("#multiple_use").prop("disabled", false);
	}
	
	jQuery("#new_customer_only").unbind("click");
	jQuery("#new_customer_only").attr("onclick","return false");
	jQuery("#new_customer_only").attr("onchange","return false");
	//jQuery("#new_customer_only").attr("disabled","disabled");
	
	jQuery("#groupid").unbind("click");
	jQuery("#groupid").attr("onclick","return false");
	jQuery("#groupid").attr("disabled","disabled");
	jQuery("#walkinid").attr("disabled","disabled");
	
	if(jQuery("#groupid").is(":checked")){
	 jQuery("#groupid").prop("disabled", false);
	}
	jQuery("#groupid").attr("onchange","return false");
	
	jQuery("#walkinid").unbind("click");
	jQuery("#walkinid").attr("onclick","return false");
	if(jQuery("#walkinid").is(":checked")){  
	 jQuery("#walkinid").prop("disabled", false); 
	}
	jQuery("#walkinid").attr("onchange","return false");
	
	//02-09-2013
	
	//11-09-2013
	
	jQuery("#title").attr("disabled","true"); 
	//jQuery("#terms_condition").attr("readonly", "readonly"); 	

	
	//11-09-2013
	
    }
	function validation(filed,message)
 	{
		
		var f="";
		if(jQuery(filed).val()=="")
		{
			 alert_msg += message;
			 f = "false";
		}
		else
		{
			f="true";
		}
		return f;
	}
	jQuery("#btnEditCampaigns").click(function(){
		//alert("vclcik");
		
		//open_popup("Notificationloader");
		
		
		var alert_msg="<?php  echo  $merchant_msg["index"]["validating_data"]; ?>";
		var head_msg="<div class='head_msg'>Message</div>";
		var content_msg="<div class='content_msg validatingdata' style='background:white;'>"+alert_msg+"</div>";
		jQuery("#NotificationloadermainContainer").html(content_msg);
		
		jQuery("#NotificationloaderFrontDivProcessing").css("display","block");
		jQuery("#NotificationloaderBackDiv").css("display","block");
		jQuery("#NotificationloaderPopUpContainer").css("display","block");
		//return false;
		
		
		
		//jQuery(".validating_data").show();
		
		var campaign_id = jQuery("#id").val();
		//alert(campaign_id);
		var campaign_exist="1";
		
		jQuery.ajax({
				type: "POST",
				url: "<?=WEB_PATH?>/merchant/process.php",
				data: "campaign_id=" + campaign_id +"&checkCampaignExistOnLocation=yes",
				async:false,
				success: function(msg) {
					var obj=jQuery.parseJSON(msg);
					//alert(obj.status);
					//alert(obj.message);
					//alert_msg +="<div>* "+ obj.message +"</div>";
					if(obj.status=="false")
					{
						campaign_exist="0";
					}
				}
		});
		
		// 25 12 2014 update referral customer limit left
		
		jQuery.ajax({
			  type: "POST",
			  url: "<?=WEB_PATH?>/merchant/process.php",
			  data: "camp_id="+ campaign_id +"&update_referral_customers_limit_left=yes",
			  async:false,
			  success: function(msg) {
				  var obj=jQuery.parseJSON(msg);
				  
				  if(obj.status=="true")
				  {
					//alert(obj.status);
					//alert(obj.max_no_sharing);
					//alert(obj.no_of_shared);
					
					var referral_customers_limit_left = parseInt(obj.max_no_sharing)-parseInt(obj.no_of_shared);
					
					var old_shared = parseInt(jQuery("#referral_customer_limit_left").attr('no_of_shared'));
					var new_shared = parseInt(obj.no_of_shared);
					if(old_shared != new_shared)
					{
						//alert(referral_customers_limit_left);
						jQuery("#referral_customer_limit_left").text(referral_customers_limit_left);	
					}
				  }
			  }
		});
							
		// 25 12 2014 update referral customer limit left					
		
		//alert(campaign_exist);
		
		if(campaign_exist=="0")
		{
			var alert_msg="<?php  echo  $merchant_msg["edit-compaign"]["Msg_campaign_not_exist"]; ?>";
			var head_msg="<div class='head_msg'>Message</div>"
			var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
			var href="<?php echo WEB_PATH; ?>/merchant/compaigns.php?action=active";
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
			return false;
		}
		
		
		var alert_msg = "";
  var total_no_of_activation_code = 0;
  var flag="true";
  var flag_hdn="true";
  var total_no_of_locations  = 0;
		var msg1="1";
		var msg2="1";
                var msg3="1";
       
		 numbers = /^[0-9]+$/;
		 
		 var currencyReg = /^\$?[0-9]+(\.[0-9][0-9])?$/;
                 //var hastagRef=/^[a-z0-9 ,\-&_]+$/i;
		 var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
		 
		//flag = validation("#title","<div>* Please Enter Campaign Title.</div>");
		var f = validation("#title","<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_campaign_title"]; ?></div>");
        if(f == 'false'){
            flag = "false";
             alert_msg +="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_campaign_title"]; ?></div>";
        }
        
        var tag_value=jQuery("#campaign_tag").val();

        var tag_tmp1=0;
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
                        tag_tmp1=1;
                        
                    }
        }

		//flag = validation("#discount","* Enter a Discount Rate.\n");
		//flag = validation("#deal_value","<div>* Please Enter Deal Value.</div>");
		
		 var deal_value=jQuery("#deal_value").val();
                if(jQuery("#deal_value").val()=="")
	{
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
		
	}
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
													 
		//flag = validation("#redeem_rewards","<div>* Please Enter Reedem Points.</div>");
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
		//flag = validation("#referral_rewards","<div>* Please Enter Sharing Points.</div>");
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
		//flag = validation("#max_no_of_sharing","<div>* Please Enter Max Number of Sharing For New Customer Registration.</div>");
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
    var numbers = /^[0-9]+$/;
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
				if(jQuery(".camp_location").length==0)
                {

                    var loc_act_code1 =jQuery('input[name^="num_activation_code_"]').val();
                    var loc_act_code = parseInt(jQuery('input[name^="num_activation_code_"]').val());
                   

                    if(loc_act_code1=="" || loc_act_code1=="NaN")
                    {
                       
                        
						alert_msg += "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code"]; ?></div>";
						alert("hi <?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code"]; ?>");
						flag="false";  
						return false;
                    }
                    else if(! numbers.test(loc_act_code))
                    {
                     //alert("else if");
                        alert_msg += "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code_not_number"]; ?></div>";
                        alert("hi123 <?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code_not_number"]; ?>");
                        flag="false";  
                        return false;
                    }
              
                    
                }
                else
                {
					var dist_list_id_str="";
                    jQuery(".camp_location").each(function(){
                        //alert_msg="";
                        var loc_id = jQuery(this).val();
						
						
						// 16-09-2013
						if(jQuery(this).attr("checked")=="checked")
						{
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
							
							// 24 12 2014 check offer_left when save
							
							jQuery.ajax({
								  type: "POST",
								  url: "<?=WEB_PATH?>/merchant/process.php",
								  data: "loc_id=" + loc_id +"&camp_id="+ campaign_id +"&check_location_offer=yes",
								  async:false,
								  success: function(msg) {
									  var obj=jQuery.parseJSON(msg);
									  //alert(obj.status);
									  //alert(obj.message);
									  //alert_msg +="<div>* "+ obj.message +"</div>";
									  if(obj.status=="true")
									  {
										//alert("offer left = "+obj.offers_left);
										
										var offers_left = parseInt(jQuery('#remaining_'+loc_id).attr('offers_left'));
										
										if(parseInt(obj.offers_left)!=offers_left)
										{
											jQuery("#remaining_"+loc_id).attr('offers_left',obj.offers_left);	
											jQuery("#remaining_"+loc_id).next('.remain_value').text(obj.offers_left);
										}
									  }
								  }
							});
							
							
							// 24 12 2014 check offer_left when save
						
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
									var myurl="<?=WEB_PATH?>/merchant/process.php?"+"dist_list_id=" + dist_list_id +"&check_dist_list_active=yes";
									
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
						
						
						}
						// 16-09-2013
					
					
	
					
					
                        jQuery("#remaining_"+loc_id).next("span").css("color","black");
                        if(jQuery(this).attr("checked")=="checked")
						{
							var loc_id = jQuery(this).val();
							//jQuery("#remaining_"+loc_id).css("color","black");
							jQuery("#remaining_"+loc_id).next("span").css("color","black");
							if(jQuery("#num_activation_code_"+loc_id).val()=="" )
							{
								if(msg1=="1")
								{
									alert_msg += "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code"]; ?></div>";
															//alert(alert_msg);
									msg1="0";
								}
								flag="false";
								//jQuery("#remaining_"+loc_id).css("color","red");
								jQuery("#remaining_"+loc_id).next("span").css("color","red");
								return;
								
							}
											else if(! jQuery("#num_activation_code_"+loc_id).val().match(numbers) || jQuery("#num_activation_code_"+loc_id).val()==0 )
											{
								if(msg3=="1")
								{
									alert_msg += "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_activation_code"]; ?></div>";
									msg3="0";
								}
								flag="false";
								//jQuery("#remaining_"+loc_id).css("color","red");
								jQuery("#remaining_"+loc_id).next("span").css("color","red");
								return;
								
							}
											//alert(parseInt(jQuery("#remaining_"+loc_id).attr("remaining_val")));
											//alert(parseInt(jQuery("#num_activation_code_"+loc_id).val()));
							if(parseInt(jQuery("#num_activation_code_"+loc_id).val())<parseInt(jQuery("#remaining_"+loc_id).attr("remaining_val")))
							{
								if(msg2=="1")
								{
									alert_msg += "<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_activationcode_greaterthan_reserved"]; ?></div>";
									msg2="0";
								}
								flag = "false";
								//jQuery("#remaining_"+loc_id).css("color","red");
								jQuery("#remaining_"+loc_id).next("span").css("color","red");
								return;
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
					if(dist_list_id_str != "")
					{
						dist_list_id_str=dist_list_id_str.substr(0,dist_list_id_str.length-1);
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
					
                }
	total_redeem_coupons =0;
	if(jQuery("#redeem_rewards").val()!="" && jQuery("#redeem_rewards").val()!="0" && jQuery("#referral_rewards")!="" && jQuery("#referral_rewards")!="0" && jQuery("#max_no_of_sharing").val()!="" )
	{
            //alert(jQuery(".camp_location").length);
                if(jQuery(".camp_location").length==0)
                {
                    
                    var loc_act_code = jQuery('input[name^="num_activation_code_"]').val();
                    //alert(loc_act_code);
                    total_no_of_activation_code = total_no_of_activation_code + parseInt(loc_act_code);
                }
                else
                {
               
                    jQuery(".camp_location").each(function() {
                            var loc_id = jQuery(this).val();
                            if(jQuery(this).attr("checked")=="checked"){
                                    total_no_of_activation_code = total_no_of_activation_code + parseInt(jQuery("#num_activation_code_"+loc_id).val());
                            }
							total_redeem_coupons = total_redeem_coupons + parseInt(jQuery("#remaining_"+loc_id).attr("total_redeem"));
                    });
					total_no_of_activation_code = total_no_of_activation_code - total_redeem_coupons;
                }
				
				if(jQuery("#hdn_transcation_fees").val() != 0)
				{
					var total1 =  (parseInt(jQuery("#redeem_rewards").val()) * total_no_of_activation_code) + ( total_no_of_activation_code * jQuery("#hdn_transcation_fees").val());
			  }
			  else
			  {
				var total1 =  parseInt(jQuery("#redeem_rewards").val()) * total_no_of_activation_code;
			  }
			  //alert(parseInt(jQuery("#redeem_rewards").val()+"=="+ total_no_of_activation_code + "=="+jQuery("#hdn_transcation_fees").val());
			  //alert(total1);
			  //alert(total1 + total2);
			  //return false;
                //var total1 =  parseInt(jQuery("#redeem_rewards").val()) * total_no_of_activation_code;
		var total2 =  parseInt(jQuery("#referral_rewards").val()) * parseInt(jQuery("#max_no_of_sharing").val());
		var block_point = total1 + total2;
		//alert(block_point);
		//	return false;
                /*
                alert(parseInt(jQuery("#redeem_rewards").val()));
                alert(total_no_of_activation_code);
                alert(total1);
                alert(parseInt(jQuery("#referral_rewards").val()));
                alert(parseInt(jQuery("#max_no_of_sharing").val()));
                alert(total2);
                alert(block_point);
                */
		var purchase_point = parseInt(jQuery(".available_point_span").html());
		var actual_valid_amount = purchase_point +  parseInt(jQuery("#block_required_point").val());
		if(actual_valid_amount < block_point)
		{
			flag="false";
			//alert_msg+="* You Can Not Create Campaign As A Required Point Is More Than Available Point.\n  Your Required Point Is :  " + block_point + "\n";
                        alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_not_activate_campaign"]; ?></div>";
		}
		else
		{
			//flag="true";
			var available_point = 0;
			jQuery("#required_point").val(block_point);
			//var remain = purchase_point - block_point;
			//jQuery("#remaining_point").val(remain);
			var block_required_point = parseInt(jQuery("#block_required_point").val());
			if(block_required_point > block_point)
			{
				available_point_plus = block_required_point - block_point;
				var final = purchase_point+available_point_plus;
				jQuery("#remaining_point").val(final);
				//return false;
			}
			else
			{
				available_point_minus = block_point-block_required_point;  
				//alert(available_point_minus);
				var final = purchase_point-available_point_minus;
				jQuery("#remaining_point").val(final);
				//return false;
			}
		}
	}
	//alert(flag);
	var max_no_sharing="<?php if(isset($get_billing_pack_data->fields['max_no_sharing'])) echo $get_billing_pack_data->fields['max_no_sharing']; ?>";
	
	
	
	
	var sharing_point_text=jQuery("#referral_rewards").val();
	
	var min_share_point="<?php echo $get_billing_pack_data->fields['min_share_point'] ?>";
	
        var referral_rewards="0";
	
	
        if(min_share_point == "0")
        {
	        jQuery("#max_no_of_sharing").val(referral_rewards);
	
        }
	if(jQuery("#max_no_of_sharing").val()<0)
	{
		flag="false";
		alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_enter_max_sharing_customers_not_negative"]; ?></div>";
	}
        var max_no_sharing="<?php echo $RS->fields['max_no_sharing']; ?>";
 
		
		/*
		if(max_no_sharing > jQuery("#max_no_of_sharing").val())
        {

            flag="false";
            alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_referral_customer_sharing_point_limitation"]; ?></div>";
        }
		*/
				
		// 29 12 2014 
		
		var max_no_of_sharing = jQuery("#max_no_of_sharing").val();
		var no_of_shared = jQuery("#referral_customer_limit_left").attr('no_of_shared');
		
		//alert("max_no_sharing "+ max_no_of_sharing +"< no_of_shared"+ no_of_shared);
        	
		if(max_no_of_sharing<no_of_shared)
		{
			flag="false";
            alert_msg+="<div><?php  echo  $merchant_msg["edit-compaign"]["Msg_referral_customer_sharing_point_limitation"]; ?></div>";
		}

        // 29 12 2014 
        
	
	
	var radio_val1=jQuery("#one_per_customer");
	var radio_val2=jQuery("#one_per_customer_per_day");
	var radio_val3=jQuery("#multiple_use");
	var new_customer_only=jQuery("#new_customer_only");
	
	/*
	
	alert(radio_val1.prop('checked') == true);
	alert(radio_val2.prop('checked') == true);
	alert(radio_val3.prop('checked') == true);
	alert(new_customer_only.prop('checked') == true);
	
	return false;
	
	*/
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
	if(jQuery("#title").val()=="" || jQuery("#redeem_rewards").val()=="" || jQuery("#referral_rewards").val()=="" ||  jQuery("#max_no_of_sharing").val()=="" || tag_tmp1==1)
	{
		flag="false";
		
	}
	if(flag=="true")
	{
		//return true;
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

                                       flaglogin=1;

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
                                    
                                }
                                else
                                {
                                   flaglogin=0;
                                }
                                
                                
                           }
                           
                         });
                       
                         if(flaglogin == 1)
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
		//alert(alert_msg);
		close_popup("Notificationloader");
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
                        <!--// 369-->
					</td>
				  </tr>
				</table>
	
		<div class="clear">&nbsp;</div><!--end of content--></div>

<!--end of contentContainer--></div>

<!--start footer--><div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>
		<!--end of footer--></div>
	
</div>
<!-- start of image upload popup div PAY-508-28033  -->
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
										/*$query = "select * from merchant_media where image_type='campaign' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'].") order by id desc" ;
										$RSImages = $objDB->execute_query($query);*/
										$RSImages = $objDB->Conn->Execute("select * from merchant_media where image_type='campaign' and (merchant_id=? or merchant_id=?) order by id desc",array($_SESSION['merchant_id'],$merchant_info->fields['merchant_parent']));

										 if($RSImages->RecordCount()>0){
										while($Row = $RSImages->FetchRow()){
										?>
									
										
										<li class="li_image_list" id="li_img_<?=$Row['id'];?>">
											<div>
												<img src="<?php echo ASSETS_IMG .'/m/campaign/'.$Row['image'];  ?>" height="50px" width="50px" />
												<span style="vertical-align: top" id="span_img_text_<?=$Row['id'];?>"><?=$Row['image']?></span>
												<span style="vertical-align: top;float: right"> Use this image&nbsp;<input type="radio" class='useradioclass' id='useradioid_<?=$Row['id']?>' name="use_image" value="<?=$Row['id']?>" /></span>
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
 <!-- end of popup div  PAY-508-28033 -->
<script>

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
/* start of script for PAY-508-28033*/
function save_from_library()
{
        $av = jQuery.noConflict();
	 var sel_val = $av('input[name=use_image]:checked').val();         
        
	 $av("#hdn_image_id").val(sel_val);
         
	 var sel_src = $av(".fancybox-inner #li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
         
         
	
	$av("#hdn_image_path").val(sel_src);
	/* NPE-252-19046 */
	
	/* NPE-252-19046 */
	file_path = "";
	//close_popup('Notification');
        jQuery.fancybox.close();
        
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ sel_src +"' class='displayimg'>";
        
        
	$av('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
}
function rm_image()
{
	$rm = jQuery.noConflict();
	$rm("#hdn_image_path").val("");
	$rm("#hdn_image_id").val("");
	$rm('#files').html("");
	
}
function rm_image_permanent(id)
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
	$as('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' id='"+file_path+"' class='cancel_remove_image' onclick='rm_image_permanent(this.id)' /></div></div></div>");
}
$atab = jQuery.noConflict();
/* NPE-252-19046 */
$atab(document).ready(function(){
	//alert($atab("#hdn_image_path").val());
	
	
	if($atab("#hdn_image_path").val()  != "")
	{
		var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ $atab("#hdn_image_path").val() +"' class='displayimg'>";
		
		if ($atab("#hdn_image_path").val().startsWith("media"))
		{
			$atab('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
			//alert("media");
		}
		else
		{
			$atab('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png'  class='cancel_remove_image' onclick='rm_image_permanent(this.id)' /></div></div></div>");
			//alert("other");
		}	
	}
	var original_description = $atab("#description").text();
	var original_category = $atab("#category_id").val();
	var original_image = $atab("#hdn_image_path").val();
	
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
				data: "template_id=" +t_val,
				success: function(msg) {
				//alert($atab('#template_id').val());
				if(t_val == "0")
				{
					atab("#deal_detail_description").text("");
                                         tinyMCE.activeEditor.setContent(original_description);
					
					 var sel_val = $atab('input[name=use_image]:checked').val();   
					var sel_src = $atab("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
					//alert(sel_src );
					$atab("#category_id").val(original_category);
					if(sel_src == "")
					{
						$atab("#hdn_image_path").val(sel_src);
					}else{
						
						$atab("#hdn_image_path").val(original_image);
					}
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
	$atab('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
					 tinyMCE.activeEditor.setContent(obj['fields'].description);
                                         $atab("#deal_detail_description").text(obj['fields'].print_coupon_description);
										 //$atab("#discount").val(obj['fields'].discount);
//                                        $atab("#description").text(obj['fields'].description);
                                       
                                        $atab("#title").val(obj['fields'].title);
					$atab("#category_id").val(obj['fields'].category_id);
					$atab("#hdn_image_path").val(obj['fields'].business_logo);
					$atab("#img_div").css("display","block");
				}
				}
			});

	});
	jQuery('input:radio[name="number_of_use"]').change(function(){
	
	   //alert(jQuery(this).parent().parent().next().html());
	   //alert(jQuery(this).val());
	   if(jQuery(this).val()=="2" || jQuery(this).val()=="3")
	   {
			jQuery("#new_customer_only").prop("checked",false);
			jQuery(this).parent().parent().next().css('display','none');
	   }
	   else
	   {
			jQuery(this).parent().parent().next().css('display','table-row');
	   }
	});
	//jQuery('input:radio[name="number_of_use"]').trigger("change");
	
	if(jQuery('input:radio[id="one_per_customer"]').is(':checked'))
	{
		jQuery('input:radio[name="number_of_use"]').parent().parent().next().css('display','table-row');
	}
	else
	{
		jQuery('input:radio[name="number_of_use"]').parent().parent().next().css('display','none');	
	}
	
	/*
	jQuery('input:radio[name="number_of_use"]').each(function(){
		if (jQuery(this).is(':checked')) 
		{
		
		}           
    });
	*/
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
				//alert(response);
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
/* NPE-252-19046 */
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
//jQuery.noConflict();
</script>
<?
$_SESSION['msg'] = "";
?>
</body>
</html>
<script language="javascript">

// 369
/*$abc = jQuery.noConflict();
$abc(document).ready(function() { 
    // bind form using ajaxForm 
  $abc('#edit_campaign_form').ajaxForm({ 
        dataType:  'json', 
        success:   processEditCampJson 
    });
  
  function processEditCampJson(data) { 
	if(data.status == "true"){

		window.location.href='<?=WEB_PATH.'/merchant/compaigns.php'?>';
	}
	else
	{
		alert(data.message);
	}
	
}
	
});*/

// 369
$abc = jQuery.noConflict();





$abc(".camp_location").click(function(){
    
    if($abc(this).attr("onclick")!="return false" )
	{
            
      //  alert("hi");
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
                                     //     alert("in success");
											
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
  }
});
$abc(".chk_private").click(function(){
	var check_active="<?php echo $check_active;?>";
    if(check_active=="true")
    {
		return false;
	}
	else
	{
	
		if($abc(this).is(':checked'))
			{
			   
				$abc("input[type=checkbox][private_group]").each(function(){
					$abc(this).attr("checked","checked") ;
					$abc(this).attr("disabled", false) ;
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
			}
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

	// 26-09-2013 check location for campaign
	var check_active="";
	var locationidname=$abc(this).parent().parent().attr("id").split("_");
	//alert($abc(this).attr("value"));	
	var loc_id=locationidname[1];
	var camp_id='<?php echo $_REQUEST['id']?>';
	
	jQuery.ajax({
				  type: "POST",
				  url: "<?=WEB_PATH?>/merchant/process.php",
				  //data: "loc_id=" + loc_id +"&camp_id="+ camp_id +"&check_location_exist_for_campaign=yes",
				  data: "dist_list_id=" + $abc(this).attr("value") +"&camp_id="+ camp_id +"&check_location_exist_for_campaign=yes",
				  async:false,
				  success: function(msg) {
					  var obj=jQuery.parseJSON(msg);
					  //alert(obj.status);
					  if(obj.status=="false")
					  {
							check_active="false";
					  }
					  else
					  {
							check_active="true";
					  }
				  }
	});
	
	//var check_active="<?php echo $check_active;?>";
	
	// 26-09-2013 check location for campaign
	
    if(check_active=="true")
    {
		//alert("true");
		if($abc(this).is(':checked'))
		{
			//alert("not checked");
			//return false;
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
			}
			else
			{
				jQuery("#hdn_campaign_type_"+ locationid).val("3");
			}
		}
		else
		{
			//alert("checked");
			return false;
		}
	}
	else
	{
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
		}
		else
		{
			jQuery("#hdn_campaign_type_"+ locationid).val("3");
		}
	}	
});
$abc(".private_group").live("click",function(){

    // 25-09-2013 check location for campaign
	var check_active="";
	var locationidname=$abc(this).parent().parent().attr("id").split("_");
			
	var loc_id=locationidname[1];
	var camp_id='<?php echo $_REQUEST['id']?>';
	
	jQuery.ajax({
				  type: "POST",
				  url: "<?=WEB_PATH?>/merchant/process.php",
				  //data: "loc_id=" + loc_id +"&camp_id="+ camp_id +"&check_location_exist_for_campaign=yes",
				  data: "dist_list_id=" + $abc(this).attr("value") +"&camp_id="+ camp_id +"&check_location_exist_for_campaign=yes",
				  async:false,
				  success: function(msg) {
					  var obj=jQuery.parseJSON(msg);
					  //alert(obj.status);
					  if(obj.status=="false")
					  {
							check_active="false";
					  }
					  else
					  {
							check_active="true";
					  }
				  }
	});
	
	//var check_active="<?php echo $check_active;?>";
	
	// 25-09-2013 check location for campaign
	
    if(check_active=="true")
    {
		//alert("true");
		if($abc(this).is(':checked'))
		{
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
		}
		else
		{
			//alert("checked");
			return false;
		}
		
	}
	else
	{
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
    //var v = 300 - $abc("#deal_detail_description").val().length;
   //$abc(".span_pcd").text(v+" characters remaining"); 
}
changetext1();
//changetext2();
changetextpcd();
function  getcancelnow()
{
	jQuery.fancybox.close();
}
/*
function  getpurchasenow()
{
    var numbers = /^[0-9]+$/;  
    if($abc(".fancybox-inner #txt_price").val().match(numbers))  
   {
       
       jQuery( ".fancybox-inner .purchasepointclass" ).hide();
     $abc.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "txt_price=" + $abc(".fancybox-inner #txt_price").val() +"&btnPurchasePointsnow=yes",
									  success: function(msg) 
                                      {
										var obj = jQuery.parseJSON(msg);		 //alert(msg);
										 //alert($abc(".available_point_span").text());
										 //alert(msg-($abc(".available_point_span").text()));
										 var new_points_purchase=msg-($abc(".available_point_span").text());
                                          $abc(".available_point_span").text(obj.available_points);
                                         $abc(".fancybox-inner .success_msg").text("You Have Successfully Purchased "+ obj.purchased_points +" Points.");
                                      }
        });
       }
        else
            {
                //alert("Please Enter Proper price");
		//var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>";
				var content_msg="<div class='table_errore_message'><?php echo $merchant_msg['edit-compaign']['please_enter_correct_amount'];?></div>";
		//		var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				
				jQuery( ".fancybox-inner .purchasepointclass" ).html(content_msg);
				jQuery( ".fancybox-inner .purchasepointclass" ).show();
				/*jQuery.fancybox({
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
                $abc(".fancybox-inner #span_con_point").text("");
                $abc(".fancybox-inner .success_msg").text("");
            }
}  */
$abc('.fancybox-inner input#txt_price').live("keyup",function(e) {
  if (e.keyCode == '13') 
  {
	//alert("if");
     //e.preventDefault();
	 //$abc("#popupcancel").focus();
	 jQuery.fancybox.close();
  }
  else
  {
		 var numbers = /^[0-9]+$/;  
		if($abc(".fancybox-inner #txt_price").val() == "")
        {
            jQuery( ".fancybox-inner .purchasepointclass" ).html("");
        }
		else if($abc(".fancybox-inner #txt_price").val().match(numbers))  
	    { 
			 $abc.ajax({
					  type: "POST",
					  url: "<?=WEB_PATH?>/merchant/process.php",
					  data: "txt_price=" + $abc(".fancybox-inner #txt_price").val() +"&getpoints=yes",
					  success: function(msg) 
					  {
						  
						  $abc(".fancybox-inner #span_con_point").text(msg);
						  $abc(".fancybox-inner .success_msg").text("");
						  
					  }
				});
		}
        else
        {
             
        }
  }
});
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
                //alert("Enter Proper Price");
		var head_msg="<div class='head_msg'>Message</div>";
		var content_msg="<div class='content_msg'><?php echo $merchant_msg['edit-compaign']['please_enter_correct_amount'];?></div>";
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
                $abc("#span_con_point").text("");
                $abc(".success_msg").text("");
            }
}
/*
$abc(".p_btn").click(function(){
   $abc("#point_block").css("display","block");
    
});
*/
jQuery(".p_btn1").click(function(){
                                    jQuery.fancybox({
                                                content:jQuery('#point_block').html(),

                                                type: 'html',

                                                openSpeed  : 300,

                                                closeSpeed  : 300,
                                                // topRatio: 0,

                                                changeFade : 'fast',  

                                                helpers: {
                                                        overlay: {
														closeClick: false,
                                                        opacity: 0.3
                                                        } // overlay
                                                }
                                        }); 
});
</script>
<!-- start datepicker -->
<!--<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.js"></script>-->

<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.ui.core.js"></script>

<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.ui.datepicker.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.ui.datepicker.css">
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.ui.theme.css">
<!--- tooltip css --->

<!--- tooltip css --->

<!-- end datepicker -->
<script type="text/javascript">
jQuery("a#compaigns").css("background-color","orange");
jQuery("#expiration_date").datetimepicker({onSelect: function(){
      //  jQuery('#expiration_date').datepicker('option', "dateFormat", "yy-mm-dd" );
}});
jQuery("#start_date").datetimepicker({ onSelect: function(){
        var date = jQuery("#start_date").datepicker( 'getDate' );
        
         var actualDate = new Date(date);
         
         var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+30);
	jQuery('#expiration_date').datepicker('option', {
            minDate: jQuery(this).datepicker( 'getDate' ),
            //maxDate : newDate
            });
 //jQuery('#start_date').datepicker('option', "dateFormat", "yy-mm-dd" );
		
	//	$( "#end_date" ).datetimepicker({minDate: $(this).datepicker( 'getDate' )});
}});

jQuery("#walkinid").click(function(){
        if(jQuery('#walkinid').is(':checked'))
	{
	    jQuery("#one_per_customer_per_day").attr("disabled", "disabled");
	    jQuery("#multiple_use").attr("disabled", "disabled");
	}
	
});
jQuery("#groupid").click(function(){
var check_active="<?php echo $check_active;?>";
    if(check_active=="true")
    {
	}
	else
	{
        if(jQuery('#groupid').is(':checked'))
		{
			jQuery("#one_per_customer_per_day").removeAttr("disabled");
			jQuery("#multiple_use").removeAttr("disabled");
		}
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
</script>
<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
	//jQuery('.list_carousel').css("height","145px");
	
	if(jQuery('#foo2').length)
	{
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
    }        
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

jQuery(".clsnum_activation_code").keyup(function(){
	var l_id = jQuery(this).attr('id').split('num_activation_code_');
	l_id = l_id[1];
	var new_val = parseInt(jQuery(this).val());
	var old_val = parseInt(jQuery('#remaining_'+l_id).attr('total_offers'));
	var offers_left = parseInt(jQuery('#remaining_'+l_id).attr('offers_left'));
	
	// new card = 2 , left = 4 - ( 5-2) ....1
	// new card = 1 , left = 4 - ( 5-1) ....0
	// if left < 0 ...disable .....save buttoon
 
	
	var new_offers_left = offers_left - (old_val- new_val);
	
	//alert(l_id+" "+old_val+" "+new_val+" "+offers_left+" "+new_offers_left);
	
	//alert(new_offers_left);
	
	if(isNaN(new_offers_left))
	{
	}
	else
	{
		jQuery('#remaining_'+l_id).next('.remain_value').text(new_offers_left);
		
		if(new_offers_left<0)
		{
			jQuery("#btnEditCampaigns").addClass("disabledmedia");
			jQuery("#btnEditCampaigns").attr("disabled", true);
			jQuery("#remaining_"+l_id).next("span").css("color","red");
		}
		else
		{
			jQuery("#btnEditCampaigns").removeClass("disabledmedia");
			jQuery("#btnEditCampaigns").attr("disabled", false);
			jQuery("#remaining_"+l_id).next("span").css("color","black");
		}
	}
});

jQuery("#max_no_of_sharing").keyup(function(){
	
	var max_no_of_sharing_old = jQuery(this).attr('customer_limit');
	var no_of_shared = jQuery("#referral_customer_limit_left").attr('no_of_shared');
	
	var max_no_of_sharing_new = jQuery(this).val();
	
	var referral_customer_limit_left_new = parseInt(max_no_of_sharing_new) - parseInt(no_of_shared);
	
	if(isNaN(referral_customer_limit_left_new))
	{
	}
	else
	{
		
		if(referral_customer_limit_left_new<0)
		{
			jQuery("#btnEditCampaigns").addClass("disabledmedia");
			jQuery("#btnEditCampaigns").attr("disabled", true);
			jQuery("#referral_customer_limit_left").css("color","red");
		}
		else
		{
			jQuery("#btnEditCampaigns").removeClass("disabledmedia");
			jQuery("#btnEditCampaigns").attr("disabled", false);
			jQuery("#referral_customer_limit_left").css("color","#747474");
		}
		jQuery("#referral_customer_limit_left").text(referral_customer_limit_left_new);
	}
	
});

  </script>
 
<div class="validating_data" style="display:none;">Validating data, please wait...</div> 
