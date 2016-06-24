<?php
/**
 * @uses merchant marketing material template
 * @used in pages : manage-marketing-material.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where = array();
if($_SESSION['merchant_id'] != "")
{
	$array_where_user['id'] = $_SESSION['merchant_id'];
	$RS_User = $objDB->Show("merchant_user", $array_where_user);
	$m_parent = $RS_User->fields['merchant_parent'];
}

if(isset($_REQUEST['btn_downloadcancel']))
{
    header("Location:manage-marketing-material.php?tab=loyalty");
    exit;
}
if(isset($_REQUEST['btn_downloadcancel_loc']))
{
    header("Location:manage-marketing-material.php?tab=location");
    exit;
}
if($_SESSION['merchant_id'] != "")
{
	if($_REQUEST['id'] == 0)
	{	 
	   //$Sql = "SELECT * FROM locations l WHERE  l.active=1 and l.created_by=".$_SESSION['merchant_id'];
	   $records_array = $objDB->Conn->Execute("SELECT * FROM locations l WHERE  l.active=1 and l.created_by=?",array($_SESSION['merchant_id']));
	}
	else
	{	
		if(isset($_REQUEST['activationcode']) && isset($_REQUEST['qrcodeid'])) 
		{
			// campaign marketing material
			
			//$Sql = "SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM campaign_location cl WHERE cl.campaign_id = ".$_REQUEST['id']." ) and l.active=1 "; 
			
			//echo "SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM loyaltycard_location cl WHERE cl.loyalty_card_id = ".$_REQUEST['id'].") and l.active=1";
			//exit();
			$records_array = $objDB->Conn->Execute("SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM loyaltycard_location cl WHERE cl.loyalty_card_id = ?) and l.active=1",array($_REQUEST['id']));
		}
		else 
		{	
			// location marketing material
			
			//$Sql = "SELECT * FROM locations l WHERE l.active=1 and l.id  = ".$_REQUEST['id']; 
			$records_array = $objDB->Conn->Execute("SELECT * FROM locations l WHERE l.active=1 and l.id  = ?",array($_REQUEST['id']));
		}
	}
    //$records_array =  $objDB->execute_query($Sql);
}
   
    //echo $records_array['id'];

    
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Marketing Material</title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
</head>

<body>
     <!-- martchant message box content -->
     <div id="dialog-message" title="Message Box" style="display:none">
     </div>
     <!-- End martchant message box content -->
<div >
     <!--<script src="https://code.jquery.com/jquery-1.4.1.min.js"></script>-->
     <!-- load from CDN-->
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->

<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.tooltip.css" />
<script src="<?=ASSETS_JS ?>/m/jquery.tooltip.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
<!--<script type="text/javascript" language="javascript" src="<?php //echo ASSETS_JS ?>/m/jquery.dataTables.js"></script> -->
<script type="text/javascript" src="<?=ASSETS ?>/tinymce/tiny_mce.js"></script>
<script language="javascript" src="<?=ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
	<style type="text/css" title="currentStyle">
        
			
	#files{width: 100%;float: left;margin-top:10px !important;}		
	</style>
                <script language="javascript">

				
/*$(function()
{		
	$('#start_date').datepick({dateFormat: 'mm-dd-yyyy'});
	$('#expiration_date').datepick({dateFormat: 'mm-dd-yyyy'});
});*/
 //   window.onbeforeunload = function() {};
 jQuery(document).ready(function(){
	window.tinymce.dom.Event.domLoaded = true;
	tinyMCE.init({
		// General options
		//mode : "textareas",
                mode : "exact",
		elements:"txt_material",
		theme : "advanced",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,,forecolor,backcolor,styleselect,formatselect,fontselect,fontsizeselect",
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
                autosave_ask_before_unload: false,
		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'mkt_mtr_location_table'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
                
	});
jQuery(".textarea_loader").css("display","none");
jQuery(".textarea_container").css("display","block");

                } );
</script>
               
<!---start header---->
	<div>
		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	<div id="content">
		<div class="title_header"><?php echo $merchant_msg['marketingmaterial']['marketing_material'];?></div>
                <div id="backdashboard">
					
					
						<a id="dashboard" href="<?=WEB_PATH?>/merchant/manage-marketing-material.php?tab=loyalty"><img src="<?=ASSETS_IMG ?>/m/back_but.png" /></a>
					
                </div>

