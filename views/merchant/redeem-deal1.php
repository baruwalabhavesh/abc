<?php
/**
 * @uses redeem deal
 * @used in pages :my-account-left.php
 * @author Sangeeta Raghavani
 */

session_start();
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
////$objDB = new DB();

?>
<!DOCTYPE HTML >
<html>
<head>
<title>ScanFlip | Redeem </title>

<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">

</head>

<body class="redeem_page">
	
	<div id="dialog-message" title="Message Box" style="display:none">
	</div>
	
<div >

<!--<script type="text/javascript" src="https://code.jquery.com/jquery-1.6.2.min.js"></script>-->
<!-- load from CDN-->
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> -->
<script type="text/javascript" src="<?=ASSETS_JS?>/m/jquery.form.js"></script>

<?php
  if(isset($_COOKIE['merchant_'.$_SESSION['merchant_id']]))
    {
	  if($_COOKIE['merchant_'.$_SESSION['merchant_id']]!="")
	  {
      ?>

<?php
     
        $lnglat = $_COOKIE['merchant_'.$_SESSION['merchant_id']];
        $lnglat_arr = explode(";",$lnglat);
        $curr_longitude = $lnglat_arr[0];
        $curr_latitude = $lnglat_arr[1];
	  }
    }
    else{
        ?>
<script>

/******** GET USER CURRENT LOCATION *********************/
function getLocation1()
{
    if (navigator.geolocation)
    {
		navigator.geolocation.getCurrentPosition(showPosition1 , function(errorCode) {
			//alert(errorCode);
			if (errorCode.code == 1) {
                              var date = new Date();
                                date.setTime(date.getTime()+(1*60*60));
                                setCookie("merchant_<?php echo $_SESSION['merchant_id']; ?>","",date.toGMTString());
                                alert("Please enter your curret location or postal code to search offers near you.");
			}
			if( errorCode.code == 2)
			{
                            var date = new Date();
                            date.setTime(date.getTime()+(1*60*60));
                            setCookie("merchant_<?php echo $_SESSION['merchant_id']; ?>","",date.toGMTString());
                            alert("We can't find your current location.Please enter your cuuret location or postal code to search offers near you.");
			 }
		});
    }
    else
    {
                alert("Geolocation is not supported by this browser.");
    }
}
function showPosition1(position)
{
	    var val;
            val="Latitude: " + position.coords.latitude + 
            "<br />Longitude: " + position.coords.longitude;
            var latlng = position.coords.latitude+";"+position.coords.longitude;
            var date = new Date();
            date.setTime(date.getTime()+(1*60*60));
            setCookie("merchant_<?php echo $_SESSION['merchant_id']; ?>",latlng,date.toGMTString());
            window.location.reload(false);
}
/*************** SET COOKIE ***********************/
function setCookie(c_name,value,exdays)
{
        var exdate=new Date();
        exdate.setDate(exdate.getDate() + exdays);
        var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
        document.cookie=c_name + "=" + c_value;
}
                    </script>
                <?php
    }
