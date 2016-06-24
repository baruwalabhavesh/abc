<?php
/**
 * @uses display templates
 * @used in pages :customize-template.php.process.php,my-account-left.php
 * 
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
//$objDBWrt = new DB('write');
$array_where = array();

/********** Delete Template logic ***************/
if(isset($_REQUEST['action']))
{
    if($_REQUEST['action'] == "delete"){
            $array_where['id'] = $_REQUEST['id'];
            $objDBWrt->Remove("campaigns_template", $array_where);
            $_SESSION['msg']=$merchant_msg['templates']['Msg_template_deleted'];
            header("Location: ".WEB_PATH."/merchant/templates.php");
            exit();
    }
}
$order_by = " ORDER BY id DESC ";
//$RS = $objDB->Show("campaigns_template", $array_where, $order_by);
/****** get parent merchant id for employee ******/


if($_SESSION['merchant_info']['merchant_parent']!=0)
{
	$arr=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
	  unset($arr[0]);
	  $arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$main_merchant_id= $json->main_merchant_id;
}
else
{
	$main_merchant_id= $_SESSION['merchant_id'];
}


  /* * ******* get all main merchant of current login employee **************** */
if($_SESSION['merchant_info']['merchant_parent']!=0)
{  
	$arr=file(WEB_PATH.'/merchant/process.php?getallmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
	  unset($arr[0]);
	  $arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$all_main_merchant_id= $json->all_main_merchant_id;  
	$all_main_mer_array=explode(",",$all_main_merchant_id);
}
else
{
	$all_main_mer_array=array();
}


/****** Get All Templates  of merchant **************/

$start_index = 0;
$num_of_records = 8;
$next_index = $start_index + $num_of_records;

//echo WEB_PATH.'/merchant/process.php?btnGetAllTemplateOfMerchant_limit=yes&mer_id='.$_SESSION['merchant_id'].'&start_index='.$start_index.'&num_of_records='.$num_of_records;		
$arr=file(WEB_PATH.'/merchant/process.php?btnGetAllTemplateOfMerchant_limit=yes&mer_id='.$_SESSION['merchant_id'].'&start_index='.$start_index.'&num_of_records='.$num_of_records);
if(trim($arr[0]) == "")
{
	unset($arr[0]);
	$arr = array_values($arr);
}
$json = json_decode($arr[0]);
$total_records= $json->total_records;
$records_array = $json->records;
$total_templates = $json->total_templates;


/* * **** get employee role ( which role is assigned to user ) ************** */
$arr=file(WEB_PATH.'/merchant/process.php?btnGetMerchantRole=yes&mer_id='.$_SESSION['merchant_id']);
			if(trim($arr[0]) == "")
			{
				unset($arr[0]);
				$arr = array_values($arr);
			}
			$json = json_decode($arr[0]);
			$total_records1= $json->total_records;
			$records_array1 = $json->records;
	
                if($total_records1>0){
            
			foreach($records_array1 as $Row_role)
			{
				 $Row_role->merchant_user_id;
				 $ass_page = unserialize($Row_role->ass_page);
				 $ass_role = unserialize($Row_role->ass_role);
			 }
                    
		}
		$sort_permision_arr = array();
$n_arr = array();
$n_arr['bSortable'] = false;
array_push($sort_permision_arr, null);
$active_locations =1;
if($_SESSION['merchant_info']['merchant_parent'] == 0)
{
/* * ****** check if any active location is there or not if not then dont allow to add campaign  ***************** */

 $arr=file(WEB_PATH.'/merchant/process.php?is_any_active_location=yes&merchant_id='.$_SESSION['merchant_id']);
                                                    if(trim($arr[0]) == "")
                                                    {
                                                            unset($arr[0]);
                                                            $arr = array_values($arr);
                                                    }
                                                    $json = json_decode($arr[0]);
                                                    $active_locations = $json->active_locations;
}
//array_push($sort_permision_arr, null);

//	 if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-compaign.php",$ass_page) ){
//		array_push($sort_permision_arr, $n_arr);
//	 }
         /*
         echo $_SESSION['merchant_info']['merchant_parent'];
         echo "edit-".in_array("edit_template.php",$ass_page);
         echo "delete-".in_array("delete-template.php",$ass_page);
         */
//	 if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("edit_template.php",$ass_page) || in_array("delete-template.php",$ass_page) ){
		array_push($sort_permision_arr, $n_arr);
//	 }
         
          
         
         
	 /*
	 if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("delete-template.php",$ass_page)){
		array_push($sort_permision_arr, $n_arr);
	 }
          
     */
           
	/*
	$array = $json_array = array();
	$array1 = $json_array = array();
	$array['id'] =$_SESSION['merchant_id'];
	$RS = $objDB->Show("merchant_user",$array);
	$merchant_parent_value=$RS->fields['merchant_parent'];
	
	$array1['id'] = $merchant_parent_value;
	$RS1 = $objDB->Show("merchant_user",$array1);
	*/
	
	$merchant_parent_value = $_SESSION['merchant_info']['merchant_parent'];
	$array1['id'] = $merchant_parent_value;
	$RS1 = $objDB->Show("merchant_user",$array1);
		
	 
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Manage Templates</title>
<?php //require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
</head>

<body>

<div >
	
<!--<script src="<?php echo WEB_PATH ?>/js/jquery-1.7.2.min.js"></script>-->
<!-- load from CDN-->
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->

<style type="text/css" title="currentStyle">
			@import "<?php echo ASSETS_CSS ?>/m/demo_page.css";
			@import "<?php echo ASSETS_CSS ?>/m/demo_table.css";
		</style>
		
<!---start header---->
	<div>
		<?
		// include header file from merchant/template directory 
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">

	<div id="content">


 
  <div class="title_header"><?php echo $merchant_msg['templates']['manage_templates']; ?></div> 
  
	<div id="media-upload-header">
		<ul id="sidemenu">
			<li id="tab-type" class="div_instore_tab"><a class="current"><?php echo $merchant_msg['templates']['tab_instore_template']; ?></a></li>
			<!--<li id="tab-type" class="div_outstore_tab"><a ><?php echo $merchant_msg['templates']['tab_instore_template']; ?></a></li>-->
		</ul>
	</div>
	<div class="inner_tabdiv">
		<div id="div_instore_tab" class="tabs">
		
  <!-- <p>
			<?php echo $merchant_msg['templates']['Msg_template_static_text']; ?>
			
			
<!--      In order to edit template please select your template and then click edit from more action links.<br />
      You can simply use prepared template or your pre-designed template in order to create campaign by selecting <font color="#0066FF" style="font-size:1.2em" >Create campaign</font> link.<br /> Or you can also create template from scratch by selecting <font color="#0066FF" style="font-size:1.2em" >Add New Template</font> button. </p>
  <p></p> -->
  <?
					/** *** 
                  @ Add Template button Validation:
                 - If employee is login then check whether employee have permission to add Template.
                  
                 * **** */    
                        
		if($_SESSION['merchant_info']['merchant_parent'] == 0){
		?>
			<!--<div id="backdashboard">
						<!--		<a id="dashboard" href="<?//=WEB_PATH?>/merchant/my-account.php"><img src="<?//=ASSETS_IMG?>/m/back_dashboard.png" /></a>
							</div> -->
            <h4 align="right"  class="add_template"><a class="commonclass" link="add-template.php" href="javascript:void(0);"><?php echo $merchant_msg['templates']['add_new_template'];?></a></h4>
            <? }
			else
			{ ?>
				<!-- <div id="backdashboard" >
							<!--	<a id="dashboard" href="<?//=WEB_PATH?>/merchant/my-account.php"><img src="<?//=ASSETS_IMG?>/m/back_dashboard.png" /></a>
							</div> -->
		<?php	}
			?>
  <?php 
	 if($_SESSION['merchant_id'] != ""){
            //$Sql = "SELECT * from merchant_user_role where merchant_user_id =".$_SESSION['merchant_id'];
		//echo $Sql."<hr>";        
            //$RS_role = $objDB->Conn->Execute($Sql);
			$arr=file(WEB_PATH.'/merchant/process.php?btnGetMerchantRole=yes&mer_id='.$_SESSION['merchant_id']);
			if(trim($arr[0]) == "")
			{
				unset($arr[0]);
				$arr = array_values($arr);
			}
			$json = json_decode($arr[0]);
			$total_records1= $json->total_records;
			$records_array1 = $json->records;
	
            if($total_records1>0){
            
           foreach($records_array1 as $Row_role)
		   {
                    $Row_role->merchant_user_id;
                    $ass_page = unserialize($Row_role->ass_page);
                    $ass_role = unserialize($Row_role->ass_role);
		   }
                    foreach ($ass_page as $op_page)
                    {
                            if($op_page=="add-template.php"){ ?> 
							<!--<div id="backdashboard">
								<a id="dashboard" href="<?//=WEB_PATH?>/merchant/my-account.php"><img src="<?//=ASSETS_IMG?>/m/back_dashboard.png" /></a> 
							</div> -->
   <h4 align="right"  class="add_template"><a href="<?=WEB_PATH?>/merchant/add-template.php"><?php echo $merchant_msg['templates']['add_new_template'];?></a></h4>
	 <? }
                    }
                    foreach ($ass_role as $op_role)
                    {
                            $op_role;
                    }
		
            
        }
        else
        {
            echo "";
        }?>
	<? } 
       /*************** check whether number active campaign reached or not for current month ************/
    $arr_c=file(WEB_PATH.'/merchant/process.php?num_campaigns_per_month=yes&mer_id='.$_SESSION['merchant_id']);
                
	if(trim($arr_c[0]) == "")
	{
		unset($arr_c[0]);
		$arr_c = array_values($arr_c);
	}
	$json = json_decode($arr_c[0]);
        
	$total_campaigns= $json->total_records;
	$addcampaign_status = $json->status;
        $max_camapign = $json->max_campaigns;
     // $addcampaign_status = true;
        ?> 
		
	<div class="template_container" >
			
	<div class="lft_sid_cat_filter">
		<div id="categories">
			Categories Filter
		</div>
		<div id="allcategories">
			<span class="allcategories_cat fltr_selected" catid="0">
				All Categories
			</span>
			<?php
			$array_cat = array();
			$array_cat['active'] = 1;
			$RSCat = $objDB->Show("categories",$array_cat);
			while ($Row = $RSCat->FetchRow()) 
			{
			?>
				<span class="allcategories_cat" catid="<?php echo $Row['id'] ?>">
					<?php echo $Row['cat_name'] ?>
				</span>
			<?php
			}
			?>
		</div>
	</div>

	
	<div id="mediya_template_container" style="display:<?php if($total_records>0) echo 'inline-block';else echo 'none'; ?>">
		<?php
		//echo $RS->RecordCount();
		if($total_records==0)
		{			
		?>
			<div class='deletemsgclass deletemsgclass_media table_errore_message' >
				<?php
					echo $merchant_msg["templates"]["Msg_no_template"];
				?>
			</div>
			<div class="deletemsgclass_close" >
				X
			</div>
		<?php
		}
		else
		{
		?>
			<div class='deletemsgclass deletemsgclass_media table_errore_message' style="display:none;">				
			</div>
			<div class="deletemsgclass_close" style="display:none;">
				X
			</div>
		<?php
		}
		?>
		<?php
		if($total_records>0)
		{
			foreach($records_array as $Row)
			{
			?>
				<div class="mediya_template_blk" cat_id="<?php echo $Row->category_id ?>">
					<img src="<?php echo ASSETS_IMG."/m/campaign/".$Row->business_logo; ?>" class= "mediya_template_grid" />
					
					<div class="mediya_template_blk_Action">
						<?php
						if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-template.php",$ass_page) || in_array("delete-template.php",$ass_page) )
						{
							if($merchant_parent_value == 0)
							{
								if($_SESSION['merchant_info']['approve'] == 2)
								{
								}
								else
								{
									if($Row->is_default ==0)
									{	   
										if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("edit_template.php",$ass_page))
										{
										?>
											<a href="javascript:void(0)" id="temp_edit_<?=$Row->id?>" class="temp_edit temp_actions" title="Edit" link="edit_template.php?id=<?=$Row->id?>">
												<img src="<?php echo ASSETS_IMG?>/m/template_edit.png">
											</a>
										<?php
										}
										if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("delete-template.php",$ass_page) )
										{
										?>
											<a href="javascript:void(0)" id="temp_delete_<?=$Row->id?>" class="temp_delete temp_actions" title="Delete" link="templates.php?id=<?=$Row->id?>&action=delete">
												<img src="<?php echo ASSETS_IMG?>/m/mediya_delete.png">
											</a>
										<?php
										}

										if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-compaign.php",$ass_page) )
										{
											if($active_locations>0) 
											{
												if($addcampaign_status=="true") 
												{	
												?>
													<a href="javascript:void(0)" id="temp_create_<?=$Row->id?>" class="temp_create temp_actions" title="Create Campaign" link="add-compaign.php?template_id=<?=$Row->id?>">
														<img src="<?php echo ASSETS_IMG?>/m/add_image_to_lbry.png">
													</a>
												<?php
												}
											}
										}
									}
									else
									{
										?>
										<a href="javascript:void(0)" id="temp_customize_<?=$Row->id?>" class="temp_customize temp_actions" title="Customize" link="customize-template.php?id=<?=$Row->id?>">
											<img src="<?php echo ASSETS_IMG?>/m/template_customize.png">
										</a>
										<?php
										if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-compaign.php",$ass_page) )
										{
											if($active_locations>0) 
											{
												if($addcampaign_status=="true") 
												{	
												?>
													<a href="javascript:void(0)" id="temp_create_<?=$Row->id?>" class="temp_create temp_actions" title="Create Campaign" link="add-compaign.php?template_id=<?=$Row->id?>">
														<img src="<?php echo ASSETS_IMG?>/m/add_image_to_lbry.png">
													</a>
												<?php
												}
											}
										}
									}
								}
							}
							else
							{
								if($RS1->fields['approve'] == 2)
							    { 
							    } 
							    else
							    {
									if($Row->is_default ==0)
									{
										if (in_array($Row->created_by, $all_main_mer_array)) 
									    {
										?>
											<a href="javascript:void(0)" id="temp_customize_<?=$Row->id?>" class="temp_customize temp_actions" title="Customize" link="customize-template.php?id=<?=$Row->id?>">
												<img src="<?php echo ASSETS_IMG?>/m/template_customize.png">
											</a>
											<?php	
											if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-compaign.php",$ass_page) )
											{
												if($active_locations>0) 
												{
													if($addcampaign_status=="true") 
													{	
													?>
														<a href="javascript:void(0)" id="temp_create_<?=$Row->id?>" class="temp_create temp_actions" title="Create Campaign" link="add-compaign.php?template_id=<?=$Row->id?>">
															<img src="<?php echo ASSETS_IMG?>/m/add_image_to_lbry.png">
														</a>
													<?php
													}
												}
											}
										}
										else
										{
											if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("edit_template.php",$ass_page))
											{
											?>
												<a href="javascript:void(0)" id="temp_edit_<?=$Row->id?>" class="temp_edit temp_actions" title="Edit" link="edit_template.php?id=<?=$Row->id?>">
													<img src="<?php echo ASSETS_IMG?>/m/template_edit.png">
												</a>
											<?php
											}
											if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("delete-template.php",$ass_page) )
											{
											?>
												<a href="javascript:void(0)" id="temp_delete_<?=$Row->id?>" class="temp_delete temp_actions" title="Delete" link="templates.php?id=<?=$Row->id?>&action=delete">
													<img src="<?php echo ASSETS_IMG?>/m/mediya_delete.png">
												</a>
											<?php
											}

											if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-compaign.php",$ass_page) )
											{
												if($active_locations>0) 
												{
													if($addcampaign_status=="true") 
													{	
													?>
														<a href="javascript:void(0)" id="temp_create_<?=$Row->id?>" class="temp_create temp_actions" title="Create Campaign" link="add-compaign.php?template_id=<?=$Row->id?>">
															<img src="<?php echo ASSETS_IMG?>/m/add_image_to_lbry.png">
														</a>
													<?php
													}
												}
											}
											if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-template.php",$ass_page) )
											{
												?>
												<a href="javascript:void(0)" id="temp_customize_<?=$Row->id?>" class="temp_customize temp_actions" title="Customize" link="customize-template.php?id=<?=$Row->id?>">
													<img src="<?php echo ASSETS_IMG?>/m/template_customize.png">
												</a>
												<?php
											}	
										}
									}
									else
								    {
										?>
										<a href="javascript:void(0)" id="temp_customize_<?=$Row->id?>" class="temp_customize temp_actions" title="Customize" link="customize-template.php?id=<?=$Row->id?>">
											<img src="<?php echo ASSETS_IMG?>/m/template_customize.png">
										</a>
										<?php
									}
								}
							}
						}
						else
						{
							if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-compaign.php",$ass_page) )
							{
								if($active_locations>0) 
								{
									if($addcampaign_status=="true") 
									{	
									?>
										<a href="javascript:void(0)" id="temp_create_<?=$Row->id?>" class="temp_create temp_actions" title="Create Campaign" link="add-compaign.php?template_id=<?=$Row->id?>">
											<img src="<?php echo ASSETS_IMG?>/m/add_image_to_lbry.png">
										</a>
									<?php
									}
								}
							}
						} 
					?>
					</div>
					<span id="media_template_name" title="<?php echo $Row->title; ?>">
					<?php
						echo $Row->title;
					?>
					</span>
				</div>
			<?php
			}
		}
	?>
	</div>
	<?php
	if($total_records>0 && $total_templates>$num_of_records)
	{
	?>
		<div id="mediya_template_showmore" style="display:block;">
			<input type="button" id="show_more_template_mediya" name="show_more_template_mediya" value="Show More" next_index="<?php echo $next_index ?>" num_of_records="<?php echo $num_of_records ?>" total_images="<?php echo $total_templates ?>" />
		</div>
	<?php
	}			
	?>
	
	