<div align="center" >
    <form class="marketing_material_form">
           <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
           <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="" />
           <input type="hidden" name="hdn_is_campaign" id="hdn_is_campaign" value="<?php if($_REQUEST['id'] ==0){ echo 0;}else { echo 1;}?>" />
        <input type="hidden" name="hdn_campaignid" id="hdn_campaignid" value="<?=$_REQUEST['id']?>" />
       
	<table width="100%"  border="0" cellspacing="2" cellpadding="2">
			   <tr >
				<td width="20%" align="right"><?php echo $merchant_msg['marketingmaterial']['field_select_document_size'];?></td>
				<td width="80%" align="left">
					<select name="document_size" id="document_size" >
						<option value="0">Select Document Size</option>
						<?php
							$array_document_size = array();
							$Rs_size = $objDB->Show("marketingmaterial_size");
							while($Row_size = $Rs_size->FetchRow())
							{ ?>
						<option value="<?php echo $Row_size['id'] ?>"><?php echo $Row_size['size_name']."( full bleed size- ".$Row_size['full_bleed_width_mm']."X".$Row_size['full_bleed_height_mm']." mm )" ?> </option>
							<?php }
						?>
						
					</select>
				</td>
			  </tr>
			  <tr>
					<td align="right" style="padding-top: 10px;vertical-align: top;"><?php echo $merchant_msg['marketingmaterial']['image'];?></td>
					<td align="left">
						<div class="cls_left">
									<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
									<div id="upload" title='Attach image for the campaign' >
									<span  >Browse
									</span> 
									</div>
									</div>  <div class="browse_right_content" > &nbsp;&nbsp;<span >Or select from </span><a class="mediaclass"  > media library </a></div> 
									
									<ul id="files" >
            
									</ul>
			 
					</td>
			   </tr>
			   <!--
			   <tr>
					<td align="right">&nbsp;</td>
					<td align="left">
						<ul id="files" >
            
						</ul>
					</td>
			   </tr>
			   -->
			   
			 <?php
			if(isset($_REQUEST['activationcode']) && isset($_REQUEST['qrcodeid'])) 
			{
			?> 
			   <tr>
					<td align="right" class="vertical_top"><?php echo $merchant_msg['marketingmaterial']['field_select_location'];?></td>
					<td align="left" > 
					<div align="center">
				<img   src="<?php echo ASSETS_IMG .'/32.GIF'; ?>" class="table_loader defaul_table_loader" />
				</div>
				
				<div class="datatable_container" style="display: none;">
					
					<table border="0" cellspacing="1" cellpadding="10" class="tableMerchant"  id="mkt_mtr_location_table">
					<thead>
				  
				  <tr>
					<th align="right" class="tableDealTh table_th_2">&nbsp;</th>
					<th align="left" class="tableDealTh table_th_65"> 
								 <?php echo $merchant_msg['marketingmaterial']['field_location_address'];?></th>
							<?php
							if(isset($_REQUEST['id']))
							{	
								/*
								if($_REQUEST['id']==0) 
								{
								?>
								  <th align="left" class="tableDealTh table_th_25"> 
									<?php echo $merchant_msg['marketingmaterial']['qr_code_status'];?>
								  </th>
								<?php 
								}
								*/
						   } ?>
				</tr>
				 
				  </thead>
					<tbody>
					   <?
						if($records_array->RecordCount()>0)
						{
							while($Row = $records_array->FetchRow())
							{
								$css ="";
								if($Row['active'] != 1)
								{
									$css="background-color:#E1E1E1";
								}
								/*
								$wh_arr = array();
								$wh_arr['location_id'] = $Row['id'];
								$rs_qrcode_campaign = $objDB->Show("qrcode_location",$wh_arr) ;
								if($rs_qrcode_campaign->RecordCount() == 0)
								{
									$printclass = "pcoupon";
									$downloadclass = "downloadqrcode";
									$linktext = "Link";
								}
								else
								{
									$wh_arr1 = array();
									$wh_arr1['id'] =$rs_qrcode_campaign->fields['qrcode_id'];
									$rs_qrcode = $objDB->Show("qrcodes",$wh_arr1) ;
									$qrcodestring = $rs_qrcode->fields['qrcode'];
									$printclass = "rcoupon";
									$downloadclass = " rdownloadqrcode";
									$linktext = "Un-link";
								}
								*/
                        ?>
						<tr class="tableDeal" style="<?=$css?>">
							<td>
								<input  <?php if($_REQUEST['id'] != 0 ) echo "checked"; ?> type="radio"  visit="first" id="rd_campaigntitle_<?php echo $Row['id']; ?>"name="rd_locationtitle" class="locationtitle" value="<?php echo $Row['id']; ?>" />
							</td>
							<td class="capitalize_heading">
								<a href="javascript:void(0)" id="showCamp_<?=$Row['id']?>">
									<?php
									$array_where = array();
									$array_where['id'] = $Row['state'];
									$RS_state = $objDB->Show("state", $array_where);

									$array_where = array();
									$array_where['id'] = $Row['city'];
									$RS_city = $objDB->Show("city", $array_where);
									?>
									<?=$Row['address'].", ".$RS_city->fields['name'].", ".$RS_state->fields['short_form'].", ".$Row['zip']?>
								</a>
							</td>
							<?php 
							/*
							if(isset($_REQUEST['id']))
							{    
								if($_REQUEST['id'] == 0 )
								{
								?>
									<td>
										<a href="javascript:void(0)" locid="<?=$Row['id']?>" qval="<?php echo $qrcodestring; ?>" class="<?php echo $downloadclass ?>" ><?php echo  $linktext; ?></a>
									</td>
								<?php 
								} 
							}
							*/ 
							?>
						</tr>
					  <?
						}
					}
					else
					{
					}
				  ?>
				  </tbody>
				</table>
				</div>
							
					</td>
			   </tr>
			<?php
			}
			else
			{
			?>
				<tr>
					<td align="right" class="vertical_top">Selected Location :</td>
					<td align="left" >
						<span style="display: block;">
						<?php
							if($records_array->RecordCount()>0)
							{
								while($Row = $records_array->FetchRow())
								{
									$array_where = array();
									$array_where['id'] = $Row['state'];
									$RS_state = $objDB->Show("state", $array_where);

									$array_where = array();
									$array_where['id'] = $Row['city'];
									$RS_city = $objDB->Show("city", $array_where);
									echo $Row['address'].", ".$RS_city->fields['name'].", ".$RS_state->fields['short_form'].", ".$Row['zip'];
								}
							}
						?>
						</span>
					</td>
			   </tr>
			<?php	
			}
		 ?>		  
			   <tr>
				<td align="right" class="vertical_top"><?php echo $merchant_msg['marketingmaterial']['field_text_editor'];?>				</td>
				<td align="left" class="textare_td vertical_top">
					<div align="center" class="textarea_loader">
					<img   src="<?php echo ASSETS_IMG .'/32.GIF'; ?>" class="defaul_table_loader" />
					</div>
					<div class="textarea_container" style="display: none;">	
					 <textarea name="txt_material" id="txt_material"></textarea>
					 </div>
        		</td>
			   </tr>
			   <tr>
				<td align="right">&nbsp;</td>
				<td align="left">
					 <div align="center">
				<input type="button" name="btn_preview" id="btn_preview" value="<?php echo $merchant_msg['marketingmaterial']['btn_preview'];?>" />&nbsp;&nbsp;
				<input type="button" name="btn_download" id="btn_download" value="<?php echo $merchant_msg['marketingmaterial']['btn_download'];?>" />&nbsp;&nbsp;
				<?php
				if(isset($_REQUEST['activationcode']) && isset($_REQUEST['qrcodeid'])) 
				{
				?>
					<input type="submit" name="btn_downloadcancel" id="btn_downloadcancel" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" />&nbsp;&nbsp;
				<?php
				}
				else
				{
				?>
					<input type="submit" name="btn_downloadcancel_loc" id="btn_downloadcancel_loc" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" />&nbsp;&nbsp;	
				<?php
				}
				?>
        </div>
        		</td>
			   </tr>
        </table>
	
	   
	<!--<div class = "information_text_div"> !
			<!--<img src="images/Information.png" alt="" /> -->
		<!--	<span id="msg_div_image_size" style="font-size: 13px;font-weight: bold;margin-left:10px;position: relative;top:-4px;"></span>
		
	</div> -->
      
