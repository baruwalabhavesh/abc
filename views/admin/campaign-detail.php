<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

if(isset($_REQUEST['id'])){
    $c_array = array();
    $c_array['id']= $_REQUEST['id'];
    $RS = $objDB->Show("campaigns", $c_array);
    
    //$array_where = array();
    //$array_where['id'] = $RS->fields['created_by'];
    //$RS_user = $objDB->Show("merchant_user", $array_where);
    //
    $sql = "SELECT * FROM customer_user c, customer_campaigns cc WHERE cc.campaign_id = ".$_REQUEST['id']." AND cc.activation_status= 1 AND c.id = cc.customer_id and c.active=1";
    $RS_campaigns_subscribe =  $objDB->execute_query($sql);
   
    //
    $Sql = "SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM campaign_location cl WHERE cl.campaign_id = ".$_REQUEST['id']." ) ";
    $RS_locations =  $objDB->execute_query($Sql);
}
$sql = "SELECT RU.*,  CU.firstname, CU.lastname, C.title
		FROM reward_user RU, campaigns C, customer_user CU
		WHERE RU.campaign_id=C.id AND RU.campaign_id=".$_REQUEST['id']." AND CU.id=RU.customer_id ";
$rewards =  $objDB->execute_query($sql);
//print_r($rewards);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">

<style>
#content div.mer_chant {
    border: 1px dashed;
    display: block;
    overflow: hidden;
    padding: 5px;
}
#content div.mer_chant div.mer_chant_div {
    background: none repeat scroll 0 0 #F5F5F5;
    border-bottom: 2px solid #FF9900;
    font-size: 18px;
}
#content div.mer_chant div.mer_chant_div p span {
    font-weight: bold;
}
#content p {
    font-family: Arial,Helvetica,sans-serif;
    font-size: 0.8em;
    padding: 0;
}
</style>
</head>

<body>
     <div id="container">

              <!---start header---->
	
		<?
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		<div id="contentContainer">

	
	<div  id="sidebarLeft">
		<?
		require_once(ADMIN_VIEW."/quick-links.php");
		?>
		<!--end of sidebar Left--></div>

		<div id="content">
		    <div style="float: left;">
<h2>Campaign Summary</h2>
</div><div style="float: right; clear: none">
                            <a href="<?=WEB_PATH?>/admin/campaigns.php">Back to list</a></div>
		    <div style="clear: both"></div>
	   <div class="mer_chant">
                  <div class="mer_chant_div">
		     <table width="100%">
			<tr>
			    <td colspan="2">
				<p><span>Campaign Title</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['title']?></p>
			    </td>
			
			</tr>
			<tr>
			    <td colspan="2">
				<p><span>Campaign Description</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['description']?></p>
			    </td>
			
			</tr>
			<?php
			    $array = array();
			    $array['campaign_id'] = $_REQUEST['id'];
			    $RSCode = $objDB->Show("activation_codes",$array);
			?>
			<tr>
			    <td colspan="2">
				<p><span>Activation Code</span><span>&nbsp;:&nbsp;</span><?=$RSCode->fields['activation_code']?></p>
			    </td>
			
			</tr>
                           <?php
                  $array_where = array();
                  $array_where['id'] = $RS->fields['created_by'];
    $RS_user = $objDB->Show("merchant_user", $array_where); ?>
			
			<tr>
			    <td width="50%">
				 <p><span>Merchant</span><span>&nbsp;:&nbsp;</span><?=$RS_user->fields['firstname']." ".$RS_user->fields['lastname']?></p>
			    </td>
			    <td>
				<p><span>Created Date</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['created_date']?></p>
			    </td>
			</tr>
			
                          <?php $array_where['id'] = $RS->fields['modified_by'];
    $RS_user = $objDB->Show("merchant_user", $array_where); ?>
			<tr>
			    <td width="50%">
				 <p><span>Modified By</span><span>&nbsp;:&nbsp;</span><?=$RS_user->fields['firstname']." ".$RS_user->fields['lastname']?></p>
			    </td>
			    <td>
				<p><span>Modified Date</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['modified_date']?></p>
			    </td>
			</tr>
			                         
                          <?php
                  $array_where = array();
                  $array_where['id'] = $RS->fields['category_id'];
    $RS_user = $objDB->Show("categories", $array_where); ?>
			    <tr>
			    <td colspan="2">
				<p><span>Category</span><span>&nbsp;:&nbsp;</span><?=$RS_user->fields['cat_name']?></p>
			    </td>
			
			</tr>
			<tr>
			    <td colspan="2">
				<p><span>Limit</span><span>&nbsp;:&nbsp;</span><?php if($RS->fields['number_of_use'] == 1) { echo "One Per Customer";}else if($RS->fields['number_of_use'] == 2){echo "One Per Customer Per Day";}else if($RS->fields['number_of_use'] == 3){ echo "Multiple Use ";}?></p>
			    </td>
			
			</tr>
 
			<tr>
			    <td width="50%">
				 <p><span>Start Date</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['start_date']?></p>
			    </td>
			    <td>
				 <p><span>Expiration date</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['expiration_date']?></p>
			    </td>
			</tr>
			<tr>
			    <td width="50%">
				 <p><span>Redeem Points</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['redeem_rewards']?></p>
			    </td>
			    <td>
				 <p><span>Referral Points</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['referral_rewards']?></p>
			    </td>
			</tr>
			
			<tr>
			    <td width="50%">
				 <p><span>Campaign Visibility:</span><span>&nbsp;:&nbsp;</span><?php if($RS->fields['level'] == 1) echo "Public"; else echo "Only Members"; ?></p>
			    </td>
			 </tr>
			<tr>
			   <!-- <td width="50%">
				 <p><span>Max coupans</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['max_coupns']?></p>
			    </td>-->
			    <td>
				 <p><span>Campaign value</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['deal_value']?></p>
			    </td>
			</tr>
			<tr>
			    <td colspan="2">
				<p><span>Campaign Image</span><span>&nbsp;:&nbsp;</span><?//=$RS->fields['business_logo']?></p>
			    </td>
			</tr>
			<tr>
			    <td colspan="2">
				 <p><img src="<?=ASSETS_IMG ?>/m/campaign/<?=$RS->fields['business_logo']?>" style="max-width: 200px;"></p>
			    </td>
			</tr> 
		     </table>
              
                  </div>
		  
		    
                     <div class="mer_chant_div">
                         <p><span>Avilable At Locations</span><span>&nbsp;:&nbsp;</span><?=$RS_locations->RecordCount()?></p>
                         <?php if($RS_locations->RecordCount() != 0){?>
			 <table>
			 <tr style="font-size: 0.8em;">
			   	<th width="30%" align="left">Business Name</th>
				<th width="40%" align="left">Location Address</th>
				<!--<th width="20%" align="left">Phone</th>-->
				<!--<th width="11%" align="left">Website</th>-->
				<th width="20%" align="left">Campaign Status</th>
				<!--<th width="14%" align="left">Merchant</th>-->
			  </tr>
		<?php	 while($Row = $RS_locations->FetchRow()){  $where_clause = array();
				$where_clause['id'] = $Row['created_by'];
				$RSMerchant = $objDB->Show("merchant_user", $where_clause); ?>
		       <tr style="font-size: 0.8em;">
			    <td><a href="location-detail.php?id=<?=$Row['id']?>"><?=$Row['location_name']?></a></td>
			    <td><?=$Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip']?></td>
			 <!--   <td><?=$Row['phone_number']?></td> -->
			  <!--  <td><?=$Row['website']?></td> -->
			    <td>
			    <?
			    if($Row['active'] == 1) echo "Active"; else echo "Inactive";
			    ?>
			    </td>
			  <!--  <td><?=$RSMerchant->fields['firstname']." ".$RSMerchant->fields['lastname']?></td> -->
		      </tr>
                         
                         <?php } echo "</table>";
			 }?>
                    </div>
           </div>
	
                     <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>

