<?php
/******** 
@USE : qrcode scan functions
@PARAMETER : 
@RETURN : 
@USED IN PAGES : call from scanning
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
include_once(LIBRARY.'/ip2locationlite.class.php');
//$objDB = new DB();
//$objDBWrt = new DB('write');
$redirect_str = "";
 $cookie_life = time() + 31536000;
 $campaignid = 0;
 $ask = 0;
function create_unique_code()
{
    $code_length=8;
    //echo $alfa = "1AB2CD3EF4G5HI6JK7LM8N9OP10QRSTU".$campaign_id."VWXYZ";
    $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $code="";
    for($i = 0; $i < $code_length; $i ++) 
    {
      $code .= $alfa[rand(0, strlen($alfa)-1)];
    } 
    return $code;
}
$qrcode= base64_decode($_REQUEST['qrcode']);

/* $sql_qrcode = "select id from qrcodes where qrcode='".$qrcode."' ";
$RS_qrcode = $objDB->Conn->Execute($sql_qrcode ); */
$RS_qrcode = $objDB->Conn->Execute("select id from qrcodes where qrcode=?",array($qrcode));

$q_id = $RS_qrcode->fields['id'];

/* $sql = "select campaign_id from  qrcode_campaign where qrcode_id =".$q_id;
$RS = $objDB->Conn->Execute($sql); */

//$RS = $objDB->Conn->Execute("select campaign_id from  qrcode_campaign where qrcode_id =?",array($q_id));
$RS = $objDB->Conn->Execute("select id 'campaign_id' from campaigns where qrcode_id =?",array($q_id));

$_qid=0;
$_campid =0;
$_locationid =0;
$_islocation =0;
  $cookie_time = time()+(20 * 365 * 24 * 60 * 60);

        
////insert data when scan qrcode
if( ! isset($_COOKIE['scanflip_customer_id'])){
    $custid = 0;
}
else{
    $custid = $_COOKIE['scanflip_customer_id'];
}
$uniqueid = create_unique_code()."".strtotime(date("Y-m-d H:i:s"));

