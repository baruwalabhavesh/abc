<?php
/******** 
@USE : display rating trend chart
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymerchants.php, my-deals.php, location_detail.php, search-deal.php, locationchart.php
*********/
//echo "1";
//exit();
//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$rating = 5;
$month = date('M');
    $year = date('Y');
    $no_of_mnths = date('m',strtotime("-2 months"));
    //echo "==".$no_of_mnths;
    $remaining_months = (12 - $no_of_mnths)."\r\n";
    $tot = $remaining_months+1; 
    for($j=0;$j<$tot;$j++){
      //      echo  date('M',strtotime(''.$no_of_mnths.' months'))      ;  
            $no_of_mnths++;         
    }
//$objDB = new DB();

$current = date('m');
$m1 = 8;
$m2 = 9;
$m3 = 10;
$month_array1= array();
//DATEDIFF(NOW(),reviewed_datetime) <=90  and

/* $sql = "select avg(rating) rating , DATE_FORMAT(reviewed_datetime,'%y-%m-%d') review_date from review_rating where location_id = ".$_REQUEST['locid']." and  DATEDIFF(NOW(),reviewed_datetime) <=90 group by  DATE_FORMAT(reviewed_datetime,'%y-%m-%d')";
$a = $objDB->Conn->Execute($sql); */
$a = $objDB->Conn->Execute("select avg(rating) rating , DATE_FORMAT(reviewed_datetime,'%y-%m-%d') review_date from review_rating where location_id =? and  DATEDIFF(NOW(),reviewed_datetime) <=90 group by  DATE_FORMAT(reviewed_datetime,'%y-%m-%d')",array($_REQUEST['locid']));

while ($newcust_row = $a->FetchRow()) 
{      
  $gave_ratings[$newcust_row['review_date']] = $newcust_row['rating'];
}
 
 // exit();
 $rating_array =  array();
 for($i=90;$i>=1;$i--)
 {
	$NewDate=Date('y-m-d', strtotime("-".$i." days"));
	if(count($rating_array) != 0)
    {
		
		if(isset($rating_array[$NewDate]))
		{
		}
		else
		{
			$rating_array[$NewDate]="";
		}
		if(isset($gave_ratings[$NewDate]) &&  isset($rating_array[$NewDate]))
		{
			$rating_array[$NewDate] = $gave_ratings[$NewDate];
		}
		
		if(!key_exists($NewDate,$gave_ratings))
		{
			$prevopus_date = Date('y-m-d', strtotime("-".($i+1)." days"));
			$rating_array[$NewDate] = $rating_array[$prevopus_date];
		}
		
    }
    else
    {	
		if(isset($gave_ratings))
		{	
			if(key_exists($NewDate,$gave_ratings))
			{
				$rating_array[$NewDate] = $gave_ratings[$NewDate];
				$start_date = $NewDate;
			}
		 }
		 else
		 {
			$start_date = $NewDate;
		}
    } 
 }
 $one =0;
 $two =0;
 $three =0;
 $four =0;
 $five =0;
 $key_value_pair = array();
 /* $sql = "select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  where  location_id = ".$_REQUEST['locid']." group by re.rating";
 //echo $sql;
 $RS = $objDB->Conn->Execute($sql); */
 $RS = $objDB->Conn->Execute("select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  where  location_id =? group by re.rating",array($_REQUEST['locid']));
 $avarage_rating = array();
 
 $total_ratings = 0;
  while ($rating_row = $RS->FetchRow()) {
	$total_ratings =  $total_ratings + $rating_row['avarage_rating_counter'];
      if($rating_row['avarage_rating'] <=1)
	  {
		$one =  $one + $rating_row['avarage_rating_counter'];
                 $key_value_pair ['Poor'] = $one ;
	  }
	  else if($rating_row['avarage_rating'] >1 && $rating_row['avarage_rating'] <= 2)
	  {
		$two =  $two + $rating_row['avarage_rating_counter'];
                $key_value_pair ['Fair'] = $two ;
	  }
	  else if($rating_row['avarage_rating'] >2 && $rating_row['avarage_rating'] <= 3)
	  {
		$three =  $three + $rating_row['avarage_rating_counter'];
                $key_value_pair ['Good'] = $three  ;
	  }
	  else if($rating_row['avarage_rating'] >3 && $rating_row['avarage_rating'] <= 4)
	  {
		$four =  $four + $rating_row['avarage_rating_counter'];
                $key_value_pair ['Very Good'] = $four  ;
	  }
	  else if($rating_row['avarage_rating'] >4 && $rating_row['avarage_rating'] <= 5)
	  {
		$five =  $five + $rating_row['avarage_rating_counter'];
                $key_value_pair ['Excellent'] = $five ;
	  }
  
  }
  

 $one_percentage = round(($one*100)/$total_ratings,2);
 $two_percentage = round(($two*100)/$total_ratings,2);
 $three_percentage = round(($three*100)/$total_ratings,2);
 $four_percentage = round(($four*100)/$total_ratings,2);
 $five_percentage = round(($five*100)/$total_ratings,2);
 $rating_values  = array();
  array_push($rating_values,$one);
 array_push($rating_values,$two);
 array_push($rating_values,$three);
  array_push($rating_values,$four);
  array_push($rating_values,$five);
 
$rating_visitor_arr  = array();
 array_push($rating_visitor_arr , $one_percentage);
 array_push($rating_visitor_arr , $two_percentage);
 array_push($rating_visitor_arr , $three_percentage);
 array_push($rating_visitor_arr , $four_percentage);
 array_push($rating_visitor_arr , $five_percentage);
 
 $json_array = array();
 $dat_arr = explode("-",$start_date);
 $year = "20".$dat_arr[0];
 $month = $dat_arr[1] -1 ;
 $rating_array =  array_values($rating_array);
 /*echo "<pre>";
 print_r($key_value_pair);
 echo "</pre>"; */
 //foreach()
  $json_array['rating_info']= implode(",",$rating_array );
           $json_array['status']='true';
           $json_array['start_year']=$year;
           $json_array['start_month']=$month;
		   $json_array['visitor_detail'] = $rating_visitor_arr ;// implode(",",$rating_visitor_arr);
                   $maxs = array_search(max($key_value_pair),$key_value_pair); //array_keys($key_value_pair, max($key_value_pair));
                    $json_array['max_rating_heading'] =$maxs ;
                    $json_array['max_rating'] =round(max($rating_visitor_arr));
		   $json_array['rating_values'] = $rating_values;
	$json = json_encode($json_array);
echo $json;
exit();
?>