</div>
	
	</div><!-- div_instore_tab -->
	
	<div id="div_outstore_tab" class="tabs" style="display: none;">
	</div>
	
	<div class="clear">&nbsp;</div>
	
	</div><!-- inner_tabdiv -->
	
<!--end of content--></div>
<!--end of contentContainer--></div>

<!---------start footer--------------->
       <div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>
		<!--end of footer--></div>
	
</div>
<!--// 369-->
<?
$_SESSION['msg'] = "";
?>
<!--// 369-->
<div id="dialog-message" title="Message Box" style="display:none">

    </div>
	
<div id="message-window" title="Message Box" style="display:none">

    </div>
</body>

<div style="display: none;" class="container_popup" id="NotificationLoadingDataPopUpContainer">
    <div class="divBack" id="NotificationLoadingDataBackDiv" style="display: block;">
    </div>
    <div style="display: block;" class="Processing" id="NotificationLoadingDataFrontDivProcessing">

        <div align="center" style="left: 45%;top: 40%;" class="textDivLoading" valign="middle" id="NotificationLoadingDataMaindivLoading">

            <div style="height:auto;width:auto" class="loading innerContainer" id="NotificationLoadingDatamainContainer">
				Loading ...
            </div>
        </div>
    </div>
</div>

</html>
<script>
	
