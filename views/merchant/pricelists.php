<?php 
//echo realpath("json");
check_merchant_session();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ScanFlip | Manage Price lists</title>
	<?php require_once(MRCH_LAYOUT."/head.php"); ?>
	<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/pricelist.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery-ui.css">

	<!--<script src="<?php echo ASSETS ?>/pricelist/js/jquery.js"></script>-->
	<script src="<?php echo ASSETS ?>/pricelist/js/bootstrap.min.js"></script>
	<script src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
	<!--<script src="<?php echo ASSETS_JS ?>/m/jquery.dataTables.js"></script>-->
	<script src="<?php echo ASSETS ?>/pricelist/js/jquery-ui.js"></script>
	<!--<script src="js/index.js"></script>-->
	
	
  </head>
<body>
	<!---------start header--------------->	
	
	<div>
	<?
	// include header file from merchant/template directory 
	require_once(MRCH_LAYOUT."/header.php");
	?>
	<!--end header-->
	</div>
	<div id="contentContainer">
		
	<div id="content">	
	<h4 id="header-title">Manage Price lists</h4>
	<div class="pricelist_main_container">
    	
		<button type="button" class="btn btn-primary" id="add_pricelist">New Price list</button>
		<button type="button" class="btn btn-primary" id="assign_pricelist">Assign Price list</button>
		<button type="button" class="btn btn-primary" id="unassign_pricelist">Unassign Price list</button>
		<a type="button" id="reorder" href="reorder.php" class="btn btn-primary">Reorder</a>
		 <div class="error_messages">
	         <div class="alert alert-warning alert_pricelist_name">Please enter a price list name</div>
			<div class="alert alert-warning alert_html" style="">No HTML allowed</div>
			<div class="alert alert-warning alert_length" style="">Maximum Length should be 50 character</div>
		</div>
		<div id="pricelist_form" style="display:none;"  >
		 	<form method="post" action="" id="new_pricelist">
		 		<p class="form_label">Price list name</p>
		 		<input type="text" name="pricelist_val" id="pricelist_name" class="form-control">
		 		 <p class="hint"><span data-max='50'>50</span> characters remaining | No HTML allowed</p>
		 		<select name="" id="pricelist_type" class="form-control" style="width:200px;">
		 			<option value="none">Select price list type</option>
		 			<option value="product">Product</option>
		 			<option value="service">Service</option>
		 		</select>
		 		<br>
		 		<button type="button" class="btn btn-primary" id="submit_pricelist_form">Create</button>
		 		<button type="button" class="btn btn-primary" id="close_pricelist_form">Cancel</button>
		 	</form>
		 </div>
		
		<div style="display:none;" class="pricelist_filter">
          <b>Filter by Price list Type : </b>
          <a id="all" class="filter active-filter" href="javascript:void(0)">All </a> | 
          <a id="product" class="filter" href="javascript:void(0)"> Product </a> | 
          <a id="service" class="filter" href="javascript:void(0)"> Service </a>
        </div>
		<table class="table display" id="pricelist_table">
			<thead>
				<tr>
					<th></th>
					<th>Price list Name</th>
					<th>Last Updated</th>
					<th>Display Order</th>
					<th>Publish Status</th>
					<th>Price list Type</th>

					<th>Actions</th>
			</tr>
			</thead>
			
		</table>
	</div>
	<div id="select_pricelist_type" style="display:none">
		<p>Please select price list type</p>
		 <button class="btn btn-primary" type="button">OK</button>
	</div>
	<div id="notification">
		Sorry you cannot delete published item.
	</div>
	</div><!-- end of content-->
	
	</div><!-- end of contentContainer-->
	
	<!---------start footer--------------->
    <div>
	<?
		require_once(MRCH_LAYOUT."/footer.php");
	?>
	<!--end of footer-->
	</div>	
	<div id="dialog-message" style="display:none;">
     </div>
	 <div class="assign_div" style="display:none;">
     </div>
     <input type="hidden" name="selected_pl" id="selected_pl"/>
  
   </body>
