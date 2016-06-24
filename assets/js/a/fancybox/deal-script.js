/*
Onclick clean input field 
*/
//jQuery(document).ready(function(){
    var not_found_msg = jQuery('#hdn_error_message').html();

//});

function try1(val,campid,locid) {
            //alert("In"+campid+"=="+locid);
            for (var prop in markerArray) {
                                     markerArray[prop].setIcon('./images/pin-small.png');
                            }
           
            infowindow.setContent(infowindowcontent[locid]);
            markerArray[locid].setIcon('./images/pin-small-blue.png'); 
            infowindow.open(map,markerArray[locid]);
			parent.$.fancybox({
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
                                topRatio: 0, 
                              
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
//         afterShow :   function() {
//             alert("1");
//            $(".fancybox-wrap").css('top', '136px');
//        }
                                // helpers
			}); // fancybox
                        jQuery('.fancybox-wrap').css('margin-top', '120px');
		// $.fancybox.update();
		}
function remove_filter(catid)
{
	var selected_cat_id=getCookie("cat_remember");
        var miles_cookie = getCookie("miles_cookie");
        filter_deals_algorithm(selected_cat_id,miles_cookie);
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
      
		
		
               		 
jQuery(document).ready(function(){
        jQuery(".miles").click(function(){
    //      alert(jQuery(this).attr("mval")) ;
    jQuery(".miles").each(function(){
            jQuery(this).removeClass("selected_miles");
        });    
        jQuery(this).addClass("selected_miles");
    var mile_val = jQuery(this).attr("mval");
     setCookie("miles_cookie",mile_val,365);
     filter_deals_algorithm(getCookie("cat_remember"),mile_val);
 });
jQuery(".filter_campaigns_by").live('click',function(){
	 filter_deals_algorithm_by_location(getCookie("cat_remember"),getCookie("miles_cookie"),jQuery(this).attr('filter_location_id'));

    });

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
 
                     
         	var selected_cat_id="";
var     miles_cookie = "";
                  
                  
                   
                            selected_cat_id=getCookie("cat_remember");
                          
                       
                            miles_cookie=getCookie("miles_cookie");
                          
                    filter_deals_algorithm(selected_cat_id,miles_cookie);
            
                    
}
function bind_hover_effect()
{
   jQuery('.deal_blk').hover(
    function(){
        jQuery(this).css('border-radius','5px 5px 5px 5px')
        .css('box-shadow','0 0 10px rgba(0,0,0,0.35)')
        .css('opacity','1')
    },
    function(){
        jQuery('.deal_blk').each(function(){
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
 //   alert(category+","+miles)
//     jQuery(".arrow-up").attr("disp1","0");
//    jQuery(".arrow-up").hide();
//     jQuery("#shareit-box").hide();
//     jQuery("#shareit-box").attr("disp","0");
 //   infowindow.close();
   /// alert(category+"=="+miles);
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
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                    if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                                else{
                                                    $(".displayul").html(ele.clone());
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
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                    if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                                else{
                                                    $(".displayul").html(ele.clone());
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
   
              /* for location marker */
               var latlngbounds = new google.maps.LatLngBounds();
              for (var prop in markerArray) {
                   markerArray[prop].setIcon('./images/pin-small.png');
                                     markerArray[prop].setVisible(false);
                            }
                for(i=0;i<visible_location_arr.length;i++)
                {
                    //alert(visible_location_arr[i]);
                    v = parseInt(visible_location_arr[i]);
                 
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

                /* for location marker */
          //       alert(alldealcnt);
                /* for check deals avialable*/
                  if(alldealcnt == 0)
                    {
                       // alert(not_found_msg);
                        jQuery(".div_msg").html(not_found_msg);
                        jQuery(".div_msg").find("#span_miles").text(selected_miles);
                    }
                    else{
                         jQuery(".div_msg").html("");
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
jQuery("#shareit-field").keyup(function(){
   
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
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                    if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                                else{
                                                    $(".displayul").html(ele.clone());
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
                                                     ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                                                    if(tot_campaigns == 0){
                                                        ele1.find(".tot_deal_counter").parent().detach();
                                                    }else{
                                                   
                                                    ele1.find(".tot_deal_counter").text(tot_campaigns);
                                                    }
                                                }
                                                else{
                                                    $(".displayul").html(ele.clone());
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
                                        markerArray[prop].setVisible(false);
                                         markerArray[prop].setIcon('./images/pin-small.png');
                               }
                   for(i=0;i<visible_location_arr.length;i++)
                   {
                       //alert(visible_location_arr[i]);
                       v = parseInt(visible_location_arr[i]);

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

                   /* for location marker */
                 //   alert(alldealcnt);
                   /* for check deals avialable*/
                 //  alert(alldealcnt);
                     if(alldealcnt == 0)
                       {
                           jQuery(".div_msg").html(not_found_msg);
                           jQuery(".div_msg").find("#span_miles").text(selected_miles);
                       }
                       else{
                            jQuery(".div_msg").html("");
                       }
            }
            else{
               
                     if(alldealcnt == 0)
                       {
//                           jQuery(".div_msg").html('<a onclick="remove_filter()"  style="cursor:pointer"><div class="showalld">Show All Deals<br></div></a><hr>');
//                           jQuery(".div_msg").append("<br/>");
                           jQuery(".div_msg").html(not_found_msg);jQuery(".div_msg").find("#span_miles").text(selected_miles);
                       }
                       else{
                             jQuery(".div_msg").html('<a onclick="remove_filter()"  style="cursor:pointer"><div class="showalld">Show All Deals<br></div></a><hr>');
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
})
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
                                                    ele1 = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                                     ele1.find(".tot_deal_counter").parent().detach();
                                            }
                                                else{
                                                    $(".displayul").html(ele.clone()); 
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
                                                    ele1 = jQuery(".displayul .deal_blk[merid='"+$(this).attr("merid")+"'][locid='"+$(this).attr("locid")+"'][campid='"+$(this).attr("campid")+"']");
                                                     ele1.find(".tot_deal_counter").parent().detach();
                                            }
                                                else{
                                                    $(".displayul").html(ele.clone()); 
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
                    }
                    else{
                         jQuery(".div_msg").html('<a onclick="remove_filter()"  style="cursor:pointer"><div class="showalld">Show All Deals<br></div></a><hr>');
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
 jQuery(document).ready(function(){
   jQuery(".btnfilterstaticscampaigns").click(function(){
	var $s = jQuery.noConflict();
	$s(".btnfilterstaticscampaigns").each(function(index){
            $s(this).css("color","#3B3B3B");
        });
	
	 
        $s(this).css('color','orange');
	selected_cat_id = $s(this).attr("mycatid");
             jQuery(".displayul").html("");
			var cat_ele=$s(this);
			
       // open_popup('Notification');
                       // alert(selected_cat_id);       
		   var zoominglevel="<?php echo $_COOKIE['zoominglevel'];?>";
		        
			if(zoominglevel == "")
		        {
				zoominglevel="10";
			}
			 url = '<?= WEB_PATH ?>/cat_deal_detail.php';
                        customer_id="<?php echo $_SESSION['customer_id']; ?>";
			setCookie("cat_remember",selected_cat_id,365);
                       miles_cookie = getCookie("miles_cookie");
                       // alert(selected_cat_id);
                      
                    filter_deals_algorithm(selected_cat_id,miles_cookie);
    });
   
 });