?>
<!---start header---->
	<div>
		<?
		//INCLUDE HEADER FILE
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	
   
	<div id="content">


  <h1><?php echo $merchant_msg["redeem-deal"]["redeem_campaign"];?></h1>
    
    
    <div id="redem_contanr">
   <div id="main_redem_selection">
	   <ul>
		   <li>
			  <input type="radio" checked id="redeem_voucher_code" name="redem_option" value="voucher" /><label for="redeem_voucher_code" class= "chk_align">Campaign Code</label>
		   </li>
		   <li>  
			  <input type="radio" id="redeem_reward_card" name="redem_option" value="reward" /><label for="redeem_reward_card" class= "chk_align">Scanflip Reward Card</label>
		   </li>
		   <li>
			  <input type="radio" id="redeem_stamp_card" name="redem_option" value="stamp" /><label for="redeem_stamp_card" class= "chk_align">Loyalty Card</label>
		  </li>
	   </ul>
   </div>							
  <div id="reward_div" style="display:none;"> 
	  <table id="enter_reward_number">
			<tbody>
			<tr>
				<td>
				<?php echo $merchant_msg["redeem-deal"]["Field_enter_loyalty_card"];?>:
				</td>
				<td>
					<input type="text" name="txt_rewardcard" id="txt_rewardcard" autocomplete="off" style="width:200px;" value=""> 
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td style="padding-top:15px;">
					<input type="button" value="Submit" name="btn_get_campaignlist" id="btn_get_campaignlist">
				</td>
			</tr>
			</tbody>
	   </table>
	   <div id="campaign_list_div">
	   </div>
  </div>	  
  <div id="redem_div" style="display:block;">
  <table width="100%"  border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td width="100%" align="left" valign="top">
	<form action="process.php" method="post" id="campaign_detail_form">
	
            
		<div align="center">
                    
                <table>
					<tr>
						<td>
						<?php echo $merchant_msg["redeem-deal"]["Field_enter_barcode"];?>:
						</td>
						<td>
							<input type="text" name="txt_barcode" id="txt_barcode" autocomplete="off" onkeyup="gofor_autocomplete(this.value);" style="width:200px;"/> 
							<br />
							<div id="autocomplete" class="redeem_autocomplete_div" >
							</div>
						</td>
					</tr>
					<tr>
						<td>
						<?php echo $merchant_msg["redeem-deal"]["Field_enter_total_sales"];?>:
							
						</td>
						<td>
							<input type="text" value="" name="txt_redeem_deal_value" id="txt_redeem_deal_value" style="width:200px;"/>
							
						</td>
					</tr>
					<tr>
						<td>
							
						</td>
						<td style="padding-top:15px;">
							
							<input type="submit" value="Redeem" name="btn_redeem" id="btn_redeem" class="disabled" disabled/>&nbsp;&nbsp;
							<input type="button" value="Cancel" name="btn_cancle" id="btn_cancle" onclick="window.location.href=window.location.href;" />
						</td>
					</tr>
			   </table>
          </div>
		  <input type="hidden" id="hdn_coupon_code" name="hdn_coupon_code" value="" />
	    </form>
	    <div id="div_error_content"></div>
	    <form action="process.php" method="post" id="redeem_form">
	    <div id="div_campaign_info">
		
	    </div>
	    </form>	
    </td>
  </tr>
  </table>
  </div>
	
	<div id="stamp_div" style="display:none;">
  <table width="100%"  border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td width="100%" align="left" valign="top">
	<form action="process.php" method="post" id="campaign_detail_form">
	
            
		<div align="center">
                    
                <table>
					<tr>
						<td>
						<?php echo $merchant_msg["redeem-deal"]["Field_enter_stampcard"];?>:
						</td>
						<td>
							<input type="text" name="txt_stampcard" id="txt_stampcard" autocomplete="off" style="width:200px;"/> 
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $merchant_msg["redeem-deal"]["Field_enter_total_sales"];?>:
						</td>
						<td>
							<input type="text" value="" name="txt_revenue" id="txt_revenue" style="width:200px;"/>
							
						</td>
					</tr>
					<tr>
						<td>
							
						</td>
						<td style="padding-top:15px;">
							
							<input type="button" value="Redeem" name="btn_redeem_card" id="btn_redeem_card" class="disabled" disabled/>&nbsp;&nbsp;
							<input type="button" value="Cancel" name="btn_cancle" id="btn_cancle" onclick="window.location.href=window.location.href;" />
						</td>
					</tr>
			   </table>
          </div>
		  <input type="hidden" id="hdn_coupon_code" name="hdn_coupon_code" value="" />
	    </form>
	    <div id="div_error_content"></div>
	    <form action="process.php" method="post" id="redeem_form">
	    <div id="div_campaign_info">
		
	    </div>
	    </form>	
    </td>
  </tr>
  </table>
  </div>
  
	</div>
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
</html>
<script>

jQuery("#btn_redeem_card").click(function(){
	var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
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
		var stampcard_number=jQuery("#txt_stampcard").val();
		var revenue=jQuery("#txt_revenue").val();
		if(stampcard_number=="")
		{
			var head_msg="<div class='head_msg'>Message</div>"
			var content_msg="<div class='content_msg'>Please enter card</div>";
			var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
			jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

			//alert(content_msg);

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
		else if(revenue=="")
		{
			var head_msg="<div class='head_msg'>Message</div>"
			var content_msg="<div class='content_msg'>Please enter revenue</div>";
			var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
			jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

			//alert(content_msg);

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
		else
		{
			jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'punchcard_for_user=yes&merchant_id='+merchant_id+"&code="+stampcard_number+"&revenue="+revenue,
				async:false,
				success:function(msg)
				{
					var obj = jQuery.parseJSON(msg);
					if (obj.status=="true")     
					{
						var head_msg="<div class='head_msg'>Message</div>"
						var content_msg="<div class='content_msg'>"+obj.message+"</div>";
						var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
						jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

						//alert(content_msg);

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
						
						jQuery("#txt_stampcard").val('');
						jQuery("#txt_revenue").val('');
						return false;
					}
					else
					{
						var head_msg="<div class='head_msg'>Message</div>";
						var content_msg="<div class='content_msg'>"+obj.message+"</div>";
						var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
						jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

						//alert(content_msg);

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
			});	
		}
	}
});
jQuery("#btn_get_campaignlist").click(function(){
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
			var reward_card_number=jQuery("#txt_rewardcard").val();
			if(reward_card_number=="")
			{
				var head_msg="<div class='head_msg'>Message</div>"
				var content_msg="<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_enter_reward_card"];?></div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

				//alert(content_msg);

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
			else
			{
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'getcamplist_from_rewardcard=true&reward_card_number='+reward_card_number,
					async:false,
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="true")     
						{
							jQuery("#enter_reward_number").css("display","none");
							jQuery("#campaign_list_div").html(obj.html);
							jQuery("#campaign_list_div").css("display","block");
							bind_campaign_select();
						}
						else
						{
							var head_msg="<div class='head_msg'>Message</div>";
							var content_msg="<div class='content_msg'>"+obj.error_msg+"</div>";
							var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
							jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

							//alert(content_msg);

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
				});	
			}
		}
});

