<?php 
check_merchant_session();
$array_where_act['active']=1;
$RSCat = $objDB->Show("loyaltycard_category",$array_where_act);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ScanFlip | Manage Cards</title>
	<?php require_once(MRCH_LAYOUT."/head.php"); ?>
	<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap.min.css">
	<!--<link rel="stylesheet" href="<?php //echo ASSETS?>/loyalty/css/reset.css"> -->
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo ASSETS?>/loyalty/css/manage.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery-ui.css">
	<!--<script src="<?php //echo ASSETS?>/loyalty/js/jquery.js"></script>-->
	<script src="<?php echo ASSETS?>/pricelist/js/bootstrap.min.js"></script>
	<script src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo ASSETS ?>/pricelist/js/jquery-ui.js"></script>
  
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
	<h4 id="header-title">Manage Loyalty Cards</h4>	
	
  <div class="loyalty_Card_maincontainer">
  <button class="btn btn-primary new_card_button">Create Cards</button>
  <div class="error_messages">
	         <div class="alert alert-warning alert_card_title">Please enter card title</div>
	         <div class="alert alert-warning alert_html" style="">No HTML allowed</div>
			<div class="alert alert-warning alert_length" style="">Maximum Length should be 76 character</div>
			<div class="alert alert-warning alert_keyword" style="">Please remove any special characters you have entered for card keywords.<br/> Also do not start or use only numbers for card keywords.<br/> You can only add 3 card keywords</div>
			<div class="alert alert-warning alert_category" style="">Please select card category</div>
		</div>
	<div class="revealable_form">
        <form id="new_card" action="new_card.php" method="post">
          <label for="">Card Title:</label>
          <input type="text" class="form-control" maxlength="76" id="card_title" name="card_title">
          <p class="hint"><span data-max='76'>76</span> characters remaining | No HTML allowed</p>
          
          <label for="">Card Keyword:</label>
          <input type="text" class="form-control" maxlength="76" id="card_keyword" name="card_keyword">
          <p class="hint">3 keywords separated by commas | No HTML allowed</p>
         
          <div class="termsdiv">
            <label for="terms_conditions">Terms and conditions:</label>
            <textarea name="terms_conditions" id="terms_conditions" class="form-control"></textarea>
            <!--<p class="text" style="display:inline">Punch rule:</p>
            <input type="radio" checked="checked" name="punch_per" class="form-control" style="display:inline" value="visit">Punch per visit
            <input type="radio" name="punch_per" class="form-control" style="display:inline" value="day">Punch per day-->
          </div>
          
          <div class="">
				<label>Select Card Category:</label>
				<div class="">
				  <div class="fleft padv10">
					<select name="card_category" id="card_category" class="select form-control w150">
						<option value="0">Select Category</option>
					  <?
						while($Row = $RSCat->FetchRow()){
						?>
							<option value="<?=$Row['id']?>"><?=$Row['name']?></option>
						<?
						}
						?>
					</select>
				  </div>            
				</div>
			</div>
			
           <div class="">
            <label>Redeemption Limit:</label>
            <p class="hint">
				Optional
			</p>
             <input type="checkbox" id="redeemption_limit" name="redeemption_limit">
            <label for="redeemption_limit" style="cursor:pointer;font-weight:100;">One per day.</label>
           
			</div>
			
			
            <br>
          <button type="button" class="btn btn-primary card_submit">Add</button>
          <button type="button" class="btn btn-primary cancel">Cancel</button>
        </form>
      </div>
    <!--<div class="inner_container"> -->
      
      
      <table class="table display" id="card_table">
        <thead>
			<tr>
				<td colspan="5" align="left" class="filter_result table_filter_area" >
                    
                        <div class="fltr_div_td">
                           <div class="cls_filter">Filter By :</div>
                               <div class="cls_filter_top" style="width: 40%; float: left;">
									<b>Year</b>
									<select id="opt_filter_year" >
										<?php
										for ($y = date('Y') - 2; $y <= date('Y'); $y++) 
										{
										?>
											<option value="<?php echo $y; ?>" 
											<?php
											if ($y == date('Y')) 
											{
												echo "selected";
											}
											?>  > <?php echo $y; ?> 
											</option>
										<?php 
										}
										?>
									</select>
									&nbsp;&nbsp;&nbsp;
									<b>Status</b>
									<select id="opt_filter_status" class="opt_filter_wd" >
										<!--<option value="21" selected="selected" > --- Active or Editable---</option>-->
										<option value="all" selected="selected" > --- All---</option>
										<option value="214" > --- Published or Unpublished---</option>
										<!--<option value="4" > --- Pause ---</option>-->
										<option value="3"  > --- Expired ---</option>
									</select>
									
                               </div>
								<div style="float: left; width: 10%; margin-top: -10px;">
										<input type="button" value="Filter" name="btnfilterCards" id="btnfilterCards" />
									</div>
                          
                          </div>  
                 </td>
			</tr>
          <tr>
            <th>Card Title</th>
            <th>Card Volume</th>
            <th>Cards Left</th>
            <th>Card Status</th>
            <th>Actions</th>
          </tr>
        </thead>
       
      </table>
    <!--</div> -->
  </div>  <!-- end of loyalty carf main container-->
  
	<div id="notification">
		Sorry you don't have enough points to activate card.
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
</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){

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
	var oTable = $('#card_table').dataTable( {
		//"bStateSave": true,
       "bFilter": false,
		"bSort" : false,
		"bLengthChange": false,
		"info": false,
		"iDisplayLength": 10,
		"bProcessing": true,
         "bServerSide": true,
		"oLanguage": {
						"sEmptyTable": "No card founds in the system. Please add at least one.",
						"sZeroRecords": "No card to display",
                        "sProcessing": "Loading..."
                    },
         "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",            
         "fnServerParams": function (aoData) {
                        aoData.push({"name": "btnGetAllCardlist", "value": true},{"name": "status", "value": jQuery('#opt_filter_status').val()},{"name": "year", "value": jQuery('#opt_filter_year').val()} )
						//bind_more_action_click();
                    },
         //"fnServerData": fnDataTablesPipeline,     
         
         "aoColumns": [
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false, "sClass": "actiontd"},
                    ]      
                    
    } );
  
  // $('#card_table').dataTable().fnUpdate( 'first row fifth col', 0, 4 );
  //  $('#card_table').dataTable().fnUpdate( 'second row third col', 1, 2 );
    
  //show new card form in index page
  $('.new_card_button').click(function(){
    $('.revealable_form').show(500);
    $('.revealable_form').find('.cancel').click(function(event){

      $(this).closest('.revealable_form').hide(500)
    })
    //submitting form if title not empty
    $('.revealable_form').find('.card_submit').click(function(){
		var html_regex=/[<>?]/;
		
		var flag="false";
		var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
		var tag_value=jQuery("#card_keyword").val();
		if(tag_value == "")
		{
			flag="true";
		}
		else
		{
			if(hastagRef.test(tag_value))
			{
				var tag_arr = tag_value.split(",");   
				//alert(tag_arr.length);
				if(tag_arr.length>3)
				{
					flag="false";
				}
				else
				{
					flag="true";
				}
			}
			else
			{
				flag="false";
			}
		}
		var card_category = $('#card_category').val();
		console.log(card_category);
		
		 var card_title = $('#card_title').val();
		 //alert(card_title);
        if(card_title.length == 0 || html_regex.test(card_title) || card_title.length > 76|| flag=="false"||card_category==0){
            console.log(card_title.length)
            if (card_title.length == 0)
            {
				$('.error_messages').find('.alert_card_title').show(500);
            }
            else
            {
				$('.error_messages').find('.alert_card_title').hide(500);
			}
			if (card_title.length > 76)
            {
                $('.error_messages').find('.alert_length').show(500);
            }
            else
            {
				$('.error_messages').find('.alert_length').hide(500);
			}
            if (html_regex.test(card_title))
            {
                $('.error_messages').find('.alert_html').show(500);
            }
            else
            {
				$('.error_messages').find('.alert_html').hide(500);
			}
			if(flag=="false")
			{
                $('.error_messages').find('.alert_keyword').show(500);
            }
            else
            {
				$('.error_messages').find('.alert_keyword').hide(500);
			}
			if(card_category==0)
			{
                $('.error_messages').find('.alert_category').show(500);
            }
            else
            {
				$('.error_messages').find('.alert_category').hide(500);
			}
        }
        else
        {
			$('.error_messages').find('.alert_card_title').hide(500);
            $('.error_messages').find('.alert_length').hide(500);
            $('.error_messages').find('.alert_html').hide(500);
            $('.error_messages').find('.alert_keyword').hide(500);
            $('.error_messages').find('.alert_category').hide(500);
			$(this).closest('form').submit();
		}

    })
  })
  
   words_remaining("#card_title")
    //function to count remaining words
    function words_remaining(input){
      $('body').on('input',input,function(){
        var len = $(this).val().length;
        var max = $(this).next().find('span').attr('data-max');
        $(this).next().find('span').html(max-len);
      })
    }
    
    //delete request to delete_card.php
    jQuery('body').on('click','.delete_card',function(){
		 var id=jQuery(this).attr('value');
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
					
				 /*
					
				  jQuery.ajax({
					  type: "POST", // HTTP method POST or GET
					  url: "delete_card.php", //Where to make Ajax calls
					  dataType:"text", // Data type, HTML, json etc.
					  data:{card_id:id},
					  success:function(response){
						console.log(response);
						oTable.fnDraw();
					  },
					  error:function (xhr, ajaxOptions, thrownError){
						  alert(thrownError);
					  }
				  })
				*/
				
				var cardid = id;
				var linkurl = "delete_card.php?card_id=" + cardid;
				var msg = "<div style='width: 270px;'>Are you sure you want to delete card ?</div>";
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
			}
		});
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
				console.log(msg);
				oTable.fnDraw();
			}
		});
	});
	
	jQuery(document).on("click","#popupcancel", function () {
	//jQuery(".fancybox-inner #popupcancel").live("click",function(){
		jQuery.fancybox.close(); 
		return false;
	});
  
    //activate card request (not working)
    jQuery('body').on('click','.activate_card',function(){
		var id=jQuery(this).attr('value');
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
				  type: "POST", // HTTP method POST or GET
				  url: "activate_card.php", //Where to make Ajax calls
				  dataType:"text", // Data type, HTML, json etc.
				  data:{card_id:id},
				  success:function(response){
					console.log(response);
					//window.location.href = 'manage_cards.php';
					
					if(response=="activate success")
					{
						oTable.fnDraw();
					}
					else
					{
						//alert("points not available");
						jQuery('#notification').fadeIn(500);
						setTimeout(function(){jQuery('#notification').fadeOut(500); }, 3000);
					}
				  },
				  error:function (xhr, ajaxOptions, thrownError){
					  alert(thrownError);
				  }
			  })
			}
		}
		});
    })
      //activate card request (not working)
    jQuery('body').on('click','.pause_card',function(){
		var id=jQuery(this).attr('value');
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
						  type: "POST", // HTTP method POST or GET
						  url: "pause_card.php", //Where to make Ajax calls
						  dataType:"text", // Data type, HTML, json etc.
						  data:{card_id:id},
						  success:function(response){
							console.log(response);
							//window.location.href = 'manage_cards.php';
							oTable.fnDraw();
						  },
						  error:function (xhr, ajaxOptions, thrownError){
							  alert(thrownError);
						  }
					  })
				}
			}
		});
    })
    //request to check if id exists and redirect to edit page if id exist
    jQuery('body').on('click','.edit_card',function(){
		 var id=jQuery(this).attr('value');
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
					
						//alert("hi");
					 
					  jQuery.ajax({
						  type: "POST", // HTTP method POST or GET
						  url: "check_card.php", //Where to make Ajax calls
						  dataType:"text", // Data type, HTML, json etc.
						  data:{card_id:id},
						  success:function(response){
							console.log(response+" "+id);

							if(response == 'success')
							{
								//alert("sucesss");
							  jQuery('#card_id').val(id);

							//attaches id to form at the bottom of page and submits the form which takes to edit page
							  jQuery('#edit_card_form').submit();
							}
						  },
						  error:function (xhr, ajaxOptions, thrownError){
							  alert(thrownError);
						  }
					  })
					  //activate card request (not working)
				}
			}
		});
  })
  
  //request to check if id exists and redirect to copy page if id exist
    jQuery('body').on('click','.copy_card',function(){
		var id=jQuery(this).attr('value');
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
					
						//alert("hi");
					  
					  jQuery.ajax({
						  type: "POST", // HTTP method POST or GET
						  url: "check_card.php", //Where to make Ajax calls
						  dataType:"text", // Data type, HTML, json etc.
						  data:{card_id:id},
						  success:function(response){
							console.log(response+" "+id);

							if(response == 'success')
							{
								//alert("sucesss");
							  jQuery('#card_id_hdn').val(id);

							//attaches id to form at the bottom of page and submits the form which takes to copy page
							  jQuery('#copy_card_form').submit();
							}
						  },
						  error:function (xhr, ajaxOptions, thrownError){
							  alert(thrownError);
						  }
					  })
					  //activate card request (not working)
				}
			}
		});
  })
  
  jQuery("#btnfilterCards").click(function(){
	  
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
				oTable.fnDraw();		
			}
        }
    });
  });
  
})
</script>
