<?
//require_once("../classes/Config.Inc.php");
include(SERVER_PATH."/merchant/classes/easy_upload/upload_class.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
include(LIBRARY."/simpleimage.php");
//$objDB = new DB();
//echo $_SESSION['admin_id'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<!-- start new plupload -->

<link rel="stylesheet" href="<?=ASSETS?>/jquery.ui.plupload/css/jquery.ui.plupload.css" type="text/css" />

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=ASSETS?>/jquery.ui.plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="<?=ASSETS?>/jquery.ui.plupload/jquery.ui.plupload.js"></script>

<!-- end new plupload -->

</head>

<body>
	<div id="container">
		<!---start header---->
		<?php
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		<div id="contentContainer">
			<div  id="sidebarLeft">
			<?php
			require_once(ADMIN_VIEW."/quick-links.php");
			?>
			</div><!--end of sidebar Left-->

			<div id="content">
			
				<h2>Media Management</h2>
				
				<div id="media-upload-header">
					<ul id="sidemenu">
						<li id="tab-type" data-hashval="uploaddiv" class="div_upload">
							<a class="current">Upload</a>
						</li>
						<li id="tab-type" data-hashval="imagediv"  class="div_image" >
							<a >Images</a>
						</li>
					</ul>
				</div>
				
				<div class="all_div_container">
					<div id="div_upload" class="tabs" style="display:block;">
						<div class="maindiv" align="center">
						   <table>
							   <tr>
								   <td style="padding-bottom:8px;float:left;">
										<?php //echo $merchant_msg["mediamanagement"]["Field_select_image_type"];?>
											<select id="selectimg" onchange="set_hidden_image_type(this)">
												<option value="0" selected>Select Image Type</option>
												<option value="1" >Campaign</option>
												<!--<option value="2" >Location</option>-->
											</select>
								   </td>
							   </tr>
							  
						   </table>
							  <div id="uploader">
									
								</div> 
						   <div class='msgclass table_errore_message'>
							   <?php echo $_SESSION['msg'];?>
						   </div>
						   
						</div>
						<div class="font_14" align="left">
							<br />
						</div>
					</div>
					
					<?php
							$start_index = 0;
							$num_of_records = 16;
							$next_index = $start_index + $num_of_records;
							
							$query = "select * from merchant_media where merchant_id=1 order by id desc limit 0,$num_of_records" ;
							$query1 = "select count(*) total from merchant_media where merchant_id=1 order by id desc" ;
							
							$myQuery = $query;    
							$RS = $objDB->Conn->Execute($query);
							$RS1 = $objDB->Conn->Execute($query1);
							  
							$total_images = $RS1->fields['total'];
							
					?>
						
					<?php
					if($RS->RecordCount()=="0")
					{			
					?>
						<div class='deltmsgclass' style="color: #ff0000;float: left;padding: 10px;width: 340px;" >
							<?php
								echo $merchant_msg["mediamanagement"]["Msg_no_images"];
							?>
						</div>
						<div class="deltmsgclass_close" style="cursor: pointer;float: left;margin-top: 10px;">
							X
						</div>
					<?php
					}
					?>
					<div id="div_image" class="tabs" style="display:none;margin-top: 15px;">
						
						
						<?php
						
						if($RS->RecordCount()>0)
						{
						?>
							<div id="mediya_image_container">
							<?php	
							while($Row = $RS->FetchRow())
							{
								
									$target = "campaign" ;
								
								
							?>
							<div class="mediya_img_blk <?php if($Row['media_type_id'] == "1"){echo "campaignfilter";} if($Row['media_type_id'] == "2"){echo "locationfilter";}?>">
								<a href="javascript:void(0)" class="mediya_camp_loca">
									<img title="Campaign" src="<?php echo ASSETS_IMG; ?>/a/mediya_campaign.png">								
								</a>
								<img src="<?php echo ASSETS_IMG."/m/".$target."/".$Row['image'];  ?>" class= "mediya_grid" />

								<a href="javascript:void(0)" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediadeleteid_<?=$Row['id']?>" class="mediya_delete">
									<img src="<?php echo ASSETS_IMG; ?>/a/mediya_delete.png">
								</a>

							</div>
							<?php
							}
							?>
						</div>
						<?php
						}
						

						if($RS->RecordCount()>0 && $total_images>$num_of_records)
						{
						?>
							<div id="mediya_showmore" style="display:block;">
								<input type="button" id="show_more_mediya" name="show_more_mediya" value="Show More" next_index="<?php echo $next_index ?>" num_of_records="<?php echo $num_of_records ?>" total_images="<?php echo $total_images ?>" />
							</div>
						<?php
						}
						?>
					</div>
				</div>
				
			</div><!--end of content-->
		</div><!--end of contentContainer-->
	</div><!--end of Container-->
<?php $_SESSION['msg']=""; ?>
</body>
</html>

<div style="display: none;" class="container_popup" id="NotificationLoadingDataPopUpContainer">
    <div class="divBack" id="NotificationLoadingDataBackDiv" style="display: block;">
    </div>
    <div style="display: block;" class="Processing" id="NotificationLoadingDataFrontDivProcessing">

        <div align="center" style="left: 45%;top: 40%;" class="textDivLoading" valign="middle" id="NotificationLoadingDataMaindivLoading">

            <div style="height:auto;width:auto" class="loading innerContainer" id="NotificationLoadingDatamainContainer">
				Loading ...
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	jQuery(function() {
	
	jQuery("#uploader").plupload({  
	//var uploader = new plupload.Uploader({
		
		//container : 'uploader',
		 	
		// General settings
		runtimes : 'html5,flash,silverlight,html4',
		url : '<?=WEB_PATH?>/admin/plupload.php',

		// User can upload no more then 20 files in one go (sets multiple_queues to false)
		max_file_count: 20,
		
		chunk_size: '10mb',
		/*
		// Resize images on clientside if we can
		resize : {
			width : 200, 
			height : 200, 
			quality : 90,
			crop: true // crop to exact dimensions
		},
		*/
		filters : {
			// Minimum file width
			min_width: 700,
			// Maximum file size
			max_file_size : '10mb',
			//max_file_size : '200kb',
			// Specify what files to browse for
			mime_types: [
				{title : "Image files", extensions : "jpg,gif,jpeg,png"},
				//{title : "Zip files", extensions : "zip"}
			]
		},

		// Rename files by clicking on their titles
		//rename: true,
		//unique_names:true,
		// Sort files
		sortable: true,

		// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
		dragdrop: true,

		// Views to activate
		views: {
			list: true,
			thumbs: true, // Show thumbs
			active: 'list'
		},

		// Flash settings
		flash_swf_url : '<?php echo ASSETS ?>/jquery.ui.plupload/js/Moxie.swf',

		// Silverlight settings
		silverlight_xap_url : '<?php echo ASSETS ?>/jquery.ui.plupload/js/Moxie.xap',
		multipart: true,
		multipart_params : {
			"image_type" : jQuery("#selectimg").val()
		},
		preinit : {
            Init: function(up, info) {
                //log('[Init]', 'Info:', info, 'Features:', up.features);  
            },
 
            UploadFile: function(up, file) {
                //log('[UploadFile]', file);
 
                // You can override settings before the file is uploaded
                // up.setOption('url', 'upload.php?id=' + file.id);
                // up.setOption('multipart_params', {param1 : 'value1', param2 : 'value2'});
            }
        },
		init : {
            PostInit: function() {
                // Called after initialization is finished and internal event handlers bound
                //log('[PostInit]');
                /* 
                document.getElementById('uploadfiles').onclick = function() {
                    uploader.start();
                    return false;
                };
                */
            },
 
            Browse: function(up) {
                // Called when file picker is clicked
                
                //log('[Browse]');
            },
 
            Refresh: function(up) {
                // Called when the position or dimensions of the picker change
                //log('[Refresh]');
            },
  
            StateChanged: function(up) {
                // Called when the state of the queue is changed
                //log('[StateChanged]', up.state == plupload.STARTED ? "STARTED" : "STOPPED");
            },
  
            QueueChanged: function(up) {
                // Called when queue is changed by adding or removing files
                if(jQuery("#selectimg").val()==0)
                {
					console.log("please select image type");
					jQuery("#uploader_start").addClass("ui-state-disabled");
				}
				else
				{
					jQuery("#uploader_start").removeClass("ui-state-disabled");
				}
                //log('[QueueChanged]');
            },
 
            OptionChanged: function(up, name, value, oldValue) {
                // Called when one of the configuration options is changed
                //log('[OptionChanged]', 'Option Name: ', name, 'Value: ', value, 'Old Value: ', oldValue);
            },
 
            BeforeUpload: function(up, file) {
                // Called right before the upload for a given file starts, can be used to cancel it if required
                up.settings.multipart_params = {'image_type': jQuery("#selectimg").val()}
                //log('[BeforeUpload]', 'File: ', file);
            },
  
            UploadProgress: function(up, file) {
                // Called while file is being uploaded
                //log('[UploadProgress]', 'File:', file, "Total:", up.total);
            },
 
            FileFiltered: function(up, file) {
                // Called when file successfully files all the filters
                //log('[FileFiltered]', 'File:', file);
    
            },
  
            FilesAdded: function(up, files) {
                // Called when files are added to queue
                //log('[FilesAdded]');
				
                plupload.each(files, function(file) {
                    //log('  File:', file);
                });
                
            },
  
            FilesRemoved: function(up, files) {
                // Called when files are removed from queue
                //log('[FilesRemoved]');
  
                plupload.each(files, function(file) {
                    //log('  File:', file);
                });
            },
  
            FileUploaded: function(up, file, info) {
                // Called when file has finished uploading
                //log('[FileUploaded] File:', file, "Info:", info);
                
                var response = jQuery.parseJSON(info.response);
				if(typeof(response.error) != "undefined")
				{
					//alert(response.error.message);
					if(response.error.code==500)
					{
						var str_msg='<div class="plupload_message ui-state-error">';
						str_msg+='<span title="Close" class="plupload_message_close ui-icon ui-icon-circle-close"></span>';
						str_msg+='<p><span class="ui-icon ui-icon-alert"></span><strong>Max File upload error.</strong>'; 
						str_msg+='<br><i>'+response.error.message+'</i>';
						str_msg+='</p>';
						str_msg+='</div>';	
						jQuery(".plupload_header").append(str_msg);
					}
					if(response.error.code==501)
					{
						var str_msg='<div class="plupload_message ui-state-error">';
						str_msg+='<span title="Close" class="plupload_message_close ui-icon ui-icon-circle-close"></span>';
						str_msg+='<p><span class="ui-icon ui-icon-alert"></span><strong>File width & height error.</strong>'; 
						str_msg+='<br><i>'+response.error.message+'</i>';
						str_msg+='</p>';
						str_msg+='</div>';	
						jQuery(".plupload_header").append(str_msg);
					}
					if(response.error.code==502)
					{
						var str_msg='<div class="plupload_message ui-state-error">';
						str_msg+='<span title="Close" class="plupload_message_close ui-icon ui-icon-circle-close"></span>';
						str_msg+='<p><span class="ui-icon ui-icon-alert"></span><strong>File width & height error.</strong>'; 
						str_msg+='<br><i>'+response.error.message+'</i>';
						str_msg+='</p>';
						str_msg+='</div>';	
						jQuery(".plupload_header").append(str_msg);
					}
					if(response.error.code==503)
					{
						var str_msg='<div class="plupload_message ui-state-error">';
						str_msg+='<span title="Close" class="plupload_message_close ui-icon ui-icon-circle-close"></span>';
						str_msg+='<p><span class="ui-icon ui-icon-alert"></span><strong>File width & height error.</strong>'; 
						str_msg+='<br><i>'+response.error.message+'</i>';
						str_msg+='</p>';
						str_msg+='</div>';	
						jQuery(".plupload_header").append(str_msg);
					}
				}
				else
				{
					jQuery.ajax({
						type:"POST",
						url:'process.php',
						data :'btnGetImageOfMerchantAjax=true',
						success:function(msg)
						{
							var obj = jQuery.parseJSON(msg);
							if (obj.status=="true") 
							{
								jQuery('#div_image').html(obj.html);
							}
						}
					});
				}
            },
  
            ChunkUploaded: function(up, file, info) {
                // Called when file chunk has finished uploading
                //log('[ChunkUploaded] File:', file, "Info:", info);
            },
 
            UploadComplete: function(up, files) {
                // Called when all files are either uploaded or failed
                up.splice();
                jQuery("#uploader_start").addClass("ui-state-disabled");
                //log('[UploadComplete]');
            },
 
            Destroy: function(up) {
                // Called when uploader is destroyed
                //log('[Destroy] ');
            },
  
            Error: function(up, args) {
                // Called when error occurs
                //log('[Error] ', args);
            }
        }
		
	});
				
	jQuery(".plupload_message_close").click(function(){
		jQuery(".plupload_message").remove();
	});
	
});	
</script>
					
<script type="text/javascript">
	
jQuery(".deltmsgclass_close").click(function(){
	jQuery(".deltmsgclass").html("");
	jQuery(".deltmsgclass").css("display","none");
	jQuery(".deltmsgclass_close").css("display","none");
});
	
function close_popup(popup_name)
{

	$("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
		$("#" + popup_name + "BackDiv").fadeOut(200, function () {
			$("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
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
	
jQuery("li#tab-type a").click(function() {
	
	//alert(jQuery(this).html());			
		
	jQuery("#sidemenu li a").each(function() {
		jQuery(this).removeClass("current");
	});
	
	jQuery(this).addClass("current");
	
	var cls = jQuery(this).parent().attr("class");
	
	jQuery(".tabs").each(function(){
		jQuery(this).css("display","none");
	});
	
	jQuery('#'+cls).css("display","block");
		
});

jQuery("input[type=radio][name=image_type]").click(function(){
	jQuery("#img_type").val($(this).val());	
});

jQuery('#selectimg>option').click(function(){
	 jQuery("#img_type").val($(this).val());
 }); 

function set_hidden_image_type(sel)
{	
	console.log(jQuery("#uploader_filelist li").length);
	if(jQuery("#selectimg").val()==0)
	{
		console.log("please select image type");
		jQuery("#uploader_start").addClass("ui-state-disabled");
	}
	else if(jQuery("#selectimg").val()>0 && jQuery("#uploader_filelist li").length>0)
	{
		jQuery("#uploader_start").removeClass("ui-state-disabled");
	}
}

jQuery('#px-submit').click(function(){
	var flag=0;
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'loginornot=true',
		async:true,
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
	if(flag==1)
	{
		return false;
	}
	else
	{
		return true;
	}
});


//jQuery("a[id^='mediadeleteid_']").click(function(){
jQuery(document).on( "click","a[id^='mediadeleteid_']",function(){
	open_popup("NotificationLoadingData");
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
				window.location.href=obj.link;
				flag=1;
			}
		}
	});
	
	if(flag == 1)
	{
		return false;
	}
	else
	{
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var cid= arr[1];
		jQuery.ajax({
		  type: "POST",
		  url: "<?=WEB_PATH?>/admin/process.php",
		  data: "id=" + cid +"&mediaactiondelete=yes&image_type=" + image_type +"&src="+ src +"",
		  async : false,
		  success: function(msg) {
			if(msg=="success")
			{
				// 14 10 2013
				var image_type=image_type_filter;
				var imgval=image_type;
				//jQuery("#upload_business_logo_ajax1").show();
				
				//jQuery(".msgclass").html("Image is deleted successfully");
				// 14 10 2013
				
				 // 08 01 2015 remove div when delete
				 
				 cur_div.parent().remove();
				 
				 // 08 01 2015 remove div when delete
				 jQuery(".deletemsgclass").html("");
			}
			else if(msg=="delete_denied")
			{
				jQuery(".deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_dont_delete_permission']; ?>");
			}
			else
			{
				jQuery(".deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_cant_delete_image'];   ?>");
			}
				   
			//alert(jQuery(".mediya_img_blk").length);
			
			if(jQuery(".mediya_img_blk").length==0)
			{
				jQuery("#mediya_image_container").append("<div class='mediya_img_blk mediya_upload_new' onclick='call_upload()' ><img src='<?php echo ASSETS_IMG  ?>/a/upload_img.png' /></div>")
			}
			
		  }
	   });
   }
   close_popup("NotificationLoadingData");
});


function call_upload()
{
	jQuery("#tab-type.div_upload a").trigger("click");
}

jQuery(".fltr_button").click(function(){
	
	var img_filter = jQuery(this).val();
	//alert(img_filter);
	
	jQuery("div.filters input").removeClass("fltr_selected");
	
	if(img_filter=="All Images")
	{
		jQuery(this).addClass("fltr_selected");
		jQuery(".mediya_img_blk.campaignfilter").css("display","block");
		jQuery(".mediya_img_blk.locationfilter").css("display","block");
	}
	else if(img_filter=="Campaigns")
	{
		jQuery(this).addClass("fltr_selected");
		jQuery(".mediya_img_blk.locationfilter").css("display","none");	
		jQuery(".mediya_img_blk.campaignfilter").css("display","block");
	}
	else if(img_filter=="Locations")
	{
		jQuery(this).addClass("fltr_selected");
		jQuery(".mediya_img_blk.campaignfilter").css("display","none");
		jQuery(".mediya_img_blk.locationfilter").css("display","block");	
	}
	//alert(jQuery(".mediya_img_blk:visible").length);
	if(jQuery(".mediya_img_blk:visible").length==0)
	{
		jQuery(".deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_no_images_with_filter']; ?>");
	}
	else
	{
		jQuery(".deletemsgclass").html("");	
	}
});

//jQuery("#show_more_mediya").click(function(){
jQuery(document).on( "click","#show_more_mediya",function(){
	open_popup("NotificationLoadingData");
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
				window.location.href=obj.link;
				flag=1;
			}
		}
	});
	
	if(flag == 1)
	{
		return false;
	}
	else
	{
		var img_filter = jQuery(".fltr_selected").val();
		//alert(img_filter);
		
		var cur_el= jQuery(this);
		var next_index = parseInt(jQuery(this).attr('next_index'));
		var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'show_more_media=yes&next_index='+next_index+'&num_of_records='+num_of_records,
			async:true,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);

				if (obj.status=="true")     
				{
					jQuery("#mediya_image_container").append(obj.html);
					cur_el.attr('next_index',next_index + num_of_records);
					if(parseInt(obj.records_return)<num_of_records)
					{
						cur_el.css("display","none");
					}
					
					if(img_filter=="All Images")
					{
						jQuery(".mediya_img_blk.campaignfilter").css("display","block");
						jQuery(".mediya_img_blk.locationfilter").css("display","block");
					}
					else if(img_filter=="Campaigns")
					{
						jQuery(".mediya_img_blk.locationfilter").css("display","none");	
						jQuery(".mediya_img_blk.campaignfilter").css("display","block");
					}
					else if(img_filter=="Locations")
					{
						jQuery(".mediya_img_blk.campaignfilter").css("display","none");
						jQuery(".mediya_img_blk.locationfilter").css("display","block");	
					}
		
				}
			}
		});
	}
	close_popup("NotificationLoadingData");	
});
 		
</script>