</div>
                               
	
<!--end of content--></div>
<!--end of contentContainer--></div>
</form>
<!---------start footer--------------->
       <div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>
        
  </div>

 
        <input  type="hidden" value="" name="hdn_qrcodedn_location_id" id="hdn_qrcodedn_location_id" />
        <input  type="hidden" value="" name="hdn_qrcode" id="hdn_qrcode" />
        <input type="hidden" value="" name="hdn_dettach_qrcode_location_id" id="hdn_dettach_qrcode_location_id" />
<input  type="hidden" value="" name="hdn_id_download_or_print" id="hdn_id_download_or_print" />
 
<div style="display:none">
<div class="QRcode_detail_div" style="">
</div>
<div class="div_preview1" style="overflow:auto" > 
	<div class="div_preview"   >
	   <h1>dfsdhj jhf fhdsfsd</h1>
		<div>fkf fs</div>
		fdjfkjfs
		sddf
		
		<a>dfds</a>
	</div> 
</div> 
</div>
<?php
	echo file_get_contents(WEB_PATH.'/merchant/import_media_library.php?mer_id='.$_SESSION['merchant_id'].'&img_type=campaign&start_index=0');
?>


		<!--end of footer--></div>
<?

$_SESSION['msg'] = "";
?>
</body>
</html>
	<script type="text/javascript" charset="utf-8">

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
            jQuery(document).ready(function(){
               open_popup("preview") ;
            });
function rm_image()
{
	
	jQuery("#hdn_image_path").val("");
	jQuery("#hdn_image_id").val("");
	jQuery('#files').html("");
	
}
			
		</script>
<?php
$confirmationstring  ='';
$confirmationstring  .='<div>';
         $confirmationstring  .='<div align="center" style="margin-bottom:10px;">';
             $confirmationstring  .= $merchant_msg['marketingmaterial']['msg_unlink_qr_code'];
         $confirmationstring  .='</div>';
		 $confirmationstring  .='</div>';
         $confirmationstring  .='<div align="center">';
             $confirmationstring  .='<input type="submit" name="btn_no" id="btn_no" value="NO" > &nbsp;&nbsp; ';
             $confirmationstring  .='<input type="submit" name="btn_yes" id="btn_yes" value="Yes" >';
             
         $confirmationstring  .='</div>';
         
     

     $sizeoptions ='';
      $sizeoptions .='<select name="opt_qrcodesize" id="opt_qrcodesize">';
      $sizeoptions .='<option value="80"> 80 X 80 </option>';
      $sizeoptions .='<option value="120"> 120 X 120 </option>';
      $sizeoptions .='<option value="200"> 200 X 200 </option>';
      $sizeoptions .='<option value="275"> 275 X 275 </option>';
      $sizeoptions .='option value="350"> 350 X 350 </option>';
      $sizeoptions .='</select>';
      $sizeoptions .='<br/><br /><br /> ';
      $sizeoptions .='<input type="submit" name="btndownloadqrcode" id="btndownloadqrcode" value="'.$merchant_msg['marketingmaterial']['download_qr_code'].'" >';
      $sizeoptions .='&nbsp;&nbsp;&nbsp;<input type="submit" name="btncancel" id="btncancel" value="'.$merchant_msg['index']['btn_cancel'].'">';
