<?php
/**
 * @uses locu share merchant 
 * @used in pages :locations.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
//$objDBWrt = new DB('write');

include 'locu.php';

//$API_KEY = '269fe167da30803613598800a3da6e0e590297ac';

$array_as = array();
$array_as['id'] = 13;
$RS_as = $objDB->Show("admin_settings",$array_as);
$API_KEY=$RS_as->fields['value'];

//create api clients
$venue_client = new VenueApiClient($API_KEY);
$menuitem_client = new MenuItemApiClient($API_KEY);

//$website = array('website_url' => 'http://www.chinobandido.com/');




    
   if($_REQUEST['savecontinue'] == 'yes')
   {
       
       $location_id=  explode(",", $_REQUEST['location_id']);
        $location_id_data="";
        for($i=0;$i<count($location_id);$i++)
        {
            $location_id_data.=$location_id[$i];

            /*$sql_location_update="UPDATE locations SET venue_id='".$_REQUEST['venue_id']."' WHERE id=".$location_id[$i];
            $objDB->Conn->Execute($sql_location_update);*/
		$objDBWrt->Conn->Execute("UPDATE locations SET venue_id=? WHERE id=?",array($_REQUEST['venue_id'],$location_id[$i]));


        }
        $get_venue_id="0";
        //$result = mysql_query("select * from locations where created_by=".$_REQUEST['customer_id']);
	$result = $objDB->Conn->Execute("select * from locations where created_by=?",array($_REQUEST['customer_id']));

