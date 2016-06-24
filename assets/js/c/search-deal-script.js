/*
Onclick clean input field 
*/
//jQuery(document).ready(function(){
    var not_found_msg = jQuery('#hdn_error_message').html();

//});

/*
function try1(val,campid,locid) {
            //alert("In"+campid+"=="+locid);
            for (var prop in markerArray) {
                                     markerArray[prop].setIcon('./images/pin-small.png');
                            }
           
            infowindow.setContent(infowindowcontent[locid]);
            markerArray[locid].setIcon('./images/pin-small-blue.png'); 
            infowindow.open(map,markerArray[locid]);
			
			// start for prev next
			var arr = [];
           var obj = {};

			obj['href'] = 'http://www.scanflip.com/popup_detail.php?CampTitle=Free+individual+dessert+with+individual+meal+purchase&businessname=Subway Restaurant&number_of_use=1&new_customer=0&address=121+King+Street+West&city=Toronto&state=ON&country=&zip=M5H 3X7&redeem_rewards=5&referral_rewards=10&o_left=2&expiration_date=2013-05-30 4:00:00 PM&img_src=http://www.scanflip.com/merchant/images/logo/campaign_1365378986.jpg&campid=529&locationid=96&deal_desc=Good at all participating Boston Market® restaurants. Present coupon when ordering.  Terms  One coupon per customer, per visit. Not valid with any other special, coupon, or reduced price offer. Not redeemable for cash or gift card purchases. Not applicable for home delivery, catering grocery';
			arr.push(obj);
			obj['href'] = 'http://www.scanflip.com/popup_detail.php?CampTitle=Free+individual+dessert+with+individual+meal+purchase&businessname=Subway Restaurant&number_of_use=1&new_customer=0&address=121+King+Street+West&city=Toronto&state=ON&country=&zip=M5H 3X7&redeem_rewards=5&referral_rewards=10&o_left=2&expiration_date=2013-05-30 4:00:00 PM&img_src=http://www.scanflip.com/merchant/images/logo/campaign_1365378986.jpg&campid=529&locationid=96&deal_desc=Good at all participating Boston Market® restaurants. Present coupon when ordering.  Terms  One coupon per customer, per visit. Not valid with any other special, coupon, or reduced price offer. Not redeemable for cash or gift card purchases. Not applicable for home delivery, catering grocery';
			arr.push(obj);
			
			var arr = [];

			var obj = {};
      obj['href'] =  $(val).attr('mypopupid');
      arr.push(obj);
      var org_text = $(val).attr('mypopupid');
    //  alert($(val).parent().parent('.deal_blk'));
     var index_of_cur = $(val).parent().parent('.deal_blk').index();
     var tot_element = ($('.mainul .deal_blk').length);
	 

	//alert(tot_element);
     for(var k = index_of_cur ;k < tot_element ;k++)
     {
         var obj = {};
        if(org_text != $('.mainul .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid'))
        {
         obj['href'] = $('.mainul .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid');
       //  alert($(this).find('.dealtitle').attr('mypopupid')); 
         arr.push(obj);
        }  
     }
     for(var k = 0 ;k < index_of_cur ;k++)
     {
         var obj = {};
        if(org_text != $('.mainul .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid'))
        {
         obj['href'] = $('.mainul .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid');
       //  alert($(this).find('.dealtitle').attr('mypopupid')); 
         arr.push(obj);
        }  
     }
			 
			// end for prev next
			parent.$.fancybox(arr,{
				//href: this.href,
				href: $(val).attr('mypopupid'),
				//content:#myDivID_3,
				width: 405,
				height: 345,
				type: 'iframe',
				openEffect : 'elastic',
				openSpeed  : 300,

				closeEffect : 'elastic',
				closeSpeed  : 300,
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				} // helpers
			}); // fancybox
		
}

*/
////a_href = a_href.replace(/(test_ref=)[^\&]+/, '$1' + updated_test_ref);
//function getParam( name )
//{
// name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
// var regexS = "[\\?&]"+name+"=([^&#]*)";
// var regex = new RegExp( regexS );
// var results = regex.exec( window.location.href );
// if( results == null )
//  return "";
//else
// return results[1];
//}

//var frank_param = getParam( 'pid' );
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
        filter_deals_algorithm(selected_cat_id,miles_cookie);
		
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
   $("body").on("click",".btnfilterstaticscampaigns",function(){
   
   
   
   jQuery("#fltr_category").text(jQuery(this).find('span').text());
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
	
	 
        $s(this).css('color','orange');
	var WEB_PATH = "<?=WEB_PATH?>";
	selected_cat_id = $s(this).attr("mycatid");
             jQuery(".displayul").html("");
			var cat_ele=$s(this);
			
       
			setCookie("cat_remember",selected_cat_id,365);
                       miles_cookie = getCookie("miles_cookie");
					   //alert(miles_cookie);
                       // alert(selected_cat_id);
                      
		      	      
                    filter_deals_algorithm(selected_cat_id,miles_cookie);
		    
		   
		var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
		/*
		else
		{			
			jQuery(".info").css("display","block");			
		}
		*/
		      
    });
   
			 
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
	if(jQuery(this).attr("mval")!="50")
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
	jQuery("#fltr_mile").text(jQuery(this).attr("mval")+" Mi");
    jQuery(".miles",window.parent.document).each(function(){
            jQuery(this).removeClass("selected_miles");
        });    
        jQuery(this).addClass("selected_miles");
    var mile_val = jQuery(this).attr("mval");
     setCookie("miles_cookie",mile_val,365);
	// alert("In document ready");
     filter_deals_algorithm(getCookie("cat_remember"),mile_val);
	 var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
 });
jQuery("body").on('click','.filter_campaigns_by',function(){
	//alert("hi");
	 filter_deals_algorithm_by_location(getCookie("cat_remember"),getCookie("miles_cookie"),jQuery(this).attr('filter_location_id'));
	//alert(jQuery(".info").css("display"));	
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
	
	// start scroll top 28-11-2013
	
	var p = jQuery('.deallistwrapper.displayul').position();
	var ptop = p.top;
	var pleft = p.left;
	jQuery(window).scrollTop(ptop);
	
	// end scroll top 28-11-2013
	
    });

});



