<?php

/**
 * @uses get qrcode data to generate report
 * @used in pages :reports.php
 * @author Sangeeta Raghavani
 */

header('Content-type: text/html; charset=utf-8');
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
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
			
			 $merchant_sql = "and  b.created_by = ".$merchant_id  ; 
		 }
//echo 123;
if($_REQUEST['month'] != 0)
    {
        if($_REQUEST['month']==4 ||$_REQUEST['month']==6 ||$_REQUEST['month']==9 ||$_REQUEST['month']==11 )
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
	if($_REQUEST['year'] != 0 && $_REQUEST['month'] != 0)
	{
		   $from_date = $_REQUEST['year']."-".$_REQUEST['month']."-01 00:00:00";
	    	$to_date =  $_REQUEST['year']."-".$_REQUEST['month']."-".$m." 23:59:59";
	}
	else if($_REQUEST['year'] != 0 && $_REQUEST['month'] == 0)
	{
			   $from_date = $_REQUEST['year']."-01-01 00:00:00";
     	     	$to_date =  $_REQUEST['year']."-12-31 23:59:59";
	}
        ?>
      
  <?php
    if($_REQUEST['month'] == 0 )
                                  {
                                     $month_expr = " and  DATE_FORMAT(c.expiration_date,'%Y') =".$_REQUEST['year']." ";
                                  }
                                  else
                                  {
                                       $month_expr = " and  DATE_FORMAT(c.expiration_date,'%m') =".$_REQUEST['year']." ";
                                  }
				if($_REQUEST['year'] != 0 )
                                {
								//echo "In IF";
                                                        $dt_wh = " DATE_FORMAT(a.scaned_date,'%Y') = ".$_REQUEST['year'] ;
                                                        $dt_wh1 = "";

                                          $month_expr = "  a.scaned_date between '".$from_date."' and '".$to_date."' ";                
                                   $sql = " select distinct a.* ,b.location_name ,b.address ,b.state,b.city,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where  c.id=a.user_id) gender
                                   from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id 
								   where a.is_location<>1 and ".$month_expr." ".$merchant_sql;
							}else
							{
							 //echo "In else";
                                                             
								  $sql = "select distinct a.* ,b.location_name ,b.address ,b.state,b.city,b.zip,b.created_by ,b.id location_id ,(select gender c from customer_user c where 
                                                                  c.id=a.user_id) gender from scan_qrcode a inner join locations b on a.location_id = b.id    inner join campaigns c on a.campaign_id = c.id 
                                                                  where a.is_location<>1 ".$merchant_sql;
							
							}
							//echo $sql;
							//exit;