//$custid = 99;
//setcookie('scanflip_scan_qrcode', "", time()-36666);
// exit;
//
if($RS->RecordCount()!= 0){
    $campaignid= $RS->fields['campaign_id'];
if(isset($_COOKIE['mycurrent_lati_qrcode']))
{
    
    if($_COOKIE['mycurrent_lati'] != "" && $_COOKIE['mycurrent_long'] != "" )
	{
        $mlatitude = $_COOKIE['mycurrent_lati'];
    $mlongitude = $_COOKIE['mycurrent_long'];
	//exit;
     $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$_COOKIE["mycurrent_lati"] .','.$_COOKIE["mycurrent_long"].'&sensor=false');
     $output= json_decode($geocode);

     $current_address_string = $output->results[0]->formatted_address;
     $current_address_string = str_replace("+"," ",$current_address_string);
     setcookie("searched_location", $current_address_string,$cookie_life);
	//////////  if user login  then set currant latitude and longitude ///////
        if(isset($_SESSION['customer_id']))
        {
           
            $timezone1  = getClosestTimezone($_COOKIE["mycurrent_lati"] ,$_COOKIE["mycurrent_long"]);
                 $timezone = new DateTimeZone($timezone1);
                  $offset1   = $timezone->getOffset(new DateTime);
                    //timezone_offset_string( $offset1 );
                 $tz = timezone_offset_string( $offset1 );
                 $curr_timezone = $tz;
     
          
       if($_COOKIE['mycurrent_lati']!="" && $curr_timezone != "" ){
            /* $update_sql = "Update customer_user set current_location ='".$current_address_string."' , curr_latitude='".$_COOKIE['mycurrent_lati']."' , curr_longitude='".$_COOKIE['mycurrent_long']."' ,curr_timezone= '".$curr_timezone."'  where id=".$_SESSION['customer_id'];
			// echo $update_sql;
            $objDB->Conn->Execute($update_sql); */
            $objDBWrt->Conn->Execute("Update customer_user set current_location =?, curr_latitude=?, curr_longitude=?,curr_timezone=? where id=?",array($current_address_string,$_COOKIE['mycurrent_lati'],$_COOKIE['mycurrent_long'],$curr_timezone,$_SESSION['customer_id']));
            }
        }
        //exit();
     ///////////  if user login  then set currant latitude and longitude /////// 
  
    }
    

//$sql = "select created_by from  campaigns where id =".$q_id;
//$RS = $objDB->Conn->Execute($sql);


$ip_addr = $_SERVER['REMOTE_ADDR'];
$ipLite = new ip2location_lite;
$ipLite->setKey('7b2dc8cc9925cc391425a522442fa34e74fc309d1b7c03e159d944e08cf5a311');
 
//Get errors and locations
$locations = $ipLite->getCity($_SERVER['REMOTE_ADDR']);
$lat = $location['latitude'];
$long = $location['longitude'];
/*$geoplugin = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip_addr) );

if ( is_numeric($geoplugin['geoplugin_latitude']) && is_numeric($geoplugin['geoplugin_longitude']) ) {

$lat = $geoplugin['geoplugin_latitude'];
$long = $geoplugin['geoplugin_longitude'];
} */
$lat = $_COOKIE['mycurrent_lati_qrcode'];
$long = $_COOKIE['mycurrent_long_qrcode'];
//echo $lat.';'.$long;

	/* $Sql  = "SELECT l.id location_id ,l.location_name,l.address,l.city,l.state
 ,l.zip,l.country,l.picture,l.latitude,l.longitude,l.is_open,round((((acos(sin((".$lat."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$lat."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$long."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) AS distance 
       FROM campaign_location cl inner join locations l on l.id = cl.location_id 
where cl.offers_left>0 and  cl.campaign_id=".$campaignid." and cl.active =1
        ORDER BY distance" ;
    $RS_location = $objDB->Conn->Execute($Sql); */
    $RS_location = $objDB->Conn->Execute("SELECT l.id location_id ,l.location_name,l.address,l.city,l.state
 ,l.zip,l.country,l.picture,l.latitude,l.longitude,l.is_open,round((((acos(sin((? *pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((? *pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((? - `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) AS distance 
       FROM campaign_location cl inner join locations l on l.id = cl.location_id 
where cl.offers_left>0 and  cl.campaign_id=? and cl.active =1
        ORDER BY distance",array($lat,$lat,$long,$campaignid));
    
    $locationid= $RS_location->fields['location_id'];

    if($RS_location->RecordCount() == 1){
        setcookie('is_scanflip_scan_qrcode',$q_id, $cookie_time);
        //$redirect_str = WEB_PATH."/qr-location.php?campaign_id=".$campaignid."&l_id=".$locationid;
     $redirect_str = $RS_location->fields['permalink'];
	 }
	 else if($RS_location->RecordCount() > 1){
	 setcookie('is_scanflip_scan_qrcode',$q_id, $cookie_time);
		$redirect_str = WEB_PATH."/qr-location.php?campaign_id=".$campaignid."&l_id=".$locationid;
	}else{
         $redirect_str =  WEB_PATH."/search-deal.php";
    }
}
}
else{
   
    /* $sql = "select location_id  from  qrcode_location where qrcode_id  =".$q_id;
	// echo $sql ;
    $RS_loc = $objDB->Conn->Execute($sql); */
    
    //$RS_loc = $objDB->Conn->Execute("select location_id  from  qrcode_location where qrcode_id  =?",array($q_id));
    $RS_loc = $objDB->Conn->Execute("select id 'location_id' from locations where qrcode_id =?",array($q_id));
    
    if($RS_loc->RecordCount() != 0)
    {
        $locationid = $RS_loc->fields['location_id'];
		
		/* $sql_l  = "select latitude,longitude , zip from  locations where id  =".$RS_loc->fields['location_id'];
        $RS_locdetail = $objDB->Conn->Execute($sql_l); */
        $RS_locdetail = $objDB->Conn->Execute("select latitude,longitude , zip from  locations where id  =?",array($RS_loc->fields['location_id']));
        
   // echo $sql ;
       setcookie('is_scanflip_scan_qrcode',$q_id, $cookie_time);
       if(isset($_COOKIE['mycurrent_lati']) && isset($_COOKIE['mycurrent_long'])) {
           
       }
       else{
             setcookie("mycurrent_lati",$RS_locdetail->fields['latitude'],$cookie_time);
              setcookie("mycurrent_long",$RS_locdetail->fields['longitude'],$cookie_time);
             setcookie("searched_location",$RS_locdetail->fields['zip'],$cookie_time);
       }
	 /* $redirect_query_location = "select location_permalink from locations where id=".$locationid;
	 $RS_redirect_query = $objDB->Conn->Execute($redirect_query_location); */
	 $RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($locationid));	
	 
	 $location_url = $RS_redirect_query->fields['location_permalink'];
           //$redirect_str = WEB_PATH."/location_detail.php?id=".$locationid;
		    $redirect_str = $location_url;
    }
    else{
       
       /* $Sql  = "select * from qrcode_group g , qrcodegroup_qrcode qq where g.id = qq.qrcodegroup_id and qq.qrcode_id  =".$q_id."  ";
       //echo $Sql."<br />";
       $RS_is_assigned = $objDB->Conn->Execute($Sql); */
       $RS_is_assigned = $objDB->Conn->Execute("select * from qrcode_group g , qrcodegroup_qrcode qq where g.id = qq.qrcodegroup_id and qq.qrcode_id  =?",array($q_id));
       
       //  print_r($RS_is_assigned);
         //echo $RS_is_assigned->fields['merchant_id']."==";
        // exit();
         if($RS_is_assigned->fields['merchant_id'] != 0){
        //if()
             $qrcodegenerator = $RS_is_assigned->fields['merchant_id'];
            
 if(isset($_COOKIE['mycurrent_lati_qrcode']))
{
    
    if($_COOKIE['mycurrent_lati'] != "" && $_COOKIE['mycurrent_long'] != "" )
	{
        $mlatitude = $_COOKIE['mycurrent_lati'];
    $mlongitude = $_COOKIE['mycurrent_long'];
	//exit;
     $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$_COOKIE["mycurrent_lati"] .','.$_COOKIE["mycurrent_long"].'&sensor=false');
     $output= json_decode($geocode);

     $current_address_string = $output->results[0]->formatted_address;
     $current_address_string = str_replace("+"," ",$current_address_string);
     setcookie("searched_location", $current_address_string,$cookie_life);
	//////////  if user login  then set currant latitude and longitude ///////
        if(isset($_SESSION['customer_id']))
        {
           
            $timezone1  = getClosestTimezone($_COOKIE["mycurrent_lati"] ,$_COOKIE["mycurrent_long"]);
                 $timezone = new DateTimeZone($timezone1);
                  $offset1   = $timezone->getOffset(new DateTime);
                    //timezone_offset_string( $offset1 );
                 $tz = timezone_offset_string( $offset1 );
                 $curr_timezone = $tz;
     
          
       if($_COOKIE['mycurrent_lati']!="" && $curr_timezone != "" ){
            /* $update_sql = "Update customer_user set current_location ='".$current_address_string."' , curr_latitude='".$_COOKIE['mycurrent_lati']."' , curr_longitude='".$_COOKIE['mycurrent_long']."' ,curr_timezone= '".$curr_timezone."'  where id=".$_SESSION['customer_id'];
			// echo $update_sql;
            $objDB->Conn->Execute($update_sql); */
            $objDBWrt->Conn->Execute("Update customer_user set current_location =?, curr_latitude=?, curr_longitude=?,curr_timezone=? where id=?",array($current_address_string,$_COOKIE['mycurrent_lati'],$_COOKIE['mycurrent_long'],$curr_timezone,$_SESSION['customer_id']));
            }
        }
     $lat = $_COOKIE['mycurrent_lati_qrcode'];
		$long = $_COOKIE['mycurrent_long_qrcode'];
		
		/* $Sql  = "SELECT *, ( 3959 * acos( cos( radians($lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($long) ) + 
            sin( radians($lat) ) * sin( radians( 
            latitude ) ) ) ) AS distance 
        FROM locations where created_by =".$qrcodegenerator."  and active=1 
        ORDER BY distance LIMIT 1" ;
		//echo $Sql;
		//  exit;
		$RS_location = $objDB->Conn->Execute($Sql); */
		$RS_location = $objDB->Conn->Execute("SELECT *, ( 3959 * acos( cos( radians($lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($long) ) + 
            sin( radians($lat) ) * sin( radians( 
            latitude ) ) ) ) AS distance 
        FROM locations where created_by =?  and active=1 
        ORDER BY distance LIMIT 1",array($qrcodegenerator));
		
    if( $RS_location->RecordCount() != 0){
        $locationid= $RS_location->fields['id'];
        
        /* $sql_l  = "select latitude,longitude , zip from  locations where id  =".$RS_location->fields['id'];
		$RS_locdetail = $objDB->Conn->Execute($sql_l); */
		$RS_locdetail = $objDB->Conn->Execute("select latitude,longitude , zip from  locations where id  =?",array($RS_location->fields['id']));
		
   // echo $sql ;
       setcookie('is_scanflip_scan_qrcode',$q_id, $cookie_time);
       if(isset($_COOKIE['mycurrent_lati']) && isset($_COOKIE['mycurrent_long'])) {
           
       }
       else{
             setcookie("mycurrent_lati",$RS_locdetail->fields['latitude'],$cookie_time);
              setcookie("mycurrent_long",$RS_locdetail->fields['longitude'],$cookie_time);
             setcookie("searched_location",$RS_locdetail->fields['zip'],$cookie_time);
       }
		/* $redirect_query_location = "select location_permalink from locations where id=".$locationid;
		$RS_redirect_query = $objDB->Conn->Execute($redirect_query_location); */
		$RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($locationid));
		
	 $location_url = $RS_redirect_query->fields['location_permalink'];
        //$redirect_str = WEB_PATH."/location_detail.php?id=".$locationid;
		$redirect_str = $location_url ;
}else{
         $redirect_str = WEB_PATH."/search-deal.php";
    }
}
   
  //  echo $ip_addr.';'.$lat.';'.$long;

   
    }
         }
    
    else{
       // echo 'innn';
     //   $qrcodegenerator = $RS_is_assigned->fields['merchant_id'];
      setcookie('is_scanflip_scan_qrcode',$q_id, $cookie_time);
      $redirect_str = WEB_PATH."/search-deal.php"; 
    }
    }

}
?>
<html>
    <head>
        <title>
        </title>        
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/template.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS ;?>/m/jquery.confirm.css" />
<style>
    .fancybox-outer{
        margin:0;
        text-align: center;
    }
   .div_heading {
        font-family: Verdana;
    font-size: 31px;
    padding-bottom: 10px;
    }
    
</style>
 <script src="<?=ASSETS_JS?>/c/jquery-1.4.1.min.js"></script>
 <script src="<?php echo ASSETS_JS; ?>/c/auto-clear-and-current-location.js"></script>
    </head>
    <body class="scan_screen">
        <div class="fancybox" href="#popupbox" id="fc">&nbsp;</div>
        <div  id="popupbox" style="" class="fancybox-outer">
            <div class="div_heading"><img border="0px" alt="ScanFlip Logo" src="<?php echo ASSETS_IMG; ?>/m/logo.png"></div>
            <div id="div_button">
                    
					<div style="display: block; float: left;width: 100%;margin-bottom: 10px;margin-top: 15px;">
						<a style='background: url("/assets/images/c/sprite_img.png") repeat scroll -21px -140px rgba(0, 0, 0, 0);
    display: inline-block;
    height: 62px;
    margin: 0 10px;
    width: 152px;' target="_new" href="<?php echo $client_msg["index"]["android_app_link"];?>"></a>
						<a style='background: url("/assets/images/c/sprite_img.png") repeat scroll -21px -210px rgba(0, 0, 0, 0);
    display: inline-block;
    height: 62px;
    margin: 0 10px;
    width: 152px;' target="_new" href="<?php echo $client_msg["index"]["iphone_app_link"];?>"></a>
                    </div>
            </div>
            <div>
                <?php
                if($campaignid != 0){
                if(isset($_COOKIE['mycurrent_lati_qrcode'] ) ){?>
                <a href="<?php echo $redirect_str; ?>" style="font-size:35px;">Continue To The Mobile Site ></a>
                <?php }
                else{
                    ?>
                <a onclick="get_lati_long()" style="font-size:35px;" class="not_applicable">Continue To The Mobile Site ></a>
                <?php
                }}else{ 
                   if(! isset($_COOKIE['mycurrent_lati_qrcode'] ) ){ ?>
                      <a onclick="get_lati_long()" style="font-size:35px;" class="not_applicable">Continue To The Mobile Site ></a>
                <?php }else { ?>
                <a href="<?php echo $redirect_str; ?>" style="font-size:35px;">Continue To The Mobile Site ></a>
                
                <?php } 
                }
?>
            </div>
            <div>

            </div>
        </div>
      
    </body>
</html>
<script>
    function get_lati_long()
    {
        getLocation_qrcode();
    }
jQuery(document).ready(function(){
//  alert(jQuery(".not_applicable").length );
    if(jQuery(".not_applicable").length != 0)
        {
	//	alert("inn");
         getLocation_qrcode();   
        }
   
    
});

</script>
<?php
 function getClosestTimezone($lat, $lng)
  {
    $diffs = array();
    foreach(DateTimeZone::listIdentifiers() as $timezoneID) {
      $timezone = new DateTimeZone($timezoneID);
      $location = $timezone->getLocation();
      $tLat = $location['latitude'];
      $tLng = $location['longitude'];
      $diffLat = abs($lat - $tLat);
      $diffLng = abs($lng - $tLng);
      $diff = $diffLat + $diffLng;
      $diffs[$timezoneID] = $diff;

    }

    //asort($diffs);
    $timezone = array_keys($diffs, min($diffs));


    return $timezone[0];

  }
?>
<script type="text/javascript">
							


							var isMobile = {
							Android: function() {
								return navigator.userAgent.match(/Android/i);
							},
							BlackBerry: function() {
								return navigator.userAgent.match(/BlackBerry/i);
							},
							iOS: function() {
								return navigator.userAgent.match(/iPhone|iPad|iPod/i);
							},
							Opera: function() {
								return navigator.userAgent.match(/Opera Mini/i);
							},
							Windows: function() {
								return navigator.userAgent.match(/IEMobile/i);
							},
							any: function() {
								return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
							}
						};

						

					//if( isMobile.iOS() ) 
					//{
							
						//alert('in iOS');
						//if('<?= $q ?>' != "")
						//{
						//alert('in iOS1');
						$(".cantent").css("display", "none");
						$(document).ready(function(){
							//alert('in iOS 2');
							//$('.item .delete').click(function(){
								
								var elem = $(this).closest('.item');
								
								$.confirm({
									'title'		: 'Use Scanflip Application?',
									'message'	: 'You Use our Application. <br />Experience! Continue?',
									'buttons'	: {
										'Open with App'	: {
											'class'	: 'blue',
											'action': function(){
											 setTimeout(function () { window.location = '<?php echo $client_msg["index"]["iphone_app_link"];?>'; }, 25);
											
											window.location = 'scanflip:/<?php echo $_SERVER["REQUEST_URI"]; ?>';
												
											}
										},
										'Open in Browser'	: {
											'class'	: 'gray',
											'action': function(){
												//var newLocation = "<?php echo $redirect_RS->fields['permalink']; ?>";
												//window.location = newLocation;
											  //header("Location:".$redirect_RS->fields['permalink']);
											}	// Nothing to do in this case. You can as well omit the action property.
										},
										'Cancel'	: {
											'class'	: 'gray',
											'action': function(){
												//var newLocation = "<?php echo $redirect_RS->fields['permalink']; ?>";
												//window.location = newLocation;
											}	// Nothing to do in this case. You can as well omit the action property.
										}
									}
								});
								
							//});
							
						});


						(function($){
							
							$.confirm = function(params){
								
								if($('#confirmOverlay').length){
									// A confirm is already shown on the page:
									return false;
								}
								
								var buttonHTML = '';
								$.each(params.buttons,function(name,obj){
									
									// Generating the markup for the buttons:
									
									buttonHTML += '<a href="#" class="button '+obj['class']+'">'+name+'<span></span></a>';
									
									if(!obj.action){
										obj.action = function(){};
									}
								});
								
								var markup = [
									'<div id="confirmOverlay">',
									'<div id="confirmBox">',
												
									'<div id="confirmButtons">',
									buttonHTML,
									'</div></div></div>'
								].join('');
								
								$(markup).hide().appendTo('body').fadeIn();
								
								var buttons = $('#confirmBox .button'),
									i = 0;

								$.each(params.buttons,function(name,obj){
									buttons.eq(i++).click(function(){
										
										// Calling the action attribute when a
										// click occurs, and hiding the confirm.
										
										obj.action();
										$.confirm.hide();
										return false;
									});
								});
							}

							$.confirm.hide = function(){
								$('#confirmOverlay').fadeOut(function(){
									$(this).remove();
								});
							}
							
						})(jQuery);

						
						//}	
							
				//}
</script>						