?>
<script>
    jQuery(function(){
		var btnUpload=jQuery('#upload');
		var status=jQuery('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'edit-marketingmaterial.php?doAction=FileUpload&img_type=campaign',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				if(jQuery('#files').children().length > 0)
				{
					jQuery('#files li').detach();
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
                             //  alert(arr[1]);
				if(arr[1]=="small")
                                {
                                    status.text(arr[0]);
                                    alert(arr[0]);
                                }
                                else
                                { 
                                    status.text('');
                                    //Add uploaded file to list
                                    file_path = arr[1];
                                    jQuery("#hdn_image_path").val(file_path);
                                  //  alert(file_path);
                                    var img = "<br><img src='<?=ASSETS_IMG ?>/m/campaign/"+ file_path +"' class='displayimg'>";
                                    jQuery('#files').html(img +"<br/><div display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
                                }
			}
		});
		
	});
      
        function save_from_computer()
{
   /* NPE-252-19046 */
	
	/* NPE-252-19046 */
	//list($width, $height, $type, $attr) = getimagesize("images/logo/".file_path);
     //  alert('<?=ASSETS_IMG ?>/m/logo/'+ file_path);
    //img.src = '<?=ASSETS_IMG ?>/m/logo/'+ file_path ;
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ file_path +"' class='displayimg'>";
	jQuery('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
}
// jQuery("#btnattachqrcode").live("click",function(){
//         //alert("In attchment code");
//           var selectedVal = "";
//var selected = jQuery("input[type='radio'][name='opt_qrcode']:checked");
//
//  var ele1 = "";
//
//        jQuery(".downloadqrcode").each(function(){
//
//            if(jQuery(this).attr("locid") == jQuery("#hdn_qrcode_location_id").val())
//            {
//                ele1 = jQuery(this);
//             }
//
//        });
////alert(jQuery("#hdn_qrcode_campaign_id").val());
//if (selected.length > 0)
//    {
//    selectedValue = selected.val();
//  jQuery.ajax({
//                                      type: "POST",
//                                      url: "<?=WEB_PATH?>/merchant/process.php",
//                                      data: "attachQRcode_location=yes&location_id="+jQuery("#hdn_qrcode_location_id").val()+"&qrcode_id="+selectedValue,
//                                      async : false,
//                                      success: function(msg) {
//                                    
//                                        var obj = jQuery.parseJSON(msg);
//                                      alert("QR Code Is Successfully Linked With location");
//                                         var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
//					var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>* QR Code Is Successfully Linked With Location</div>";
//					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
//					jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
//					jQuery.fancybox({
//						     content:jQuery('#dialog-message').html(),
//						     type: 'html',
//						     openSpeed  : 300,
//						     closeSpeed  : 300,
//						     changeFade : 'fast',  
//						     helpers: {
//							     overlay: {
//							     opacity: 0.3
//							     } // overlay
//						     }
//						 });
//                                            jQuery("#hdn_qrcode").val(obj.qval);
//                                            ele1.removeClass("downloadqrcode");
//                                          ele1.addClass("rdownloadqrcode");
//                                          ele1.text("Unlink");
//                                          jQuery(".rdownloadqrcode").each(function(){
//                                           if(jQuery(this).attr("locid") == jQuery("#hdn_qrcode_location_id").val())
//                                                {
//                                                  ele2 = jQuery(this);
//                                                     }
// 
//                                            });
//                                            ele2.attr("qval",obj.qval);
//                                      jQuery(".rdownloadqrcode").click(function(){
//                                           // open_popup('qrcode');
//                                                jQuery("#hdn_dettach_qrcode_location_id").val(jQuery(this).attr("locid"));
//                                            
//                                                 jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
//                                                  var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Un-link QR Code</div>"
//					var content_msg='<div style="text-align:left;margin-top:10px;padding:5px;"><?php echo $confirmationstring; ?></div>';
//					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
//					jQuery( "#dialog-message" ).html(head_msg + content_msg );//+footer_msg);
//                                                    jQuery.fancybox({
//						     content:jQuery("#dialog-message" ).html(),
//                                        	     type: 'html',
//						     openSpeed  : 300,
//						     closeSpeed  : 300,
//						     changeFade : 'fast',  
//						     helpers: {
//							     overlay: {
//							     opacity: 0.3
//							     } // overlay
//						     }
//						 });
//                                                 bind_confirmation_event(); 
//                                         // bind_event1();
//                                          close();
//                                          //$("#jqReviewHelpfulMessageNotification").html(msg);
//                                        });
//                                    //  close_popup('qrcode');
//                                     jQuery.fancybox.close(); 
//                                 
//                                      }
//        });
//    }else{
//    //alert("Please Select QR Code");
//     var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
//					var content_msg='<div style="text-align:left;margin-top:10px;padding:5px;">* Please Select QR Code</div>';
//					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
//					jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
//					jQuery.fancybox({
//						     content:jQuery('#dialog-message').html(),
//						     type: 'html',
//						     openSpeed  : 300,
//						     closeSpeed  : 300,
//						     changeFade : 'fast',  
//						     helpers: {
//							     overlay: {
//							     opacity: 0.3
//							     } // overlay
//						     }
//						 });
//    }
//});
//}
jQuery("#btnattachqrcode").live("click",function(){
      // alert("In attchment code");
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
                                 window.location.href=obj.link;
                                    
                                }
                                else
                                    {
                                         var selectedVal = "";
           var selectedValue= "";
var selected = jQuery("input[type='radio'][name='opt_qrcode']:checked");

  var ele1 = "";

        jQuery(".downloadqrcode").each(function(){

            if(jQuery(this).attr("locid") == jQuery("#hdn_qrcode_location_id").val())
            {
                ele1 = jQuery(this);
             }

        });
//alert(jQuery("#hdn_qrcode_location_id").val());
 //selectedValue = selected.val();
//alert("attachQRcode_location=yes&location_id="+jQuery("#hdn_qrcode_location_id").val()+"&qrcode_id="+selectedValue);
if (selected.length > 0)
    {
    selectedValue = selected.val();
  jQuery.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "attachQRcode_location=yes&location_id="+jQuery("#hdn_qrcode_location_id").val()+"&qrcode_id="+selectedValue,
                                      async : false,
                                      success: function(msg) {
                                    
                                        var obj = jQuery.parseJSON(msg);
										jQuery.fancybox.close(); 
                                         //alert("QR Code Is Successfully Linked With location");
                                      /*    var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['qr_code_linked_location'] ?></div>";
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
						 }); */
                                            jQuery("#hdn_qrcode").val(obj.qval);
                                            ele1.removeClass("downloadqrcode");
                                          ele1.addClass("rdownloadqrcode");
                                          ele1.text("Unlink");
                                          jQuery(".rdownloadqrcode").each(function(){
                                           if(jQuery(this).attr("locid") == jQuery("#hdn_qrcode_location_id").val())
                                                {
                                                  ele2 = jQuery(this);
                                                     }
 
                                            });
                                            ele2.attr("qval",obj.qval);
                                      jQuery(".rdownloadqrcode").click(function(){
                                           // open_popup('qrcode');
                                                jQuery("#hdn_dettach_qrcode_location_id").val(jQuery(this).attr("locid"));
                                            
                                                 jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                                                 jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                                                   var head_msg="<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['unlink_qr_code'];?></div>"
					var content_msg='<div class="content_msg"><?php echo $confirmationstring; ?></div>';
					var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
					jQuery( "#dialog-message" ).html(head_msg + content_msg );//+footer_msg);
                                                    jQuery.fancybox({
						     content:jQuery("#dialog-message" ).html(),
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
                                                 bind_confirmation_event(); 
                                         // bind_event1();
                                          close();
                                          //$("#jqReviewHelpfulMessageNotification").html(msg);
                                        });
                                      close_popup('qrcode');
                                 
                                      }
        });
    }else{
    //alert("Please Select QR Code");
       close_popup('qrcode');
     var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_select_qr_code'];?></div>";
					var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                           }
         });
          
});
     function bind_event(){
       
}


    function bind_confirmation_event()
    {
         jQuery("#btn_no").live("click",function(){
      // close_popup("qrcode");
      jQuery.fancybox.close();
    });
    jQuery("#btn_yes").live("click",function(){
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
                                 window.location.href=obj.link;
                                    
                                }
                                else
                                    {
                                             var val = jQuery("#hdn_dettach_qrcode_location_id").val();
       
        jQuery(".rdownloadqrcode").each(function(){
            if(jQuery(this).attr("locid") ==  jQuery("#hdn_dettach_qrcode_location_id").val())
            {
                    ele1 = jQuery(this);
             }
        });
         jQuery.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "removeqrcodeattachment_location=yes&location_id="+val,
                                      async : false,
                                      success: function(msg) {
                                     
                                          ele1.removeClass("rdownloadqrcode");
                                          ele1.addClass("downloadqrcode");
                                          ele1.text("Link");
										  jQuery.fancybox.close(); 
                                         // alert("QR Code Is Successfully Un-linked From location");
                                      /*     var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['qr_code_unlinked_location'];?></div>";
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
						 }); */
                                          //download_function();
                                                    jQuery(".downloadqrcode").click(function(){

                                                 // open_popup('qrcode');
                                                  jQuery.ajax({
                                                type: "POST",
                                                url: "<?=WEB_PATH?>/merchant/process.php",
                                                data: "getQRcodelist=yes&location_id="+jQuery(this).attr("locid"),
                                                async : false,
                                                success: function(msg) {
                                                    jQuery(".QRcode_detail_div").html(msg);
                                                     var data_found = jQuery(".QRcode_detail_div #hdn_qrcode_data_found").val();
                                          if(data_found=="false")
										  {
											  var head_msg="<div class='head_msg'>Message</div>"
										  
										  }
										  else
										  {
										  var head_msg="<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['link_qr_code'];?></div>"
										  }
					var content_msg="<div class='content_msg'>"+ msg +"</div>";
					var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
					jQuery( "#dialog-message" ).html(head_msg + content_msg );//+footer_msg);
                                                    jQuery.fancybox({
						     content:jQuery("#dialog-message" ).html(),
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
                                                    bind_event();
                                                    close();
                                                }
                                              });

                                                  return false;

                                               });
                                  //   close_popup('qrcode');
                                  jQuery.fancybox.close();
                                    }
                                    });
                                    }
                           }
                       });
        
    });
    }
   function close()
    {
       
        jQuery("#btncancel").live("click",function(){
        jQuery.fancybox.close(); 
           // close_popup('qrcode');
        return false;
        });
        
    }
    function download_function()
    {
     
      jQuery("#btndownloadqrcode").live("click",function(){
        var sp_size =  jQuery("#opt_qrcodesize").val();
        window.location.href="<?php echo WEB_PATH?>/merchant/demopdf/demo_qrcode.php?id="+jQuery("#hdn_qrcodedn_location_id").val()+"&size="+sp_size+"&is_location=1s";
        close();
       //  close_popup('confirmation');
       jQuery.fancybox.close();
    });
     
    jQuery("#btn_cancel").live("click",function(){
    //close_popup("confirmation") ;
    jQuery.fancybox.close();
 });

//function save_from_library()
//{
//      
//	 var sel_val = jQuery('input[name=use_image]:checked').val();
//	 <!--// 369-->
//	 if (sel_val==undefined)
//	 {
//	 	close_popup('Notification');
//	 }
//	 else
//	 {
//		
//		jQuery("#hdn_image_id").val(sel_val);
//		var sel_src = jQuery("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
//		//alert(sel_src);
//		
//	       
//	       jQuery("#hdn_image_path").val(sel_src);
//	       /* NPE-252-19046 */
//	       
//	       /* NPE-252-19046 */
//	       file_path = "";
//	       close_popup('Notification');
//	       var img = "<img src='<?=ASSETS_IMG ?>/m/logo/"+ sel_src +"' class='displayimg'>";
//	       jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/large_close.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
//	 }
//	 <!--// 369-->
//}
jQuery("#btn_continue").click(function(){
window.location.href = "<?php echo WEB_PATH?>/merchant/locations_for_marketingmaterial.php";
//     jQuery.ajax({
//                                      type: "POST",
//                                      url: "<?=WEB_PATH?>/merchant/process.php",
//                                      data: "getlocationlist_for_marketingmaterial=yes",
//                                      async : false,
//                                      success: function(msg) {
//                                      
//                                          jQuery(".locationlist_div").html(msg);
//                                          
//                                        //  bind_event();
//                                        //  close();
//                                          //$("#jqReviewHelpfulMessageNotification").html(msg);
//                                      }
//        });
});
}
jQuery(".downloadqrcode").click(function(){

//open_popup('qrcode');
var loc_id=jQuery(this).attr("locid");
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
                                 window.location.href=obj.link;
                                    
                                }
                                else
                                    {
                                        
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?=WEB_PATH?>/merchant/process.php",
                                            data: "getQRcodelist=yes&location_id="+loc_id,
                                            async : false,
                                            success: function(msg) {

                                                jQuery(".QRcode_detail_div").html(msg);
                                                                                         var data_found = jQuery(".QRcode_detail_div #hdn_qrcode_data_found").val();
                                                if(data_found=="false")
                                                                                        {
                                                                                                var head_msg="<div class='head_msg'>Message</div>"

                                                                                        }
                                                                                        else
                                                                                        {
                                                                                        var head_msg="<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['download_qr_code'];?></div>"
                                                                                        }
                                              var content_msg="<div class='content_msg'>"+ msg +"</div>";
                                              var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                              jQuery( "#dialog-message" ).html(head_msg + content_msg );//+footer_msg);
                                                          jQuery.fancybox({
                                                           content:jQuery("#dialog-message" ).html(),
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
                                                bind_event();
                                             close();
                                            }
              });

              return false;
                                        
                                    }
                           }
});


});
  jQuery(".rdownloadqrcode").click(function(){
  
      // open_popup('qrcode');
   
                                     jQuery("#hdn_dettach_qrcode_location_id").val(jQuery(this).attr("locid"));
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
                                 window.location.href=obj.link;
                                    
                                }
                                else
                                    {
                                            jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
										var head_msg="<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['unlink_qr_code'];?></div>"
										var content_msg='<div class="content_msg"><?php echo $confirmationstring; ?></div>';
										var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
										jQuery( "#dialog-message" ).html(head_msg + content_msg );//+footer_msg);
                                                    jQuery.fancybox({
						     content:jQuery("#dialog-message" ).html(),
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
                                          bind_confirmation_event(); 
                                        
                                    }
                           }
                                     });       
    });
 
 jQuery('#btn_downloadqrcode').click(function(){
        
        var selectedVal = "";
var selected = jQuery("input[name='chklocation[]']:checked").length;
var c = "";
var str_location = "";
alert(selected);
if (selected > 0)
    {
             jQuery("input[name='chklocation[]']:checked").each(function() {
             str_location += jQuery(this).val() +  "-";
 
});
str_location = str_location.substring(0,str_location.length-1);
//alert(str_location );

            window.location.href="<?php echo WEB_PATH?>/merchant/demopdf/demo.php?id="+val+"locationlist="+str_locations;
    }
    else{
     
       //alert("Please Select At least One Location");
       var content_msg="<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_select_atleast_one_location'];?></div>";
       var head_msg="<div class='head_msg'>Message</div>"
       var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
 
    

return false;
   
      
	});
function back_to_managemarketingmaterial()
{
    window.locaton.href= "<?php echo WEB_PATH ?>/merchant/manage-marketing-material.php";
}
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
function save_from_library()
{
        $av = jQuery.noConflict();
	 var sel_val = $av('input[name=use_image]:checked').val();         
         //alert(sel_val);
	 $av("#hdn_image_id").val(sel_val);
         
	var sel_src = $av(".fancybox-inner #li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
        
	 //alert(sel_src);
	$av("#hdn_image_path").val(sel_src);
	/* NPE-252-19046 */
	
	/* NPE-252-19046 */
	file_path = "";
	jQuery.fancybox.close();
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ sel_src +"' class='displayimg'>";
        
	$av('#files').html(img +"<br/><div display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
}

//
jQuery("#document_size").change(function(){
    
        
        return false;
});

jQuery("#btn_preview").click(function(){
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
			window.location.href=obj.link;
		}
		else
        {                                   
			//    alert(tinyMCE.get('txt_material').getContent());
			//
			//    if(tinyMCE.activeEditor.getContent()=='<p>&nbsp;</p><div id="__tbSetup">&nbsp;</div>')
			//        {
			//            alert("In If");
			//            
			//        }
			//        else{
			//            alert("In Else");
			//        }
			//   alert("clicked") ;
			var val = "";

			if(jQuery('#document_size').val()==0)
			{
				var head_msg="<div class='head_msg'>Message</div>"
				var content_msg="<div class='content_msg' style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['msg_select_document_size'];?></div>";
				var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
				
				return false;
			}
		
			var acticationcode = "<?php if(isset($_REQUEST['activationcode']))echo $_REQUEST['activationcode']?>";
			var lid = 0;
				
			if(acticationcode=="")
			{
				if(jQuery("#hdn_campaignid").val() ==  0 || jQuery("#hdn_campaignid").val() == "" )
				{
					
					//alert("Select Location For Marketing Material");
					var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_select_location'];?></div>";
					var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
					
					return false;
				}
				lid = "<?php echo $_REQUEST['id']?>";
			}
			else
			{
				//alert("campaign");
				
				if( jQuery('input:radio[name=rd_locationtitle]:checked').length == 0)
				{
					
					//alert("Select Location For Marketing Material");
					var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_select_location'];?></div>";
					var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
					
					return false;
				}
				else if(jQuery("#hdn_campaignid").val() ==  0)
				{
					var selectedValue = jQuery('input:radio[name=rd_locationtitle]:checked').val();

					if(jQuery.trim(jQuery("a[locid='"+selectedValue+"']").attr("class"))=="rdownloadqrcode")
					{

					}
					else
					{
						//alert("Please Link QR Code Status to Selected Location");
						var head_msg="<div class='head_msg'>Message</div>"
						var content_msg="<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_link_qr_code_selected_location'];?></div>";
						var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
						return false;
					}
					
				}
				lid = jQuery('input:radio[name=rd_locationtitle]:checked').val();
			}
				
			if(jQuery("#hdn_image_path").val()=="" )
			{
				//alert("Select Image For Your Marketing Material");
				var head_msg="<div class='head_msg'>Message</div>"
				var content_msg="<div class='content_msg' style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_image_to_material'];?></div>";
				var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
			else
			{
				///alert(jQuery("#hdn_image_path").val());
				//alert( "getmarketingmaterial_loyalty=yes&locid="+lid+"&campid=<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id'] ?>&image="+ jQuery("#hdn_image_path").val()+"&text="+jQuery("#txt_material").val()+"&size_id="+jQuery("#document_size").val());
				//console.log("getmarketingmaterial_loyalty=yes&locid="+lid+"&campid=<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id'] ?>&image="+ jQuery("#hdn_image_path").val()+"&text="+jQuery("#txt_material").val()+"&size_id="+jQuery("#document_size").val()+"&merchant_id=<?php echo $_SESSION['merchant_id']; ?>");
				jQuery.ajax({
					type: "POST",
					url: "<?=WEB_PATH?>/merchant/process.php",
					data: "getmarketingmaterial_loyalty=yes&locid="+lid+"&campid=<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id'] ?>&image="+ jQuery("#hdn_image_path").val()+"&text="+jQuery("#txt_material").val()+"&size_id="+jQuery("#document_size").val()+"&merchant_id=<?php echo $_SESSION['merchant_id']; ?>",
					async : false,
					success: function(msg) {
						var obj = jQuery.parseJSON(msg);
						//       alert(obj.location_image);
						jQuery(".div_preview").html(obj.html_content);

						var d = new Date();
						var n = d.getTime();

						jQuery(".div_preview img[id='img_campaign']").attr("src","<?php echo WEB_PATH."/merchant/demopdf/".$_SESSION['merchant_id']."_upload/resized_image1.jpg?n="; ?>"+n);

						jQuery("#img_qrcode").attr("src","<?php echo WEB_PATH.'/libraries/demopdf/'.$_SESSION['merchant_id'].'_upload/template-qrcode1.jpg'; ?>");
						jQuery("#img_location").attr("src",obj.location_image);
						jQuery("#description_content").html(tinyMCE.activeEditor.getContent());
						jQuery("#location_address").html(obj.location_address);
						jQuery("#appstore-image").attr("src","<?php echo ASSETS_IMG ?>/m/app-store.png");
						jQuery("#anroid-image").attr("src","<?php echo ASSETS_IMG ?>/m/anroid.png");
						jQuery("#scanflip-logo").attr("src","<?php echo ASSETS_IMG ?>/m/scanplip-logo.png");
						//   open_popup("confirmation");
						//   alert(jQuery('.div_preview').html());
                              
						jQuery.fancybox({
							content:jQuery('.div_preview1').html(),
							width:800,
							height:400,
							autoScale:false, 
							fitToView : false,
							centerOnScroll :false,
							autoDimension:true,
							type: 'html',
							openSpeed  : 300,
							closeSpeed  : 300,
							changeFade : 'fast', 
							scrolling : 'no',
							beforeLoad:function(){
							//  jQuery(".fancybox-inner").css("width","800px");
							},
							afterLoad : function(){
								jQuery(".fancybox-wrap .fancybox-close").css("top","-15px");
								jQuery(".fancybox-wrap .fancybox-close").css("right","-15px");
							},
							afterShow:function(){
								jQuery(".fancybox-inner").css("height","200px");
								jQuery(".fancybox-inner #location_address").parent().css("text-align","left");
								jQuery(".div_preview #description_content").css("text-align","left");
								jQuery(".fancybox-wrap .fancybox-close").css("top","-15px");
								jQuery(".fancybox-wrap .fancybox-close").css("right","-15px");

							},
							helpers: {
								overlay: {
								opacity: 0.3
								} // overlay
							}
						});
					}
			});
		}
        return false;
		}
    }
    });
});

