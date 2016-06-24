<?php
/******** 
@USE : qrcode scan functions
@PARAMETER : 
@RETURN : 
@USED IN PAGES : qr.php
*********/
//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
//$objDBWrt = new DB('write');
//$objJSON = new JSON();
$JSON = $objJSON->get_compain_details($_REQUEST['campaign_id']);
$reserved = 0;
$RS = json_decode($JSON);
/*****************/
/* $redirect_query = "select permalink from campaign_location where campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['l_id'];
$redirect_RS = $objDB->Conn->Execute($redirect_query ); */
$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));

$r_url = $redirect_RS->fields['permalink'];
/******************/
?>
  <?php
        if(isset($_SESSION['customer_id']))
        {
			/* $Sql_new = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$_REQUEST['campaign_id']."' AND location_id =".$_REQUEST['l_id']." and activation_status=1";
			$RS_new = $objDB->Conn->Execute($Sql_new); */
			$RS_new = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =? and activation_status=1",array($_SESSION['customer_id'],$_REQUEST['campaign_id'],$_REQUEST['l_id']));
			
                if($RS_new->RecordCount()<=0)
                {
                    $no_rec = "inactive";
                }
                /* $Sql_new_coupon = "SELECT * FROM  coupon_codes where customer_campaign_code =".$_REQUEST['campaign_id']." AND location_id=".$_REQUEST['l_id']." and customer_id=".$_SESSION['customer_id']." and active=1";
				$RS_new_coupon = $objDB->Conn->Execute($Sql_new_coupon); */
				$RS_new_coupon = $objDB->Conn->Execute("SELECT * FROM  coupon_codes where customer_campaign_code =? AND location_id =? and customer_id=? and activation_status=1",array($_REQUEST['campaign_id'],$_REQUEST['l_id'],$_SESSION['customer_id']));
                    
        }
       
         ?>
                       <?php 
                 
            /* $location_max_sql = "Select num_activation_code , offers_left , active from campaign_location where  campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['l_id'];
			$location_max = $objDB->Conn->Execute($location_max_sql); */
			$location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left , active from campaign_location where  campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
			
			$max_coupon = $location_max->fields['num_activation_code'];
			$o_left = $location_max->fields['offers_left'];
			$is_active =  $location_max->fields['active'];
           
          // echo  $max_coupon."==";
          
                /* $remain_sql="select count(*) as total from coupon_codes where customer_campaign_code =".$_REQUEST['campaign_id']." AND location_id=".$_REQUEST['l_id'];        
                $RS_remain = $objDB->Conn->Execute($remain_sql); */
                $RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes where customer_campaign_code =? AND location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
                
                        $remain_val = $RS_remain->fields['total'];
                      //  echo  $remain_val."==";
                        $max_coupon = $max_coupon - $remain_val;
                         $is_new_user= 0;
                        ?>
                              <?php 
		
                            if(isset($_SESSION['customer_id']))
			    {
                                
                                // Starting //
                            /* */
											/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where  location_id=". $_REQUEST['l_id']." and customer_id=".$_SESSION['customer_id'].") ";
											//echo "sql_check===".$sql_chk."<br/>";
                                            $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
                                            $Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where  location_id=? and customer_id=?)",array($_REQUEST['l_id'],$_SESSION['customer_id']));
                                            
                                            if($Rs_is_new_customer->RecordCount()==0)
                                            {
                                                $is_new_user= 1;
                                            }
                                            else {
                                                  $is_new_user= 0;
                                            }
                                            /* */
                                
								/* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_REQUEST['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_REQUEST['l_id'];
                                //$sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_REQUEST['campaign_id']." and cg.group_id=mg.id ";
								//  echo "<br/>===".$sql."===<br />";
                                $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                                $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
                                
                                $c_g_str = "";
                                $cnt =1;
                              //  echo $RS_campaign_groups->RecordCount()."====";
                               if($is_new_user == 0)
                               {
                                 $is_it_in_group = 0;
                                if($RS[0]->level == 0)
                                { 
                                    if($RS_campaign_groups->RecordCount()>0)
                                        {
                                     while($Row_campaign = $RS_campaign_groups->FetchRow())
                                        { 
                                            $c_g_str .= $Row_campaign['group_id'];
                                            if($cnt != $RS_campaign_groups->RecordCount())
                                            {
                                                $c_g_str .= "," ;
                                            }
                                            $cnt++;
                                        }
										
										/* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
										// echo $Sql_new_;
										$RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                        /* for checking whether customer in campaign group */
										$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=?  AND group_id in( select  id from merchant_groups where id in(?))",array($_SESSION['customer_id'],$c_g_str));
										
                                        while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                        {
                                            /* $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
                                            $RS_query = $objDB->Conn->Execute($query); */
											$RS_query = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=?  and group_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));			
                                             if($RS_query->RecordCount() > 0)
                                             {

                                                  $is_it_in_group = 1;
                                             }
                                        }
                                        if($is_it_in_group == 1 )
                                            { 
                                                  $cust_in_same_group_of_camp = 1;  	
                                            }
                                           else {
                                                $cust_in_same_group_of_camp = 0;
                                           }
                                        }
                                            else{
                                                $cust_in_same_group_of_camp = 0;  	
                                            }
                                } 
                                else
                                {
                                 //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                   $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$_REQUEST['l_id']."  )";
                                   $cust_in_same_group_of_camp = 1;  	
                                }
                               }else{
                                    $cust_in_same_group_of_camp = 1;  
                               }
                                //
                               
                               
                               // echo "<br />SQl_new===".$Sql_new_ ."=====<br />";
                         
                            /* for checking whether customer in campaign group */
                                  
									
								/* $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$_REQUEST['l_id']."  )";
								$RS_check_s = $objDB->Conn->Execute($Sql_new); */
								$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where location_id  =?)",array($_SESSION['customer_id'],$_REQUEST['l_id']));
								
								// echo $Sql_new;
                                $onepercust_flag = 1;
                                $oneperday_multi_flag = 1;
                                $share_flag = 1;
                                
                                  
                                $its_new_user =0;
                                   if($RS[0]->new_customer == 1)
                                          {
                                              /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=". $_REQUEST['l_id'].") ";
                                              $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                              $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($_SESSION['customer_id'],$_REQUEST['l_id']));
                                              if($subscibed_store_rs->RecordCount()==0)
                                              {
                                                  $share_flag= 1;
                                                  $its_new_user =1;
                                              }
                                              else {
                                                    $share_flag= 0;
                                                    $its_new_user =0;
                                              }
                                              
                                          }
                                  if($RS_check_s->RecordCount()>0 && $RS_new->RecordCount()<=0 )
                                  { 
                                      if(  $o_left>0 && $is_active==1)
                                      {
                                          //echo "I main if";
                                      //    echo $share_flag;
                                            if( $share_flag== 1)
                                            {
                                          //      echo "In if";
                                                if($cust_in_same_group_of_camp ==1 || $its_new_user==1)
                                                {
                                                     
                                                $reserved = 0;

                                                }
                                               else{
                                                $reserved = 0; 
                                                }
                                            }  else {
                                              $reserved = 0;
                                                }
                                          
                                      ?>
                              
                              <?php
                                      }  else {
                                          $reserved = 0;
                                          
                                          }
                                  }
                                  else if($RS_check_s->RecordCount()>0 || $RS_new->RecordCount()>0 )
                                  { 
                                     
								//  if(  $o_left>0 && $is_active==1)
                                      //{
                                      
                                      if($RS[0]->new_customer == 1)
                                       {
                                          
												/* $sql_is_redeem = "select * from reward_user where campaign_id =".$_REQUEST['campaign_id']." AND location_id=".$_REQUEST['l_id']." and customer_id=".$_SESSION['customer_id']." and referred_customer_id=0"; 
												$RS_is_redeem =$objDB->Conn->Execute($sql_is_redeem); */
												$RS_is_redeem = $objDB->Conn->Execute("select * from reward_user where campaign_id =? AND location_id=? and customer_id=? and referred_customer_id=0",array($_REQUEST['campaign_id'],$_REQUEST['l_id'],$_SESSION['customer_id']));
                                                 if($RS_is_redeem->RecordCount() == 0)
                                                 {
                                                    
                                                     $reserved = 1;
                                                  }
                                                  else{
                                                
                                                      // for one per customer redeem deal (new customers only)
                                                      $onepercust_flag = 0;
                                                    $reserved = 0;
                                                        }
                                      }
                                      else
                                      {
                                        
                                          if($RS[0]->number_of_use ==1)
                                          {
                                            
												/* $sql_is_redeem = "select * from reward_user where campaign_id =".$_REQUEST['campaign_id']." AND location_id=".$_REQUEST['l_id']." and customer_id=".$_SESSION['customer_id']." and referred_customer_id=0";
                                                $RS_is_redeem =$objDB->Conn->Execute($sql_is_redeem); */
												$RS_is_redeem = $objDB->Conn->Execute("select * from reward_user where campaign_id =? AND location_id=? and customer_id=? and referred_customer_id=0",array($_REQUEST['campaign_id'],$_REQUEST['l_id'],$_SESSION['customer_id']));
												
                                                 if($RS_is_redeem->RecordCount() == 0)
                                                 {
                                      
						$reserved = 1;
                                                  }
                                                  else{
                                                  
                                                      // for one per customer redeem deal (new customers only)
                                                      $onepercust_flag = 0;
                                                      $reserved = 0;
                                                      }
                                          }
                                          else{
                                           
												/* $sql_is_redeem = "select * from reward_user where campaign_id =".$_REQUEST['campaign_id']." AND location_id=".$_REQUEST['l_id']." and customer_id=".$_SESSION['customer_id']." and referred_customer_id=0";
                                                $RS_is_redeem =$objDB->Conn->Execute($sql_is_redeem); */
                                                $RS_is_redeem = $objDB->Conn->Execute("select * from reward_user where campaign_id =? AND location_id=? and customer_id=? and referred_customer_id=0",array($_REQUEST['campaign_id'],$_REQUEST['l_id'],$_SESSION['customer_id']));
                                                 if($RS_is_redeem->RecordCount() == 0)
                                                 {
                                                 
                                                         $reserved = 1;
                                                 }
                                                 else{
                                                   
                                                      if($o_left>0 )
                                                     {
                                                          
                                                            $reserved = 1;
                                                     }
                                                     else{
                                                       
                                                      // for one per customer per day , multiple use 
                                                         $oneperday_multi_flag = 0;
                                                        $reserved = 0; 
                                                     }
                                                 }
                                                 }
                                      }
                              
//}  else { ?>
                              <?php // }
						/* $Sql_new = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$_REQUEST['campaign_id']."' AND location_id =".$_REQUEST['l_id'];
						$RS_new = $objDB->Conn->Execute($Sql_new); */
						$RS_new = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=?  AND campaign_id=?  AND location_id = ? ",array($_SESSION['customer_id'],$_REQUEST['campaign_id'],$_REQUEST['l_id'],));
						
                if($RS_new->RecordCount()>0){
                    ?>
                              <!--                       <a class="reserve_print" href="process.php?btnunreservedeal=1&campaign_id=<?php echo $_REQUEST['campaign_id']; ?>&l_id=<?php echo $_REQUEST['l_id']; ?>">                                    
					UnReserve Offer
									</a>-->
                              <?php
                }
									?>
                              <?php  }  else {
                                      if(  $o_left>0 && $is_active==1) {
                                          if($cust_in_same_group_of_camp ==1)
                                          {
                                              
                                          }
                                            if( $share_flag== 1)
                                            {
                                                 if($cust_in_same_group_of_camp ==1 || $its_new_user==1)
                                                {
                                                     if($RS[0]->level == 0)
                                                     { 
                                                         $reserved = 0;
                                                         }
                                                     else{
                                                         $reserved = 0;
                                                     }
                                      ?>
                            <?php }else{
                                                    $reserved = 0;
                                                }
                                            }
                                            else{
                                                $reserved = 0;
                                            }
                                      }
                                      else{
                                          $reserved = 0;
                                          
                                          }
                                  }
                               		}
							else { 
                                                            $reserved = 0;
                                                }
