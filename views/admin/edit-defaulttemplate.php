<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$RS = $objDB->Show("categories");
$array_where['id'] = $_REQUEST['id'];
$RS_t = $objDB->Show("campaigns_template", $array_where);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/a/jquery.js"></script>
<script language="javascript" src="<?=ASSETS_JS ?>/a/ajaxupload.3.5.js" ></script>
<script type="text/javascript" src="<?=ASSETS ?>/tinymce/tiny_mce.js"></script>
<script>
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
});	
</script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/a/jquery.tooltip.css" />

<script src="<?=ASSETS_JS ?>/a/jquery.tooltip.js" type="text/javascript"></script>
<style>
    .displayimg {
    max-height: 70px;
    max-width: 90px;
}
#upload {
    background: none repeat scroll 0 0 #F2F2F2;
    border: 1px solid #CCCCCC;
    border-radius: 5px 5px 5px 5px;
    color: #3366CC;
    cursor: pointer !important;
    font-family: Arial,Helvetica,sans-serif;
    font-size: 1.1em;
    font-weight: bold;
    height: 15px;
    padding: 6px;
    text-align: center;
    width: 60px;
}
  h3 {
    color: black !important;
	font-size:14px !important;
    font-weight: lighter;
	background-color:hsl(0, 0%, 93%) !important;
	border:none !important;
}
#tooltip{
	z-index:9999 !important;
	opacity:1.0 !important;
}
</style>
</head>

<body>
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
	<h2>Edit Campaign template</h2>
                    <form action="process.php" method="post">
                          <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?=$RS_t->fields['business_logo']?>" />
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
		<table border="0" cellspacing="2" cellpadding="2">
		    <tr>
					<td width="20%" align="right">Template Title : </td>
					<td width="80%" align="left">
						<input type="text" name="title" id="title" value="<?=$RS_t->fields['title']?>" />
					</td>
				  </tr>
				  <!--
                   <tr>
					<td width="20%" align="right">Discount Rate : </td>
					<td width="80%" align="left">
						<input type="text" name="discount" id="discount" value="<?=$RS_t->fields['discount']?>" onkeyup="changetext2()" maxlength="8"/><span class="span_c2"  >Maximum 8 characters</span><span class="notification_tooltip"  title="Please enter Save $2, $2 OFF , 20% OFF etc. to highlight discount on your campaign for web and mobile devices.">&nbsp;&nbsp;&nbsp;</span>
					</td>
				  </tr>
                  -->                
				  <tr>
					<td align="right">Category: </td>
					<td align="left">
					<select name="category_id" id="category_id">
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
					<td align="right">Campaign Image : </td>
					<td align="left">
						<!-- start of  PAY-508-28033   -->
						<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
						<div style="float: left;">
									<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
									<div id="upload" >
									<span  >Browse
									</span> 
									</div>
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
				  <tr>
					<td align="right">Template Description <span class="notification_tooltip" title="Campaign description appear on the printed coupon. Please do not enter campaign title again in web editor.">&nbsp;&nbsp;&nbsp;</span>: </td>
					<td align="left">
					<div align="center" class="textarea_loader">
					<img   src="<?php echo ASSETS_IMG."/32" ?>.GIF" class="defaul_table_loader" />
					</div>
					<div class="textarea_container" style="display: none;">	
					<textarea id="description" name="description" rows="15" cols="80" style="width: 80%"><?=htmlentities($RS_t->fields['description'])?></textarea>
					</div><span id="desc_limit">Maximum 1200 characters</span>
                                        </td>
				  </tr>
				  
                                  <tr>
					<td align="right">Template Terms & Condition<span class="notification_tooltip" title="Please enter terms and condition for the campaign. Please do not type terms and conditions again in web editor.">&nbsp;&nbsp;&nbsp;</span> : </td>
					<td align="left">
					<textarea id="terms_condition" name="terms_condition" rows="15" cols="80" style="width: 80%"><?=htmlentities($RS_t->fields['terms_condition'])?></textarea>
					<span id="terms_limit">Maximum 1200 characters</span>
                                        </td>
				  </tr>
                   <!--               
                  <tr>
					<td align="right">Print Coupon Description <span class="notification_tooltip" title="Campaign description appear on the printed coupon. Please do not enter campaign title again in web editor.">&nbsp;&nbsp;&nbsp;</span>: </td>
					<td align="left">
					<textarea id="print_coupon_description" name="print_coupon_description" rows="10" cols="40" style="width: 80%"><?=htmlentities($RS_t->fields['print_coupon_description'])?></textarea>
					</td>
				  </tr>
                  -->                
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
                                             <script>function btncanTemplate(){                                                
                                                window.location="<?=WEB_PATH?>/merchant/templates.php";}
                                                function mycall()
												{
													jQuery('#description').val(tinyMCE.get('description').getContent());
                                                                                                        jQuery('#terms_condition').val(tinyMCE.get('terms_condition').getContent());
												}
                                                </script>
						<input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="bna" />
						<input type="submit" name="btneditTemplate" value="Save" onClick="mycall();">
                                               <input type="submit" name="btnCanceltemplate" value="Cancel" >                                                
					</td>
				  </tr>
		 
		</table>
	  </form>
	    <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>

    <script>
    $(document).ready(function() { 
    // bind form using ajaxForm 

   if($("#hdn_image_path").val() != ""){
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ $("#hdn_image_path").val() +"' class='displayimg'>";
	$('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/a/delete.gif' onclick='rm_image()' /></div></div></div>");
	}
	//changetext2();
});
    function changetext2(){
   var v = 8 - $("#discount").val().length;
   $(".span_c2").text(v+" characters remaining");
}
	var file_path = "";
$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		
		new AjaxUpload(btnUpload, {
			action: '<?php echo WEB_PATH;?>/admin/merchant_media_upload.php?doAction=FileUpload&img_type=template',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				if($('#files').children().length > 0)
				{
					$('#files li').detach();
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
	 var sel_val = $('input[name=use_image]:checked').val();
	 $("#hdn_image_id").val(sel_val);
	 var sel_src = $("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
	 //alert(sel_src);
	$("#hdn_image_path").val(sel_src);
	file_path ="";
	
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ sel_src +"' class='displayimg'>";
	$('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/a/delete.gif' onclick='rm_image()' /></div></div></div>");
}
function rm_image()
{
	$("#hdn_image_path").val("");
	$("#hdn_image_id").val("");
	$('#files').html("");
	
}
function save_from_computer()
{
	$("#hdn_image_path").val(file_path);
	$("#hdn_image_id").val("");
	
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ file_path +"' class='displayimg'>";
	$('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/a/delete.gif' onclick='rm_image()' /></div></div></div>");
}
 jQuery('.notification_tooltip').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
    });
    </script>
<?
$_SESSION['msg'] = "";
?>

</body>
</html>