jQuery("body").on("click",".unsubscribestore",function(){
var locid1 = $(this).attr("s_lid1");
    var ele = jQuery(this);
    //alert('btnRegisterStore=1&location_id='+$(this).attr("s_lid"));
     $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'btnunsubscribelocation=1&location_id='+$(this).attr("s_lid"),
                 // async:false,
		  success:function(msg)
		  {
                      
      //                alert(ele.text());
                     ele.text("Subscribe to store");
        //              alert(ele.text());
                      ele.removeClass("unsubscribestore");
                      ele.addClass("subscribestore");
                         
                             jQuery("#temp_infowiondow").html(infowindowcontent[locid1]);
                    var ele1 = jQuery("#temp_infowiondow .unsubscribestore");
                    ele1.text("Subscribe to store");
                    ele1.removeClass("unsubscribestore");
                      ele1.addClass("subscribestore");
      infowindowcontent[locid1] = jQuery("#temp_infowiondow").html();
			
                     // alert(1);
                     
					 //jQuery(".displayul .deal_blk[locid="+locid1+"][level=0]").css("display","none");
					 
					 
					 jQuery(".displayul .deal_blk[locid="+locid1+"][level=2]").each(function(){
							jQuery(this).detach();
					});
					 jQuery(".displayul_all .deal_blk[locid="+locid1+"][level=2]").each(function(){
							jQuery(this).detach();
					});
					  
					 
					 //ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                //     ele1.find(".tot_deal_counter").parent().detach();
					   
					   
					   selected_cat_id=getCookie("cat_remember");
                          
                       
                       miles_cookie=getCookie("miles_cookie");
                          
                       filter_deals_algorithm(selected_cat_id,miles_cookie);
					
					// 9-8-2013 to solve marker infowindow closed when unsubscribe
						infowindow.setContent(infowindowcontent[locid1]);
						markerArray[locid1].setIcon('./images/pin-small-blue.png'); 
						infowindow.open(map,markerArray[locid1]);
					// 9-8-2013
                  }
   });
});
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
                alert($(this).html());
                 $('.mainul').append($(this));
                // $('.displayul').append($(this));
                //li_maintain = li_maintain + $(this).html();
            });
           $('.displayul').append($('.mainul').html());
        } */
 bind_hover_effect();
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
                    filter_deals_algorithm(selected_cat_id,miles_cookie);
            
                    
}
function bind_hover_effect()
{
   jQuery('.deal_blk').hover(
    function(){
       // alert(jQuery(this).find(".strip").length);
       var aplyview=getCookie("view");
	   /*
		if(aplyview=="gridview")
		{
			if(jQuery(this).find(".strip_grid").length > 0)
			{
				var ele_strip = jQuery(this).find(".strip_grid");
				ele_strip.slideDown('300');
			}
		}
		else
		{
            if(jQuery(this).find(".strip").length > 0)
			{
				var ele_strip = jQuery(this).find(".strip");
				ele_strip.slideDown('300');
			}
        }
		*/
		if(jQuery(this).find(".strip").length > 0)
		{
			var ele_strip = jQuery(this).find(".strip");
			ele_strip.slideDown('300');
		}
        
        jQuery(this).css('border-radius','5px 5px 5px 5px')
        .css('box-shadow','0 0 10px rgba(0,0,0,0.35)')
        .css('opacity','1')
		
		for (var prop in markerArray)
		{
			if(prop != "indexOf")
			{
				markerArray[prop].setIcon('./images/pin-small.png');
            }
        }
							
		var lokid=jQuery(this).attr("locid");
		infowindow.setContent(infowindowcontent[lokid]);
		markerArray[lokid].setIcon('./images/pin-small-blue.png'); 
		infowindow.open(map,markerArray[lokid]);
    },
    function(){
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
	
	jQuery('.location_tool').hover(
    function(){
      
	  
		jQuery('.location_tool').css('opacity','1').css('border-radius','').css('box-shadow','');
		
        jQuery(this).css('border-radius','5px 5px 5px 5px')
        .css('box-shadow','0 0 10px rgba(0,0,0,0.35)')
        .css('opacity','1')
		
		for (var prop in markerArray)
		{
			if(prop != "indexOf")
			{
				markerArray[prop].setIcon('./images/pin-small.png');
            }
        }
							
		var lokid=jQuery(this).attr("locid");
		infowindow.setContent(infowindowcontent[lokid]);
		markerArray[lokid].setIcon('./images/pin-small-blue.png'); 
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
	
	
}
bind_hover_effect(); 
function filter_deals_algorithm(category,miles)
{
	//alert("in filter location"+category+"===="+miles+"====");
	//alert(miles);
     var m_counter= 0;
	//alert(category);
    jQuery(".arrow-up",window.parent.document).attr("disp1","0");
    jQuery(".arrow-up",window.parent.document).hide();
     jQuery("#shareit-box",window.parent.document).hide();
     jQuery("#shareit-box",window.parent.document).attr("disp","0");
    infowindow.close();
   /// alert(category+"=="+miles);
      var visible_location_arr = [];
       var campaign_arr = [];
       var counter_campaingn_arr = [];
       var counter_location_arr = [];
       var counter_merchant_arr = [];
       var all_merchant_arr = [];
       var all_locations = [];
       var c_l = new Array();
    var text_ele = jQuery("#shareit-field",window.parent.document)
	//alert(text_ele.val());
      var location_str = "";
                     //   alert(str);
                        str = "";
         	var selected_cat_id=category;
                var selected_miles=parseInt(miles);
//            
                 var alldealcnt = 0;
              //   alert(selected_cat_id);
                if(selected_cat_id != 0){
                    var temp_arr = [];
                var str = "";
                    //alert(jQuery(".displayul_all .deal_blk[catid='"+selected_cat_id+"']").length);
                    jQuery(".displayul_all .deal_blk[catid='"+selected_cat_id+"']").each(function(){
                      //        alert($(this).attr("locid"));
                       var str1=jQuery(this).find(".busi_name").text();
                   //   alert(str1+"==="+text_ele.val());
var aString = text_ele.val();
if(aString=="Filter By Merchant Name")
	aString="";
var str= str1;

var patt=new RegExp("\^"+aString,'i');
//document.write(patt.test(aString));
                      if(patt.test(str1)){
                           if(jQuery(this).attr("miles") <= selected_miles)
                           {
                           
                         location_str = "";                            
                     if(counter_merchant_arr.length == 0){
                
                     }
                     else{
                       
                     c_l =[];

                        if(all_merchant_arr.indexOf($(this).attr("merid")) == -1)
                        {

                       c_l =[];
                        }
                        else{
                            c_l = counter_merchant_arr[$(this).attr("merid")] ;
                          //  alert($(this).attr("merid")+"=="+c_l.length);

                        }
                     }
                
                    if(jQuery(".displayul_all .deal_blk[locid='"+$(this).attr("locid")+"'][catid='"+selected_cat_id+"']").length == 1)
                    {
                        
                          c_l[$(this).attr("locid")] = $(this).attr("campid");
                           if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                  if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                              location_str = location_str.replace("undefined","");
                            
                       counter_location_arr[$(this).attr("merid")]=location_str;
                    }
                    else{
                      
                        if(c_l.length == 0){
                        
                           c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                            if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                  if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                              location_str = location_str.replace("undefined","");
                            
                       counter_location_arr[$(this).attr("merid")]=location_str;
                        }
                        else{
                        
                            if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                            if(c_l.indexOf($(this).attr("campid")) == -1)
                            {
                               c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                              if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                 if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                                  
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                                 
                              location_str = location_str.replace("undefined","");
                          
                       counter_location_arr[$(this).attr("merid")]=location_str;
                            }
                            else{
                               var flag= 1;
                                jQuery(".displayul_all .deal_blk[locid='"+$(this).attr("locid")+"'][catid='"+selected_cat_id+"']").each(function(){
                                    if(c_l.indexOf(jQuery(this).attr("campid"))  == -1)
                                      {
                                          flag=0; 
                                      }
                                    
                                });
                                if(flag==1)
                                    {
                                         c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                              if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                 if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                                  
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                                 
                              location_str = location_str.replace("undefined","");
                          
                       counter_location_arr[$(this).attr("merid")]=location_str;
                                    }
                            }
                        }
                        }
                    }
                     if(all_merchant_arr.indexOf($(this).attr("merid")) == -1)
                        {
                            all_merchant_arr.push($(this).attr("merid"));
                        }
                     counter_merchant_arr[$(this).attr("merid")] = []; 


                          counter_merchant_arr[$(this).attr("merid")] =   c_l ;
                          str ="";
                  }
                      }
                 });
         
   var max_counter =0;
   var max_counter1 =0;
                for(i in counter_merchant_arr)
                {
                       temp_arr = counter_merchant_arr[i];
                    max_counter =0;
                        for(j in temp_arr)
                        { 
                            max_counter1++;
                          
                        }
                          if(max_counter < max_counter1){
                        max_counter = max_counter1;
                          }
                }

               //alert(all_merchant_arr.length); 
         jQuery(".displayul").html("");
                 
                      var loc_arr = new Array();
                    for(i=0;i<max_counter;i++)
                    {
                        for(k=0;k<all_merchant_arr.length;k++)
                        {
                            m_counter = 0;
                            mid= all_merchant_arr[k];
                             temp_arr = counter_merchant_arr[mid];
                            temp_arr_str = counter_location_arr[mid];
                             temp_arr_str = temp_arr_str.substring(0,(temp_arr_str.length-1));
                           //  alert(temp_arr_str);
                             loc_arr = temp_arr_str.split("-");
                           
                            if(loc_arr.length >= i+1){

                                         str = mid+"=="+ loc_arr[i]+"=="+temp_arr[loc_arr[i]]+"<br/>";
                                       tot_campaigns  = jQuery(".displayul_all .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][catid='"+selected_cat_id+"']").length;
                                     tot_campaigns = tot_campaigns-1;
                                            ele = jQuery(".displayul_all .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                            if($(".displayul .deal_blk").length>0)
                                                {
                                                    $(".displayul").append(ele.clone())
													
													// start 9 block navigation
													if($(".displayul .deal_blk").length<=9)
													{
														$(".navigationul").append(ele.clone());
													}
													// end 9 block navigation
													
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                    if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                                else{
                                                    $(".displayul").html(ele.clone());
													// start 9 block navigation
													$(".navigationul").html(ele.clone());
													// end 9 block navigation
													
																								
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                     if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                     
                                     
                            }
                            
                        }
                    }
               
                   
                     jQuery(".displayul .deal_blk").each(function(){
                          jQuery(this).css("display","block");
                            alldealcnt  = alldealcnt + 1;
                             if(visible_location_arr.indexOf(jQuery(this).attr("locid")) != 1)
                                             {
                                         visible_location_arr.push(jQuery(this).attr("locid"));
                                             }
                     });

                }
                else
                {
                    var location_str ="";
                    all_locations = [];
                    jQuery(".displayul_all .deal_blk").each(function(){
                         var str1=jQuery(this).find(".busi_name").text();
                   //   alert(str1+"==="+text_ele.val());
var aString = text_ele.val();
if(aString=="Filter By Merchant Name")
	aString="";
var str= str1;
var patt=new RegExp("\^"+aString,'i');
//document.write(patt.test(aString));
                      if(patt.test(str1)){
                             if(jQuery(this).attr("miles") <= selected_miles)
                           {
                   location_str = "";                            
                     if(counter_merchant_arr.length == 0){
                
                     }
                     else{
                       
                     c_l =[];

                        if(all_merchant_arr.indexOf($(this).attr("merid")) == -1)
                        {

                       c_l =[];
                        }
                        else{
                            c_l = counter_merchant_arr[$(this).attr("merid")] ;
                          //  alert($(this).attr("merid")+"=="+c_l.length);

                        }
                     }
                
                    if(jQuery(".displayul_all .deal_blk[locid='"+$(this).attr("locid")+"']").length == 1)
                    {
                        
                          c_l[$(this).attr("locid")] = $(this).attr("campid");
                          if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                  
                                   if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                              location_str = location_str.replace("undefined","");
                             
                       counter_location_arr[$(this).attr("merid")]=location_str;
                    }
                    else{
                      
                        if(c_l.length == 0){
                        
                           c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                           if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                  if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                              location_str = location_str.replace("undefined","");
                            
                       counter_location_arr[$(this).attr("merid")]=location_str;
                        }
                        else{
                         if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                            if(c_l.indexOf($(this).attr("campid")) == -1)
                            {
                               c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                              if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                 if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                                  
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                                 
                              location_str = location_str.replace("undefined","");
                          
                       counter_location_arr[$(this).attr("merid")]=location_str;
                            }
                            else{
                               var flag= 1;
                                jQuery(".displayul_all .deal_blk[locid='"+$(this).attr("locid")+"']").each(function(){
                                    if(c_l.indexOf(jQuery(this).attr("campid"))  == -1)
                                      {
                                          flag=0; 
                                      }
                                    
                                });
                                if(flag==1)
                                    {
                                         c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                              if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                 if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                                  
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                                 
                              location_str = location_str.replace("undefined","");
                          
                       counter_location_arr[$(this).attr("merid")]=location_str;
                                    }
                            }
                        }
                        }
                    }
                     if(all_merchant_arr.indexOf($(this).attr("merid")) == -1)
                        {
                            all_merchant_arr.push($(this).attr("merid"));
                        }
                     counter_merchant_arr[$(this).attr("merid")] = []; 


                          counter_merchant_arr[$(this).attr("merid")] =   c_l ;
                          str ="";
                           }
                      }
                 });


                var temp_arr = [];
                var str = "";
                 
                    jQuery(".displayul").html("");
                    var max_counter =0;
   var max_counter1 =0;
                for(i in counter_merchant_arr)
                {
                       temp_arr = counter_merchant_arr[i];
                    max_counter =0;
                        for(j in temp_arr)
                        { 
                            max_counter1++;
                          
                        }
                          if(max_counter < max_counter1){
                        max_counter = max_counter1;
                          }
                }

                var tot_campaigns =0;
         jQuery(".displayul").html("");
                 
                      var loc_arr = new Array();
                    for(i=0;i<max_counter;i++)
                    {
                        for(k=0;k<all_merchant_arr.length;k++)
                        {
                            m_counter = 0;
                            mid= all_merchant_arr[k];
                             temp_arr = counter_merchant_arr[mid];
                            temp_arr_str = counter_location_arr[mid];
                             temp_arr_str = temp_arr_str.substring(0,(temp_arr_str.length-1));
                         //   alert(temp_arr_str);
                             loc_arr = temp_arr_str.split("-");
                           
                            if(loc_arr.length >= i+1){

                                         str = mid+"=="+ loc_arr[i]+"=="+temp_arr[loc_arr[i]]+"<br/>"+(i);
                                  
                                     tot_campaigns  = jQuery(".displayul_all .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"']").length;
                                     tot_campaigns = tot_campaigns-1;
                                            ele = jQuery(".displayul_all .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                            if($(".displayul .deal_blk").length>0)
                                                {
                                                    $(".displayul").append(ele.clone())
													
													// start 9 block navigation
													if($(".displayul .deal_blk").length<=9)
													{
														$(".navigationul").append(ele.clone());
													}
													// end 9 block navigation
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                    if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                                else{
                                                    $(".displayul").html(ele.clone());
													// start 9 block navigation
													$(".navigationul").html(ele.clone());
													// end 9 block navigation
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                     if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                     
                            }
                            
                        }
                    }
 
                    
                     jQuery(".displayul .deal_blk").each(function(){
                          jQuery(this).css("display","block");
                            alldealcnt  = alldealcnt + 1;
                             if(visible_location_arr.indexOf(jQuery(this).attr("locid")) != 1)
                                             {
                                         visible_location_arr.push(jQuery(this).attr("locid"));
                                             }
                     });
                }
				
				// strat display error msg from campaign page to category selection on search deal page
				if(alldealcnt == 0)
                    {
                        jQuery(".div_msg").html(not_found_msg);
                        jQuery(".div_msg").find("#span_miles").text(selected_miles);
						if(jQuery("#fltr_category").text()=="All Categories")
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text());
						else
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text()+ " category");
						jQuery(".info").css("display","none");
                    }
				else{
                         jQuery(".div_msg").html("");
						 jQuery(".info").css("display","block");
                    }
					if(alldealcnt == 0 || alldealcnt == 1)
					{
						jQuery(".info").css("display","none");
					}
				// end display error msg from campaign page to category selection on search deal page	
				
              /* for location marker */
			  //alert(visible_location_arr.length);
               var latlngbounds = new google.maps.LatLngBounds();
              for (var prop in markerArray) {
                  if(prop != "indexOf") {
                  markerArray[prop].setIcon('./images/pin-small.png');
                                     markerArray[prop].setVisible(false);
                  }
                            }
                for(i=0;i<visible_location_arr.length;i++)
                {
                    //alert(visible_location_arr[i]);
                    
                    v = parseInt(visible_location_arr[i]);
                  //  alert(v);
                 if(v != "indexOf")
                    markerArray[v].setVisible(true);
                    if(visible_location_arr.length >1)
                        {
                    var latlng2 = new google.maps.LatLng(markerArray[v].position.lat(), markerArray[v].position.lng());
                    latlngbounds.extend(latlng2);
                        }
                }
                 if(visible_location_arr.length >1)
                        {
                 map.setCenter(latlngbounds.getCenter());
                 map.fitBounds(latlngbounds); 
                        }
						else
						{
							map.setZoom(10);
							map.setCenter(new google.maps.LatLng(markerArray[v].position.lat(), markerArray[v].position.lng()));
						}
						
                /* for location marker */
              //   alert(alldealcnt);
                /* for check deals avialable*/
				//alert(alldealcnt);
                  if(alldealcnt == 0)
                    {
                        jQuery(".div_msg").html(not_found_msg);
                        jQuery(".div_msg").find("#span_miles").text(selected_miles);
						if(jQuery("#fltr_category").text()=="All Categories")
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text());
						else
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text()+ " category");
						jQuery(".info").css("display","none");
                    }
                    else{
                         jQuery(".div_msg").html("");
						 jQuery(".info").css("display","block");
                    }
                    if(alldealcnt == 0 || alldealcnt == 1)
					{
						jQuery(".info").css("display","none");
					}
                    /* for show deals avialable */
                    
                  /* for show more */
                    if(alldealcnt > 9)
                    {
                      
                        jQuery("#divshowoffer").css('display','block');
                    }
                    else
                    {

                        jQuery("#divshowoffer").css('display','none');
                    }
                     bind_hover_effect();
}
jQuery("#shareit-field",window.parent.document).keyup(function(){
   
var text_ele = jQuery(this);
//     jQuery(".arrow-up",window.parent.document).attr("disp1","0");
//    jQuery(".arrow-up",window.parent.document).hide();
//     jQuery("#shareit-box",window.parent.document).hide();
//     jQuery("#shareit-box",window.parent.document).attr("disp","0");
    infowindow.close();
   /// alert(category+"=="+miles);
      var visible_location_arr = [];
       var campaign_arr = [];
       var counter_campaingn_arr = [];
       var counter_location_arr = [];
       var counter_merchant_arr = [];
       var all_merchant_arr = [];
       var all_locations = [];
       var c_l = new Array();
     var search_div = "";
       var location_str = "";
                     //   alert(str);
                        str = "";
                       var selected_cat_id=getCookie("cat_remember");

                    var     selected_miles = parseInt( getCookie("miles_cookie"));
         	
//            
                 var alldealcnt = 0;
     if(jQuery(".showalld").length == 0)
         {
             search_div = "displayul_all";
                 if(selected_cat_id != 0){
                    var temp_arr = [];
                var str = "";
                    //alert(jQuery(".displayul_all .deal_blk[catid='"+selected_cat_id+"']").length);
                    jQuery("."+search_div+" .deal_blk[catid='"+selected_cat_id+"']").each(function(){
                      //        alert($(this).attr("locid"));
                      
                      var str1=jQuery(this).find(".busi_name").text();
                   //   alert(str1+"==="+text_ele.val());
var aString = text_ele.val();
if(aString=="Filter By Merchant Name")
	aString="";
var str= str1;

var patt=new RegExp("\^"+aString,'i');
//document.write(patt.test(aString));
                      if(patt.test(str1)){
                           if(jQuery(this).attr("miles") <= selected_miles)
                           {
                           
                         location_str = "";                            
                     if(counter_merchant_arr.length == 0){
                
                     }
                     else{
                       
                     c_l =[];

                        if(all_merchant_arr.indexOf($(this).attr("merid")) == -1)
                        {

                       c_l =[];
                        }
                        else{
                            c_l = counter_merchant_arr[$(this).attr("merid")] ;
                          //  alert($(this).attr("merid")+"=="+c_l.length);

                        }
                     }
                
                    if(jQuery(".displayul_all .deal_blk[locid='"+$(this).attr("locid")+"'][catid='"+selected_cat_id+"']").length == 1)
                    {
                        
                          c_l[$(this).attr("locid")] = $(this).attr("campid");
                           if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                  if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                              location_str = location_str.replace("undefined","");
                            
                       counter_location_arr[$(this).attr("merid")]=location_str;
                    }
                    else{
                      
                        if(c_l.length == 0){
                        
                           c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                            if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                  if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                              location_str = location_str.replace("undefined","");
                            
                       counter_location_arr[$(this).attr("merid")]=location_str;
                        }
                        else{
                        
                            if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                            if(c_l.indexOf($(this).attr("campid")) == -1)
                            {
                               c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                              if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                 if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                                  
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                                 
                              location_str = location_str.replace("undefined","");
                          
                       counter_location_arr[$(this).attr("merid")]=location_str;
                            }
                            else{
                               var flag= 1;
                                jQuery(".displayul_all .deal_blk[locid='"+$(this).attr("locid")+"'][catid='"+selected_cat_id+"']").each(function(){
                                    if(c_l.indexOf(jQuery(this).attr("campid"))  == -1)
                                      {
                                          flag=0; 
                                      }
                                    
                                });
                                if(flag==1)
                                    {
                                         c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                              if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                 if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                                  
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                                 
                              location_str = location_str.replace("undefined","");
                          
                       counter_location_arr[$(this).attr("merid")]=location_str;
                                    }
                            }
                        }
                        }
                    }
                     if(all_merchant_arr.indexOf($(this).attr("merid")) == -1)
                        {
                            all_merchant_arr.push($(this).attr("merid"));
                        }
                     counter_merchant_arr[$(this).attr("merid")] = []; 


                          counter_merchant_arr[$(this).attr("merid")] =   c_l ;
                          str ="";
                  }
                    }
                 });
         
   var max_counter =0;
   var max_counter1 =0;
                for(i in counter_merchant_arr)
                {
                       temp_arr = counter_merchant_arr[i];
                    max_counter =0;
                        for(j in temp_arr)
                        { 
                            max_counter1++;
                          
                        }
                          if(max_counter < max_counter1){
                        max_counter = max_counter1;
                          }
                }

                
         jQuery(".displayul").html("");
                 
                      var loc_arr = new Array();
                    for(i=0;i<max_counter;i++)
                    {
                        for(k=0;k<all_merchant_arr.length;k++)
                        {
                            m_counter = 0;
                            mid= all_merchant_arr[k];
                             temp_arr = counter_merchant_arr[mid];
                            temp_arr_str = counter_location_arr[mid];
                             temp_arr_str = temp_arr_str.substring(0,(temp_arr_str.length-1));
                           //  alert(temp_arr_str);
                             loc_arr = temp_arr_str.split("-");
                           
                            if(loc_arr.length >= i+1){

                                         str = mid+"=="+ loc_arr[i]+"=="+temp_arr[loc_arr[i]]+"<br/>";
                                       tot_campaigns  = jQuery(".displayul_all .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][catid='"+selected_cat_id+"']").length;
                                     tot_campaigns = tot_campaigns-1;
                                            ele = jQuery(".displayul_all .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                            if($(".displayul .deal_blk").length>0)
                                                {
                                                    $(".displayul").append(ele.clone())
													// start 9 block navigation
													if($(".displayul .deal_blk").length<=9)
													{
														$(".navigationul").append(ele.clone());
													}
													// end 9 block navigation
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                    if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                                else{
                                                    $(".displayul").html(ele.clone());
													// start 9 block navigation
													$(".navigationul").html(ele.clone());
													// end 9 block navigation
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                     if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                     
                                     
                            }
                            
                        }
                    }
               
                   
                     jQuery(".displayul .deal_blk").each(function(){
                          jQuery(this).css("display","block");
                            alldealcnt  = alldealcnt + 1;
                             if(visible_location_arr.indexOf(jQuery(this).attr("locid")) != 1)
                                             {
                                         visible_location_arr.push(jQuery(this).attr("locid"));
                                             }
                     });

                }
                else
                {
                    var location_str ="";
                    all_locations = [];
                    jQuery("."+search_div+" .deal_blk").each(function(){
                        var str1=jQuery(this).find(".busi_name").text();
                   //   alert(str1+"==="+text_ele.val());
var aString = text_ele.val();
if(aString=="Filter By Merchant Name")
	aString="";
var str= str1;

var patt=new RegExp("\^"+aString,'i');
//document.write(patt.test(aString));
                      if(patt.test(str1)){
                             if(jQuery(this).attr("miles") <= selected_miles)
                           {
                   location_str = "";                            
                     if(counter_merchant_arr.length == 0){
                
                     }
                     else{
                       
                     c_l =[];

                        if(all_merchant_arr.indexOf($(this).attr("merid")) == -1)
                        {

                       c_l =[];
                        }
                        else{
                            c_l = counter_merchant_arr[$(this).attr("merid")] ;
                          //  alert($(this).attr("merid")+"=="+c_l.length);

                        }
                     }
                
                    if(jQuery(".displayul_all .deal_blk[locid='"+$(this).attr("locid")+"']").length == 1)
                    {
                        
                          c_l[$(this).attr("locid")] = $(this).attr("campid");
                          if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                  
                                   if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                              location_str = location_str.replace("undefined","");
                             
                       counter_location_arr[$(this).attr("merid")]=location_str;
                    }
                    else{
                      
                        if(c_l.length == 0){
                        
                           c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                           if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                  if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                              location_str = location_str.replace("undefined","");
                            
                       counter_location_arr[$(this).attr("merid")]=location_str;
                        }
                        else{
                         if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                            if(c_l.indexOf($(this).attr("campid")) == -1)
                            {
                               c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                              if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                 if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                                  
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                                 
                              location_str = location_str.replace("undefined","");
                          
                       counter_location_arr[$(this).attr("merid")]=location_str;
                            }
                            else{
                               var flag= 1;
                                jQuery(".displayul_all .deal_blk[locid='"+$(this).attr("locid")+"']").each(function(){
                                    if(c_l.indexOf(jQuery(this).attr("campid"))  == -1)
                                      {
                                          flag=0; 
                                      }
                                    
                                });
                                if(flag==1)
                                    {
                                         c_l[$(this).attr("locid")] = $(this).attr("campid");//+"-"+jQuery(".displayul .deal_blk[locid='"+$(this).attr("locid")+"']").lenngth;
                              if(counter_location_arr[$(this).attr("merid")] != "")
                              {
                                 if(all_locations.indexOf( $(this).attr("locid")) == -1)
                                {
                                    all_locations.push($(this).attr("locid"));
                                     location_str = counter_location_arr[$(this).attr("merid")]+$(this).attr("locid")+"-";
                                }
                                else{
                                    location_str = counter_location_arr[$(this).attr("merid")]
                                }
                                  
                              }
                              else{
                                   location_str = $(this).attr("locid")+"-";
                              }
                                 
                              location_str = location_str.replace("undefined","");
                          
                       counter_location_arr[$(this).attr("merid")]=location_str;
                                    }
                            }
                        }
                        }
                    }
                     if(all_merchant_arr.indexOf($(this).attr("merid")) == -1)
                        {
                            all_merchant_arr.push($(this).attr("merid"));
                        }
                     counter_merchant_arr[$(this).attr("merid")] = []; 


                          counter_merchant_arr[$(this).attr("merid")] =   c_l ;
                          str ="";
                           }
                    }
                 });


                var temp_arr = [];
                var str = "";
                 
                    jQuery(".displayul").html("");
                    var max_counter =0;
   var max_counter1 =0;
                for(i in counter_merchant_arr)
                {
                       temp_arr = counter_merchant_arr[i];
                    max_counter =0;
                        for(j in temp_arr)
                        { 
                            max_counter1++;
                          
                        }
                          if(max_counter < max_counter1){
                        max_counter = max_counter1;
                          }
                }

                var tot_campaigns =0;
         jQuery(".displayul").html("");
                 
                      var loc_arr = new Array();
                    for(i=0;i<max_counter;i++)
                    {
                        for(k=0;k<all_merchant_arr.length;k++)
                        {
                            m_counter = 0;
                            mid= all_merchant_arr[k];
                             temp_arr = counter_merchant_arr[mid];
                            temp_arr_str = counter_location_arr[mid];
                             temp_arr_str = temp_arr_str.substring(0,(temp_arr_str.length-1));
                         //   alert(temp_arr_str);
                             loc_arr = temp_arr_str.split("-");
                           
                            if(loc_arr.length >= i+1){

                                         str = mid+"=="+ loc_arr[i]+"=="+temp_arr[loc_arr[i]]+"<br/>"+(i);
                                  
                                     tot_campaigns  = jQuery(".displayul_all .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"']").length;
                                     tot_campaigns = tot_campaigns-1;
                                            ele = jQuery(".displayul_all .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                            if($(".displayul .deal_blk").length>0)
                                                {
                                                    $(".displayul").append(ele.clone())
													// start 9 block navigation
													if($(".displayul .deal_blk").length<=9)
													{
														$(".navigationul").append(ele.clone());
													}
													// end 9 block navigation
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                    if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                                else{
                                                    $(".displayul").html(ele.clone());
													// start 9 block navigation
													$(".navigationul").html(ele.clone());
													// end 9 block navigation
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                     if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                     
                            }
                            
                        }
                    }
 
                    
                     jQuery(".displayul .deal_blk").each(function(){
                          jQuery(this).css("display","block");
                            alldealcnt  = alldealcnt + 1;
                             if(visible_location_arr.indexOf(jQuery(this).attr("locid")) != 1)
                                             {
                                         visible_location_arr.push(jQuery(this).attr("locid"));
                                             }
                     });
                }
              
         }
         else{

             search_div = "displayul"; 
              if(selected_cat_id != 0){
                  
                    var temp_arr = [];
                var str = "";
                    jQuery(".displayul .deal_blk[catid='"+selected_cat_id+"']").each(function(){

                       var str1=jQuery(this).find(".busi_name").text();
var aString = text_ele.val();
if(aString=="Filter By Merchant Name")
	aString="";
var str= str1;
var patt=new RegExp("\^"+aString,'i');

                      if(patt.test(str1)){
                           if(jQuery(this).attr("miles") <= selected_miles)
                           {
                              ele = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                 ele.css("display","block");
                                   alldealcnt  = alldealcnt + 1;

                 } 
                      }else{
                      ele = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                          ele.css("display","none");
                      }
                 });
         
                

                }
                else
                {
                    var location_str ="";
                    all_locations = [];
                    jQuery(".displayul .deal_blk").each(function(){
                         var str1=jQuery(this).find(".busi_name").text();
                   //   alert(str1+"==="+text_ele.val());
var aString = text_ele.val();
if(aString=="Filter By Merchant Name")
	aString="";
var str= str1;

var patt=new RegExp("\^"+aString,'i');
                      if(patt.test(str1)){
                      
                             if(jQuery(this).attr("miles") <= selected_miles)
                           {
                                 ele = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                 ele.css("display","block");
                                   alldealcnt  = alldealcnt + 1;

                           }
                      }
                      else{
                           ele = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                         
                          ele.css("display","none");
                      }
                 });

                }
         }
      //   alert(search_div);
    
              //   alert(selected_cat_id);
            
              //}

              /* for location marker */
               if(jQuery(".showalld").length == 0)
            {
                  var latlngbounds = new google.maps.LatLngBounds();

                 for (var prop in markerArray) {
                     if(prop != "indexOf") {
                                        markerArray[prop].setVisible(false);
                                         markerArray[prop].setIcon('./images/pin-small.png');
                     }
                               }
                   for(i=0;i<visible_location_arr.length;i++)
                   {
                       //alert(visible_location_arr[i]);
                       v = parseInt(visible_location_arr[i]);
                        if(v != "indexOf")
                       markerArray[v].setVisible(true);
                       if(visible_location_arr.length >1)
                           {
                       var latlng2 = new google.maps.LatLng(markerArray[v].position.lat(), markerArray[v].position.lng());
                       latlngbounds.extend(latlng2);
                           }
                   }
                    if(visible_location_arr.length >1)
                           {
                    map.setCenter(latlngbounds.getCenter());
                    map.fitBounds(latlngbounds); 
                           }
							else
						{
							map.setZoom(10);
							map.setCenter(new google.maps.LatLng(markerArray[v].position.lat(), markerArray[v].position.lng()));
						}
                   /* for location marker */
                 //   alert(alldealcnt);
                   /* for check deals avialable*/
                 //  alert(alldealcnt);
                     if(alldealcnt == 0)
                       {
                           jQuery(".div_msg").html(not_found_msg);
                           jQuery(".div_msg").find("#span_miles").text(selected_miles);
						   if(jQuery("#fltr_category").text()=="All Categories")
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text());
							else
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text()+ " category");
						  jQuery(".info").css("display","none");
                       }
                       else{
                            jQuery(".div_msg").html("");
							jQuery(".info").css("display","block");
                       }
					   if(alldealcnt == 0 || alldealcnt == 1)
						{
							jQuery(".info").css("display","none");
						}
            }
            else{
               
                     if(alldealcnt == 0)
                       {
//                           jQuery(".div_msg").html('<a onclick="remove_filter()"  style="cursor:pointer"><div class="showalld">Show All Deals<br></div></a><hr>');
//                           jQuery(".div_msg").append("<br/>");
                           jQuery(".div_msg").html(not_found_msg);jQuery(".div_msg").find("#span_miles").text(selected_miles);
						   if(jQuery("#fltr_category").text()=="All Categories")
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text());
							else
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text()+ " category");
						  jQuery(".info").css("display","none");
                       }
                       else{
                             jQuery(".div_msg").html('<a onclick="remove_filter()"  style="cursor:pointer"><div class="showalld">Show All Offers<br></div></a><hr>');
							 jQuery(".info").css("display","block");
                       }
					   if(alldealcnt == 0 || alldealcnt == 1)
						{
							jQuery(".info").css("display","none");
						}
            }
                    /* for show deals avialable */
                    
                  /* for show more */
                    if(alldealcnt > 9)
                    {
                      
                        jQuery("#divshowoffer").css('display','block');
                    }
                    else
                    {

                        jQuery("#divshowoffer").css('display','none');
                    }
                     bind_hover_effect();
					 var aplyview=getCookie("view");
					//alert(aplyview);
					if(aplyview=="gridview")
					{			
						jQuery(".info").css("display","none");
					}
})
function  filter_deals_algorithm_by_location(category,miles,locationid)
{
    jQuery(".arrow-up",window.parent.document).attr("disp1","0");
    jQuery(".arrow-up",window.parent.document).hide();
     jQuery("#shareit-box",window.parent.document).hide();
     jQuery("#shareit-box",window.parent.document).attr("disp","0");
     var visible_location_arr = [];
       var campaign_arr = [];
       var counter_campaingn_arr = [];
       var counter_location_arr = [];
       var counter_merchant_arr = [];
       var all_merchant_arr = [];
       var all_locations = [];
       var c_l = new Array();
     jQuery(".displayul").html("");
      var location_str = "";
                     //   alert(str);
                        str = "";
         	var selected_cat_id=category;
                var selected_miles=parseInt(miles);
                var selected_locationid = locationid;
//            
                 var alldealcnt = 0;
              //   alert(selected_cat_id);
                if(selected_cat_id != 0){
                    var temp_arr = [];
                var str = "";
                    //alert(jQuery(".displayul_all .deal_blk[catid='"+selected_cat_id+"']").length);
                    jQuery(".displayul_all .deal_blk[catid='"+selected_cat_id+"'][locid='"+selected_locationid+"']").each(function(){
                      //        alert($(this).attr("locid"));
                           if(jQuery(this).attr("miles") <= selected_miles)
                           {
                              ele = jQuery(".displayul_all .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                               
                                            if($(".displayul .deal_blk").length>0)
                                                {
                                                    $(".displayul").append(ele.clone());
													// start 9 block navigation
													if($(".displayul .deal_blk").length<=9)
													{
														$(".navigationul").append(ele.clone());
													}
													// end 9 block navigation
                                                    ele1 = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                                     ele1.find(".tot_deal_counter").parent().detach();
                                            }
                                                else{
                                                    $(".displayul").html(ele.clone()); 
													// start 9 block navigation
													$(".navigationul").html(ele.clone());
													// end 9 block navigation
                                                    ele1 = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                                    ele1.find(".tot_deal_counter").parent().detach();
                                                }
                 }
                 });
         
                  jQuery(".displayul .deal_blk").each(function(){
                          jQuery(this).css("display","block");
                            alldealcnt  = alldealcnt + 1;
                             if(visible_location_arr.indexOf(jQuery(this).attr("locid")) != 1)
                                             {
                                         visible_location_arr.push(jQuery(this).attr("locid"));
                                             }
                     });

                }
                else
                {
                    var location_str ="";
                    all_locations = [];
                    jQuery(".displayul_all .deal_blk[locid='"+selected_locationid+"']").each(function(){
                             if(jQuery(this).attr("miles") <= selected_miles)
                           {
                                 ele = jQuery(".displayul_all .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                            if($(".displayul .deal_blk").length>0)
                                                {
                                                    $(".displayul").append(ele.clone());
													// start 9 block navigation
													if($(".displayul .deal_blk").length<=9)
													{
														$(".navigationul").append(ele.clone());
													}
													// end 9 block navigation
                                                    ele1 = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                                     ele1.find(".tot_deal_counter").parent().detach();
                                            }
                                                else{
                                                    $(".displayul").html(ele.clone()); 
													// start 9 block navigation
													$(".navigationul").html(ele.clone());
													// end 9 block navigation
                                                    ele1 = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                                    ele1.find(".tot_deal_counter").parent().detach();
                                                }
                           }
                 });


              
                    
                     jQuery(".displayul .deal_blk").each(function(){
                          jQuery(this).css("display","block");
                            alldealcnt  = alldealcnt + 1;
                             if(visible_location_arr.indexOf(jQuery(this).attr("locid")) != 1)
                                             {
                                         visible_location_arr.push(jQuery(this).attr("locid"));
                                             }
                     });
                }
              
              
              
              
              /* for location marker */
//              for (var prop in markerArray) {
//                                     markerArray[prop].setVisible(false);
//                            }
//                for(i=0;i<visible_location_arr.length;i++)
//                {
//                    //alert(visible_location_arr[i]);
//                    v = parseInt(visible_location_arr[i]);
//                 
//                    markerArray[v].setVisible(true);
//                }

                /* for location marker */
              //   alert(alldealcnt);
                /* for check deals avialable*/
                  if(alldealcnt == 0)
                    {
                        jQuery(".div_msg").html(not_found_msg);
                        jQuery(".div_msg").find("#span_miles").text(selected_miles);
						if(jQuery("#fltr_category").text()=="All Categories")
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text());
						else
							jQuery(".div_msg").find("#span_category").text(jQuery("#fltr_category").text()+ " category");
						jQuery(".info").css("display","none");
                    }
                    else{
                         jQuery(".div_msg").html('<a onclick="remove_filter()"  style="cursor:pointer"><div class="showalld">Show All Offers<br></div></a><hr>');
						 jQuery(".info").css("display","block");
                    }
					
					/*
					if(alldealcnt == 0 || alldealcnt == 1)
					{
						jQuery(".info").css("display","none");
					}
					*/
					
					/* 27-8-2013 */
					//alert(selected_locationid);
					var total_marker=0;
					/*
					for (var prop in markerArray) 
					{
						total_marker++;
                    }
					*/
					/* 07-10-2013 */
					for (var prop in markerArray) 
					{
                        if(markerArray[prop].visible)
							total_marker++;
                    }
					/* 07-10-2013 */
					//alert(total_marker);
					if(total_marker <=1 )
					{
						jQuery(".info").css("display","none");
					}
					/*
					else
					{
						jQuery(".info").css("display","block");
					}
					*/
                    /* 27-8-2013 */
                    /* for show deals avialable */
                    
                  /* for show more */
                    if(alldealcnt > 9)
                    {
                      
                        jQuery("#divshowoffer").css('display','block');
                    }
                    else
                    {

                        jQuery("#divshowoffer").css('display','none');
                    }
                     bind_hover_effect();
}
jQuery("#fltr_category_close").click(function(){
	//alert(jQuery("#fltr_category").text());
	if(jQuery("#fltr_category").text()=="All Categories")
	{
		//alert("default category");
		//jQuery(this).find("img").css("cursor","text");
	}
	else
	{
		jQuery(this).find("img").css("cursor","text");
		jQuery("#fltr_category_close").find("img").attr("src","templates/images/filter_close_grey.png");
		//alert(jQuery(".selected_miles").attr("mval"));
		jQuery("#fltr_category").text("All Categories");
		setCookie("cat_remember",0);
		jQuery(".btnfilterstaticscampaigns").each(function(index){
			if(jQuery(this).attr("mycatid")=="0")
			{
				jQuery(this).css('color','orange');
			}
			else
			{
				jQuery(this).css("color","#3B3B3B");
			}
		});	
		var mile_val=jQuery(".selected_miles").attr("mval");
		filter_deals_algorithm(0,mile_val);
		
		var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
	}
	//5-11-2013 with font without image
	jQuery("#fltr_category_close").removeClass("filterimage");
	//5-11-2013 with font without image
});

jQuery("#fltr_mile_close").click(function(){
	//alert(jQuery("#fltr_mile").text());
	if(jQuery("#fltr_mile").text()=="50 Mi")
	{
		//alert("default mile");
		//jQuery(this).find("img").css("cursor","text");
	}
	else
	{
		jQuery(this).find("img").css("cursor","text");
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
		filter_deals_algorithm(cat_val,50);
		
		var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
		//5-11-2013 with font without image
		jQuery("#fltr_mile_close").removeClass("filterimage");
		//5-11-2013 with font without image	
	}
});