jQuery("#btn_download").click(function(){
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
				window.location.href=obj.link;
			}
			else
			{
				if(jQuery('#document_size').val()==0)
				{
					var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg' style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['msg_select_document_size'];?></div>";
					var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
					return false;
				}
				//alert("In downloading");
				
				var acticationcode = "<?php if(isset($_REQUEST['activationcode']))echo $_REQUEST['activationcode']?>";
				var lid = 0;
					
				if(acticationcode=="")
				{
					if(jQuery("#hdn_campaignid").val() ==  0 || jQuery("#hdn_campaignid").val() == "" )
					{
						
						//alert("Select Location For Marketing Material");
						var head_msg="<div class='head_msg'>Message</div>"
						var content_msg="<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_select_location'];?></div>";
						var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
						
						return false;
					}
					lid = "<?php echo $_REQUEST['id']?>";
				}
				else
				{
					//alert("campaign");
					
					if( jQuery('input:radio[name=rd_locationtitle]:checked').length == 0)
					{
						//alert("Select Location For Marketing Material");
						var head_msg="<div class='head_msg'>Message</div>"
						var content_msg="<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_select_location'];?></div>";
						var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
						return false;
					}
					else if(jQuery("#hdn_campaignid").val() ==  0)
					{
						var selectedValue = jQuery('input:radio[name=rd_locationtitle]:checked').val();

						if(jQuery.trim(jQuery("a[locid='"+selectedValue+"']").attr("class"))=="rdownloadqrcode")
						{
						}
						else
						{
							//alert("Please Link QR Code to Selected Location");
							var head_msg="<div class='head_msg'>Message</div>"
							var content_msg="<div class='content_msg''><?php echo $merchant_msg['marketingmaterial']['please_link_qr_code_selected_location'];?></div>";
							var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
							jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
							jQuery.fancybox({
								content:jQuery('#dialog-message').html(),
								type: 'html',
								fitToView:true,
								imageScale:true,
								autoSize:true,
								openSpeed  : 300,
								closeSpeed  : 300,
								changeFade : 'fast',  
								helpers: {
									overlay: {
									opacity: 0.3
									} // overlay
								}
							});
							return false;
						}
					}
					lid = jQuery('input:radio[name=rd_locationtitle]:checked').val();
				}
				
				if(jQuery("#hdn_image_path").val()=="" )
				{
					//alert("Select Image For Your Marketing Material");
					var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg' style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_image_to_material'];?></div>";
					var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
					jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
					jQuery.fancybox({
						content:jQuery('#dialog-message').html(),
						type: 'html',
						fitToView:true,
						imageScale:true,
						autoSize:true,
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
				else
				{
					//console.log("getmarketingmaterial_loyalty=yes&locid="+lid+"&campid=<?php if(isset($_REQUEST['id']))echo $_REQUEST['id'] ?>&image="+ jQuery("#hdn_image_path").val()+"&text="+jQuery("#txt_material").val()+"&size_id="+jQuery("#document_size").val()+"&merchant_id=<?php echo $_SESSION['merchant_id']; ?>");	
					jQuery.ajax({
						type: "POST",
						url: "<?=WEB_PATH?>/merchant/process.php",
						data: "getmarketingmaterial_loyalty=yes&locid="+lid+"&campid=<?php if(isset($_REQUEST['id']))echo $_REQUEST['id'] ?>&image="+ jQuery("#hdn_image_path").val()+"&text="+jQuery("#txt_material").val()+"&size_id="+jQuery("#document_size").val()+"&merchant_id=<?php echo $_SESSION['merchant_id']; ?>",
						async : false,
						success: function(msg) {
							
							var obj = jQuery.parseJSON(msg);
							jQuery(".div_preview").html(obj.html_content);
							
							jQuery("#img_qrcode").attr("src","<?php echo WEB_PATH.'/QR-Generator-PHP-master/php/qr.php?d=';?>"+obj.qrcode+"&size=180");
							jQuery("#img_location").attr("src",obj.location_image);
							jQuery("#description_content").html(tinyMCE.activeEditor.getContent());
							jQuery("#location_address").html(obj.location_address);
							jQuery("#appstore-image").attr("src","<?php echo ASSETS_IMG ?>/m/app-store.png");
							jQuery("#anroid-image").attr("src","<?php echo ASSETS_IMG ?>/m/anroid.png");
							jQuery("#scanflip-logo").attr("src","<?php echo ASSETS_IMG ?>/m/scanplip-logo.png");
							jQuery(".div_preview #location_address").parent().css("text-align","left");
							jQuery(".div_preview #description_content").css("overflow","hidden");
							
							
							/* from preview */
							
							var d = new Date();
							var n = d.getTime();

							jQuery(".div_preview img[id='img_campaign']").attr("src","<?php echo WEB_PATH."/merchant/demopdf/".$_SESSION['merchant_id']."_upload/resized_image1.jpg?n="; ?>"+n);

							jQuery("#img_qrcode").attr("src","<?php echo WEB_PATH.'/libraries/demopdf/'.$_SESSION['merchant_id'].'_upload/template-qrcode1.jpg'; ?>");
							jQuery("#img_location").attr("src",obj.location_image);
							jQuery("#description_content").html(tinyMCE.activeEditor.getContent());
							jQuery("#location_address").html(obj.location_address);
							jQuery("#appstore-image").attr("src","<?php echo ASSETS_IMG ?>/m/app-store.png");
							jQuery("#anroid-image").attr("src","<?php echo ASSETS_IMG ?>/m/anroid.png");
							jQuery("#scanflip-logo").attr("src","<?php echo ASSETS_IMG ?>/m/scanplip-logo.png");
						
							/* from preview */

							
							var id="<?php if(isset($_REQUEST['id']))echo $_REQUEST['id']?>";
							
							var acticationcode = "<?php if(isset($_REQUEST['activationcode']))echo $_REQUEST['activationcode']?>";
							
							//if (id == 0) 
							if (acticationcode == "") 
							{
								// for location
								
								//jQuery('.locationtitle').each(function(){

								//var locationid=jQuery('input:radio[name=rd_locationtitle]:checked').val();
								var locationid=lid;
								
								var qrcodeid="<?php if(isset($qrcodestring)) echo $qrcodestring;?>";

								// alert("<?php echo WEB_PATH ?>/merchant/demopdf/demo_loadtemplate.php?locid="+lid+"&campid=<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>&image="+ jQuery("#hdn_image_path").val()+"&text="+encodeURIComponent(tinyMCE.activeEditor.getContent())+"&size_id="+jQuery("#document_size").val()+"&activationcode="+locationid+"&qrcode="+qrcodeid)
								window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_loadtemplate.php?locid="+lid+"&campid=<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>&image="+ jQuery("#hdn_image_path").val()+"&text="+encodeURIComponent(tinyMCE.activeEditor.getContent())+"&size_id="+jQuery("#document_size").val()+"&activationcode="+locationid+"&qrcode="+qrcodeid;
								//var qrcodeid=

								//});
							}
							else
							{
								// for loyalty card
								
								var qrcodeid="<?php if(isset($_REQUEST['qrcodeid'])) echo $_REQUEST['qrcodeid'];?>";
								var activationcode="<?php if(isset($_REQUEST['activationcode'])) echo $_REQUEST['activationcode'];?>";

								window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_loadtemplate.php?locid="+lid+"&campid=<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id'] ?>&image="+ jQuery("#hdn_image_path").val()+"&text="+encodeURIComponent(tinyMCE.activeEditor.getContent())+"&size_id="+jQuery("#document_size").val()+"&activationcode="+activationcode+"&qrcode="+qrcodeid+"&is_loyalty=1";
								// open_popup("confirmation");
							}             
						}
					});
				}
                return false;                                
			}
		}
	});
});
function getCookie(c_name)
	{
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++)
	  {
	  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
	  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
	  x=x.replace(/^\s+|\s+$/g,"");
	  if (x==c_name)
	    {
	    return unescape(y);
	    }
	  }
	}
	
	function setCookie(c_name,value,exdays)
	{
     var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
	}
