<?php
//require_once("../classes/Config.Inc.php");
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
include_once(SERVER_PATH."/admin/phpqrcode/qrlib.php");   
//$objDB = new DB();
//$objJSON = new JSON();
if(isset($_REQUEST['reset_timezonedst']))
{
try{
	$sql = "select * from locations limit ".$_REQUEST['startlimit'].",".$_REQUEST['endlimit'];
	echo $sql;
	$RS = $objDB->Conn->Execute($sql);  
	while($Row = $RS->FetchRow()){
	
    $geocode= file_get_contents("http://api.timezonedb.com/?lat=".$Row['latitude']."&lng=".$Row['longitude']."&key=V5R3I13VWW28&format=json");
	$geojson= json_decode($geocode,true);	
	print_r($geojson);
		if($geojson['dst'] == 1)
		{
			$timezone_name = $geojson['zoneName'];
			$zonehouroffset = timezone_offsethour_string($geojson['gmtOffset']).",0";
			//echo $Row['id']."===old timezone-".$Row['timezone_name']."-".$Row['timezone']."===".$Row['latitude']."===".$Row['longitude']."==".timezone_offsethour_string($geojson['gmtOffset'])."=new time zone".$geojson['zoneName']."<br/>";
		}
		else
		{
			$timezone_name = $geojson['zoneName'];
			$zonehouroffset = getStandardOffsetUTC($geojson['zoneName'],$geojson['gmtOffset']).",0";
			//echo $Row['id']."===old timezone-".$Row['timezone_name']."-".$Row['timezone']."===".$Row['latitude']."===".$Row['longitude']."==".getStandardOffsetUTC($geojson['zoneName'],$geojson['gmtOffset'])."=new time zone".$geojson['zoneName']."<br/>";
		} 
		$sql = "update locations set timezone_name='".$timezone_name."' , timezone='".$zonehouroffset."' where id=".$Row['id'];
		$objDB->Conn->Execute($sql);
		
	}
	}catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	echo $_REQUEST['endlimit']." records successfully updated";
}
// get UTC/GMT timezone from offset

function timezone_offsethour_string( $offset )
{
       return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
}
function getStandardOffsetUTC($timezone,$offset)
{
if($timezone == 'UTC') {
	return '';
} else {
	$timezone = new DateTimeZone($timezone);
	$transitions = array_slice($timezone->getTransitions(), -3, null, true);

	foreach (array_reverse($transitions, true) as $transition)
	{
		if ($transition['isdst'] == 1)
		{
			continue;
		}

		return sprintf('%+03d:%02u', $offset / 3600, abs($offset) % 3600 / 60);
	}

	return false;

}
} 
?>