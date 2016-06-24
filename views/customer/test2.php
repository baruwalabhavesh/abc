<?php
//require_once("classes/Config.Inc.php");
//echo phpinfo();
$data = file_get_contents(WEB_PATH."/cached_json/mydata.json");
$obj = json_decode($data);
$fields_detail = $obj->records;

$start=$_REQUEST['start'];
$total=$_REQUEST['total'];

$cnt=0;
$total_var = 1;

$json_array = array();

foreach ($fields_detail as $record) 
{
	 if($cnt >= $start && $total_var <= $total)
	 {
		  //echo "cnt = ".$cnt." ".$record->location_id." - ".$record->location_name."<br/>";
		  $json_array['records'][$total_var] = $record;
		  $total_var++;
	 }
	 $cnt++;
}

$json = json_encode($json_array);
echo $json;
exit;

?>
