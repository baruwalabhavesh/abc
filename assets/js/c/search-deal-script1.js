/*
Onclick clean input field 
*/
//jQuery(document).ready(function(){
    var not_found_msg = jQuery('#hdn_error_message').html();

//});
/*
Onclick clean input field 
*/
//jQuery(document).ready(function(){
    var not_found_msg = jQuery('#hdn_error_message').html();

//});

function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
function validate_register(){
    
        if(email_validation( jQuery(".fancybox-inner #email").val()) == false && jQuery(".fancybox-inner #mycaptcha_rpc").val() == "")
            {
                 jQuery(".fancybox-inner #emailerror").html("Please enter valid email");
                jQuery(".fancybox-inner #captchaerror").html("Captcha does not match");
                document.getElementById("email").focus();
                error_var="false";
		return false;
            }
            
	else if(email_validation( jQuery(".fancybox-inner #email").val()) == false){
		//alert("Please Enter valid Email");
                 jQuery(".fancybox-inner #emailerror").html("Please enter valid email");
		jQuery(".fancybox-inner #email").focus();
                error_var="false";
		return false;
	}
        else if( jQuery(".fancybox-inner #mycaptcha_rpc").val() == ""){
		//alert("Please Enter Captcha");
                jQuery(".fancybox-inner #captchaerror").html("Captcha does not match");
		jQuery(".fancybox-inner #mycaptcha_rpc").focus();
		error_var="false";
                return false;
	}
        else
        {
             jQuery(".fancybox-inner #captchaerror").html("");
            jQuery(".fancybox-inner #emailerror").html("");
            error_var="true";
            return true;
        }
	
	
}
 function bind_popup_event()
 {
//     alert( jQuery('.fancybox-inner #login_frm').length);
     jQuery('.fancybox-inner #login_frm').ajaxForm({ 
        dataType:  'json', 
        success:   processLogJson 
    });
jQuery("body").on("click",".fancybox-inner #btn_msg_div",function(){
	      

                 jQuery(".fancybox-inner .email_popup_div").css("display","block");
                    jQuery(".fancybox-inner .popupmainclass").css("display","none");
                jQuery(".fancybox-inner .mainloginclass").css("display","none");
                 //$(".popupmainclass").show();
	    //   open_popup('emailNotification');
                

	});
         $(".fancybox-inner .textlink").click(function(){
              jQuery(".fancybox-inner .forgotmainclass").css("display","block");
              jQuery(".fancybox-inner .email_popup_div").css("display","none");
              jQuery(".fancybox-inner .popupmainclass").css("display","none");
               jQuery(".fancybox-inner .updateprofile").css("display","none");
              jQuery(".fancybox-inner .mainloginclass").css("display","none");
        });
        $(".fancybox-inner #btn_cancel_forgot").click(function(){
            jQuery(".fancybox-inner .email_popup_div").css("display","none");
                    jQuery(".fancybox-inner .popupmainclass").css("display","block");
                jQuery(".fancybox-inner .mainloginclass").css("display","none");
                 jQuery(".fancybox-inner .updateprofile").css("display","none");
                 jQuery(".fancybox-inner .forgotmainclass").css("display","none");
        });
        jQuery("body").on("click",".fancybox-inner .email_popup_div #btn_cancel",function(){
           jQuery(".fancybox-inner .email_popup_div").css("display","none");
           jQuery(".fancybox-inner .popupmainclass").css("display","block");
            jQuery(".fancybox-inner .updateprofile").css("display","none");
           jQuery(".fancybox-inner .mainloginclass").css("display","none");
                 jQuery(".fancybox-inner .forgotmainclass").css("display","none");
        });
     
        
         $(".fancybox-inner  #btn_goback_error").click(function(){
	           $(".fancybox-inner  .forgotmainclass").css("display","block");
		    jQuery(".fancybox-inner .email_popup_div").css("display","none");
		   jQuery(".fancybox-inner .popupmainclass").css("display","none");
                jQuery(".fancybox-inner .mainloginclass").css("display","none");
		   $(".fancybox-inner  .errormainclass").css("display","none");
	   });
         
 } 
function remove_filter(catid)
{
	var selected_cat_id=getCookie("cat_remember");
        var miles_cookie = getCookie("miles_cookie");
        
		var aplyview=getCookie("view");
		if(aplyview=="gridview")
		{
			
			jQuery(".info").css("display","none");
		}
		/* 27-8-2013 */
		/*
		else
		{
			
			jQuery(".info").css("display","block");
			
		}
		*/
		/* 27-8-2013 */
}
function urlParameter() 
{
    var url = window.location.href,
    retObject = {},
    parameters;

    if (url.indexOf('?') === -1) {
        return null;
    }

    url = url.split('?')[1];

    parameters = url.split('&');

    for (var i = 0; i < parameters.length; i++) {
        retObject[parameters[i].split('=')[0]] = parameters[i].split('=')[1];
    }

    return retObject;
}
function setCookie(c_name,value,exdays)
{	
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}       
		
//		if(zoomlevel != "")
//		{
//			
//		}
//		else
//		{
//			zoomlevel=10;
//			
//		}
//function bind_category_click_event() { 
try
{
 var timeoutReference;
 jQuery(".btnfilterstaticscampaigns").click(function(){
 open_loader();
  // $("body").on("click",".btnfilterstaticscampaigns",function(){
  // alert("filter click");
  // $( "body").off( "click", ".btnfilterstaticscampaigns");
  //jQuery(".btnfilterstaticscampaigns").off('click');
  var selected_ele = jQuery(this);
	 if (timeoutReference) clearTimeout(timeoutReference);
    timeoutReference = setTimeout(function() {
  jQuery("#category_slider li").removeClass("current");
	selected_ele.addClass("current");
	
	jQuery("#category_slider li img").each(function(){
		var myimg=jQuery(this);
		var main_image=myimg.attr("main_image");
		myimg.attr("src",main_image);
	});
	
	selected_ele.find("img").attr("src",selected_ele.find("img").attr("active_image"));
	
    jQuery("#fltr_category").text(selected_ele.find('span').text());
   if(jQuery("#fltr_category").text()!="All Categories")
		{
			//jQuery("#span_category").text(jQuery(this).find('span').text()+ " category");
			jQuery("#fltr_category_close").find("img").css("cursor","pointer");
			jQuery("#fltr_category_close").find("img").attr("src","templates/images/filter_close.png");
			
			//5-11-2013 with font without image
			jQuery("#fltr_category_close").addClass("filterimage");
			//5-11-2013 with font without image
		}
		else
		{
			//jQuery("#span_category").text(jQuery(this).find('span').text());
			jQuery("#fltr_category_close").find("img").css("cursor","text");
			jQuery("#fltr_category_close").find("img").attr("src","templates/images/filter_close_grey.png");
			
			//5-11-2013 with font without image
			jQuery("#fltr_category_close").removeClass("filterimage");
			//5-11-2013 with font without image
		}
		//alert(jQuery("#span_category").text());
   //alert("hi");
	var $s = jQuery.noConflict();
	$s(".btnfilterstaticscampaigns").each(function(index){
            $s(this).css("color","#3B3B3B");
        });
	
	 
        selected_ele.css('color','orange');
	var WEB_PATH = "<?=WEB_PATH?>";
	selected_cat_id = selected_ele.attr("mycatid");
             jQuery(".displayul").html("");
			var cat_ele=selected_ele;
			
       
			setCookie("cat_remember",selected_cat_id,365);
                       miles_cookie = getCookie("miles_cookie");
					  //  alert(selected_cat_id + "========="+ getCookie("cat_remember"));
					    filter_locations(selected_cat_id,miles_cookie);
						close_loader();
					   //alert(miles_cookie);
                      
                   		               var firstlocid = "";
	 
	
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	
	var flag= "no";
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});
	

if(flag == "yes")
{
	firstlocid = new_locid;
}
//alert(firstlocid+"first location");
//jQuery("#hdn_is_offer_div_click").val("0");

//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

		 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		
		}, 500);	
		
		
		
		
		

		// Note, there is also scrollToX and scrollToY methods if you only
		// want to scroll in one dimension
		if(iOS)
		{
			jQuery('.location1').scrollTo(jQuery("div.newFilterControls"),{ offsetTop : '250'});
		}
		else
		{
		var pane = jQuery('.location');
		pane.jScrollPane(
			{
				horizontalGutter:5,
				verticalGutter:5
				/*
				'showArrows': false,
				mouseWheelSpeed: 50,
				animateScroll:true,
				animateDuration:700,
				animateEase:'linear'
				*/
			}
		);
		var api = pane.data('jsp');
			api.scrollToY(parseInt(0));
		}
		
													
    });
   
			 //}