</html>
<script type="text/javascript">
	$(document).ready(function(){
	
var html_regex=/[<>?]/;
  $('body').on('click','#add_pricelist',function(){
    $('#pricelist_form').show(500);  
    $('#pricelist_name').focus()
  })
  $('body').on('click','.fancybox-inner #assign_pricelist_location',function(){
	 
	    var checkedValues = $('.fancybox-inner input:checkbox:checked').map(function() {
		return this.value;
		}).get();
		console.log(checkedValues);
		var selected_locations = checkedValues.toString();
		var checkedValues_count = $('.fancybox-inner input:checkbox:checked').length;
		console.log(checkedValues_count);
		if(checkedValues_count==0)
		{
			jQuery(".fancybox-inner #pl_l_error").css("display","block");
			return false;
		}
		else
		{
			jQuery(".fancybox-inner #pl_l_error").css("display","none");
		
			var selected_pricelist = $("#selected_pl").val();
			console.log("Pricelist = " + selected_pricelist);
			console.log("Location = " + selected_locations);
			$.ajax({
				type: "POST",
				url: "<?= WEB_PATH ?>/merchant/process.php",
				data: "btnAssignPricelistToLocation=yes&pricelist_ids="+selected_pricelist+"&location_ids="+selected_locations,
				async : false,
				success: function(msg) {
					var obj = jQuery.parseJSON(msg);    
					if(obj.status=="true")
					{                  
						var head_msg="<div class='head_msg' >Message</div>";
						var content_msg='<div class="content_msg">Price list assigned to location sucessfully</div>';
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
						return false;
					}
					//jQuery('#pricelist_table input[id^="chk_"]').prop('checked', false);
				}
			});
		}
	})
	
	 $('body').on('click','.fancybox-inner #unassign_pricelist_location',function(){
		 
	    var checkedValues = $('.fancybox-inner input:checkbox:checked').map(function() {
		return this.value;
		}).get();
		console.log(checkedValues);
		var selected_locations = checkedValues.toString();
		var checkedValues_count = $('.fancybox-inner input:checkbox:checked').length;
		console.log(checkedValues_count);
		if(checkedValues_count==0)
		{
			jQuery(".fancybox-inner #pl_l_error").css("display","block");
			return false;
		}
		else
		{
			jQuery(".fancybox-inner #pl_l_error").css("display","none");
		
			var selected_pricelist = $("#selected_pl").val();
			console.log("Pricelist = " + selected_pricelist);
			console.log("Location = " + selected_locations);
			$.ajax({
				type: "POST",
				url: "<?= WEB_PATH ?>/merchant/process.php",
				data: "btnUnssignPricelistFromLocation=yes&pricelist_ids="+selected_pricelist+"&location_ids="+selected_locations,
				async : false,
				success: function(msg) {
					var obj = jQuery.parseJSON(msg);    
					if(obj.status=="true")
					{                  
						var head_msg="<div class='head_msg' >Message</div>";
						var content_msg='<div class="content_msg">Price list unassigned from location sucessfully</div>';
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
						return false;
					}
					//jQuery('#pricelist_table input[id^="chk_"]').prop('checked', false);
				}
			});
		}
	})
	
   $('body').on('click','.fancybox-inner #assign_pricelist_cancel',function(){
	   $.fancybox.close();
	})
	 $('body').on('click','.fancybox-inner #unassign_pricelist_cancel',function(){
	   $.fancybox.close();
	})
	
  jQuery(".fancybox-inner #popupcancel").live("click",function(){
		jQuery.fancybox.close(); 
		jQuery('#pricelist_table input[id^="chk_"]').prop('checked', false);
		//close_popup('confirmation');
		return false;
  });
			
  $('body').on('click','#assign_pricelist',function(){
	   
    var checkedValues = $('body input:checkbox:checked').map(function() {
    return this.value;
	}).get();
	console.log(checkedValues);
	var checkedValues_count = $('body input:checkbox:checked').length;
	console.log(checkedValues_count);
	//alert(checkedValues_count);
	if(checkedValues_count==0)
	{
		var head_msg="<div class='head_msg' >Message</div>";
		var content_msg='<div class="content_msg">Please select at least one price list</div>';
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
		return false;
	}
                                                                                                            
	$("#selected_pl").val(checkedValues.toString());
	
	$.ajax({
		type: "POST",
		url: "<?= WEB_PATH ?>/merchant/process.php",
		data: "btnGetLocationListForPricelistAssigned=yes&pid="+$("#selected_pl").val(),
		async : false,
		success: function(msg) {
			var obj = jQuery.parseJSON(msg);                      
			//$(".assign_div").html(obj.html);
			jQuery.fancybox({									  
				content:obj.html,
				type: 'html',                                               
				//autoDimensions: false,
				//autoSize: false,
				//fitToView:false,
				width:800,	
				maxWidth: 790,
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
	});
	
  })
  
  $('body').on('click','#unassign_pricelist',function(){
	  
    var checkedValues = $('body input:checkbox:checked').map(function() {
    return this.value;
	}).get();
	console.log(checkedValues);
	var checkedValues_count = $('body input:checkbox:checked').length;
	console.log(checkedValues_count);
	//alert(checkedValues_count);
	if(checkedValues_count==0)
	{
		var head_msg="<div class='head_msg' >Message</div>";
		var content_msg='<div class="content_msg">Please select at least one price list</div>';
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
		return false;
	}
                                                                                                            
	$("#selected_pl").val(checkedValues.toString());
	
	$.ajax({
		type: "POST",
		url: "<?= WEB_PATH ?>/merchant/process.php",
		data: "btnGetLocationListForPricelistUnassigned=yes&pid="+$("#selected_pl").val(),
		async : false,
		success: function(msg) {
			var obj = jQuery.parseJSON(msg);                      
			//$(".assign_div").html(obj.html);
			jQuery.fancybox({									  
				content:obj.html,
				type: 'html',                                               
				//autoDimensions: false,
				//autoSize: false,
				//fitToView:false,
				width:800,	
				maxWidth: 790,
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
	});
	
  })
  
  var oCache = {
                    iCacheLower: -1
                };

                function fnSetKey(aoData, sKey, mValue)
                {
                    for (var i = 0, iLen = aoData.length; i < iLen; i++)
                    {
                        if (aoData[i].name == sKey)
                        {
                            aoData[i].value = mValue;
                        }
                    }
                }

                function fnGetKey(aoData, sKey)
                {
                    for (var i = 0, iLen = aoData.length; i < iLen; i++)
                    {
                        if (aoData[i].name == sKey)
                        {
                            return aoData[i].value;
                        }
                    }
                    return null;
                }

                function fnDataTablesPipeline(sSource, aoData, fnCallback) {
                    var iPipe = 2; /* Ajust the pipe size */

                    var bNeedServer = false;
                    var sEcho = fnGetKey(aoData, "sEcho");
                    var iRequestStart = fnGetKey(aoData, "iDisplayStart");
                    var iRequestLength = fnGetKey(aoData, "iDisplayLength");
                    var iRequestEnd = iRequestStart + iRequestLength;
                    oCache.iDisplayStart = iRequestStart;

                    /* outside pipeline? */
                    if (oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper)
                    {
                        bNeedServer = true;
                    }

                    /* sorting etc changed? */
                    if (oCache.lastRequest && !bNeedServer)
                    {
                        for (var i = 0, iLen = aoData.length; i < iLen; i++)
                        {
                            if (aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho")
                            {
                                if (aoData[i].value != oCache.lastRequest[i].value)
                                {
                                    bNeedServer = true;
                                    break;
                                }
                            }
                        }
                    }

                    /* Store the request for checking next time around */
                    oCache.lastRequest = aoData.slice();

                    if (bNeedServer)
                    {
                        if (iRequestStart < oCache.iCacheLower)
                        {
                            iRequestStart = iRequestStart - (iRequestLength * (iPipe - 1));
                            if (iRequestStart < 0)
                            {
                                iRequestStart = 0;
                            }
                        }

                        oCache.iCacheLower = iRequestStart;
                        oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
                        oCache.iDisplayLength = fnGetKey(aoData, "iDisplayLength");
                        fnSetKey(aoData, "iDisplayStart", iRequestStart);
                        fnSetKey(aoData, "iDisplayLength", iRequestLength * iPipe);

                        $.getJSON(sSource, aoData, function (json) {
                            /* Callback processing */
                            oCache.lastJson = jQuery.extend(true, {}, json);

                            if (oCache.iCacheLower != oCache.iDisplayStart)
                            {
                                json.aaData.splice(0, oCache.iDisplayStart - oCache.iCacheLower);
                            }
                            json.aaData.splice(oCache.iDisplayLength, json.aaData.length);

                            fnCallback(json)
                        });
                    }
                    else
                    {
                        json = jQuery.extend(true, {}, oCache.lastJson);
                        json.sEcho = sEcho; /* Update the echo for each response */
                        json.aaData.splice(0, iRequestStart - oCache.iCacheLower);
                        json.aaData.splice(iRequestLength, json.aaData.length);
                        fnCallback(json);
                        return;
                    }
                }
                
  //data table function on pricelist index page table
	var oTable = $('#pricelist_table').dataTable( {
		//"bStateSave": true,
       "bFilter": false,
		"bSort" : false,
		"bLengthChange": false,
		"info": false,
		"iDisplayLength": 10,
		"bProcessing": true,
         "bServerSide": true,
		"oLanguage": {
						"sEmptyTable": "No price list founds in the system. Please add at least one.",
						"sZeroRecords": "No price list to display",
                        "sProcessing": "Loading..."
                    },
         "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",            
         "fnServerParams": function (aoData) {
                        aoData.push({"name": "btnGetAllPricelist", "value": true} )
						//bind_more_action_click();
                    },
         //"fnServerData": fnDataTablesPipeline,     
         
         "aoColumns": [
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false, "sClass": "actiontd"},
                    ]      
                    
    } );
    
     //$('#pricelist_table').dataTable().fnUpdate("aaa",1,1);
      $('body').on('click','.publish_pricelist_row',function(){
        //$(this).closest('td').find('.publish_form').submit();
        var pricelistid = jQuery(this).attr("id");
        var publish = jQuery(this).attr("publish");
        jQuery.ajax({
            type: "POST",
            url: "<?= WEB_PATH ?>/merchant/process.php",
            data: "pricelist_id=" + pricelistid +"&publish="+publish+"&publish_pricelist=yes",
            async : false,
            success: function(msg) {
               var obj = jQuery.parseJSON(msg); 
               //alert(obj.status);
               
				//jQuery('#pricelist_table').dataTable().fnDestroy();
				//jQuery("#pricelist_table").html(obj.html);
               try
               {
				   //alert("hi");
				   //var oTable = $('#pricelist_table').dataTable();
				oTable.fnDraw();
				}
				catch(e)
				{
					alert(e.message);
				}
            }
        });
        
    })

    //delete button event on pricelist table
    $('body').on('click','.delete_pricelist_row',function(){
        if($(this).attr('data-disabled')=="true"){
            $('#notification').fadeIn(500);
            setTimeout(function(){$('#notification').fadeOut(500); }, 3000);
        }
        else
        {
			/*
    	   //$(this).closest('td').find('.delete_form').submit(); 
    	   
    	   var pricelistid = jQuery(this).attr("id");
			jQuery.ajax({
				type: "POST",
				url: "<?= WEB_PATH ?>/merchant/process.php",
				data: "pricelist_id=" + pricelistid +"&delete_pricelist=yes",
				async : false,
				success: function(msg) {
				   //var obj = jQuery.parseJSON(msg); 
				   //alert(obj.status);
				   
					//jQuery('#pricelist_table').dataTable().fnDestroy();
					//jQuery("#pricelist_table").html(obj.html);
				   try
				   {
					   //alert("hi");
					   //var oTable = $('#pricelist_table').dataTable();
					oTable.fnDraw();
					}
					catch(e)
					{
						//alert(e.message);
					}
				}
			});
			*/
			var pricelistid = jQuery(this).attr("id");
			var linkurl = "process.php?pricelist_id=" + pricelistid +"&delete_pricelist=yes";
			var msg = "<div style='width: 270px;'>Are you sure you want to delete price list ?</div>";
			var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
			var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
			var footer_msg = "<div style='text-align:center'><hr><input type='button' link='<?php echo WEB_PATH ?>/merchant/" + linkurl + "' value='Yes' id='popupyes' name='popupyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'>&nbsp;&nbsp;&nbsp;<input type='button'  value='No' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
			jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

			jQuery.fancybox({
				content: jQuery('#dialog-message').html(),
				type: 'html',
				openSpeed: 300,
				closeSpeed: 300,
				// topRatio: 0,

				changeFade: 'fast',
				beforeShow: function () {
					$(".fancybox-inner").addClass("msgClass");
				},
				helpers: {
					overlay: {
						opacity: 0.3
					} // overlay
				}
			});

        }
    })
    
   jQuery(document).on("click","#popupyes", function () {
		var linkurl = jQuery(this).attr("link");
		jQuery.fancybox.close();
			//window.location.href = linkurl;
		jQuery.ajax({
			type: "POST",
			url: linkurl,
			//data: '<?= WEB_PATH ?>/merchant/' + linkurl,
			async: false,
			success: function (msg)
			{
				oTable.fnDraw();
			}
		});
	});
	        
    //edit button event on pricelist table
    $('body').on('click','.edit_pricelist',function(){
    	$(this).closest('.dropdown-menu').siblings('.edit_form').submit();
    })
    //publish button event on pricelist table
  

     //event for close pricelist
    $('body').on('click','#close_pricelist_form',function(){
    	$(this).closest('#pricelist_form').hide(500);
        $(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_pricelist_name').hide(500);
            $(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_html').hide(500);
    })
    var pricelist_type = $('#pricelist_type').val();
    var pricelist_name = $('#pricelist_name').val();
    $('#pricelist_type').on('change',function(){
        pricelist_type = $('#pricelist_type').val();
        if(pricelist_type == "product"){
            $('#new_pricelist').attr('action','product_pricelist.php');
        }
        else if (pricelist_type == "service"){
            $('#new_pricelist').attr('action','service_pricelist.php');
        }
    })
    //evento to submit form
    $('#submit_pricelist_form').click(function(){
        pricelist_name = $('#pricelist_name').val();
        if(pricelist_name.length == 0 || html_regex.test(pricelist_name) || pricelist_name.length > 50){
            console.log(pricelist_name.length)
            if (pricelist_name.length == 0)
            {
                $(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_pricelist_name').show(500);
            }
            else
            {
				$(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_pricelist_name').hide(500);
			}
			if (pricelist_name.length > 50)
            {
                $(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_length').show(500);
            }
            else
            {
				$(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_length').hide(500);
			}
            if (html_regex.test(pricelist_name))
            {
                $(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_html').show(500);
            }
            else
            {
				$(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_html').hide(500);
			}
        }
        else{
            $(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_pricelist_name').hide(500);
            $(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_length').hide(500);
            $(this).closest('#pricelist_form').siblings('.error_messages').find('.alert_html').hide(500);
            //checks pricelist type and submits form
            if(pricelist_type == "product"){
                $('#new_pricelist').attr('action','product_pricelist.php');
                $('#new_pricelist').submit();
            }
            else if(pricelist_type == 'none'){
                $('#select_pricelist_type').dialog({modal:true});
            }
            else if (pricelist_type == "service"){
                $('#new_pricelist').attr('action','service_pricelist.php');
                $('#new_pricelist').submit();   
            }
            
        }
    })

      $('#pricelist_name').keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
          $('#submit_pricelist_form').trigger('click');
        }
      });
    $('#select_pricelist_type').find('button').click(function(){
        $('#select_pricelist_type').dialog("close");
    })
    words_remaining("#pricelist_name")
    //function to count remaining words
    function words_remaining(input){
      $('body').on('input',input,function(){
        var len = $(this).val().length;
        var max = $(this).next().find('span').attr('data-max');
        $(this).next().find('span').html(max-len);
      })
    }
})

jQuery(".filter").live("click",function(){
        
        jQuery(".active-filter").removeClass("active-filter");
        jQuery(this).addClass("active-filter");
           
        var oTable = jQuery('#pricelist_table').DataTable();
		
		if(jQuery(this).attr("id")=="product")
        {
			searchTerm = 'product'; 
			oTable.fnFilter("^"+searchTerm+"$",5,true,false);
		}
		else if(jQuery(this).attr("id")=="service")
        {
			searchTerm = 'service'; 
			oTable.fnFilter("^"+searchTerm+"$",5,true,false);
		}
		else if(jQuery(this).attr("id")=="all")
		{
			//searchTerm = 'Active - In Use'; 
			var oSettings = oTable.fnSettings();
			for(iCol = 0; iCol < oSettings.aoPreSearchCols.length; iCol++) 
			{
				oSettings.aoPreSearchCols[ iCol ].sSearch = '';
			}
			oSettings.oPreviousSearch.sSearch = '';
			oTable.fnDraw();
		}   
});
</script>