jQuery("#sidemenu li#tab-type a").click(function () {

	jQuery("#sidemenu li a").each(function () {
		jQuery(this).removeClass("current");
	});
	jQuery(this).addClass("current");
	var cls = jQuery(this).parent().attr("class");
	jQuery(".tabs").each(function () {
		jQuery(this).css("display", "none");
	});
	//draw_location_chart();

	jQuery('#' + cls).css("display", "block");
});

jQuery("a[id^='temp_delete_']").live("click",function(){
	
	var flag=0;
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'loginornot=true',
		async:false,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				flag=1;
			}
		}
	});
	
	if(flag == 1)
	{
		return false;
	}
	else
	{
		
		var cur_div = jQuery(this);	
		var arr = jQuery(this).attr("id").split("_");
		var cid= arr[2];
	   
		var msg = "<div style='width: 252px;'>Are you sure you want to delete template ?</div>";
		var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
		var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
		var footer_msg="<div style='text-align:center'><hr><input type='button' cur_div_id='"+cur_div.attr('id')+"' link='<?php echo WEB_PATH?>/merchant/process.php?id=" + cid +"&btnDeleteTemplate=yes' value='Yes' id='popupyes' name='popupyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'>&nbsp;&nbsp;&nbsp;<input type='button'  value='No' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
		
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		
		jQuery.fancybox({
				content:jQuery('#dialog-message').html(),
				type: 'html',
				openSpeed  : 300,
				closeSpeed  : 300,
				// topRatio: 0,
				changeFade : 'fast',  
				beforeShow : function(){
					jQuery(".fancybox-inner").addClass("msgClass");
				},
				helpers: {
						overlay: {
						opacity: 0.3
						} // overlay
				}
		});  
    }
});

