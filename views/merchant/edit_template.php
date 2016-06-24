<?php
/**
 * @uses to edit template
 * @used in pages :templates.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where_act['active']=1;
$RS = $objDB->Show("categories",$array_where_act);
$campaigns_id = $_REQUEST['id'];
$array_where['id'] = $_REQUEST['id'];
$RS_t = $objDB->Show("campaigns_template", $array_where);
$title = $RS_t->fields['title']; 
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Edit Templates</title>
<?php //require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<script type="text/javascript" src="<?=ASSETS ?>/tinymce/tiny_mce.js"></script>
<script language="javascript" src="<?=ASSETS_JS ?>/m/jquery.datepick.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?=ASSETS_CSS ?>/m/jquery.datepick.css">
<script language="javascript" src="<?=ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery.form.js"></script>
<script language="javascript">
$(document).ready(function() { 
    // bind form using ajaxForm 

    $('#edit_template_form').ajaxForm({
        
        beforeSubmit:processLoginorNot,
        dataType:  'json', 
        success:   processEditTempJson 
    });
	//changetext2();
});
function changetext2(){
   var v = 8 - $("#discount").val().length;
   $(".span_c2").text(v+" characters remaining");
}
function processLoginorNot() {

var flag=0;
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
                                    flag=1;
                                    window.location.href=obj.link;
                                    
                                }
                                                              
                           }
                           
                     });
                     if(flag == 1)
                     {
                         return false;
                     }
                     else
                     {
                         return true;
                     }       
}
function processEditTempJson(data) { 
	
	if(data.status == "true"){

		window.location.href='<?=WEB_PATH.'/merchant/templates.php'?>';
	}
	else
	{
		//alert(data.message);
		var head_msg="<div class='head_msg'>Message</div>";
				var content_msg="<div class='content_msg'>"+data.message+"</div>";
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
	}
	
}
$(function()
{		
	$('#start_date').datepick({dateFormat: 'mm-dd-yyyy'});
	$('#expiration_date').datepick({dateFormat: 'mm-dd-yyyy'});
});
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
                            document.getElementById("desc_limit").innerHTML=l+" characters remaining";
                        if(tinyMCE.activeEditor.id=="terms_condition")
                            document.getElementById("terms_limit").innerHTML=l+" characters remaining";
		});
		}
	});
jQuery(".textarea_loader").css("display","none");
jQuery(".textarea_container").css("display","block");

                } );
</script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap.css" />
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
<style>

#tooltip{
	z-index:9999 !important;
	opacity:1.0 !important;
}
.disabledmedia{
                            background-color: #ABABAB !important;
                            background-image:url("images/button-corner-dis1.jpg") !important;
                        }
                        .disabledmedia:hover{
                            background-color: #ABABAB !important;
                                background-image:url("images/button-corner-hover1.png") !important;
                        }
</style>
</head>

<body>
     <div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div >

<!---start header---->
	<div>
		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	
    
	<div id="content">


  <h3><?php echo $merchant_msg['templates']['Field_edit_template']; ?></h3>
	<form action="process.php" method="post" enctype="multipart/form-data" id="edit_template_form">
            <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?=$RS_t->fields['business_logo']?>" />
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
				<table width="100%"  border="0" cellspacing="2" cellpadding="2" id="table_spacer">
				 <!-- <tr>
				    <td colspan="2" align="center" style="color:#FF0000; "><?=$_SESSION['msg']?></td>
			      </tr>-->
				  <tr>
					<td width="20%" align="right"><?php echo $merchant_msg['templates']['Field_template_title']; ?></td>
					<td width="80%" align="left">
						<?php
							$title=$RS_t->fields['title'];
							//echo $RS_t->fields['title'];
							//echo "</br>";
							
							//$title=str_replace("''","'",$RS_t->fields['title']);
							
							//echo $title;
							//echo "</br>";
							
							//echo htmlentities($title);
							//echo "</br>";
						?>
						<input type="text" name="title" id="title" size="50" onkeyup="changetext1()" placeholder="Required"  maxlength="76" value="<?=htmlentities($title);?>" /><span class="span_c1" >Maximum 76 characters | No HTML allowed</span>
					</td>
				  </tr>
				  <!--
                                  <tr>
					<td width="20%" align="right"><?php echo $merchant_msg['templates']['Field_discount_rate']; ?></td>
					<td width="80%" align="left">
						<input type="text" name="discount" id="discount" value="<?=$RS_t->fields['discount']?>" onkeyup="changetext2()" maxlength="8"/>
						<span class="span_c2"  >Maximum 8 characters</span>
						<span class="notification_tooltip"  title="<?php echo $merchant_msg['templates']['Tooltip_discount_rate']; ?>">&nbsp;&nbsp;&nbsp;</span>
					</td>
				  </tr>
                    -->              
				  <tr>
					<td align="right"><?php echo $merchant_msg['templates']['Field_category']; ?></td>
					<td align="left">
					<select name="category_id" id="category_id">
					<option value="0">Select Category</option>
					<?
					while($Row = $RS->FetchRow()){
						echo $Row['id'];
					?>
						<option value="<?=$Row['id']?>" <? if($RS_t->fields['category_id'] == $Row['id']) echo "selected";?>><?=$Row['cat_name']?></option>
					<?
					}
					?>
					</select>
					</td>
				  </tr>
				  <tr>
					<td align="right"><?php echo $merchant_msg['templates']['Field_business_logo']; ?></td>
					<td align="left">
						<!-- start of  PAY-508-28033   -->
						<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
						<div class="cls_left">
									<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
									<div id="upload" >
									<span  >Browse
									</span> 
									</div>
									</div> <div class="browse_right_content"> &nbsp;&nbsp;<span >Or select from </span><a class="mediaclass"  > media library </a></div> 
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
				  <tr>
					<td align="right"><?php echo $merchant_msg['templates']['Field_web_description']; ?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg['templates']['Tooltip_web_description']; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
					<td align="left" class="textare_td">
					<div align="center" class="textarea_loader">
					<img   src="<?php echo ASSETS_IMG.'/32.GIF' ?>" class="defaul_table_loader" />
					</div>
					<div class="textarea_container" style="display: none;">	
					<textarea id="description" name="description" rows="15" cols="80"  class="table_th_80"><?=htmlentities($RS_t->fields['description'])?></textarea>
					</div><span id="desc_limit">Maximum 1200 characters | No HTML allowed</span>
                                        </td>
				  </tr>
				  
                                  <tr>
					<td align="right"><?php echo $merchant_msg['templates']['Field_term_condition']; ?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg['templates']['Tooltip_term_condition']; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
					<td align="left" class="textare_td">
					<div align="center" class="textarea_loader">
					<img   src=""<?php echo ASSETS_IMG.'/32.GIF' ?>" class="defaul_table_loader" />
					</div>
					<div class="textarea_container" style="display: none;">	
					<textarea id="terms_condition" name="terms_condition" rows="15" cols="80"  class="table_th_80"><?=htmlentities($RS_t->fields['terms_condition'])?></textarea>
					</div><span id="terms_limit">Maximum 1200 characters | No HTML allowed</span>
                                        </td>
				  </tr>
                   <!--               
                   <tr>
					<td align="right"><?php echo $merchant_msg['templates']['Field_description']; ?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg['templates']['Tooltip_description']; ?>">&nbsp;&nbsp;&nbsp;</span>:  </td>
					<td align="left">
					<textarea id="print_coupon_description" name="print_coupon_description" rows="10" cols="40" style="width: 80%"><?=htmlentities($RS_t->fields['print_coupon_description'])?></textarea>
					</td>
				  </tr>
                   -->               
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
                                             <script>function btncanTemplate(){                                                
                                                window.location="<?=WEB_PATH?>/merchant/templates.php";
                                                }
                                                function mycall()
						{
													jQuery('#description').val(tinyMCE.get('description').getContent());
													jQuery('#terms_condition').val(tinyMCE.get('terms_condition').getContent());
						}
                                                </script>
						<input type="hidden" value="<?php echo $campaigns_id; ?>" name="bna" />
						<input type="submit" name="btneditTemplate" value="<?php echo $merchant_msg['index']['btn_save'];?>" onClick="mycall();" id="btneditTemplate">
                                               <input type="submit" name="btnCancel" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanTemplate()">                                                
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
<!-- start of image upload popup div for PAY-508-28033-->
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
									<?php echo $merchant_msg['templates']['Field_add_campaign_logo']; ?>
								</font>
							 </div>
                                                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
							<!-- -->
							<div id="media-upload-header">
								<ul id="sidemenu">
								<li id="tab-type" class="tab_from_library"><a class="current" ><?php echo $merchant_msg['templates']['Field_media_library']; ?></a></li>
								
								</ul>
							</div>
							<!-- -->
								
                                                          
							   <div style="clear: both" ></div>
							   <div style="display: none;padding-left: 13px; padding-right: 13px;" class="div_from_computer">
								<div  style="padding-top:10px;padding-bottom:10px"><?php echo $merchant_msg['templates']['Field_add_media_library']; ?>
									
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
								<div  style="padding-top:10px;padding-bottom:10px"><?php echo $merchant_msg['templates']['Field_add_campaign_logo_media_library']; ?></div>
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
										/*$query = "select * from merchant_media where image_type='template' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'].") order by id desc" ;
										$RSImages = $objDB->execute_query($query);*/
										$RSImages = $objDB->Conn->Execute("select * from merchant_media where image_type=? and (merchant_id=? or merchant_id=?) order by id desc",array('template',$_SESSION['merchant_id'],$merchant_info->fields['merchant_parent']));

										 if($RSImages->RecordCount()>0){
										while($Row = $RSImages->FetchRow()){
										?>
									
										
										<li class="li_image_list" id="li_img_<?=$Row['id'];?>">
											<div>
												<img src="<?php echo ASSETS_IMG ?>/m/campaign/".$Row['image'];  ?>" height="50px" width="50px" />
												<span style="vertical-align: top" id="span_img_text_<?=$Row['id'];?>"><?=$Row['image']?></span>
												<span style="vertical-align: top;float: right"> Use this image&nbsp;<input type="radio" name="use_image" value="<?=$Row['id']?>" /></span>
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
											<?php echo $merchant_msg['templates']['Msg_dont_access_images']; ?>
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
		echo file_get_contents(WEB_PATH.'/merchant/import_media_library.php?mer_id='.$_SESSION['merchant_id'].'&img_type=template&start_index=0');
	?>
 </form>
 <!-- end of popup div for PAY-508-28033-->
<script>
	
function changetext1(){
   var v = 76 - jQuery("#title").val().length;
   jQuery(".span_c1").text(v+" characters remaining");
}
changetext1();
	
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
	
jQuery(function(){
		var btnUpload=jQuery('#upload');
		var status=jQuery('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUpload&img_type=template',
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
/* start of script for PAY-508-28033*/
function save_from_library()
{
        $av = jQuery.noConflict();
	 var sel_val = $av('input[name=use_image]:checked').val();
         
	
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
              
    
              
	       $av('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/large_close.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
	 }

}
function rm_image()
{
	jQuery("#hdn_image_path").val("");
	jQuery("#hdn_image_id").val("");
	jQuery('#files').html("");
	
}
function rm_image_permanent(id)
{

	jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'is_image_delete=yes&image_type=campaign&filename='+id,
                          async:false,
                           success:function(msg)
                           {
								jQuery("#hdn_image_path").val("");
								jQuery("#hdn_image_id").val("");
								jQuery('#files').html("");
                           }
                           
                     });
}
function save_from_computer()
{
	jQuery("#hdn_image_path").val(file_path);
	jQuery("#hdn_image_id").val("");
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ file_path +"' class='displayimg'>";
	jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/large_close.png' id='"+file_path+"'  class='cancel_remove_image' onclick='rm_image_permanent(this.id)' /></div></div></div>");
}

$(document).ready(function() {
	if($("#hdn_image_path").val() != "")
	{
		var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ $("#hdn_image_path").val() +"' class='displayimg'>";
		

		if ($("#hdn_image_path").val().startsWith("media"))
		{
			$('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/large_close.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");	
		}
		else
		{
			$('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/large_close.png' class='cancel_remove_image' onclick='rm_image_permanent(this.id)' /></div></div></div>");
		}
	}
	$("#btneditTemplate").click(function(){
		
		
		var alert_msg="<?php  echo  $merchant_msg["index"]["validating_data"]; ?>";
var head_msg="<div class='head_msg'>Message</div>";
var content_msg="<div class='content_msg validatingdata' style='background:white;'>"+alert_msg+"</div>";
jQuery("#NotificationloadermainContainer").html(content_msg);
jQuery("#NotificationloaderFrontDivProcessing").css("display","block");
jQuery("#NotificationloaderBackDiv").css("display","block");
jQuery("#NotificationloaderPopUpContainer").css("display","block");

	     alert_msg = "";
  
    var flag="true";
    var t_name=jQuery("#title").val();
    jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :"check_template_name_exist=yes&template_name="+ t_name +"&mer_id=<?php echo $_SESSION['merchant_id']; ?>&id=<?php echo $_REQUEST['id'];?>",
                          async:false,
                           success:function(msg)
                           {
                               
                              
                               if(msg  == 1)
				    {
                                        
                                         flag= "true";
                                         
                                    }
                                    else
                                    {
                                        //$(".span_error_msg").text(" *Distribution List already exist. Please select different name.");
					
                                        alert_msg +="<?php echo $merchant_msg['templates']['Msg_template_exist']; ?>";
                                        flag = "false";

                                    }
                           }
        });
  
    
     if($("#title").val() == "")
     {
       flag="false";
       alert_msg="<div><?php echo $merchant_msg['templates']['Msg_enter_template_title']; ?></div>";
       
     }
      var cat_id=jQuery('#category_id option:selected').val();
	 if(cat_id==0)
	 {
		flag="false";
		alert_msg+="<div><?php echo $merchant_msg['templates']['Msg_select_template_category']; ?></div>";
	 }
      if(jQuery("#hdn_image_path").val()=="")
	 {
		 flag="false";
		alert_msg+="<div><?php echo $merchant_msg['templates']['Msg_upload_image']; ?></div>";
	 } 
    if(flag=="true")
    {
		var alert_msg="<?php  echo  $merchant_msg["index"]["saving_data"]; ?>";
		var head_msg="<div class='head_msg'>Message</div>";
		var content_msg="<div class='content_msg savingdata' style='background:white;'>"+alert_msg+"</div>";
		jQuery("#NotificationloadermainContainer").html(content_msg);
		return true;
    }
    else
    {
		close_popup("Notificationloader");	
        //alert(alert_msg);
       var head_msg="<div class='head_msg'>Message Box</div>"
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
       
	
	});

 jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
    
     jQuery('.notification_tooltip').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
    });
$(".tab_from_library a").click(function(){
	$("#sidemenu li a").each(function() {
		$(this).removeClass("current");
		});
	$(this).addClass("current");
	$(".div_from_library").css("display","block");
	$(".div_from_computer").css("display","none");
	});
$(".tab_from_computer a").click(function(){
	$("#sidemenu li a").each(function() {
		$(this).removeClass("current");
		});
	$(this).addClass("current");
	$(".div_from_library").css("display","none");
	$(".div_from_computer").css("display","block");
	});
        $('.mediaclass').click(function(){
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


/* end of of script for PAY-508-28033*/
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
</script>
<?
$_SESSION['msg'] = "";
?>
</body>
</html>
