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
            //  alert(val);
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
      x.innerHTML="User denied the request for Geolocation."
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