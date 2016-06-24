<?php 
check_merchant_session();
$array_where_act['active']=1;
$RSCat = $objDB->Show("loyaltycard_category",$array_where_act);

$mer_info = array();
$mer_info['id'] = $_SESSION['merchant_id'];
$RS_mer_info = $objDB->Show("merchant_user",$mer_info);

$RS_stamp_image = $objDB->Show("stamp_image_type",'',' order by id asc');

$pack_data = array();
$pack_data['merchant_id'] = $_SESSION['merchant_id'];
$get_pack_data = $objDB->Show("merchant_billing",$pack_data);

$pack_data1 = array();
$pack_data1['id'] = $get_pack_data->fields['pack_id'];
$get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);
//echo "active_loyalty_cards = ".$get_billing_pack_data->fields['active_loyalty_cards']."<br/>";
//echo "transaction_fees_stamp = ".$get_billing_pack_data->fields['transaction_fees_stamp']."<br/>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>ScanFlip | Manage Cards</title>
	<?php require_once(MRCH_LAYOUT."/head.php"); ?>
	<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=ASSETS?>/loyalty/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=ASSETS?>/loyalty/css/reset.css">
	<link rel="stylesheet" href="<?=ASSETS?>/loyalty/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?=ASSETS?>/loyalty/css/jquery.dataTables.css">
	<link rel="stylesheet" href="<?=ASSETS?>/loyalty/css/cards_design.css">
	<!--<script src="<?=ASSETS?>/loyalty/js/jquery.js"></script>-->
	<script src="<?=ASSETS?>/loyalty/js/bootstrap.min.js"></script>
	<script src="<?=ASSETS?>/loyalty/js/jscolor.js"></script>
	<script language="javascript" src="<?=ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
  