//$sql = "select distinct a.* ,b.location_name ,b.id location_id ,(select gender c from customer_user c where  c.id=a.user_id) gender
// from scan_qrcode a inner join locations b on a.location_id = b.id  ";
    //$RS_qrcode = $objDB->execute_query($sql);
	$RS_qrcode = $objDB->Conn->Execute($sql);
    $location_arr = array();
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
//       $sql = " select distinct a.* ,b.location_name ,b.created_by ,b.id location_id ,(select gender c from customer_user c where  c.id=a.user_id) gender
//                                                                    from scan_qrcode a inner join locations b on a.location_id = b.id 
//								   where a.is_location=1";
//        
//          $RS_location_tot = $objDB->execute_query($sql);
//            while($Row_qrcode1 = $RS_location_tot->FetchRow())
//            {
////                 $temparr =$location_arr[$Row_qrcode1['location_id']];
//            if(array_key_exists($Row_qrcode['location_id'],$location_arr)){
//          
//                
//           if($Row_qrcode1['is_unique']==1 )
//            {
//                 echo "<br/>In if <br/>";
//                 $uniquecounterlocation = $unique_qrcodecampaign_arr[$Row_qrcode1['location_id']] +1;
//            }
//            else if($Row_qrcode1['is_unique']==0 )
//            {
//               echo "<br/>In Else<br/>";
//                 $allcounterlocation = $all_qrcodelocation_arr[$Row_qrcode1['location_id']] +1;
//            }
//             if($Row_qrcode1['gender'] ==1)
//                {
//                    $male_arr_location[$Row_qrcode1['location_id']]= $male_arr_location[$Row_qrcode1['location_id']] + 1;
//                }
//                else if($Row_qrcode1['gender'] ==2)
//                {
//                    $female_arr_location[$Row_qrcode1['location_id']]= $female_arr_location[$Row_qrcode1['location_id']] + 1;
//                }
//                else{
//                        $unknown_arr_location[$Row_qrcode1['location_id']]= $unknown_arr_location[$Row_qrcode1['location_id']] + 1;
//                }
//            $totalcounter = $unique_qrcodetotal_arr[$Row_qrcode1['location_id']]+1;
//         $unique_qrcodelocation_arr[$Row_qrcode1['location_id']] = $uniquecounterlocation;
//        
//         $all_qrcodelocation_arr[$Row_qrcode1['location_id']] = $allcounterlocation;
////         
//          $unique_qrcodetotal_arr[$Row_qrcode1['location_id']] = $totalcounter;
//            }
//            else {
//           
//             // array_push($location_arr,$Row_qrcode['location_id']);
//            if($Row_qrcode1['location_name']== "")
//            {
////                $sql = "select business merchant_user where id ="+$Row_qrcode['created_by'];
////                $RS_business = $objDB->execute_query($sql);
////                $locname= $RS_business->fields['business'];
//                $locname= " ";
//            }
//            else{
//                $locname= $Row_qrcode1['location_name'];
//            }
//            $location_arr[$Row_qrcode1['location_id']] = $locname."#!#".$Row_qrcode1['location_id'];
//                $unique_qrcodelocation_arr[$Row_qrcode1['location_id']] = 0;
//              $unique_qrcodecampaign_arr[$Row_qrcode1['location_id']] = 0;
//            $unique_qrcodetotal_arr[$Row_qrcode1['location_id']] = 1;
//            $all_qrcodelocation_arr[$Row_qrcode1['location_id']] = 0;
//           
//                     $male_arr_location[$Row_qrcode1['location_id']]=  0;
//               
//                 
//                    $female_arr_location[$Row_qrcode1['location_id']]=  0;
//           
//                   
//                    $unknown_arr_location[$Row_qrcode1['location_id']]=  0;
//                 
//      
//                  if($Row_qrcode1['gender'] ==1)
//                {
//                    $male_arr_location[$Row_qrcode1['location_id']]=1;
//                }
//                else if($Row_qrcode1['gender'] ==2)
//                {
//                    $female_arr_location[$Row_qrcode1['location_id']]= 1;
//                }
//                else{
//                   
//                    $unknown_arr_location[$Row_qrcode1['location_id']]= 1;
//                }
//            if($Row_qrcode1['is_unique']==1 && $Row_qrcode1['campaign_id']==0)
//            {
//               
//                  $unique_qrcodelocation_arr[$Row_qrcode1['location_id']] =1;
//            }
//            else if($Row_qrcode1['is_unique']==0 && $Row_qrcode1['campaign_id']==0)
//            {
//                
//                $all_qrcodelocation_arr[$Row_qrcode1['location_id']] =1;
//            }
//
//                    }
//            }
//     
   if($_REQUEST['selected_locations'] != "")
    {
        $selected_loations = explode(";",$_REQUEST['selected_locations']);
         $allfilter_locations  = array();
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
           
                $allfilter_locations[$Row_qrcode['location_id']] =  $locname."#!#".$Row_qrcode['location_id'];
            }
        }
    }
    else{
        $check_counter = 1;
         $selected_loations = array();
         $allfilter_locations  = array();
        while($Row_qrcode = $RS_qrcode->FetchRow())
        {
            if($check_counter <=7){
           if(!in_array($Row_qrcode['location_id'],$selected_loations)){
            array_push($selected_loations ,$Row_qrcode['location_id'] );
            $check_counter = $check_counter+1;
           }
           
            }
            
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
           
                $allfilter_locations[$Row_qrcode['location_id']] =  $locname."#!#".$Row_qrcode['location_id'];
            }
        }
    }