jQuery(document).ready(function(){
		
		jQuery("#fltr_category").text(jQuery.trim(jQuery("#fltr_category").text()));
		jQuery("#fltr_mile").text(jQuery.trim(jQuery("#fltr_mile").text()));
		//alert(jQuery("#fltr_category").text());
		//alert(jQuery("#fltr_mile").text());
		if(jQuery("#fltr_category").text()=="All Categories")
		{
			jQuery("#fltr_category_close").find("img").css("cursor","text");
			jQuery("#fltr_category_close").find("img").attr("src","templates/images/filter_close_grey.png");
			
			//5-11-2013 with font without image
			jQuery("#fltr_category_close").removeClass("filterimage");
			//5-11-2013 with font without image
		}
		else
		{
			jQuery("#fltr_category_close").find("img").attr("src","templates/images/filter_close.png");
			//5-11-2013 with font without image
			jQuery("#fltr_category_close").addClass("filterimage");
			//5-11-2013 with font without image
		}
		if(jQuery("#fltr_mile").text()=="50 Mi")
		{
			jQuery("#fltr_mile_close").find("img").css("cursor","text");
			jQuery("#fltr_mile_close").find("img").attr("src","templates/images/filter_close_grey.png");
			//5-11-2013 with font without image
			jQuery("#fltr_mile_close").removeClass("filterimage");
			//5-11-2013 with font without image
		}
		else
		{
			jQuery("#fltr_mile_close").find("img").attr("src","templates/images/filter_close.png");
			//5-11-2013 with font without image
			jQuery("#fltr_mile_close").addClass("filterimage");
			//5-11-2013 with font without image
		}
															
        jQuery(".miles",window.parent.document).click(function(){   
	
         // alert(jQuery(this).attr("mval")) ;
		  var selected_ele = jQuery(this);
	 if (timeoutReference) clearTimeout(timeoutReference);
    timeoutReference = setTimeout(function() {
		if(selected_ele.attr("mval")!="50")
		{
			jQuery("#fltr_mile_close").find("img").css("cursor","pointer");
			jQuery("#fltr_mile_close").find("img").attr("src","templates/images/filter_close.png");
			//5-11-2013 with font without image
			jQuery("#fltr_mile_close").addClass("filterimage");
			//5-11-2013 with font without image
		}
		else
		{
			jQuery("#fltr_mile_close").find("img").css("cursor","text");
			jQuery("#fltr_mile_close").find("img").attr("src","templates/images/filter_close_grey.png");
			//5-11-2013 with font without image
			jQuery("#fltr_mile_close").removeClass("filterimage");
			//5-11-2013 with font without image
		}
	jQuery("#fltr_mile").text(selected_ele.attr("mval")+" Mi");
    jQuery(".miles",window.parent.document).each(function(){
            jQuery(this).removeClass("selected_miles");
        });    
        selected_ele.addClass("selected_miles");
    var mile_val = selected_ele.attr("mval");
     setCookie("miles_cookie",mile_val,365);
     //filter_deals_algorithm(getCookie("cat_remember"),mile_val);
	
	 filter_locations(getCookie("cat_remember"),mile_val);
	 /** 03-01-2014 ***/
	  		               var firstlocid = "";
	 
	
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	var flag= "no";
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});

jQuery('#shareit-box').css("display","none");
			jQuery('#price-box').css("display","none");
			jQuery('#discount-box').css("display","none");
			jQuery('#filterpriceidarrow').css("display","none");
			jQuery('#filterdistanceidarrow').css("display","none");
			jQuery("#filterdiscountidarrow").css("display","none");
			 $("#shareit-box").attr("disp","0");
			 $("#price-box").attr("disp","0");
			 $("#discount-box").attr("disp","0");
			 $("#filterpriceidarrow").attr("disp1","0");
			 $("#filterdiscountidarrow").attr("disp1","0");
                       // $("#shareit-box").hide();
						   $("#filterdistanceidarrow").attr("disp1","0");
                     //   $("#filterdistanceidarrow").hide();
if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
	/*** 03-01-2014 ****/
	var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
 },500);


});

});
jQuery(".prices",window.parent.document).click(function(){   
	// alert(jQuery(this).attr("mval")) ;
		  var selected_ele = jQuery(this);
	 if (timeoutReference) clearTimeout(timeoutReference);
    timeoutReference = setTimeout(function() {
		
			jQuery("#fltr_price_close").css("display","block");
			jQuery("#fltr_price").css("display","block");
		
		jQuery("#fltr_price").text("price("+selected_ele.text()+")");
    jQuery(".prices",window.parent.document).each(function(){
            jQuery(this).removeClass("selected_miles");
        });    
        selected_ele.addClass("selected_miles");
    var mile_val = selected_ele.attr("mval");
	jQuery("#hdn_price").val(mile_val);
     //filter_deals_algorithm(getCookie("cat_remember"),mile_val);
	   mile_val1 = getCookie("miles_cookie");
	 filter_locations(getCookie("cat_remember"),mile_val1);
	 /** 03-01-2014 ***/
	  		               var firstlocid = "";
	 
	
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	var flag= "no";
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
	/*** 03-01-2014 ****/
	var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
 },500);


});
/**** discount filter ****/
jQuery(".discounts",window.parent.document).click(function(){   
	// alert(jQuery(this).attr("mval")) ;
		  var selected_ele = jQuery(this);
	 if (timeoutReference) clearTimeout(timeoutReference);
    timeoutReference = setTimeout(function() {
		
			jQuery("#fltr_discount_close").css("display","block");
			jQuery("#fltr_discount").css("display","block");
		
		jQuery("#fltr_discount").text("Discount("+selected_ele.text()+")");
    jQuery(".discounts",window.parent.document).each(function(){
            jQuery(this).removeClass("selected_miles");
        });    
        selected_ele.addClass("selected_miles");
    var mile_val = selected_ele.attr("mval");
	jQuery("#hdn_discount").val(mile_val);
     //filter_deals_algorithm(getCookie("cat_remember"),mile_val);
	   mile_val1 = getCookie("miles_cookie");
	 filter_locations(getCookie("cat_remember"),mile_val1);
	 /** 03-01-2014 ***/
	  		               var firstlocid = "";
	 
	
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	var flag= "no";
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
	/*** 03-01-2014 ****/
	var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
 },500);


});
}
catch(e)
{
	//alert(e);
}


