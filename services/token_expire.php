<?php
//require_once("/var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();


$array_where_as = array();
$array_where_as['id'] = 6;
$RS = $objDB->Show("admin_settings", $array_where_as);
if($RS->RecordCount()>0)
{
	//echo $RS->fields['action'];

	if($RS->fields['action']==0)
	{	
		$array_values_as = $array_where_as = array();
		$array_values_as['action'] = 1;
		$array_where_as['id'] = 6;
		$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
$array_where=array();
//$array_where['emailaddress'] = "baruwalabhavesh@yahoo.in";
$RS = $objDB->Show("customer_user",$array_where);
      


/*
// year moth days hour minute second difference	between two dates  
$arr=otherDiffDate('2013-09-12 01:02:17',true);
print_r($arr);
echo $arr['Days'];
echo "</br>";
*/

/*
// hours difference between two dates
$date2=date('Y-m-d H:i:s');
$date1='2013-09-12 01:02:17';
$seconds = strtotime($date2) - strtotime($date1);
$hours = $seconds / 60 /  60;
echo $hours." hours";
*/
	  
while($Row = $RS->FetchRow())
{
	$id=$Row["id"];
    $email=$Row['emailaddress'];
	$token=$Row['token'];
	$token_created_at=$Row['token_created_at'];
	if($token!="" && $token_created_at!="0000-00-00 00:00:00")
	{
		//echo $id."   -   ".$email."   -    ".$token."    -    ".$token_created_at;
		//echo "</br>";
		$date2=date('Y-m-d H:i:s');
		$date1=$token_created_at;
		$seconds = strtotime($date2) - strtotime($date1);
		$hours = $seconds / 60 /  60;
		//echo $hours." hours";
		if($hours>24)
		{
			//echo "token expire";
			
			$array_where1=array();
			$array_values1=array();
			$array_values1['token'] = '';
			$array_values1['token_created_at']="0000-00-00 00:00:00";
            $array_where1['emailaddress'] = $email;
            $objDB->Update($array_values1,"customer_user", $array_where1);
			
		}
		else
		{
			//echo "token active";
		}
		//echo "</br>";
	}
}


// start for merchant user

$array_where=array();
$RS = $objDB->Show("merchant_user",$array_where);

while($Row = $RS->FetchRow())
{
	$id=$Row["id"];
    $email=$Row['email'];
	$token=$Row['token'];
	$token_created_at=$Row['token_created_at'];
	if($token!="" && $token_created_at!="0000-00-00 00:00:00")
	{
		//echo $id."   -   ".$email."   -    ".$token."    -    ".$token_created_at;
		//echo "</br>";
		$date2=date('Y-m-d H:i:s');
		$date1=$token_created_at;
		$seconds = strtotime($date2) - strtotime($date1);
		$hours = $seconds / 60 /  60;
		//echo $hours." hours";
		if($hours>24)
		{
			//echo "token expire";
			
			$array_where1=array();
			$array_values1=array();
			$array_values1['token'] = '';
			$array_values1['token_created_at']="0000-00-00 00:00:00";
            $array_where1['email'] = $email;
            $objDB->Update($array_values1,"merchant_user", $array_where1);
			
		}
		else
		{
			//echo "token active";
		}
		//echo "</br>";
	}
}

	}	
	
	$array_values_as = $array_where_as = array();
	$array_values_as['action'] = 0;
	$array_where_as['id'] = 6;
	$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
}
function otherDiffDate($end, $out_in_array=false)
{
        $intervalo = date_diff(date_create(), date_create($end));
        $out = $intervalo->format("Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s");
        if(!$out_in_array)
            return $out;
        $a_out = array();
        array_walk(explode(',',$out),
        function($val,$key) use(&$a_out){
            $v=explode(':',$val);
            $a_out[$v[0]] = $v[1];
        });
        return $a_out;
}
?>