jQuery(".fancybox-inner #popupyes").live("click",function(){
	
	jQuery.fancybox.close(); 
	
	var linkurl = jQuery(this).attr("link");
	var cur_div_id = jQuery(this).attr("cur_div_id");
	var cur_div = jQuery("#"+cur_div_id);
	
	//alert(jQuery("#"+cur_div_id).parent().attr("id"));
	open_popup("NotificationLoadingData");
	jQuery.ajax({
	  type: "POST",
	  url: linkurl,
	  async : false,
	  success: function(msg) {
		//alert(msg); 
		if(msg=="success")
		{
			cur_div.parent().parent().remove();
			 
			jQuery(".deletemsgclass").html("<?php echo $merchant_msg["templates"]['Msg_template_deleted']; ?>");
			jQuery(".deletemsgclass").css("display","block");
			jQuery(".deletemsgclass_close").css("display","block");
			 
		}
		
	  }
	}); 
	close_popup("NotificationLoadingData");
});

jQuery("a[id^='temp_edit_']").live("click",function(){
	
	var flag=0;
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'loginornot=true',
		async:false,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				flag=1;
			}
		}
	});
	
	if(flag == 1)
	{
		return false;
	}
	else
	{
	   var edit_link = jQuery(this).attr("link");
	   window.location.href = edit_link;
   }
});