//    $RS_qrcode = $objDB->execute_query($sql);
	$RS_qrcode = $objDB->Conn->Execute($sql);

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
        if(array_key_exists($Row_qrcode['location_id'],$location_arr)){
           if(in_array($Row_qrcode['location_id'] ,$selected_loations)) {
            $temparr =$location_arr[$Row_qrcode['location_id']];
            
            if($Row_qrcode['is_unique']==1 && $Row_qrcode['campaign_id']!=0)
            {
                if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_campaign[$Row_qrcode['location_id']]= $male_arr_campaign[$Row_qrcode['location_id']] + 1;
                   
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_campaign[$Row_qrcode['location_id']]= $female_arr_campaign[$Row_qrcode['location_id']] + 1;
                    
                }
                else{
                    $unknown_arr_campaign[$Row_qrcode['location_id']]= $unknown_arr_campaign[$Row_qrcode['location_id']] + 1;
               
                }
                 $unique_qrcodecampaign_arr[$Row_qrcode['location_id']]=  $unique_qrcodecampaign_arr[$Row_qrcode['location_id']]+1;
             
                $all_qrcodecampaign_arr[$Row_qrcode['location_id']] = $all_qrcodecampaign_arr[$Row_qrcode['location_id']] +1;
            }
            else if($Row_qrcode['is_unique']==0 && $Row_qrcode['campaign_id']!=0)
            {
                if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_campaign[$Row_qrcode['location_id']]= $male_arr_campaign[$Row_qrcode['location_id']] + 1;
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_campaign[$Row_qrcode['location_id']]= $female_arr_campaign[$Row_qrcode['location_id']] + 1;
                }
                else{
                 
                    $unknown_arr_campaign[$Row_qrcode['location_id']]= $unknown_arr_campaign[$Row_qrcode['location_id']] + 1;
                   
                }
                 $all_qrcodecampaign_arr[$Row_qrcode['location_id']] = $all_qrcodecampaign_arr[$Row_qrcode['location_id']] +1;
            }
           
             if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_location[$Row_qrcode['location_id']]= $male_arr_location[$Row_qrcode['location_id']] + 1;
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_location[$Row_qrcode['location_id']]= $female_arr_location[$Row_qrcode['location_id']] + 1;
                }
                else{
                        $unknown_arr_location[$Row_qrcode['location_id']]= $unknown_arr_location[$Row_qrcode['location_id']] + 1;
                }
            $unique_qrcodetotal_arr[$Row_qrcode['location_id']] = $unique_qrcodetotal_arr[$Row_qrcode['location_id']]+1;
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
                $unique_qrcodelocation_arr[$Row_qrcode['location_id']] = 0;
              $unique_qrcodecampaign_arr[$Row_qrcode['location_id']] = 0;
            $unique_qrcodetotal_arr[$Row_qrcode['location_id']] = 1;
            $all_qrcodelocation_arr[$Row_qrcode['location_id']] = 0;
            $all_qrcodecampaign_arr[$Row_qrcode['location_id']] = 0;
           
                    $male_arr_campaign[$Row_qrcode['location_id']]=  0;
                     $male_arr_location[$Row_qrcode['location_id']]=  0;
               
                    $female_arr_campaign[$Row_qrcode['location_id']]= 0;
                    $female_arr_location[$Row_qrcode['location_id']]=  0;
           
                    $unknown_arr_campaign[$Row_qrcode['location_id']]=  0;
                    $unknown_arr_location[$Row_qrcode['location_id']]=  0;
                 
      
                     if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_location[$Row_qrcode['location_id']]=1;
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_location[$Row_qrcode['location_id']]= 1;
                }
                else{
                   
                    $unknown_arr_location[$Row_qrcode['location_id']]= 1;
                }
            if($Row_qrcode['is_unique']==1 && $Row_qrcode['campaign_id']!=0)
            {
                  $unique_qrcodecampaign_arr[$Row_qrcode['location_id']]=1;
                  $unique_qrcodelocation_arr[$Row_qrcode['location_id']] =1;
                  $all_qrcodelocation_arr[$Row_qrcode['location_id']] =1;
                  $all_qrcodecampaign_arr[$Row_qrcode['location_id']] =1;
                  if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_campaign[$Row_qrcode['location_id']]=  1;
                    
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_campaign[$Row_qrcode['location_id']]= 1;
                }
                else{
                    $unknown_arr_campaign[$Row_qrcode['location_id']]=  1;
                }
            }
            else if($Row_qrcode['is_unique']==0 && $Row_qrcode['campaign_id']!=0)
            {
                 if($Row_qrcode['gender'] ==1)
                {
                    $male_arr_campaign[$Row_qrcode['location_id']]= 1;
                }
                else if($Row_qrcode['gender'] ==2)
                {
                    $female_arr_campaign[$Row_qrcode['location_id']]= 1;
                }
                else{
                   
                    $unknown_arr_campaign[$Row_qrcode['location_id']]=  1;
                }
                 $all_qrcodecampaign_arr[$Row_qrcode['location_id']] =1;
            }
            else if($Row_qrcode['is_unique']==1 && $Row_qrcode['campaign_id']==0)
            {
               
                  $unique_qrcodelocation_arr[$Row_qrcode['location_id']] =1;
            }
            else if($Row_qrcode['is_unique']==0 && $Row_qrcode['campaign_id']==0)
            {
                
                $all_qrcodelocation_arr[$Row_qrcode['location_id']] =1;
            }
           }
                    }
    }
