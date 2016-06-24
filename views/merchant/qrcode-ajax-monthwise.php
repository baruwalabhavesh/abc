<?php
/**
 * @uses get qrcode data to generate report by month wise
 * @used in pages :reports.php
 * @author Sangeeta Raghavani
 */

header('Content-type: text/html; charset=utf-8');
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
//getting monthwise data


    $unique_qrcodelocation_arr = array();
    $unique_qrcodecampaign_arr = array();
    $unique_qrcodetotal_arr = array();
    $all_qrcodecampaign_arr = array();
    $all_qrcodelocation_arr = array();
    $female_counter = 0;
    $male_counter = 0;
    $unknon_counter = 0;
    $female_arr_location = array();
    $male_arr_location = array();
	  $unknown_arr_location = array();
	  $female_arr_campaign = array();
    $male_arr_campaign = array();
      $unknown_arr_campaign = array();

foreach ($months as $month) {
	//echo "1";

$merchant_id = $_SESSION['merchant_id'];
$arr=file(WEB_PATH.'/merchant/process.php?getallsubmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
if(trim($arr[0]) == "")
{
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$ids= $json->all_sub_merchant_id;
if($ids=="")
             $ids=$_SESSION['merchant_id'];
			 
			 
			 if($_SESSION['merchant_info']['merchant_parent'] == 0)
		 { 
         	$merchant_sql = " and (b.created_by = ".$merchant_id." or b.created_by in (".$ids.")) "; 
		 }
		 else
		 {
			 $merchant_array = array();
			$merchant_array['merchant_user_id'] = $_REQUEST['mer_id'];
			$merchant_info = $objDB->Show("merchant_user_role",$merchant_array);
	
			$Where1=" a.location_id=".$merchant_info->fields['location_access'];
			$merchant_sql = "";
			// $merchant_sql = "and  b.created_by = ".$merchant_id  ; 
		 }
//echo 123;
if($month != 0)
    {
        if($month==4 || $month==6 ||$month==9 ||$month==11 )
        {
            $m=30;
        }
        else if($_REQUEST['month']==2)
        {
            $m=28;
        }
        else
        {
            $m =31;
        }
    }
	
	if($_REQUEST['year'] != 0 )
	{
		$year = $_REQUEST['year'];
	}
	else{
		$year = date('Y');
	}
			   $from_date = $year."-".$month."-01 00:00:00";
     	     	$to_date =  $year."-".$month."-31 23:59:59";
	//}
    $month_expr = " and  DATE_FORMAT(a.scaned_date,'%m') =".$month." ";
//	echo $month_expr."<br/>";
	// if($_REQUEST['year'] != 0 )
                  //              {
									$dt_wh = "a.scaned_date <'".$to_date ."' ";
									$dt_wh1 = "";

								   $month_expr = " a.scaned_date between '".$from_date."' and '".$to_date."' ";
									   $sql = " select distinct a.* ,b.location_name ,b.address ,b.state,b.city,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where  c.id=a.user_id) gender
												from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id 
								   where b.id=".$_REQUEST['selected_locations']." and a.is_location<>1 and 
									 $month_expr $merchant_sql ";
                                                   //     }
							/*else
							{
                                                              
								  $sql = "select distinct a.* ,b.location_name ,b.address ,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where 
                                                                  c.id=a.user_id) gender from scan_qrcode a inner join locations b on a.location_id = b.id    inner join campaigns c on a.campaign_id = c.id 
                                                                  where a.is_location<>1 and a.campaign_id in ( select campaign_id from campaign_location cl where cl.active<>1 and cl.active_in_future<>1 
								  ) AND c.expiration_date <  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(b.timezone,1, POSITION(',' IN b.timezone)-1)) ".$merchant_sql;

								
							}  */

					// $RS_qrcode = $objDB->execute_query($sql);
 $RS_qrcode = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.address ,b.state,b.city,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where  c.id=a.user_id) gender from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id where b.id=? and a.is_location<>? and $month_expr $merchant_sql ",array($_REQUEST['selected_locations'],1));

					$location_arr = array();
					
					
	$selected_loations = explode(";",$_REQUEST['selected_locations']);
         $allfilter_locations  = array();
        
	//$RS_qrcode = $objDB->execute_query($sql);
	$mnt = $month -1;
        
	if($RS_qrcode->RecordCount() == 0)
	{
	//echo $month;
		 $male_arr_campaign[$mnt] = 0;
		 $female_arr_campaign[$mnt] = 0;
		$unique_qrcodecampaign_arr[$mnt] = 0;
		 $unique_qrcodelocation_arr[$mnt] = 0;
		  $all_qrcodelocation_arr[$mnt] = 0;
		 $unknown_arr_campaign[$mnt] = 0;
		 $all_qrcodecampaign_arr[$mnt] = 0;
		 $male_arr_location[$mnt] = 0;
		$female_arr_location[$mnt] = 0;
		 $unknown_arr_location[$mnt] = 0;
		 $unique_qrcodetotal_arr[$mnt] = 0;
	}
	else{
	while($Row_qrcode = $RS_qrcode->FetchRow())
    {
        if($Row_qrcode['gender'] ==1)
        {
            $male_counter++;
        }
        else if($Row_qrcode['gender'] ==2)
        {
            $female_counter++;
        }
        else{
            $unknon_counter++;
        }
		$mnt = $month -1;
        if(array_key_exists($Row_qrcode['location_id'],$location_arr)){
           if(in_array($Row_qrcode['location_id'] ,$selected_loations)) {
		    
            $temparr =$location_arr[$Row_qrcode['location_id']];
            
            if($Row_qrcode['is_unique']==1 && $Row_qrcode['campaign_id']!=0)
            {
                if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_campaign[$mnt]= $male_arr_campaign[$mnt] + 1;
                   
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_campaign[$mnt]= $female_arr_campaign[$mnt] + 1;
                    
                }
                else{
                    $unknown_arr_campaign[$mnt]= $unknown_arr_campaign[$mnt] + 1;
               
                }
                 $unique_qrcodecampaign_arr[$mnt]=  $unique_qrcodecampaign_arr[$mnt]+1;
             
                $all_qrcodecampaign_arr[$mnt] = $all_qrcodecampaign_arr[$mnt] +1;
            }
            else if($Row_qrcode['is_unique']==0 && $Row_qrcode['campaign_id']!=0)
            {
                if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_campaign[$mnt]= $male_arr_campaign[$mnt] + 1;
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_campaign[$mnt]= $female_arr_campaign[$mnt] + 1;
                }
                else{
                 
                    $unknown_arr_campaign[$mnt]= $unknown_arr_campaign[$mnt] + 1;
                   
                }
                 $all_qrcodecampaign_arr[$mnt] = $all_qrcodecampaign_arr[$mnt] +1;
            }
           
             if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_location[$mnt]= $male_arr_location[$mnt] + 1;
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_location[$mnt]= $female_arr_location[$mnt] + 1;
                }
                else{
                        $unknown_arr_location[$mnt]= $unknown_arr_location[$mnt] + 1;
                }
            $unique_qrcodetotal_arr[$mnt] = $unique_qrcodetotal_arr[$mnt]+1;
		//	echo $mnt."<br/>";
			//echo $unique_qrcodetotal_arr[$mnt]."===";
//          $unique_qrcodelocation_arr[$Row_qrcode['location_id']] = $uniquecounterlocation;
//          $unique_qrcodecampaign_arr[$Row_qrcode['location_id']] = $uniquecountercampaign;
//          $all_qrcodelocation_arr[$Row_qrcode['location_id']] = $allcounterlocation;
//          $all_qrcodecampaign_arr[$Row_qrcode['location_id']] = $allcountercampaign;
//          $unique_qrcodetotal_arr[$Row_qrcode['location_id']] = $totalcounter;
        }
        }
        else {
           if(in_array($Row_qrcode['location_id'] ,$selected_loations)) {
             // array_push($location_arr,$Row_qrcode['location_id']);
            if($Row_qrcode['location_name']== "")
            {
//                $sql = "select business merchant_user where id ="+$Row_qrcode['created_by'];
//                $RS_business = $objDB->execute_query($sql);
//                $locname= $RS_business->fields['business'];
                $locname= " ";
            }
            else{
                $locname= $Row_qrcode['location_name'];
            }
			$locname = $Row_qrcode['address'].",".$Row_qrcode['city'].",".$Row_qrcode['state'].",".$Row_qrcode['zip'];
            $location_arr[$Row_qrcode['location_id']] = $locname."#!#".$Row_qrcode['location_id'];
                $unique_qrcodelocation_arr[$mnt] = 0;
              $unique_qrcodecampaign_arr[$mnt] = 0;
            $unique_qrcodetotal_arr[$mnt] = 1;
            $all_qrcodelocation_arr[$mnt] = 0;
            $all_qrcodecampaign_arr[$mnt] = 0;
           
                    $male_arr_campaign[$mnt]=  0;
                     $male_arr_location[$mnt]=  0;
               
                    $female_arr_campaign[$mnt]= 0;
                    $female_arr_location[$mnt]=  0;
           
                    $unknown_arr_campaign[$mnt]=  0;
                    $unknown_arr_location[$mnt]=  0;
                 
      
                     if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_location[$mnt]=1;
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_location[$mnt]= 1;
                }
                else{
                   
                    $unknown_arr_location[$mnt]= 1;
                }
            if($Row_qrcode['is_unique']==1 && $Row_qrcode['campaign_id']!=0)
            {
                  $unique_qrcodecampaign_arr[$mnt]=1;
                  $unique_qrcodelocation_arr[$mnt] =1;
                  $all_qrcodelocation_arr[$mnt] =1;
                  $all_qrcodecampaign_arr[$mnt] =1;
                  if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_campaign[$mnt]=  1;
                    
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_campaign[$mnt]= 1;
                }
                else{
                    $unknown_arr_campaign[$mnt]=  1;
                }
            }
            else if($Row_qrcode['is_unique']==0 && $Row_qrcode['campaign_id']!=0)
            {
                 if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_campaign[$mnt]= 1;
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_campaign[$mnt]= 1;
                }
                else{
                   
                    $unknown_arr_campaign[$mnt]=  1;
                }
                 $all_qrcodecampaign_arr[$mnt] =1;
            }
            else if($Row_qrcode['is_unique']==1 && $Row_qrcode['campaign_id']==0)
            {
               
                  $unique_qrcodelocation_arr[$mnt] =1;
            }
            else if($Row_qrcode['is_unique']==0 && $Row_qrcode['campaign_id']==0)
            {
                
                $all_qrcodelocation_arr[$mnt] =1;
            }
           }
                    }
    }
	
	foreach($male_arr_campaign as $key=>$value)
    {
        $tatal_users = $male_arr_campaign[$key]+$female_arr_campaign[$key]+$unknown_arr_campaign[$key];
        if($tatal_users!=0)
        {
			$per = round((($male_arr_campaign[$key]*100)/$tatal_users));
			$male_arr_campaign[$key]=$per;
			$per = round((($female_arr_campaign[$key]*100)/$tatal_users));
			$female_arr_campaign[$key]=$per;
			$per = round((($unknown_arr_campaign[$key]*100)/$tatal_users));
			$unknown_arr_campaign[$key]=$per;
		}
    }
     foreach($male_arr_location as $key=>$value)
    {
        $tatal_users = $male_arr_location[$key]+$female_arr_location[$key]+$unknown_arr_location[$key];
        if($tatal_users!=0)
        {
			$per = round((($male_arr_location[$key]*100)/$tatal_users));
			$male_arr_location[$key]=$per;
			$per = round((($female_arr_location[$key]*100)/$tatal_users));
			$female_arr_location[$key]=$per;
			$per = round((($unknown_arr_location[$key]*100)/$tatal_users));
			$unknown_arr_location[$key]=$per;
		}
    }
				}
       // $json_array['locatino_json']=$locatino_json;
       
	///
 }
 if($_REQUEST['year'] != 0 )
	{
		$year = $_REQUEST['year'];
	}
	else{
		$year = date('Y');
	}
  //echo "1";
 if($_REQUEST['year'] != 0 && $_REQUEST['month'] != 0)
	{
		   $from_date = $year."-".$_REQUEST['month']."-01 00:00:00";
	    	$to_date =  $year."-".$_REQUEST['month']."-".$m." 23:59:59";
	}
	else if($_REQUEST['year'] != 0 && $_REQUEST['month'] == 0)
	{
			   $from_date = $year."-01-01 00:00:00";
     	     	$to_date =  $year."-12-31 23:59:59";
	}
	
	 $month_expr = " and  DATE_FORMAT(c.expiration_date,'%Y') =".$year." ";
 if($_REQUEST['year'] != 0 )
{
						$dt_wh = "c.expiration_date <'".$to_date ."' ";
						$dt_wh1 = "";

					   
						   /*$sql = " select distinct a.* ,b.location_name ,b.address ,b.state,b.city,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where  c.id=a.user_id) gender
									from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id 
   where a.is_location<>1 and a.campaign_id in ( select campaign_id from campaign_location cl where cl.active<>1 and cl.active_in_future<>1 
  ) and 
	($dt_wh) $month_expr $merchant_sql ";*/
	$RS_qrcode = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.address ,b.state,b.city,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where  c.id=a.user_id) gender
									from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id 
   where a.is_location<>1 and a.campaign_id in ( select campaign_id from campaign_location cl where cl.active<>? and cl.active_in_future<>? 
  ) and ($dt_wh) $month_expr $merchant_sql ",array(1,1));

}else
{
							 
  /*$sql = "select distinct a.* ,b.location_name ,b.address ,b.state,b.city,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where 
								  c.id=a.user_id) gender from scan_qrcode a inner join locations b on a.location_id = b.id    inner join campaigns c on a.campaign_id = c.id 
								  where a.is_location<>1 and a.campaign_id in ( select campaign_id from campaign_location cl where cl.active<>1 and cl.active_in_future<>1 
  ) AND c.expiration_date <  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(b.timezone,1, POSITION(',' IN b.timezone)-1)) ".$merchant_sql;*/

$RS_qrcode = $objDB->Conn->Execute("select distinct a.* ,b.location_name ,b.address ,b.state,b.city,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where 
								  c.id=a.user_id) gender from scan_qrcode a inner join locations b on a.location_id = b.id    inner join campaigns c on a.campaign_id = c.id 
								  where a.is_location<>? and a.campaign_id in ( select campaign_id from campaign_location cl where cl.active<>1 and cl.active_in_future<>? 
  ) AND c.expiration_date <  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(b.timezone,1, POSITION(',' IN b.timezone)-1)) ".$merchant_sql,array(1,1));


}