jQuery("a[id^='temp_customize_']").live("click",function(){
	
	var flag=0;
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'loginornot=true',
		async:false,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				flag=1;
			}
		}
	});
	
	if(flag == 1)
	{
		return false;
	}
	else
	{
	   var edit_link = jQuery(this).attr("link");
	   window.location.href = edit_link;
   }
});

jQuery("a[id^='temp_create_']").live("click",function(){
	
	var flag=0;
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'loginornot=true',
		async:false,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				flag=1;
			}
		}
	});
	
	if(flag == 1)
	{
		return false;
	}
	else
	{
	   var edit_link = jQuery(this).attr("link");
	   window.location.href = edit_link;
   }
});


function close_popup(popup_name)
{

	$("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
		$("#" + popup_name + "BackDiv").fadeOut(200, function () {
			$("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				$("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
			});
		});
	});

}
function open_popup(popup_name)
{


	$("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		$("#" + popup_name + "BackDiv").fadeIn(200, function () {
			$("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         

			});
		});
	});


}
    	
jQuery(".deletemsgclass_close").click(function(){

	jQuery(".deletemsgclass").html("");
	jQuery(".deletemsgclass").css("display","none");
	jQuery(".deletemsgclass_close").css("display","none");
});

jQuery(".allcategories_cat").click(function(){
	
	var img_filter = jQuery(this).attr('catid');
	//alert(img_filter);
	
	jQuery("div#allcategories span").removeClass("fltr_selected");
	
	if(img_filter=="0")
	{
		jQuery(this).addClass("fltr_selected");
		jQuery(".mediya_template_blk").css("display","block");
	}
	else
	{
		jQuery(this).addClass("fltr_selected");
		jQuery(".mediya_template_blk").css("display","none");	
		jQuery(".mediya_template_blk[cat_id="+img_filter+"]").css("display","block");
	}
	
	if(jQuery("#mediya_template_container .mediya_template_blk:visible").length==0)
	{
		jQuery(".deletemsgclass").html("<?php echo $merchant_msg["templates"]['Msg_no_template_filter']; ?>");
		jQuery(".deletemsgclass").css("display","block");
		jQuery(".deletemsgclass_close").css("display","block");
	}
	else
	{
		jQuery(".deletemsgclass").html("");	
		jQuery(".deletemsgclass").css("display","none");
		jQuery(".deletemsgclass_close").css("display","none");
	}
});


