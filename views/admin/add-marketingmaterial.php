<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$RS = $objDB->Show("categories");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/a/jquery.js"></script>
<script type="text/javascript" src="<?=ASSETS ?>/tinymce/tiny_mce.js"></script>
<script language="javascript" src="<?=ASSETS_JS ?>/a/ajaxupload.3.5.js" ></script>
<script>
  
tinyMCE.init({
		// General options
		//mode : "textareas",
		mode : "exact",
		elements:"material_format",
		theme : "advanced",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
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
		}
	});

</script>
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
</style>
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
                    <h2>Add Marketing Material</h2>
                    <div>
                        <span class="span_error_msg"  style="color:#FF0000; "></span>
                    </div>
	<form action="process.php" method="post">
              <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="" />
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
		<table border="0" cellspacing="2" cellpadding="2" >
		   <tr>
					<td width="40%" align="right">Material Name : </td>
					<td width="60%" align="left">
						<input type="text" name="name" id="name" />
					</td>
				  </tr>
				 
                   <tr>
					<td width="40%" align="right">Document Size : </td>
					<td width="60%" align="left">
                                            <select name="document_size" id="document_size" >
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
					<td align="right">Material Format </td>
					<td align="left">
					<textarea id="material_format" name="material_format" rows="15" cols="80" style="width: 80%"></textarea>
                    <!--<input type="hidden" name="hdndescription" id="hdndescription" value="" />-->
					</td>
				  </tr>
                 
				  
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<input type="submit" name="btnAddmarketingmaterial" value="Save" >
						<!--// 369-->
						<input type="submit" name="btnCancelmarketingmaterial"  value="Cancel" >
                        <!--// 369-->
					</td>
				  </tr>
		 
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<script>
    function changetext2(){
   var v = 8 - $("#discount").val().length;
   $(".span_c2").text(v+" characters remaining");
}
var file_path = "";
$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		
		new AjaxUpload(btnUpload, {
			action: '<?php echo WEB_PATH;?>/merchant/edit.php?doAction=FileUpload&img_type=template',
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
	
		
		$("#hdn_image_id").val(sel_val);
		var sel_src = $("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
		//alert(sel_src);
	       $("#hdn_image_path").val(sel_src);
	       file_path = "";
	  
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

</script>

<?
$_SESSION['msg'] = "";
?>

</body>
</html>