function bind_campaign_select()
{
	jQuery("input[id^=campaign_select_]").click(function(){
		//alert(jQuery(this).attr('coupon_code'));
		var v_code = jQuery(this).attr('coupon_code');
		jQuery("#campaign_list_div").css("display","none");
		jQuery("#txt_barcode").val(v_code);
		jQuery("#redem_div").css("display","block");
	});
	
	jQuery("#goback_rewardcard").click(function(){
		jQuery("#campaign_list_div").css("display","none");
		jQuery("#enter_reward_number").css("display","block");
	});
	
	jQuery("input[class=fltr_button]").click(function(){
		var fltr_option=jQuery(this).attr("id");
		/*
		alert(fltr_option);
		alert(jQuery("div.campaign_inner_div").size());	
		alert(jQuery("div.campaign_inner_div[camp='1']").size());	
		alert(jQuery("div.campaign_inner_div[camp='2']").size());	
		*/
		
		jQuery("div.filters input").removeClass("fltr_selected");
		if(fltr_option=="fltr_all")
		{
			jQuery(this).addClass("fltr_selected");
			jQuery("div.campaign_inner_div").css("display","block");
		}
		else if(fltr_option=="fltr_campaign")
		{
			jQuery(this).addClass("fltr_selected");
			jQuery("div.campaign_inner_div").css("display","none");
			jQuery("div.campaign_inner_div[camp='1']").css("display","block");
		}
		else if(fltr_option=="fltr_stamp")
		{
			jQuery(this).addClass("fltr_selected");
			jQuery("div.campaign_inner_div").css("display","none");
			jQuery("div.campaign_inner_div[camp='2']").css("display","block");
		}
	});
}   
jQuery("input[type=radio][name=redem_option]").click(function(){
    var redem_option=jQuery(this).val();
    //alert(redem_option);
    if(redem_option=="voucher")
    {
		jQuery("#reward_div").css("display","none");	
		jQuery("#stamp_div").css("display","none");
		jQuery("#redem_div").css("display","block");
		
	}
	else if(redem_option=="reward")
    {
		jQuery("#redem_div").css("display","none");
		jQuery("#stamp_div").css("display","none");	
		jQuery("#reward_div").css("display","block");
		jQuery("#campaign_list_div").css("display","none");
		jQuery("#enter_reward_number").css("display","block");
		
	}
	else if(redem_option=="stamp")
    {
		jQuery("#reward_div").css("display","none");
		jQuery("#redem_div").css("display","none");
		jQuery("#stamp_div").css("display","block");
	}
	
});


jQuery(document).ready(function(){
    /****** SUBMIT FORM WITHOUT REFRESHING PAGE **************/
	jQuery('#campaign_detail_form').ajaxForm({
		dataType:  'json', 
		beforeSubmit:loginornot,   // CALL REDEEEM FUNCTION
		success:   processRedeemJson  /// IF ERROR RETURN BY REDEEM FUNCTION THEN HADLE BY THIS METHOD
	});

	
});


