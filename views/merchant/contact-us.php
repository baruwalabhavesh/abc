<?php
/**
 * @uses contact us
 * @used in pages : header.php
 * @author Sangeeta Raghavani
 */


//require_once("../classes/Config.Inc.php");
require_once(LIBRARY."/class.phpmailer.php");

if(isset($_REQUEST['submit']))
{
	
	$mail = new PHPMailer();
	$body = "<p>Inquiry from Scanflip Merchant</p>";
	$body .= "<p>Name : ".$_REQUEST['name']."<br/>";
	$body .= "Email : ".$_REQUEST['email']."<br/>";
	$body .= "Phone : ".$_REQUEST['phone']."<br/>";
	$body .= "Comment : ".$_REQUEST['comment']."<br/></p>";
	$body .= "<p>Thank you,<br/>Scanflip Support Team.</p>";
	$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
	$mail->AddAddress($merchant_msg['contactus']['email']);
	$mail->From = "no-reply@scanflip.com";
	$mail->FromName = "ScanFlip Support";
	$mail->Subject    = "Inquiry from Scanflip Merchant";
	$mail->MsgHTML($body);      
	$mail->Send();	
		
}
?>
<!DOCTYPE HTML>    
<html>
<head>
<title>ScanFlip | Contact Us</title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/fancybox/jquery.fancybox.css" media="screen" />

<script type="text/javascript" src="<?=ASSETS_JS?>/m/fancybox/jquery.fancybox.js"></script> 

<script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true&.js"></script>
<!--<script type='text/javascript' src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>-->
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "address": [{
    "@type": "PostalAddress",
    "addressLocality": "LosAngeles",
    "addressRegion": "CA",
    "postalCode": "91303",
    "streetAddress": "21133 Victory Blvd Suite# 201"
  },
  {"@type": "PostalAddress",
    "addressLocality": "Toronto",
    "addressRegion": "ON",
    "postalCode": "M5V3A3",
    "streetAddress": "642 King Street West, Suite#200"
  }
  ],
  "name": "Scanflip.com ( Scanflip)",
  "legalName": "Scanflip Corp",
  "telephone": "(818)-883-7277",
  "url": "https://www.scanflip.com",
  "logo":"https://test.scanflip.com/assets/images/c/logo.png",
  "contactPoint" : {
    "@type" : "ContactPoint",
    "contactType" : "Customer Service",
    "telephone" : "+1-111-111-1111",
    "faxNumber" : "+1-111-111-1111",
    "contactOption" : "TollFree",
    "areaServed" : ["US","CA"],
    "availableLanguage" : "English",
    "email" : "support@scanflip.com"
}
}
</script>
<script>

function initialize() 
{
        var map_canvas = document.getElementById('map_canvas');
        var map_options = {
          center: new google.maps.LatLng(34.19317677785251,-118.5889596348877),
          zoom: 14,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        var map = new google.maps.Map(map_canvas, map_options)
	
		var contentString = "21133 Victory Blvd Suite# 201,</br>Los Angeles,CA 91303, Ph#818-883-7277";
		var latlng = new google.maps.LatLng(34.188562,-118.593337);
		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			zIndex: Math.round(latlng.lat() * -100000) << 5,
			title: "Scanflip Corp",
			icon: new google.maps.MarkerImage('<?=ASSETS_IMG?>/m/pin-small.png')
		});
		
		 infowindow = new google.maps.InfoWindow({
					size: new google.maps.Size(150, 50)
				});
		infowindow.setContent(contentString);
		infowindow.open(map, marker);
		
		google.maps.event.addListener(map, 'center_changed', function() {
			var c = map.getCenter();
			//alert(c.lat()+"=="+c.lng());
		});
}
google.maps.event.addDomListener(window,'load', initialize);

function initialize1() 
{
        var map_canvas = document.getElementById('map_canvas1');
        var map_options = {
          center: new google.maps.LatLng(43.64935117786162,-79.39800394177246),
          zoom: 14,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        var map = new google.maps.Map(map_canvas, map_options)
		
		
		var contentString = "642 King Street West Suite# 200,</br>Toronto,ON,M5V 1M7, Ph#818-883-7277";
		var latlng = new google.maps.LatLng(43.644631,-79.401523);
		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			zIndex: Math.round(latlng.lat() * -100000) << 5,
			title: "Scanflip Corp",
			icon: new google.maps.MarkerImage('<?=ASSETS_IMG?>/m/pin-small.png')
		});
		
		 infowindow = new google.maps.InfoWindow({
					size: new google.maps.Size(150, 50)
				});
		infowindow.setContent(contentString);
		infowindow.open(map, marker);
		
		google.maps.event.addListener(map, 'center_changed', function() {
			var c = map.getCenter();
			//alert(c.lat()+"=="+c.lng());
		});

				
}
google.maps.event.addDomListener(window,'load', initialize1);


</script>

