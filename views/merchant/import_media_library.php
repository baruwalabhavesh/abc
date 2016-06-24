<?php

/**
 * @uses import media library
 * @used in pages :add-comapign.php,add-location.php,add-template.php,copy-campaign.php,customize-template.php,edit-comaign.php,edit-location.php,edit_template.php,merchant-marketing-template.php
 * @author Sangeeta Raghavani
 */

	//require_once("../classes/Config.Inc.php");
	//include_once(SERVER_PATH."/classes/DB.php");
	//$objDB = new DB('read');
	
	$merchant_id = $_REQUEST['mer_id'];
	$img_type = $_REQUEST['img_type'];
	
	$start_index =  $_REQUEST['start_index'];
	$num_of_records = 12;
	$next_index = $start_index + $num_of_records;
	
?>
<div id="mediablock" style="display: none;">
    <div class="mediablock_div">
		<div class='head_msg'>
            <?php 
				if($img_type=="campaign")
				{
					echo $merchant_msg["edit-compaign"]["Field_add_campaign_logo"];
				}
				if($img_type=="template")
				{
					echo $merchant_msg["templates"]["Field_add_campaign_logo"];
				}
				if($img_type=="store")
				{
					echo $merchant_msg["addlocation"]["Field_add_campaign_logo"];
				}
            ?>
		</div>
    
		<div class="main_content" > 	
                                                         
			<div class="message-box message-success" id="jqReviewHelpfulMessageNotification" >
				<!-- -->
				<div id="media-upload-header">
					<ul id="sidemenu">
						<li id="tab-type" class="tab_from_library">
							<a class="current" ><?php  echo  $merchant_msg["addlocation"]["Field_media_library"]; ?></a>
						</li>
					</ul>
				</div>
				<!-- -->
                                         
				<div style="clear: both" ></div>
				
				<div  class="div_from_computer">
					<div  style="padding-top:10px;padding-bottom:10px"><?php echo $merchant_msg["addlocation"]["Field_add_media_library"];?>	
					</div>
					<div style="clear: both" >
					</div>
					<div style="width: 100%;height: 168px;border: dashed 1px black;display: block;" align="center">
						<div style="padding-top:20px;">
						<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
							<div id="upload" >
								<span  >Upload Photo
								</span>
							</div>
						</div>
					</div>
					<div  align="center" style="padding-top:10px">
						<input class="save_btn" type="button" name="btn_save_from_computer" id="btn_save_from_computer" onclick="save_from_computer()"  value="<?php echo $merchant_msg['index']['btn_save'];?>"/>
					</div>
				</div>
							   
				<div  class="div_from_library">
					<div class="library_text" >
						<?php 
							if($img_type=="campaign")
							{
								echo $merchant_msg["edit-compaign"]["Field_add_campaign_logo_media_library"];
							}
							if($img_type=="template")
							{
								echo $merchant_msg["edit-compaign"]["Field_add_campaign_logo_media_library"];
							}
							if($img_type=="store")
							{
								echo $merchant_msg["addlocation"]["Field_add_campaign_logo_media_library"];
							}
						?>
					</div>
					<?php
					
						$flag = true;
						$merchant_array = array();
						$merchant_array['id'] = $merchant_id;
						$merchant_info = $objDB->Show("merchant_user",$merchant_array);
						//echo $merchant_info->fields['merchant_parent'] ;
						if($merchant_info->fields['merchant_parent'] != 0)
						{
							
							$media_acc_array = array();
							$media_acc_array['merchant_user_id'] = $merchant_id;
							$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array);
							$media_val = unserialize($RSmedia->fields['media_access']);
							if(in_array("view-use",$media_val))
							{
								$flag = true;
							}
							else{
								$flag = false;	
							}
						}
						else{
							$flag = true;
						}
						
						if($flag)
						{
	
					?>
						<div id="media_library_listing" >
							<div style="clear: both"></div>
							<ul class="ul_image_list" >
							<?php
								//$query = "select * from merchant_media where image_type='campaign' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'].") order by id desc" ;
								//$RSImages = $objDB->execute_query($query);
								 //if($RSImages->RecordCount()>0){
								//while($Row = $RSImages->FetchRow()){
								//echo WEB_PATH.'/merchant/process.php?btnGetLogosOfMerchant=yes&mer_id='.$_SESSION['merchant_id']."&img_type=campaign&mer_parent_id=".$merchant_info->fields['merchant_parent']; 
								
								
		
								//echo WEB_PATH."/merchant/process.php?btnGetLogosOfMerchant=yes&mer_id=".$merchant_id."&img_type=campaign&mer_parent_id=".$merchant_info->fields['merchant_parent']."&start_index=".$start_index."&num_of_records=".$num_of_records;										
								$arr123=file(WEB_PATH."/merchant/process.php?btnGetLogosOfMerchant=yes&mer_id=".$merchant_id."&img_type=".$img_type."&mer_parent_id=".$merchant_info->fields['merchant_parent']."&start_index=".$start_index."&num_of_records=".$num_of_records);
								
								//print_r($arr);
								if(trim($arr123[0]) == "")
								{
																			
									unset($arr123[0]);
									$arr123 = array_values($arr123);
																				
								}
								$json123 = json_decode($arr123[0]);
																		 
								$total_records123= $json123->total_records;
								
								$records_array123 = $json123->records;
								
								$total_images = $json123->total_images;
								
								//echo $json123->query_new;
								//echo $json123->query_old;
								
								if($total_records123>0)
								{
									foreach($records_array123 as $Row)
									{
								?>	
									<li class="li_image_list" id="li_img_<?=$Row->id;?>">
										<div>
											<?php
											
											$src="";
											if($img_type=="campaign")
											{
												$src = ASSETS_IMG ."/m/campaign/block/".$Row->image;												
											}
											if($img_type=="template")
											{
												$src = ASSETS_IMG ."/m/campaign/block/".$Row->image;
											}
											if($img_type=="store")
											{
												$src = ASSETS_IMG ."/m/location/mythumb/".$Row->image;
											}
											
											?>
											<img src="<?php echo $src;?>" />
											<span style="display:none;" class="vertical_top" id="span_img_text_<?=$Row->id;?>"><?=$Row->image?></span>
											<span style="display:none;" class="vertical_top cls_right"> Use this image&nbsp;<input type="radio" class='useradioclass' id='useradioid_<?=$Row->id?>' name="use_image" value="<?=$Row->id?>" /></span>
										</div>
										
									</li>
								<?php 
									}
								}
								?>
								
							</ul>
							<?php
								if($total_records123>0 && $total_images>$num_of_records)
								{
								?>
									<div id="mediya_showmore" style="display:block;">
										<input type="button" id="show_more_mediya_browse" name="show_more_mediya_browse" value="Show More" next_index="<?php echo $next_index ?>" num_of_records="<?php echo $num_of_records ?>" total_images="<?php echo $total_images ?>" />
									</div>
								<?php
								}			
								?>
						</div>
						<div  align="center" >
							   <input type="button" class="save_btn disabledmedia" disabled='disabled' name="btn_save_from_library" id="btn_save_from_library" onclick="save_from_library()"  value="<?php echo $merchant_msg['index']['btn_save'];?>"/>
							   <input type="button" class="cancel_btn" name="btn_cancel_from_library" id="btn_cancel_from_library" onclick="jQuery.fancybox.close();"  value="<?php echo $merchant_msg['index']['btn_cancel'];?>"/>
							</div>
					   </div>
					   <?php
							}
							else
							{
								?>
								<div  class="media_noaccess_msg">
									<?php echo $merchant_msg["addlocation"]["Msg_dont_access_images"];?>
								</div>
								<?php
							}
					   ?>
					</div>
			</div>
    </div>
</div>