</script>
<?php 
if($_REQUEST['id'] == 0){
?>
<script>

				jQuery('#mkt_mtr_location_table').dataTable({
					 "bFilter": false,
					"bSort" : false,
					"bLengthChange": false,
					"info": false,
					 // "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                     "iDisplayLength" :10,
					//"aoColumnDefs":[{"bSortable":false , "aTargets":[0,2 ]}]
				});
				jQuery(".table_loader").css("display","none");
				jQuery(".datatable_container").css("display","block");
</script>
<?php
}
else
{
?>
<script>

				jQuery('#mkt_mtr_location_table').dataTable({
					 "bFilter": false,
					"bSort" : false,
					"bLengthChange": false,
					"info": false,
					 // "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                     "iDisplayLength" : 10,
					//"aoColumnDefs":[{"bSortable":false , "aTargets":[0 ]}]
				});
				jQuery(".table_loader").css("display","none");
				jQuery(".datatable_container").css("display","block");
</script>
<?php
}
?>
<script>
    jQuery(document).ready(function() {
	 var sizetext=jQuery("#document_size :selected").val();
	 if (sizetext == 1) {
	    	jQuery(".info").show();
		jQuery("#msg_div_image_size").text("Recommended image size to link: 549 pixels x 130 pixels (142 mm x 110 mm).");
	 }
		
				jQuery("#popupcancel").live("click",function(){
					jQuery.fancybox.close(); 
					return false; 
				 });
				jQuery("#document_size").change(function(){
				    var sizetext=jQuery("#document_size :selected").val();
				    if (sizetext == 1) {
					    jQuery(".info").show();
					    jQuery("#msg_div_image_size").text("Recommended image size to link: 549 pixels x 130 pixels (142 mm x 110 mm).");
                                            //.text("Flyer Standard( banner- 142X110 mm ) - 549X130 px (145mmX34mm)");
				     }
				    else if (sizetext == 2) {
					    jQuery(".info").show();
					    jQuery("#msg_div_image_size").text("Recommended image size to link: 800 pixels x 150 pixels (211 mm x 39 mm).");
                                            //"Flyer Oversize( full bleed size- 219X142 mm ) - 800X150 px (211mmX62mm)");
				     }
				    else if (sizetext == 3) {
					    jQuery(".info").show();
					    jQuery("#msg_div_image_size").text("Recommended image size to link: 800 pixels x 235 pixels (211 mm x 62 mm).");
                                            //"Flyer A4( full bleed size- 219X282 mm )  - 800X235px (211mmX62mm)");
				     }
				    else if (sizetext == 5) {
					    jQuery(".info").show();
					    jQuery("#msg_div_image_size").text("Recommended image size to link: 670 pixels x 140 pixels (177 mm x 37 mm).");
                                      }
				    
				});
				
			} );
                        jQuery(".mediaclass").click(function(){
                                    jQuery.fancybox({
                                                content:jQuery('#mediablock').html(),

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
         function show_tooltip(){
	 jQuery('.notification_tooltip').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
});
}
show_tooltip();
 jQuery("input[type='radio'][id^='rd_campaigntitle_']").live("click",function() {
			  if(jQuery(this).attr("visit") == "first")
			 {
			        jQuery(this).attr("checked",true);
					jQuery(this).attr("visit","second");
			 }
			 else{
					jQuery("input[type='radio'][name='rd_locationtitle']:checked").attr("checked",false);
					jQuery(this).attr("visit","first");
			 }
			
			 });
			jQuery("a[id^='showCamp_']").live("click",function() {
			
				var val_arr=jQuery(this).attr('id').split("_");
				if(  jQuery("#rd_campaigntitle_"+val_arr[1]).attr("checked"))
				{
				jQuery("input[type='radio'][name='rd_locationtitle']:checked").attr("checked",false);
				jQuery("#rd_campaigntitle_"+val_arr[1]).attr("visit","first");
				}
				else{
                                jQuery("#rd_campaigntitle_"+val_arr[1]).attr("checked",true);
								jQuery("#rd_campaigntitle_"+val_arr[1]).attr("visit","second");
					}			 
			});
    </script>
