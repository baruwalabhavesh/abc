<?php
/******** 
@USE : update access token
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymerchants.php, my-deals.php, location_detail.php, search-deal.php, campaign.php
*********/
//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

require LIBRARY.'/fb-sdk/src/facebook.php';


/*$facebook = new Facebook(array(
  'appId'  => '654125114605525',
  'secret' => '2870b451a0e7d287f1899d0e401d3c4e',
));*/

include_once(LIBRARY."/fb-sdk/src/facebook_secret.php");
$facebook = new Facebook(array(
  'appId'  => facebookappId,
  'secret' => facebooksecret,
  
));

//$app_id='654125114605525';
//$lient_secret='2870b451a0e7d287f1899d0e401d3c4e';

                
                
               if ($_REQUEST['facebook_user_id'] != "") {
                    try {
                       
                              $accessToken = $facebook->getAccessToken();
                              //echo $accessToken;
                              //$user_profiles = $facebook->api('me/accounts/pages');  
                              //$facebook->api("/".$_REQUEST['facebook_user_id']."?fields=access_token");
                              
                               
                             if (!empty( $accessToken )) {			
                               
                                 


                                   

                                      $sql="UPDATE customer_user SET access_token='".$accessToken."' WHERE id=".$_REQUEST['customer_id'];
                                      
                                      $objDB->Conn->Execute($sql);

                                       echo "tokensucess";


                             } else {
                                $status = 'No access token recieved';
                             }
                     } catch (FacebookApiException $e) {
                            error_log($e); 				
                     }

             } 
             
             
            
   



?>