/***** IF VOUCHER CODE IS ENTERD AND VOUCHER CODE LENGTH MUST BE GREATER THEN 10 AND REDEEM DEAL VALUE MUST NE ENTERD THEN ENABLE THE REDEEM BUTTON ACTIVE********/
jQuery("input[type='text']").keyup(function(){
	
    if(jQuery("#txt_barcode").val().length==8 && jQuery("#txt_redeem_deal_value").val().length>0)
	{
		jQuery('#btn_redeem').removeAttr("disabled");
		jQuery('#btn_redeem').removeClass("disabled");
	}
	else
	{
		jQuery('#btn_redeem').attr("disabled","");
		jQuery('#btn_redeem').addClass("disabled");
	}
	
	if(jQuery("#txt_stampcard").val().length==8 && jQuery("#txt_revenue").val().length>0)
	{
		jQuery('#btn_redeem_card').removeAttr("disabled");
		jQuery('#btn_redeem_card').removeClass("disabled");
	}
	else
	{
		jQuery('#btn_redeem_card').attr("disabled","");
		jQuery('#btn_redeem_card').addClass("disabled");
	}
});

jQuery(".fancybox-inner #popupredeem").live("click",function(){
	window.location = "redeem-deal.php";
});

/***** IF CURRENT MERCAHNT SESSION TIME OUT THEN SEND TO REGISTER PAGE AND AFTER REGISTRATION AGAIN COME TO REDEEM DEAL PAGE , 
OTHERWISE CONTINUE WITH REDEEM FUNCTIONALITY
BEFORE SUBMITING FROM CHECK WHETHER VOUCHER CODE IS ENETER AND IS PROPER REDEEM VALUE IS ENTERED OR NOT AND GIVE PROPER ERROR MESSAGE
*****/
	function loginornot()
    {
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
			var barcode=jQuery("#txt_barcode").val();
			if(barcode=="")
			{
				var head_msg="<div class='head_msg'>Message</div>"
				var content_msg="<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_enter_proper_coupon_code"];?></div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

				//alert(content_msg);

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
			var deal_value = jQuery("#txt_redeem_deal_value").val();
			var numbers = /^[0-9]+$/;
			var float_value=/^[+-]?\d+(\.\d+)?$/;
			/*
			if(deal_value.match(float_value))
			{
				alert("match");
			}
			else
			{
				alert("not match");
			}
			return false;
			*/
			if(deal_value.match(float_value) && deal_value!="")
			{			
			
				if(deal_value<=0)
				{
					//alert("Enter proper deal value");
					var head_msg="<div class='head_msg'>Message</div>"
					var content_msg="<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"];?></div>";
					var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
					jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

					//alert(content_msg);

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
				else
				{
					return true;
				}
			}
			else
			{
				var head_msg="<div class='head_msg'>Message</div>"
				var content_msg="<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"];?></div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

				//alert(content_msg);

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
    }
    function processLogJson(data)
    {
		// alert(data.status);
		$("#err_msg").html("");
		if(data.status == "true")
		{
		
			//  window.location = "redeem-deal.php?id="+data.id;

			//$("#div_error_content").html("<p>"+data.point_message+"</p>");

			var head_msg="<div class='head_msg'>Message</div>"
			var content_msg="<div class='content_msg'>"+ "* " + data.point_message+"</div>";
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
						closeClick: false,
						opacity: 0.3
					} // overlay
				}
			});
			$.ajax({
				type:"POST",
				url:'process.php',
				data :'id='+data.id+'&btn_campaign_detail=true&coupon_code='+$("#txt_barcode").val(),
				success:function(msg)
				{	
					$("#div_campaign_info").html(msg);
					$("#btn_submit").css("display","none"); 
				}
			});

		}
		else
		{
			// window.location = "redeem-deal.php";
			//$("#div_error_content").html("<p>"+data.message+"</p>");
			var head_msg="<div class='head_msg'>Message</div>"
			var content_msg="<div class='content_msg'>"+ "* " + data.message+"</div>";
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
						closeClick: false,
						opacity: 0.3
					} // overlay
				}
			});
			$("#div_campaign_info").html("");
			return false;
		}
    }	
    function precheckingRedeem(formData, jqForm, options)
    {
        
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
        
            
            var val = parseInt($("#txt_redeem_deal_value").val());
         
           if(val<=0)
               {
                   //alert("Enter proper deal value");
		   var head_msg="<div class='head_msg'>Message</div>"
		var content_msg="<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"];?></div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		
		//alert(content_msg);
		
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
               }else
                   {
                       return true;
                   }
        }
        
           
    }
    function processRedeemJson(data)
    {
     if(data.status == "true"){
          var head_msg="<div class='head_msg'>Message</div>"
		var content_msg="<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_coupon_redeemed"];?></div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupredeem' name='popupredeem' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		
		
		jQuery.fancybox({
					content:jQuery('#dialog-message').html(),
					type: 'html',
					openSpeed  : 300,
					closeSpeed  : 300,
					changeFade : 'fast',  
					helpers: {
						overlay: {
						closeClick: false,
						opacity: 0.3
						} // overlay
					}
		});   
	    //window.location = "redeem-deal.php";
	}else
	{
	    //$("#div_error_content").html("<p>"+data.message+"</p>");
           
		var head_msg="<div class='head_msg'>Message</div>"
		var content_msg="<div class='content_msg'>"+data.message+"</div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		
		//alert(content_msg);
		
		jQuery.fancybox({
					content:jQuery('#dialog-message').html(),
					type: 'html',
					openSpeed  : 300,
					closeSpeed  : 300,
					changeFade : 'fast',  
					helpers: {
						overlay: {
						closeClick: false,
						opacity: 0.3
						} // overlay
					}
		});
	    
	    $("#div_campaign_info").html("");
		return false;
	}
    }
    
