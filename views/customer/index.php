<?php
/******** 
@USE : index page
@PARAMETER : 
@RETURN : 
@USED IN PAGES : header.php, footer.php, before-footer.php
*********/
//require_once("classes/Config.Inc.php");
//echo "dd";exit;
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Powering Smart Savings from Local Merchants</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="x-ua-compatible" content="IE=Edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    
<meta name="twitter:app:name:iphone" content="Scanflip"/>
<meta name="twitter:app:id:iphone" content="com.lanet.scanflip"/>
<!--<meta name="twitter:app:url:iphone" content="example://action/5149e249222f9e600a7540ef"/>-->
<meta name="twitter:app:name:ipad" content="Scanflip"/>
<meta name="twitter:app:id:ipad" content="com.lanet.scanflip"/>
<!--<meta name="twitter:app:url:ipad" content="example://action/5149e249222f9e600a7540ef"/>-->

<!--<script type="text/javascript" src="<?php echo WEB_PATH?>/js/jquery-1.6.2.min.js"></script>-->

<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/jquery.form.js"></script>
<!--<script type="text/javascript" src="<?php echo WEB_PATH?>/js/auto-clear.js"></script>-->
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/fadeslideshow.js"></script>
<script type="text/javascript">
var mygallery1=new fadeSlideShow({
	wrapperid: "fadeshow11", //ID of blank DIV on page to house Slideshow
	dimensions: [1024, 304], //width/height of gallery in pixels. Should reflect dimensions of largest image
	imagearray: [
		["./assets/images/c/slide/slide1.jpg","","",""],
		["./assets/images/c/slide/slide2.jpg","","",""],
		["./assets/images/c/slide/slide3.jpg","","",""],
		["./assets/images/c/slide/slide4.jpg","","",""] 
  	],
	displaymode: {type:'auto', pause:2500, cycles:0, wraparound:false},
	persist: false, //remember last viewed slide and recall within same session?
	fadeduration: 1500, //transition duration (milliseconds)
	descreveal: "ondemand",
	togglerid: ""
})

</script>
<link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
</head>
<body >

<?php
require_once(CUST_LAYOUT."/header.php");

?>

<div class="benner_block">
 <div class="my_main_div">
    <div id="fadeshow11"><!--end of slide--></div>
 </div>
 </div>
<div id="content" class="cantent">
			<div class="my_main_div">
				<div class="contentContainer">
					<div class="google_apps"> 
						<p><?php echo $client_msg["index"]["label_powering"];?>   </p>
                        <div class="apps-store">
                                <a target="_new" href="<?php echo $client_msg["index"]["android_app_link"];?>"></a>
                                <a target="_new" href="<?php echo $client_msg["index"]["iphone_app_link"];?>" class="google-play" ></a>
                            </div>
					</div>
				</div>
			</div>	
		</div>	
	
<script>
    jQuery(document).ready(function(){
 // alert("In"+getCookie("code")) ;
  if (document.cookie.indexOf(" " + "code" + "=") >= 0) {
   jQuery("#activation_code").val(getCookie("code"));
   //.setCookie("code",'',-365);
   del_cookie("code");
   jQuery("#btnActivationCode").trigger("click");
  }
});
function del_cookie(name)
{
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
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
    </script>

<?
require_once(CUST_LAYOUT."/footer.php");
?>
</body>
</html>