$all_data=array();
$venue_id="";
$venue_id_yes="0";
$location_id="";
        //while( $userData = mysql_fetch_array($result)){   
       while( $userData = $result->FetchRow()){
    if($userData['website'] != "")
    {
        if($userData['venue_id'] == "")
        { 
            
            //$url = $userData['website'];
            //$path_url=parse_url($url);
 
            //$website = array('website_url' => $path_url['host']);
            
            $url = $userData['website'];
            $path_url=parse_url($url);
            $path_url="http://".$path_url['host'];
            //echo $path_url; 
            //echo $path_url['host']."</br>";
            //$website = array('website_url' => 'http://'.$path_url['host']);
			$website = array('website_url' => $path_url,'has_menu' => true,'country' => $userData['country']);
            //$website = array('website_url' => $userData['website']);
            $locudata=$venue_client->search($website);

            
            for($i=0;$i<count($locudata);$i++)
            {
                
                
                if($locudata[$i]['has_menu'] == "1")
                {
                    $location_id= $userData['id'];
                    $location_name= $userData['location_name'];
                    $location_address= $userData['address'];
                    $location_city= $userData['city'];
                    $location_state= $userData['state'];
                    $location_country= $userData['country'];
                    $location_zip= $userData['zip'];
                     $location_website=$userData['website']; 
                    $venue_id=$locudata[$i]['id'];
                    $get_venue_id="1";
                     
                    
                    //break;
                }
                

            }
           
        }   
    }
     if($get_venue_id == "1")
    {
        break;
    }
    //array_push($all_data,$venue_client->search($website));  
    
}
 if($venue_id != "")
            {
 /*$sql_location_detail_not_match="select * from locations where created_by=".$_REQUEST['customer_id']." && id NOT IN($location_id)";
 $location_detail_not_match_data=  $objDB->Conn->Execute($sql_location_detail_not_match);*/
	$location_detail_not_match_data=  $objDB->Conn->Execute("select * from locations where created_by=? && id NOT IN(?)",array($_REQUEST['customer_id'],$location_id));

 $location_detail="";
                if($location_detail_not_match_data->RecordCount() > 0)
                {
                    $location_detail.="<table style='width:100%;line-height:19px'>";
                                            $location_detail.="<tr>";
                                                    $location_detail.="<th>";
                                                             $location_detail.="Action"; 
                                                     $location_detail.="</th>";
                                                     /*
                                                  $location_detail.="<th>";
                                                            $location_detail.="Location Name"; 
                                                     $location_detail.="</th>";
                                                     
                                                      */
                                                     $location_detail.="<th>";
                                                             $location_detail.="Address"; 
                                                     $location_detail.="</th>";
                                                     
                                                     $location_detail.="<th>";
                                                             $location_detail.="Postal Code"; 
                                                     $location_detail.="</th>";
                                                     
                                             $location_detail.="</tr>";
                                        
                                    $count_data=0;   
                               while($Row_data_not_match = $location_detail_not_match_data->FetchRow())
                               { 
                                     //echo $location_detail=$Row_data_not_match['location_name']."</br>";
                                      if($location_website == $Row_data_not_match['website'] || $Row_data_not_match['website'] == "")
                                      {
                                         if($Row_data_not_match['venue_id'] == "")
                                         {   
                                            $location_detail.="<tr>";
                                     
                                                    $location_detail.="<td>";
                                                                $location_detail.="<input type='checkbox' class='location_".$Row_data_not_match['id']."' value='".$Row_data_not_match['id']."' name='location_venue' />";
                                                    $location_detail.="</td>";
/*
                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['location_name'];
                                                    $location_detail.="</td>";
*/
                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['address'].",".$Row_data_not_match['city'].",".$Row_data_not_match['state'];
                                                    $location_detail.="</td>";



                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['zip'];
                                                    $location_detail.="</td>";
                                            
                                            
                                            
                                            $location_detail.="</tr>";
                                            $count_data++;
                                         }   
                                      }
                               }
                               $location_detail.="</table>";
                         if($count_data == "0")
                               {
                                   $location_detail="";
                                   echo "success"."###".$venue_id."###".$location_id."###".$location_name."###".$location_address."###".$location_city."###".$location_state."###".$location_country."###".$location_zip."###".$location_detail."###".$location_website."###"."notgetlocation";
                               }
                               else
                               {
            
                                    echo "success"."###".$venue_id."###".$location_id."###".$location_name."###".$location_address."###".$location_city."###".$location_state."###".$location_country."###".$location_zip."###".$location_detail."###".$location_website;
                               }       

          
                
            }
            else
            {
               echo "success"."###".$venue_id."###".$location_id."###".$location_name."###".$location_address."###".$location_city."###".$location_state."###".$location_country."###".$location_zip."###".$location_detail."###".$location_website;  
            }
         }
            else
            {
                /*$sql_location_detail_not_match="select * from locations where created_by='".$_REQUEST['customer_id']."' and (venue_id IS NULL or venue_id='')";
                 //$sql_location_detail_not_match="select * from locations where created_by=17";
                            
                            $location_detail_not_match_data=  $objDB->Conn->Execute($sql_location_detail_not_match);*/
				$location_detail_not_match_data=  $objDB->Conn->Execute("select * from locations where created_by=? and (venue_id IS NULL or venue_id='')",array($_REQUEST['customer_id']));

                            if($location_detail_not_match_data->RecordCount() > 0)
                            {
                                $location_detail.="<table style='width:100%;line-height:19px'>";
                                $location_detail.="<tr>";
                                   /* 
                                  $location_detail.="<th>";
                                            $location_detail.="Location Name"; 
                                     $location_detail.="</th>";
                                    
                                    */
                                     $location_detail.="<th>";
                                             $location_detail.="Address"; 
                                     $location_detail.="</th>";

                                     $location_detail.="<th>";
                                             $location_detail.="Postal Code"; 
                                     $location_detail.="</th>";

                                $location_detail.="</tr>";

                                       
                               while($Row_data_not_match = $location_detail_not_match_data->FetchRow())
                               { 
                                     //echo $location_detail=$Row_data_not_match['location_name']."</br>";
                                     
                                         //if($Row_data_not_match['venue_id'] == "")
                                         //{   
                                            $location_detail.="<tr>";
                                     
                                                   
                                                    /*
                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['location_name'];
                                                    $location_detail.="</td>";
                                                    */
                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['address'].",".$Row_data_not_match['city'].",".$Row_data_not_match['state'];
                                                    $location_detail.="</td>";



                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['zip'];
                                                    $location_detail.="</td>";
                                            
                                            
                                            
                                            $location_detail.="</tr>";
                                         //}   
                                      
                               }
                               $location_detail.="</table>";
                               
                               $location_detail.="error"."###".$location_detail;
                            }
                            else
                            {
                                 $location_detail.=$merchant_msg['locations']['Msg_all_menu_price_get_venue_id']."###"."allclick";
                            }
                            
                          echo $location_detail;
                           
                
            }
   }
   else
   {
       
$get_venue_id="0";
//$result = mysql_query("select * from locations where created_by=".$_REQUEST['customer_id']);
$result = $objDB->Conn->Execute("select * from locations where created_by=?",array($_REQUEST['customer_id']));

$all_data=array();
$venue_id="";
$venue_id_yes="0";
$location_id="";
//while( $userData = mysql_fetch_array($result)){   
   while( $userData = $result->FetchRow()){  
    if($userData['website'] != "")
    {
        if($userData['venue_id'] == "")
        { 
            
            $url = $userData['website'];
            $path_url=parse_url($url);
            $path_url="http://".$path_url['host'];
            
            //$website = array('website_url' => 'http://'.$path_url['host']);
            $website = array('website_url' => $path_url,'has_menu' => true,'country' => $userData['country']);
			//$website .="&has_menu=true&country=".$userData['country'];
            
            //$website = array('website_url' => $userData['website']);
            $locudata=$venue_client->search($website);
		
			
            for($i=0;$i<count($locudata);$i++)
            {
                
                
                if($locudata[$i]['has_menu'] == "1")
                {
                    $location_id= $userData['id'];
                    $location_name= $userData['location_name'];
                    $location_address= $userData['address'];
                    $location_city= $userData['city'];
                    $location_state= $userData['state'];
                    $location_country= $userData['country'];
                    $location_zip= $userData['zip'];
                     $location_website=$userData['website']; 
                    $venue_id=$locudata[$i]['id'];
                    $get_venue_id="1"; 
                    
                    //break;
                }
                

            }
           
       }   
    }
	
    if($get_venue_id == "1")
    {
        break;
    }
    //array_push($all_data,$venue_client->search($website));  
    
}

if($venue_id != "")
            {

 /*$sql_location_detail_not_match="select * from locations where created_by=".$_REQUEST['customer_id'];
  
 $location_detail_not_match_data=  $objDB->Conn->Execute($sql_location_detail_not_match);*/
$location_detail_not_match_data=  $objDB->Conn->Execute("select * from locations where created_by=?",array($_REQUEST['customer_id']));

 $location_detail="";
            if($location_detail_not_match_data->RecordCount() > 0)
            {
								$location_detail.="<div style='display:none;color:red;height:20px;' id='error_msg_checkbox'>Please select at least one location</div>";	
                                $location_detail.="<table style='width:100%;line-height:19px'>";
                                            $location_detail.="<tr>";
                                                    $location_detail.="<th>";
                                                             $location_detail.="Action"; 
                                                     $location_detail.="</th>";
                                                     /*
                                                  $location_detail.="<th>";
                                                            $location_detail.="Location Name"; 
                                                     $location_detail.="</th>";
                                                     
                                                      */
                                                     $location_detail.="<th>";
                                                             $location_detail.="Address"; 
                                                     $location_detail.="</th>";
                                                     
                                                     $location_detail.="<th>";
                                                             $location_detail.="Postal Code"; 
                                                     $location_detail.="</th>";
                                                     
                                             $location_detail.="</tr>";
                                        
                               $count_data=0;        
                               while($Row_data_not_match = $location_detail_not_match_data->FetchRow())
                               { 
                                     //echo $location_detail=$Row_data_not_match['location_name']."</br>";
                                      if($location_website == $Row_data_not_match['website'] || $Row_data_not_match['website'] == "")
                                      {
                                         if($Row_data_not_match['venue_id'] == "")
                                         {   
                                            $location_detail.="<tr>";
                                     
                                                    $location_detail.="<td>";
                                                                $location_detail.="<input type='checkbox' class='location_".$Row_data_not_match['id']."' value='".$Row_data_not_match['id']."' name='location_venue' />";
                                                    $location_detail.="</td>";
/*
                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['location_name'];
                                                    $location_detail.="</td>";
*/
                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['address'].",".$Row_data_not_match['city'].",".$Row_data_not_match['state'];
                                                    $location_detail.="</td>";



                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['zip'];
                                                    $location_detail.="</td>";
                                            
                                            
                                            
                                            $location_detail.="</tr>";
                                            $count_data++;
                                         }  
                                         
                                      }
                               }
                               $location_detail.="</table>";

                               if($count_data == "0")
                               {
                                   $location_detail="";
                                   echo "success"."###".$venue_id."###".$location_id."###".$location_name."###".$location_address."###".$location_city."###".$location_state."###".$location_country."###".$location_zip."###".$location_detail."###".$location_website."###"."notgetlocation";
                               }
                               else
                               {
            
                                    echo "success"."###".$venue_id."###".$location_id."###".$location_name."###".$location_address."###".$location_city."###".$location_state."###".$location_country."###".$location_zip."###".$location_detail."###".$location_website;
                               }
              }
              else
              {
                echo "success"."###".$venue_id."###".$location_id."###".$location_name."###".$location_address."###".$location_city."###".$location_state."###".$location_country."###".$location_zip."###".$location_detail."###".$location_website;
              } 
            }
            else
            {
                
                 
                 /*$sql_location_detail_not_match="select * from locations where created_by='".$_REQUEST['customer_id']."' and (venue_id IS NULL or venue_id='')";
                 //$sql_location_detail_not_match="select * from locations where created_by=17";
                            
                            $location_detail_not_match_data=  $objDB->Conn->Execute($sql_location_detail_not_match);*/
				$location_detail_not_match_data=  $objDB->Conn->Execute("select * from locations where created_by=? and (venue_id IS NULL or venue_id='')",array($_REQUEST['customer_id']));
                            
                            if($location_detail_not_match_data->RecordCount() > 0)
                            {
                                $location_detail.="<table style='width:100%;line-height:19px'>";
                                $location_detail.="<tr>";
                                    /*
                                  $location_detail.="<th>";
                                            $location_detail.="Location Name";
                                     
                                     */ 
                                     $location_detail.="</th>";
                                     $location_detail.="<th>";
                                             $location_detail.="Address"; 
                                     $location_detail.="</th>";

                                     $location_detail.="<th>";
                                             $location_detail.="Postal Code"; 
                                     $location_detail.="</th>";

                                $location_detail.="</tr>";

                                       
                               while($Row_data_not_match = $location_detail_not_match_data->FetchRow())
                               { 
                                     //echo $location_detail=$Row_data_not_match['location_name']."</br>";
                                     
                                         //if($Row_data_not_match['venue_id'] == "")
                                         //{   
                                            $location_detail.="<tr>";
                                     
                                                   
/*
                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['location_name'];
                                                    $location_detail.="</td>";
*/
                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['address'].",".$Row_data_not_match['city'].",".$Row_data_not_match['state'];
                                                    $location_detail.="</td>";



                                                    $location_detail.="<td>";
                                                                $location_detail.=$Row_data_not_match['zip'];
                                                    $location_detail.="</td>";
                                            
                                            
                                            
                                            $location_detail.="</tr>";
                                        // }   
                                      
                               }
                               $location_detail.="</table>";
                                $location_detail.="error"."###".$location_detail;
                            }
                            else
                            {
                                 $location_detail.=$merchant_msg['locations']['Msg_all_menu_price_get_venue_id']."###"."allclick";
                            }
                            
                            echo $location_detail;
                          
                           
               
            
            }
   }
//$website = array('website_url' => 'http://www.subway.com');

//echo "<pre>";
//print_r($all_data);
//print_r($venue_client->search($website));
//echo "</pre>";
?>