function open_popup(popup_name)
{

	//alert(popup_name+" open function");
	
	jQuery("#" + popup_name + "FrontDivProcessing").fadeIn(100, function () {
		jQuery("#" + popup_name + "BackDiv").fadeIn(100, function () {
			 jQuery("#" + popup_name + "PopUpContainer").fadeIn(100, function () {         
	
			 });
		});
	});
	
	
}
function close_popup(popup_name)
{
	//alert(popup_name+"close function");
	//alert(popup_name);
	
	if(popup_name=="LocationFancy")
	{
		jQuery("body").removeClass("add-over-hidden");
	}
	
	jQuery("#" + popup_name + "FrontDivProcessing").fadeOut(100, function () {
	jQuery("#" + popup_name + "BackDiv").fadeOut(100, function () {
		 jQuery("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				jQuery("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
var tot_record = '<?php echo $tot_records;?>';
    get_next_html('nextoffer');
    jQuery('.nextoffer').click(function(){
        //alert("next click");
		get_next_html('nextoffer');
        
    });
   
    function get_next_html(next_or_prev)
    {
	try
	{
	//alert('hi');
    //$('.mainul').html($('.displayul').html());
    var li_maintain = '';
    jQuery('.mainul').html('');
	
    // alert(jQuery('.mainul').html());
        if(next_or_prev == "nextoffer")
        {
		
		//alert('nextoffer');
		/*
		jQuery('.navigationul').html('');
		   jQuery('.displayul .deal_blk:lt(9)').each(function(){
               
				 jQuery('.navigationul').append(jQuery(this));
						 
            });			*/
			/*
			jQuery('.navigationul').html('');
			jQuery('.navigationul').append(jQuery('.mainul').html());
		*/
			
			
            jQuery('.displayul .deal_blk:lt(9)').each(function(){
                //alert($(this).html());
				//alert($(this).attr('miles'));
                 jQuery('.mainul').append(jQuery(this));
				
				
		 //try1();
                // $('.displayul').append($(this));
                //li_maintain = li_maintain + $(this).html();
            });
			
			
			
	    jQuery('.displayul .deal_blk1:lt(9)').each(function(){
                //alert($(this).html());
                 jQuery('.mainul').append(jQuery(this));
				 
		// try1();
                // $('.displayul').append($(this));
                //li_maintain = li_maintain + $(this).html();
            });
			
		   
           jQuery('.displayul').append(jQuery('.mainul').html());
		   var $col_div=jQuery('.displayul .deal_blk:lt(9)').clone();
			jQuery('.navigationul').html('');
			jQuery('.navigationul').append($col_div);
		   
			
		   /*
		   jQuery('.navigationul').html('');
			jQuery('.navigationul').append(jQuery('.mainul').html());
		   */
		   
		   
        }
       /* else  if(next_or_prev == "prev")
        {
            var tot = tot_record - 9;
          //  alert(tot);
         // alert(($(".displayul  > div").length));
             $('.displayul div:gt()').each(function(){
                //alert($(this).html());
                 $('.mainul').append($(this));
                // $('.displayul').append($(this));
                //li_maintain = li_maintain + $(this).html();
            });
           $('.displayul').append($('.mainul').html());
        } */
 bind_hover_effect();
 }
 catch(e)
 {
	//alert(e);
 }
    }
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
          
function defalut_location_markers()
{
	try
	{
       if(document.cookie.indexOf("miles_cookie") >= 0)
       {
	   miles_cookie=getCookie("miles_cookie");
	  // alert("In if");
         
       }
       else{
	  // alert("In else");
		setCookie("miles_cookie",50,365);
       }     
         	var selected_cat_id="";
var     miles_cookie = "";
                  
                  
                   
                            selected_cat_id=getCookie("cat_remember");
                          
                       
                            miles_cookie=getCookie("miles_cookie");
                        //  alert("In default location marker");
                   
         }
			catch(e)
			{
				//alert(e);
			}
}
function bind_hover_effect()
{
try{
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    }
    else{
 	jQuery('.location_tool').hover(function(){     
	  
		jQuery('.location_tool').css('opacity','1').css('border-radius','').css('box-shadow','');
		
        jQuery(this).css('border-radius','5px 5px 5px 5px')
        .css('box-shadow','0 0 10px rgba(0,0,0,0.35)')
        .css('opacity','1')
		
		for (var prop in markerArray)
		{
			if(prop != "indexOf")
			{
				markerArray[prop].setIcon('./assets/images/c/pin-small.png');
            }
        }
							
		var lokid=jQuery(this).attr("locid");
		infowindow.setContent(infowindowcontent[lokid]);
		markerArray[lokid].setIcon('./assets/images/c/pin-small-blue.png'); 
		infowindow.open(map,markerArray[lokid]);
    },
    function(){
       
        jQuery('.location_tool').each(function(){
           jQuery(this).css('opacity','1'); 
        });
        //jQuery(this).css('background','none repeat scroll 0 0 #FFFFFF')
        jQuery(this).css('border-radius','')
        jQuery(this).css('box-shadow','')
        
    });
	
	// 25-03-2014 start deal block hover reserve redeem tooltip
	
		jQuery('.deal_blk').live('mouseenter',function(){
			//alert(jQuery(this).find(".strip").length);
		   //alert(jQuery(this).find(".strip_grid").length);
			/*
			if(jQuery(this).find(".strip").length > 0)
			{
				var ele_strip = jQuery(this).find(".strip");
				ele_strip.slideDown('300');
			}
			if(jQuery(this).find(".strip_grid").length > 0)
			{
				var ele_strip = jQuery(this).find(".strip_grid");
				ele_strip.slideDown('300');
			}
			*/
			jQuery(this).css('border-radius','5px 5px 5px 5px')
			.css('box-shadow','0 0 10px rgba(0,0,0,0.35)')
			.css('opacity','1')
			
	    });
		jQuery('.deal_blk').live('mouseleave',function(){
			if(jQuery(this).find(".strip").length > 0)
			{
				var ele_strip = jQuery(this).find(".strip");
				ele_strip.slideUp('300');
			}
			if(jQuery(this).find(".strip_grid").length > 0)
			{
				var ele_strip = jQuery(this).find(".strip_grid");
				ele_strip.slideUp('300');
			} 

			jQuery('.deal_blk').each(function(){
			   jQuery(this).css('opacity','1'); 
			});
			//jQuery(this).css('background','none repeat scroll 0 0 #FFFFFF')
			jQuery(this).css('border-radius','')
			jQuery(this).css('box-shadow','')
		
		});
	
	// 25-03-2014 end deal block hover reserve redeem tooltip
	
    }
	}catch(e)
	{
		//alert(e);
	}
}
bind_hover_effect(); 

try{
jQuery("#shareit-field",window.parent.document).keyup(function(){
   var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {
          
          
        
		  selected_cat_id = getCookie("cat_remember");
		miles_cookie = getCookie("miles_cookie");
			filter_locations(selected_cat_id,miles_cookie);
			               var firstlocid = "";
	 
	
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(textval.css("display") == "block")
		{
			new_locid = textval.attr("camp_locid")
		}
	});
	var flag= "no";
	
	jQuery(".location_tool").each(function() {
		if(textval.css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = textval.attr("locid");
			}
			if(new_locid != 0)
			{	
			if(new_locid == textval.attr("locid") )
			{
				flag = "yes";
			}
			}
		}
		
		
	});
	


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
	 var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
	 if(!iOS)
	 {
		jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		}
		/* display marker */
}, 500);		
});

jQuery("#fltr_category_close").click(function(){
 var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {
	//alert(jQuery("#fltr_category").text());
	if(jQuery("#fltr_category").text()=="All Categories")
	{
		//alert("default category");
		//jQuery(this).find("img").css("cursor","text");
		
	}
	else
	{
		textval.find("img").css("cursor","text");
		jQuery("#fltr_category_close").find("img").attr("src","templates/images/filter_close_grey.png");
		//alert(jQuery(".selected_miles").attr("mval"));
		jQuery("#fltr_category").text("All Categories");
		setCookie("cat_remember",0);
		jQuery(".btnfilterstaticscampaigns").each(function(index){
            jQuery(this).css("color","#3B3B3B");
        });
	
	
     jQuery("#category_slider li img").each(function(){
		var myimg=jQuery(this);
		var main_image=myimg.attr("main_image");
		myimg.attr("src",main_image);
	});
	
	
		jQuery(".btnfilterstaticscampaigns").each(function(index){
			if(jQuery(this).attr("mycatid")=="0")
			{
				jQuery(this).addClass("current");
				   jQuery(this).css('color','orange');
				   jQuery(this).find("img").attr("src",jQuery(this).find("img").attr("active_image"));
			}
			else
			{
				jQuery(this).removeClass("current");
			}
		});	
		
		
		
		var mile_val=jQuery(".selected_miles").attr("mval");
		//filter_deals_algorithm(0,mile_val);
		filter_locations(0,mile_val);
		var firstlocid = "";
	 
	
	var new_locid =0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	
	var flag= "no";
	if(new_locid == 0)
	{
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
		}
	});
	}


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
	//	jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		var aplyview=getCookie("view");
		////alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
	}
	//5-11-2013 with font without image
	jQuery("#fltr_category_close").removeClass("filterimage");
	//5-11-2013 with font without image
	},500);
});

