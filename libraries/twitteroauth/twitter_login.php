<?php 

//require_once("../classes/Config.Inc.php");
//check_customer_session();

//include_once(SERVER_PATH."/classes/JSON.php");

//include_once(SERVER_PATH."/classes/DB.php");

//$objDB = new DB();



require_once('twitteroauth.php');

//require_once('config-sample.php');

include_once("twitter_secret.php");

//define('CONSUMER_KEY', 'OBP22khyDh7t9cxKvuWA');
//define('CONSUMER_SECRET', 'EoWzcYS5oRCvq9aZsFL68PeCMa75tIprl6OaNnG8B0');
//define('TOKEN', '2202420074-2D0HsMqr9MagVmlqWhmBGEi09CGMvWkHgAFP0Ha');
//define('TOKEN_SECRET', 'q9YgNyXqfFMPTuF5qbMrqXYGfqvYF4OCeSYbJiPycwe7V');

define('OAUTH_CALLBACK', $_REQUEST['redirect_url']);


$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
$request_token = $connection->getRequestToken(OAUTH_CALLBACK); //get Request Token
//echo $request_token; 
session_start();

$Sql_new = "select * from customer_user  where id=".$_REQUEST['customer_id'];
$RS_user_data = $objDB->Conn->Execute($Sql_new);
                    
//echo $_REQUEST['campaign_title'];
//exit;
if( $request_token)
{
    $token = $request_token['oauth_token'];
    $_SESSION['request_token'] = $token ;
    $_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];
 
   if($RS_user_data->fields['twitter_access_token'] != "")
    {
       try{
            $connection_a = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $RS_user_data->fields['twitter_access_token'], $RS_user_data->fields['twitter_access_token_secret']);
             
            $camp_keywd="";
            
			$campaign_title = $_REQUEST['campaign_title'];
			$campaign_tag = $_REQUEST['campaign_tag'];
			
            $link_length=strlen($campaign_title)+23;
            $total_count=$link_length;
            //echo $link_length;
            //echo "<br/>";
             $camp_keyword=explode(" ",$campaign_tag);
             
             if(array_key_exists(0, $camp_keyword) && $camp_keyword[0]!="" )
             {
                 //echo strlen($camp_keyword[0]);
                 
                 $total_count = $total_count +strlen($camp_keyword[0]);
                 if($total_count>140)
                 {
                 
                 }
                 else
                 {
                      $camp_keywd = $camp_keywd.$camp_keyword[0]." ";                    
                 }
              /*
                 echo $total_count; 
                 echo "<br/>";
                */ 
             }
             if(array_key_exists(1, $camp_keyword) && $camp_keyword[1]!="" )
             {
                 $total_count = $total_count +strlen($camp_keyword[1]);
                 if($total_count>140)
                 {
                 
                 }
                 else
                 {
                      $camp_keywd = $camp_keywd.$camp_keyword[1]." ";                     
                 }
                /*
                 echo $total_count;
                 echo "<br/>";
                 */
                  
             }
             if(array_key_exists(2, $camp_keyword) && $camp_keyword[2]!="" )
             {
                 $total_count = $total_count +strlen($camp_keyword[2]);
                 if($total_count>140)
                 {
                 
                 }
                 else
                 {
                      $camp_keywd = $camp_keywd.$camp_keyword[2]." ";         
                 }
                 /*
                 echo $total_count;
                 echo "<br/>";
                 */
                  
             }
            
             //$tweet_data=$campaign_title." ".$camp_keywd." http://www.scanflip.com/campaign_facebook.php?campaign_id=752&l_id=80&domain=1 ";
			 
             //$twitter_share_link	= "http://www.scanflip.com/register.php?campaign_id=".$_REQUEST['campaign_id']."&l_id=".$_REQUEST['l_id']."&domain=2&share=true&customer_id=".base64_encode($_REQUEST['customer_id']);
			 $twitter_share_link	= WEB_PATH."/register.php?campaign_id=".$_REQUEST['campaign_id']."&l_id=".$_REQUEST['l_id']."&domain=2&share=true&customer_id=".base64_encode($_REQUEST['customer_id']);
              $tweet_data="#".$campaign_title." ".$camp_keywd.$twitter_share_link;
			  
              /*
               if(strlen($tweet_data)>196)
			  {
					$camp_title = substr($campaign_title,0,strlen($campaign_title)-(strlen($tweet_data)-196));
					
					$tweet_data=$camp_title." ".$camp_keywd.$twitter_share_link;
			  }
			  */

			  //$tweet_data = "#Scanflip Offer -campaign title character testing at ! 20% off , Wow $200 , at Grecko testing #characterFIFA http://www.scanflip.com/register.php?campaign_id=1365&l_id=71&domain=1&share=true&customer_id=OTk=";
			  
            /*
              echo $tweet_data;
              echo "<br/>";
              
              echo strlen($tweet_data);
              echo "<br/>";
             */ 
              
            $params_tweets =array(
                
                'status'=>$tweet_data,
                
                
            );
           
           $content = $connection_a->post('statuses/update',$params_tweets);
            
			//print_r($content);
            
            
            if(isset($content->errors[0]))
            {
				if($content->errors[0]->code == "89")
				{
					$url = $connection->getAuthorizeURL($token);
					 
					echo "reautho|".$url; 
				}
			}
            else
            {
                echo "successtweet";
            }
            
             
           
       }catch(Exception $t)
       {
           echo $t;
       }
    }
    else
    {
        switch ($connection->http_code) 
        {
            case 200:
                $url = $connection->getAuthorizeURL($token);
                //redirect to Twitter .
                //header('Location: ' . $url); 
                echo "success|".$url; 
                break;
            default:
                echo "Coonection with twitter Failed";
                break;
        }
    } 
}
else //error receiving request token
{
    echo "Error Receiving Request Token";
}



?>

    
    
    
    
    
    
    



