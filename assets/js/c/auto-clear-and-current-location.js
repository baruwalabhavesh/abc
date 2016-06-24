/*
Onclick clean input field 
*/
var visitorGeolocation;
function clickclear(thisfield, defaulttext) 
{
	/*
	if (thisfield.value == defaulttext) 
	{
		thisfield.value = "";
	}
	*/
	if (thisfield.name == "activation_code") 
	{
		if (thisfield.value == defaulttext) 
		{
			thisfield.value = "";
		}
	}
}
function clickrecall(thisfield, defaulttext) 
{    
	if (thisfield.name == "activation_code") 
	{
		if (thisfield.value == "") 
		{
			  thisfield.value = defaulttext;
		}
	}
	else
	{
		if (thisfield.value == "") 
		{
			  thisfield.value = document.getElementById('hdn_serach_value').value;
		}
	}

}

 var myVar ;
 function medaitor(val)
 {
     //alert("In mediator");
      myVar=setInterval(function(){myTimer()},30000);
      getLocation1(val);
	 //alert("out mediator"); 
 }
function getLocation1(val)
{
  if (navigator.geolocation)
  {
      //alert("In in");
		navigator.geolocation.getCurrentPosition(showPosition1 , function(errorCode) {
                   
                     check_geolocation_support();
					//alert(errorCode.code);
                    if (errorCode.code == 0) {
					
			  // alert("Please enter your current location or postal code to search offers near you.");
					  visitorGeolocation = new geolocate(false, true, 'visitorGeolocation');
						var callback = function(){
								setCookie("mycurrent_lati",visitorGeolocation.getField('latitude'),365);
								setCookie("mycurrent_long",visitorGeolocation.getField('longitude'),365);
								setCookie("searched_location",visitorGeolocation.getField('cityName')+","+visitorGeolocation.getField('regionName')+","+visitorGeolocation.getField('countryName'),365);
								  window.location.reload(false);
							};
						visitorGeolocation.checkcookie_geo(callback);
                           //display_fancybox("Please enter your current location or postal code to search offers near you.");
                          clearInterval(myVar);
                             //  enter_zip_code_popup();
			}
                        if (errorCode.code == 3) {
						
						visitorGeolocation = new geolocate(false, true, 'visitorGeolocation');
	var callback = function(){
			setCookie("mycurrent_lati",visitorGeolocation.getField('latitude'),365);
			setCookie("mycurrent_long",visitorGeolocation.getField('longitude'),365);
			setCookie("searched_location",visitorGeolocation.getField('cityName')+","+visitorGeolocation.getField('regionName')+","+visitorGeolocation.getField('countryName'),365);
			  window.location.reload(false);
		};
	visitorGeolocation.checkcookie_geo(callback);
			//   alert("Please enter your current location or postal code to search offers near you.");
                     //   display_fancybox("Please enter your current location or postal code to search offers near you.");
                          clearInterval(myVar);
                            //   enter_zip_code_popup();
			}
			if (errorCode.code == 1) {
		
                      //display_fancybox("Cannot retrieve your current location.Please enable location sharing for your browser.");
						visitorGeolocation = new geolocate(false, true, 'visitorGeolocation');
					var callback = function(){
							setCookie("mycurrent_lati",visitorGeolocation.getField('latitude'),365);
							setCookie("mycurrent_long",visitorGeolocation.getField('longitude'),365);
							setCookie("searched_location",visitorGeolocation.getField('cityName')+","+visitorGeolocation.getField('regionName')+","+visitorGeolocation.getField('countryName'),365);
							  window.location.reload(false);
						};
					visitorGeolocation.checkcookie_geo(callback); 
                             clearInterval(myVar);
                             //  enter_zip_code_popup();
			}
			if( errorCode.code == 2)
			{
		
                           
				//alert("We can't find your current location.Please enter your current location or postal code to search offers near you.");
                              // alert("Cannot find your location. Make sure your network connection is active and click the link request current location to try again.");
                                display_fancybox("Cannot find your location. Make sure your network connection is active and click the link request current location to try again.");
							   visitorGeolocation = new geolocate(false, true, 'visitorGeolocation');
					var callback = function(){
							setCookie("mycurrent_lati",visitorGeolocation.getField('latitude'),365);
							setCookie("mycurrent_long",visitorGeolocation.getField('longitude'),365);
							setCookie("searched_location",visitorGeolocation.getField('cityName')+","+visitorGeolocation.getField('regionName')+","+visitorGeolocation.getField('countryName'),365);
							  window.location.reload(false);
						};
					visitorGeolocation.checkcookie_geo(callback); 
                                clearInterval(myVar);
                                // enter_zip_code_popup();
			 }
		});
    }
  else
  {
     
         if(val == ""){
              //display_fancybox("Geolocation is not supported by your current browser");
			  visitorGeolocation = new geolocate(false, true, 'visitorGeolocation');
	var callback = function(){
			setCookie("mycurrent_lati",visitorGeolocation.getField('latitude'),365);
			setCookie("mycurrent_long",visitorGeolocation.getField('longitude'),365);
			setCookie("searched_location",visitorGeolocation.getField('cityName')+","+visitorGeolocation.getField('regionName')+","+visitorGeolocation.getField('countryName'),365);
			  window.location.reload(false);
		};
	visitorGeolocation.checkcookie_geo(callback);
			  
          }
          else{
        visitorGeolocation = new geolocate(false, true, 'visitorGeolocation');
	var callback = function(){
			setCookie("mycurrent_lati",visitorGeolocation.getField('latitude'),365);
			setCookie("mycurrent_long",visitorGeolocation.getField('longitude'),365);
			setCookie("searched_location",visitorGeolocation.getField('cityName')+","+visitorGeolocation.getField('regionName')+","+visitorGeolocation.getField('countryName'),365);
			  window.location.reload(false);
		};
	visitorGeolocation.checkcookie_geo(callback);
          // enter_zip_code_popup();
          }
             clearInterval(myVar);
             
             //  enter_zip_code_popup();
           
  }
          
        
          
}
  
  function showPosition1(position)
  {
   //alert("In show postion  function");
       clearInterval(myVar);
	  
              var val;
              val="Latitude: " + position.coords.latitude + 
              "<br />Longitude: " + position.coords.longitude;
   
               setCookie("mycurrent_lati",position.coords.latitude,365);
               setCookie("mycurrent_long",position.coords.longitude,365);
//               setCookie("cat_remember",0,365);
//               setCookie("miles_cookie",50,365);
               setCookie("curr_address","yes",365);
            //  alert(val);
              check_geolocation_support();
                window.location.reload(false);
  }