$allfilter_locations = array();
//$RS_qrcode = $objDB->execute_query($sql);
 
   while($Row_qrcode = $RS_qrcode->FetchRow())
        {
		   if($Row_qrcode['location_name']== "")
            {
                     $locname = $Row_qrcode['address']." - ".$Row_qrcode['zip'];
            }
            else
            {
                    $locname = $Row_qrcode['location_name']." - ".$Row_qrcode['zip'];
            }
			$locname = $Row_qrcode['address'].",".$Row_qrcode['city'].",".$Row_qrcode['state'].",".$Row_qrcode['zip'];
            if(!array_key_exists($Row_qrcode['location_id'],$allfilter_locations)){
			 //echo "In if".$locname."#!#".$Row_qrcode['location_id'];
               $allfilter_locations[$Row_qrcode['location_id']] =  $locname."#!#".$Row_qrcode['location_id'];
            }
			
        }
		//echo "<pre>";
	//	print_r($location_arr );
		//echo "</pre>";
		$alllocations  = array_values($allfilter_locations);
 //print_r($allfilter_locations);
       $string = "";
			$string .= "<div style='overflow-y:auto;height:200px' >";
	
	for($k=0;$k<count($alllocations);$k++)
        {
              $id = explode("#!#",$alllocations[$k]);
			 
            if(in_array($id[1],$selected_loations))
            {
          
            $string .= '<input type="checkbox" value="'.$id[1].'" checked="checked" >'.$id[0].'<br  />';
            }
            else{
            $string .= '<input type="checkbox" value="'.$id[1].'" >'.$id[0].'<br  />'; 
            }
        }
        if($string != ""){
		$string .= "</div> <div style='border-top:1px dashed gray;margin-top:10px;padding-top:10px;'>";
            $string .="<input type='submit' name='btn_viewchart_campaign' id='btn_viewchart_campaign' value='View Chart' /></div>";
        }
		else{
			$string = "";
			}
		//	echo $string;
 $json_array=array();
 
        $json_array['arr_unique_qrcodetotal_arr']= $unique_qrcodetotal_arr;
        $json_array['arr_unique_qrcodelocation_arr']= $unique_qrcodelocation_arr;
        $json_array['arr_unique_qrcodecampaign_arr']= $unique_qrcodecampaign_arr;
        $json_array['arr_all_qrcodelocation_arr']= $all_qrcodelocation_arr;
        $json_array['arr_all_qrcodecampaign_arr']= $all_qrcodecampaign_arr;
        $json_array['arr_male_arr_campaign']= $male_arr_campaign;
        $json_array['arr_female_arr_campaign']= $female_arr_campaign;
        $json_array['arr_unknown_arr_campaign']= $unknown_arr_campaign;
        $json_array['arr_male_arr_location']= $male_arr_location;
        $json_array['arr_female_arr_location']= $female_arr_location;
        $json_array['arr_unknown_arr_location']= $unknown_arr_location;
 $json_array['unique_qrcodetotal_arr']= $unique_qrcodetotal_arr;
        $json_array['unique_qrcodelocation_arr']= $unique_qrcodelocation_arr;
        $json_array['unique_qrcodecampaign_arr']= $unique_qrcodecampaign_arr;
        $json_array['all_qrcodelocation_arr']= $all_qrcodelocation_arr;
        $json_array['all_qrcodecampaign_arr']= $all_qrcodecampaign_arr;
        $json_array['male_arr_campaign']= $male_arr_campaign;
        $json_array['female_arr_campaign']= $female_arr_campaign;
        $json_array['unknown_arr_campaign']= $unknown_arr_campaign;
        $json_array['male_arr_location']= $male_arr_location;
        $json_array['female_arr_location']= $female_arr_location;
        $json_array['unknown_arr_location']= $unknown_arr_location;
        $json_array['status']='true';
		     $json_array['location_text']  = $string;
		$json = json_encode($json_array);
	echo $json;
	exit();
// getting monthwise data
?>
