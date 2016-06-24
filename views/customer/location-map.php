<?php
/******** 
@USE : display location map
@PARAMETER : 
@RETURN : 
@USED IN PAGES : campaign page
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

$where_clause = array();
$where_clause['id'] = $_REQUEST['location_id'];
$RSComp = $objDB->Show("locations", $where_clause);

define("MAP_WIDTH","530px");
define("MAP_HEIGHT","300px");

include_once(LIBRARY."/GoogleMap.php");
include_once(LIBRARY."/JSMin.php");

$MAP_OBJECT = new GoogleMapAPI(); $MAP_OBJECT->_minify_js = isset($_REQUEST["min"])?FALSE:TRUE;

?>
<?php 

$busines_name="";
	$array_where['id'] = $_REQUEST['location_id'];
	$RSlocation = $objDB->Show("locations", $array_where);
        
        if($RSlocation->RecordCount()<=0){
            
        }
        else 
        {
            if($RSlocation->fields['location_name']!="")
            {
                $busines_name=$RSlocation->fields['location_name'];
            }
            else 
            {
                $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$_REQUEST['location_id']);
                        if(trim($arr[0]) == "")
                             {
                                     unset($arr[0]);
                                     $arr = array_values($arr);
                             }
                             $json = json_decode($arr[0]);
                        $busines_name  = $json->bus_name;
       
		//$businessname = $busines_name ;
            }
                
        }


       
       
		 ?>
<html>
<head>


<script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true&.js"></script>
<script type='text/javascript' src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>
<script type='text/javascript'>
	var map = null;
				var markerArray = []; //create a global array to store markers
				var locations = [
				<?
				$RSComp->MoveFirst();
						$total = $RSComp->RecordCount();
						
						$count = 1;
						$lat = $lon = "";
					if($RSComp->RecordCount()>0){
					while($Row = $RSComp->FetchRow()){
						$where_clause = array();
						$where_clause['id'] = $Row['id'];
						$RSLocation = $objDB->Show("locations", $where_clause);
						
						$lat = $RSLocation->fields['latitude'];
						$lon = $RSLocation->fields['longitude'];
						$address = $RSLocation->fields['address'].", ".$RSLocation->fields['city'].", ".$RSLocation->fields['state'].", ".$RSLocation->fields['zip'].", ".$RSLocation->fields['country'];
						//echo $address."<hr>";
						$image = '<img height="60" src="'.WEB_PATH.'/merchant/images/location/'.$RSLocation->fields['picture'].'" border="0" />';
						$MARKER_HTML = "<div style='clear:both;width:auto;font:Arial, Helvetica, sans-serif;'>";
						$MARKER_HTML .= "<div><b>".$busines_name."</b></div>";
						//$MARKER_HTML .= $image."<br />";
						$MARKER_HTML .= "<div>".$address."</div><br />";
						$MARKER_HTML .= "</div>";
				?>
				
				["<?=$MARKER_HTML?>", <?=$lat?>, <?=$lon?>]
				<?
				if($count != $total) echo ",";
							$count++;
						}
					}
				?>
];
				
</script>
<script>

		
		  function initialize() {
				   map = new google.maps.Map(document.getElementById('map_canvas'), {
					  zoom:  14,
					  center: new google.maps.LatLng(<?=$lat?>, <?=$lon?>),
					  mapTypeId: google.maps.MapTypeId.ROADMAP
					});
				
				   for (var i = 0; i < locations.length; i++) {
					
						createMarker(locations[i][1], locations[i][2], locations[i][0]);
					}
		  }
			
				 function handleNoGeolocation(errorFlag) {
					contentString = "Error: The Geolocation service failed.";
					setUserLocationPoint(contentString, true);
				}
				function setUserLocationPoint(infoWinsowContentObj, defaultOpen) {
					if (marker) {
						marker.setMap(null);
					}
					map.setCenter(someLocation);
				}
		
			var infowindow = new google.maps.InfoWindow({
					size: new google.maps.Size(150, 50)
				});
				 function createMarker(lat,lan,mycontent) {
				 	var content = mycontent;
						var marker = new google.maps.Marker({
								  position: new google.maps.LatLng(lat,lan),
								  map: map,
								  zIndex: Math.round(lat * -100000) << 5,
								  icon: new google.maps.MarkerImage('<?=ASSETS_IMG?>/c/pin-small.png')
								});
					
					google.maps.event.addListener(marker, 'click', function() {
					
						//infowindow.setContent(content);
						//infowindow.open(map,marker);
					     });
					//infowindow.setContent(content);
					//	infowindow.open(map,marker);
					//infowindow.setContent(contentString);
					//infowindow.open(map,marker);
						//infowindow.open(map, marker);
						//markerArray.push(marker); 
				 }
window.onload = initialize;
			

</script>
</head>
<body>

<div id="map_canvas" style="width: 100%; height: 300px; position:relative;">
	
</div>
</body>
</html>