jQuery("#show_more_template_mediya").live("click",function(){
	open_popup("NotificationLoadingData");
	var flag=0;
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'loginornot=true',
		async:false,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				flag=1;
			}
		}
	});
	
	if(flag == 1)
	{
		return false;
	}
	else
	{
		var img_filter = jQuery(".fltr_selected").attr('catid');
		//alert(img_filter);
		
		var cur_el= jQuery(this);
		var next_index = parseInt(jQuery(this).attr('next_index'));
		var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'show_more_template_media=yes&next_index='+next_index+'&num_of_records='+num_of_records,
			async:true,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);

				if (obj.status=="true")     
				{
					jQuery("#mediya_template_container").append(obj.html);
					cur_el.attr('next_index',next_index + num_of_records);
					
					if(parseInt(obj.records_return)<num_of_records)
					{
						
						cur_el.css("display","none");
					}
					//alert(img_filter);
					if(img_filter=="0")
					{
						jQuery(".mediya_template_blk").css("display","block");	
					}
					else
					{
						jQuery(".mediya_template_blk").css("display","none");	
						jQuery(".mediya_template_blk[cat_id="+img_filter+"]").css("display","block");
					}					

				}
			}
		});
	}
	close_popup("NotificationLoadingData");	
});

	
    jQuery(document).ready(function(){
        jQuery(".addcampaign_chk").click(function(){
         var msg = "At present you can only add "+<?php echo $max_camapign; ?>+" campaign per month.<br /> Please contact scanflip sales team if you wish to increase number of campaigns for your account."; 
         jQuery("#error_message").text('');
        //jQuery("#error_message").text(msg);
	    var head_msg="<div class='head_msg'>Message Box</div>"
		var content_msg="<div class='content_msg'>"+msg+"</div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		jQuery.fancybox({
					content:jQuery('#dialog-message').html(),
					type: 'html',
					openSpeed  : 300,
					closeSpeed  : 300,
					changeFade : 'fast',  
					helpers: {
						overlay: {
						opacity: 0.3
						} // overlay
					}
		});
        });
	
	jQuery("#popupcancel").live("click",function(){
		jQuery.fancybox.close(); 
	    return false; 
	 });
	
    });



