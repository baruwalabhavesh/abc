<?php
/******** 
@USE : get addresses to search deal
@PARAMETER : 
@RETURN : 
@USED IN PAGES : zipcodediv.php
*********/
//require_once("classes/Config.Inc.php");

//include_once(SERVER_PATH."/classes/DB.php");

$ip = $_SERVER["REMOTE_ADDR"];
//$location_detail=geoCheckIP($ip);
$location_detail=ip_info($ip);
//print_r($location_detail);
//$country_name= $location_detail['country'];
//$country_name= $location_detail;
$country_name= $location_detail['country_code'];


//$objDB = new DB();
$array_where = array();
$coupon_code=$_REQUEST['couponname'];
if($coupon_code!="last3")
{
	$mystring = $_SERVER['HTTP_USER_AGENT'];
	$findme   = 'Opera';
	$pos=strpos($mystring, $findme);
	if ($pos === false) 
	{
		//if($country_name == "CA - Canada")
		if($country_name == "CA")
		{
			$Sql = 'SELECT * from search_city where City like "'.$coupon_code.'%" AND Country="Canada"' ;
			$RS = $objDB->Conn->Execute($Sql);
			//$RS = $objDB->Conn->Execute("SELECT * from search_city where City like '?%' AND Country='Canada'",array($coupon_code));
		}
		//elseif($country_name == "US - United States")
		elseif($country_name == "US")
		{
			$Sql = 'SELECT * from search_city where City like "'.$coupon_code.'%" AND Country="USA"' ;
			$RS = $objDB->Conn->Execute($Sql);
			//$RS = $objDB->Conn->Execute("SELECT * from search_city where City like '?%' AND Country='USA'",array($coupon_code));
		}
		else
		{
			$Sql = 'SELECT * from search_city where City like "'.$coupon_code.'%"' ;
			$RS = $objDB->Conn->Execute($Sql);
			//$RS = $objDB->Conn->Execute("SELECT * from search_city where City like '?%'",array($coupon_code));
		}
	} 
	else
	{
		$Sql = 'SELECT * from search_city where City like "'.$coupon_code.'%"' ;
		$RS = $objDB->Conn->Execute($Sql);
		//$RS = $objDB->Conn->Execute("SELECT * from search_city where City like '?%'",array($coupon_code));
	}
	
	//echo $Sql ;
	//exit();
	
	
	$RS->MoveFirst();
	/*
	$couponcodes="";
	while($Row = $RS->FetchRow())
	{
		$couponcodes .= $Row['coupon_code'].",";
	}
	$couponcodes=substr($couponcodes,0,strlen($couponcodes)-1);
	echo $couponcodes;
	*/
	echo $RS->RecordCount();
	echo "###";
	$html = '<div style="" class="">';

	while($Row = $RS->FetchRow())
	{
		$html .='<div style="">';
		$html .='<div onclick="repalcevalue(this);" style="" class="autocomplete">'. $Row['City'].",".$Row['StateCode'].",".$Row['Country'].'</div>';
		$html .='</div>';
	}

	$html .= '</div>';
	echo $html;
}
else
{
	if(isset($_COOKIE["recently_searched"]))
	{
		$ar_se=$_COOKIE["recently_searched"];
		$ar_se = json_decode($ar_se);
		echo count($ar_se);
		echo "###";
		$c=0;
		$html = '<div style="">';
		for($i=count($ar_se)-1;$i>=0;$i--)
		{
			$c++;
			if($c<=4)
			{
				//echo $ar_se[$i];
				$html .='<div style="">';
				$html .='<div onclick="repalcevalue(this);" style="" class="autocomplete">'. $ar_se[$i].'</div>';
				$html .='</div>';
			}
			
		}
		$html .='</div>';
		echo $html;
	}
}

function geoCheckIP($ip)
{
	   //check, if the provided ip is valid
	   if(!filter_var($ip, FILTER_VALIDATE_IP))
	   {
			   throw new InvalidArgumentException("IP is not valid");
	   }

	   //contact ip-server
	   
	   //$response=@file_get_contents('http://www.netip.de/search?query='.$ip);
	   $response=@file_get_contents('http://api.hostip.info/country.php?ip='.$ip);
	   
	   if (empty($response))
	   {
			   throw new InvalidArgumentException("Error contacting Geo-IP-Server");
	   }

	   /*
	   //Array containing all regex-patterns necessary to extract ip-geoinfo from page
	   $patterns=array();
	   $patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
	   $patterns["country"] = '#Country: (.*?)&nbsp;#i';
	   $patterns["state"] = '#State/Region: (.*?)<br#i';
	   $patterns["town"] = '#City: (.*?)<br#i';

	   //Array where results will be stored
	   $ipInfo=array();

	   //check response from ipserver for above patterns
	   foreach ($patterns as $key => $pattern)
	   {
			   //store the result in array
			   $ipInfo[$key] = preg_match($pattern,$response,$value) && !empty($value[1]) ? $value[1] : 'not found';
	   }

	   return $ipInfo;
	   */
	   return $response;
}
       
function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) 
{
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}       
?>