$("a#redeem-coupons").css("background-color","orange");


/****** AUTO COMPLETE VOUCHER CODE TEXT BOX FROM DATABASE *******/
function gofor_autocomplete(val)
{
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
					window.location.href=obj.link;
					flag=1;
				}  
			}
    });
	
	if(flag==0)
	{
		if(val.length>=4)
		{
			//alert(val);
			url = '<?= WEB_PATH ?>/merchant/get_vouchercode.php';
			//alert(url);
			jQuery.ajax({
				  type: "POST",
				  url:url,
				  data:"couponname="+ val,
				  async: true,
				  beforeSend: function(){
						//closePopup(200);
						//open_popup('Wait');				   				  
					},
				  success: function(result){
					 
					  //alert(result);
					  document.getElementById("autocomplete").style.display = "block";
					  document.getElementById("autocomplete").innerHTML = result;
					  
					
				}
			});  
		}
		else
		{
			 document.getElementById("autocomplete").style.display = "none";
			 document.getElementById("div_error_content").innerHTML="";
		}
	}
}
jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
    
function repalcevalue(val)
{
	var val = val.innerHTML;
	document.getElementById("txt_barcode").value = val;
	document.getElementById("autocomplete").style.display = "none";
	
	if(jQuery("#txt_barcode").val().length>=10 && jQuery("#txt_redeem_deal_value").val().length>0)
	{
		jQuery('#btn_redeem').removeAttr("disabled");
		jQuery('#btn_redeem').removeClass("disabled");
	}
	else
	{
		jQuery('#btn_redeem').attr("disabled","");
		jQuery('#btn_redeem').addClass("disabled");
	}
}
<!--// 369-->

var merchant_ ='<?php echo $_SESSION['merchant_info']['merchant_parent'];?>'; 
//alert(merchant_);
if(merchant_==0)
{
	var merchant_redeem ='<?php echo $_SESSION['merchant_info']['redeem_location'];?>'; 
	//alert(merchant_redeem);
	if(merchant_redeem=="")
	{
		var head_msg="<div class='head_msg'>Message</div>";
		var content_msg="<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_select_location"];?></div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' onclick='btncanprofile()' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

		//alert(content_msg);

		jQuery.fancybox({
					content:jQuery('#dialog-message').html(),
					type: 'html',
					openSpeed  : 300,
					closeSpeed  : 300,
					changeFade : 'fast',  
					helpers: {
						overlay: {
						closeClick: false,
						opacity: 0.3
						} // overlay
					}
		});
	}
}
function btncanprofile()
{                                                
	window.location="<?=WEB_PATH?>/merchant/my-profile.php";
}

jQuery("#main_redem_selection ul li").click(function(){
	//alert(jQuery(this).find("input").val());
	jQuery("#main_redem_selection ul input").prop("checked","false");
	jQuery(this).find("input").prop("checked","true");
	//jQuery(this).find("input").trigger("click");
	
	var redem_option=jQuery(this).find("input").val();
	if(redem_option=="voucher")
    {
		jQuery("#reward_div").css("display","none");
		jQuery("#stamp_div").css("display","none");	
		jQuery("#redem_div").css("display","block");
	}
	else if(redem_option=="reward")
    {
		jQuery("#redem_div").css("display","none");	
		jQuery("#stamp_div").css("display","none");
		jQuery("#reward_div").css("display","block");
		jQuery("#campaign_list_div").css("display","none");
		jQuery("#enter_reward_number").css("display","block");
		
	}
	else if(redem_option=="stamp")
    {
		jQuery("#reward_div").css("display","none");
		jQuery("#redem_div").css("display","none");
		jQuery("#stamp_div").css("display","block");
	}
	
});

</script>
