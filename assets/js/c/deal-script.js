/*
Onclick clean input field 
*/
//jQuery(document).ready(function(){
    var not_found_msg = jQuery('#hdn_error_message').html();

//});

function getParam( name , url )
{
 name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
 var regexS = "[\\?&]"+name+"=([^&#]*)";
 var regex = new RegExp( regexS );
 var results = regex.exec(url);
 if( results == null )
    return "";
 else
  return results[1];
}


function remove_filter(catid)
{
	var selected_cat_id=getCookie("cat_remember");
        var miles_cookie = getCookie("miles_cookie");
       // filter_deals_algorithm(selected_cat_id,miles_cookie);
		
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
      
		



jQuery("body").on("click",".unsubscribestore",function(){
var locid1 = $(this).attr("s_lid1");
//alert(locid1);
    var ele = jQuery(this);
   // alert('process.php?btnunsubscribelocation=1&location_id='+$(this).attr("s_lid"));
	//return false;
     $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'btnunsubscribelocation=1&location_id='+$(this).attr("s_lid"),
                 // async:false,
		  success:function(msg)
		  {
                      try
					  {
                      //alert(ele.text());
                     ele.text("Subscribe to store");
                      //alert(ele.text());
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
					 
					 
					 
					  
					 
					 //ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                //     ele1.find(".tot_deal_counter").parent().detach();
					   
					   
					   selected_cat_id=getCookie("cat_remember");
                          
                       
                       miles_cookie=getCookie("miles_cookie");
					
                          
                       filter_locations(selected_cat_id,miles_cookie);
					// 9-8-2013 to solve marker infowindow closed when unsubscribe
						infowindow.setContent(infowindowcontent[locid1]);
						markerArray[locid1].setIcon('./images/pin-small-blue.png'); 
						infowindow.open(map,markerArray[locid1]);
					// 9-8-2013
                  }
				  catch(e){alert(e);}
				  }
   });
});	
               		 
jQuery(document).ready(function(){
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


if(flag == "yes")
{
	firstlocid = new_locid;
}
jQuery("#hdn_is_offer_div_click").val("0");
jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");
	 /* display marker */
		jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
	/*** 03-01-2014 ****/
	var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			jQuery(".info").css("display","none");
		}
 });
jQuery(".filter_campaigns_by").live('click',function(){
	 filter_deals_algorithm_by_location(getCookie("cat_remember"),getCookie("miles_cookie"),jQuery(this).attr('filter_location_id'));
	
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
	
		if(typeof getCookie('searched_location') == "undefined" || getCookie('searched_location') == "" || getCookie('searched_location') == null ){
}else{
		// 22-8-2013
		var aplyview=getCookie("view");
		if(aplyview=="gridview")
		{
			//jQuery('#gridview').trigger('click');
			//jQuery(".info").css("display","none");
		}
		else
		{
		//	jQuery('#mapview').trigger('click');
			//jQuery(".info").css("display","block");
		}
}
});