</head>
<body>
	
	<!---------start header--------------->	
	
	<div>
	<?
	// include header file from merchant/template directory 
	require_once(MRCH_LAYOUT."/header.php");
	?>
	<!--end header-->
	</div>
	<div id="contentContainer">
		
	<div id="content">	
		
  <div id="notification">Saved Successfully</div>
  <div class="maincontainer">
    <div class="notification notification-card-volume">Card volume should be numeric and greater than 0</div>
    <div class="notification notification-reward-text">Reward text cannot be empty </div>
    <div class="notification notification-reward-per-visit">Reward per visit can be numeric only</div>
    <div class="notification notification-additional-points">Additional reward points can be numeric only</div>

    <div class="card_details_box">
       <?php 
       $id = $_POST['card_id'];
        if(isset($id)){

          //include 'dbconnect.php';

            /*$sql='SELECT * FROM cards where id =?';
            $rs=$conn->Execute($sql,$id);*/
             $rs=$objDB->Conn->Execute('SELECT * FROM merchant_loyalty_card where id =?',array($id)); 
            if($rs === false) {
              trigger_error(' Error: ' . $objDB->ErrorMsg(), E_USER_ERROR);
            } else {
              $rows_returned = $rs->RecordCount();
              $rs->MoveFirst();
              while (!$rs->EOF) {
                //loads all the data fields into variables for repopulating
                $card_title = $rs->fields['title']; 
                $card_category = $rs->fields['card_category']; 
                $card_type = $rs->fields['card_type']; 
                $stamp_image_type_id = $rs->fields['stamp_image_type_id']; 
                $card_text_color = $rs->fields['card_text_color']; 
                $card_background = $rs->fields['card_background']; 
                $stamps_per_card = $rs->fields['stamps_per_card']; 
                $reward_per_visit = $rs->fields['reward_per_visit']; 
                
                
                //$participating_locations = $rs->fields['participating_locations']; 
                
                $sql_loy_loc = 'SELECT location_id FROM loyaltycard_location where loyalty_card_id ='.$id;
				$rs_loy_loc = $objDB->Conn->Execute($sql_loy_loc);
				while($Row_loy_loc = $rs_loy_loc->FetchRow())
				{
					$participating_locations .= $Row_loy_loc['location_id'].",";			
				}
                $participating_locations = rtrim($participating_locations, ",");
                
                $card_volume = $rs->fields['card_volume']; 
                $redeemption_limit = $rs->fields['redeemption_limit']; 
                $punch_type = $rs->fields['punch_type']; 
                $card_type = $rs->fields['card_type']; 
                $card_status = $rs->fields['card_status']; 
                $cards_left = $rs->fields['cards_left']; 
                $terms_conditions = $rs->fields['terms_conditions']; 
                 $card_keyword = $rs->fields['card_keyword']; 
                
                
                
                // get data from reward_card table
                
                $rs1=$objDB->Conn->Execute('SELECT * FROM merchant_loyalty_reward_card where loyalty_card_id =?',array($id)); 
				if($rs1 === false) 
				{
					trigger_error('Error: ' . $objDB->ErrorMsg(), E_USER_ERROR);
				} 
				else 
				{
					$rows_returned1 = $rs1->RecordCount();
					$rs1->MoveFirst();
					while (!$rs1->EOF) 
					{
						$reward_title = $rs1->fields['reward_title']; 
						$reward_points = $rs1->fields['reward_points']; 
						$additional_reward_points = $rs1->fields['reward_points']; 
						$reward_background = $rs1->fields['reward_background_image']; 
						 $rs1->MoveNext();
					}
				}
				
				// get data from reward_card table
				
                echo '<div class="title cardtitle_ellip">'.$card_title.'</div>';
                echo '<div class="hidden" id="card_type" value="'.$card_type.'"></div>';
                // echo '<div class="hidden" id="terms_conditions" value="'.$terms_conditions.'"></div>';

                echo '<div class="hidden" id="punch" value="'.$punch_type.'"></div>';
                $rs->MoveNext();
              }
            }
        }
        else{
           //header("Location: manage_cards.php");
        }

      ?> 
      <button type="button" class="btn fleft btn-primary" id="edit_card_title">Edit Card Detail</button>
    </div>
    <div class="error_messages">
	         <div class="alert alert-warning alert_card_title">Please enter card title</div>
	         <div class="alert alert-warning alert_html" style="">No HTML allowed</div>
			<div class="alert alert-warning alert_length" style="">Maximum Length should be 76 character</div>
			<div class="alert alert-warning alert_keyword" style="">Please remove any special characters you have entered for card keywords.<br/> Also do not start or use only numbers for card keywords.<br/> You can only add 3 card keywords</div>
			<div class="alert alert-warning alert_category" style="">Please select card category</div>
		</div>
    <div style="display: none;" id="edit_card_form">
        <form>
			
          <div class="heading">Card Title:</div>
          <input type="text" class="form-control" maxlength="76" id="card_title" name="card_title">
          <p class="hint"><span data-max="76">76</span> characters remaining | No HTML allowed</p>
          
          
          <div class="heading">Card Keyword:</div>
          <input type="text" class="form-control" maxlength="76" id="card_keyword" name="card_keyword" value="<?php echo $card_keyword ?>">
          <p class="hint">3 keywords separated by commas | No HTML allowed</p>
          
          <div class="termsdiv">
            <div class="heading">Terms and Conditions:</div>
            <textarea name="terms_conditions" id="terms_conditions" class="form-control"><?php echo $terms_conditions?></textarea>            
          </div>
          
          <div class="row">
				<div class="heading">Select Card Category:</div>
				<div class="row clearfix">
				  <div class="fleft padv10">
					<select name="card_category" id="card_category" class="select form-control w150">
						<option value="0">Select Category</option>
					  <?
						while($Row = $RSCat->FetchRow()){
						?>
							<option <?php if($card_category==$Row['id']){echo "selected";} ?> value="<?=$Row['id']?>"><?=$Row['name']?></option>
						<?
						}
						?>
					</select>
				  </div>            
				</div>
			</div>
			
          <div class="">
            <div class="heading">Redemption Limit:</div>
            <p class="hint">
				(Optional)
			</p>
             <input type="checkbox" id="redeemption_limit" <?php if(isset($_POST['redeemption_limit'])){echo 'checked';} ?> >
            <label for="redeemption_limit" style="cursor:pointer;font-weight:100;">One per day</label>
            
		  </div>
          <button id="update_card_title" class="btn btn-primary" type="button">Update</button>
          <button id="close_card_form" class="btn btn-primary" type="button">Cancel</button>
        </form>
    </div>
    <div class="layoutbox">
      <div class="container_label">Stamp Layout</div>
      <div class="container_box clearfix">
        <div class="layout_box fleft">
			
          
          <div class="select_colors">
            <div class="heading">Select colors</div>
            <div class="background_color">
              <p class="text dib">Select background color  loyality card:</p>
              <input id="background_color" name="background_color"  value="#0066FF"  class="dib color form-control">
            </div>
            <div class="font_color">
              <p class="text dib">Select text color:</p>
              <input id="text_color" value="#ffffff" class="dib color form-control">
            </div>
           <div class="row">
            <div class="heading">Stamp volume</div>
            <div class="text">Select number of stamps per card</div>
            <select name="" id="stamp_volume" class="form-control w150" disabled>
              <option value="6">6 Stamps</option>
              <option value="8">8 Stamps</option>
              <option value="10">10 Stamps</option>
              <option value="12">12 Stamps</option>
            </select>

          </div>
          <div class="row">
            <div class="text dib">Reward points per visit</div>
              <input type="text" id="reward_per_visit" class="dib form-control w150" placeholder="00 (optional)" disabled>
              <div class="hint" style="margin-left:175px">numeric only</div>
          </div>
          <div class="row">
            <div class="heading">Select Stamp Images</div>
            <div class="row clearfix">
              <div class="fleft padv10">
                <p>Choose stamps</p>
                <select name="" id="stamp_image" class="select form-control w150">
					  <?
						while($Row = $RS_stamp_image->FetchRow()){
						?>
							<option stampedimage="<?php echo $Row['stamped_image'] ?>" unstampedimage="<?php echo $Row['unstamped_image'] ?>" <?php if($stamp_image_type_id==$Row['id']){echo "selected";} ?> value="<?=$Row['id']?>"><?=$Row['name']?></option>
						<?
						}
						?>            
                </select>
              </div>            
            </div>
          </div>
          </div>
        </div>
        <div class="loyality_card_preview loyality_card_images  fleft" id="loyality">
          <div class="company_logo fleft"><img src="<?php echo ASSETS_IMG?>/m/icon/<?php echo $RS_mer_info->fields['merchant_icon']?>" alt=""></div>
          <div class="fright no_of_stamps">
            Number of stamps: <span>3</span>
          </div>
          <div class="fright points_per_visit">
            Reward points:<span></span> 
          </div>
          <div class="company_title "><?php echo $RS_mer_info->fields['business']?></div>
          <div class="stamps_box">
            
              <?php 
              
				$sQuery = $objDB->Conn->Execute("SELECT * from stamp_image_type where id=".$stamp_image_type_id);
				$stmped_img= $sQuery->fields['stamped_image'];
				$unstmped_img= $sQuery->fields['unstamped_image'];
	
              echo '<div class="stamps_box'.$stamps_per_card.' clearfix">
              <div class="stamp stamped fleft"><img src="'.ASSETS.'/loyalty/images/'.$stmped_img.'"></div>
              <div class="stamp stamped fleft"><img src="'.ASSETS.'/loyalty/images/'.$stmped_img.'"></div>
              <div class="stamp stamped fleft"><img src="'.ASSETS.'/loyalty/images/'.$stmped_img.'"></div>';
                $unstamped = $stamps_per_card -3;

                for($i=0;$i<$unstamped;$i++){
                  echo '<div class="stamp unstamped fleft"><img src="'.ASSETS.'/loyalty/images/'.$unstmped_img.'"></div>';
                }

               ?>
              
            </div>  
          </div>
          <div class="qr_code">
            <img src="<?php echo ASSETS?>/loyalty/images/barcode.png" >
          </div>

        </div>
      </div>
    </div>
    
    <div class="stampsbox">
      <div class="container_label">Reward Layout</div>
      <div class="container_box clearfix">
        <div class="switch_box fleft">
          <div class="row">
            <div class="heading">Reward</div>
            <div class="text">Reward for collecting stamps</div>
            <input type="text" id="reward_text" placeholder="Eg. You are entitled to Free coffee." maxlength="100" class="form-control " > 
            <div class="hint"><span data-max="100">100</span> characters remaining</div>
            <div class="heading">Upload a reward image</div>
            
            <!--<div class="image_upload_button" id="reward_upload_button">Select image</div>-->
            
			<div class="cls_left">
			<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
				<div id="upload" >
					<span >Browse</span> 
				</div>
			</div> 
            
            <div class="browse_right_content">
				&nbsp;&nbsp;<span >Or select from </span>
					<a class="mediaclass" > media library </a>
			</div> 
			
            <span id="status" style="float: left;width: 100%"></span>
						<br/>
           
						<ul id="files" >
          
						 </ul>
						 
            <!--<input class="hidden"  type="file" accept=".jpg,.png,.gif" onchange=""  id="reward_upload">
            <div class="hint">Image should be  320px x 240px or larger</div>-->
            <img src="" id="reward_image_check" alt="">
            <div class="row">
              <div class="dib">Additional reward points</div> <input id="additional_reward_points" type="text" class="dib form-control w150" placeholder="00 (optional)" disabled> 
              <div class="hint" style="margin-left:175px">numeric only</div>
            </div>
          </div>
        </div>
          <div class="loyality_card_preview loyality_card_front fleft">
            <div class="company_logo fleft"><img src="<?php echo ASSETS_IMG?>/m/icon/<?php echo $RS_mer_info->fields['merchant_icon']?>" alt=""></div>
            <div class="fright no_of_stamps">
               Number of stamps: <span>6</span>
            </div>
            <div class="reward_points_show fright">Reward points:<span></span></div>
            <div class="company_title "><?php echo $RS_mer_info->fields['business']?></div>
            <div class="reward_image">
              <img src="<?php echo ASSETS_IMG.'/m/campaign/'.$reward_background ?>" alt="">
            </div>
            <div class="reward_text fleft">
              <div class="elastic_text"><span>Eg. Free coffee</span></div>
            </div>
            <div class="qr_code">
              <img src="<?php echo ASSETS?>/loyalty/images/barcode.png" alt="">
            </div>
          </div> 
        </div>
      </div>
    <div class="companybox">
      <div class="container_label">Participating Locations</div>
      <div class="container_box clearfix">
        <div class="company_box fleft">
        <div class="participating_div">
        <table id="participating_locations" class="table display"">
        <tbody>
          <?php 


            $sql='SELECT id,address,city,state,zip,active FROM locations where created_by='.$_SESSION['merchant_id'];
           
            $rs=$objDB->Conn->Execute($sql);
             
            if($rs === false) {
              trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $objDB->ErrorMsg(), E_USER_ERROR);
            } else {
              $rows_returned = $rs->RecordCount();
              $rs->MoveFirst();
              while (!$rs->EOF) {
                echo '<tr>';
                echo '<td><input type="checkbox" active="'.$rs->fields['active'].'" id="chk'.$rs->fields['id'].'" data-id="'.$rs->fields['id'].'"/></td>';
                
                $array_where = array();
				$array_where['id'] = $rs->fields['state'];
				$RS_state = $objDB->Show("state", $array_where);

				$array_where = array();
				$array_where['id'] = $rs->fields['city'];
				$RS_city = $objDB->Show("city", $array_where);
				
                 echo '<td><label for="chk'.$rs->fields['id'].'">'.$rs->fields['address'].','.$RS_city->fields['name'].','.$RS_state->fields['short_form'].','.$rs->fields['zip'].'</label></td>';
                $rs->MoveNext();
                echo '</tr>';
              }
            }
           ?> 
        </tbody>
      </table>
          </div>	       
        </div>
      </div>
    </div>
    <div class="companybox">
      <div class="container_label">Stamp Card Volume</div>
      <div class="container_box clearfix">
        <div class="company_box fleft">
          <div class="col-lg-5">
            <div class="text dib">Number of cards</div>
            <input type="text" id="card_volume" class="form-control dib w150" placeholder="0" >
            <div class="hint dib">*required</div>
            <div class="hint ml125">numeric only</div>
          </div>
           <div class="col-lg-3">
            <div class="edit_cards_left">Cards left: <span>0</span></div>
          </div>
          <div class="col-lg-3">
            <div class="total_points">Points required: <span>0</span> points</div>
          </div>
      </div>
      <div class="row">
        <div class="notification notification-minimum-cards">Stamp card volume should be greater than <span></span></div>
      </div>
    </div>
    </div>
      <button type="button" id="create_card_submit" class="btn btn-primary m10">Submit</button>
      <button type="button" id="cancel_card" class="btn btn-primary m10">Cancel</button>
  </div>
  <?php 
  //adding values to hidden fields for repopulate using js in edit_card.js
    echo ' <form action="process_card.php" id="form_to_process" class="hidden">
    <input type="hidden" value="'.$id.'" name="card_id_val" id="card_id_val">
    <input type="hidden" value="'.$card_title.'" name="card_title_val" id="card_title_val">
    <input type="hidden" value="'.$card_type.'" name="card_type_val" id="card_type_val">
    <input type="hidden" value="'.$punch_type.'" name="punch_type_val" id="punch_type_val">
    <input type="hidden" value="'.$card_text_color.'" name="text_color_val" id="text_color_val">
    <input type="hidden" value="'.$card_background.'" name="background_color_val" id="background_color_val">
    <input type="hidden" value="'.$reward_per_visit.'" name="reward_per_visit_val" id="reward_per_visit_val">
    <input type="hidden" value="'.$reward_title.'" name="reward_title_val" id="reward_title_val">
    <input type="hidden" value="'.$reward_points.'" name="reward_points_val" id="reward_points__val">
    <input type="hidden" value="'.$reward_background.'" name="reward_image_val" id="reward_image_val">
    <input type="hidden" value="'.$participating_locations.'" name="participating_locations_val" id="participating_locations_val">
    <input type="hidden" value="'.$card_status.'" name="card_status_val" id="card_status_val">
    <input type="hidden" value="'.$stamps_per_card.'" name="number_stamps_val" id="number_stamps_val">
    <input type="hidden" value="'.$card_volume.'" name="card_volume_val" id="card_volume_val">
     <input type="hidden" value="'.$redeemption_limit.'" name="redeemption_limit_val" id="redeemption_limit_val">
    <input type="hidden" value="'.$terms_conditions.'" name="terms_conditions_val" id="terms_conditions_val">
    <input type="hidden" value="'.$punch_type.'" name="punch_type_val" id="punch_type_val">
    <input type="hidden" value="'.$additional_reward_points.'" name="additional_reward_points_val" id="additional_reward_points_val">
    <input type="hidden" value="'.$cards_left.'" name="cards_left_val" id="cards_left_val">
	<input type="hidden" value="'.$cards_left.'" name="original_cards_left" id="original_cards_left">
	<input type="hidden" value="'.$cards_left.'" name="new_cards_left" id="new_cards_left">
    </form>'
   ?>
	<?php
		echo file_get_contents(WEB_PATH.'/merchant/import_media_library.php?mer_id='.$_SESSION['merchant_id'].'&img_type=template&start_index=0');
	?>
	 <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?php echo $reward_background; ?>" />
     <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
     <input type="hidden" name="hdn_title" id="hdn_title" value="<?php echo $card_title ?>" />
	</div><!-- end of content-->
	
	</div><!-- end of contentContainer-->
	
	<!---------start footer--------------->
    <div>
	<?
		require_once(MRCH_LAYOUT."/footer.php");
	?>
	<!--end of footer-->
	</div>