?>
<html>
<head>
<title>ScanFlip | Campaign</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src="<?php echo ASSETS_JS ?>/a/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/c/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/c/fancybox/jquery.fancybox.css" media="screen" />
<script type="text/javascript" src="<?=ASSETS_JS?>/c/fancybox/jquery_for_popup.js"></script>
<!--<script type="text/javascript" src="<?=WEB_PATH?>/js/jquery-1.9.0.min.js"></script>-->

<script type="text/javascript" src="<?=ASSETS_JS?>/c/fancybox/jquery.fancybox-buttons.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/c/fancybox/jquery.fancybox.js"></script>
<style>
 
    .class_for_map{
      width:426px !important;
        height:345px !important;
    }
    .class_for_wrap{
        width:auto !important ;
    }
</style>
    </head>

<body class="campaignpage" >
<div id="wrong_file_data" style="display:none;width:400px;height:400px">
                        <?php
                        $sql_location="select * from campaign_location where campaign_id='".$_REQUEST['campaign_id']."' and offers_left>=1 and active=1";
                       
                        $RS = $objDB->Conn->Execute($sql_location);
                         if($RS->RecordCount()<=0){?>
                            
                            <form  action="search-deal.php" method="post" enctype="multipart/form-data">
                                  <div style="height:auto;text-align:center;padding:10px;font-size:15px;margin-right: 19px;width:400px;">Sorry currently all voucher codes of campaign are reserved by customers for all participating locations.
                                  Please browse Scanflip to find exclusive offer from other merchants near you.</div>
					<div style="margin-top:16px;margin-bottom: 10px;" align="center">
                                            
                                            <input type="submit" class="popupcancel" name="popupcancel" id="popupcancel" value="Cancel" />
                                        </div>
                        </form>
                         <?php }
                         
                  
                            
                        // }
                        ?>
					
                </div>
