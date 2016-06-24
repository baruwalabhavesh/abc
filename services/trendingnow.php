<?php
//error_reporting(E_ALL);
//require_once("/var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

$array_where_as = array();
$array_where_as['id'] = 10;
$RS = $objDB->Show("admin_settings", $array_where_as);
if($RS->RecordCount()>0)
{
	//echo $RS->fields['action'];

	if($RS->fields['action']==0)
	{	
		$array_values_as = $array_where_as = array();
		$array_values_as['action'] = 1;
		$array_where_as['id'] = 10;
		$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
$where_clause = $array_values = $array_where = array();
$array = $json_array = $where_clause = array();
$dismile=50;
$RS_users = $objDB->Show("customer_user", $where_clause);
if($RS_users->RecordCount()>0)
{
	while($Row_user = $RS_users->FetchRow())
	{ 
		//$str = $Row_user['id']."-".$Row_user['curr_latitude']."-".$Row_user['curr_longitude'];
		//echo $str."<br/>";
		
		$customer_id=$Row_user['id'];
		
		$array_where = array();
		$array_where['customer_id'] = $customer_id;  
		$objDB->Remove("customer_trending_campaign", $array_where);
		
		/*
		echo "=================================================";
		echo "<br/>"."customer id=". $Row_user['id']."<br/>";	
		echo "---------------------------------------------------------------------------------------------------------------------------------------------";
		echo "<br/>";
		*/
		if($Row_user['curr_latitude']!="" && $Row_user['curr_longitude']!="")
		{		
			$sql_get_users_camp_loc="SELECT c.id campaignid,l.id locationid,
			(
(select (count(*)*0.2)  from coupon_codes where customer_campaign_code=c.id and location_id=l.id and generated_date between DATE_SUB(NOW(),INTERVAL 2 HOUR) and NOW())+
(select (count(*)*0.4)  from coupon_redeem where coupon_id in (select id from coupon_codes where customer_campaign_code=c.id and location_id=l.id) and redeem_date between DATE_SUB(NOW(),INTERVAL 2 HOUR) and NOW())+
(SELECT (count(*)*0.2)  from share_counter where campaign_id=c.id and location_id=l.id and timestamp between DATE_SUB(NOW(),INTERVAL 2 HOUR) and NOW())+
(SELECT (count(*)*0.2)  from pageview_counter where campaign_id=c.id and location_id=l.id and timestamp between DATE_SUB(NOW(),INTERVAL 2 HOUR) and NOW())
) total_weightage 
			FROM campaign_location cl 
inner join campaigns c 
on cl.campaign_id =c.id 
inner join locations l 
on l.id = cl.location_id 
WHERE l.active = 1 
and c.level=1 
and (69.1*(l.latitude-(".$Row_user['curr_latitude']."))*69.1*(l.latitude-(".$Row_user['curr_latitude'].")))+(53.0*(l.longitude-(".$Row_user['curr_longitude']."))*53.0*(l.longitude-(".$Row_user['curr_longitude'].")))<=".$dismile*$dismile."
AND CONVERT_TZ(NOW(),'-05:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date 
and cl.active=1 and cl.offers_left>0 
ORDER BY c.id";

		//echo $sql_get_users_camp_loc."<br/>";
		
			$RS_limit_data=$objDB->Conn->Execute($sql_get_users_camp_loc);
			if($RS_limit_data->RecordCount()>0)
			{
				/*
				echo "<table border=2>";
				
				echo "<tr>";
				echo "<thead>";
				echo "<td>campaign id</td><td>location id</td><td>total weightage</td><td>same</td><td>max weightage</td>";	
				echo "</thead>";
				echo "</tr>";
				
				echo "<tbody>";
				*/
				$camp_id=0;
				$unique=0;
				$max_weightage=0;
				$trending_array=array();
				while($Row = $RS_limit_data->FetchRow())
				{					
					if($camp_id==$Row['campaignid'])
					{
						$unique=0;
					}
					else
					{
						$unique=1;
						$max_weightage=0;
						$camp_id = 	$Row['campaignid'];
						$trending_array[$Row['campaignid']] = array();
					}
					
					if($Row['total_weightage'] >= $max_weightage)
					{
						$max_weightage = $Row['total_weightage'];
						$arr=array();
						$arr['campaign_id']=$Row['campaignid'];
						$arr['location_id']=$Row['locationid'];
						$arr['weightage']=$max_weightage;
						$trending_array[$Row['campaignid']]=$arr;
					}
					/*
					echo "<tr>";
					
					echo "<td>";
					echo $Row['campaignid'];
					echo "</td>";
					
					echo "<td>";
					echo $Row['locationid'];
					echo "</td>";
					
					echo "<td>";
					echo $Row['total_weightage'];
					echo "</td>";
					
					if($unique==0)
					{
						echo "<td>";
						echo "same";
						echo "</td>";
					}
					else
					{
						echo "<td>";
						echo "new";
						echo "</td>";
					}
					
					echo "<td>";
					echo $max_weightage;
					echo "</td>";
					
					echo "</tr>";		
					*/
				}
				/*
				echo "</tbody>";
				echo "</table>";
				
				
				echo "<pre>";	
				print_r($trending_array);	
				echo "</pre>";	
				*/
				sortBy('weightage', $trending_array, 'desc');
				
				$cnt=0;
				foreach ($trending_array as $key => $value) 
				{
					if($cnt<5)
					{
						//echo $key."-".$value."<br/>";
						$campid=0;
						$locid=0;
						foreach ($value as $key1 => $value1) 
						{
							//echo $key1."-".$value1."<br/>";
							if($key1=="campaign_id")
							{
								$campid=$value1;
							}
							if($key1=="location_id")
							{
								$locid=$value1;
							}
						}
						
						$array = array();
						$array['customer_id'] = $customer_id; 
						$array['campaign_id'] = $campid; 
						$array['location_id'] = $locid; 
						$objDB->Insert($array, "customer_trending_campaign");
	
						//echo $customer_id."-".$campid."-".$locid;
					}
					$cnt++;
					//echo "<br/>";
				}
				
			}
		}
		/*
		echo "---------------------------------------------------------------------------------------------------------------------------------------------";
		echo "<br/>";
		*/
	}
}


	}	
	
	$array_values_as = $array_where_as = array();
	$array_values_as['action'] = 0;
	$array_where_as['id'] = 10;
	$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
}
function sortBy($field, &$array, $direction = 'asc')
{
    usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if ($a == $b)
        {
            return 0;
        }

        return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
    '));

    return true;
}
?>