//,showError,{enableHighAccuracy:true}
//alert("call js");
function getLocation()
  {
  //alert("call getLocation");
          
  if (navigator.geolocation)
    {
    navigator.geolocation.getCurrentPosition(showPosition,showError);
    }
  else{
              alert("Geolocation is not supported by this browser.");
  }
  }
function showPosition(position)
  {
  //alert("show position");
              var val;
              val="Latitude: " + position.coords.latitude + 
              "<br />Longitude: " + position.coords.longitude;
              document.getElementById("my_latitude").value = position.coords.latitude;
              //alert(document.getElementById("my_latitude").value );
              document.getElementById("my_longitude").value = position.coords.longitude;
              //alert(document.getElementById("my_longitude").value );
              document.getElementById("zip").value = "Current Location";
              document.getElementById("slider_iframe").src = WEB_PATH+"/search-deal-map.php?latitude="+position.coords.latitude+"&longitude="+position.coords.longitude;
			 
  }
  function showError(error)
  {
      
  
  switch(error.code) 
    {
    case error.PERMISSION_DENIED:
        alert("User denied the request for Geolocation.");
      break;
    case error.POSITION_UNAVAILABLE:
      x.innerHTML="Location information is unavailable."
      break;
    case error.TIMEOUT:
      x.innerHTML="The request to get user location timed out."
      break;
    case error.UNKNOWN_ERROR:
      x.innerHTML="An unknown error occurred."
      break;
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
	
	function setCookie(c_name,value,exdays)
	{
     var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
	}
	//
	//function getLocation()
	//	  {
	//	  if (navigator.geolocation)
	//		{
	//		navigator.geolocation.getCurrentPosition(showPosition);
	//		}
	//	  else{
	//			var locationmsg=document.getElementById("demo");
	//			locationmsg.innerHTML="Geolocation is not supported by this browser.";
	//		}
	//	  }
	// 
	//function showPosition(position)
	//	  {		  
	//	  document.getElementById("zip").value = "Current Location";
	//	  document.getElementById("my_latitude").value = position.coords.latitude;
	//	  document.getElementById("my_longitude").value = position.coords.longitude;
	//	  //document.getElementById("slider_iframe").src = WEB_PATH+"/search-deal-map.php?latitude="+position.coords.latitude+"&longitude="+position.coords.longitude;
	//	  }

function display_fancybox(msg)
{
     jQuery.fancybox({
            href: this.href,
            //href: $(val).attr('mypopupid'),
            content:jQuery('#enter_zipcode_div').html(),
            width: 470,
            height:98,
            type: 'html',
            openEffect : 'elastic',
            openSpeed  : 300,
            scrolling : 'no',
            closeEffect : 'elastic',
            closeSpeed  : 300,
            afterShow:function(){
              //  alert("In after");
                 jQuery(".fancybox-inner").css("height","98px");
            },
            beforeShow:function(){
              //  alert("In before");
                //alert(jQuery("#activation_code").val());
               //  setCookie("code",jQuery("#activation_code").val(),365);
                   // $(".fancybox-inner").css("height","98px");
                    jQuery(".fancybox-inner").addClass("enterZipcode");
                    jQuery(".fancybox-inner .geo_errormsg").html(msg);
            },
            helpers: {
                    overlay: {
                    opacity: 0.3,
                    closeClick:false
                    } // overlay
            },
            enableEscapeButton : false,
            keys:{
                close:null
            },
            showCloseButton: false,
            
'afterShow': function() {
    jQuery('.fancybox-close').attr('id','close');
    //override fancybox_close btn
    
 jQuery("#close").unbind("click");
   jQuery("#close").detach();
}
            
            // helpers
    });
}

function getLocation_qrcode()
{
  if (navigator.geolocation)
  {
  		navigator.geolocation.getCurrentPosition(showPosition_qrcode , function(errorCode) {
                    if (errorCode.code == 0) {
			}
                        if (errorCode.code == 3) {
			}
			if (errorCode.code == 1) {
                      }
			if( errorCode.code == 2)
			{
                         }
		});
    }
  else
  {
     
            alert("Geolocation is not supported by this browser");
         
  }
          
        
          
}
  
  function showPosition_qrcode(position)
  {
   
       
              var val;
              val="Latitude: " + position.coords.latitude + 
              "<br />Longitude: " + position.coords.longitude;
   
               setCookie("mycurrent_lati_qrcode",position.coords.latitude,365);
               setCookie("mycurrent_long_qrcode",position.coords.longitude,365);
                 setCookie("mycurrent_lati",position.coords.latitude,365);
               setCookie("mycurrent_long",position.coords.longitude,365);
                window.location.reload(false);
  }
  
  
  function geolocate(timezone, cityPrecision, objectVar) {
 
  var api = (cityPrecision) ? "ip-city" : "ip-country";
  var domain = 'api.ipinfodb.com';
  var url = "http://" + domain + "/v3/" + api + "/?key=7b2dc8cc9925cc391425a522442fa34e74fc309d1b7c03e159d944e08cf5a311&format=json" + "&callback=" + objectVar + ".setGeoCookie";
  var geodata;
  var callbackFunc;
  var JSON = JSON || {};
 
  // implement JSON.stringify serialization
  JSON.stringify = JSON.stringify || function (obj) {
    var t = typeof (obj);
    if (t != "object" || obj === null) {
      // simple data type
      if (t == "string") obj = '"'+obj+'"';
        return String(obj);
    } else {
    // recurse array or object
      var n, v, json = [], arr = (obj && obj.constructor == Array);
      for (n in obj) {
        v = obj[n]; t = typeof(v);
        if (t == "string") v = '"'+v+'"';
        else if (t == "object" && v !== null) v = JSON.stringify(v);
        json.push((arr ? "" : '"' + n + '":') + String(v));
      }
      return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
    }
  };
 
  // implement JSON.parse de-serialization
  JSON.parse = JSON.parse || function (str) {
    if (str === "") str = '""';
      eval("var p=" + str + ";");
      return p;
  };
 
  //Check if cookie already exist. If not, query IPInfoDB
  this.checkcookie_geo = function(callback) {
    geolocationCookie = getCookie_geo('geolocation');
    callbackFunc = callback;
    if (!geolocationCookie) {
      getGeolocation();
    } else {
      geodata = JSON.parse(geolocationCookie);
      callbackFunc();
    }
  }
 
  //API callback function that sets the cookie with the serialized JSON answer
  this.setGeoCookie = function(answer) {
    if (answer['statusCode'] == 'OK') {
      JSONString = JSON.stringify(answer);
     // setCookie('geolocation', JSONString, 365);
      geodata = answer;
      callbackFunc();
    }
  }
 
  //Return a geolocation field
  this.getField = function(field) {
    try {
      return geodata[field];
    } catch(err) {}
  }
 
  //Request to IPInfoDB
  function getGeolocation() {
    try {
      script = document.createElement('script');
      script.src = url;
      document.body.appendChild(script);
    } catch(err) {}
  }
 
  //Set the cookie
  function setCookie_geo(c_name, value, expire) {
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+expire);
    document.cookie = c_name+ "=" +escape(value) + ((expire==null) ? "" : ";expires="+exdate.toGMTString());
  }
 
  //Get the cookie content
  function getCookie_geo(c_name) {
    if (document.cookie.length > 0 ) {
      c_start=document.cookie.indexOf(c_name + "=");
      if (c_start != -1){
        c_start=c_start + c_name.length+1;
        c_end=document.cookie.indexOf(";",c_start);
        if (c_end == -1) {
          c_end=document.cookie.length;
        }
        return unescape(document.cookie.substring(c_start,c_end));
      }
    }
    return '';
  }
}
//var visitorGeolocation = new geolocate(false, true, 'visitorGeolocation');
 
//Check for cookie and run a callback function to execute after geolocation is read either from cookie or IPInfoDB API
/*var callback = function(){
                alert('Visitor country code : ' + visitorGeolocation.getField('latitude')+"=="+visitorGeolocation.getField('longitude'))
               }; 
visitorGeolocation.checkcookie_geo(callback);*/