jQuery("#fltr_mile_close").click(function(){
var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {
	//alert(jQuery("#fltr_mile").text());
	if(jQuery("#fltr_mile").text()=="50 Mi")
	{
		//alert("default mile");
		//jQuery(this).find("img").css("cursor","text");
	}
	else
	{
		textval.find("img").css("cursor","text");
		jQuery("#fltr_mile_close").find("img").attr("src","templates/images/filter_close_grey.png");
		//alert(getCookie("cat_remember"));
		jQuery("#fltr_mile").text("50 Mi");
		setCookie("miles_cookie","50",365);
		jQuery(".miles").each(function(){
			if(jQuery(this).attr("mval")=="50")
			{
				jQuery(this).addClass("selected_miles");
			}
			else
			{
				jQuery(this).removeClass("selected_miles");
			}
		});    		
		var cat_val=getCookie("cat_remember");
		//filter_deals_algorithm(cat_val,50);
		filter_locations(cat_val,50);
		var firstlocid = "";
	 
	
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	var flag= "no";
	if(new_locid == 0)
	{
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
		}
	});
	}

if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("clcick");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		var aplyview=getCookie("view");
		//alert(aplyview);
		
		//5-11-2013 with font without image
		jQuery("#fltr_mile_close").removeClass("filterimage");
		//5-11-2013 with font without image	
	}
	},500);
});
}
catch(e)
{
	//alert(e);
}
function filter_locations(category_id,miles){
try{
var is_expiringtoday= jQuery("#hdn_is_expiring_today").val();
var is_new= jQuery("#hdn_is_new_campaign").val();
var is_opennow = jQuery("#hdn_is_opennow").val();
var price = jQuery("#hdn_price").val();
var discount = jQuery("#hdn_discount").val();
var is_price_set = 1;
var is_discount_set = 1;

if(price == "")
{
	is_price_set = 0;
}
if(discount == "")
{
	is_discount_set = 0;
}
//alert("In filter location function"+category_id+"="+miles)
	
	
	// 28 01 2014
	//alert(jQuery(".newFilterControls.location_Category .chips .chip span").text());
	var location_category=jQuery(".newFilterControls.location_Category .chips .chip span").text();
	// 28 01 2014
	
	var text_ele = jQuery("#shareit-field");
	
	
	//alert(category_id+","+miles);
	//if(category_id != 0)
	//{
		 for (var prop in markerArray) {
		          if(prop != "indexOf") {
                  markerArray[prop].setIcon('./assets/images/c/pin-small.png');
                                     markerArray[prop].setVisible(false);
                  }
                            }
		var inc_scroll = 100;
		var setnull = 0;
		jQuery(".location_tool").each(function(){
			var str1= jQuery(this).find(".merchant_name").val();
				var str2= jQuery(this).find(".business_tag").val();
	var aString = text_ele.val();
	if(aString=="Filter by merchant name , product or services")
		aString="";
	var str= str1;
	var patt=new RegExp("\^"+aString,'i');
	var patt1=new RegExp("\[A-Za-z0-9]*"+aString,'i');
	//alert(jQuery(".location_tool[locid='"+  jQuery(this).attr("locid") +"'] .business_tag:contains('saloo')").length);
			all_categories = jQuery(this).attr("categories");
			arr = all_categories.split(",");
			
			all_expiring = jQuery(this).attr("t_l_e");
			all_discount = jQuery(this).attr("d_range");
			arr_expr = all_expiring.split(",");
			arr_discount = all_discount.split(",");
			arr_new_camp = (jQuery(this).attr("is_new")).split(",");
			
			milesaway = jQuery(this).attr("miles");
					//milesaway = 28;			
			if( category_id !=  0 )
			{
				if(arr.indexOf(category_id ) == -1  ) 
				{
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else{
				
				//alert(arr_new_camp.indexOf("1")+"=="+is_new);
				if(arr_expr.indexOf("1" ) == -1 && is_expiringtoday == 1  ) 
				{
					//alert("1");
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else if(jQuery(this).attr("o_c_status") == 0 && is_opennow == 1)
				{
					//alert("2");
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else if(arr_new_camp.indexOf("1" )  == -1 && is_new == 1)
				{
					//alert("3");
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else{
				//alert(4);
				if((parseInt(milesaway) <= miles && is_price_set == 1 && jQuery(this).attr("p_range") == price) || (parseInt(milesaway) <= miles && is_price_set != 1)) {
				
					if(patt.test(str1) || aString=="" || patt1.test(str2) ){	
							if(location_category!="")
							{
								//alert("if");							
								v = parseInt(jQuery(this).attr("locid"));
								//jQuery(".location_tool[locid='"+ v +"']:not(:contains('"+location_category+"'))" ).css("display","none");
								
								if (jQuery(".location_tool[locid='"+ v +"']:contains('"+location_category+"')").length > 0) 
								{
									if(v != "indexOf")
									markerArray[v].setVisible(true);
									jQuery(this).css("display","block");
									jQuery(this).attr("scroll",inc_scroll);
									inc_scroll =  inc_scroll+170;
									var location_categories = jQuery(this).attr("categories");
									var loc_arr = location_categories.split(",");
									var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var new_campaign_arr = (jQuery(this).attr("is_new")).split(",");
									var no_times = 0;
									var levl_arr = (jQuery(this).attr("levels")).split(",");
									
									for(i=0;i<loc_arr.length;i++)
									{
										if( is_expiringtoday== 1 )
										{
													if(loc_arr[i] == category_id && expr_arr[i]==1)
													{
														if(is_opennow == 1)
														{
															if( jQuery(this).attr("o_c_status")==1)
															{ 
																if(is_new == 1)
																{
																	if(new_campaign_arr[i] == 1)
																	{
																	  if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																		
																	}
																	
																}
																else{
																
															//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																}
															}
														}
														else
														{
															if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
														}
													}
											//
										}
										else if(is_opennow == 1)
										{
											if(loc_arr[i] == category_id && jQuery(this).attr("o_c_status")==1)
											{
											
													if(is_new == 1)
													{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
																	
																}
																else{
																
															//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
												}
													
											}
										}
										else if(is_new == 1)
										{
														if(loc_arr[i] == category_id && new_campaign_arr[i] == 1)
														{
															if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
														}
														
													}
										else if(is_discount_set == 1)
										{
														if(loc_arr[i] == category_id && arr_discount[i] == discount)
														{
															
															if((jQuery(this).find(".subscribestore").length == 1) )
															{
																if(parseInt(levl_arr[i]) != 2)
																{
																	no_times++;
																}
															}
															else
															{
																no_times++;
															}
																			
														}
														
										}
										else{
										if(loc_arr[i] == category_id )
										{
											//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm else condition");
												if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
											}
										}
									}
									if(no_times == 0)
									{
										jQuery(this).css("display","none");
										jQuery(this).attr("scroll",setnull);
									}
									jQuery(this).find(".loca_total_offers span").text(no_times+" Offers")
									//	alert("In block"+milesaway+"="+miles);
								} 
								else 
								{
									jQuery(this).css("display","none");
									jQuery(this).attr("scroll",setnull);
								}
								
							}
							else
							{
								//alert("else");
								v = parseInt(jQuery(this).attr("locid"));	
								if(v != "indexOf")
								markerArray[v].setVisible(true);
								jQuery(this).css("display","block");
								jQuery(this).attr("scroll",inc_scroll);
								inc_scroll =  inc_scroll+170;
								var location_categories = jQuery(this).attr("categories");
									var loc_arr = location_categories.split(",");
									var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var new_campaign_arr = (jQuery(this).attr("is_new")).split(",");
									var no_times = 0;
									var levl_arr = (jQuery(this).attr("levels")).split(",");
									for(i=0;i<loc_arr.length;i++)
									{
										if( is_expiringtoday== 1 )
										{
											if(loc_arr[i] == category_id && expr_arr[i]==1)
											{
												if(is_opennow == 1)
												{
																if(is_new == 1)
																{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
																	
																}
																else{
																
															//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																}
												}
												else
												{
													if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
												}
											}
										}
										else if(is_opennow == 1)
										{
											if(loc_arr[i] == category_id && jQuery(this).attr("o_c_status")==1)
											{
												if(is_new == 1)
																{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
																	
																}
																else{
																
															//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
														//	alert(is_discount_set+"=="+discount);
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																}
											}
										}
										else if(is_new == 1)
										{
														if(loc_arr[i] == category_id && new_campaign_arr[i] == 1)
														{
															if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
														}
														
										}
										else if(is_discount_set == 1)
										{
														if(loc_arr[i] == category_id && arr_discount[i] == discount)
														{
															
																	if((jQuery(this).find(".subscribestore").length == 1) )
																	{
																		if(parseInt(levl_arr[i]) != 2)
																		{
																			no_times++;
																		}
																	}
																	else
																	{
																		no_times++;
																	}
																			
														}
														
										}
										else{
										//	alert("In else:");
											if(loc_arr[i] == category_id )
											{
										//	alert(category_id);
											//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm else condition");
											//alert(jQuery(this).find(".unsubscribestore").length+"==="+levl_arr[i]);
												if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
											}
										}
										
									}
									jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									if(no_times == 0)
									{
										jQuery(this).css("display","none");
										jQuery(this).attr("scroll",setnull);
										v = parseInt(jQuery(this).attr("locid"));	
								if(v != "indexOf")
								markerArray[v].setVisible(false);
									}
								//	alert("In block"+milesaway+"="+miles);
							}
							
							// 28 01 2014
							}
							else{
						jQuery(this).css("display","none");
						jQuery(this).attr("scroll",setnull);
						//alert("In display none"+milesaway+"="+miles);
					}
					}else{
						jQuery(this).css("display","none");
						jQuery(this).attr("scroll",setnull);
					}
				}
				}
				//
			}
			else{
			//alert("in all category");
		//	alert(jQuery(this).attr("o_c_status")+"==="+is_opennow);
			if(arr_expr.indexOf("1" ) == -1 && is_expiringtoday== 1  ) 
				{
				//	alert("a");
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else if(jQuery(this).attr("o_c_status") == 0 && is_opennow == 1)
				{
				//alert("b");
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else if(arr_new_camp.indexOf("1" )  == -1 && is_new == 1)
				{
					//alert("3");
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else{
				//alert("c");
				//alert(is_price_set+"=is price set ="+price+"= price value ="+ jQuery(this).attr("p_range"));
				//alert(parseInt(milesaway)+"=="+miles);
				if( parseInt(milesaway) > miles ) 
				{
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else if(is_price_set == 1 && price != jQuery(this).attr("p_range"))
				{
			//	alert("e");
					//alert("in else if");
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else{
				//alert("1");
			
					if(patt.test(str1)|| aString=="" || patt1.test(str2)){	
				//alert("2");	
						// 28 01 2014
							
							if(location_category!="")
							{
							//alert("3");
								//alert("if");							
								v = parseInt(jQuery(this).attr("locid"));
								//jQuery(".location_tool[locid='"+ v +"']:not(:contains('"+location_category+"'))" ).css("display","none");
								
								if (jQuery(".location_tool[locid='"+ v +"']:contains('"+location_category+"')").length > 0) 
								{
								//alert("4");
									if(v != "indexOf")
									markerArray[v].setVisible(true);
									jQuery(this).css("display","block");
									jQuery(this).attr("scroll",inc_scroll);
									inc_scroll =  inc_scroll+170;
									var levl_arr = (jQuery(this).attr("levels")).split(",");
									/* count offers left */
									var location_expires = jQuery(this).attr("t_l_e");
									var new_campaign_arr = (jQuery(this).attr("is_new")).split(",");
									
									var expr_arr = location_expires.split(",");
									var no_times = 0;
									//alert("in here");
									if( is_expiringtoday== 1 )
									{
									//alert("5");
									//alert("in expiring condition");
										if(is_opennow == 1)
										{
										//alert("6");
											if(jQuery(this).attr("o_c_status") == 1)
											{	
											//alert("7");
												for(i=0;i<expr_arr.length;i++)
												{
														if(expr_arr[i]==1)
														{
															if(is_new == 1)
															{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
															}
															else
															{
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
															}
														}
													
												}
											}
										}
										else
										{
										//alert("8");
											for(i=0;i<expr_arr.length;i++)
												{
														if(expr_arr[i]==1)
														{
														
															if(is_new == 1)
															{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
															}
															else
															{
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
															}
														}
													
												}
										}
										
										if(no_times == 0)
										{
										//alert("9");
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
									if(v != "indexOf")
									markerArray[v].setVisible(false);
										}
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									
									}
									else if(is_opennow == 1)
									{
									//alert("10");
										if(jQuery(this).attr("o_c_status") == 1)
										{	
											//alert("11");
											for(i=0;i<levl_arr.length;i++)
												{
														if(is_new == 1)
															{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
															}
															else
															{
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
															}
												}
											var location_categories = jQuery(this).attr("categories");
											var loc_arr = location_categories.split(",");
											jQuery(this).find(".loca_total_offers span").text(loc_arr.length+" Offers");
										}
										/*if(no_times == 0)
										{
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
									if(v != "indexOf")
									markerArray[v].setVisible(false);
										} */
										//jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									}
									else if(is_new == 1)
									{
										for(i=0;i<levl_arr.length;i++)
												{
											if(new_campaign_arr[i] == 1)
											{
												if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
											}
											}
											if(no_times == 0)
										{
										//alert("19");
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
									if(v != "indexOf")
									markerArray[v].setVisible(false);
										}
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									}
									else if(is_discount_set == 1)
									{
										for(i=0;i<levl_arr.length;i++)
												{
											
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  
											
											}
											if(no_times == 0)
										{
										//alert("19");
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
									if(v != "indexOf")
									markerArray[v].setVisible(false);
										}
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									}
									else{
								//	alert("12");
								for(i=0;i<levl_arr.length;i++)
										{
												if((jQuery(this).find(".subscribestore").length == 1) )
												{
													if(parseInt(levl_arr[i]) != 2)
													{
														no_times++;
													}
												}
												else
												{
													no_times++;
												}
										}

									//alert("no of times"+no_times);
										if(no_times == 0)
										{
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
											if(v != "indexOf")
											markerArray[v].setVisible(false);
										}
										else
										{
										var location_categories = jQuery(this).attr("categories");
										var loc_arr = location_categories.split(",");
										jQuery(this).find(".loca_total_offers span").text(loc_arr.length+" Offers");
										}
									}
									/* offers left */
									//	alert("In block"+milesaway+"="+miles);
								} 
								else 
								{
								//alert("13");
									jQuery(this).css("display","none");
									jQuery(this).attr("scroll",setnull);
								}
								
							}
							else
							{
							//alert("14");
								//alert("else");
								v = parseInt(jQuery(this).attr("locid"));	
								if(v != "indexOf")
								markerArray[v].setVisible(true);
								jQuery(this).css("display","block");
								jQuery(this).attr("scroll",inc_scroll);
								inc_scroll =  inc_scroll+170;
								/* count offers left */
								var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var new_campaign_arr = (jQuery(this).attr("is_new")).split(",");
									var no_times = 0;
									var levl_arr = (jQuery(this).attr("levels")).split(",");
									if( is_expiringtoday== 1 )
									{
								//	alert("15");
									//alert("in expiring condition");
										if(is_opennow == 1)
										{
										//alert("16");
											if(jQuery(this).attr("o_c_status") == 1)
											{	
											//alert("17");
												for(i=0;i<expr_arr.length;i++)
												{
												
														if(expr_arr[i]==1)
														{
															if(is_new == 1)
															{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
															}
															else
															{
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
															}
														}
													
												}
											}
										}
										else
										{
										//alert("18");
											for(i=0;i<expr_arr.length;i++)
												{
														if(expr_arr[i]==1)
														{
															if(is_new == 1)
															{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
															}
															else
															{
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
															}
														}
													
												}
										}
										
										if(no_times == 0)
										{
										//alert("19");
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
									if(v != "indexOf")
									markerArray[v].setVisible(false);
										}
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									
									}
									else if(is_opennow == 1)
									{
									//alert("20");
										if(jQuery(this).attr("o_c_status") == 1)
										{	
										//alert("21");
										for(i=0;i<levl_arr.length;i++)
												{
														if(is_new == 1)
															{ 
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
															}
															else
															{
														//	alert(is_discount_set+"=="+discount+"=="+arr_discount[i]);
															
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
															}
														}
														var location_categories = jQuery(this).attr("categories");
										var loc_arr = location_categories.split(",");
										jQuery(this).find(".loca_total_offers span").text(loc_arr.length+" Offers");
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
											
										}
										if(no_times == 0)
										{
										//alert("22");
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
									if(v != "indexOf")
									markerArray[v].setVisible(false);
										}
										
										//jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									}
									else if(is_new == 1)
									{
								
										for(i=0;i<levl_arr.length;i++)
												{
								
											if(new_campaign_arr[i] == 1)
											{
												if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
											}
											}
										if(no_times == 0)
										{
										//alert("19");
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
									if(v != "indexOf")
									markerArray[v].setVisible(false);
										}
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									}
									else if(is_discount_set == 1)
									{
									//	alert(is_discount_set+"==="+levl_arr.length+"subscribed length");
										for(i=0;i<levl_arr.length;i++)
												{
								
																		//alert(arr_discount[i] +"=="+ discount+"==discount");
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																						//alert(no_times + "no of times");
																					}
																				}
																				else
																				{
																				
																					no_times++;
																					//alert(no_times+ "no of times");
																				}
																			}
												
											}
										if(no_times == 0)
										{
										//alert("19");
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
									if(v != "indexOf")
									markerArray[v].setVisible(false);
										}
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									}
									else{
									
										for(i=0;i<levl_arr.length;i++)
										{
										//alert("in each for loop "+jQuery(this).attr("locid")+"==="+levl_arr[i]+"=="+(jQuery(this).find(".subscribestore").length ));
												if(is_new == 1)
															{
																	if(new_campaign_arr[i] == 1)
																	{
																		if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
																	}
															}
															else
															{
																if(is_discount_set == 1)
																	  {
																			if(arr_discount[i] == discount)
																			{
																				if((jQuery(this).find(".subscribestore").length == 1) )
																				{
																					if(parseInt(levl_arr[i]) != 2)
																					{
																						no_times++;
																					}
																				}
																				else
																				{
																					no_times++;
																				}
																			}
																	  }
																	  else{
																
																		//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
																			if((jQuery(this).find(".subscribestore").length == 1) )
																			{
																				if(parseInt(levl_arr[i]) != 2)
																				{
																					no_times++;
																				}
																			}
																			else
																			{
																				no_times++;
																			}
																		}
															}
										}
									
									//alert("no of times"+no_times);
									
										if(no_times == 0)
										{
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
											if(v != "indexOf")
											markerArray[v].setVisible(false);
										}
										else
										{
										/*var location_categories = jQuery(this).attr("categories");
										var loc_arr = location_categories.split(",");
										jQuery(this).find(".loca_total_offers span").text(loc_arr.length+" Offers");*/
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
										}
									}
									/* offers left *///	alert("In block"+milesaway+"="+miles);
							}
							
							// 28 01 2014
					
						/*
							v = parseInt(jQuery(this).attr("locid"));
						  if(v != "indexOf")
							markerArray[v].setVisible(true);
								jQuery(this).css("display","block");
								jQuery(this).attr("scroll",inc_scroll);
							inc_scroll =  inc_scroll+170;
								var location_categories = jQuery(this).attr("categories");
								var loc_arr = location_categories.split(",");
								jQuery(this).find(".loca_total_offers span").text(loc_arr.length+" Offers")
					
						*/
					
							}
							else{
							//alert("23");
						jQuery(this).css("display","none");
						jQuery(this).attr("scroll",setnull);
					}
				}		
				}
			}
		});
		
		var total_lacations = 0;
		jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
				total_lacations= total_lacations+1;
		}
	});
     
	
	//jQuery(".searchdeal_offers").css("display","block");
	if(parseInt(total_lacations) == 0)
	{
	//clearTimeout(timer1);
	
	//alert("In if");
		infowindow.close();
		
		//alert("=="+jQuery("#imagek").css("width")+"===="+jQuery("#imagek").width());
		//if(jQuery("#imagek").css("width") == "0px")
		//{
			//if(jQuery("#imagek").css("opacity") != 1)
			//		{
		$('.slider-carriage').stop(false, false).animate({
            left: (-100 * $('#course').position().left / $('.slider-viewport').width()) + '%'
        }, 500,function(){
		});
		jQuery(".searchdeal_offers").css("display","none");
		//alert(jQuery("#category_slider .current[mycatid='"+category_id+"'] span").text());
		var category_text = jQuery("#category_slider .current[mycatid='"+category_id+"'] span").text();
		jQuery(".div_msg").html(not_found_msg);
		jQuery(".div_msg").find("#span_miles").text(miles);
		//alert(jQuery("#fltr_category").text());
		if(category_text=="All Categories")
			jQuery(".div_msg").find("#span_category").text(category_text);
		else
			jQuery(".div_msg").find("#span_category").text(category_text+ " category");
		jQuery(".info").css("display","none");
           // jQuery(".flip_map").css("display","block");
		jQuery(".flip_map").css("display","none");
		jQuery(".flip_map_loc").css("display","none");
		jQuery(".location_tool").each(function(){
			jQuery(this).find(".loca_total_offers span").css("display","block");
		});
		//}
	}
	else{
	//if($("#imager").css("opacity") != 1)
	//{
	/*var margin = $("#imager").width()/2;
	var width=$("#imager").width();
	var height=$("#imager").height(); */
	
		//alert(margin+"---"+width+"---"+height+"/"+margin1+"---"+width1+"---"+height1);
		
		
		$('.slider-carriage').stop(false, false).animate({
            left: (-100 * $('#camp').position().left / $('.slider-viewport').width()) + '%'
        },500,function(){
		//alert("COMPLETE 1");
		jQuery(".flip_map").css("display","none");
		jQuery(".loca_total_offers span").css("display","block");
		jQuery(".flip_map_loc").css("display","none");
		
		//}
                         jQuery(".div_msg").html("");
						 jQuery(".info").css("display","block");
						// alert("COMPLETE 2");
						if(parseInt(total_lacations) == 0)
						{
							jQuery(".searchdeal_offers").css("display","none");
						}
						else
						{
							jQuery(".div_msg").html("");
										 jQuery(".info").css("display","block");
						} 
		});
		
	
                    }
					//if(jQuery("#imagek").css("opacity") != 1)
	//{
	//jQuery(".searchdeal_offers").css("display","block");
	/*	*/
	//}
                    if(total_lacations == 1)
                        {
                            google.maps.event.trigger(map, 'resize');
                            map.setZoom(10);
                        }
                        else if(total_lacations > 0)
            {
         var latlngbounds = new google.maps.LatLngBounds();
         //alert(latlngbounds);
			for (var prop in markerArray)
			{
				if(prop != "indexOf")
				{
                                    
                                    if(jQuery(".location_tool[locid='"+prop+"']").css("display") != "none")
                                        {
                                            
                                             var latlng2 = new google.maps.LatLng(markerArray[prop].position.lat(), markerArray[prop].position.lng());
                                            latlngbounds.extend(latlng2);
                                        }
				}
			}	
                map.setCenter(latlngbounds.getCenter());
				 map.fitBounds(latlngbounds);	
            }
		jQuery(".location_tool").each(function(){
			jQuery(this).removeClass("current_loc");
			jQuery(this).find(".flip_map_loc").css("display","none");
			jQuery(this).find(".loca_total_offers span").css("display","block");
			infowindow.close();
		});
		var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
		if(!iOS)
		{
		
	jQuery('.location').jScrollPane({
			horizontalGutter:5,
			verticalGutter:5,
			'showArrows': false,
                        mouseWheelSpeed: 50
                        
		});
		}
	//}
	//$( "body" ).bind( ".btnfilterstaticscampaigns", "click");
	}
	catch(e)
	{
		//alert(e);
	}
}
try
{
  $("body").on("click",".sortbuttondistance",function(){
		if(jQuery(this).attr("sorttye") == "asc" )
		{
			jQuery(this).attr("sorttye","desc");
		}
		else{
			jQuery(this).attr("sorttye","asc");
		}
		sort_distance(jQuery(this).attr("sorttye"));
		bind_hover_effect();
		bind_location_tool_effect();
		
  });
  $("body").on("click",".sortbuttonrating",function(){
	
	if(jQuery(this).attr("sorttye") == "asc" )
		{
			jQuery(this).attr("sorttye","desc");
		}
		else{
			jQuery(this).attr("sorttye","asc");
		}
		sort_rating(jQuery(this).attr("sorttye"));
		bind_hover_effect();
		bind_location_tool_effect();
  });
  }catch(e)
  {
	//alert(e);
  }
function sort_distance(type)
{
	// get array of elements
var myArray = $(".filterd_location .location_tool");
var count = 0;
$(".temp_location").html("");

//$(".temp_location").html();
// sort based on timestamp attribute
myArray.sort(function (a, b) {
    
    // convert to integers from strings
    a = parseFloat($(a).attr("miles"));
    b = parseFloat($(b).attr("miles"));
    count += 2;
    // compare
	//alert(a+"=="+b);
	if(type == "asc")
	{
		if(a > b) {
			return 1;
		} else if(a < b) {
			return -1;
		} else {
			return 0;
		}
	}
	else
	{
		if(a < b) {
			return 1;
		} else if(a > b) {
			return -1;
		} else {
			return 0;
		}

	}
});

// put sorted results back on page
//$(".filterd_location").html("");
$(".filterd_location").empty();
$(".filterd_location").html(myArray);

/* $.each(myArray, function(i, div){
	//alert();
      $(".filterd_location").append(div);     
  }); */


$("#calls").append(count+1);
}

function sort_rating(type)
{
	// get array of elements
var myArray = $(".filterd_location .location_tool");
var count = 0;
$(".temp_location").html("");

//$(".temp_location").html();
// sort based on timestamp attribute
myArray.sort(function (a, b) {
    
    // convert to integers from strings
    a = parseFloat($(a).attr("avg_rating"));
    b = parseFloat($(b).attr("avg_rating"));
    count += 2;
    // compare
	//alert(a+"=="+b);
	if(type == "asc")
	{
		if(a > b) {
			return 1;
		} else if(a < b) {
			return -1;
		} else {
			return 0;
		}
	}
	else
	{
		if(a < b) {
			return 1;
		} else if(a > b) {
			return -1;
		} else {
			return 0;
		}

	}
});

// put sorted results back on page
//$(".filterd_location").html("");
$(".filterd_location").empty();
$(".filterd_location").html(myArray);
$("#calls").append(count+1);
}
/*jQuery("#map_canvas").live("mouseleave",function(){
	//alert("mouse leave");
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	var total_lacations = 0;
		jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
				total_lacations= total_lacations+1;
		}
	});
	if(total_lacations != 0)
	{
	//alert(new_locid);
	lokid = new_locid;
	for (var prop in markerArray)
		{
			if(prop != "indexOf")
			{
				markerArray[prop].setIcon('./images/pin-small.png');
            }
        }
		
						
	//	var lokid=jQuery(this).attr("locid");
		infowindow.setContent(infowindowcontent[lokid]);
		markerArray[lokid].setIcon('./images/pin-small-blue.png'); 
		infowindow.open(map,markerArray[lokid]);
		}
}); */
try
{
jQuery(".filterbuttonopennow").live("click",function(){
   var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {

jQuery("#fltr_opennow").css("display","block");
jQuery("#fltr_opennow_close").css("display","block");
	jQuery("#hdn_is_opennow").val("1");
	selected_cat_id = getCookie("cat_remember");
	miles_cookie = getCookie("miles_cookie");
	filter_locations(selected_cat_id,miles_cookie);
	var firstlocid = "";
	var new_locid =0;
	
	var flag= "no";
	
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid");
			//flag = "yes";
		}
	});

	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//alert(firstlocid);
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
	//	jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		},500);
});
jQuery(".filterbuttonexpiringtoday").live("click",function(){
   var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {

jQuery("#fltr_expiring").css("display","block");
jQuery("#fltr_expiring_close").css("display","block");
	jQuery("#hdn_is_expiring_today").val("1");
	selected_cat_id = getCookie("cat_remember");
	miles_cookie = getCookie("miles_cookie");
	filter_locations(selected_cat_id,miles_cookie);
	var firstlocid = "";
	var new_locid =0;
	
	var flag= "no";
	
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid");
			//flag = "yes";
		}
	});

	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//alert(firstlocid);
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
	//	jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		},500);
});
jQuery("#fltr_expiring_close").live("click",function(){

   var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {
jQuery("#fltr_expiring").css("display","none");
jQuery("#fltr_expiring_close").css("display","none");

//jQuery("#span_category").text(jQuery(this).find('span').text());
			
	
	jQuery("#hdn_is_expiring_today").val("0");
	selected_cat_id = getCookie("cat_remember");
	miles_cookie = getCookie("miles_cookie");
	filter_locations(selected_cat_id,miles_cookie);
	var firstlocid = "";
	var new_locid =0;
	
	var flag= "no";
	
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid");
		//	flag = "yes";
		}
	});
	//alert(new_locid);
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//alert(firstlocid);
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		},500);
});
jQuery("#fltr_opennow_close").live("click",function(){

   var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {
jQuery("#fltr_opennow").css("display","none");
jQuery("#fltr_opennow_close").css("display","none");

//jQuery("#span_category").text(jQuery(this).find('span').text());
			
	
	jQuery("#hdn_is_opennow").val("0");
	selected_cat_id = getCookie("cat_remember");
	miles_cookie = getCookie("miles_cookie");
	filter_locations(selected_cat_id,miles_cookie);
	var firstlocid = "";
	var new_locid =0;
	
	var flag= "no";
	
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid");
		//	flag = "yes";
		}
	});
	//alert(new_locid);
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//alert(firstlocid);
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		},500);
});
jQuery("#fltr_price_close").live("click",function(){

var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {
		/* remove all selection */
		jQuery(".prices").each(function(){
			{
				jQuery(this).removeClass("selected_miles");
			}
		}); 
		/* remove all selection */
	jQuery("#hdn_price").val("");
	jQuery("#fltr_price_close").css("display","none");
	
	jQuery("#fltr_price").css("display","none");
	jQuery("#fltr_price").text("");
	   mile_val1 = getCookie("miles_cookie");
	 filter_locations(getCookie("cat_remember"),mile_val1);
	 /** 03-01-2014 ***/
	  		               var firstlocid = "";
	 
	
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	var flag= "no";
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
	/*** 03-01-2014 ****/
	var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
 },500);

});

