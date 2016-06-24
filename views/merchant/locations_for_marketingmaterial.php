<?php
/**
 * @uses locations for marketing materials
 * @used in pages :locations_for_marketingmaterial.php,manage-marketing-material.php,merchant-marketing-coupon.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where = array();
if($_SESSION['merchant_id'] != "")
{
    $array_where_user['id'] = $_SESSION['merchant_id'];
    $RS_User = $objDB->Show("merchant_user", $array_where_user);
    $m_parent = $RS_User->fields['merchant_parent'];
}
/********* on canceling page redirect to main marketing material page *********/
if(isset($_REQUEST['btn_downloadcancel']))
{
    header("Location:manage-marketing-material.php");
    exit;
}
/********** get all location of merchant **********/
if($RS_User->fields['merchant_parent'] == 0)
{
	$array_where['created_by'] = $_SESSION['merchant_id'];
	$order_by = " ORDER BY id DESC ";
	//$RS = $objDB->Show("locations", $array_where, $order_by);	
	
	$arr=file(WEB_PATH.'/merchant/process.php?btnGetAllLocationforgrid=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
		
}
else
{
	$media_acc_array = array(); 
	$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id']; 
	$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array); 
	$location_val = $RSmedia->fields['location_access'];
	//$l_str = implode(",",$location_val);
	
	//$sql = "SELECT * FROM locations  WHERE id in (select l.id from locations l where l.created_by=".$m_parent ." and l.id = ".$location_val .")";
	//$RS = $objDB->execute_query($sql);
	$arr=file(WEB_PATH.'/merchant/process.php?btnGetAllLocationforgrid=yes&mer_id='.$_SESSION['merchant_id'].'&loc_id='.$location_val);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
}
if($_SESSION['merchant_id'] != "")
{
    /* $Sql = "SELECT * FROM locations l WHERE  l.active=1 and l.created_by=".$_SESSION['merchant_id'];  
     $records_array = $objDB->execute_query($Sql);*/
	$records_array = $objDB->Conn->Execute("SELECT * FROM locations l WHERE  l.active=1 and l.created_by=?",array($_SESSION['merchant_id']));

     $total_records = $records_array->RecordCount();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Manage Marketing Material</title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="dialog-message" title="Message" style="display:none">
</div>
<div>
    <!-- <script src="<?=ASSETS_JS ?>/m/jquery-1.4.1.min.js"></script>-->
     <!-- load from CDN-->
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->

      <link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.tooltip.css" />

<script src="<?=ASSETS_JS ?>/m/jquery.tooltip.js" type="text/javascript"></script>

<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/m/jquery.dataTables.js"></script>
	<style type="text/css" title="currentStyle">
			@import "<?php echo ASSETS_CSS ?>/m/demo_page.css";
			@import "<?php echo ASSETS_CSS ?>/m/demo_table.css";
		</style>	
               
<!---start header---->
	<div>
		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	
  
	<div id="content">
		<h1>Manage Marketing Material</h1>
                <div id="backdashboard" s>
                    <a id="dashboard" href="<?=WEB_PATH?>/merchant/manage-marketing-material.php"><img src="<?=ASSETS_IMG ?>/m/back_but.png" /></a>
                </div>

<div align="center" >
    <div  ></div>
    <form>
	<div align="center">
	<img   src="<?php echo ASSETS_IMG ?>/32" ?>.GIF" class="table_loader defaul_table_loader" />
	</div>
	<div class="datatable_container" style="display: none;">
	<table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant"  id="example1" >
		<thead>
	  
	  <tr>
              <th align="right" class="tableDealTh table_th_2">&nbsp;</th>
		
		<th align="left" class="tableDealTh table_th_45">Location Address</th>
		
		<th align="left" class="tableDealTh table_th_25">QR Code Status</th>
		

		
	  </tr>
	  </thead>
		<tbody>
	   <?
          
	  if($total_records>0){
	  	while($Row = $records_array->FetchRow())
		{
                    $css ="";
                   // echo $Row['active'];
                    if($Row['active'] != 1)
                    {
                        $css="background-color:#E1E1E1";
                    }
                    
					/*
                   $wh_arr = array();
                   $wh_arr['location_id'] = $Row['id'];
                   $rs_qrcode_campaign = $objDB->Show("qrcode_location",$wh_arr) ;
                   */
                   $rs_qrcode_campaign = $objDB->Conn->Execute("select qrcode_id from locations where id =? and qrcode_id!=0",array($lid));
                   
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
                    ?>
	  <tr class="tableDeal" >
		 <td><input type="radio"  visit="first" id="rd_campaigntitle_<?php echo $Row['id']; ?>" name="rd_locationtitle" value="<?php echo $Row['id']; ?>" /></td>
		<!--<td><a href="javascript:void(0)"  id="showCamp_<?=$Row['id']?>"><?=$Row['location_name']?></a></td> -->
		<td class="capitalize_heading"><a href="javascript:void(0)" id="showCamp_<?=$Row['id']?>"> <?=$Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip']?></a></td>
		<td>
                     <a href="javascript:void(0)" locid="<?=$Row['id']?>" qval="<?php echo $qrcodestring; ?>" class="<?php echo $downloadclass ?>" ><?php echo  $linktext; ?></a>
                </td>

                
                
	  </tr>
	  <?
	  	}
	  }else{
	  ?>
	
	  <?
	  }
	  ?>
	  </tbody>
	</table>
	</div>
</div>
 <div class="cls_clear"></div>
                                <div align="center" class="marign-top-20">
                    <input type="button" value="Download QR Code" name="btn_downloadqrcode" id="btn_downloadqrcode" />&nbsp;&nbsp;
                    <input type="submit" value="Cancel" name="btn_downloadcancel" id="btn_downloadcancel" />&nbsp;&nbsp;
                </div>
	<div class="clear">&nbsp;</div>
<!--end of content--></div>
<!--end of contentContainer--></div>
</form>
<!---------start footer--------------->
       <div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>

								
 
        <input  type="hidden" value="" name="hdn_qrcodedn_location_id" id="hdn_qrcodedn_location_id" />
        <input  type="hidden" value="" name="hdn_qrcode" id="hdn_qrcode" />
        <input type="hidden" value="" name="hdn_dettach_qrcode_location_id" id="hdn_dettach_qrcode_location_id" />
<input  type="hidden" value="" name="hdn_id_download_or_print" id="hdn_id_download_or_print" />
  <div style="display:none"> 
  <div class="QRcode_detail_div" >
  </div>
<div class="campaign_detail_div" >
</div>								
 <div class="locationlist_div" >
 </div>
 </div>
								
		<!--end of footer--></div>
<?

$_SESSION['msg'] = "";
?>
</body>
</html>
	
<?php
$confirmationstring  ='';
$confirmationstring  .='<div>';
         $confirmationstring  .='<div align="center" class="marign-left-20 marign-right-20">';
             $confirmationstring  .='Do you want to Un-link QR code ?';
         $confirmationstring  .='</div>';
         $confirmationstring  .='<div align="center" class="marign-top-20">';
             $confirmationstring  .='<input type="submit" name="btn_no" id="btn_no" value="NO" > &nbsp;&nbsp; ';
             $confirmationstring  .='<input type="submit" name="btn_yes" id="btn_yes" value="Yes" >';
             
         $confirmationstring  .='</div>';
         
     $confirmationstring  .='</div>';

     $sizeoptions ='';
     $sizeoptions .='<div class="marign-left-20">';
      $sizeoptions .='<span>Select Size : </span><select name="opt_qrcodesize" id="opt_qrcodesize">';
      $sizeoptions .='<option value="80"> 80 X 80 </option>';
      $sizeoptions .='<option value="120"> 120 X 120 </option>';
      $sizeoptions .='<option value="200"> 200 X 200 </option>';
      $sizeoptions .='<option value="275"> 275 X 275 </option>';
      $sizeoptions .='option value="350"> 350 X 350 </option>';
      $sizeoptions .='</select>';
      $sizeoptions .='<br/><br /> ';
      $sizeoptions .='<span>Select QR Code format : </span><select name="opt_qrcodetype" id="opt_qrcodetype">';
      $sizeoptions .='<option value="1"> pdf  </option>';
      $sizeoptions .='<option value="2"> jpeg  </option>';
      $sizeoptions .='<option value="3"> Vector  </option>';
      $sizeoptions .='</select>';
      $sizeoptions .='<br/><br /><br /> ';
      $sizeoptions .='<input type="submit" name="btndownloadqrcode" id="btndownloadqrcode" value="Downlaod QR Code" >';
      $sizeoptions .='&nbsp;&nbsp;&nbsp;<input type="submit" name="btncancel" id="btncancel" value="Cancel" ></div>';
?>
<script>

jQuery("#btnattachqrcode").live("click",function(){
      // alert("In attchment code");
               
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
										jQuery.fancybox.close(); 
                                        var obj = jQuery.parseJSON(msg);
                                         //alert("QR Code Is Successfully Linked With location");
                                    /*      var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'> QR code is successfully linked with location</div>";
					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                          ele1.text("Un-link");
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
                    var head_msg="<div class='head_msg'>Unlink Qr Code</div>"
					var content_msg='<div class="content_msg"><?php echo $confirmationstring; ?></div>';
					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
     var head_msg="<div  class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'> Please select QR code.</div>";
					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
});
  function bind_event(){
        
}
  jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
    function bind_confirmation_event()
    {
         jQuery("#btn_no").live("click",function(){
       //close_popup("qrcode");
       jQuery.fancybox.close(); 
    });
    jQuery("#btn_yes").live("click",function(){
         var val = jQuery("#hdn_dettach_qrcode_location_id").val();
       
        jQuery(".rdownloadqrcode").each(function(){
            if(jQuery(this).attr("locid") ==  jQuery("#hdn_dettach_qrcode_location_id").val())
            {
                    ele1 = jQuery(this);
             }
        });
        //alert(val)
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
                                         // alert("QR Code Is Successfully Unlinked From location");
                                          /*var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'> QR code is unlinked successfully from location</div>";
					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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

                                          //        open_popup('qrcode');
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
											  var head_msg="<div class='head_msg' >Message</div>"
										  
										  }
										  else
										  {
										  var head_msg="<div class='head_msg' >Link QR Code</div>"
										  }
					var content_msg="<div class='content_msg'>"+ msg +"</div>";
					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                     close_popup('qrcode');
                                    }
                                    });
    });
    }
   function close()
    {
       
        jQuery("#btncancel").live("click",function(){
        jQuery.fancybox.close(); 
         //   close_popup('qrcode');
        return false;
        });
        
    }
    function download_function()
    {
     
     
     
    jQuery("#btn_cancel").click(function(){
    close_popup("confirmation") ;
 });
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
										  var head_msg="<div class='head_msg'>Link QR Code</div>"
										  }
										  var content_msg="<div class='content_msg'>"+ msg +"</div>";
					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                                               // open_popup('qrcode');
                                   
                                     
                                          jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                                            var head_msg="<div class='head_msg' >Unlink QR Code</div>"
					var content_msg='<div class="content_msg"><?php echo $confirmationstring; ?></div>';
					var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
 
                                         // bind_event1();
                                        //  close();
                                          //$("#jqReviewHelpfulMessageNotification").html(msg);
                                     


        
    });
 
 jQuery('#btn_downloadqrcode').click(function(){
   
 jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'loginornot=true',
                          async:false,
                           success:function(msg)
                           { 
                               
                            // alert(msg);
                                 var obj = jQuery.parseJSON(msg);
                               
                                 
                                if (obj.status=="false")     
                                {
                                   
                                    window.location.href=obj.link;
                                    
                                }
                                else
                                    { 
                                        
                                    
									//alert("In else");
                                        //return false;
                                                            var selectedVal = "";
                                                            var selected = jQuery("input[type='radio'][name='rd_locationtitle']:checked");

                                                            if (selected.length > 0)
                                                                {
                                                                            selectedValue = selected.val();
                                                                            jQuery("#hdn_qrcodedn_location_id").val(selectedValue);

                                                                           if(jQuery.trim(jQuery("a[locid='"+selectedValue+"']").attr("class"))=="rdownloadqrcode")
                                                                               {
                                                                                     jQuery(".locationlist_div").html('<?php echo $sizeoptions; ?>');
                                                                                                    var head_msg="<div class='head_msg' >Download QR Code</div>"
                                                                                                    var content_msg='<div class="content_msg"><?php echo $sizeoptions; ?></div>';
                                                                                                    var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                                                                    jQuery( "#dialog-message" ).html(head_msg + content_msg );//+footer_msg);
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
                                                                                                          jQuery("#btncancel").live("click",function(){
                                                                                                                jQuery.fancybox.close(); 
                                                                                                                //close_popup('confirmation');
                                                                                                            return false;
                                                                                                            });
                                                                                                     download_function();
                                                                               }
                                                                               else{
                                                                                 //  alert("Please link QR Code to location");
                                                                                  var head_msg="<div class='head_msg'>Message</div>"
                                                                                                    var content_msg="<div class='content_msg'> Please link QR code to campaign.</div>";
                                                                                                    var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                                                          //  if()

                                                                }
                                                                else{

                                                                    jQuery.ajax({
                                                                                                  type: "POST",
                                                                                                  url: "<?=WEB_PATH?>/merchant/process.php",
                                                                                                  data: "attach_message_location=yes",
                                                                                                  async : false,
                                                                                                  success: function(msg) {

                                                                                                   jQuery(".locationlist_div").html(msg);
                                                                                                          //open_popup('confirmation');
                                                                                                    var head_msg="<div class='head_msg' >Message</div>"
                                                                                                    var content_msg="<div class='content_msg'> Please select location.</div>";
                                                                                                    var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                                                                                     download_function();
                                                                                                  }
                                                                    });
                                                                     
                                                                }



                                                            return false;
                                        
                                   }
                                
                                
                          }
                           
                         }); 
       
   
      
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
 jQuery("#btndownloadqrcode").live("click",function(){
        
        
       var sp_size =  jQuery(".fancybox-inner #opt_qrcodesize").val();
          var sp_type = jQuery(".fancybox-inner #opt_qrcodetype").val();
          
        
var qrcodeid="<?php if(isset($qrcodestring)) echo $qrcodestring;?>";


       // window.location.href="<?php echo WEB_PATH?>/merchant/demopdf/demo_qrcode.php?id="+jQuery("#hdn_qrcodedn_location_id").val()+"&size="+sp_size+"&is_location=1";
         if(sp_type == 1)
        {
            window.location.href="<?php echo WEB_PATH?>/merchant/demopdf/demo_qrcode.php?id="+jQuery("#hdn_qrcodedn_location_id").val()+"&size="+sp_size+"&is_location=1&qrcodeid="+qrcodeid;
        }
        else if(sp_type == 2){
            jQuery.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "get_campaign_qrcode_url=yes&id="+jQuery("#hdn_qrcodedn_location_id").val()+"&is_location=1",
                                      async : false,
                                      success: function(msg) {
                                           var obj = jQuery.parseJSON(msg);
										   //is_location=1&
										   window.location.href="<?php echo WEB_PATH?>/merchant/demopdf/demo_qrcode_jpg.php?id="+jQuery("#hdn_qrcodedn_location_id").val()+"&size="+sp_size+"&is_location=1&qrcodeid="+qrcodeid; 
                                         // window.location.href= '<?php echo WEB_PATH."/QR-Generator-PHP-master/php/qr.php?t=j&d="; ?>'+obj.qrcode_url+"&size="+sp_size+'&download=qrcode&qrcodeid='+qrcodeid;
                                      }
                });
        }
        else{
            //document.execCommand('SaveAs',true,'camp_image1.jpg');
		
            jQuery.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "get_campaign_qrcode_url=yes&id="+jQuery("#hdn_qrcodedn_location_id").val()+"&is_location=1",
                                      async : false,
                                      success: function(msg) {
									  	
                                               var obj = jQuery.parseJSON(msg);
											//   alert( '<?php echo WEB_PATH."/merchant/download_qrcode.php?imgage_path="; ?>'+obj.qrcode_url+"&qrcodeid="+qrcodeid+"&size="+sp_size);
                                               //alert('<?php echo WEB_PATH."/merchant/download_qrcode.php?imgage_path="; ?>'+obj.qrcode_url+"&size="+sp_size+"&qrcodeid="+qrcodeid);
                                             window.location.href= '<?php echo WEB_PATH."/merchant/download_qrcode.php?imgage_path="; ?>'+obj.qrcode_url+"&qrcodeid="+obj.qrcode_string+"&size="+sp_size;
                                    }
             });
        }
         
        close();
         close_popup('confirmation');
         jQuery.fancybox.close(); 
    });
</script>
<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function() {
				jQuery('#example1').dataTable( {
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					  "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                                        "iDisplayLength" : 5,
					"aoColumnDefs":[{"bSortable":false , "aTargets":[0,2 ]}]
				} );
				jQuery(".table_loader").css("display","none");
				jQuery(".datatable_container").css("display","block");
				
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
			} );
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
