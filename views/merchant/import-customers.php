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
</head>
<body>
	<div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div >

<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery-1.7.2.min.js"></script>-->
<!-- load from CDN-->
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->

        <script language="javascript" src="<?=ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
	
<!---start header---->
	<div><?require_once(MRCH_LAYOUT."/header.php");?>
		<!--end header--></div>
	<div id="contentContainer">	
   
	<div id="content">
  <div class="title_header">Add Customer List</div>
	<div id="backdashboard" align="right" >
    	<a id="dashboard" href="<?=WEB_PATH?>/merchant/manage-customers.php" ><img src="<?=ASSETS_IMG ?>/m/back_but.png" /></a>
    </div>
    <div class="disclaimer">Disclaimer: We'll automatically clean duplicate instances of email addresses from the list. Importing does not send any confirmation emails to your list, because we trust you've already received permission. Make sure everyone on your list has actually granted you permission to email them. Do not import any third party lists, prospects, lists that you scraped from websites, chambers of commerce lists, etc. </div>	
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
                        <div class="td_left">Distribution List Name :</div>
                        
                    
                        <div class="td_right"><input type="text" name="group_name" id="group_name" /></div>
			<span class="span_error_msg"></span>
                    </td>
                    </tr>
                
                        
                       <?php
                       /*
			if($_SESSION['merchant_info']['merchant_parent'] == 0){ ?>
			    <tr class="tr_top">
                    <td>
			<div class="td_left">Assign To Location :</div>
                        <div class="td_right">
                            
                            <select name="location_name" id="location_name" >
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
                            
                        
                        </div>
			 </td>
                </tr>
		    
			<?php
			}else
			{ 
                           ?>
			<div class="td_left">Assign To Location :</div>
			 <div class="td_right">
	<?php 
			   if($total_records>0){
					foreach($records_array as $RSStore)
					{
                        $sub_location_id = $RSStore->id;
						$location_string = $RSStore->address.", ".$RSStore->city.", ".$RSStore->state.", ".$RSStore->zip;
						 $location_string = (strlen($location_string) > 57) ? substr($location_string,0,57).'...' : $location_string;
													
                         echo $location_string; 
					}
				  } ?>
				  <input type="hidden" name="location_name" id="location_name" value="<?=$RSStore->id?>" />
			<?php }	  
			?>
			</div>
               <?php
               */
               ?>    
              <tr class="divide tr_top">
                <td>
						<div class="import_customer">Import new customer list (CSV File) :</div>
						<div  class="browse_btn" ><div id="upload" >
									<span  >Choose File
									</span> 
									</div>
						<span id="status" ></span>
                        <span id="uploadedfile"></span></div>
                      <div class="imprt_cust_tooltip"> <span class="notification_tooltip"  data-toggle="tooltip" data-placement="right" title="<?php  echo  $merchant_msg["import-customers"]["Tooltip_csv_file"]; ?>" >&nbsp;&nbsp;&nbsp</span></div>
              	</td>
               
              
                <td class="csv_download_div">
		<div class="csv_border">
                	<div class ="csv_text">Download standard csv file format:</div>
			<input type="submit" class="btn btn-success" name="download_csv" id="download_csv" value="Download" >
                	
                </div>
		</td>
		</tr>
             
              <tr class="tr_top">
                  <td>
                       <div class="tr_top">
                        <input type="submit" name="Import_user" id="Import_user" value="<?php echo $merchant_msg['index']['btn_save'];?>" />&nbsp;&nbsp;
                        
                        <input type="submit" name="btnCanDistributionlist" id="btnCanDistributionlist" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" />
                    </div> 
                  </td>
              </tr>
              </table>
              </td>
		</tr>
	</table>
                    <input type="hidden" name="hdn_csvfile" id="hdn_csvfile" value="" />
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
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.min.js"></script>

<!--- tooltip css --->
</html>

<script type="text/javascript">
    var file_path = "";
$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'edit-csv.php?doAction=FileUpload',
			name: 'uploadcsvfile',
			onSubmit: function(file, ext){
				if($('#files').children().length > 0)
				{
					$('#files li').detach();
				}
				 if (! (ext && /^(csv|xls|xlsx)$/.test(ext))){ 
                    // extension is not allowed 
                    jQuery("#hdn_csvfile").val('');
                    jQuery("#uploadedfile").text('');
					status.text('Only csv files are allowed.');
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
                                //alert(response);
                              
                                var arr = response.split("|");
				
                                    status.text('');
                                    //Add uploaded file to list
                                    file_path = arr[1];
                                    file_name  = arr[2];
                                    jQuery("#hdn_csvfile").val(file_path);
                                    jQuery("#uploadedfile").text(file_name);
                                    
                                
			}
		});
});
jQuery("#Import_user").click(function(){
     
     var alert_msg="<?php  echo  $merchant_msg["index"]["validating_data"]; ?>";
var head_msg="<div class='head_msg'>Message</div>";
var content_msg="<div class='content_msg validatingdata' style='background:white;'>"+alert_msg+"</div>";
jQuery("#NotificationloadermainContainer").html(content_msg);
jQuery("#NotificationloaderFrontDivProcessing").css("display","block");
jQuery("#NotificationloaderBackDiv").css("display","block");
jQuery("#NotificationloaderPopUpContainer").css("display","block");

        var t_val  = $("#group_name").val();
	var t_location_name=$("#location_name").val();
	var flag_login=0;
	var msg_box="";
        var file_msg = "";
        var flag2 = true;
        if(jQuery("#hdn_csvfile").val() == "")
        {
            file_msg = "<br/>* Please attach csv file of customer list.";
            var flag2 = false;
        }
        if(t_val != "")
            {
			$.ajax({
				type: "POST",
				url: "<?=WEB_PATH?>/merchant/process.php",
				data: "check_group_name_exist=yes&group_name=" + t_val + "&location_name=" + t_location_name+"&mer_id=<?php echo $_SESSION['merchant_id']; ?>",
                                async:false,
				success: function(msg) {
                                  //    alert(msg);
                                    if(msg  == 1)
									{
                                         flag= true;
                                         
                                    }
                                    else
                                    {
                                        //$(".span_error_msg").text(" *Distribution List already exist. Please select different name.");
					msg_box +=" Distribution List already exist. Please select different name.";
                                        flag = false;

                                    }
                                }
			});
                   }
                   else
                   {
                     //$(".span_error_msg").text(" Please Enter Distribution List Name");
		     msg_box +=" * Please Enter Distribution List Name";
                     flag = false;
                   }
                   /*
				   loc_id = $("#location_name").val();
				  $.ajax({
				type: "POST",
				url: "<?=WEB_PATH?>/merchant/process.php",
				data:"loc_id=" + loc_id +"&check_location_active=yes",
                async:false,
				success: function(msg) {
                                //    alert(msg);
                                   var obj=jQuery.parseJSON(msg);
                                        if(obj.status=="false")
										{
											  flag="false";
											  msg_box +="<div>* <?php echo $merchant_msg["manage-customers"]['Title_Manage_Customer_Distribution_List'] ?></div>";
											  flag = false;
										}

                                    
                                }
			});
			*/
		//	return false;
				   /// check whether location is deactivated before edit location
                   if(!flag2)
                   {
                      flag = false;
                   }
                   if(flag )
                   {
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
                         
                   }else{
                   close_popup("Notificationloader");
			        var head_msg="<div class='head_msg'>Message</div>";
				var content_msg="<div class='content_msg'>"+msg_box+file_msg+"</div>";
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


$("a#myaccount").css("background-color","orange");
jQuery(document).ready(function(){
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
</script>