var merchant_parent_value="<?php echo $merchant_parent_value; ?>";
var main_merchant_approve="<?php echo $_SESSION['merchant_info']['approve'];?>";

var sub_merchant_approve="<?php echo $RS1->fields['approve'];?>";

if(merchant_parent_value == 0)
	 {
		if(main_merchant_approve == 2)
		{
			//alert("first");
			jQuery(".add_template").hide();
			
	        }
		
	}
	else
	{
			if(sub_merchant_approve == 2)
			{
				//alert("second");
				jQuery(".add_template").hide();
				
			}		
	}
/******* Close More action div block click any where in the window if block open *****/
$("body").click
(
  function(e)
  {
   
    if((e.target.className).trim() == "actiontd")
    {
    
    }
    else
        {
            $('.actiontd').find('#actiondiv').slideUp("slow");
        }
  }
);
/******* Open More action div block on click more action link *****/
$('.actiontd').click(function(){
  
    if($(this).find('#actiondiv').css('display') == 'none')
     {
          
        $('.actiondivclass').css("display","none");
        //$(this).find('#actiondiv').css('display','block');
         $(this).find('#actiondiv').slideDown("slow");
     }
    else
        $(this).find('#actiondiv').slideUp('slow');
  /*
},function(){
   $(this).find('#actiondiv').css('display','none');  */
});

/*********** before perform any action check whether merchant current session is active or not *********/
$('.commonclass').click(function(){
     var linkurl=jQuery(this).attr("link");
        
                jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'loginornot=true',
                          async:false,
                           success:function(msg)
                           {
                               
                             
                                 var obj = jQuery.parseJSON(msg);
                               
                                 
                                if (obj.status=="false")     
                                {

                                    window.location.href=obj.link;
                                }
                                else
                                {


                                         window.location.href = "<?=WEB_PATH?>/merchant/"+linkurl;

                                }
                           }
                        });
                     });
function show_error(message)
{
    var alert_msg = message;
        var head_msg="<div class='head_msg'>Message</div>";
        var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
	var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
        jQuery( "#message-window" ).html(head_msg + content_msg + footer_msg);
        jQuery.fancybox({
				content:jQuery('#message-window').html(),
				
				type: 'html',
				
				openSpeed  : 300,
                                
				closeSpeed  : 300,
				// topRatio: 0,
                
                                changeFade : 'fast',  
				
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
        });
      //  alert("There is no Package assigned to you. Please contact Scanflip account executive for your package subscription.");
        return false;
   // alert(message);
}

</script>