</body>
</html>
<script type="text/javascript">
	var file_path = "";
function close_popup(popup_name)
{

	jQuery("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
	jQuery("#" + popup_name + "BackDiv").fadeOut(200, function () {
		 jQuery("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				jQuery("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
jQuery('.mediaclass').click(function(){
	jQuery.fancybox({
                content:jQuery('#mediablock').html(),

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
});
    	
jQuery("#show_more_mediya_browse").live("click",function(){
	var cur_el= jQuery(this);
	var next_index = parseInt(jQuery(this).attr('next_index'));
	var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'show_more_media_browse=yes&next_index='+next_index+'&num_of_records='+num_of_records+"&img_type=campaign",
		async:true,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			//alert(obj.status);
			jQuery(".fancybox-inner .ul_image_list").append(obj.html);
			cur_el.attr('next_index',next_index + num_of_records);
			if(parseInt(obj.total_records)<num_of_records)
			{
				cur_el.css("display","none");
			}
		}
	});
});

jQuery(".ul_image_list li").live("click",function(){
	
	jQuery(".useradioclass").prop( "checked", false );
	var imgid=jQuery(this).attr("id").split("img_");
	imgid=imgid[1];
	//alert(imgid);
	jQuery(this).find(".useradioclass").prop( "checked", true );
	
	jQuery(".ul_image_list li").removeClass("current");
	jQuery(this).addClass("current");
	
	jQuery(".fancybox-inner .useradioclass").each(function(){
               
		if(jQuery(".fancybox-inner .useradioclass").is(":checked"))
		{
			
			jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
			jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
			jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#3C99F4 !important");
		}
		else
		{
			jQuery(".fancybox-inner #btn_save_from_library").attr("disabled",true);
			jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
			jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#ABABAB !important");
		}
		
	});
            
	
});

function save_from_library()
{
        $av = jQuery.noConflict();
	 var sel_val = $av('input[name=use_image]:checked').val();
         
	
	 if (sel_val==undefined)
	 {
	 	jQuery.fancybox.close();
	 }
	 else
	 {
		
                
		$av("#hdn_image_id").val(sel_val);
		
            var sel_src = $av(".fancybox-inner #li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
          
		
	       
	       $av("#hdn_image_path").val(sel_src);
               
	       /* NPE-252-19046 */
	       
	       /* NPE-252-19046 */
	       file_path = "";
	       //close_popup('Notification');
               jQuery.fancybox.close();
	       var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ sel_src +"' class='displayimg'>";
            var img_src = "<?=ASSETS_IMG ?>/m/campaign/"+ sel_src;
	       //$av('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/large_close.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
	       
	     jQuery('#reward_upload').attr('data-url',img_src);
         jQuery('.reward_image img').attr('src',img_src)
                
	 }

}

jQuery(function(){
		var btnUpload=jQuery('#upload');
		var status=jQuery('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUpload&img_type=template',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				if(jQuery('#files').children().length > 0)
				{
					jQuery('#files li').detach();
				}
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
				//On completion clear the status
				/*
				var arr = response.split("|");
				
				status.text('');
				//Add uploaded file to list
				file_path = arr[1];
				save_from_computer();
                                */
                               var arr = response.split("|");
				if(arr[1]=="small")
                                {
                                    status.text(arr[0]);
                                }
                                else
                                {
                                    status.text('');
                                    //Add uploaded file to list
                                    file_path = arr[1];
                                    save_from_computer();
                                }
			}
		});
		
	});

function save_from_computer()
{
	jQuery("#hdn_image_path").val(file_path);
	jQuery("#hdn_image_id").val("");
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ file_path +"' class='displayimg'>";
	var img_src = "<?=ASSETS_IMG ?>/m/campaign/"+ file_path;
	//jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/large_close.png' id='"+file_path+"'  class='cancel_remove_image' onclick='rm_image_permanent(this.id)' /></div></div></div>");
	jQuery('#reward_upload').attr('data-url',img_src);
    jQuery('.reward_image img').attr('src',img_src)			
}	
	
jQuery(document).ready(function(){
  var background_color =jQuery('#background_color_val').val();
  var text_color =jQuery('#text_color_val').val();
  var card_type = jQuery('#card_type_val').val();
  var stamps_per_card=jQuery('#number_stamps_val').val();
  var reward_per_visit =jQuery('#reward_per_visit_val').val();
  var reward_title = jQuery('#reward_title_val').val();
  var reward_points =jQuery('#reward_points_val').val();
  var reward_background =jQuery('#reward_image_val').val();
  var participating_locations =jQuery('#participating_locations_val').val();
  var card_volume =jQuery('#card_volume_val').val();
  var redeemption_limit =jQuery('#redeemption_limit_val').val();
  var punch_type =jQuery('#punch_type_val').val();
  var card_status =jQuery('#card_status_val').val();
  var terms_conditions =jQuery('#terms_conditions_val').val();
  var additional_reward_points = jQuery('#additional_reward_points_val').val();
  var cards_left = jQuery('#cards_left_val').val();
  console.log(additional_reward_points)

  //var additional_reward_points =$('#').val();

  //repopulating data in page
console.log(participating_locations)
  jQuery('#background_color').val(background_color);
  jQuery('.loyality_card_preview').css('background','#'+background_color)
  jQuery('#text_color').val(text_color);
  jQuery('.loyality_card_preview').css('color','#'+text_color);
  jQuery('#stamp_volume').val(stamps_per_card);
  jQuery('#reward_per_visit').val(reward_per_visit);
  jQuery('#reward_text').val(reward_title);
  jQuery('.elastic_text span').html(reward_title);
  jQuery('#card_volume').val(card_volume);

	  if(redeemption_limit==1)
	  {	
		jQuery('#redeemption_limit').prop("checked",true);
	}
	else
	{
		jQuery('#redeemption_limit').prop("checked",false);
	}

  jQuery('.edit_cards_left span').html(cards_left)
  jQuery('#additional_reward_points').val(additional_reward_points);
  if(additional_reward_points!="")
  {
	  jQuery(".reward_points_show span").html(additional_reward_points);
	  jQuery(".reward_points_show").css("display","block");
  }
  //$('.no_of_stamps span').html(stamps_per_card)
  jQuery('.stampsbox .no_of_stamps span').html(stamps_per_card)
  var locations_array = participating_locations.split(',');
  var locations_array_length = locations_array.length;
  for(i=0;i<locations_array_length;i++){
    console.log(locations_array[i])
    jQuery('#participating_locations').find('input[data-id="'+locations_array[i]+'"]').prop('checked', true);
    jQuery('#participating_locations').find('input[data-id="'+locations_array[i]+'"]').prop('disabled', true);

  }
  
	var deactive_locations_array="";
	jQuery("input[id^='chk'][active=0]").each(function(){ 
		var str = jQuery(this).attr('id');
		var location_id = str.substring(3);	
		deactive_locations_array = deactive_locations_array + location_id + ",";
		console.log("location id ="+location_id);
		//deactive_locations_array +=
        
	});
	console.log("deactive_locations_array ="+deactive_locations_array);
    deactive_locations_array = deactive_locations_array.substring(0,deactive_locations_array.length-1);
	console.log("deactive_locations_array ="+deactive_locations_array);	
	
	var deactive_loc_array = deactive_locations_array.split(',');
	var deactive_loc_array_length = deactive_loc_array.length;
	
	for(i=0;i<deactive_loc_array_length;i++){
		console.log(deactive_loc_array[i]+" "+jQuery("#chk"+deactive_loc_array[i]).prop('checked'));
		if(jQuery("#chk"+deactive_loc_array[i]).prop('checked') == true)
		{
		}
		else
		{
			jQuery("#chk"+deactive_loc_array[i]).parent().parent().remove();
		}
	} 
	
  calculate_points();
  var original_card_volume = jQuery('#card_volume_val').attr('value');
  var cards_left_volume= jQuery('#cards_left_val').attr('value')
  console.log(original_card_volume);
  console.log('********')
  jQuery('#card_volume').on('input',function(){
    var cards_blocked = original_card_volume - cards_left_volume;
    console.log("original_card_volume="+original_card_volume);
    console.log("cards_left_volume="+cards_left_volume);
	console.log("cards_blocked="+cards_blocked);
    if(jQuery('#card_volume').val()< cards_blocked ){
      jQuery('.notification-minimum-cards span').html(cards_blocked)
      jQuery('.notification-minimum-cards').show(500)
      jQuery('#create_card_submit').attr('disabled','disabled');
    }
    else{
      var change = original_card_volume - jQuery('#card_volume').val()
      console.log("change="+change);
      jQuery('#cards_left_val').attr('value',cards_left_volume)
      console.log("edit_cards_left="+(cards_left_volume-change));
      jQuery('.edit_cards_left span').html(cards_left_volume-change)
      jQuery('.notification-minimum-cards').hide(500)
      jQuery('#create_card_submit').removeAttr('disabled')

    }
     jQuery('#new_cards_left').val(cards_left_volume-change);
  })
  if(reward_per_visit>0){
    jQuery('.points_per_visit span').html(reward_per_visit);
    jQuery('.points_per_visit').show()
  }

	jQuery('#edit_card_title').click(function(){
        jQuery('#edit_card_form').show(500);
        jQuery('#card_title').val(jQuery('.cardtitle_ellip').html());
        jQuery('#card_title').focus();
        words_remaining("#card_title")
         var len = jQuery("#card_title").val().length;
        var max = jQuery("#card_title").next().find('span').attr('data-max');
        jQuery("#card_title").next().find('span').html(max-len);
    })
     jQuery('#edit_card_form #close_card_form').click(function(){
    	jQuery('#edit_card_form').hide(500);
    	jQuery(this).closest('#edit_card_form').siblings('.error_messages').find('.alert_card_title').hide(500);
    	 jQuery(this).closest('#edit_card_form').siblings('.error_messages').find('.alert_length').hide(500);
         jQuery(this).closest('#edit_card_form').siblings('.error_messages').find('.alert_html').hide(500);
    })
     jQuery('#update_card_title').click(function(){
		 var html_regex=/[<>?]/;
		
		var flag="false";
		var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
		var tag_value=jQuery("#card_keyword").val();
		if(tag_value == "")
		{
			flag="true";
		}
		else
		{
			if(hastagRef.test(tag_value))
			{
				var tag_arr = tag_value.split(",");   
				//alert(tag_arr.length);
				if(tag_arr.length>3)
				{
					flag="false";
				}
				else
				{
					flag="true";
				}
			}
			else
			{
				flag="false";
			}
		}
		var card_category = jQuery('#card_category').val();
		console.log(card_category);
		
		 var card_title = jQuery('#card_title').val();
		 //alert(card_title);
        if(card_title.length == 0 || html_regex.test(card_title) || card_title.length > 76 || flag=="false"||card_category==0){
            console.log(card_title.length)
            if (card_title.length == 0)
            {
				jQuery('.error_messages').find('.alert_card_title').show(500);
            }
            else
            {
				jQuery('.error_messages').find('.alert_card_title').hide(500);
			}
			if (card_title.length > 76)
            {
                jQuery('.error_messages').find('.alert_length').show(500);
            }
            else
            {
				jQuery('.error_messages').find('.alert_length').hide(500);
			}
            if (html_regex.test(card_title))
            {
                jQuery('.error_messages').find('.alert_html').show(500);
            }
            else
            {
				jQuery('.error_messages').find('.alert_html').hide(500);
			}
			if(flag=="false")
			{
                jQuery('.error_messages').find('.alert_keyword').show(500);
            }
            else
            {
				jQuery('.error_messages').find('.alert_keyword').hide(500);
			}
			if(card_category==0)
			{
                jQuery('.error_messages').find('.alert_category').show(500);
            }
            else
            {
				jQuery('.error_messages').find('.alert_category').hide(500);
			}
        }
        else
        {
			jQuery('.error_messages').find('.alert_card_title').hide(500);
            jQuery('.error_messages').find('.alert_length').hide(500);
            jQuery('.error_messages').find('.alert_html').hide(500);
			jQuery('.error_messages').find('.alert_keyword').hide(500);
            jQuery('.error_messages').find('.alert_category').hide(500);
			jQuery('#edit_card_form').hide(500);
			var card_title= jQuery('#card_title').val();
			console.log('')
			jQuery('.cardtitle_ellip').html(card_title);
			jQuery('#card_title_val').val(card_title);
			jQuery('#hdn_title').val(card_title);
			
		}
	});

})
jQuery(document).ready(function(){
  
  //button click function for image upload buttons
  button_click('logo');
  button_click('icon');
 // button_click('unstamped');
  //button_click('stamped');
  button_click('reward_upload');

  //events for background color selection 
  jQuery('body').on('change','#background_color',function(){
    var color = jQuery(this).val();
    jQuery('.loyality_card_preview').css('background','#'+color);
  })
  //event to change stamp images for default images
  jQuery('#stamp_image').on('change',function(){
    var length = jQuery('.stamps_box .stamp').length;
    console.log(length)
    var stmp_img = jQuery(this).find('option:selected', this).attr('stampedimage');
    var unstmp_img = jQuery(this).find('option:selected', this).attr('unstampedimage');
    console.log("stamp="+stmp_img);
    console.log("unstamp="+unstmp_img);
    for(k=0;k<length;k++){
        //var stamped = '<?php echo ASSETS?>/loyalty/images/'+$(this).val()+'-stamped.png';
        //var unstamped = '<?php echo ASSETS?>/loyalty/images/'+$(this).val()+'-unstamped.png';
        
        
       var stamped = '<?php echo ASSETS?>/loyalty/images/'+stmp_img;
       var unstamped = '<?php echo ASSETS?>/loyalty/images/'+unstmp_img;
        jQuery('.stamped img').attr('src',stamped);    
        jQuery('.unstamped img').attr('src',unstamped);

    } 
  })
  
  //overlay for reward text on image upload
  jQuery('#reward_upload').on('change',function(){
    jQuery('.reward_text').css('background','#333')
  })
  //events for stamp volume change for updating stamps on card.
  jQuery('#stamp_volume').on('change',function(){
	 
    var volume = jQuery('#stamp_volume').val();
    
    var stamped_image = jQuery('.stamped img').attr('src')
    var unstamped_image = jQuery('.unstamped img').attr('src')
    var dom = "";
    var image = jQuery('#stamp_image').val();

    //$('.no_of_stamps span').html(volume);
     jQuery('.stampsbox .no_of_stamps span').html(volume);
    dom+= '<div class="stamps_box'+volume+' clearfix">';
    for(i=0;i<volume;i++){
      if(i<3){
        dom+= '<div class="stamp stamped fleft"><img src="'+stamped_image+'"></div>';  
      }
      else{
        dom+= '<div class="stamp unstamped fleft"><img src="'+unstamped_image+'"></div>';
      }
    }
    dom+='</div>';
    jQuery('.stamps_box').html(dom);
  })
  //changing card text color on color pick
  jQuery('#text_color').change(function(){
    var textcolor = jQuery(this).val();
    jQuery('.loyality_card_preview').css('color','#'+textcolor);
  })
  //updating reward text on card
  jQuery('#reward_text').on('keyup',function(){
    var reward_text = jQuery(this).val();
    jQuery('.elastic_text span').html(reward_text);

    if(reward_text.length==0){
      jQuery('.elastic_text').html('<span>'+jQuery(this).attr('placeholder')+'</span>')
    }
  })
  //function for image button click
  function button_click(button){
    jQuery('#'+button+'_button').click(function(){
      jQuery(this).next('#'+button).trigger('click');
    })
  }
  //showing additional reward points on card if > 0
  jQuery('#additional_reward_points').on('input',function(){
    if(jQuery(this).val()>0){
      jQuery('.reward_points_show').show();
      jQuery('.reward_points_show span').html(jQuery(this).val());
    }
    else{
      jQuery('.reward_points_show').hide();
    }
  })
  //showing reward per visit on card
  jQuery('#reward_per_visit').on('input',function(){
    
    if(jQuery(this).val()>0){
      jQuery('.points_per_visit').show();
      jQuery('.points_per_visit span').html(jQuery(this).val());
    }
    else{
      jQuery('.points_per_visit').hide();
    }
  })
  //show words remaining below text field
  words_remaining('#reward_text')
  rewardimageValidation('#reward_upload',320,240)
  stampimageValidation('unstamped',100,100)
  stampimageValidation('stamped',100,100)

//calculating points required and updating corresponding data at the botom
jQuery('#stamp_volume').on('change',function(){
  calculate_points();
})
jQuery('#reward_per_visit').on('input',function(){
  calculate_points();
})
jQuery('#additional_reward_points').on('input',function(){
  calculate_points();
})
jQuery('#card_volume').on('input',function(){
  calculate_points();
})
jQuery('#card_volume').on('input',function(){
  var val = jQuery(this).val();
  jQuery('.cards_left span').html(val);
  
 
  
})


  // function colorChange(color){
  //   var color = color.val();
  //   $('.loyality_card_preview').css('background','#'+color);
  // }
})
//function to calculate points required
function calculate_points(){
  var number_of_stamps = jQuery('#stamp_volume').val();
  var points_per_visit = jQuery('#reward_per_visit').val();
  var additional_reward = jQuery('#additional_reward_points').val();
  var card_volume= jQuery('#card_volume').val();
  //per visit points X number of stamps per card x total number of cards + number of cards x reward card point
  var total_points = (card_volume * number_of_stamps * <?php echo $get_billing_pack_data->fields['transaction_fees_stamp']?>)  +(points_per_visit * number_of_stamps * card_volume) + (card_volume * additional_reward);
  //console.log(total_points);
  jQuery('.total_points span').html(total_points)
}
//imgage validation, validates width and height
function rewardimageValidation(id,minwidth,minheight){
  var _URL = window.URL || window.webkitURL;
  jQuery('body').on('change',id,function(e) {
      
      var image, file;

      if ((file = this.files[0])) {
         
          image = new Image();
          
          image.onload = function() {
              
              //alert("The image width is " +this.width + " and image height is " + this.height);
              if(this.width < minwidth || this.height<minheight){
                alert('Image should be 320px x 240px or larger');
                jQuery(id).replaceWith(jQuery(id).val('').clone(true));
              }
              else{
				  console.log(image);
				 
				   //alert(image.src);
                jQuery('#reward_upload').attr('data-url',image.src);
                jQuery('.reward_image img').attr('src',image.src)
              }
          };
      
          image.src = _URL.createObjectURL(file);


      }

  });
}
//custom stamp image validation
//stamp image currently disabled on dom
function stampimageValidation(id,maxwidth,maxheight){
  var _URL = window.URL || window.webkitURL;
  jQuery('body').on('change','#'+id,function(e) {

      var image, file;

      if ((file = this.files[0])) {
         
          image = new Image();
          
          image.onload = function() {
              //alert("The image width is " +this.width + " and image height is " + this.height);
              console.log(this.width)
              if(this.width > maxwidth || this.height>maxheight){
                alert('Cannot upload image larger than 100 X 100');
                jQuery('#'+id).replaceWith(jQuery('#'+id).val('').clone(true));
              }
              else{
                jQuery('#'+id).attr('data-url',image.src);
                jQuery('.'+id+' img').attr('src',image.src)
              }
          };
      
          image.src = _URL.createObjectURL(file);


      }

  });
}

//function to count words remaining 
function words_remaining(input){
  jQuery('body').on('input',input,function(){
    var len = jQuery(this).val().length;
    var max = jQuery(this).next().find('span').attr('data-max');
    jQuery(this).next().find('span').html(max-len);
  })
}
//update logo on upload
//feature removed at dom
function showLogo(input, identity) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
    var url = e.target.result;
      if(e.target.result.length > 0){
        jQuery(input).attr('data-url',e.target.result);
        jQuery('.company_logo ').html('<img src="'+jQuery("#logo").attr("data-url")+'" alt=""/>');
        jQuery('.logo_alt').remove();
      };
    }
    reader.readAsDataURL(input.files[0]);
  }
}
//image change on upload
function imageChange(input,target){
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
    var url = e.target.result;
      if(e.target.result.length > 0){
        console.log(url)
        jQuery(input).attr('data-url',e.target.result);
        jQuery('.'+target+' img').attr('src',url)
      };
    }
    reader.readAsDataURL(input.files[0]);
  }
}
jQuery(document).ready(function(){
  jQuery('body').on('click','#cancel_card',function(){
    window.location.href = "manage_cards.php";
  })
 jQuery('body').on('click','#create_card_submit',function(){
	
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
				
					//taking all values into variables to bind it to ajax post request
					 var card_id=jQuery('#card_id_val').val();
					var card_title = jQuery('#card_title').val();
					card_title = trimming(card_title)
				   jQuery('#card_title_val').val(card_title)
					card_title = jQuery("#hdn_title").val();
					
					var card_type=jQuery("#card_type").attr('value');
					jQuery('#card_type_val').val(card_type)


					var terms_conditions = jQuery('#terms_conditions').attr('value');
					terms_conditions = trimming(terms_conditions);

					var punch = jQuery('#punch').attr('value');
					jQuery('#punch_type_val').val(punch);

					var card_background = jQuery('#background_color').val();

					var text_background = jQuery('#text_color').val();

					var no_of_stamps = jQuery('#stamp_volume').val();

					var reward_text = jQuery('#reward_text').val();


					//var reward_image = jQuery('#reward_upload').attr('data-url');
					var reward_image = jQuery('#hdn_image_path').val();
					
					//if additional points is  empty setting it to 0.
					var additional_reward_points = jQuery('#additional_reward_points').val();
					if(additional_reward_points == '' ){
					  additional_reward_points = 0 ;
					}
				  
					var reward_per_visit = jQuery('#reward_per_visit').val()

					var participating_locations_length = jQuery('table td').find('input[type=checkbox]').length;
					var participating_locations_array = "";
					//appending location ids to array string to save it in db.
					for(i=0;i<participating_locations_length;i++){
					  if(jQuery('tbody tr').eq(i).find('input[type=checkbox]').is(':checked')){
						participating_locations_array = participating_locations_array + jQuery('tbody').find('tr').eq(i).find('input[type=checkbox]').attr('data-id');
						if(i< participating_locations_length-1){
						  participating_locations_array = participating_locations_array + ',';
						}
					  }
					}
					
				  
					// regex to check if locations array have any "," at the end. remove if any "," exist.
					if(/,$/.test(participating_locations_array)== true){
					  participating_locations_array = participating_locations_array.substring(0, participating_locations_array.length-1);
					  console.log(participating_locations_array)
					}  
					var card_volume = jQuery('#card_volume').val();
					//set card_volume is empty , sets it 0 if 
					// if (card_volume== ''){
					//   card_volume = 0;
					// }
					// console.log(card_volume)
					

					var regex = /^[0-9]*(?:\.\d{1,2})?$/;
					var reward_per_visit_val = jQuery('#reward_per_visit').val();
					var additional_points = jQuery('#additional_reward_points');
					var original_card_volume = jQuery('#card_volume_val').attr('value')
					var card_left_volume = jQuery('#card_left_val').attr('value');
					var total_points = jQuery('.total_points span').html();
					console.log('total points'+total_points)
					var redeemption_limit =  jQuery('#redeemption_limit').val();
					if(jQuery("#redeemption_limit").is(':checked'))
					{
						redeemption_limit = 1;
					}
					else
					{
						redeemption_limit = 0;
					}
					var cat_value = jQuery('#card_category').val();
					var card_keyword = jQuery('#card_keyword').val();
					var terms_conditions = jQuery('#terms_conditions').val();
					
					var flag="false";
						var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
						var tag_value=jQuery("#card_keyword").val();
						if(tag_value == "")
						{
							flag="true";
						}
						else
						{
							if(hastagRef.test(tag_value))
							{
								var tag_arr = tag_value.split(",");   
								//alert(tag_arr.length);
								if(tag_arr.length>3)
								{
									flag="false";
								}
								else
								{
									flag="true";
								}
							}
							else
							{
								flag="false";
							}
						}
						
					var stamp_image = jQuery('#stamp_image').val();
					var reward_image = jQuery('#hdn_image_path').val();
					
					// new code for increase or decrease merchant points
					
					var original_cards_left = jQuery('#original_cards_left').val();
					var new_cards_left = jQuery('#new_cards_left').val();
					
					var number_of_stamps1 = jQuery('#stamp_volume').val();
					var points_per_visit1 = jQuery('#reward_per_visit').val();
					var additional_reward1 = jQuery('#additional_reward_points').val();
					var card_volume1 = Math.abs(original_cards_left-new_cards_left);
					//per visit points X number of stamps per card x total number of cards + number of cards x reward card point
					var total_points1 = (card_volume1 * number_of_stamps1 * <?php echo $get_billing_pack_data->fields['transaction_fees_stamp']?>)  +(points_per_visit1 * number_of_stamps1 * card_volume1) + (card_volume1 * additional_reward1);
  
					// new code for increase or decrease merchant points
					
						//alert(jQuery("#hdn_title").val());
					//checking validations
					if(flag=="true" && cat_value!=0 && card_volume != 0 && regex.test(card_volume) && reward_text.length !=0 && regex.test(card_volume) && regex.test(reward_per_visit_val) && regex.test(additional_reward_points)){
					  //showing notifications
					  jQuery('.notification-card-volume').hide(500);
					  jQuery('.notification-redeemption-limit').hide(500); 
					  jQuery('.notification-reward-text').hide(500); 
					  jQuery('.notification-additional-points').hide(500); 
					  jQuery('.notification-reward-per-visit').hide(500); 
					  jQuery('.alert_category').hide(500); 
					  jQuery('.alert_keyword').hide(500); 
					  //ajax request and sending data to process_card.php 
					  jQuery.ajax({
						  type: "POST", // HTTP method POST or GET
						  url: "process_card.php", //Where to make Ajax calls
						  dataType:"text", // Data type, HTML, json etc.
						  data:{total_points1:total_points1,original_cards_left:original_cards_left,new_cards_left:new_cards_left,stamp_image:stamp_image,card_keyword:card_keyword,card_category:cat_value,redeemption_limit:redeemption_limit,cardtitle: card_title,cardid: card_id , cardtype: card_type,textbackground: text_background,cardbackground:card_background,textcolor:text_background,stampspercard:no_of_stamps,rewardtitle:reward_text,rewardpoints:additional_reward_points,rewardbackgroundimage:reward_image,cardvolume:card_volume,termsandconditions: terms_conditions,locations:participating_locations_array,rewardpervisit:reward_per_visit,additionalrewardpoints:additional_reward_points,originalvolume:original_card_volume,totalpoints:total_points},
						  success:function(response){
							console.log(response);
							if(response >0){
							  jQuery('#card_id_val').val(response);
							}
							  jQuery('#notification').fadeIn(500);
							  setTimeout(function(){jQuery('#notification').fadeOut(500); }, 3000);
							 window.location.href = "manage_cards.php";
							
						  },
						  error:function (xhr, ajaxOptions, thrownError){
							  alert(thrownError);
						  }
					  })
					}
					//showing notifications if validation fails
					else{
					  jQuery('.notification-card-volume').hide(500);  
					  jQuery('.notification-reward-text').hide(500); 
					  jQuery('.notification-additional-points').hide(500); 
					  jQuery('.notification-reward-per-visit').hide(500); 
					  jQuery('.alert_category').hide(500); 
					  jQuery('.alert_keyword').hide(500); 
					 if(cat_value == 0){
						jQuery('.alert_category').show(500);
					  }
					  if(flag == "false"){
						jQuery('.alert_keyword').show(500);
					  }
					  if(card_volume == 0 || !regex.test(card_volume)){
						jQuery('.notification-card-volume').show(500);
					  }
					  if(reward_text.length ==0){
						jQuery('.notification-reward-text').show(500); 
					  }
					  if(!regex.test(reward_per_visit_val) ){
						jQuery('.notification-reward-per-visit').show(500); 
					  }
					  if(!regex.test(additional_reward_points)){
						jQuery('.notification-additional-points').show(500); 
					  }
					}
			}
			
			}
		});
  })
})


//trimming function to remove spaces for saving input data into variables
function trimming(input) {
    var str = input;
  str = str.replace(/\s{2,}/g, ' ').trim();
  return str;
  console.log(str);
}
</script>
