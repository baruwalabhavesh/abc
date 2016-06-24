<?php
/******** 
@USE : print coupon
@PARAMETER : 
@RETURN : 
@USED IN PAGES : search-deal.php,mymerchants.php,my-deals.php,print_coupon.php,location_detail.php
*********/
//require_once("Config.Inc.php");
//include_once(ROOT."/configs/DB.php");
//$objDB = new DB();


 if(isset($_REQUEST["load_print_coupon_data1"]))
 {
     
    $cid=$_REQUEST["cid"];
    $lid=$_REQUEST["lid"];  
    $br=$_REQUEST["barcodea"];
    $optmulity=1; 
	   //$objDB = new DB();
         $where_clause = array();
		$where_clause['id'] = $cid;
		$RSCompDetails = $objDB->Show("campaigns", $where_clause);
              
                $where_clause_l = array();
                $where_clause_l['id'] = $lid;
                $loc_nm = $objDB->Show("locations",$where_clause_l);
                
               
                
                $title = $RSCompDetails->fields['title'];
                $expdate = $RSCompDetails->fields['expiration_date'];
                $expdate=date("m/d/y g:i:s A",strtotime($expdate));
                $discont = $RSCompDetails->fields['discount'];
                //$desc = $RSCompDetails->fields['deal_detail_description'];
                $desc = $RSCompDetails->fields['description'];
				$terms=$RSCompDetails->fields['terms_condition'];
                $rpoint = $RSCompDetails->fields['redeem_rewards'];
                $timzone = $RSCompDetails->fields['timezone'];
                
                $where_x = array();
		$where_x['campaign_id'] = $cid;
		$CodeDetails = $objDB->Show("activation_codes", $where_x);
                $act_code = $CodeDetails->fields['activation_code'];
                
                $where_clause_m = array();
                $where_clause_m['id'] = $RSCompDetails->fields['created_by'];
                $merchant_user = $objDB->Show("merchant_user",$where_clause_m);
                
                $merchant_icon = $merchant_user->fields['merchant_icon'];
                
?>
<div id="coupon_div_<?php echo $cid ?>_<?php echo $lid; ?>" c_id="chk_<?=$cid?>_<?php echo $br;?>_<?php echo $lid ?>"  style="display:none;margin-bottom:280px;-webkit-print-color-adjust:exact;<?php if($optmulity==1) { echo "display:none"; } ?> ">
<!--<div id="coupon_div" style="-webkit-print-color-adjust:exact; ">-->
    
		<div style="border: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px; border-bottom: none;" class="c_div">            
            <div style="padding: 5px; font-weight: bold; font-size: 18px; color: rgb(255, 255, 255); background: none repeat scroll 0px 0px rgb(35, 30, 30);height:15px;" class="c_title">
<!--                <span style="text-transform:uppercase"><?=$title;?></span>-->
                <span style="float: right; font-weight: normal; font-size: 15px;font-weight: bold" class="expr_div">Expiration Date :- <?=$expdate;?></span>
            </div>
             <div style="overflow:hidden;margin:10px 0 0;float:left;width:45%;position:relative;">            
                <div style="float:left;">
                    
                    <div style="overflow:hidden;border: 1px solid white;">
                        <?php
                        $img_src="";
						
                        $where_clause123 = array();
                        $where_clause123['id'] = $loc_nm->fields['created_by'];
                        $RSMer = $objDB->Show("merchant_user", $where_clause123);
						
                        if($RSMer->fields['merchant_icon']!="")
                        {
                            $img_src=ASSETS_IMG."/m/icon/".$RSMer->fields['merchant_icon']; 
                        }
                        else
                        {
                            $img_src=ASSETS_IMG."/c/Merchant.png";
                        }
                        $size = getimagesize($img_src);
						$height=$size[1];
						if($height>=111)
						{
                        ?>
							<img src="<?php echo $img_src ?>" style="width:144px;height:111px;border: 4px solid #fff;"/>
						<?php
						}
						else
						{
						?>
							<img src="<?php echo $img_src ?>" style="width:144px;border: 4px solid #fff;"/>
						<?php
						}
						?>
                    </div>
                    <div style="text-overflow:ellipsis;white-space: nowrap;width:350px; overflow:hidden;padding:7px 0 0;font:400 16px/18px Arial;text-decoration:none;"><?php
                     // for business name 
                                                        $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='. $lid);
                                                                        if(trim($arr[0]) == "")
                                                                             {
                                                                                     unset($arr[0]);
                                                                                     $arr = array_values($arr);
                                                                             }
                                                                             $json = json_decode($arr[0]);
                                                                        $busines_name  = $json->bus_name;
                                                                        echo $busines_name;
                                                        // for business name 
                ?></div>
                </div> 
				
					  
				
            </div>
             <div style="width: 53%;font-size: 15px;color: #333;text-align: justify;font-weight: 400;margin-top: 13px;height: inherit;margin-left: 377px;border: 1px dotted #f7f7f7">
						<span>	<?php
							echo $title;
						?>
					
						
						</span>
					<div style="margin-top: 20px;text-align: center;">
						<img id="image_barcode_<?php echo $cid ?>_<?php echo $lid ?>" src="<?=WEB_PATH?>/showbarcode.php?br=<?php echo $br; ?>" alt="barcode" />
					</div>
							<script>function printpage(){ window.print() }
							function save_image() { window.open("<?=WEB_PATH?>"+"/showbarcode.php?br="+"<?php echo $br; ?>");}
							</script>
							<!--// b--> 
								<!--		<input type="submit" value="Save Barcode Image" onclick="save_image()" />
								<input type="submit" value="Print this page" onclick="printpage()" /><br/><br/>-->
							<!--// b-->
				</div>
        </div>
    
    
		<?php 
        $str ="";
        if($RSCompDetails->fields['number_of_use']==1){ 
         $str = "* Limit one per customer, Not valid with any other offer" ;
        }
        elseif($RSCompDetails->fields['number_of_use']==2)
        {
           $str = "* Limit one per customer per day , not valid with any other offer";
        } 
        elseif($RSCompDetails->fields['number_of_use']==3)
        {
           $str = "* Earn Redemption Points On Every Visit";
        }                 
       $arr=file(WEB_PATH.'/process.php?get_location_name_of_campaigns=yes&camp_id='.$cid);
       if(trim($arr[0]) == "")
        {
            unset($arr[0]);
            $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
       $locations = $json->records;
       $t_records = $json->total_records;
       $cnt = 1;
     if($t_records > 1 || $str!= "")
     { ?>
         <div style="border-left: 1px solid rgb(0, 0, 0);  border-right: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px; border-top:1px solid #ccc;border-bottom: none;">
         <?php                  
     }
     
  if($t_records > 1){
                    ?>
        <div style="display:none;"> You can find this deal available on following location also.
            <?php 
              foreach($locations as $Rownew)
               {
                 if($Rownew->id != $lid){
                     $address = $Rownew->address.", ".$Rownew->city.", ".$Rownew->state.", ".$Rownew->zip.", ".$Rownew->country ;
                     ?>
            <p style="margin-bottom: 8px;margin-top: 8px;  padding: 0px !important; padding-left: 20px !important;">
                     <?php //echo $Rownew->location_name?>
                <?php
                     // for business name 
                                                        $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='. $Rownew->id);
                                                                        if(trim($arr[0]) == "")
                                                                             {
                                                                                     unset($arr[0]);
                                                                                     $arr = array_values($arr);
                                                                             }
                                                                             $json = json_decode($arr[0]);
                                                                        $busines_name  = $json->bus_name;
                                                                        echo $busines_name;
                                                        // for business name 
                ?>
                &nbsp;<span>(<?=$address?>)</span></p>
        <?php
                 }
               }
            ?>
        
            
        </div>   
        <?php }
        if($t_records > 1 || $str!= "")
 {
            $str1="<div style=' float:left;'>Please advice crew member of offer prior to ordering  </div>";
        echo "<div style=' font-size: 15px; width:100%;'>";
                    echo $str1;
                    echo "<div style=' float:right;'>".$str."</div>";
                    echo "</div>";
 }
                    if($t_records > 1 || $str!= "")
 { ?>
     </div>
     <?php                  
 } ?>
   
	
            <div style="  border: 1px solid rgb(0, 0, 0);  padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px;text-align:left;">
                <span style="float:left;display:block;line-height:18px;">Where to Redeem :- </span><!--<span class="loca_name" style="font-weight:bold; padding-left: 18px">
                <?php //echo $loc_nm->fields['location_name'];?>
                    <?php
                     // for business name 
                                                        $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='. $loc_nm->fields['id']);
                                                                        if(trim($arr[0]) == "")
                                                                             {
                                                                                     unset($arr[0]);
                                                                                     $arr = array_values($arr);
                                                                             }
                                                                             $json = json_decode($arr[0]);
                                                                        $busines_name  = $json->bus_name;
                                                                        //echo $busines_name;
                                                        // for business name 
                ?>
                </span>-->
                                            <span style="padding-left:10px;max-width:228px;float:left;display:block;line-height:18px;">
                                                                        <?php //echo $loc_nm->fields['address'];?>
                                                <?php $address = $loc_nm->fields['address'].", ".$loc_nm->fields['city'].", ".$loc_nm->fields['state'].", ".$loc_nm->fields['zip'].", ".$loc_nm->fields['country']; ?>
                                                <?php echo $address; ?>
                                            </span>
<!--                                            <span  style="padding-left:10px;float:left;display:block;line-height:18px;"><?=$loc_nm->fields['phone_number'];?></span>-->
                <span  style="padding-left:10px;float:left;display:block;line-height:18px;">Ph# (<?= substr($loc_nm->fields['phone_number'],4,3);?>) <?=substr($loc_nm->fields['phone_number'],8);?></span>
                                            <span style="float:left;display:block;line-height:18px;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                 <span style="float:left;display:block;line-height:18px;">Redemption point :- </span><span style="font-weight: bold; padding-left: 10px;float:left;display:block;line-height:18px;"><?=$rpoint;?></span><span class="contents_location" style="float:left;display:block;line-height:18px;">&nbsp;&nbsp;|&nbsp;&nbsp;</span> 
                <div style="float:right;overflow: hidden;height:18px;"><img style="max-width:85px;" src="<?php echo ASSETS_IMG."/c/header-logo.jpg"; ?>" alt="Scanflip"/></div>
            </div>
<!--            <div class="c_last_div" style=" border: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px;text-align:right;">
                Participating Location :- <div class="straddress">
                            <div class="strname" style="font-weight: bold;"><?=$loc_nm->fields['location_name'];?></div>
                            <div class="straddress" style="font-weight: bold;"><?=$loc_nm->fields['address'];?></div>
                            <div class="straphone" style="font-weight: bold;"><?=$loc_nm->fields['phone_number'];?></div>
                        </div>&nbsp;&nbsp;|&nbsp;&nbsp; 
                Redemption point :- <span style="font-weight: bold;"><?=$rpoint;?></span>&nbsp;&nbsp;|&nbsp;&nbsp; 
                <span style="font-weight: bold;">Scanflip merchant</span>
            </div>-->
			<?php 
				if($desc!="")
				{
			?>
				<div style="font-size:13px;margin:0 auto;padding:10px !important;text-align:left;border:1px solid black;width:820px;margin-top:10px;">
					<div style="font-weight: bold;">
						Campaign Description : 
					</div> 
					<?php
						echo $desc;
					?>
				</div>
			<?php
				}
			?>			
            <div style="font-size:13px;margin:0 auto;padding:10px;text-align:left;border:1px solid black;width:820px;margin-top:10px;">
                   <div style="font-weight: bold;">
           Terms & Condition :
       </div> <?php
                    if($terms!="")
					{
								echo $terms."<p>Additional Terms</p><p>
												No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.
												</p>";
					}
					else
					{
						echo "<p>No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.
												</p>";
					}
                    ?>
                </div> 
</div>
<?php
}

//echo "hi";
?>