</head>
<body>
<div id="dialog-message" style="display:none;"></div>
<div class="my_main_div">
<!--start header--><div class="my_inner_div">

		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	<div id="content">

            <div class="contactus_div" >
			<div class="contactus_scanfliptext" >Scanflip is always striving to provide businesses with the most cost effective marketing tools to win over and retain the right customers.If you're interested in partnering with us , please register on-line. Regarding customer support, technical or mobile application issue please submit contact us form below or give us a call and Scanflip representative will be ready to assist you.</div>
			<p class="contact-fromheading" >Contact Form:</p>
			<form method="post">
			<table border="0">
	<tbody><tr>
		<td><?php echo $merchant_msg["contactus"]["label_name"] ?></td>
		<td><input value="" size="30" name="name" id="name" type="text"></td>
		<td></td>
	</tr>
	<tr>
		<td><?php echo $merchant_msg["contactus"]["label_email"] ?></td>
		<td><input value="" size="30" name="email" id="email" type="text"></td>
		<td></td>
	</tr>
	<tr>
		<td><?php echo $merchant_msg["contactus"]["label_phone"] ?></td>
		<td><input value="" size="30" name="phone" id="phone" type="text"></td>
		<td></td>
	</tr>
	<tr>
		<td><?php echo $merchant_msg["contactus"]["label_comment"] ?></td>
		<td><textarea cols="40" rows="6" name="comment" id="comment" ></textarea></td>
		<td></td>
	</tr>
	<tr>
		<td class= "vertically_top1"><?php echo $merchant_msg["contactus"]["label_captcha"] ?></td>
		<td class="can_last"><input type="text" id="mycaptcha" name="mycaptcha" /><br><img src="get_captcha_m_r.php" alt="" id="captcha" />&nbsp;<a id="captcha_image" href="javascript:void(0)"><?php echo $client_msg["login_register"]["label_captcha_different"];?></a></td>
		<td></td>
	</tr>
</tbody></table>
			
<br>
<div class="contactus-submit" ><input id="submit" type="submit" name="submit" value="Submit"></input></div>
</form>
<br></br>
			<p>
            <div class="located_heading" >We Are Located At:</div>
            </p>
			<!--<iframe width="425" scrolling="no" height="350" frameborder="0" marginheight="0" marginwidth="0" src="https://maps.google.co.in/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=21133+Victory+Blvd+Suite%23+201+Los+Angeles+,+CA+91303+&amp;aq=&amp;sspn=7.044452,13.392334&amp;ie=UTF8&amp;hq=&amp;hnear=21133+Victory+Blvd+%23201,+Los+Angeles,+California+91303,+United+States&amp;t=m&amp;z=14&amp;output=embed"></iframe>-->
			<div id="map_canvas" ></div>	
			
			<!--<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.co.in/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=642+King+Street+West+,+Suite%23+200+,+Toronto+,+ON&amp;aq=&amp;sspn=0.012318,0.026157&amp;ie=UTF8&amp;hq=&amp;hnear=642+King+St+W+%23200,+Toronto,+Ontario+M5V+1M7,+Canada&amp;t=m&amp;z=14&amp;output=embed"></iframe>-->
			<!--<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.de/maps?f=q&source=s_q&hl=de&geocode=&q=Harrods,+Brompton+Road,+London,+Gro%C3%9Fbritannien&aq=0&oq=harrods&sll=51.500808,-0.143003&sspn=0.006532,0.016512&g=Buckingham+Palace,+London,+United+Kingdom&ie=UTF8&hq=Harrods,+Brompton+Road,+London,+Gro%C3%9Fbritannien&hnear=&radius=15000&t=m&cid=5481296058834203814&z=13&iwloc=A&output=embed"></iframe>-->
			<div id="map_canvas1"></div>
		</div>
		
		
    </div>
<div class="cls_clear"></div>
	<!--end of content--></div>
    <!--start footer--><div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>
		<!--end of footer--></div>
<!--end of contentContainer--></div>
	

</div>

</body>
</html>
<script type="text/javascript">
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
jQuery("#submit").click(function(){
	var msg="";
	var flag="true";
	
	if(jQuery("#name").val() == "")
	{
		msg +="<div><?php echo $merchant_msg['contactus']['Msg_name']; ?></div>";
		flag="false";
	}
	
	if(email_validation(jQuery("#email").val()) == false)
	{
		msg +="<div><?php echo $merchant_msg['contactus']['Msg_email']; ?></div>";
		flag="false";
	}
	
	if(jQuery("#phone").val() == "")
	{
		msg +="<div><?php echo $merchant_msg['contactus']['Msg_phone']; ?></div>";
		flag="false";
	}
	
	if(jQuery("#comment").val() == "")
	{
		msg +="<div><?php echo $merchant_msg['contactus']['Msg_comment']; ?></div>";
		flag="false";
	}
	
	if(jQuery("#mycaptcha").val() == "")
	{
		msg +="<div><?php echo $merchant_msg['contactus']['Msg_captcha']; ?></div>";
		flag="false";
	}
	else
	{
		var code=jQuery("#mycaptcha").val();
		jQuery.ajax({
			   url:"process.php",
			   data:"captchacode_check_m_r=yes&code="+code,
			   cache: false,
			   async: false,
			   success: function(result){
					if(result == "1")
					{
						flag="true";
					}
					else
					{
						flag="false";
						msg +="<div><?php echo $merchant_msg['contactus']['Msg_captcha']; ?></div>";
					}
			
			 
				}
		  });
	}
	
	if(jQuery("#name").val() == "" || jQuery("#email").val() == "" || jQuery("#phone").val()== "" || jQuery("#comment").val()== "")
	{
		flag="false";
	}
	
	var head_msg="<div class='head_msg'>Message Box</div>"
	var content_msg="<div class='content_msg'>"+msg+"</div>";
	var footer_msg="<div ><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
	jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
	
	if(flag == "false")
	{
			jQuery.fancybox({
									content:jQuery('#dialog-message').html(),

									type: 'html',
				
									openSpeed  : 300,

									closeSpeed  : 300,
									// topRatio: 0,

									changeFade : 'fast',  
									beforeShow : function(){
										$(".fancybox-inner").addClass("msgClass");
									},

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
});

jQuery('#captcha_image').click(function(){  			
	change_captcha();
});

function change_captcha()
{
document.getElementById('captcha').src="get_captcha_m_r.php?rnd=" + Math.random();
}

jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
</script>