var tot_record = '<?php echo $tot_records;?>';
    get_next_html('nextoffer');
    jQuery('.nextoffer').click(function(){
        get_next_html('nextoffer');
        
    });
   
    function get_next_html(next_or_prev)
    {
    //$('.mainul').html($('.displayul').html());
    var li_maintain = '';
    jQuery('.mainul').html('');
    // alert(jQuery('.mainul').html());
        if(next_or_prev == "nextoffer")
        {
            jQuery('.displayul .deal_blk:lt(9)').each(function(){
                //alert($(this).html());
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
     selected_cat_id=getCookie("cat_remember");
        miles_cookie=getCookie("miles_cookie");
    if(typeof miles_cookie == "undefined")
       {
         setCookie("miles_cookie",50,365);
       }
       else{
     miles_cookie=getCookie("miles_cookie"); 
       }
       if(typeof selected_cat_id == "undefined")
       {
         setCookie("cat_remember",0,365);
       }
       else{
    selected_cat_id=getCookie("cat_remember");
       }
     	var selected_cat_id="";
        var miles_cookie = "";
        selected_cat_id=getCookie("cat_remember");
        miles_cookie=getCookie("miles_cookie");
       
        filter_locations(selected_cat_id,miles_cookie);
            
                    
}
function bind_hover_effect()
{
  jQuery('.deal_blk').live('mouseenter',function(){
		//alert(jQuery(this).find(".strip").length);
	   //alert(jQuery(this).find(".strip_grid").length);
	   
       var aplyview=getCookie("view");
		
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
    var  m_counter = 0;
    
     jQuery(".arrow-up").attr("disp1","0");
    jQuery(".arrow-up").hide();
     jQuery("#shareit-box").hide();
     jQuery("#shareit-box").attr("disp","0");
    infowindow.close();
   // alert(category+"=="+miles);
      var visible_location_arr = [];
       var campaign_arr = [];
       var counter_campaingn_arr = [];
       var counter_location_arr = [];
       var counter_merchant_arr = [];
       var all_merchant_arr = [];
       var all_locations = [];
       var c_l = new Array();
    var text_ele = jQuery("#shareit-field")
      var location_str = "";
                     //   alert(str);
                        str = "";
         	var selected_cat_id=category;
                var selected_miles=parseInt(miles);
//            
                 var alldealcnt = 0;
                
                if(selected_cat_id != 0){
                    var temp_arr = [];
                var str = "";
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
                          //  m_counter = 0;
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
		/*	else
                        {
                            //alert("in else");
                                    if(v != "indexOf") {
                                     //   alert("In zoom"+markerArray[v].position.lat()+"==="+markerArray[v].position.lng());
                                          map.setZoom(10);
                                             map.setCenter(new google.maps.LatLng(markerArray[v].position.lat(), markerArray[v].position.lng()));
                                    }
                        } */

                /* for location marker */
          //       alert(alldealcnt);
                /* for check deals avialable*/
                  if(alldealcnt == 0)
                    {
                       // alert(not_found_msg);
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
   /* var str1=jQuery(this).find(".busi_name").text();
var text_ele = jQuery("#shareit-field",window.parent.document);
	
var aString = text_ele.val();
if(aString=="Filter By Merchant Name")
	aString="";
var str= str1;

var patt=new RegExp("\^"+aString,'i');
//document.write(patt.test(aString));
		  if(patt.test(str1)){	
		  } */
		  selected_cat_id = getCookie("cat_remember");
		miles_cookie = getCookie("miles_cookie");
			filter_locations(selected_cat_id,miles_cookie);
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
jQuery("#hdn_is_offer_div_click").val("0");
jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");
	 /* display marker */
		jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */		
});
function  filter_deals_algorithm_by_location(category,miles,locationid)
{
    jQuery(".arrow-up").attr("disp1","0");
    jQuery(".arrow-up").hide();
     jQuery("#shareit-box").hide();
     jQuery("#shareit-box").attr("disp","0");
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
					if(alldealcnt == 0 || alldealcnt == 1 )
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
					//alert(total_marker);
					/* 07-10-2013 */
					for (var prop in markerArray) 
					{
                        if(markerArray[prop].visible)
							total_marker++;
                    }
					/* 07-10-2013 */
					if(total_marker <=1 )
					{
						jQuery(".info").css("display","none");
					}
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
 //jQuery(document).ready(function(){
   jQuery(".btnfilterstaticscampaigns").click(function(){
  // alert("in click");
	jQuery("#category_slider li").removeClass("current");
	jQuery(this).addClass("current");
	
	jQuery("#category_slider li img").each(function(){
		var myimg=jQuery(this);
		var main_image=myimg.attr("main_image");
		myimg.attr("src",main_image);
	});
	
	jQuery(this).find("img").attr("src",jQuery(this).find("img").attr("active_image"));
   
   jQuery("#fltr_category").text(jQuery(this).find('span').text());
   if(jQuery("#fltr_category").text()!="All Categories")
		{
			
			jQuery("#fltr_category_close").find("img").css("cursor","pointer");
			jQuery("#fltr_category_close").find("img").attr("src","templates/images/filter_close.png");
			
			//5-11-2013 with font without image
			jQuery("#fltr_category_close").addClass("filterimage");
			//5-11-2013 with font without image
		}
		else
		{
			jQuery("#fltr_category_close").find("img").css("cursor","text");
			jQuery("#fltr_category_close").find("img").attr("src","templates/images/filter_close_grey.png");
			
			//5-11-2013 with font without image
			jQuery("#fltr_category_close").removeClass("filterimage");
			//5-11-2013 with font without image
			
		}
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

					    filter_locations(selected_cat_id,miles_cookie);
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

jQuery("#hdn_is_offer_div_click").val("0");
jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");
		 /* display marker */
		jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
										
		var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{			
			//jQuery(".info").css("display","none");
		}
		/*
		else
		{			
			jQuery(".info").css("display","block");			
		}
		*/
		      
    });
   
 //});
 function bind_popup_event()
 {
  jQuery(".fancybox-inner #btn_msg_div").live("click",function(){
	      

                 jQuery(".fancybox-inner .email_popup_div").css("display","block");
                    jQuery(".fancybox-inner .popupmainclass").css("display","none");
                    jQuery(".forgotmainclass").css("display","none"); 
            jQuery(".mainloginclass").css("display","none");
             jQuery(".fancybox-inner .updateprofile").css("display","none");
                
                 //$(".popupmainclass").show();
	    //   open_popup('emailNotification');
                

	});
 }   
jQuery(".fancybox-inner .email_popup_div #btn_cancel").live("click",function(){
           jQuery(".fancybox-inner .email_popup_div").css("display","none");
           jQuery(".fancybox-inner .popupmainclass").css("display","block");
            jQuery(".fancybox-inner .updateprofile").css("display","none");
});
 
       
   $(".fancybox-inner #btn_cancel_forgot").live("click",function(){
            jQuery(".fancybox-inner .email_popup_div").css("display","none");
                    jQuery(".fancybox-inner .popupmainclass").css("display","block");
                jQuery(".fancybox-inner .mainloginclass").css("display","none");
                 jQuery(".fancybox-inner .updateprofile").css("display","none");
                 jQuery(".fancybox-inner .forgotmainclass").css("display","none");
        });      
       
      
   
function validate_register(){
    
	if(email_validation(document.getElementById("email").value) == false){
		alert("Please Enter valid Email");
		document.getElementById("email").focus();
                error_var="false";
		return false;
	}
        else if(document.getElementById("mycaptcha_rpc").value == ""){
		alert("Please Enter Captcha");
		document.getElementById("mycaptcha_rpc").focus();
		error_var="false";
                return false;
	}
        else
        {
            error_var="true";
        }
	
	
}
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
var location_id = "<?php echo $locationid; ?>";
 function processLogJson(data) {
       
	//alert(data.c_id+"==="+data.l_id);
	if(data.status == "true"){
            
	    //$(".mainloginclass").hide();
            //$(".popupmainclass").show();												
		window.top.location.href = "<?php echo WEB_PATH;?>/search-deal.php";
		//window.opener.reload();
             //window.close();
                
	}else{
           
		alert(data.message);
		return false;
	}
     
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
jQuery("#hdn_is_offer_div_click").val("0");
jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
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
jQuery("#hdn_is_offer_div_click").val("0");
jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");
	 /* display marker */
		jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
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
function filter_locations(category_id,miles){
var is_expiringtoday= jQuery("#hdn_is_expiring_today").val();
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
                  markerArray[prop].setIcon('./images/pin-small.png');
                                     markerArray[prop].setVisible(false);
                  }
                            }
		var inc_scroll = 100;
		var setnull = 0;
		jQuery(".location_tool").each(function(){
			var str1= jQuery(this).find(".merchant_name").val();
			
	var aString = text_ele.val();
	if(aString=="Filter By Merchant Name")
		aString="";
	var str= str1;
	var patt=new RegExp("\^"+aString,'i');
			  
			all_categories = jQuery(this).attr("categories");
			arr = all_categories.split(",");
			
			all_expiring = jQuery(this).attr("t_l_e");
			
			arr_expr = all_expiring.split(",");
			
			
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
				//
				//alert(arr_expr.indexOf(1 )+"=="+is_expiringtoday);
				if(arr_expr.indexOf("1" ) == -1 && is_expiringtoday== 1  ) 
				{
				
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else{
				
				if(parseInt(milesaway) <= miles ) {
				//alert(aString+"==="+patt.test(str1));
					if(patt.test(str1) || aString=="" ){	
			  
						
							
							// 28 01 2014
							
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
									inc_scroll =  inc_scroll+150;
									var location_categories = jQuery(this).attr("categories");
									var loc_arr = location_categories.split(",");
									var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var no_times = 0;
									for(i=0;i<loc_arr.length;i++)
									{
										if( is_expiringtoday== 1 )
										{
											if(loc_arr[i] == category_id && expr_arr[i]==1)
											{
												//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
												no_times++;
											}
										}
										else{
											if(loc_arr[i] == category_id )
											{
											//alert(loc_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm else condition");
												no_times++;
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
								inc_scroll =  inc_scroll+150;
								var location_categories = jQuery(this).attr("categories");
									var loc_arr = location_categories.split(",");
									var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var no_times = 0;
									for(i=0;i<loc_arr.length;i++)
									{
										if( is_expiringtoday== 1 )
										{
											if(loc_arr[i] == category_id && expr_arr[i]==1)
											{
												no_times++;
											}
										}
										else{
											if(loc_arr[i] == category_id )
											{
												no_times++;
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
			//alert(arr_expr.indexOf("1" )+"==="+is_expiringtoday);
			if(arr_expr.indexOf("1" ) == -1 && is_expiringtoday== 1  ) 
				{
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else{
				if( parseInt(milesaway) > miles ) 
				{
					jQuery(this).css("display","none");
					jQuery(this).attr("scroll",setnull);
				}
				else{
					if(patt.test(str1)|| aString==""){	
					
						// 28 01 2014
							
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
									inc_scroll =  inc_scroll+150;
									/* count offers left */
									var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var no_times = 0;
									//alert("in here");
									if( is_expiringtoday== 1 )
									{
									//alert("in expiring condition");
										for(i=0;i<expr_arr.length;i++)
										{
											if(expr_arr[i]==1)
											{
												alert(expr_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
												no_times++;
											}
										}
										if(no_times == 0)
									{
										jQuery(this).css("display","none");
										jQuery(this).attr("scroll",setnull);
										v = parseInt(jQuery(this).attr("locid"));	
								if(v != "indexOf")
								markerArray[v].setVisible(false);
									}
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									
									}
									else{
										var location_categories = jQuery(this).attr("categories");
										var loc_arr = location_categories.split(",");
										jQuery(this).find(".loca_total_offers span").text(loc_arr.length+" Offers");
									}
									/* offers left */
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
								inc_scroll =  inc_scroll+150;
								/* count offers left */
								var location_expires = jQuery(this).attr("t_l_e");
									var expr_arr = location_expires.split(",");
									var no_times = 0;
									if( is_expiringtoday== 1 )
									{
										for(i=0;i<expr_arr.length;i++)
										{
											if(expr_arr[i]==1)
											{
											//	alert(expr_arr[i]+"==="+category_id+"===="+expr_arr[i]+"imm if condition");
												no_times++;
											}
										}
										if(no_times == 0)
										{
											jQuery(this).css("display","none");
											jQuery(this).attr("scroll",setnull);
											v = parseInt(jQuery(this).attr("locid"));	
								if(v != "indexOf")
								markerArray[v].setVisible(false);
										}
										jQuery(this).find(".loca_total_offers span").text(no_times+" Offers");
									}
									else{
										var location_categories = jQuery(this).attr("categories");
										var loc_arr = location_categories.split(",");
										jQuery(this).find(".loca_total_offers span").text(loc_arr.length+" Offers");
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
							inc_scroll =  inc_scroll+150;
								var location_categories = jQuery(this).attr("categories");
								var loc_arr = location_categories.split(",");
								jQuery(this).find(".loca_total_offers span").text(loc_arr.length+" Offers")
					
						*/
					
							}
							else{
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
	
	jQuery(".searchdeal_offers").css("display","block");
	if(parseInt(total_lacations) == 0)
	{
	//alert("In if");
		infowindow.close();
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
                 
				
	}
	else{
                         jQuery(".div_msg").html("");
						 jQuery(".info").css("display","block");
                    }
	jQuery('.location').jScrollPane({
			horizontalGutter:5,
			verticalGutter:5,
			'showArrows': false,
                        mouseWheelSpeed: 50
                        
			});
	//}
}
//alert("In filter location function"+category_id+"="+miles)

  $("body").on("click",".sortbuttondistance",function(){
		if(jQuery(this).attr("sorttye") == "asc" )
		{
			jQuery(this).attr("sorttye","desc");
		}
		else{
			jQuery(this).attr("sorttye","asc");
		}
		sort_distance(jQuery(this).attr("sorttye"));
		
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
  });
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
$(".filterd_location").html("");
$(".filterd_location").html(myArray);
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
$(".filterd_location").html("");
$(".filterd_location").html(myArray);
$("#calls").append(count+1);
}

jQuery("#map_canvas").live("mouseleave",function(){
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
});
jQuery(".filterbuttonexpiringtoday").live("click",function(){
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
jQuery("#hdn_is_offer_div_click").val("0");

jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
});
jQuery("#fltr_expiring_close").live("click",function(){
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
			//flag = "yes";
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

//alert(flag);
if(flag == "yes")
{
	firstlocid = new_locid;
}
jQuery("#hdn_is_offer_div_click").val("0");
//alert(firstlocid);
jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");

	 /* display marker */
		jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
		/* display marker */
});