jQuery("#fltr_discount_close").live("click",function(){

var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {
		/* remove all selection */
		jQuery(".discounts").each(function(){
			{
				jQuery(this).removeClass("selected_miles");
			}
		}); 
		/* remove all selection */
	jQuery("#hdn_discount").val("");
	jQuery("#fltr_discount_close").css("display","none");
	jQuery("#fltr_discount").css("display","none");
	jQuery("#fltr_discount").text("");
	   mile_val1 = getCookie("miles_cookie");
	 filter_locations(getCookie("cat_remember"),mile_val1);
	 /** 03-01-2014 ***/
	  		               var firstlocid = "";
	 
	
	var new_locid = 0;
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid")
		}
	});
	var flag= "no";
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
	/*** 03-01-2014 ****/
	var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
 },500);

});
}
catch(e)
{
	//alert(e);
}


function get_private_deals(category_id,miles ,query_location){
	try
	{
var is_expiringtoday= jQuery("#hdn_is_expiring_today").val();
var is_new= jQuery("#hdn_is_new_campaign").val();
var is_opennow = jQuery("#hdn_is_opennow").val();
var price = jQuery("#hdn_price").val();
var is_price_set = 1;
var no_times = 0;
if(price == "")
{
	is_price_set = 0;
}
//alert("In filter location function"+category_id+"="+miles)
	
	
	// 28 01 2014
	//alert(jQuery(".newFilterControls.location_Category .chips .chip span").text());
	var location_category=jQuery(".newFilterControls.location_Category .chips .chip span").text();
	// 28 01 2014
	
	var text_ele = jQuery("#shareit-field");
	
	
	//alert(category_id+","+miles);
	//if(category_id != 0)
	//{
	
		var inc_scroll = 100;
		var setnull = 0;
		jQuery(".location_tool[locid='"+ query_location +"'] ").each(function(){
			var str1= jQuery(this).find(".merchant_name").val();
				var str2= jQuery(this).find(".business_tag").val();
	var aString = text_ele.val();
	if(aString=="Filter by merchant name , product or services")
		aString="";
	var str= str1;
	var patt=new RegExp("\^"+aString,'i');
	var patt1=new RegExp("\[A-Za-z0-9]*"+aString,'i');
	//alert(jQuery(".location_tool[locid='"+  jQuery(this).attr("locid") +"'] .business_tag:contains('saloo')").length);
			all_categories = jQuery(this).attr("categories");
			arr = all_categories.split(",");
			
			all_expiring = jQuery(this).attr("t_l_e");
			
			arr_expr = all_expiring.split(",");
			arr_new_camp = (jQuery(this).attr("is_new")).split(",");
			
			milesaway = jQuery(this).attr("miles");
					//milesaway = 28;			
			if( category_id !=  0 )
			{
				if(arr.indexOf(category_id ) == -1  ) 
				{
				}
				else{
				//
				//alert(arr_expr.indexOf(1 )+"=="+is_expiringtoday);
				if(arr_expr.indexOf("1" ) == -1 && is_expiringtoday== 1  ) 
				{
				}
				else if(jQuery(this).attr("o_c_status") == 0 && is_opennow == 1)
				{
				}
				else{
				if((parseInt(milesaway) <= miles && is_price_set == 1 && jQuery(this).attr("p_range") == price) || (parseInt(milesaway) <= miles && is_price_set != 1)) {
					if(patt.test(str1) || aString=="" || patt1.test(str2) ){	
							// 28 01 2014
							if(location_category!="")
							{
								v = parseInt(jQuery(this).attr("locid"));
								//jQuery(".location_tool[locid='"+ v +"']:not(:contains('"+location_category+"'))" ).css("display","none");
								
								if (jQuery(".location_tool[locid='"+ v +"']:contains('"+location_category+"')").length > 0) 
								{
								
									var location_categories = jQuery(this).attr("categories");
									var loc_arr = location_categories.split(",");
									var level_arr = (jQuery(this).attr("levels")).split(",");
									var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
						
									for(i=0;i<loc_arr.length;i++)
									{
										if( is_expiringtoday== 1 )
										{
											if(loc_arr[i] == category_id && expr_arr[i]==1)
											{
												if(is_opennow == 1)
												{
													if( jQuery(this).attr("o_c_status")==1)
													{
														if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
													}
												}
												else
												{
														if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
												}
											}
										}
										else if(is_opennow == 1)
										{
											if(loc_arr[i] == category_id && jQuery(this).attr("o_c_status")==1)
											{
														 if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}

											}
										}
										else if(is_new == 1)
										{
											if(new_campaign_arr[i] == 1)
											{
												 if(parseInt(level_arr[i]) == 2)
												 {
											
												no_times++;
												}
											}
										}
														
										else{
											if(loc_arr[i] == category_id )
											{
											//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm else condition");
														 if(parseInt(level_arr[i]) == 2)
														 {
													//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
														no_times++;
														}

											}
										}
									}
									if(no_times == 0)
									{
									}
								} 
								else 
								{
								}
								
							}
							else
							{
								
								var location_categories = jQuery(this).attr("categories");
									var loc_arr = location_categories.split(",");
									var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var level_arr = (jQuery(this).attr("levels")).split(",");
											var new_campaign_arr = (jQuery(this).attr("is_new")).split(",");
									for(i=0;i<loc_arr.length;i++)
									{
										if( is_expiringtoday== 1 )
										{
											if(loc_arr[i] == category_id && expr_arr[i]==1)
											{
												if(is_opennow == 1)
												{
													if( jQuery(this).attr("o_c_status")==1)
													{
														 if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
													}
												}
												else
												{
													 if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
												}
											}
										}
										else if(is_opennow == 1)
										{
											if(loc_arr[i] == category_id && jQuery(this).attr("o_c_status")==1)
											{
												 if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
											}
										}
										else if(is_new == 1)
										{
											if(new_campaign_arr[i] == 1)
											{
												 if(parseInt(level_arr[i]) == 2)
												 {
														no_times++;
												}
											}
										}
										else{
									
											if(loc_arr[i] == category_id )
											{
												if(parseInt(level_arr[i]) == 2)
												{
												no_times++;
												}
											}
										}
										
									}
									if(no_times == 0)
									{
									}
								
							}
							
							// 28 01 2014
							}
							else{
						}
					}else{
						}
				}
				}
				//
			}
			else{
			
		//	alert(jQuery(this).attr("o_c_status")+"==="+is_opennow);
			if(arr_expr.indexOf("1" ) == -1 && is_expiringtoday== 1  ) 
				{
				}
				else if(jQuery(this).attr("o_c_status") == 0 && is_opennow == 1)
				{
				}
				else{
				//alert("c");
				//alert(is_price_set+"=is price set ="+price+"= price value ="+ jQuery(this).attr("p_range"));
				//alert(parseInt(milesaway)+"=="+miles);
				if( parseInt(milesaway) > miles ) 
				{
				}
				else if(is_price_set == 1 && price != jQuery(this).attr("p_range"))
				{
			//	alert("e");
				}
				else{
				
			
					if(patt.test(str1)|| aString=="" || patt1.test(str2)){	
				
						// 28 01 2014
							
							if(location_category!="")
							{
								if (jQuery(".location_tool[locid='"+ v +"']:contains('"+location_category+"')").length > 0) 
								{
									
									var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var level_arr = (jQuery(this).attr("levels")).split(",");
									var new_campaign_arr = (jQuery(this).attr("is_new")).split(",");
									
									if( is_expiringtoday== 1 )
									{
										if(is_opennow == 1)
										{
									
											if(jQuery(this).attr("o_c_status") == 1)
											{	
									
												for(i=0;i<expr_arr.length;i++)
												{
														if(expr_arr[i]==1)
														{
														 if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
														}
													
												}
											}
										}
										else
										{
											for(i=0;i<expr_arr.length;i++)
												{
														if(expr_arr[i]==1)
														{
														
														  if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
														}
													
												}
										}
										
										if(no_times == 0)
										{
											
										}
									}
									else if(is_opennow == 1)
									{
										if(jQuery(this).attr("o_c_status") == 1)
										{	
											for(i=0;i<level_arr.length;i++)
											{
														 if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
												}
											
									}
									}
									else  if(is_new == 1)
									{
										for(i=0;i<level_arr.length;i++)
											{
										if(new_campaign_arr[i] == 1)
										{
									 if(parseInt(level_arr[i]) == 2)
									 {
								
									no_times++;
									}
										}
										}
									}
														
									else{
										for(i=0;i<level_arr.length;i++)
												{
															if(parseInt(level_arr[i]) == 2)
															 {
														//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
															no_times++;
															}
													}
										}
									/* offers left */
								
								} 
								else 
								{
								}
								
							}
							else
							{
								//alert("else");
								/* count offers left */
								var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var level_arr = (jQuery(this).attr("levels")).split(",");
									 var new_campaign_arr = (jQuery(this).attr("is_new")).split(",");						
									if( is_expiringtoday== 1 )
									{
									//alert("in expiring condition");
										if(is_opennow == 1)
										{
											if(jQuery(this).attr("o_c_status") == 1)
											{	
												for(i=0;i<expr_arr.length;i++)
												{
												
														if(expr_arr[i]==1)
														{
															 if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
														}
													
												}
											}
										}
										else
										{
											for(i=0;i<expr_arr.length;i++)
												{
														if(expr_arr[i]==1)
														{
														if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
														}
													
												}
										}
										
										if(no_times == 0)
										{
										}
									}
									else if(is_opennow == 1)
									{
										if(jQuery(this).attr("o_c_status") == 1)
										{	
												for(i=0;i<level_arr.length;i++)
												{
															if(is_new == 1)
														{
															if(new_campaign_arr[i] == 1)
															{
														 if(parseInt(level_arr[i]) == 2)
														 {
													
														no_times++;
														}
															}
														}
														else
														{
															if(parseInt(level_arr[i]) == 2)
															 {
														
															no_times++;
															}
														}
													}

															
										}
										if(no_times == 0)
										{
										}
									}
									else  if(is_new == 1)
									{
										for(i=0;i<level_arr.length;i++)
											{
										if(new_campaign_arr[i] == 1)
										{
									 if(parseInt(level_arr[i]) == 2)
									 {
								
									no_times++;
									}
										}
										}
									}
									else{
												for(i=0;i<level_arr.length;i++)
												{
															if(parseInt(level_arr[i]) == 2)
															 {
														//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
															no_times++;
															}
													}
	
										}
									
							}
						
					
							}
							else{
					}
				}		
				}
			}
		});
		//alert(no_times);
return no_times;		
}
catch(e)
{
	//alert(e);
	
}
}
try{
jQuery(".filternewcampaigns").live("click",function(){
	//alert("In filter new campaign click");
	  var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {

jQuery("#fltr_newcampaign").css("display","block");
jQuery("#fltr_newcampaign_close").css("display","block");
	jQuery("#hdn_is_new_campaign").val("1");
	selected_cat_id = getCookie("cat_remember");
	miles_cookie = getCookie("miles_cookie");
	filter_locations(selected_cat_id,miles_cookie);
	var firstlocid = "";
	var new_locid =0;
	
	var flag= "no";
	
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid");
			//flag = "yes";
		}
	});

	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//alert(firstlocid);
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
	//	jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		},500);
});
jQuery("#fltr_newcampaign_close").live("click",function(){

   var textval = jQuery(this); // copy of this object for further usage
        
        if (timeoutReference) clearTimeout(timeoutReference);
        timeoutReference = setTimeout(function() {
jQuery("#fltr_newcampaign").css("display","none");
jQuery("#fltr_newcampaign_close").css("display","none");

//jQuery("#span_category").text(jQuery(this).find('span').text());
			
	
	jQuery("#hdn_is_new_campaign").val("0");
	selected_cat_id = getCookie("cat_remember");
	miles_cookie = getCookie("miles_cookie");
	filter_locations(selected_cat_id,miles_cookie);
	var firstlocid = "";
	var new_locid =0;
	
	var flag= "no";
	
	jQuery(".searchdeal_offers .campaignlist").each(function(){
		if(jQuery(this).css("display") == "block")
		{
			new_locid = jQuery(this).attr("camp_locid");
		//	flag = "yes";
		}
	});
	//alert(new_locid);
	
	jQuery(".location_tool").each(function() {
		if(jQuery(this).css("display") != "none" )
		{
			if(firstlocid == "")
			{
			firstlocid = jQuery(this).attr("locid");
			}
			if(new_locid != 0)
			{
			if(new_locid == jQuery(this).attr("locid") )
			{
				flag = "yes";
			}
			}
		}
	});


if(flag == "yes")
{
	firstlocid = new_locid;
}
//jQuery("#hdn_is_offer_div_click").val("0");
//alert(firstlocid);
//jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		//jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
		},500);
});
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
$(document).on("click touchstart", function (e) {
      if(jQuery("#LocationFancyFrontDivProcessing").css("display") == "block")
  {
 
		if(((e.target.className) == "divBack"))
		{
			close_popup('LocationFancy');
		}
		else
		{
			
		}
  }
	//if((e.target.className).trim() == "actiontd")
      //  alert((e.target.className) );
	  if((e.target.className) ==  "discount-box")
    {

    }
	if((e.target.className) ==  "discount-box")
    {

    }
    if((e.target.className) ==  "price-box")
    {

    }
    else if((e.target.className) == "filterbuttondistance" )
    {
    
    }
    else
        {
           /* jQuery('#shareit-box').css("display","none");
			jQuery('#price-box').css("display","none");
			jQuery('#filterpriceidarrow').css("display","none");
			jQuery('#filterdistanceidarrow').css("display","none");
			 $("#shareit-box").attr("disp","0");
			 $("#price-box").attr("disp","0");
			 $("#filterpriceidarrow").attr("disp1","0");
                         $("#filterdistanceidarrow").attr("disp1","0");
         */
                       // $("#shareit-box").hide();
						   
                     //   $("#filterdistanceidarrow").hide();
        }
  
});
}
else{
jQuery("body").click
(function(e)
  {
      
 
  if(jQuery("#LocationFancyFrontDivProcessing").css("display") == "block")
  {
 
		if(((e.target.className) == "divBack"))
		{
			close_popup('LocationFancy');
		}
		else
		{
			
		}
  }
	//if((e.target.className).trim() == "actiontd")
    if((e.target.className) == "filterbuttondistance" )
    {
    
    }
    else
        {
            jQuery('#shareit-box').css("display","none");
			jQuery('#price-box').css("display","none");
			jQuery('#discount-box').css("display","none");
			jQuery('#filterpriceidarrow').css("display","none");
			jQuery('#filterdistanceidarrow').css("display","none");
			jQuery("#filterdiscountidarrow").css("display","none");
			 $("#shareit-box").attr("disp","0");
			 $("#price-box").attr("disp","0");
			 $("#discount-box").attr("disp","0");
			 $("#filterpriceidarrow").attr("disp1","0");
			 $("#filterdiscountidarrow").attr("disp1","0");
                       // $("#shareit-box").hide();
						   $("#filterdistanceidarrow").attr("disp1","0");
                     //   $("#filterdistanceidarrow").hide();
        }
  }
);
}
}
catch(e)
{
	//alert(e);
}