</body>
</html>
<script>
     jQuery(document).ready(function(){
	 var cookies_value="<?php if(isset($_COOKIE['is_scanflip_scan_qrcode']))echo $_COOKIE['is_scanflip_scan_qrcode'];?>";
	 if(cookies_value != "")
        {
       /*
        //alert(jQuery(".reserve_print").length);
        var cookies_value="<?php if(isset($_COOKIE['is_scanflip_scan_qrcode']))echo $_COOKIE['is_scanflip_scan_qrcode'];?>";
        
       var reserve = "<?php echo $reserved ; ?>";
     
        if(cookies_value != "")
        {
          
            var offerleft="<?php echo $o_left;?>";
            var locationid="<?php echo $_REQUEST['l_id'];?>";
            var campaignid="<?php echo $_REQUEST['campaign_id'];?>";
            var allofferleft="<?php echo $RS->RecordCount();?>";
           
            if(reserve == 0)            
            {
           if(offerleft == 0)
           {
             
			 if(allofferleft<=0)
			 {
                        
				  jQuery.fancybox({
		   		   content:jQuery('#wrong_file_data').html(),
                   // href:"<?=WEB_PATH?>/process.php?getMapForLocation=yes&locationid="+locationid+"&campaignid="+campaignid,
                    width: 435,
                    height: 345,
                   
                    openEffect : 'elastic',
                    openSpeed  : 300,
                    closeEffect : 'elastic',
                    closeSpeed  : 300,
                    // topRatio: 0,

                    changeFade : 'fast',  
					 beforeShow:function(){
						jQuery(".fancybox-inner").addClass("Class_for_activation");
					},
					afterClose: function () {
			         
                     window.location.href = "<?=WEB_PATH?>/search-deal.php";
            		},					
                    helpers:  {
                            overlay: {
                            opacity: 0.3
                            } // overlay
                    }
           
		});
			 }
			 else
			 {
                         
               jQuery.fancybox({
		   // content:jQuery('#wrong_file_data').html(),
                    href:"<?=WEB_PATH?>/process.php?getMapForLocation=yes&locationid="+locationid+"&campaignid="+campaignid,
                    width:500,
                    height :500,
                    type: 'iframe',
                    openEffect : 'elastic',
                    openSpeed  : 300,
                    closeEffect : 'elastic',
                    closeSpeed  : 300,
                    // topRatio: 0,
                    
                    changeFade : 'fast',  
					 beforeShow:function(){
					jQuery(".fancybox-inner").addClass("class_for_map");
                                        jQuery(".fancybox-wrap").addClass("class_for_wrap");
					},
                    helpers:  {
                            overlay: {
                            opacity: 0.3,
                            closeClick:false
                            } // overlay
                    },
                    enableEscapeButton : false,
                    keys:{
                close:null
            },
            showCloseButton: false,
            'afterShow': function() {
                $('.fancybox-close').attr('id','close');
                //override fancybox_close btn

             jQuery("#close").unbind("click");
               jQuery("#close").detach();
        }
           
		});
			 }
           }
           
    else{
	
        //window.location.href = "campaign.php?campaign_id="+campaignid+"&l_id="+locationid;
		window.location.href = "<?php echo $r_url; ?>";
    }
            
        }
         else{
			//window.location.href = "campaign.php?campaign_id="+campaignid+"&l_id="+locationid;
			window.location.href = "<?php echo $r_url; ?>";
    }
    //       del_cookie("is_scanflip_scan_qrcode");
        } */
         
		  jQuery.fancybox({
		   // content:jQuery('#wrong_file_data').html(),
                    href:"<?=WEB_PATH?>/process.php?getMapForLocation=yes&locationid=<?php echo $_REQUEST['l_id']; ?>&campaignid=<?php echo $_REQUEST['campaign_id']; ?>",
                    width:500,
                    height :500,	
                    type: 'iframe',
                    openEffect : 'elastic',
                    openSpeed  : 300,
                    closeEffect : 'elastic',
                    closeSpeed  : 300,
                    // topRatio: 0,
                    
                    changeFade : 'fast',  
					 beforeShow:function(){
					jQuery(".fancybox-inner").addClass("class_for_map");
                                        jQuery(".fancybox-wrap").addClass("class_for_wrap");
					},
                    helpers:  {
                            overlay: {
                            opacity: 0.3,
                            closeClick:false
                            } // overlay
                    },
                    enableEscapeButton : false,
                    keys:{
                close:null
            },
            showCloseButton: false,
            'afterShow': function() {
                $('.fancybox-close').attr('id','close');
                //override fancybox_close btn

             jQuery("#close").unbind("click");
               jQuery("#close").detach();
        }
           
		});
		}
		else
		{
			window.location.href = "<?php echo WEB_PATH."/search-deal.php"; ?>";
		}
      });
</script>




         