<?php
//$address = $RS->fields['address'].", ".$RS->fields['city'].", ".$RS->fields['state'].", ".$RS->fields['zip'].", ".$RS->fields['country'];
//$image = '<img height="60" src="'.WEB_PATH.'/merchant/images/location/'.$RS->fields['picture'].'" border="0" />';
//	$MARKER_HTML = '<div style="clear:both; width:225px;font:Arial, Helvetica, sans-serif;">';
//		$MARKER_HTML .= "<b>".$RS->fields['location_name']."</b><br />";
//		$MARKER_HTML .= $image."<br />";
//		$MARKER_HTML .= $address."<br />";
//		$MARKER_HTML .= '</div>';
?>
</body>

<!--<script type='text/javascript' src="http://maps.google.com/maps/api/js?sensor=true&.js"></script>
<script type='text/javascript' src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>-->


 <script type="text/javascript">
	
//    	 var map;
//	
//
//	 google.maps.event.addDomListener(window, 'load', initialize);
//	 
//		var lat = <?=$RS->fields['latitude']?>;
//		var lng = <?=$RS->fields['longitude']?>;
//		var contentString = '<?=$MARKER_HTML?>';
//		//alert(lat);
//		//alert(lng);
//		
//		  function initialize() {
//				   map = new google.maps.Map(document.getElementById('map_canvas'), {
//					  zoom: 17,
//					  center: new google.maps.LatLng(lat,lng),
//					  mapTypeId: google.maps.MapTypeId.ROADMAP
//					});
//				   createMarker(lat,lng,contentString);
//		  }
//			
//				 function handleNoGeolocation(errorFlag) {
//					contentString = "Error: The Geolocation service failed.";
//					setUserLocationPoint(contentString, true);
//				}
//				function setUserLocationPoint(infoWinsowContentObj, defaultOpen) {
//					if (marker) {
//						marker.setMap(null);
//					}
//					map.setCenter(someLocation);
//				}
//		
//			var infowindow = new google.maps.InfoWindow({
//					size: new google.maps.Size(150, 50)
//				});
//				 function createMarker(lat,lan,mycontent) {
//				 	//alert(contentString);
//					var content = mycontent;
//						var marker = new google.maps.Marker({
//								  position: new google.maps.LatLng(lat,lan),
//								  map: map,
//								  zIndex: Math.round(lat * -100000) << 5,
//								  icon: new google.maps.MarkerImage('http://www.scanflip.com/images/pin-small.png')
//								});
//					
//					google.maps.event.addListener(marker, 'click', function() {
//					
//						infowindow.setContent(contentString);
//						infowindow.open(map,marker);
//					     });
//					infowindow.setContent(contentString);
//						infowindow.open(map,marker);
//					//infowindow.setContent(contentString);
//					//infowindow.open(map,marker);
//						//infowindow.open(map, marker);
//						//markerArray.push(marker); 
//				 }

		
			

</script>
</html>