//calculate percentage
    
//    foreach($location_arr as $key=>$value)
//    {
       
          
    //}
    foreach($male_arr_campaign as $key=>$value)
    {
        $tatal_users = $male_arr_campaign[$key]+$female_arr_campaign[$key]+$unknown_arr_campaign[$key];
        $per = round((($male_arr_campaign[$key]*100)/$tatal_users));
        $male_arr_campaign[$key]=$per;
        $per = round((($female_arr_campaign[$key]*100)/$tatal_users));
        $female_arr_campaign[$key]=$per;
        $per = round((($unknown_arr_campaign[$key]*100)/$tatal_users));
        $unknown_arr_campaign[$key]=$per;
    }
     foreach($male_arr_location as $key=>$value)
    {
        $tatal_users = $male_arr_location[$key]+$female_arr_location[$key]+$unknown_arr_location[$key];
        $per = round((($male_arr_location[$key]*100)/$tatal_users));
        $male_arr_location[$key]=$per;
        $per = round((($female_arr_location[$key]*100)/$tatal_users));
        $female_arr_location[$key]=$per;
        $per = round((($unknown_arr_location[$key]*100)/$tatal_users));
        $unknown_arr_location[$key]=$per;
    }
    $json_array=array();
    $json_array['arr_locatino_json']=$location_arr;
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
    //calculate percentage
        $alllocations  = array_values($allfilter_locations);
    $locatino_json = array_values($location_arr);
     $unique_qrcodetotal_arr = array_values($unique_qrcodetotal_arr);
     $unique_qrcodelocation_arr =array_values($unique_qrcodelocation_arr);
     $unique_qrcodecampaign_arr = array_values($unique_qrcodecampaign_arr);
     $all_qrcodelocation_arr =array_values($all_qrcodelocation_arr);
     $all_qrcodecampaign_arr = array_values($all_qrcodecampaign_arr);
     $male_arr_campaign = array_values($male_arr_campaign);
     $female_arr_campaign = array_values($female_arr_campaign);
     $unknown_arr_campaign = array_values($unknown_arr_campaign);
     $male_arr_location = array_values($male_arr_location);
     $female_arr_location = array_values($female_arr_location);
     $unknown_arr_location = array_values($unknown_arr_location);
            $string = "";
			$string .= "<div style='overflow-y:auto;height:200px' >";
	for($k=0;$k<count($alllocations);$k++)
        {
              $id = explode("#!#",$alllocations[$k]);
			  
            if(array_key_exists($id[1],$location_arr))
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
     
        $json_array['locatino_json']=$locatino_json;
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
            $json_array['allfilterlocations']= $alllocations;
            $json_array['location_text']  = $string;
	$json = json_encode($json_array);
	echo $json;
	exit();

?>
