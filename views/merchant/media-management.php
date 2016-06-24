<?php
/**
 * @uses media management
 * @used in pages : media-management.php,process.php,my-account-left.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
include(LIBRARY."/easy_upload/upload_class.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
include(LIBRARY."/simpleimage.php");
//$objDB = new DB();
$array = array();

// delete 
if(isset($_REQUEST['action']))
{
    if($_REQUEST['action'] == "delete"){
	$folder ="";
	if($_REQUEST['image_type']=="campaign")
	{
		$folder = "campaign";
		$c_array = array();
		$c_array ['business_logo'] = $_REQUEST['src'];
		$merchant_info = $objDB->Show("campaigns",$c_array );
		
		$total = $merchant_info->RecordCount();
	}
	if($_REQUEST['image_type']=="store")
	{
		$folder = "location";
		$c_array = array();
		$c_array ['picture'] = $_REQUEST['src'];
		$merchant_info = $objDB->Show("locations",$c_array );
		$total = $merchant_info->RecordCount();
	}
	if($_REQUEST['image_type']=="template")
	{
		$folder = "campaign";
		$c_array = array();
		$c_array['business_logo'] = $_REQUEST['src'];
		$merchant_info = $objDB->Show("campaigns_template",$c_array );
		$total = $merchant_info->RecordCount();
	}
	
	if($total== 0)
	{
	
		$array_where = array();
		$array_where['id'] = $_REQUEST['id'];
		$objDB->Remove("merchant_media", $array_where);
		unlink(UPLOAD_IMG."/m/".$folder."/".$_REQUEST['src']);
		$_SESSION['msg']="Image is deleted successfully";
		header("Location: ".WEB_PATH."/merchant/media-management.php");
		exit();
	}else{
	    $_SESSION['msg']=$merchant_msg["mediamanagement"]["Msg_cant_delete_image"];
		header("Location: ".WEB_PATH."/merchant/media-management.php");
	exit();
	}
 }
}
 $sort_permision_arr = array();
$n_arr = array();
$n_arr['bSortable'] = false;
array_push($sort_permision_arr, null);
array_push($sort_permision_arr, null);
//array_push($sort_permision_arr, null);
/* * **** get employee role ( which role is assigned to user ) ************** */
		if($_SESSION['merchant_info']['merchant_parent'] != 0)
		{
			
			$media_acc_array = array();
			$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];;
			$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array);
			$media_val = unserialize($RSmedia->fields['media_access']);
			if(in_array("delete",$media_val))
			{
				array_push($sort_permision_arr, $n_arr);
			}
			
		}
		else
		{
			array_push($sort_permision_arr, $n_arr);
		}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Media management </title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap.css" />
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.min.js"></script>

<!-- start new plupload -->

<link rel="stylesheet" href="<?=ASSETS?>/jquery.ui.plupload/css/jquery.ui.plupload.css" type="text/css" />

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=ASSETS?>/jquery.ui.plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="<?=ASSETS?>/jquery.ui.plupload/jquery.ui.plupload.js"></script>

<!-- end new plupload -->

<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
</head>

<body>
<div >
<!---start header---->
	<div>
		<?
		// inclde header file 
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	
	<div id="content">

  <div class="title_header">Media Management </div>
	    
	<div id="media-upload-header">
		<ul id="sidemenu">
			<?php
			if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='imagegallery')
			{
			?>
				<li id="tab-type" data-hashval="uploaddiv" class="div_upload">
				<a >Upload</a>
				</li>
				<li id="tab-type" data-hashval="imagediv"  class="div_image" >
					<a >My Images</a>
				</li>
				<li id="tab-type" data-hashval="videodiv"  class="div_video" >
					<a >My Videos</a>
				</li>
				<li id="tab-type" data-hashval="librarydiv"  class="div_library" >
					<a >Image Library</a>
				</li>
				<li id="tab-type" data-hashval="imagegallerydiv"  class="div_imagegallery" >
					<a class="current">Location Media Gallery</a>
				</li>
			<?php
			}
			else
			{
			?>
				<li id="tab-type" data-hashval="uploaddiv" class="div_upload">
					<a class="current">Upload</a>
				</li>
				<li id="tab-type" data-hashval="imagediv"  class="div_image" >
					<a >My Images</a>
				</li>
				<li id="tab-type" data-hashval="videodiv"  class="div_video" >
					<a >My Videos</a>
				</li>
				<li id="tab-type" data-hashval="librarydiv"  class="div_library" >
					<a >Image Library</a>
				</li>
				<li id="tab-type" data-hashval="imagegallerydiv"  class="div_imagegallery" >
					<a >Location Media Gallery</a>
				</li>
			<?php
			}
			?>
		</ul>
	</div>
	
	<div class="all_div_container">
		<?php
		if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='imagegallery')
		{
		?>
		<div id="div_upload" class="tabs" style="display:none;">
		
		<?php
       
        $arr_1=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
		if(trim($arr_1[0]) == "")
		{
		 unset($arr_1[0]);
		 $arr_1 = array_values($arr_1);
		}
		$json_1 = json_decode($arr_1[0]);
		$main_merchant_id = $json_1->main_merchant_id;
		  
			   $arr=file(WEB_PATH.'/merchant/process.php?getallsubmercahnt_id=yes&mer_id='.$main_merchant_id);
		if(trim($arr[0]) == "")
		{
				unset($arr[0]);
				$arr = array_values($arr);
		}
		$json = json_decode($arr[0]);
		$ids= $json->all_sub_merchant_id;
		if($ids == "")
		  {
			  $ids =$_SESSION['merchant_id'];
		  }
		  

		/* * ******* get all sub merchant of current login employee **************** */
			   $arr_2=file(WEB_PATH.'/merchant/process.php?getallsubmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
			   
		if(trim($arr_2[0]) == "")
		{
				unset($arr_2[0]);
				$arr_2 = array_values($arr_2);
		}
		$json_2 = json_decode($arr_2[0]);
		$sub_merchant_string= $json_2->all_sub_merchant_id;

		$sub_merchant=  explode(',', $sub_merchant_string);

		if($sub_merchant == "")
		  {
			  $sub_merchant =$_SESSION['merchant_id'];
		  }

		/* * ******* get all main merchant of current login employee **************** */
		 $arr=file(WEB_PATH.'/merchant/process.php?getallmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
					 
		 if(trim($arr[0]) == "")
		 {
		  unset($arr[0]);
		  $arr = array_values($arr);
		 }
		 $json = json_decode($arr[0]);
				
		  $all_main_merchant_id= $json->all_main_merchant_id; 
		  
		  $all_main_mer_array = array ();
		  
		  if($all_main_merchant_id == "")
		  {
			  $all_main_merchant_id =$_SESSION['merchant_id'];
		  }
		  else
		  {
			  $all_main_mer_array=explode(",",$all_main_merchant_id);
		  }    
  
		 
		/****
		- get all media images
		- If merchant is login then He will able see all images uploaded by own and as well his all employess uploaded images
		- If employee is login then He will able see all images uploaded by own and as well his all employess uploaded images and his parent merchant's uploaded images
		******/

         $merchant_array = array();
		$merchant_array['id'] = $_SESSION['merchant_id'];
		$merchant_info = $objDB->Show("merchant_user",$merchant_array);
		
		$start_index = 0;
		$num_of_records = 16;
		$num_of_records_l = 16;
		$num_of_records_l_v = 16;
		$next_index = $start_index + $num_of_records;
		$next_index_l = $start_index + $num_of_records_l;
		$next_index_l_v = $start_index + $num_of_records_l_v;
		
			if(isset($_REQUEST['imagetype']))
			{
				if($_REQUEST['imagetype']!='all')
				{
					$query = "select * from merchant_media where image_type='".$_REQUEST['imagetype']."' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'] ." or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .")) order by id desc limit 0,$num_of_records" ;
					$query1 = "select count(*) total from merchant_media where image_type='".$_REQUEST['imagetype']."' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'] ." or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .")) order by id desc" ;
				}
				else
				{
					$query = "select * from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (".$all_main_merchant_id.") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") order by id desc limit 0,$num_of_records" ;
					$query1 = "select count(*) total from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (".$all_main_merchant_id.") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") order by id desc" ;
				}
			}
			else 
			{
				//$query = "select * from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (". $all_main_merchant_id .") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") order by id desc limit 0,$num_of_records" ;
				// 12 01 2015 to add import images from admin
				//$query = "select * from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (". $all_main_merchant_id .") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") or id in( select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'] .") order by id desc limit 0,$num_of_records" ;
				$query = "select * from merchant_media where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) or id in( select merchant_media_id from media_import_image where merchant_id=?) order by id desc limit 0,$num_of_records" ; // with prepare query				
				// 12 01 2015 to add import images from admin
				
				
				//$query1 = "select count(*) total from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (". $all_main_merchant_id .") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") order by id desc" ;
				// 12 01 2015 to add import images from admin
				//$query1 = "select count(*) total from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (". $all_main_merchant_id .") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .")  or id in( select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'] .") order by id desc" ;
				$query1 = "select count(*) total from merchant_media where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?)  or id in( select merchant_media_id from media_import_image where merchant_id=?) order by id desc" ;  // with prepare query
				// 12 01 2015 to add import images from admin
				
				// video query
				$query_v = "select * from merchant_videos where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) order by id desc limit 0,$num_of_records" ; // with prepare query
				$query1_v = "select count(*) total from merchant_videos where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) order by id desc" ;  // with prepare query
				// video query	
				
			}
                    
          //echo $query;
		  $myQuery = $query;    
		  //$RS = $objDB->execute_query($query);
		  $RS = $objDB->Conn->Execute($query,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id'],$_SESSION['merchant_id']));
		  //$RS1 = $objDB->execute_query($query1);
		  $RS1 = $objDB->Conn->Execute($query1,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id'],$_SESSION['merchant_id']));
		 
		  $total_images = $RS1->fields['total'];
		  
		  // video query
			$RS_v = $objDB->Conn->Execute($query_v,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id']));
			$RS1_v = $objDB->Conn->Execute($query1_v,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id']));
			$total_videos = $RS1_v->fields['total'];
			// video query
			
		  // image gallery queries
				
				$query_l = "select * from merchant_media where media_type_id=2 and ( merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) or id in( select merchant_media_id from media_import_image where merchant_id=?) ) order by id desc limit 0,$num_of_records_l" ; // with prepare query				
				
				//echo "select * from merchant_media where merchant_id=".$_SESSION['merchant_id']." or merchant_id in (".$all_main_merchant_id.") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =".$_SESSION['merchant_id'].") or id in( select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'].") order by id desc limit 0,$num_of_records_l";
				
				$query1_l = "select count(*) total from merchant_media where media_type_id=2 and ( merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?)  or id in( select merchant_media_id from media_import_image where merchant_id=?) ) order by id desc" ;  // with prepare query
				
				$RS_l = $objDB->Conn->Execute($query_l,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id'],$_SESSION['merchant_id']));
				
				$RS1_l = $objDB->Conn->Execute($query1_l,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id'],$_SESSION['merchant_id']));
				
				$total_images_l = $RS1_l->fields['total'];
				 
		// image gallery queries
		
		// video gallery queries
				
		$query_l_v = "select * from merchant_videos where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) order by id desc limit 0,$num_of_records_l_v" ; // with prepare query
		$query1_l_v = "select count(*) total from merchant_videos where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) order by id desc" ;  // with prepare query
		
		$RS_l_v = $objDB->Conn->Execute($query_l_v,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id']));
		$RS1_l_v = $objDB->Conn->Execute($query1_l_v,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id']));
		
		$total_videos_l = $RS1_l_v->fields['total'];
		 
		// video gallery queries

				
		  //echo $RS1->fields['total'];
		  $flag_delete = true;
		  $flag_view = true;
		  $flag_upload = true;
		
		if($merchant_info->fields['merchant_parent'] != 0)
		{
			/* * **** get employee role ( which role is assigned to user ) check upload,view ,delete uplaod image permission of employee ************** */
			$media_acc_array = array();
			$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];;
			$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array);
			$media_val = unserialize($RSmedia->fields['media_access']);
			if(in_array("delete",$media_val))
			{
				$flag_delete = true;
			}
			else{
				$flag_delete = false;	
			}
			if(in_array("view-use",$media_val))
			{
				$flag_view = true;
			}
			else{
				$flag_view = false;	
			}
			if(in_array("upload",$media_val))
			{
				$flag_upload = true;
			}
			else{
				$flag_upload = false;	
			}
		}
		else{
			$flag_delete = true;
			  $flag_view = true;
			$flag_upload = true;
		}
		
		if($flag_upload)
		{
          
       ?>
       <div class="tabbable tabs-left segment-tab" style="width: 70%;float: left;">
			<div class="row">
				<div class="col-md-2" style="width: 25%;">
					<ul class="nav nav-tabs no-margin">
						<?php 
						if($_SESSION['is_video_added']==1)
						{
						?>
							<li class=""><a data-toggle="tab" href="#a" aria-expanded="true">Images</a></li>
							<li class="active"><a data-toggle="tab" href="#b" aria-expanded="false">Videos</a></li>
						<?php
						}
						else
						{
						?>
							<li class="active"><a data-toggle="tab" href="#a" aria-expanded="true">Images</a></li>
							<li class=""><a data-toggle="tab" href="#b" aria-expanded="false">Videos</a></li>
						<?php
						}
						?>
					</ul>
				</div>
				<div class="col-md-10"  style="width: 74%;">
					<div class="row">
						<div class="tab-content ">
							<?php 
							if($_SESSION['is_video_added']==1)
							{
							?>
							<div id="a" class="tab-pane">
								<div class="maindiv" align="center">
									<div id="upload_image_guideline">
										Upload image guideline
									</div>
								<?php 
								if(isset($_SESSION['msg']) && $_SESSION['msg']!="")
								{			
								?>
									<div class='msgclass upload_error_msg table_errore_message' ><?php echo $_SESSION['msg']; ?></div>
									<div class="deletemsgclass_close1" >
										X
									</div>
								<?php
								}
								else
								{					
								?>
									<div class='msgclass upload_error_msg table_errore_message' style="border: 0px solid #f7f7f7;"></div>
									<div class="deletemsgclass_close1" style="display:none;">
										X
									</div>
								<?php
								}
								?>
							   
							   
							   <form id="form" method="post" >
								<table width="100%"  border="0" style>
									<tr>
										<td style="width:150px;padding-top: 7px;vertical-align: top;">
											<select id="selectimg" onchange="set_hidden_image_type(this)">
												<option value="0" selected>Select Image Type</option>
												<option value="1" >Campaign</option>
												<option value="2" >Location</option>
												<option value="3" >Gift card</option>
											</select>
										</td>
									</tr>
								</table>
							   <div id="uploader">
									
								</div>
								
								</form>
								  
							   
							   </div>
								<div class="font_14" align="left">
									<br />
								</div>
							</div>
							
							<div id="b" class="tab-pane active">
								
								
								
								<input type="text" id="url" name="url" placeholder="Please enter YouTube or Vimeo URL" />
								<input type="button" id="btnadd" name="btnadd" value="Add Url"/>
								&nbsp;&nbsp;<input type="button" value="Submit" name="btnProcessUrl" id="btnProcessUrl">
								
								<div class='msgclass tbl_eror_msg_video' style="display:none;"></div>
								<div class="deletemsgclass_close1" style="display:none;">
										X
								</div>
								
								<div class='msgclass tbl_process_msg_video' style="display:none;">
									Validating video...
								</div>
								<div class="deleteprocessmsgclass_close1" style="display:none;">
										X
								</div>
								
								<div id="urllist">
		
								</div>
							</div>
							<?php
							}
							else
							{
							?>
							<div id="a" class="tab-pane active">
								<div class="maindiv" align="center">
									<div id="upload_image_guideline">
										Upload image guideline
									</div>
								<?php 
								if(isset($_SESSION['msg']) && $_SESSION['msg']!="")
								{			
								?>
									<div class='msgclass upload_error_msg table_errore_message' ><?php echo $_SESSION['msg']; ?></div>
									<div class="deletemsgclass_close1" >
										X
									</div>
								<?php
								}
								else
								{					
								?>
									<div class='msgclass upload_error_msg table_errore_message' style="border: 0px solid #f7f7f7;"></div>
									<div class="deletemsgclass_close1" style="display:none;">
										X
									</div>
								<?php
								}
								?>
							   
							   
							   <form id="form" method="post" >
								<table width="100%"  border="0" style>
									<tr>
										<td style="width:150px;padding-top: 7px;vertical-align: top;">
											<select id="selectimg" onchange="set_hidden_image_type(this)">
												<option value="0" selected>Select Image Type</option>
												<option value="1" >Campaign</option>
												<option value="2" >Location</option>
												<option value="3" >Gift card</option>
											</select>
										</td>
									</tr>
								</table>
							   <div id="uploader">
									
								</div>
								
								</form>
								  
							   
							   </div>
								<div class="font_14" align="left">
									<br />
								</div>
							</div>
							
							<div id="b" class="tab-pane">
								
								
								<input type="text" id="url" name="url" placeholder="Please enter YouTube or Vimeo URL" />
								<input type="button" id="btnadd" name="btnadd" value="Add Url"/>
								&nbsp;&nbsp;<input type="button" value="Submit" name="btnProcessUrl" id="btnProcessUrl">
								<div class='msgclass tbl_eror_msg_video' style="display:none;"></div>
								<div class="deletemsgclass_close1" style="display:none;">
										X
								</div>
								<div class='msgclass tbl_process_msg_video' style="display:none;">
									Validating video...
								</div>
								<div class="deleteprocessmsgclass_close1" style="display:none;">
										X
								</div>
								<div id="urllist">
		
								</div>
							</div>
							<?php
							}
							?>
						</div>	
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		else
		{
		?>
			<div><?php echo $merchant_msg["mediamanagement"]["Msg_dont_upload_permission"];?></div>
		<?php
		}
		?>
			
       </div>
		<div id="div_image" class="tabs" style="display:none;margin-top: 15px;">
			<?php
				if($flag_view)
				{
				?>
			<div align="center">
				<div class="filtergroup">
					<h3>Filter</h3>
					<div class="filters">
						<input type="button" id="imagetype_all" name="imagetype_all"  class="fltr_button fltr_selected" value="All Images" />
						<input type="button" id="imagetype_campaign" name="imagetype_campaign"  class="fltr_button" value="Campaigns" />
						<input type="button" id="imagetype_location" name="imagetype_location"  class="fltr_button" value="Locations" />
						<input type="button" id="imagetype_giftcard" name="imagetype_giftcard"  class="fltr_button" value="Gift cards" />
					</div>
				</div>
			</div>
			<?php
			//echo $RS->RecordCount();
			if($RS->RecordCount()=="0")
			{			
			?>
				<div class='deletemsgclass deletemsgclass_media table_errore_message' >
					<?php
						echo $merchant_msg["mediamanagement"]["Msg_no_images"];
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
			<div id="upload_business_logo_ajax1" class="upload_business_logo_ajax nax_scrape_indicator hidden_elem" style="display:none;">
				<img height="32" width="32" alt="" src="<?php echo ASSETS_IMG."/24" ?>.GIF" >
			</div>	
				<div class="mediya_image_container_wrapper" >
				<div id="mediya_image_container" style="display:<?php if($RS->RecordCount()>0) echo 'inline-block';else echo 'none'; ?>">
				<?php  
				if($RS->RecordCount()>0)
				{ 
				?>
					
					<?php	
					while($Row = $RS->FetchRow())
					{
						if($Row['media_type_id'] == "1")
						{
							$target = "campaign" ;
						}
						if($Row['media_type_id'] == "2")
						{
							$target = "location";
						}
						if($Row['media_type_id'] == "3")
						{
							$target = "giftcard";
						}
					?>
						<div class="mediya_img_blk <?php if($Row['media_type_id'] == "1"){echo "campaignfilter";} if($Row['media_type_id'] == "2"){echo "locationfilter";} if($Row['media_type_id'] == "3"){echo "giftcardfilter";} ?>">
							<a href="javascript:void(0)" class="mediya_camp_loca">
								<?php
								if($Row['media_type_id'] == "1")
								{
								?>
									<img title="Campaign" src="<?php echo ASSETS_IMG ?>/m/mediya_campaign.png">
								<?php
								}
								?>
								<?php
								if($Row['media_type_id'] == "2")
								{
								?>
									<img title="Location" src="<?php echo ASSETS_IMG ?>/m/mediya_location.png">
								<?php
								}
								?>
								<?php
								if($Row['media_type_id'] == "3")
								{
								?>
									<img title="Giftcard" src="<?php echo ASSETS_IMG ?>/m/mediya_giftcard.png">
								<?php
								}
								?>
							</a>
							
							<img src="<?php echo ASSETS_IMG."/m/".$target."/".$Row['image'];  ?>" class= "mediya_grid" />
							<?php
							
							//echo $Row['merchant_id'];
							
							if($flag_delete)
							{
								if($Row['merchant_id']==1)
								{
								?>
									<a href="javascript:void(0)" admin_image="1" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediadeleteid_<?=$Row['id']?>" class="mediya_delete">
										<img src="<?php echo ASSETS_IMG ?>/m/mediya_delete.png">
									</a>
								<?php 	
								}
								else
								{
									if(in_array($Row['merchant_id'], $all_main_mer_array))
									{ 
									?>
									
									<?php  
									}
									/* This logic is for syblic condtion */
									else if(!in_array($Row['merchant_id'], $all_main_mer_array) && !in_array($Row['merchant_id'], $sub_merchant) && $Row['merchant_id']!=$_SESSION['merchant_id'])
									{
									?>
										
									<?php
									}								
									else
									{
									?>
										<a href="javascript:void(0)" admin_image="0" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediadeleteid_<?=$Row['id']?>" class="mediya_delete">
											<img src="<?php echo ASSETS_IMG ?>/m/mediya_delete.png">
										</a>
									<?php 
									}
								} 
							}
							
							?>
						</div>
					<?php
					}
					?>
					
				<?php
				}
				?>
				</div>
				<?php
				if($RS->RecordCount()>0 && $total_images>$num_of_records)
				{
				?>
					<div id="mediya_showmore" style="display:block;">
						<input type="button" id="show_more_mediya" name="show_more_mediya" value="Show More" next_index="<?php echo $next_index ?>" num_of_records="<?php echo $num_of_records ?>" total_images="<?php echo $total_images ?>" />
					</div>
				<?php
				}			
				?>
				</div>
				   
				<?php }else
				{
				?>
				<div><?php echo $merchant_msg["mediamanagement"]["Msg_dont_view_permission"];?></div>
				<?php
				} ?>
			</div>
		
		<div id="div_video" class="tabs" style="display:none;margin-top: 15px;">
			
			<div align="center">
				<div class="filtergroup_video">
					<h3>Filter</h3>
					<div class="filters_video">
						<input type="button" id="videotype_all" name="videotype_all"  class="fltr_button_video fltr_selected_video" value="All Videos" />
						<input type="button" id="videotype_youtube" name="videotype_youtube"  class="fltr_button_video" value="Youtube" />
						<input type="button" id="videotype_vimeo" name="videotype_vimeo"  class="fltr_button_video" value="Vimeo" />
					</div>
				</div>
			</div>
			<?php
			//echo $RS->RecordCount();
			if($RS_v->RecordCount()=="0")
			{			
			?>
				<div class='deletemsgclass deletemsgclass_media table_errore_message' >
					<?php
						echo $merchant_msg["mediamanagement"]["Msg_no_videos"];
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
			<div id="upload_business_logo_ajax1" class="upload_business_logo_ajax nax_scrape_indicator hidden_elem" style="display:none;">
				<img height="32" width="32" alt="" src="<?php echo ASSETS_IMG."/24" ?>.GIF" >
			</div>	
			<div class="mediya_video_container_wrapper" >
			<div id="mediya_video_container" style="display:<?php if($RS_v->RecordCount()>0) echo 'inline-block';else echo 'none'; ?>">
			<?php  
			if($RS_v->RecordCount()>0)
			{ 
				while($Row = $RS_v->FetchRow())
				{	
				?>
					<div class="mediya_video_blk <?php if($Row['video_type'] == "youtube"){echo "youtubefilter";} if($Row['video_type'] == "vimeo"){echo "vimeofilter";}?>">
						<a href="<?php echo $Row['video_link'];  ?>" target="_new" >
							<img src="<?php echo $Row['thumbnail_url'];  ?>" class= "mediya_grid_video" />
						</a>
						<a href="javascript:void(0)" image_type=<?=$Row['video_type']?> id="videodeleteid_<?=$Row['id']?>" class="mediya_delete">
							<img src="<?php echo ASSETS_IMG ?>/m/mediya_delete.png">
						</a>
						<?php
						
						//echo $Row['merchant_id'];
						
						
						
						?>
					</div>
				<?php
				}
			}
			?>
			</div>
			<?php
			if($RS_v->RecordCount()>0 && $total_videos>$num_of_records)
			{
			?>
				<div id="mediya_showmore_videos" style="display:block;">
					<input type="button" id="show_more_videos" name="show_more_videos" value="Show More" next_index="<?php echo $next_index ?>" num_of_records="<?php echo $num_of_records ?>" total_videos="<?php echo $total_videos ?>" />
				</div>
			<?php
			}			
			?>
			</div>
		</div>
		<div id="div_library" class="tabs" style="display:none;margin-top: 15px;">
			<?php
				$start_index_admin = 0;
				$num_of_records_admin = 16;
				$next_index_admin = $start_index_admin + $num_of_records_admin;
				
				//$query_admin = "select * from merchant_media where merchant_id=1 and id not in(select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'].") order by id desc limit 0,$num_of_records_admin" ;
				$query_admin = "select * from merchant_media where merchant_id=1 and id not in(select merchant_media_id from media_import_image where merchant_id=?) order by id desc limit 0,$num_of_records_admin" ; // with prepare
				//$query1_admin = "select count(*) total from merchant_media where merchant_id=1 and id not in(select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'].") order by id desc" ;
				$query1_admin = "select count(*) total from merchant_media where merchant_id=1 and id not in(select merchant_media_id from media_import_image where merchant_id=?) order by id desc" ; // with prepare
				
				$myQuery_admin = $query_admin;    
				//$RS_admin = $objDB->execute_query($query_admin);
				$RS_admin = $objDB->Conn->Execute($query_admin,array($_SESSION['merchant_id']));
				//$RS1_admin = $objDB->execute_query($query1_admin);
				$RS1_admin = $objDB->Conn->Execute($query1_admin,array($_SESSION['merchant_id']));
				  
				$total_images_admin = $RS1_admin->fields['total'];
				
			?>
			<div align="center" style="display:none;">
				<div class="filtergroup_admin">
					<h3>Filter</h3>
						<div class="filters_admin">
							<input type="button" id="imagetype_all_admin" name="imagetype_all_admin"  class="fltr_button_admin fltr_selected_admin" value="All Images" />
							<input type="button" id="imagetype_campaign_admin" name="imagetype_campaign_admin"  class="fltr_button_admin" value="Campaigns" />
							<input type="button" id="imagetype_location_admin" name="imagetype_location_admin"  class="fltr_button_admin" value="Locations" />
						</div>
				</div>
			</div>
			<?php
			if($RS_admin->RecordCount()=="0")
			{			
			?>
				<div class='deletemsgclass deletemsgclass_media table_errore_message' >
					<?php
					if($RS_admin->RecordCount()==0)
					{
						echo $merchant_msg["mediamanagement"]["Msg_no_images"];
					}
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
			
			if($RS_admin->RecordCount()>0)
			{
			?>
				<div id="mediya_image_container_admin">
				<?php	
				while($Row = $RS_admin->FetchRow())
				{
					if($Row['media_type_id'] == "1")
					{
						$target = "campaign" ;
					}
					if($Row['media_type_id'] == "2")
					{
						$target = "location";
					}
				?>
				<div class="mediya_img_blk <?php if($Row['media_type_id'] == "1"){echo "campaignfilter";} if($Row['media_type_id'] == "2"){echo "locationfilter";}?>">
					<a href="javascript:void(0)" class="mediya_camp_loca">
						<?php
						if($Row['media_type_id'] == "1")
						{
						?>
							<img title="Campaign" src="<?php echo ASSETS_IMG ?>/m/mediya_campaign.png">
						<?php
						}
						?>
						<?php
						if($Row['media_type_id'] == "2")
						{
						?>
							<img title="Location" src="<?php echo ASSETS_IMG ?>/m/mediya_location.png">
						<?php
						}
						?>
						
					</a>
					<img src="<?php echo ASSETS_IMG."/m/".$target."/".$Row['image'];  ?>" class= "mediya_grid" />
					<a title="Add to My Images" href="javascript:void(0)" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediaadd_<?=$Row['id']?>" class="mediya_add">
						<img src="<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png">
						<span>Add to My Images</span>
					</a>
				</div>
				<?php
				}
				?>
			</div>
			<?php
			}
			?>
			<?php
			if($RS_admin->RecordCount()>0 && $total_images_admin>$num_of_records_admin)
			{
			?>
				<div id="mediya_showmore_admin" style="display:block;">
					<input type="button" id="show_more_mediya_admin" name="show_more_mediya_admin" value="Show More" next_index="<?php echo $next_index_admin ?>" num_of_records="<?php echo $num_of_records_admin ?>" total_images="<?php echo $total_images_admin ?>" />
				</div>
			<?php
			}
			?>
		</div>	
					
	<div id="div_imagegallery" class="tabs" style="display:block;margin-top: 15px;">
		<?php
			if($flag_view)
			{
				//echo $RS->RecordCount();
				if($RS->RecordCount()=="0")
				{			
				?>
					<div class='deletemsgclass deletemsgclass_media table_errore_message' >
						<?php
							echo $merchant_msg["mediamanagement"]["Msg_no_images"];
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
				<div id="gallery_list">
					<div class="gallery_list_up">
						<!--<input type="button" id="add_location_gallery" name="add_location_gallery" value="Add Image Gallery"  />-->
						<h4 class="add_new_location" align="right">
							<a id="add_location_gallery" name="add_location_gallery" href="javascript:void(0);">Add Media Gallery</a>
						</h4>
					</div>
					<div class="gallery_list_down">
						
						<table border="0" cellspacing="1" cellpadding="10" class="tableMerchant" id="manage_loc_gallery_table" style="display:none;">
								<thead>
									<tr>
										<td colspan="4" align="left" class="table_errore_message">
											<?php echo (isset($_SESSION['msg'])) ? $_SESSION['msg'] : ''; ?>&nbsp;
										</td>
									</tr>
									<tr>
										<th>Location</th>
										<th>Video Count</th>
										<th>Image Count</th>
										<th>Actions</th>
									</tr>
								</thead>

						</table>	
					</div>
				</div>
				<div id="gallery_add" style="display:none;">
					<h3>Add Media Gallery</h3>
					<div id="mediya_assigntolocation" style="display:block;">
						<input type="button" value="Save Location Media Gallery" name="assign_gallery" id="assign_gallery" disabled class="disabled">&nbsp;&nbsp;
						<input type="button" value="Cancel" name="cancel_Add_gallery" id="cancel_Add_gallery">
					</div>
					
						
					<div class="drpdwndiv" >
						<div class="lmg_lbl">
							<b>
								<?php echo $merchant_msg['report']['Campaign_Filter_Loactions']; ?>
							</b>
						</div>
						<div class="lmg_cntrl">
							<?php
								$arr_loc = file(WEB_PATH . '/merchant/process.php?btnGetlocatioidwithgallery=yes');
								if (trim($arr_loc[0]) == "") {
									unset($arr_loc[0]);
									$arr_loc = array_values($arr_loc);
								}
								$json_loc = json_decode($arr_loc[0]);
								$records_array_loc = $json_loc->records;
							?>
							<select id="opt_filter_location">
								<option value="0" selected="selected" > --- Select Location ---</option>
								<?php
								 if ($_SESSION['merchant_info']['merchant_parent'] != 0) {
									$media_acc_array = array();
									$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
									$RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
									$location_val = $RSmedia->fields['location_access'];
									$arr1 = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $_SESSION['merchant_id'] . '&loc_id=' . $location_val);
							} else {
									$arr1 = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $_SESSION['merchant_id']);
							}

							if (trim($arr1[0]) == "") {
									unset($arr1[0]);
									$arr1 = array_values($arr1);
							}
							$json1 = json_decode($arr1[0]);
							$total_records2 = $json1->total_records;
							$records_array2 = $json1->records;
								
								if ($total_records2 > 0) {
										$cnt = 0;
										foreach ($records_array2 as $Row) {
											if(in_array($Row->id,$records_array_loc))
											{
											}
											else
											{
												if ($Row->location_name == "") {
														$locname = $Row->address . " - " . $Row->zip;
												} else {
														$locname = $Row->location_name . " - " . $Row->zip;
												}
												
												$array_where = array();
												$array_where['id'] = $Row->state;
												$RS_state = $objDB->Show("state", $array_where);

												$array_where = array();
												$array_where['id'] = $Row->city;
												$RS_city = $objDB->Show("city", $array_where);
			
												$location_string = $Row->address . ", " . $RS_city->fields['name'] . ", " . $RS_state->fields['short_form'] . ", " . $Row->zip;
												$location_string = (strlen($location_string) > 57) ? substr($location_string, 0, 57) . '...' : $location_string;
												?>
												<option title="<?php echo $Row->address . ", " . $RS_city->fields['name'] . ", " . $RS_state->fields['short_form'] . ", " . $Row->zip; ?>" value="<?php echo $Row->id; ?>" >
												<?php echo $location_string; ?> 
												</option>
												<?php
											}
										}
								}
								?>
							</select>
						</div>
					</div>
					
					
					<div class="tabbable tabs-left segment-tab" >
						<div class="row">
							<div class="col-md-2" style="width: 25%;">
								<ul class="nav nav-tabs no-margin">
									<li class="active"><a data-toggle="tab" href="#c" aria-expanded="true">Images</a></li>
									<li class=""><a data-toggle="tab" href="#d" aria-expanded="false">Videos</a></li>
								</ul>
							</div>
							<div class="col-md-10"  style="width: 74%;">
								<div class="row">
									<div class="tab-content ">
										<div id="c" class="tab-pane active">
											<div class="tabdiv">
												<div class="tabdiv_erormsg" style="display:<?php if($RS_l->RecordCount()==0) echo 'block';else echo 'none'; ?>">
													No images available to add.
												</div>
												<div id="mediya_image_container_gallery" style="display:<?php if($RS_l->RecordCount()>0) echo 'inline-block';else echo 'none'; ?>">
												
													<?php  
													
													if($RS_l->RecordCount()>0)
													{ 
														$RS_l->MoveFirst();
														while($Row = $RS_l->FetchRow())
														{
															if($Row['media_type_id'] == "2")
															{
																if($Row['media_type_id'] == "1")
																{
																	$target = "campaign" ;
																}
																if($Row['media_type_id'] == "2")
																{
																	$target = "location";
																}
																if($Row['media_type_id'] == "3")
																{
																	$target = "giftcard";
																}
															?>
															<div class="mediya_img_blk <?php if($Row['media_type_id'] == "1"){echo "campaignfilter";} if($Row['media_type_id'] == "2"){echo "locationfilter";} if($Row['media_type_id'] == "3"){echo "giftcardfilter";} ?>">
																<a href="javascript:void(0)" class="mediya_camp_loca" style="display: none;">
																	<?php
																	if($Row['media_type_id'] == "1")
																	{
																	?>
																		<img title="Campaign" src="<?php echo ASSETS_IMG ?>/m/mediya_campaign.png">
																	<?php
																	}
																	?>
																	<?php
																	if($Row['media_type_id'] == "2")
																	{
																	?>
																		<img title="Location" src="<?php echo ASSETS_IMG ?>/m/mediya_location.png">
																	<?php
																	}
																	?>
																	<?php
																	if($Row['media_type_id'] == "3")
																	{
																	?>
																		<img title="Giftcard" src="<?php echo ASSETS_IMG ?>/m/mediya_giftcard.png">
																	<?php
																	}
																	?>
																</a>
										
																<img src="<?php echo ASSETS_IMG."/m/".$target."/".$Row['image'];  ?>" class= "mediya_grid" />
																
																	<img class="mediya_grid1_img_selected mediya_slctd" src="<?php echo ASSETS_IMG ?>/m/image_Added_to_lbry.png">
																
																<a title="Select Image" href="javascript:void(0)" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediaselect_<?=$Row['id']?>" class="mediya_select">
																	<img src="<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png">
																	<span>Select Image</span>
																</a>
															</div>
														<?php
															}
														}
														//echo $RS_l->RecordCount()."-".$total_images_l."-".$num_of_records_l;
														
															
													}
													?>
													
												</div>
											</div>
										<?php
										if($RS_l->RecordCount()>0 && $total_images_l>$num_of_records_l)
												{
												?>
													<div id="mediya_showmore" style="display:block;">
														<input type="button" id="show_more_mediya_gallery" name="show_more_mediya_gallery" value="Show More" next_index="<?php echo $next_index_l ?>" num_of_records="<?php echo $num_of_records_l ?>" total_images="<?php echo $total_images_l ?>" />
													</div>
													
												<?php
												}
												?>
										</div>
										<div id="d" class="tab-pane">
											<div class="tabdiv">
												<div class="tabdiv_erormsg" style="display:<?php if($RS_l_v->RecordCount()==0) echo 'block';else echo 'none'; ?>">
													No videos available to add.
												</div>
												<div id="mediya_video_container_gallery" style="display:<?php if($RS_l_v->RecordCount()>0) echo 'inline-block';else echo 'none'; ?>">
													<?php  
													if($RS_l_v->RecordCount()>0)
													{ 
														while($Row = $RS_l_v->FetchRow())
														{	
														?>
															<div class="mediya_video_blk" >
																
																	<img src="<?php echo $Row['thumbnail_url'];  ?>" class= "mediya_grid_video" />
																
																<img class="mediya_grid1_img_selected mediya_slctd" src="<?php echo ASSETS_IMG ?>/m/image_Added_to_lbry.png">
																
																<a title="Select Video" href="javascript:void(0)" id="mediaselectvideo_<?=$Row['id']?>" class="mediya_select">
																	<img src="<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png">
																	<span>Select Video</span>
																</a>
															</div>
														<?php
														}
													}
													?>
												</div>
											</div>
										
											<?php
										if($RS_l_v->RecordCount()>0 && $total_videos_l>$num_of_records_l_v)
												{
												?>
													<div id="mediya_video_showmore" style="display:block;">
														<input type="button" id="show_more_mediya_video_gallery" name="show_more_mediya_video_gallery" value="Show More" next_index="<?php echo $next_index_l_v ?>" num_of_records="<?php echo $num_of_records_l_v ?>" total_videos="<?php echo $total_videos_l ?>" />
													</div>
													
												<?php
												}
												?>
										</div>
									</div>	
								</div>
							</div>
						</div>
					</div>
					
					<input type="hidden" id="hdnimageids" name="hdnimageids" />
					<input type="hidden" id="hdnimageids_assign" name="hdnimageids_assign" />
					<input type="hidden" id="hdnimageids_unassign" name="hdnimageids_unassign" />	
					
					<input type="hidden" id="hdnvideoids" name="hdnvideoids" />
					<input type="hidden" id="hdnvideoids_assign" name="hdnvideoids_assign" />
					<input type="hidden" id="hdnvideoids_unassign" name="hdnvideoids_unassign" />			
					
				<?php
			}
			else
			{
			?>
				<div><?php echo $merchant_msg["mediamanagement"]["Msg_dont_view_permission"];?></div>
			<?php
			} 
			?>	
		
	</div>

<!--end of all_div_container--></div>
		<?php
		}
		else
		{
		?>
		<div id="div_upload" class="tabs" style="display:block;">
		
		<?php
       
        $arr_1=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
		if(trim($arr_1[0]) == "")
		{
		 unset($arr_1[0]);
		 $arr_1 = array_values($arr_1);
		}
		$json_1 = json_decode($arr_1[0]);
		$main_merchant_id = $json_1->main_merchant_id;
		  
			   $arr=file(WEB_PATH.'/merchant/process.php?getallsubmercahnt_id=yes&mer_id='.$main_merchant_id);
		if(trim($arr[0]) == "")
		{
				unset($arr[0]);
				$arr = array_values($arr);
		}
		$json = json_decode($arr[0]);
		$ids= $json->all_sub_merchant_id;
		if($ids == "")
		  {
			  $ids =$_SESSION['merchant_id'];
		  }
		  

		/* * ******* get all sub merchant of current login employee **************** */
			   $arr_2=file(WEB_PATH.'/merchant/process.php?getallsubmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
			   
		if(trim($arr_2[0]) == "")
		{
				unset($arr_2[0]);
				$arr_2 = array_values($arr_2);
		}
		$json_2 = json_decode($arr_2[0]);
		$sub_merchant_string= $json_2->all_sub_merchant_id;

		$sub_merchant=  explode(',', $sub_merchant_string);

		if($sub_merchant == "")
		  {
			  $sub_merchant =$_SESSION['merchant_id'];
		  }

		/* * ******* get all main merchant of current login employee **************** */
		 $arr=file(WEB_PATH.'/merchant/process.php?getallmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
					 
		 if(trim($arr[0]) == "")
		 {
		  unset($arr[0]);
		  $arr = array_values($arr);
		 }
		 $json = json_decode($arr[0]);
				
		  $all_main_merchant_id= $json->all_main_merchant_id; 
		  
		  $all_main_mer_array = array ();
		  
		  if($all_main_merchant_id == "")
		  {
			  $all_main_merchant_id =$_SESSION['merchant_id'];
		  }
		  else
		  {
			  $all_main_mer_array=explode(",",$all_main_merchant_id);
		  }    
  
		 
		/****
		- get all media images
		- If merchant is login then He will able see all images uploaded by own and as well his all employess uploaded images
		- If employee is login then He will able see all images uploaded by own and as well his all employess uploaded images and his parent merchant's uploaded images
		******/

         $merchant_array = array();
		$merchant_array['id'] = $_SESSION['merchant_id'];
		$merchant_info = $objDB->Show("merchant_user",$merchant_array);
		
		$start_index = 0;
		$num_of_records = 16;
		$num_of_records_l = 16;
		$num_of_records_l_v = 16;
		$next_index = $start_index + $num_of_records;
		$next_index_l = $start_index + $num_of_records_l;
		$next_index_l_v = $start_index + $num_of_records_l_v;
			if(isset($_REQUEST['imagetype']))
			{
				if($_REQUEST['imagetype']!='all')
				{
					$query = "select * from merchant_media where image_type='".$_REQUEST['imagetype']."' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'] ." or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .")) order by id desc limit 0,$num_of_records" ;
					$query1 = "select count(*) total from merchant_media where image_type='".$_REQUEST['imagetype']."' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'] ." or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .")) order by id desc" ;
				}
				else
				{
					$query = "select * from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (".$all_main_merchant_id.") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") order by id desc limit 0,$num_of_records" ;
					$query1 = "select count(*) total from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (".$all_main_merchant_id.") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") order by id desc" ;
				}
			}
			else 
			{
				//$query = "select * from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (". $all_main_merchant_id .") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") order by id desc limit 0,$num_of_records" ;
				// 12 01 2015 to add import images from admin
				//$query = "select * from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (". $all_main_merchant_id .") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") or id in( select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'] .") order by id desc limit 0,$num_of_records" ;
				$query = "select * from merchant_media where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) or id in( select merchant_media_id from media_import_image where merchant_id=?) order by id desc limit 0,$num_of_records" ; // with prepare query				
				// 12 01 2015 to add import images from admin
				
				
				//$query1 = "select count(*) total from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (". $all_main_merchant_id .") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .") order by id desc" ;
				// 12 01 2015 to add import images from admin
				//$query1 = "select count(*) total from merchant_media where merchant_id=".$_SESSION['merchant_id'] ." or merchant_id in (". $all_main_merchant_id .") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =". $_SESSION['merchant_id'] .")  or id in( select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'] .") order by id desc" ;
				$query1 = "select count(*) total from merchant_media where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?)  or id in( select merchant_media_id from media_import_image where merchant_id=?) order by id desc" ;  // with prepare query
				// 12 01 2015 to add import images from admin
				
				// video query
				$query_v = "select * from merchant_videos where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) order by id desc limit 0,$num_of_records" ; // with prepare query
				$query1_v = "select count(*) total from merchant_videos where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) order by id desc" ;  // with prepare query
				// video query
				
			}
                    
          //echo $query;
		  $myQuery = $query;    
		  //$RS = $objDB->execute_query($query);
		  $RS = $objDB->Conn->Execute($query,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id'],$_SESSION['merchant_id']));
		  //$RS1 = $objDB->execute_query($query1);
		  $RS1 = $objDB->Conn->Execute($query1,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id'],$_SESSION['merchant_id']));
		 
		  $total_images = $RS1->fields['total'];
		  
		  // video query
			$RS_v = $objDB->Conn->Execute($query_v,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id']));
			$RS1_v = $objDB->Conn->Execute($query1_v,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id']));
			$total_videos = $RS1_v->fields['total'];
		  // video query
		  
		  // image gallery queries
				
				$query_l = "select * from merchant_media where media_type_id=2 and ( merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) or id in( select merchant_media_id from media_import_image where merchant_id=?) ) order by id desc limit 0,$num_of_records_l" ; // with prepare query				
				
				//echo "select * from merchant_media where merchant_id=".$_SESSION['merchant_id']." or merchant_id in (".$all_main_merchant_id.") or merchant_id in (".$ids.") or merchant_id in (select id from merchant_user where merchant_parent =".$_SESSION['merchant_id'].") or id in( select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'].") order by id desc limit 0,$num_of_records_l";
				
				$query1_l = "select count(*) total from merchant_media where media_type_id=2 and ( merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?)  or id in( select merchant_media_id from media_import_image where merchant_id=?) ) order by id desc" ;  // with prepare query
				
				$RS_l = $objDB->Conn->Execute($query_l,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id'],$_SESSION['merchant_id']));
				
				$RS1_l = $objDB->Conn->Execute($query1_l,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id'],$_SESSION['merchant_id']));
				
				$total_images_l = $RS1_l->fields['total'];
				 
		// image gallery queries
		
		// video gallery queries
				
				$query_l_v = "select * from merchant_videos where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) order by id desc limit 0,$num_of_records_l_v" ; // with prepare query
				$query1_l_v = "select count(*) total from merchant_videos where merchant_id=? or merchant_id in (?) or merchant_id in (?) or merchant_id in (select id from merchant_user where merchant_parent =?) order by id desc" ;  // with prepare query
				
				$RS_l_v = $objDB->Conn->Execute($query_l_v,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id']));
				$RS1_l_v = $objDB->Conn->Execute($query1_l_v,array($_SESSION['merchant_id'],$all_main_merchant_id,$ids,$_SESSION['merchant_id']));
				
				$total_videos_l = $RS1_l_v->fields['total'];
				 
		// video gallery queries
		
				
		  //echo $RS1->fields['total'];
		  $flag_delete = true;
		  $flag_view = true;
		  $flag_upload = true;
		
		if($merchant_info->fields['merchant_parent'] != 0)
		{
			/* * **** get employee role ( which role is assigned to user ) check upload,view ,delete uplaod image permission of employee ************** */
			$media_acc_array = array();
			$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];;
			$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array);
			$media_val = unserialize($RSmedia->fields['media_access']);
			if(in_array("delete",$media_val))
			{
				$flag_delete = true;
			}
			else{
				$flag_delete = false;	
			}
			if(in_array("view-use",$media_val))
			{
				$flag_view = true;
			}
			else{
				$flag_view = false;	
			}
			if(in_array("upload",$media_val))
			{
				$flag_upload = true;
			}
			else{
				$flag_upload = false;	
			}
		}
		else{
			$flag_delete = true;
			  $flag_view = true;
			$flag_upload = true;
		}
		
		if($flag_upload)
		{
          
       ?>
       
       <div class="tabbable tabs-left segment-tab" style="width: 70%;float: left;">
			<div class="row">
				<div class="col-md-2" style="width: 25%;">
					<ul class="nav nav-tabs no-margin">
						<?php 
						if($_SESSION['is_video_added']==1)
						{
						?>
							<li class=""><a data-toggle="tab" href="#a" aria-expanded="true">Images</a></li>
							<li class="active"><a data-toggle="tab" href="#b" aria-expanded="false">Videos</a></li>
						<?php
						}
						else
						{
						?>
							<li class="active"><a data-toggle="tab" href="#a" aria-expanded="true">Images</a></li>
							<li class=""><a data-toggle="tab" href="#b" aria-expanded="false">Videos</a></li>
						<?php
						}
						?>
					</ul>
				</div>
				<div class="col-md-10"  style="width: 74%;">
					<div class="row">
						<div class="tab-content ">
							<?php 
							if($_SESSION['is_video_added']==1)
							{
							?>
							<div id="a" class="tab-pane">
								<div class="maindiv" align="center">
									<div id="upload_image_guideline">
										Upload image guideline
									</div>
								<?php 
								if(isset($_SESSION['msg']) && $_SESSION['msg']!="")
								{			
								?>
									<div class='msgclass upload_error_msg table_errore_message' ><?php echo $_SESSION['msg']; ?></div>
									<div class="deletemsgclass_close1" >
										X
									</div>
								<?php
								}
								else
								{					
								?>
									<div class='msgclass upload_error_msg table_errore_message' style="border: 0px solid #f7f7f7;"></div>
									<div class="deletemsgclass_close1" style="display:none;">
										X
									</div>
								<?php
								}
								?>
							   
							   
							   <form id="form" method="post" >
								<table width="100%"  border="0" style>
									<tr>
										<td style="width:150px;padding-top: 7px;vertical-align: top;">
											<select id="selectimg" onchange="set_hidden_image_type(this)">
												<option value="0" selected>Select Image Type</option>
												<option value="1" >Campaign</option>
												<option value="2" >Location</option>
												<option value="3" >Gift card</option>
											</select>
										</td>
									</tr>
								</table>
							   <div id="uploader">
									
								</div>
								
								</form>
								  
							   
							   </div>
								<div class="font_14" align="left">
									<br />
								</div>
							</div>
							
							<div id="b" class="tab-pane active">
								
								
								<input type="text" id="url" name="url" placeholder="Please enter YouTube or Vimeo URL" />
								<input type="button" id="btnadd" name="btnadd" value="Add Url"/>
								&nbsp;&nbsp;<input type="button" value="Submit" name="btnProcessUrl" id="btnProcessUrl">
								<div class='msgclass tbl_eror_msg_video' style="display:none;"></div>
								<div class="deletemsgclass_close1" style="display:none;">
										X
								</div>
								<div class='msgclass tbl_process_msg_video' style="display:none;">
									Validating video...
								</div>
								<div class="deleteprocessmsgclass_close1" style="display:none;">
										X
								</div>
								<div id="urllist">
		
								</div>
							</div>
							<?php
							}
							else
							{
							?>
							<div id="a" class="tab-pane active">
								<div class="maindiv" align="center">
									<div id="upload_image_guideline">
										Upload image guideline
									</div>
								<?php 
								if(isset($_SESSION['msg']) && $_SESSION['msg']!="")
								{			
								?>
									<div class='msgclass upload_error_msg table_errore_message' ><?php echo $_SESSION['msg']; ?></div>
									<div class="deletemsgclass_close1" >
										X
									</div>
								<?php
								}
								else
								{					
								?>
									<div class='msgclass upload_error_msg table_errore_message' style="border: 0px solid #f7f7f7;"></div>
									<div class="deletemsgclass_close1" style="display:none;">
										X
									</div>
								<?php
								}
								?>
							   
							   
							  <form id="form" method="post" >
								<table width="100%"  border="0" style>
									<tr>
										<td style="width:150px;padding-top: 7px;vertical-align: top;">
											<select id="selectimg" onchange="set_hidden_image_type(this)">
												<option value="0" selected>Select Image Type</option>
												<option value="1" >Campaign</option>
												<option value="2" >Location</option>
												<option value="3" >Gift card</option>
											</select>
										</td>
									</tr>
								</table>
							   <div id="uploader">
									
								</div>
								
								</form>
								  
							   
							   </div>
								<div class="font_14" align="left">
									<br />
								</div>
							</div>
							
							<div id="b" class="tab-pane">
								
								
								<input type="text" id="url" name="url" placeholder="Please enter YouTube or Vimeo URL" />
								<input type="button" id="btnadd" name="btnadd" value="Add Url"/>
								&nbsp;&nbsp;<input type="button" value="Submit" name="btnProcessUrl" id="btnProcessUrl">
								<div class='msgclass tbl_eror_msg_video' style="display:none;"></div>
								<div class="deletemsgclass_close1" style="display:none;">
										X
								</div>
								<div class='msgclass tbl_process_msg_video' style="display:none;">
									Validating video...
								</div>
								<div class="deleteprocessmsgclass_close1" style="display:none;">
										X
								</div>
								<div id="urllist">
		
								</div>
							</div>
							<?php
							}
							?>
						</div>	
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		else
		{
		?>
			<div><?php echo $merchant_msg["mediamanagement"]["Msg_dont_upload_permission"];?></div>
		<?php
		}
		?>
		</div>
		<div id="div_image" class="tabs" style="display:none;margin-top: 15px;">
			<?php
				if($flag_view)
				{
				?>
			<div align="center">
				<div class="filtergroup">
					<h3>Filter</h3>
					<div class="filters">
						<input type="button" id="imagetype_all" name="imagetype_all"  class="fltr_button fltr_selected" value="All Images" />
						<input type="button" id="imagetype_campaign" name="imagetype_campaign"  class="fltr_button" value="Campaigns" />
						<input type="button" id="imagetype_location" name="imagetype_location"  class="fltr_button" value="Locations" />
						<input type="button" id="imagetype_giftcard" name="imagetype_giftcard"  class="fltr_button" value="Gift cards" />
					</div>
				</div>
			</div>
			<?php
			//echo $RS->RecordCount();
			if($RS->RecordCount()=="0")
			{			
			?>
				<div class='deletemsgclass deletemsgclass_media table_errore_message' >
					<?php
						echo $merchant_msg["mediamanagement"]["Msg_no_images"];
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
			<div id="upload_business_logo_ajax1" class="upload_business_logo_ajax nax_scrape_indicator hidden_elem" style="display:none;">
				<img height="32" width="32" alt="" src="<?php echo ASSETS_IMG."/24" ?>.GIF" >
			</div>	
				<div class="mediya_image_container_wrapper" >
				<div id="mediya_image_container" style="display:<?php if($RS->RecordCount()>0) echo 'inline-block';else echo 'none'; ?>">
				<?php  
				if($RS->RecordCount()>0)
				{ 
				?>
					
					<?php	
					while($Row = $RS->FetchRow())
					{
						if($Row['media_type_id'] == "1")
						{
							$target = "campaign" ;
						}
						if($Row['media_type_id'] == "2")
						{
							$target = "location";
						}
						if($Row['media_type_id'] == "3")
						{
							$target = "giftcard";
						}
					?>
						<div class="mediya_img_blk <?php if($Row['media_type_id'] == "1"){echo "campaignfilter";} if($Row['media_type_id'] == "2"){echo "locationfilter";} if($Row['media_type_id'] == "3"){echo "giftcardfilter";} ?>">
							<a href="javascript:void(0)" class="mediya_camp_loca">
								<?php
								if($Row['media_type_id'] == "1")
								{
								?>
									<img title="Campaign" src="<?php echo ASSETS_IMG ?>/m/mediya_campaign.png">
								<?php
								}
								?>
								<?php
								if($Row['media_type_id'] == "2")
								{
								?>
									<img title="Location" src="<?php echo ASSETS_IMG ?>/m/mediya_location.png">
								<?php
								}
								?>
								<?php
								if($Row['media_type_id'] == "3")
								{
								?>
									<img title="Giftcard" src="<?php echo ASSETS_IMG ?>/m/mediya_giftcard.png">
								<?php
								}
								?>
							</a>
							<img src="<?php echo ASSETS_IMG."/m/".$target."/".$Row['image'];  ?>" class= "mediya_grid" />
							<?php
							
							//echo $Row['merchant_id'];
							
							if($flag_delete)
							{
								if($Row['merchant_id']==1)
								{
								?>
									<a href="javascript:void(0)" admin_image="1" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediadeleteid_<?=$Row['id']?>" class="mediya_delete">
										<img src="<?php echo ASSETS_IMG ?>/m/mediya_delete.png">
									</a>
								<?php 	
								}
								else
								{
									if(in_array($Row['merchant_id'], $all_main_mer_array))
									{ 
									?>
									
									<?php  
									}
									/* This logic is for syblic condtion */
									else if(!in_array($Row['merchant_id'], $all_main_mer_array) && !in_array($Row['merchant_id'], $sub_merchant) && $Row['merchant_id']!=$_SESSION['merchant_id'])
									{
									?>
										
									<?php
									}								
									else
									{
									?>
										<a href="javascript:void(0)" admin_image="0" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediadeleteid_<?=$Row['id']?>" class="mediya_delete">
											<img src="<?php echo ASSETS_IMG ?>/m/mediya_delete.png">
										</a>
									<?php 
									}
								} 
							}
							
							?>
						</div>
					<?php
					}
					?>
					
				<?php
				}
				?>
				</div>
				<?php
				if($RS->RecordCount()>0 && $total_images>$num_of_records)
				{
				?>
					<div id="mediya_showmore" style="display:block;">
						<input type="button" id="show_more_mediya" name="show_more_mediya" value="Show More" next_index="<?php echo $next_index ?>" num_of_records="<?php echo $num_of_records ?>" total_images="<?php echo $total_images ?>" />
					</div>
				<?php
				}			
				?>
				</div>
				   
				<?php }else
				{
				?>
				<div><?php echo $merchant_msg["mediamanagement"]["Msg_dont_view_permission"];?></div>
				<?php
				} ?>
			</div>
		<div id="div_video" class="tabs" style="display:none;margin-top: 15px;">
			<div align="center">
				<div class="filtergroup_video">
					<h3>Filter</h3>
					<div class="filters_video">
						<input type="button" id="videotype_all" name="videotype_all"  class="fltr_button_video fltr_selected_video" value="All Videos" />
						<input type="button" id="videotype_youtube" name="videotype_youtube"  class="fltr_button_video" value="Youtube" />
						<input type="button" id="videotype_vimeo" name="videotype_vimeo"  class="fltr_button_video" value="Vimeo" />
					</div>
				</div>
			</div>
			<?php
			//echo $RS->RecordCount();
			if($RS_v->RecordCount()=="0")
			{			
			?>
				<div class='deletemsgclass deletemsgclass_media table_errore_message' >
					<?php
						echo $merchant_msg["mediamanagement"]["Msg_no_videos"];
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
			<div id="upload_business_logo_ajax1" class="upload_business_logo_ajax nax_scrape_indicator hidden_elem" style="display:none;">
				<img height="32" width="32" alt="" src="<?php echo ASSETS_IMG."/24" ?>.GIF" >
			</div>	
			<div class="mediya_video_container_wrapper" >
			<div id="mediya_video_container" style="display:<?php if($RS_v->RecordCount()>0) echo 'inline-block';else echo 'none'; ?>">
			<?php  
			if($RS_v->RecordCount()>0)
			{ 
				while($Row = $RS_v->FetchRow())
				{	
				?>
					<div class="mediya_video_blk <?php if($Row['video_type'] == "youtube"){echo "youtubefilter";} if($Row['video_type'] == "vimeo"){echo "vimeofilter";}?>">
						<a href="<?php echo $Row['video_link'];  ?>" target="_new" >
							<img src="<?php echo $Row['thumbnail_url'];  ?>" class= "mediya_grid_video" />
						</a>
						<a href="javascript:void(0)" image_type=<?=$Row['video_type']?> id="videodeleteid_<?=$Row['id']?>" class="mediya_delete">
							<img src="<?php echo ASSETS_IMG ?>/m/mediya_delete.png">
						</a>
						<?php
						
						//echo $Row['merchant_id'];
						
						
						
						?>
					</div>
				<?php
				}
			}
			?>
			</div>
			<?php
			if($RS_v->RecordCount()>0 && $total_videos>$num_of_records)
			{
			?>
				<div id="mediya_showmore_videos" style="display:block;">
					<input type="button" id="show_more_videos" name="show_more_videos" value="Show More" next_index="<?php echo $next_index ?>" num_of_records="<?php echo $num_of_records ?>" total_videos="<?php echo $total_videos ?>" />
				</div>
			<?php
			}			
			?>
			</div>
		</div>
		<div id="div_library" class="tabs" style="display:none;margin-top: 15px;">
			<?php
				$start_index_admin = 0;
				$num_of_records_admin = 16;
				$next_index_admin = $start_index_admin + $num_of_records_admin;
				
				//$query_admin = "select * from merchant_media where merchant_id=1 and id not in(select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'].") order by id desc limit 0,$num_of_records_admin" ;
				$query_admin = "select * from merchant_media where merchant_id=1 and id not in(select merchant_media_id from media_import_image where merchant_id=?) order by id desc limit 0,$num_of_records_admin" ; // with prepare
				//$query1_admin = "select count(*) total from merchant_media where merchant_id=1 and id not in(select merchant_media_id from media_import_image where merchant_id=".$_SESSION['merchant_id'].") order by id desc" ;
				$query1_admin = "select count(*) total from merchant_media where merchant_id=1 and id not in(select merchant_media_id from media_import_image where merchant_id=?) order by id desc" ; // with prepare
				
				$myQuery_admin = $query_admin;    
				//$RS_admin = $objDB->execute_query($query_admin);
				$RS_admin = $objDB->Conn->Execute($query_admin,array($_SESSION['merchant_id']));
				//$RS1_admin = $objDB->execute_query($query1_admin);
				$RS1_admin = $objDB->Conn->Execute($query1_admin,array($_SESSION['merchant_id']));
				  
				$total_images_admin = $RS1_admin->fields['total'];
				
			?>
			<div align="center" style="display:none;">
				<div class="filtergroup_admin">
					<h3>Filter</h3>
						<div class="filters_admin">
							<input type="button" id="imagetype_all_admin" name="imagetype_all_admin"  class="fltr_button_admin fltr_selected_admin" value="All Images" />
							<input type="button" id="imagetype_campaign_admin" name="imagetype_campaign_admin"  class="fltr_button_admin" value="Campaigns" />
							<input type="button" id="imagetype_location_admin" name="imagetype_location_admin"  class="fltr_button_admin" value="Locations" />
						</div>
				</div>
			</div>
			<?php
			if($RS_admin->RecordCount()=="0")
			{			
			?>
				<div class='deletemsgclass deletemsgclass_media table_errore_message' >
					<?php
					if($RS_admin->RecordCount()==0)
					{
						echo $merchant_msg["mediamanagement"]["Msg_no_images"];
					}
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
			
			if($RS_admin->RecordCount()>0)
			{
			?>
				<div id="mediya_image_container_admin">
				<?php	
				while($Row = $RS_admin->FetchRow())
				{
					if($Row['media_type_id'] == "1")
					{
						$target = "campaign" ;
					}
					if($Row['media_type_id'] == "2")
					{
						$target = "location";
					}
				?>
				<div class="mediya_img_blk <?php if($Row['media_type_id'] == "1"){echo "campaignfilter";} if($Row['media_type_id'] == "2"){echo "locationfilter";}?>">
					<a href="javascript:void(0)" class="mediya_camp_loca">
						<?php
						if($Row['media_type_id'] == "1")
						{
						?>
							<img title="Campaign" src="<?php echo ASSETS_IMG ?>/m/mediya_campaign.png">
						<?php
						}
						?>
						<?php
						if($Row['media_type_id'] == "2")
						{
						?>
							<img title="Location" src="<?php echo ASSETS_IMG ?>/m/mediya_location.png">
						<?php
						}
						?>
					</a>
					<img src="<?php echo ASSETS_IMG."/m/".$target."/".$Row['image'];  ?>" class= "mediya_grid" />
					<a title="Add to My Images" href="javascript:void(0)" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediaadd_<?=$Row['id']?>" class="mediya_add">
						<img src="<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png">
						<span>Add to My Images</span>
					</a>
				</div>
				<?php
				}
				?>
			</div>
			<?php
			}
			?>
			<?php
			if($RS_admin->RecordCount()>0 && $total_images_admin>$num_of_records_admin)
			{
			?>
				<div id="mediya_showmore_admin" style="display:block;">
					<input type="button" id="show_more_mediya_admin" name="show_more_mediya_admin" value="Show More" next_index="<?php echo $next_index_admin ?>" num_of_records="<?php echo $num_of_records_admin ?>" total_images="<?php echo $total_images_admin ?>" />
				</div>
			<?php
			}
			?>
		</div>	
					
	<div id="div_imagegallery" class="tabs" style="display:none;margin-top: 15px;">
		<?php
			if($flag_view)
			{
				//echo $RS->RecordCount();
				if($RS->RecordCount()=="0")
				{			
				?>
					<div class='deletemsgclass deletemsgclass_media table_errore_message' >
						<?php
							echo $merchant_msg["mediamanagement"]["Msg_no_images"];
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
				<div id="gallery_list">
					<div class="gallery_list_up">
						<!--<input type="button" id="add_location_gallery" name="add_location_gallery" value="Add Image Gallery"  />-->
						<h4 class="add_new_location" align="right">
							<a id="add_location_gallery" name="add_location_gallery" href="javascript:void(0);">Add Media Gallery</a>
						</h4>
					</div>
					<div class="gallery_list_down">
						
							<table border="0" cellspacing="1" cellpadding="10" class="tableMerchant" id="manage_loc_gallery_table" >
								<thead>
									<tr>
										<td colspan="4" align="left" class="table_errore_message">
											<?php echo (isset($_SESSION['msg'])) ? $_SESSION['msg'] : ''; ?>&nbsp;
										</td>
									</tr>
									<tr>
										<th>Location</th>
										<th>Video Count</th>
										<th>Image Count</th>
										<th>Actions</th>
									</tr>
								</thead>

							</table>
                        
					</div>
				</div>
				<div id="gallery_add" style="display:none;">
					<h3>Add Media Gallery</h3>
					<div id="mediya_assigntolocation" style="display:block;">
						<input type="button" value="Save Location Media Gallery" name="assign_gallery" id="assign_gallery" disabled class="disabled">&nbsp;&nbsp;
						<input type="button" value="Cancel" name="cancel_Add_gallery" id="cancel_Add_gallery">
					</div>
					<div class="drpdwndiv" >
						<div class="lmg_lbl">
							<b>
								<?php echo $merchant_msg['report']['Campaign_Filter_Loactions']; ?>
							</b>
						</div>
						<div class="lmg_cntrl">
							<?php
								$arr_loc = file(WEB_PATH . '/merchant/process.php?btnGetlocatioidwithgallery=yes');
								if (trim($arr_loc[0]) == "") {
									unset($arr_loc[0]);
									$arr_loc = array_values($arr_loc);
								}
								$json_loc = json_decode($arr_loc[0]);
								$records_array_loc = $json_loc->records;
							?>
							<select id="opt_filter_location">
								<option value="0" selected="selected" > --- Select Location ---</option>
								<?php
								 if ($_SESSION['merchant_info']['merchant_parent'] != 0) {
									$media_acc_array = array();
									$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
									$RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
									$location_val = $RSmedia->fields['location_access'];
									$arr1 = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $_SESSION['merchant_id'] . '&loc_id=' . $location_val);
							} else {
									$arr1 = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $_SESSION['merchant_id']);
							}

							if (trim($arr1[0]) == "") {
									unset($arr1[0]);
									$arr1 = array_values($arr1);
							}
							$json1 = json_decode($arr1[0]);
							$total_records2 = $json1->total_records;
							$records_array2 = $json1->records;
								
								
								
								if ($total_records2 > 0) {
										$cnt = 0;
										foreach ($records_array2 as $Row) {
											if(in_array($Row->id,$records_array_loc))
											{
											}
											else
											{
												if ($Row->location_name == "") {
														$locname = $Row->address . " - " . $Row->zip;
												} else {
														$locname = $Row->location_name . " - " . $Row->zip;
												}
												$array_where = array();
												$array_where['id'] = $Row->state;
												$RS_state = $objDB->Show("state", $array_where);

												$array_where = array();
												$array_where['id'] = $Row->city;
												$RS_city = $objDB->Show("city", $array_where);
			
												$location_string = $Row->address . ", " . $RS_city->fields['name'] . ", " . $RS_state->fields['short_form'] . ", " . $Row->zip;
												$location_string = (strlen($location_string) > 57) ? substr($location_string, 0, 57) . '...' : $location_string;
												?>
												<option title="<?php echo $Row->address . ", " . $RS_city->fields['name'] . ", " . $RS_state->fields['short_form'] . ", " . $Row->zip; ?>" value="<?php echo $Row->id; ?>" >
												<?php echo $location_string; ?> 
												</option>
												<?php
											}
										}
								}
								?>
							</select>
						</div>
					</div>
					<div class="tabbable tabs-left segment-tab" >
						<div class="row">
							<div class="col-md-2" style="width: 25%;">
								<ul class="nav nav-tabs no-margin">
									<li class="active"><a data-toggle="tab" href="#c" aria-expanded="true">Images</a></li>
									<li class=""><a data-toggle="tab" href="#d" aria-expanded="false">Videos</a></li>
								</ul>
							</div>
							<div class="col-md-10"  style="width: 74%;">
								<div class="row">
									<div class="tab-content ">
										<div id="c" class="tab-pane active">
											<div class="tabdiv">
												<div class="tabdiv_erormsg" style="display:<?php if($RS_l->RecordCount()==0) echo 'block';else echo 'none'; ?>">
													No images available to add.
												</div>
												<div id="mediya_image_container_gallery" style="display:<?php if($RS_l->RecordCount()>0) echo 'inline-block';else echo 'none'; ?>">
												
													<?php  
													
													if($RS_l->RecordCount()>0)
													{ 
														$RS_l->MoveFirst();
														while($Row = $RS_l->FetchRow())
														{
															if($Row['media_type_id'] == "2")
															{
																if($Row['media_type_id'] == "1")
																{
																	$target = "campaign" ;
																}
																if($Row['media_type_id'] == "2")
																{
																	$target = "location";
																}
																if($Row['media_type_id'] == "3")
																{
																	$target = "giftcard";
																}
															?>
															<div class="mediya_img_blk <?php if($Row['media_type_id'] == "1"){echo "campaignfilter";} if($Row['media_type_id'] == "2"){echo "locationfilter";} if($Row['media_type_id'] == "3"){echo "giftcardfilter";} ?>">
																<a href="javascript:void(0)" class="mediya_camp_loca" style="display: none;">
																	<?php
																	if($Row['media_type_id'] == "1")
																	{
																	?>
																		<img title="Campaign" src="<?php echo ASSETS_IMG ?>/m/mediya_campaign.png">
																	<?php
																	}
																	?>
																	<?php
																	if($Row['media_type_id'] == "2")
																	{
																	?>
																		<img title="Location" src="<?php echo ASSETS_IMG ?>/m/mediya_location.png">
																	<?php
																	}
																	?>
																	<?php
																	if($Row['media_type_id'] == "3")
																	{
																	?>
																		<img title="Giftcard" src="<?php echo ASSETS_IMG ?>/m/mediya_giftcard.png">
																	<?php
																	}
																	?>
																</a>
										
																<img src="<?php echo ASSETS_IMG."/m/".$target."/".$Row['image'];  ?>" class= "mediya_grid" />
																
																	<img class="mediya_grid1_img_selected mediya_slctd" src="<?php echo ASSETS_IMG ?>/m/image_Added_to_lbry.png">
																
																<a title="Select Image" href="javascript:void(0)" src=<?=$Row['image']?> image_type=<?=$Row['media_type_id']?> id="mediaselect_<?=$Row['id']?>" class="mediya_select">
																	<img src="<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png">
																	<span>Select Image</span>
																</a>
															</div>
														<?php
															}
														}
														//echo $RS_l->RecordCount()."-".$total_images_l."-".$num_of_records_l;
														
															
													}
													?>
													
												</div>
											</div>
										<?php
										if($RS_l->RecordCount()>0 && $total_images_l>$num_of_records_l)
												{
												?>
													<div id="mediya_showmore" style="display:block;">
														<input type="button" id="show_more_mediya_gallery" name="show_more_mediya_gallery" value="Show More" next_index="<?php echo $next_index_l ?>" num_of_records="<?php echo $num_of_records_l ?>" total_images="<?php echo $total_images_l ?>" />
													</div>
													
												<?php
												}
												?>
										</div>
										<div id="d" class="tab-pane">
											<div class="tabdiv">
												<div class="tabdiv_erormsg" style="display:<?php if($RS_l_v->RecordCount()==0) echo 'block';else echo 'none'; ?>">
													No videos available to add.
												</div>
												<div id="mediya_video_container_gallery" style="display:<?php if($RS_l_v->RecordCount()>0) echo 'inline-block';else echo 'none'; ?>">
													<?php  
													if($RS_l_v->RecordCount()>0)
													{ 
														while($Row = $RS_l_v->FetchRow())
														{	
														?>
															<div class="mediya_video_blk" >
																
																	<img src="<?php echo $Row['thumbnail_url'];  ?>" class= "mediya_grid_video" />
																
																<img class="mediya_grid1_img_selected mediya_slctd" src="<?php echo ASSETS_IMG ?>/m/image_Added_to_lbry.png">
																
																<a title="Select Video" href="javascript:void(0)" id="mediaselectvideo_<?=$Row['id']?>" class="mediya_select">
																	<img src="<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png">
																	<span>Select Video</span>
																</a>
															</div>
														<?php
														}
													}
													?>
												</div>
											</div>
										
											<?php
										if($RS_l_v->RecordCount()>0 && $total_videos_l>$num_of_records_l_v)
												{
												?>
													<div id="mediya_video_showmore" style="display:block;">
														<input type="button" id="show_more_mediya_video_gallery" name="show_more_mediya_video_gallery" value="Show More" next_index="<?php echo $next_index_l_v ?>" num_of_records="<?php echo $num_of_records_l_v ?>" total_videos="<?php echo $total_videos_l ?>" />
													</div>
													
												<?php
												}
												?>
										</div>
									</div>	
								</div>
							</div>
						</div>
					</div>
					
					<input type="hidden" id="hdnimageids" name="hdnimageids" />
					<input type="hidden" id="hdnimageids_assign" name="hdnimageids_assign" />
					<input type="hidden" id="hdnimageids_unassign" name="hdnimageids_unassign" />	
					
					<input type="hidden" id="hdnvideoids" name="hdnvideoids" />
					<input type="hidden" id="hdnvideoids_assign" name="hdnvideoids_assign" />
					<input type="hidden" id="hdnvideoids_unassign" name="hdnvideoids_unassign" />			
					
				<?php
			}
			else
			{
			?>
				<div><?php echo $merchant_msg["mediamanagement"]["Msg_dont_view_permission"];?></div>
			<?php
			} 
			?>	
		
	</div>

<!--end of all_div_container--></div>
		<?php
		}
		?>
		<div id="dialog-message" title="Message Box" style="display:none">

                    </div>
<!--end of content--></div>	                        	
<!--end of contentContainer--></div>
<!--end of body--></div>

<!---------start footer--------------->
       <div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		$_SESSION['msg']= "";
		?>
		<!--end of footer--></div>
	
</div>
<div id="videonotificationcontainer">
</div>
<div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
    <div id="NotificationBackDiv" class="divBack">
    </div>
    <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">

        <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading">

            <div id="NotificationmainContainer" class="innerContainer" style="height:auto;width:auto">
                <img src="<?= ASSETS_IMG ?>/loading.gif" style="display: block;" id="image_loader_div"/>
            </div>
        </div>
    </div>
</div>

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

<script>
	jQuery("#popupcancel").live("click", function () {
		jQuery.fancybox.close();
		return false;
	});
	
	
	 
	
		
		
		
 jQuery(document).ready(function () {
	 
	 /******* initialize jQuery data table ****/
                                
                                var oCache = {
                        iCacheLower: -1
                    };

                    function fnSetKey(aoData, sKey, mValue)
                    {
                        for (var i = 0, iLen = aoData.length; i < iLen; i++)
                        {
                            if (aoData[i].name == sKey)
                            {
                                aoData[i].value = mValue;
                            }
                        }
                    }

                    function fnGetKey(aoData, sKey)
                    {
                        for (var i = 0, iLen = aoData.length; i < iLen; i++)
                        {
                            if (aoData[i].name == sKey)
                            {
                                return aoData[i].value;
                            }
                        }
                        return null;
                    }

                    function fnDataTablesPipeline(sSource, aoData, fnCallback) {
                        var iPipe = 2; /* Ajust the pipe size */

                        var bNeedServer = false;
                        var sEcho = fnGetKey(aoData, "sEcho");
                        var iRequestStart = fnGetKey(aoData, "iDisplayStart");
                        var iRequestLength = fnGetKey(aoData, "iDisplayLength");
                        var iRequestEnd = iRequestStart + iRequestLength;
                        oCache.iDisplayStart = iRequestStart;

                        /* outside pipeline? */
                        if (oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper)
                        {
                            bNeedServer = true;
                        }

                        /* sorting etc changed? */
                        if (oCache.lastRequest && !bNeedServer)
                        {
                            for (var i = 0, iLen = aoData.length; i < iLen; i++)
                            {
                                if (aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho")
                                {
                                    if (aoData[i].value != oCache.lastRequest[i].value)
                                    {
                                        bNeedServer = true;
                                        break;
                                    }
                                }
                            }
                        }

                        /* Store the request for checking next time around */
                        oCache.lastRequest = aoData.slice();

                        if (bNeedServer)
                        {
                            if (iRequestStart < oCache.iCacheLower)
                            {
                                iRequestStart = iRequestStart - (iRequestLength * (iPipe - 1));
                                if (iRequestStart < 0)
                                {
                                    iRequestStart = 0;
                                }
                            }

                            oCache.iCacheLower = iRequestStart;
                            oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
                            oCache.iDisplayLength = fnGetKey(aoData, "iDisplayLength");
                            fnSetKey(aoData, "iDisplayStart", iRequestStart);
                            fnSetKey(aoData, "iDisplayLength", iRequestLength * iPipe);

                            $.getJSON(sSource, aoData, function (json) {
                                /* Callback processing */
                                oCache.lastJson = jQuery.extend(true, {}, json);

                                if (oCache.iCacheLower != oCache.iDisplayStart)
                                {
                                    json.aaData.splice(0, oCache.iDisplayStart - oCache.iCacheLower);
                                }
                                json.aaData.splice(oCache.iDisplayLength, json.aaData.length);

                                fnCallback(json)
                            });
                        }
                        else
                        {
                            json = jQuery.extend(true, {}, oCache.lastJson);
                            json.sEcho = sEcho; /* Update the echo for each response */
                            json.aaData.splice(0, iRequestStart - oCache.iCacheLower);
                            json.aaData.splice(iRequestLength, json.aaData.length);
                            fnCallback(json);
                            return;
                        }
                    }
                    
	 var oTable =   jQuery('#manage_loc_gallery_table').dataTable({
                                        // "sPaginationType": "full_numbers",
                                        "oLanguage": {
                                            "sEmptyTable": "No gallery in the system currently, please add at least one.",
                                        },
                                        "bPaginate": true,
                                        "bFilter": false,
                                        "bSort": false,
                                        "bLengthChange": false,
                                        "info": false,
                                        "bProcessing": true,
                                        "bServerSide": true,
                                        "iDisplayLength": 10,
										"fnPreDrawCallback": function( oSettings ) {
											  jQuery.ajax({
											type:"POST",
											url:'process.php',
											data :'loginornot=true',
											success:function(msg)
												{
													var obj = jQuery.parseJSON(msg);
													if (obj.status=="false") 
													{
														window.location.href="<?php echo WEB_PATH ?>/merchant/register.php";
													}
											
												}
											});
										},
										"sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
										"fnServerParams": function (aoData) {
											aoData.push({"name": "btngetlocationgallery", "value": true});

										},
										"aoColumns": [{"sWidth": "515px"},{"sWidth": "85px"},{"sWidth": "85px"},{"sWidth": "160px","sClass": "actiontd"}]
									});
		jQuery("#manage_loc_gallery_table").show();
				
		jQuery(document).on("click", "#popupyes", function () {
			var linkurl = jQuery(this).attr("link");
			jQuery.fancybox.close();
			//window.location.href = linkurl;
			jQuery.ajax({
				type: "POST",
				url: linkurl,
				//data: '<?= WEB_PATH ?>/merchant/' + linkurl,
				async: false,
				success: function (msg)
				{
					var obj = jQuery.parseJSON(msg);
					if (obj.status=="true")     
					{
						jQuery("#opt_filter_location option:first").after(obj.html);
						oCache.iCacheLower = -1;
						oTable.fnDraw();
					}
				}
			});

		});
		
		 jQuery(document).on("click", "#popupyes_assign", function () {
			var linkurl = jQuery(this).attr("link");
			var selected_location = jQuery(this).attr("lid");
			jQuery.fancybox.close();
			
			var selected_images = jQuery("#hdnimageids_assign").val();
			selected_images = selected_images.substr(0,selected_images.length-1);
			jQuery("#hdnimageids_assign").val("");
			console.log("Selected Location : "+selected_location);
			console.log("Image Id : "+selected_images);
			
			var selected_videos = jQuery("#hdnvideoids_assign").val();
			selected_videos = selected_videos.substr(0,selected_videos.length-1);
			jQuery("#hdnvideoids_assign").val("");
			console.log("Selected Location : "+selected_location);
			console.log("video Id : "+selected_videos);
		
			jQuery.ajax({
			  type: "POST",
			  url: "<?=WEB_PATH?>/merchant/process.php",
			  data: "location_id=" + selected_location +"&video_ids="+selected_videos+"&image_ids="+ selected_images +"&btnAddlocationgallery=yes",
			  async : false,
			  success: function(msg) {
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
				
					var if_tab = "<?php if(isset($_REQUEST["tab"]) && $_REQUEST["tab"]=="imagegallery")echo "1"; else "0";?>";
					if(if_tab)
					{
						//window.location.href=window.location.href;
						oTable.fnDraw();
					}
					else
					{
						//window.location.href+="?tab=imagegallery";
						oTable.fnDraw();
					}
				}
				else
				{
					
				}
				
			  }
		   });
			
		});
		
		jQuery(document).on("click", "#popupyes_unassign", function () {
			var linkurl = jQuery(this).attr("link");
			var selected_location = jQuery(this).attr("lid");
			jQuery.fancybox.close();
			
			var selected_images = jQuery("#hdnimageids_unassign").val();
			selected_images = selected_images.substr(0,selected_images.length-1);
			jQuery("#hdnimageids_unassign").val("");
			console.log("Selected Location : "+selected_location);
			console.log("Image Id : "+selected_images);
			
			var selected_videos = jQuery("#hdnvideoids_unassign").val();
			selected_videos = selected_videos.substr(0,selected_videos.length-1);
			jQuery("#hdnvideoids_unassign").val("");
			console.log("Selected Location : "+selected_location);
			console.log("video Id : "+selected_videos);
			
			jQuery.ajax({
			  type: "POST",
			  url: "<?=WEB_PATH?>/merchant/process.php",
			  data: "location_id=" + selected_location +"&video_ids="+selected_videos+"&image_ids="+ selected_images +"&btnRemoveimagefromlocationgallery=yes",
			  async : false,
			  success: function(msg) {
				//alert(msg);
				if(msg=="success")
				{
					var if_tab = "<?php if(isset($_REQUEST["tab"]) && $_REQUEST["tab"]=="imagegallery")echo "1"; else "0";?>";
					if(if_tab)
					{
						//window.location.href=window.location.href;
						oTable.fnDraw();
					}
					else
					{
						//window.location.href+="?tab=imagegallery";
						oTable.fnDraw();
					}
				}
				else
				{
					
				}
				
			  }
		   });
			
		});
 });
				$("body").click
                        (
                                function (e)
                                {

                                    if ((e.target.className).trim() == "actiontd")
                                    {

                                    }
                                    else
                                    {
                                        $('.actiontd').find('#actiondiv').slideUp("slow");
                                    }
                                }
                        );
                         /********** open more action popup ************/
                var isMobile = {
                Android: function () {
                    return navigator.userAgent.match(/Android/i);
                },
                BlackBerry: function () {
                    return navigator.userAgent.match(/BlackBerry/i);
                },
                iOS: function () {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                },
                Opera: function () {
                    return navigator.userAgent.match(/Opera Mini/i);
                },
                Windows: function () {
                    return navigator.userAgent.match(/IEMobile/i);
                },
                any: function () {
                    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
                }
            };

            if (isMobile.iOS() || isMobile.Android())
            {
				jQuery(document).on("touchstart", 'td.actiontd', function () {

                    if ($(this).find('#actiondiv').css('display') == 'none')
                    {

                        $('.actiondivclass').css("display", "none");
                        //$(this).find('#actiondiv').css('display','block');
                        $(this).find('#actiondiv').slideDown("slow");
                    }
                    else
                        $(this).find('#actiondiv').slideUp('slow');
                    /*
                     },function(){
                     $(this).find('#actiondiv').css('display','none');  */
                });	
			} 
			else
			{        
                $(document).on('click', '.actiontd', function () {

                    if ($(this).find('#actiondiv').css('display') == 'none')
                    {

                        $('.actiondivclass').css("display", "none");
                        //$(this).find('#actiondiv').css('display','block');
                        $(this).find('#actiondiv').slideDown("slow");
                    }
                    else
                        $(this).find('#actiondiv').slideUp('slow');
                    /*
                     },function(){
                     $(this).find('#actiondiv').css('display','none');  */
                });	
			}
				jQuery(document).on('click', '.commonclass', function () {
                    var linkurl = jQuery(this).attr("link");
                    var txt = jQuery(this).text();
					var lid = jQuery(this).attr("lid");
                    jQuery.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'loginornot=true',
                        async: false,
                        success: function (msg)
                        {
                            var obj = jQuery.parseJSON(msg);
                            if (obj.status == "false")
                            {
                                window.location.href = obj.link;
                            }
                            else
                            {
								//console.log(txt);
                                if (txt == 'View Gallery') 
                                {
                                    
                                    var msg = "<div style='width: 252px;'>Are you sure you want to delete employee ?</div>";
									var head_msg = "";
									//head_msg+="<div class='lmgp_f_wrapper'>";
									head_msg+="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Gallery</div>"
									var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
									var footer_msg = "<div style='text-align:center'><hr><input type='button' link='<?php echo WEB_PATH ?>/merchant/" + linkurl + "' value='Yes' id='popupyes' name='popupyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'>&nbsp;&nbsp;&nbsp;<input type='button'  value='No' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
									jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
									
									jQuery.ajax({
										type: "POST",
										url: 'process.php',
										data: 'btngetlocationgalleryforviewgallery=yes&location_id='+lid,
										async: false,
										success: function (msg)
										{
											var obj = jQuery.parseJSON(msg);
											content_msg = obj.html;
											//content_msg +='</div>';
											jQuery("#dialog-message").html(head_msg + content_msg);
										}
									});
							
									jQuery.fancybox({
										content: jQuery('#dialog-message').html(),
										type: 'html',
										openSpeed: 300,
										closeSpeed: 300,
										// topRatio: 0,
										minWidth:600,
										minHeight:460,
										changeFade: 'fast',
										beforeShow: function () {
											jQuery(".fancybox-inner").addClass("msgClass");
										},
										helpers: {
											overlay: {
												opacity: 0.3
											} // overlay
										}
									});
                                } 
                                else if(txt == 'Assigned Media') 
                                {
									var msg = "<div style='width: 252px;'>Are you sure you want to delete employee ?</div>";
									var head_msg = "";
									//head_msg+="<div class='lmgp_f_wrapper'>";
									head_msg+="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Add Media</div>"
									var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
									var footer_msg = "<div style='text-align:right;'><input type='button' lid= "+lid+" link='' value='Add selected media' id='popupyes_assign' class='disabled' disabled name='popupyes_assign' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'>&nbsp;&nbsp;&nbsp;<input type='button'  value='Cancel' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
									//footer_msg="";
									jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
									
									head_msg +='<div id="mediya_image_container_viewgallery" style="display:inline-block">';
									
									jQuery.ajax({
										type: "POST",
										url: 'process.php',
										data: 'btngetlocationgalleryforunassigngallery=yes&location_id='+lid,
										async: false,
										success: function (msg)
										{
											var obj = jQuery.parseJSON(msg);
											content_msg = obj.html;
											content_msg +='</div>';
											//content_msg +='</div>';
											jQuery("#dialog-message").html(head_msg + footer_msg + content_msg);
										}
									});
							
									jQuery.fancybox({
										content: jQuery('#dialog-message').html(),
										type: 'html',
										openSpeed: 300,
										closeSpeed: 300,
										// topRatio: 0,
										minWidth:600,
										minHeight:460,
										changeFade: 'fast',
										beforeShow: function () {
											jQuery(".fancybox-inner").addClass("msgClass");
										},
										helpers: {
											overlay: {
												opacity: 0.3
											} // overlay
										}
									});		
									
                                }
								else if(txt == 'Unassigned Media')
								{
									console.log(txt);
									var msg = "<div style='width: 252px;'>Are you sure you want to delete employee ?</div>";
									var head_msg = "";
									//head_msg+="<div class='lmgp_f_wrapper'>";
									head_msg+="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Remove Media</div>"
									var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
									var footer_msg = "<div style='text-align:right;'><input type='button' lid= "+lid+" link='' value='Remove selected media' id='popupyes_unassign' class='disabled' disabled name='popupyes_unassign' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'>&nbsp;&nbsp;&nbsp;<input type='button'  value='Cancel' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
									jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
									
									head_msg +='<div id="mediya_image_container_viewgallery" style="display:inline-block">';
									
									jQuery.ajax({
										type: "POST",
										url: 'process.php',
										data: 'btngetlocationgalleryforassigngallery=yes&location_id='+lid,
										async: false,
										success: function (msg)
										{
											var obj = jQuery.parseJSON(msg);
											content_msg = obj.html;
											content_msg +='</div>';
											//content_msg +='</div>';
											jQuery("#dialog-message").html(head_msg + footer_msg + content_msg);
										}
									});
							
									jQuery.fancybox({
										content: jQuery('#dialog-message').html(),
										type: 'html',
										openSpeed: 300,
										closeSpeed: 300,
										// topRatio: 0,
										minWidth:600,
										minHeight:460,
										changeFade: 'fast',
										beforeShow: function () {
											jQuery(".fancybox-inner").addClass("msgClass");
										},
										helpers: {
											overlay: {
												opacity: 0.3
											} // overlay
										}
									});
								}
								else if (txt == 'Delete') 
                                {
									linkurl = "process.php?delete_location_gallery=yes&id="+lid;
									var msg = "<div style='width: 260px;'>Are you sure you want to delete gallery ?</div>";
									var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
									var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
									var footer_msg = "<div style='text-align:center'><hr><input type='button' link='<?php echo WEB_PATH ?>/merchant/" + linkurl + "' value='Yes' id='popupyes' name='popupyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'>&nbsp;&nbsp;&nbsp;<input type='button'  value='No' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
									jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

									jQuery.fancybox({
										content: jQuery('#dialog-message').html(),
										type: 'html',
										openSpeed: 300,
										closeSpeed: 300,
										// topRatio: 0,

										changeFade: 'fast',
										beforeShow: function () {
											$(".fancybox-inner").addClass("msgClass");
										},
										helpers: {
											overlay: {
												opacity: 0.3
											} // overlay
										}
									});
								}	
                            }
                        }
                    });
                });                


                                    
$("input[type=radio][name=image_type]").click(function(){
	$("#img_type").val($(this).val());	
});

$('#selectimg>option').click(function(){
     $("#img_type").val($(this).val());
 }); 

function set_hidden_image_type(sel)
{	
	console.log(jQuery("#uploader_filelist li").length);
	if(jQuery("#selectimg").val()==0)
	{
		console.log("please select image type");
		jQuery("#uploader_start").addClass("ui-state-disabled");
	}
	else if(jQuery("#selectimg").val()>0 && jQuery("#uploader_filelist li").length>0)
	{
		jQuery("#uploader_start").removeClass("ui-state-disabled");
	}
	
}

jQuery("#add_location_gallery").click(function(){
	jQuery("#gallery_list").css("display","none");
	//jQuery("#gallery_add").css("display","block");
	jQuery("#gallery_add").css("display","inline-block");
	jQuery("select#opt_filter_location").prop('selectedIndex', 0);
});

jQuery("#cancel_Add_gallery").click(function(){
	jQuery("#gallery_add").css("display","none");
	jQuery("#gallery_list").css("display","block");
});

$('#px-submit').live("click",function(){
  var flag=0;
   jQuery.ajax({
		   type:"POST",
		   url:'process.php',
		   data :'loginornot=true',
		  async:true,
		   success:function(msg)
		   {
			   
			 
				 var obj = jQuery.parseJSON(msg);
			   
				 
				if (obj.status=="false")     
				{
					flag=1;
					window.location.href=obj.link;
				}
				
		   }
		});
		if(flag==1)
			{
				return false;
			}
		else
			{
				return true;
			}
});


// delete media image 
jQuery("a[id^='mediadeleteid_']").live("click",function(){
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
		
		
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var admin_image=jQuery(this).attr('admin_image');
	   var cid= arr[1];
		jQuery.ajax({
		  type: "POST",
		  url: "<?=WEB_PATH?>/merchant/process.php",
		  data: "admin_image="+admin_image+"&id=" + cid +"&mediaactiondelete=yes&image_type=" + image_type +"&src="+ src +"",
		  async : false,
		  success: function(msg) {
			if(msg=="success")
			{
				// 14 10 2013
				//var image_type=image_type_filter;
				//var imgval=image_type;
				//jQuery("#upload_business_logo_ajax1").show();
				
				//jQuery(".msgclass").html("Image is deleted successfully");
				// 14 10 2013
							 
				// 15 01 2015 add deleted admin image to image library
				
				//alert(admin_image);
				
				 if(admin_image==1)
				 {
					//alert(image_type);
					if(image_type==1)
					{
						jQuery("#mediya_image_container_admin").prepend('<div class="mediya_img_blk campaignfilter">'+cur_div.parent().html()+'</div>');
					}
					if(image_type==2)
					{
						jQuery("#mediya_image_container_admin").prepend('<div class="mediya_img_blk locationfilter">'+cur_div.parent().html()+'</div>');
					}
					if(image_type==3)
					{
						jQuery("#mediya_image_container_admin").prepend('<div class="mediya_img_blk giftcardfilter">'+cur_div.parent().html()+'</div>');
					}
					
					var add_to_images = '<a title="Add to My Images" class="mediya_add" id="mediaadd_'+cid+'" image_type="'+image_type+'" src="'+src+'" href="javascript:void(0)"><img src="<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png"><span>Add to My Images</span></a>';
					//alert(add_to_images);
					jQuery("#mediya_image_container_admin .mediya_img_blk:first-child").find("a.mediya_delete").replaceWith(add_to_images);
				 }
				 
				 // 15 01 2015 add deleted admin image to image library
				 
				 // 08 01 2015 remove div when delete
				 
				 cur_div.parent().remove();
				 
				 // 08 01 2015 remove div when delete
				 /*
				 jQuery("#div_image .deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_image_deleted']; ?>");
				 jQuery("#div_image .deletemsgclass").css("display","block");
				 jQuery("#div_image .deletemsgclass_close").css("display","block");
				 */
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='greenspan'><?php echo $merchant_msg["mediamanagement"]['Msg_image_deleted']; ?></span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
				
				
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'btnGetImageOfMerchantAjaxInGallery=true',
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="true") 
						{
							jQuery('#mediya_image_container_gallery').html(obj.html);
						}
					}
				});
				 
			}
			else if(msg=="delete_denied")
			{
				/*
				jQuery("#div_image .deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_dont_delete_permission']; ?>");
				jQuery("#div_image .deletemsgclass").css("display","block");
				jQuery("#div_image .deletemsgclass_close").css("display","block");
				*/
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'><?php echo $merchant_msg["mediamanagement"]['Msg_dont_delete_permission']; ?></span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
			}
			else
			{
				/*
				jQuery("#div_image .deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_cant_delete_image'];   ?>");
				jQuery("#div_image .deletemsgclass").css("display","block");
				jQuery("#div_image .deletemsgclass_close").css("display","block");
				*/
				
				if(image_type==1)
				{
					jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'><?php echo $merchant_msg["mediamanagement"]['Msg_cant_delete_image_campaign']; ?></span></div>");
					jQuery("#videonotificationcontainer").show();
					setTimeout(function(){
						jQuery(".videonotification").fadeOut('slow');
					},5000);
				}
				if(image_type==2)
				{
					jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'><?php echo $merchant_msg["mediamanagement"]['Msg_cant_delete_image_location']; ?></span></div>");
					jQuery("#videonotificationcontainer").show();
					setTimeout(function(){
						jQuery(".videonotification").fadeOut('slow');
					},5000);
				}
				if(image_type==3)
				{
					jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'><?php echo $merchant_msg["mediamanagement"]['Msg_cant_delete_image_giftcard']; ?></span></div>");
					jQuery("#videonotificationcontainer").show();
					setTimeout(function(){
						jQuery(".videonotification").fadeOut('slow');
					},5000);
				}
					
				
			}
				   
			//alert(jQuery(".mediya_img_blk").length);
			
			if(jQuery("#mediya_image_container .mediya_img_blk").length==0)
			{
				jQuery("#mediya_image_container").append("<div class='mediya_img_blk mediya_upload_new' onclick='call_upload()' ><img src='/assets/images/m/upload_img.png' /></div>")
			}
			
		  }
	   });
	   close_popup("NotificationLoadingData");
   }
});

function call_upload()
{
	jQuery("#tab-type.div_upload a").trigger("click");
}

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
</script>


</body>
</html>

<script type="text/javascript">
$("a#myaccount").css("background-color","orange");


jQuery("#show_more_mediya").live("click",function(){
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
		var img_filter = jQuery(".fltr_selected").val();
		//alert(img_filter);
		
		var cur_el= jQuery(this);
		var next_index = parseInt(jQuery(this).attr('next_index'));
		var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'show_more_media=yes&next_index='+next_index+'&num_of_records='+num_of_records,
			async:true,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);

				if (obj.status=="true")     
				{
					jQuery("#mediya_image_container").append(obj.html);
					cur_el.attr('next_index',next_index + num_of_records);
					if(parseInt(obj.records_return)<num_of_records)
					{
						cur_el.css("display","none");
					}
					
					if(img_filter=="All Images")
					{
						jQuery("#mediya_image_container .mediya_img_blk.campaignfilter").css("display","block");
						jQuery("#mediya_image_container .mediya_img_blk.locationfilter").css("display","block");
						jQuery("#mediya_image_container .mediya_img_blk.giftcardfilter").css("display","block");
					}
					else if(img_filter=="Campaigns")
					{
						jQuery("#mediya_image_container .mediya_img_blk.locationfilter").css("display","none");	
						jQuery("#mediya_image_container .mediya_img_blk.campaignfilter").css("display","block");
						jQuery("#mediya_image_container .mediya_img_blk.giftcardfilter").css("display","none");
					}
					else if(img_filter=="Locations")
					{
						jQuery("#mediya_image_container .mediya_img_blk.campaignfilter").css("display","none");
						jQuery("#mediya_image_container .mediya_img_blk.locationfilter").css("display","block");
						jQuery("#mediya_image_container .mediya_img_blk.giftcardfilter").css("display","none");	
					}
					else if(img_filter=="Gift cards")
					{
						jQuery("#mediya_image_container .mediya_img_blk.campaignfilter").css("display","none");
						jQuery("#mediya_image_container .mediya_img_blk.locationfilter").css("display","none");
						jQuery("#mediya_image_container .mediya_img_blk.giftcardfilter").css("display","block");	
					}
		
				}
			}
		});
	}
	close_popup("NotificationLoadingData");	
});

jQuery("#show_more_mediya_gallery").live("click",function(){
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
		var img_filter = jQuery(".fltr_selected").val();
		img_filter = "Locations";
		//alert(img_filter);
		
		var cur_el= jQuery(this);
		var next_index = parseInt(jQuery(this).attr('next_index'));
		var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'show_more_media_l=yes&next_index='+next_index+'&num_of_records='+num_of_records,
			async:true,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);

				if (obj.status=="true")     
				{
					jQuery("#mediya_image_container_gallery").append(obj.html);
					cur_el.attr('next_index',next_index + num_of_records);
					if(parseInt(obj.records_return)<num_of_records)
					{
						cur_el.css("display","none");
					}
					
					if(img_filter=="All Images")
					{
						jQuery("#mediya_image_container_gallery .mediya_img_blk.campaignfilter").css("display","block");
						jQuery("#mediya_image_container_gallery .mediya_img_blk.locationfilter").css("display","block");
						jQuery("#mediya_image_container_gallery .mediya_img_blk.giftcardfilter").css("display","block");
					}
					else if(img_filter=="Campaigns")
					{
						jQuery("#mediya_image_container_gallery .mediya_img_blk.locationfilter").css("display","none");	
						jQuery("#mediya_image_container_gallery .mediya_img_blk.campaignfilter").css("display","block");
						jQuery("#mediya_image_container_gallery .mediya_img_blk.giftcardfilter").css("display","none");
					}
					else if(img_filter=="Locations")
					{
						jQuery("#mediya_image_container_gallery .mediya_img_blk.campaignfilter").css("display","none");
						jQuery("#mediya_image_container_gallery .mediya_img_blk.locationfilter").css("display","block");
						jQuery("#mediya_image_container_gallery .mediya_img_blk.giftcardfilter").css("display","none");	
					}
					else if(img_filter=="Gift cards")
					{
						jQuery("#mediya_image_container_gallery .mediya_img_blk.campaignfilter").css("display","none");
						jQuery("#mediya_image_container_gallery .mediya_img_blk.locationfilter").css("display","none");
						jQuery("#mediya_image_container_gallery .mediya_img_blk.giftcardfilter").css("display","block");	
					}
		
				}
			}
		});
	}
	close_popup("NotificationLoadingData");	
});
jQuery("#show_more_mediya_video_gallery").live("click",function(){
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
		var img_filter = jQuery(".fltr_selected").val();
		img_filter = "Locations";
		//alert(img_filter);
		
		var cur_el= jQuery(this);
		var next_index = parseInt(jQuery(this).attr('next_index'));
		var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'show_more_media_l_v=yes&next_index='+next_index+'&num_of_records='+num_of_records,
			async:true,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);

				if (obj.status=="true")     
				{
					jQuery("#mediya_video_container_gallery").append(obj.html);
					cur_el.attr('next_index',next_index + num_of_records);
					if(parseInt(obj.records_return)<num_of_records)
					{
						cur_el.css("display","none");
					}
		
				}
			}
		});
	}
	close_popup("NotificationLoadingData");	
});

jQuery("#show_more_mediya_admin").live("click",function(){
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
		var img_filter = jQuery(".fltr_selected_admin").val();
		//alert(img_filter);
		
		var cur_el= jQuery(this);
		var next_index = parseInt(jQuery(this).attr('next_index'));
		var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'show_more_media_admin=yes&next_index='+next_index+'&num_of_records='+num_of_records,
			async:true,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);

				if (obj.status=="true")     
				{
					jQuery("#mediya_image_container_admin").append(obj.html);
					cur_el.attr('next_index',next_index + num_of_records);
					if(parseInt(obj.records_return)<num_of_records)
					{
						cur_el.css("display","none");
					}
					
					if(img_filter=="All Images")
					{
						jQuery("#mediya_image_container_admin .mediya_img_blk.campaignfilter").css("display","block");
						jQuery("#mediya_image_container_admin .mediya_img_blk.locationfilter").css("display","block");
					}
					else if(img_filter=="Campaigns")
					{
						jQuery("#mediya_image_container_admin .mediya_img_blk.locationfilter").css("display","none");	
						jQuery("#mediya_image_container_admin .mediya_img_blk.campaignfilter").css("display","block");
					}
					else if(img_filter=="Locations")
					{
						jQuery("#mediya_image_container_admin .mediya_img_blk.campaignfilter").css("display","none");
						jQuery("#mediya_image_container_admin .mediya_img_blk.locationfilter").css("display","block");	
					}
		
				}
			}
		});
	}
	close_popup("NotificationLoadingData");	
});

jQuery("li#tab-type a").click(function() {
	
	//alert(jQuery(this).html());			
		
	jQuery("#sidemenu li a").each(function() {
		jQuery(this).removeClass("current");
	});
	
	jQuery(this).addClass("current");
	
	var cls = jQuery(this).parent().attr("class");
	
	jQuery(".tabs").each(function(){
		jQuery(this).css("display","none");
	});
	
	if(cls=="div_imagegallery")
	{
		jQuery("#gallery_list").css("display","block");
		jQuery("#gallery_add").css("display","none");
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btnGetImageOfMerchantAjaxInGallery=true',
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true") 
				{
					jQuery('#mediya_image_container_gallery').html(obj.html);
				}
			}
		});
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btnGetVideoOfMerchantAjaxInGallery=true',
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true") 
				{
					jQuery('#mediya_video_container_gallery').html(obj.html);
				}
			}
		});
		
	}
	
	jQuery('#'+cls).css("display","block");	
});

//jQuery(".fancybox-inner #media-upload-header1 #sidemenu1 li#tab-type1 a").click(function() {
jQuery(document).on( "click",".fancybox-inner #media-upload-header #sidemenu li#tab-type a",function(){
	//console.log("click");
	//alert(jQuery(this).html());			
		
	jQuery(".fancybox-inner #media-upload-header #sidemenu li a").each(function() {
		jQuery(this).removeClass("current");
	});
	
	jQuery(this).addClass("current");
	
	var cls = jQuery(this).parent().attr("class");
	
	jQuery(".fancybox-inner .all_div_container .tabs").each(function(){
		jQuery(this).css("display","none");
	});
	
	jQuery('.fancybox-inner .all_div_container #'+cls).css("display","block");	
});

jQuery(".fltr_button").live("click",function(){
	
	var img_filter = jQuery(this).val();
	//alert(img_filter);
	
	jQuery("div.filters input").removeClass("fltr_selected");
	
	if(img_filter=="All Images")
	{
		jQuery(this).addClass("fltr_selected");
		jQuery("#mediya_image_container .mediya_img_blk.campaignfilter").css("display","block");
		jQuery("#mediya_image_container .mediya_img_blk.locationfilter").css("display","block");
		jQuery("#mediya_image_container .mediya_img_blk.giftcardfilter").css("display","block");
	}
	else if(img_filter=="Campaigns")
	{
		jQuery(this).addClass("fltr_selected");
		jQuery("#mediya_image_container .mediya_img_blk.locationfilter").css("display","none");	
		jQuery("#mediya_image_container .mediya_img_blk.campaignfilter").css("display","block");
		jQuery("#mediya_image_container .mediya_img_blk.giftcardfilter").css("display","none");
	}
	else if(img_filter=="Locations")
	{
		jQuery(this).addClass("fltr_selected");
		jQuery("#mediya_image_container .mediya_img_blk.campaignfilter").css("display","none");
		jQuery("#mediya_image_container .mediya_img_blk.locationfilter").css("display","block");
		jQuery("#mediya_image_container .mediya_img_blk.giftcardfilter").css("display","none");	
	}
	else if(img_filter=="Gift cards")
	{
		jQuery(this).addClass("fltr_selected");
		jQuery("#mediya_image_container .mediya_img_blk.campaignfilter").css("display","none");
		jQuery("#mediya_image_container .mediya_img_blk.locationfilter").css("display","none");	
		jQuery("#mediya_image_container .mediya_img_blk.giftcardfilter").css("display","block");	
	}
	jQuery("#div_image #mediya_image_container").css("display","inline-block");
	//alert(jQuery("#div_image #mediya_image_container .mediya_img_blk:visible").length);
	if(jQuery("#div_image #mediya_image_container .mediya_img_blk:visible").length==0)
	{
		jQuery("#div_image .deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_no_images_with_filter']; ?>");
		jQuery("#div_image .deletemsgclass").css("display","block");
		jQuery("#div_image .deletemsgclass_close").css("display","block");
		jQuery("#div_image #mediya_image_container").css("display","none");
		jQuery("#div_image #mediya_showmore").css("display","none");
	}
	else
	{
		jQuery("#div_image .deletemsgclass").html("");	
		jQuery("#div_image .deletemsgclass").css("display","none");
		jQuery("#div_image .deletemsgclass_close").css("display","none");
		jQuery("#div_image #mediya_image_container").css("display","inline-block");
		jQuery("#div_image #mediya_showmore").css("display","block");
	}
	
	if(img_filter=="All Images")
	{
		jQuery("#div_image #mediya_showmore").css("display","block");
	}
	else
	{
		jQuery("#div_image #mediya_showmore").css("display","none");
	}		
	
});

jQuery(".fltr_button_admin").click(function(){
	
	var img_filter = jQuery(this).val();
	//alert(img_filter);
	
	jQuery("div.filters_admin input").removeClass("fltr_selected_admin");
	
	if(img_filter=="All Images")
	{
		jQuery(this).addClass("fltr_selected_admin");
		jQuery("#mediya_image_container_admin .mediya_img_blk.campaignfilter").css("display","block");
		jQuery("#mediya_image_container_admin .mediya_img_blk.locationfilter").css("display","block");
	}
	else if(img_filter=="Campaigns")
	{
		jQuery(this).addClass("fltr_selected_admin");
		jQuery("#mediya_image_container_admin .mediya_img_blk.locationfilter").css("display","none");	
		jQuery("#mediya_image_container_admin .mediya_img_blk.campaignfilter").css("display","block");
	}
	else if(img_filter=="Locations")
	{
		jQuery(this).addClass("fltr_selected_admin");
		jQuery("#mediya_image_container_admin .mediya_img_blk.campaignfilter").css("display","none");
		jQuery("#mediya_image_container_admin .mediya_img_blk.locationfilter").css("display","block");	
	}
	//alert(jQuery(".mediya_img_blk:visible").length);
	if(jQuery("#mediya_image_container_admin .mediya_img_blk:visible").length==0)
	{
		jQuery("#div_library .deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_no_images_with_filter']; ?>");
		jQuery("#div_library .deletemsgclass").css("display","block");
		jQuery("#div_library .deletemsgclass_close").css("display","block");
	}
	else
	{
		jQuery("#div_library .deletemsgclass").html("");	
		jQuery("#div_library .deletemsgclass").css("display","none");
		jQuery("#div_library .deletemsgclass_close").css("display","none");
	}
});

jQuery("a[id^='mediaadd_']").live("click",function(){
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
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var cid= arr[1];
		jQuery.ajax({
		  type: "POST",
		  url: "<?=WEB_PATH?>/merchant/process.php",
		  data: "id=" + cid +"&add_to_myimages=yes",
		  async : false,
		  success: function(msg) {
			//alert(msg);
			if(msg=="success")
			{
				if(image_type==1)
				{
					jQuery("#mediya_image_container").prepend('<div class="mediya_img_blk campaignfilter">'+cur_div.parent().html()+'</div>');
				}
				if(image_type==2)
				{
					jQuery("#mediya_image_container").prepend('<div class="mediya_img_blk locationfilter">'+cur_div.parent().html()+'</div>');
				}
				if(image_type==3)
				{
					jQuery("#mediya_image_container").prepend('<div class="mediya_img_blk giftcardfilter">'+cur_div.parent().html()+'</div>');
				}
				
				var delet_a = '<a class="mediya_delete" admin_image="1" id="mediadeleteid_'+cid+'" image_type="'+image_type+'" src="'+src+'" href="javascript:void(0)"><img src="<?php echo ASSETS_IMG ?>/m/mediya_delete.png"></a>';
				jQuery("#mediya_image_container .mediya_img_blk:first-child").find("a.mediya_add").replaceWith(delet_a);
				
				/*
				<a class="mediya_delete" id="mediadeleteid_362" image_type="1" src="media_campaign_1420745657.jpg" href="javascript:void(0)">
										<img src="<?php echo ASSETS_IMG ?>/m/mediya_delete.png">
									</a>
				*/					
				//jQuery("#mediya_image_container a.mediya_add").remove();
				
				cur_div.parent().remove();
			}
			else
			{
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'><?php echo $merchant_msg["mediamanagement"]['Msg_image_not_exist']; ?></span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
				
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'update_admin_image_library=true',
					async:false,
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="true")     
						{
							jQuery('#mediya_image_container_admin').html(obj.html);
						}
					}
				});
		
			}
			jQuery("#mediya_image_container").css("display","inline-block");
			jQuery(".deletemsgclass").html("");
			jQuery(".deletemsgclass").css("display","none");
			jQuery(".deletemsgclass_close").css("display","none");
		  }
	   });
	   close_popup("NotificationLoadingData");
   }
});

jQuery("a[id^='mediaselect_']").live("click",function(){
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
		console.log(jQuery(this).parent().find(".mediya_grid"));
		var img_ele = jQuery(this).parent().find(".mediya_grid");
		img_ele.addClass("imgselected");
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var cid= arr[1];
	   var selected_location = jQuery("#opt_filter_location").val(); 
	   //hdnimageids
	   var selected_images = "";
	   
	   if(jQuery(this).attr("title")=="Select Image")
	   {
		   jQuery(this).attr("title","Unselect Image");
		    jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/remove_image_to_lbry.png");
		   jQuery(this).find("span").text("Unselect Image");
		   jQuery(this).parent().addClass("mediya_img_blk_selected");
		   
		   if(jQuery("#hdnimageids").val()=="")
		   {
			   selected_images = cid +",";
		   }
		   else
		   {
			   selected_images = jQuery("#hdnimageids").val() + cid +",";
		   }
		   jQuery("#hdnimageids").val(selected_images);
		   console.log("Selected Location : "+selected_location);
		   console.log("Image Id : "+selected_images);
	   }
	   else
	   {
		   jQuery(this).attr("title","Select Image");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png");
		   jQuery(this).find("span").text("Select Image");
		   jQuery(this).parent().removeClass("mediya_img_blk_selected");
		   
		   selected_images = jQuery("#hdnimageids").val();
		   if(selected_images.indexOf(cid+",")!=-1)
		   {
			   selected_images = selected_images.replace(cid+",", "");
		   }
		   var img_ele = jQuery(this).parent().find(".mediya_grid");
		   img_ele.removeClass("imgselected");
		   
		   jQuery("#hdnimageids").val(selected_images);
		   console.log("Selected Location : "+selected_location);
		   console.log("Image Id : "+selected_images);
		   
	   }
	   /*
	   if(jQuery("#hdnimageids").val()=="")
	   {
		   selected_images = cid +",";
	   }
	   else
	   {
		   selected_images = jQuery("#hdnimageids").val() + cid +",";
	   }
	   jQuery("#hdnimageids").val(selected_images);
	   console.log("Selected Location : "+selected_location);
	   console.log("Image Id : "+selected_images);   
	   */
   }
   //var selected_len = jQuery("#mediya_image_container_gallery .imgselected").length;
   var selected_len = jQuery("#gallery_add .imgselected").length;
   console.log(selected_len);
   if(selected_len>0 && jQuery("#opt_filter_location").val()!=0)
   {
	   jQuery("#assign_gallery").removeClass("disabled");
	   jQuery("#assign_gallery").removeAttr("disabled");
	    
   }
   else
   {
	   jQuery("#assign_gallery").addClass("disabled");
	   jQuery("#assign_gallery").attr("disabled");
   }
   close_popup("NotificationLoadingData");
});

jQuery("a[id^='mediaselectvideo_']").live("click",function(){
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
		console.log(jQuery(this).parent().find(".mediya_grid_video"));
		var img_ele = jQuery(this).parent().find(".mediya_grid_video");
		img_ele.addClass("imgselected");
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var cid= arr[1];
	   var selected_location = jQuery("#opt_filter_location").val(); 
	   //hdnimageids
	   var selected_videos = "";
	   
	   if(jQuery(this).attr("title")=="Select Video")
	   {
		   jQuery(this).attr("title","Unselect Video");
		    jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/remove_image_to_lbry.png");
		   jQuery(this).find("span").text("Unselect Video");
		   jQuery(this).parent().addClass("mediya_img_blk_selected");
		   
		   if(jQuery("#hdnvideoids").val()=="")
		   {
			   selected_videos = cid +",";
		   }
		   else
		   {
			   selected_videos = jQuery("#hdnvideoids").val() + cid +",";
		   }
		   jQuery("#hdnvideoids").val(selected_videos);
		   console.log("Selected Location : "+selected_location);
		   console.log("Video Id : "+selected_videos);
	   }
	   else
	   {
		   jQuery(this).attr("title","Select Video");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png");
		   jQuery(this).find("span").text("Select Video");
		   jQuery(this).parent().removeClass("mediya_img_blk_selected");
		   
		   selected_videos = jQuery("#hdnvideoids").val();
		   if(selected_videos.indexOf(cid+",")!=-1)
		   {
			   selected_videos = selected_videos.replace(cid+",", "");
		   }
		   var img_ele = jQuery(this).parent().find(".mediya_grid_video");
		   img_ele.removeClass("imgselected");
		   
		   jQuery("#hdnvideoids").val(selected_videos);
		   console.log("Selected Location : "+selected_location);
		   console.log("Video Id : "+selected_videos);
		   
	   }
	   /*
	   if(jQuery("#hdnimageids").val()=="")
	   {
		   selected_images = cid +",";
	   }
	   else
	   {
		   selected_images = jQuery("#hdnimageids").val() + cid +",";
	   }
	   jQuery("#hdnimageids").val(selected_images);
	   console.log("Selected Location : "+selected_location);
	   console.log("Image Id : "+selected_images);   
	   */
   }
   //var selected_len = jQuery("#mediya_video_container_gallery .imgselected").length;
    var selected_len = jQuery("#gallery_add .imgselected").length;
   console.log(selected_len);
   if(selected_len>0 && jQuery("#opt_filter_location").val()!=0)
   {
	   jQuery("#assign_gallery").removeClass("disabled");
	   jQuery("#assign_gallery").removeAttr("disabled");
	    
   }
   else
   {
	   jQuery("#assign_gallery").addClass("disabled");
	   jQuery("#assign_gallery").attr("disabled");
   }
   close_popup("NotificationLoadingData");
});


jQuery("#opt_filter_location").change(function(){
	console.log(jQuery(this).val());
	var selected_len = jQuery("#mediya_image_container_gallery .imgselected").length;
   console.log(selected_len);
   if(selected_len>0 && jQuery(this).val()!=0)
   {
	   jQuery("#assign_gallery").removeClass("disabled");
	   jQuery("#assign_gallery").removeAttr("disabled");
	    
   }
   else
   {
	   jQuery("#assign_gallery").addClass("disabled");
	   jQuery("#assign_gallery").attr("disabled");
   }
});

jQuery("a[id^='mediaselectassigned_']").live("click",function(){
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
		console.log(jQuery(this).parent().find(".mediya_grid1"));
		var img_ele = jQuery(this).parent().find(".mediya_grid1");
		img_ele.addClass("imgselected");
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var cid= arr[1];
	   var selected_location = jQuery("#opt_filter_location").val(); 
	   //hdnimageids
	   var selected_images = "";
	   
	   if(jQuery(this).attr("title")=="Select Image")
	   {
		   jQuery(this).attr("title","Unselect Image");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/remove_image_to_lbry.png");
		   jQuery(this).find("span").text("Unselect Image");
		   jQuery(this).parent().addClass("mediya_img_blk_selected");
		   
		   if(jQuery("#hdnimageids_assign").val()=="")
		   {
			   selected_images = cid +",";
		   }
		   else
		   {
			   selected_images = jQuery("#hdnimageids_assign").val() + cid +",";
		   }
		   jQuery("#hdnimageids_assign").val(selected_images);
		   console.log("Selected Location : "+selected_location);
		   console.log("Image Id : "+selected_images);
	   }
	   else
	   {
		   jQuery(this).attr("title","Select Image");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png");
		   jQuery(this).find("span").text("Select Image");
		   jQuery(this).parent().removeClass("mediya_img_blk_selected");
		   
		   selected_images = jQuery("#hdnimageids_assign").val();
		   if(selected_images.indexOf(cid+",")!=-1)
		   {
			   selected_images = selected_images.replace(cid+",", "");
		   }
		   var img_ele = jQuery(this).parent().find(".mediya_grid1");
		   img_ele.removeClass("imgselected");
		   
		   jQuery("#hdnimageids_assign").val(selected_images);
		   console.log("Selected Location : "+selected_location);
		   console.log("Image Id : "+selected_images);
		   
	   }
 
   }
   
   var selected_len = jQuery("#mediya_image_container_viewgallery .imgselected").length;
   console.log(selected_len);
   if(selected_len>0)
   {
	   jQuery(".fancybox-inner #popupyes_assign").removeClass("disabled");
	   jQuery(".fancybox-inner #popupyes_assign").removeAttr("disabled");
	    
   }
   else
   {
	   jQuery(".fancybox-inner #popupyes_assign").addClass("disabled");
	   jQuery(".fancybox-inner #popupyes_assign").attr("disabled");
   }
   
   close_popup("NotificationLoadingData");
});

jQuery("a[id^='mediaselectassignedvideo_']").live("click",function(){
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
		console.log(jQuery(this).parent().find(".mediya_grid1"));
		var img_ele = jQuery(this).parent().find(".mediya_grid1");
		img_ele.addClass("imgselected");
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var cid= arr[1];
	   var selected_location = jQuery("#opt_filter_location").val(); 
	   //hdnimageids
	   var selected_videos = "";
	   
	   if(jQuery(this).attr("title")=="Select Video")
	   {
		   jQuery(this).attr("title","Unselect Video");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/remove_image_to_lbry.png");
		   jQuery(this).find("span").text("Unselect Video");
		   jQuery(this).parent().addClass("mediya_img_blk_selected");
		   
		   if(jQuery("#hdnvideoids_assign").val()=="")
		   {
			   selected_videos = cid +",";
		   }
		   else
		   {
			   selected_videos = jQuery("#hdnvideoids_assign").val() + cid +",";
		   }
		   jQuery("#hdnvideoids_assign").val(selected_videos);
		   console.log("Selected Location : "+selected_location);
		   console.log("Video Id : "+selected_videos);
	   }
	   else
	   {
		   jQuery(this).attr("title","Select Video");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png");
		   jQuery(this).find("span").text("Select Video");
		   jQuery(this).parent().removeClass("mediya_img_blk_selected");
		   
		   selected_videos = jQuery("#hdnvideoids_assign").val();
		   if(selected_videos.indexOf(cid+",")!=-1)
		   {
			   selected_videos = selected_videos.replace(cid+",", "");
		   }
		   var img_ele = jQuery(this).parent().find(".mediya_grid1");
		   img_ele.removeClass("imgselected");
		   
		   jQuery("#hdnvideoids_assign").val(selected_videos);
		   console.log("Selected Location : "+selected_location);
		   console.log("Video Id : "+selected_videos);
		   
	   }
 
   }
   
   var selected_len = jQuery("#mediya_image_container_viewgallery .imgselected").length;
   console.log(selected_len);
   if(selected_len>0)
   {
	   jQuery(".fancybox-inner #popupyes_assign").removeClass("disabled");
	   jQuery(".fancybox-inner #popupyes_assign").removeAttr("disabled");
	    
   }
   else
   {
	   jQuery(".fancybox-inner #popupyes_assign").addClass("disabled");
	   jQuery(".fancybox-inner #popupyes_assign").attr("disabled");
   }
   
   close_popup("NotificationLoadingData");
});

jQuery("a[id^='mediaselectunassigned_']").live("click",function(){
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
		console.log(jQuery(this).parent().find(".mediya_grid1"));
		var img_ele = jQuery(this).parent().find(".mediya_grid1");
		img_ele.addClass("imgselected");
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var cid= arr[1];
	   var selected_location = jQuery("#opt_filter_location").val(); 
	   //hdnimageids
	   var selected_images = "";
	   
	   if(jQuery(this).attr("title")=="Select Image")
	   {
		   jQuery(this).attr("title","Unselect Image");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/remove_image_to_lbry.png");
		   jQuery(this).find("span").text("Unselect Image");
		   jQuery(this).parent().addClass("mediya_img_blk_selected");
		   
		   if(jQuery("#hdnimageids_unassign").val()=="")
		   {
			   selected_images = cid +",";
		   }
		   else
		   {
			   selected_images = jQuery("#hdnimageids_unassign").val() + cid +",";
		   }
		   jQuery("#hdnimageids_unassign").val(selected_images);
		   console.log("Selected Location : "+selected_location);
		   console.log("Image Id : "+selected_images);
	   }
	   else
	   {
		   jQuery(this).attr("title","Select Image");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png");
		   jQuery(this).find("span").text("Select Image");
		   jQuery(this).parent().removeClass("mediya_img_blk_selected");
		   
		   selected_images = jQuery("#hdnimageids_unassign").val();
		   if(selected_images.indexOf(cid+",")!=-1)
		   {
			   selected_images = selected_images.replace(cid+",", "");
		   }
		   var img_ele = jQuery(this).parent().find(".mediya_grid1");
		   img_ele.removeClass("imgselected");
		   
		   jQuery("#hdnimageids_unassign").val(selected_images);
		   console.log("Selected Location : "+selected_location);
		   console.log("Image Id : "+selected_images);
		   
	   }
	   
   }
   var selected_len = jQuery("#mediya_image_container_viewgallery .imgselected").length;
   console.log(selected_len);
   if(selected_len>0)
   {
	   jQuery(".fancybox-inner #popupyes_unassign").removeClass("disabled");
	   jQuery(".fancybox-inner #popupyes_unassign").removeAttr("disabled");
	    
   }
   else
   {
	   jQuery(".fancybox-inner #popupyes_unassign").addClass("disabled");
	   jQuery(".fancybox-inner #popupyes_unassign").attr("disabled");
   }
   
   close_popup("NotificationLoadingData");
});

jQuery("a[id^='mediaselectunassignedvideo_']").live("click",function(){
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
		console.log(jQuery(this).parent().find(".mediya_grid1"));
		var img_ele = jQuery(this).parent().find(".mediya_grid1");
		img_ele.addClass("imgselected");
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   var src=jQuery(this).attr('src');
	   var cid= arr[1];
	   var selected_location = jQuery("#opt_filter_location").val(); 
	   //hdnimageids
	   var selected_videos = "";
	   
	   if(jQuery(this).attr("title")=="Select Video")
	   {
		   jQuery(this).attr("title","Unselect Video");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/remove_image_to_lbry.png");
		   jQuery(this).find("span").text("Unselect Video");
		   jQuery(this).parent().addClass("mediya_img_blk_selected");
		   
		   if(jQuery("#hdnvideoids_unassign").val()=="")
		   {
			   selected_videos = cid +",";
		   }
		   else
		   {
			   selected_videos = jQuery("#hdnvideoids_unassign").val() + cid +",";
		   }
		   jQuery("#hdnvideoids_unassign").val(selected_videos);
		   console.log("Selected Location : "+selected_location);
		   console.log("Video Id : "+selected_videos);
	   }
	   else
	   {
		   jQuery(this).attr("title","Select Video");
		   jQuery(this).find("img").attr("src","<?php echo ASSETS_IMG ?>/m/add_image_to_lbry.png");
		   jQuery(this).find("span").text("Select Video");
		   jQuery(this).parent().removeClass("mediya_img_blk_selected");
		   
		   selected_videos = jQuery("#hdnvideoids_unassign").val();
		   if(selected_videos.indexOf(cid+",")!=-1)
		   {
			   selected_videos = selected_videos.replace(cid+",", "");
		   }
		   var img_ele = jQuery(this).parent().find(".mediya_grid1");
		   img_ele.removeClass("imgselected");
		   
		   jQuery("#hdnvideoids_unassign").val(selected_videos);
		   console.log("Selected Location : "+selected_location);
		   console.log("Video Id : "+selected_videos);
		   
	   }
	   
   }
   var selected_len = jQuery("#mediya_image_container_viewgallery .imgselected").length;
   console.log(selected_len);
   if(selected_len>0)
   {
	   jQuery(".fancybox-inner #popupyes_unassign").removeClass("disabled");
	   jQuery(".fancybox-inner #popupyes_unassign").removeAttr("disabled");
	    
   }
   else
   {
	   jQuery(".fancybox-inner #popupyes_unassign").addClass("disabled");
	   jQuery(".fancybox-inner #popupyes_unassign").attr("disabled");
   }
   
   close_popup("NotificationLoadingData");
});

jQuery("#assign_gallery").live("click",function(){
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
		var selected_location = jQuery("#opt_filter_location").val(); 
		var selected_images = jQuery("#hdnimageids").val();
		selected_images = selected_images.substr(0,selected_images.length-1);
		jQuery("#hdnimageids").val("");
		console.log("Selected Location : "+selected_location);
		console.log("Image Id : "+selected_images);
		
		var selected_videos = jQuery("#hdnvideoids").val();
		selected_videos = selected_videos.substr(0,selected_videos.length-1);
		jQuery("#hdnvideoids").val("");
		console.log("Selected Location : "+selected_location);
		console.log("video Id : "+selected_videos);
		
		
		jQuery.ajax({
		  type: "POST",
		  url: "<?=WEB_PATH?>/merchant/process.php",
		  data: "location_id=" + selected_location +"&video_ids="+selected_videos+"&image_ids="+ selected_images +"&btnAddlocationgallery=yes",
		  async : false,
		  success: function(msg) {
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{
				var if_tab = "<?php if(isset($_REQUEST["tab"]) && $_REQUEST["tab"]=="imagegallery")echo "1"; else "0";?>";
				if(if_tab)
				{
					window.location.href=window.location.href;
				}
				else
				{
					window.location.href+="?tab=imagegallery";
				}
			}
			else
			{
				console.log("Some images or video not exist in system.");
				
				var msg = "<div style='width: 252px;'>Some images or video not exist in system.</div>";
				var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>";
				var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
				var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='OK' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

				jQuery.fancybox({
					content: jQuery('#dialog-message').html(),
					type: 'html',
					openSpeed: 300,
					closeSpeed: 300,
					// topRatio: 0,

					changeFade: 'fast',
					beforeShow: function () {
						$(".fancybox-inner").addClass("msgClass");
					},
					helpers: {
						overlay: {
							opacity: 0.3
						} // overlay
					}
				});
	
			}
			
		  }
	   });
	   
		
	}
	close_popup("NotificationLoadingData");
});
jQuery(".deletemsgclass_close").click(function(){

	jQuery(".deletemsgclass").html("");
	jQuery(".deletemsgclass").css("display","none");
	jQuery(".deletemsgclass_close").css("display","none");
});
jQuery(".deletemsgclass_close1").click(function(){

	jQuery(".msgclass").html("");
	jQuery(".msgclass").css("display","none");
	jQuery(".deletemsgclass_close1").css("display","none");
	jQuery(".deleteprocessmsgclass_close1").css("display","none");
}); 

jQuery(".deleteprocessmsgclass_close1").click(function(){

	jQuery(".tbl_process_msg_video").html("");
	jQuery(".tbl_process_msg_video").css("display","none");
	jQuery(".deleteprocessmsgclass_close1").css("display","none");
}); 


	 
var urllist = [];
jQuery("#btnadd").click(function(){
	
	var url=jQuery("#url").val();
	
	var ytId = ytVidId(jQuery("#url").val());
	console.log(ytId);
        
	var flaglogin=0;
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
				flaglogin=1;
				window.location.href=obj.link;
			}
			else
			{
			}
		}
	});
	
	if(flaglogin == 1)
	{
		return false;
	}
	else
	{
		
		if(ytId)
		{
			
	
			console.log("youtube video found");
			//var videoId="dYGJpRhC9VI";
			var videoId=jQuery("#url").val();
				
			videoId = ytId;
			console.log(videoId);
			
			var urlindex = urllist.indexOf(videoId);
			if(urlindex==-1)
			{
				/*
				jQuery(".tbl_process_msg_video").html("Validating video...");
				jQuery(".tbl_process_msg_video").show();
				jQuery(".deleteprocessmsgclass_close1").show();
				*/		
				var myApiKey="AIzaSyCZDH77Jwgv41TJEUDCbxzBrEFvV2IbjmY";
				$.getJSON('https://www.googleapis.com/youtube/v3/videos?id='+videoId+'&key='+myApiKey+'&part=snippet',function(data){
					if (typeof(data.items[0]) != "undefined") {
						console.log('video exists');
						console.log('id : ' + data.items[0].id);
						console.log('title : ' + data.items[0].snippet.title);
						console.log('description : ' + data.items[0].snippet.description);
						console.log('thumbnail : ' + data.items[0].snippet.thumbnails.medium.url);
						
						urllist.push(videoId);				
						
						var v_id=data.items[0].id;
						var v_title=data.items[0].snippet.title;
						var v_thumbnail_url=data.items[0].snippet.thumbnails.medium.url;
						
						jQuery("#urllist").append("<div class='urldiv' vid='"+v_id+"' vtitle='"+v_title+"' vlink='"+url+"' vthumburl='"+v_thumbnail_url+"' vtype='youtube' ><div>"+urllist.length+"</div><div>"+url+"</div><div>Video detected</div><div class='delete_url_row'><img src='<?php echo ASSETS_IMG."/m/mediya_delete.png" ?>' title='Delete'/></div></div>");
								
						console.log(urllist);
						jQuery("#url").val("");
						//jQuery(".tbl_process_msg_video").html("Video has been detected.");		
						/*
						jQuery.ajax({
							type:"POST",
							url:'process.php',
							data :'btnAddVideoOfMerchant=true&video_type=youtube&video_link='+jQuery("#url").val()+'&video_id='+data.items[0].id+'&thumbnail_url='+data.items[0].snippet.thumbnails.medium.url+'&video_title='+data.items[0].snippet.title,
							async:false,
							success:function(msg)
							{
								var obj = jQuery.parseJSON(msg);
								if (obj.status=="false")     
								{
									console.log('video ID already exist');
									jQuery(".msgclass").css("display","block");
									jQuery(".tbl_eror_msg_video").css("display","block");
									jQuery(".tbl_eror_msg_video").html('video ID already exist');
									jQuery(".tbl_eror_msg_video").next().css("display","block");
								}
								else
								{
									var msg_box="Your video has been added successfully.";
									var head_msg="<div class='head_msg'>Message</div>"
									var content_msg="<div class='content_msg'>"+msg_box+"</div>";
									var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel1' name='popupcancel1' class='msg_popup_cancel'></div>";
									jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
									
									jQuery.fancybox({
										content:jQuery('#dialog-message').html(),											
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
								}
							}
						});
						*/
						jQuery(".tbl_eror_msg_video").css("display","none");
						jQuery(".tbl_eror_msg_video").html('');
						jQuery(".tbl_eror_msg_video").next().css("display","none");
					} 
					else 
					{
						/*
						console.log('youtube ID not found');
						//jQuery(".msgclass").css("display","block");
						jQuery(".tbl_eror_msg_video").css("display","block");
						jQuery(".tbl_eror_msg_video").html('youtube ID not found');
						jQuery(".tbl_eror_msg_video").next().css("display","block");
						*/
						jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>Youtube ID not found</span></div>");
						jQuery("#videonotificationcontainer").show();
						setTimeout(function(){
							jQuery(".videonotification").fadeOut('slow');
						},5000);
						
					}   
				});
			}
			else
			{
				/*
				console.log('Duplicate video ID found.');
				//jQuery(".msgclass").css("display","block");
				jQuery(".tbl_eror_msg_video").css("display","block");
				jQuery(".tbl_eror_msg_video").html('Duplicate video ID found.');
				jQuery(".tbl_eror_msg_video").next().css("display","block");
				*/
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>Duplicate video ID found</span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
			}
		}
		else if(ytId==false && url.indexOf("vimeo")>-1)
		{
			
	
			console.log("vimeo video found");
			var videoId=jQuery("#url").val();
			var start_index=videoId.lastIndexOf("/");
			videoId = videoId.substring(start_index+1);
			console.log(videoId);
			
			var urlindex = urllist.indexOf(videoId);
			if(urlindex==-1)
			{	
				/*
				jQuery(".tbl_process_msg_video").html("Validating video...");
				jQuery(".tbl_process_msg_video").show();
				jQuery(".deleteprocessmsgclass_close1").show();
				*/		
				UrlExists('https://vimeo.com/api/oembed.json?url='+jQuery("#url").val(), function(status){
					if(status === 200){
						// file was found
						console.log('video exists');
						$.getJSON('https://vimeo.com/api/v2/video/'+videoId+'.json',function(data){
							if (typeof(data[0]) != "undefined") 
							{
								console.log('video exists');
								console.log('id : ' + data[0].id);
								console.log('title : ' + data[0].title);
								console.log('description : ' + data[0].description);
								console.log('thumbnail : ' + data[0].thumbnail_medium);
								
								urllist.push(videoId);
								
								var v_id=data[0].id;
								var v_title=data[0].title;
								var v_thumbnail_url=data[0].thumbnail_medium;
								
								jQuery("#urllist").append("<div class='urldiv' vid='"+v_id+"' vtitle='"+v_title+"' vlink='"+url+"' vthumburl='"+v_thumbnail_url+"' vtype='vimeo' ><div>"+urllist.length+"</div><div>"+url+"</div><div>Video detected</div><div class='delete_url_row'><img src='<?php echo ASSETS_IMG."/m/mediya_delete.png" ?>' title='Delete'/></div></div>");
								console.log(urllist);
								jQuery("#url").val("");		
								//jQuery(".tbl_process_msg_video").html("Video has been detected.");
								/*
								jQuery.ajax({
									type:"POST",
									url:'process.php',
									data :'btnAddVideoOfMerchant=true&video_type=vimeo&video_link='+jQuery("#url").val()+'&video_id='+data[0].id+'&thumbnail_url='+data[0].thumbnail_medium+'&video_title='+data[0].title,
									async:false,
									success:function(msg)
									{
										var obj = jQuery.parseJSON(msg);
										if (obj.status=="false")     
										{
											console.log('video ID already exist');
											jQuery(".msgclass").css("display","block");
											jQuery(".tbl_eror_msg_video").css("display","block");
											jQuery(".tbl_eror_msg_video").html('video ID already exist');
											jQuery(".tbl_eror_msg_video").next().css("display","block");
										}
										else
										{
											var msg_box="Your video has been added successfully.";
											var head_msg="<div class='head_msg'>Message</div>"
											var content_msg="<div class='content_msg'>"+msg_box+"</div>";
											var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel1' name='popupcancel1' class='msg_popup_cancel'></div>";
											jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
											
											jQuery.fancybox({
												content:jQuery('#dialog-message').html(),											
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
				
										}
									}
								});
								*/
								jQuery(".tbl_eror_msg_video").css("display","none");
								jQuery(".tbl_eror_msg_video").html('');
								jQuery(".tbl_eror_msg_video").next().css("display","none");
							} 
							else 
							{
								/*
								console.log('video not exists');
								//jQuery(".msgclass").css("display","block");
								jQuery(".tbl_eror_msg_video").css("display","block");
								jQuery(".tbl_eror_msg_video").html('video not exists');
								jQuery(".tbl_eror_msg_video").next().css("display","block");
								*/
								
								jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>video not exists</span></div>");
								jQuery("#videonotificationcontainer").show();
								setTimeout(function(){
									jQuery(".videonotification").fadeOut('slow');
								},5000);
						
							}   
						});
					}
					else if(status === 404){
					   // 404 not found
					   /*
					   console.log('vimeo ID not found');
					   //jQuery(".msgclass").css("display","block");
					   jQuery(".tbl_eror_msg_video").css("display","block");
					   jQuery(".tbl_eror_msg_video").html('vimeo ID not found');
					   jQuery(".tbl_eror_msg_video").next().css("display","block");
					   */
					   
						jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>Vimeo ID not found</span></div>");
						jQuery("#videonotificationcontainer").show();
						setTimeout(function(){
							jQuery(".videonotification").fadeOut('slow');
						},5000);
								
					}
				});
			}
			else
			{
				/*
				console.log('Duplicate video ID found.');
				//jQuery(".msgclass").css("display","block");
				jQuery(".tbl_eror_msg_video").css("display","block");
				jQuery(".tbl_eror_msg_video").html('Duplicate video ID found.');
				jQuery(".tbl_eror_msg_video").next().css("display","block");
				*/
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>Duplicate video ID found</span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
			}
		}
		else
		{
			/*
			console.log("video not found");
			//jQuery(".msgclass").css("display","block");
			jQuery(".tbl_eror_msg_video").css("display","block");
			jQuery(".tbl_eror_msg_video").html('video not found');
			jQuery(".tbl_eror_msg_video").next().css("display","block");
			*/
			jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>Video not found</span></div>");
			jQuery("#videonotificationcontainer").show();
			setTimeout(function(){
				jQuery(".videonotification").fadeOut('slow');
			},5000);
		}
		
		/*
		if(url.indexOf("vimeo")>-1)
		{
			console.log("vimeo video found");
			var videoId=jQuery("#url").val();
			var start_index=videoId.lastIndexOf("/");
			videoId = videoId.substring(start_index+1);
			console.log(videoId);
	
			UrlExists('https://vimeo.com/api/oembed.json?url='+jQuery("#url").val(), function(status){
				if(status === 200){
					// file was found
					console.log('video exists');
					$.getJSON('https://vimeo.com/api/v2/video/'+videoId+'.json',function(data){
						if (typeof(data[0]) != "undefined") 
						{
							console.log('video exists');
							console.log('id : ' + data[0].id);
							console.log('title : ' + data[0].title);
							console.log('description : ' + data[0].description);
							console.log('thumbnail : ' + data[0].thumbnail_medium);
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'btnAddVideoOfMerchant=true&video_type=vimeo&video_link='+jQuery("#url").val()+'&video_id='+data[0].id+'&thumbnail_url='+data[0].thumbnail_medium+'&video_title='+data[0].title,
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
									}
									else
									{
										var msg_box="Your video has been added successfully.";
										var head_msg="<div class='head_msg'>Message</div>"
										var content_msg="<div class='content_msg'>"+msg_box+"</div>";
										var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel1' name='popupcancel1' class='msg_popup_cancel'></div>";
										jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
										
										jQuery.fancybox({
											content:jQuery('#dialog-message').html(),											
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
			
									}
								}
							});
						} 
						else 
						{
							console.log('video not exists');
							jQuery(".msgclass").css("display","block");
							jQuery(".tbl_eror_msg_video").css("display","block");
							jQuery(".tbl_eror_msg_video").html('video not exists');
							jQuery(".tbl_eror_msg_video").next().css("display","block");
						}   
					});
				}
				else if(status === 404){
				   // 404 not found
				   console.log('video not exists');
				   jQuery(".msgclass").css("display","block");
				   jQuery(".tbl_eror_msg_video").css("display","block");
				   jQuery(".tbl_eror_msg_video").html('video not exists');
				   jQuery(".tbl_eror_msg_video").next().css("display","block");
				}
			});		
		}
		else if(url.indexOf("youtube")>-1)
		{
			console.log("youtube video found");
			//var videoId="dYGJpRhC9VI";
			var videoId=jQuery("#url").val();
			var start_index=videoId.indexOf("v=");
			videoId = videoId.substring(start_index+2);
			videoId = videoId.substring(0,11);
			console.log(videoId);
			var myApiKey="AIzaSyCZDH77Jwgv41TJEUDCbxzBrEFvV2IbjmY";
			$.getJSON('https://www.googleapis.com/youtube/v3/videos?id='+videoId+'&key='+myApiKey+'&part=snippet',function(data){
				if (typeof(data.items[0]) != "undefined") {
					console.log('video exists');
					console.log('id : ' + data.items[0].id);
					console.log('title : ' + data.items[0].snippet.title);
					console.log('description : ' + data.items[0].snippet.description);
					console.log('thumbnail : ' + data.items[0].snippet.thumbnails.medium.url);
					
					jQuery.ajax({
						type:"POST",
						url:'process.php',
						data :'btnAddVideoOfMerchant=true&video_type=youtube&video_link='+jQuery("#url").val()+'&video_id='+data.items[0].id+'&thumbnail_url='+data.items[0].snippet.thumbnails.medium.url+'&video_title='+data.items[0].snippet.title,
						async:false,
						success:function(msg)
						{
							var obj = jQuery.parseJSON(msg);
							if (obj.status=="false")     
							{
							}
							else
							{
								var msg_box="Your video has been added successfully.";
								var head_msg="<div class='head_msg'>Message</div>"
								var content_msg="<div class='content_msg'>"+msg_box+"</div>";
								var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel1' name='popupcancel1' class='msg_popup_cancel'></div>";
								jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
								
								jQuery.fancybox({
									content:jQuery('#dialog-message').html(),											
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
							}
						}
					});
				} 
				else 
				{
					console.log('video not exists');
					jQuery(".msgclass").css("display","block");
					jQuery(".tbl_eror_msg_video").css("display","block");
					jQuery(".tbl_eror_msg_video").html('video not exists');
					jQuery(".tbl_eror_msg_video").next().css("display","block");
				}   
			});

		}
		else
		{
			console.log("other video found");
			jQuery(".msgclass").css("display","block");
			jQuery(".tbl_eror_msg_video").css("display","block");
			jQuery(".tbl_eror_msg_video").html('other video found');
			jQuery(".tbl_eror_msg_video").next().css("display","block");
		}
		*/
	}
	
});
	function ytVidId(url) {
	  var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
	  return (url.match(p)) ? RegExp.$1 : false;
	}
	function UrlExists(url, cb){
		jQuery.ajax({
			url:      url,
			dataType: 'text',
			type:     'GET',
			complete:  function(xhr){
				if(typeof cb === 'function')
				   cb.apply(this, [xhr.status]);
			}
		});
	}
	
	
jQuery("#btnProcessUrl").click(function(){
	
	var flaglogin=0;
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
				flaglogin=1;
				window.location.href=obj.link;
			}
			else
			{
			}
		}
	});
	
	if(flaglogin == 1)
	{
		return false;
	}
	else
	{
		
		console.log(jQuery('#urllist .urldiv').length);
		if(jQuery('#urllist .urldiv').length==0)
		{
			/*
			console.log('Please add atleast one url');
			//jQuery(".msgclass").css("display","block");
			jQuery(".tbl_eror_msg_video").css("display","block");
			jQuery(".tbl_eror_msg_video").html('Please add atleast one url');
			jQuery(".tbl_eror_msg_video").next().css("display","block");
			*/
			
			jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>Please add atleast one url</span></div>");
			jQuery("#videonotificationcontainer").show();
			setTimeout(function(){
				jQuery(".videonotification").fadeOut('slow');
			},5000);
				
		}
		else
		{
			/*
			var msg_box="Your videos are being processed.";
			var head_msg="<div class='head_msg'>Message</div>"
			var content_msg="<div class='content_msg'>"+msg_box+"</div>";
			var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel1' name='popupcancel1' class='msg_popup_cancel'></div>";
			footer_msg="";
			jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
			
			jQuery.fancybox({
				content:jQuery('#dialog-message').html(),											
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
			*/				
			var str_msg="";
			var suces_cnt=0;
			var flr_cnt=0;
			var suces_vid="";
			var flr_vid="";
			var false_upload=0;
			var false_upload_msg="";
			
			jQuery('#urllist .urldiv').each(function(){
				
				var v_id=jQuery(this).attr("vid");
				var v_title=jQuery(this).attr("vtitle");
				var v_thumbnail_url=jQuery(this).attr("vthumburl");
				var v_type=jQuery(this).attr("vtype");
				var v_link=jQuery(this).attr("vlink");
				console.log("v_id : "+v_id);
				console.log("v_title : "+v_title);
				console.log("v_thumbnail_url : "+v_thumbnail_url);
				console.log("v_type : "+v_type);
				console.log("v_link : "+v_link);
				
				//jQuery(".urldiv[vid='"+v_id+"']").append("<div class='videostatus'>Processing...</div>");
				
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'btnAddVideoOfMerchant=true&video_type='+v_type+'&video_link='+v_link+'&video_id='+v_id+'&thumbnail_url='+v_thumbnail_url+'&video_title='+v_title,
					async:false,
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if(obj.status=="false_upload")     
						{
							false_upload=1;
							false_upload_msg=obj.message;
							console.log("false_upload = "+false_upload);
						}
						else if(obj.status=="false")     
						{
							flr_cnt = flr_cnt + 1;
							flr_vid += v_id +",";
							/*
							jQuery("#videonotificationcontainer").append("<div id='"+v_id+"' class='videonotification'><span class='redspan'>video Id "+v_id+" already exist</span></div>");
					
							//jQuery('#'+v_id).delay(5000).fadeOut('slow');
							
							setTimeout(function(){
								jQuery('#'+v_id).fadeOut('slow');
							},5000);
							*/
							
						}
						else
						{
							suces_cnt = suces_cnt + 1;
							suces_vid += v_id +",";
							/*
							jQuery("#videonotificationcontainer").append("<div id='"+v_id+"' class='videonotification'><span class='greenspan'>video Id "+v_id+" added successfully</span></div>");
							
							//jQuery('#'+v_id).delay(5000).fadeOut('slow');
							
							setTimeout(function(){
								jQuery('#'+v_id).fadeOut('slow');
							},5000);
							*/
							
						}
					}
				});
		
			});
			console.log("flr_cnt = "+flr_cnt);
			console.log("false_upload = "+false_upload);
			if(false_upload==1)
			{
				
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>"+false_upload_msg+"</span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
			}
			
			if(false_upload==0 && flr_cnt==0)
			{
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='greenspan'>All videos added successfully</span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
			}
			else
			{
				suces_vid=suces_vid.substring(0, suces_vid.length - 1);
				flr_vid=flr_vid.substring(0, flr_vid.length - 1);
				if(suces_cnt>0 && flr_cnt>0)
				{
					jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='greenspan'>Video Ids "+suces_vid+" added successfully</span></div><div class='videonotification'><span class='redspan'>Video Ids "+flr_vid+" already exist</span></div>");
				}
				else if(suces_cnt>0)
				{
					jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='greenspan'>Video Ids "+suces_vid+" added successfully</span></div>");
				}
				else if(flr_cnt>0)
				{
					jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'>Video Ids "+flr_vid+" already exist</span></div>");
				}
				
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
		
			}
			if(suces_cnt>0)
			{
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'btnGetVideoOfMerchantAjax=true',
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="true") 
						{
							jQuery('#div_video').html(obj.html);
						}
					}
				});
			}
			
			
			jQuery("#urllist").html("");
			urllist=[];
			//jQuery.fancybox.close();
		}
	}
});	
	jQuery(document).on( "click","#popupcancel1",function(){
        jQuery.fancybox.close(); 
        window.location.href=window.location.href;
		return false; 
    });
    
    
    jQuery("#show_more_videos").live("click",function(){
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
			var img_filter = jQuery(".fltr_button_video").val();
			//alert(img_filter);
			
			var cur_el= jQuery(this);
			var next_index = parseInt(jQuery(this).attr('next_index'));
			var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
			
			jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'show_more_video=yes&next_index='+next_index+'&num_of_records='+num_of_records,
				async:true,
				success:function(msg)
				{
					var obj = jQuery.parseJSON(msg);

					if (obj.status=="true")     
					{
						jQuery("#mediya_video_container").append(obj.html);
						cur_el.attr('next_index',next_index + num_of_records);
						if(parseInt(obj.records_return)<num_of_records)
						{
							cur_el.css("display","none");
						}
						
						if(img_filter=="All Videos")
						{
							jQuery("#mediya_video_container .mediya_video_blk.youtubefilter").css("display","block");
							jQuery("#mediya_video_container .mediya_video_blk.vimeofilter").css("display","block");
						}
						else if(img_filter=="Youtube")
						{
							jQuery("#mediya_video_container .mediya_video_blk.vimeofilter").css("display","none");	
							jQuery("#mediya_video_container .mediya_video_blk.youtubefilter").css("display","block");
						}
						else if(img_filter=="Vimeo")
						{
							jQuery("#mediya_video_container .mediya_video_blk.youtubefilter").css("display","none");
							jQuery("#mediya_video_container .mediya_video_blk.vimeofilter").css("display","block");	
						}								
					}
				}
			});
		}
		close_popup("NotificationLoadingData");	
	});

jQuery(document).on('click',".fltr_button_video",function(){
	
	var img_filter = jQuery(this).val();
	//alert(img_filter);
	
	jQuery("div.filters_video input").removeClass("fltr_selected_video");

	if(img_filter=="All Videos")
	{
		jQuery(this).addClass("fltr_selected_video");
		jQuery("#mediya_video_container .mediya_video_blk.youtubefilter").css("display","block");
		jQuery("#mediya_video_container .mediya_video_blk.vimeofilter").css("display","block");
	}
	else if(img_filter=="Youtube")
	{
		jQuery(this).addClass("fltr_selected_video");
		jQuery("#mediya_video_container .mediya_video_blk.vimeofilter").css("display","none");	
		jQuery("#mediya_video_container .mediya_video_blk.youtubefilter").css("display","block");
	}
	else if(img_filter=="Vimeo")
	{
		jQuery(this).addClass("fltr_selected_video");
		jQuery("#mediya_video_container .mediya_video_blk.youtubefilter").css("display","none");
		jQuery("#mediya_video_container .mediya_video_blk.vimeofilter").css("display","block");	
	}
	jQuery("#div_video #mediya_video_container").css("display","inline-block");
	//alert(jQuery("#div_video #mediya_video_container .mediya_video_blk:visible").length);
	if(jQuery("#div_video #mediya_video_container .mediya_video_blk:visible").length==0)
	{
		jQuery("#div_video .deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_no_videos_with_filter']; ?>");
		jQuery("#div_video .deletemsgclass").css("display","block");
		jQuery("#div_video .deletemsgclass_close").css("display","block");
		jQuery("#div_video #mediya_video_container").css("display","none");
		jQuery("#div_video #mediya_showmore_videos").css("display","none");
	}
	else
	{
		jQuery("#div_video .deletemsgclass").html("");	
		jQuery("#div_video .deletemsgclass").css("display","none");
		jQuery("#div_video .deletemsgclass_close").css("display","none");
		jQuery("#div_video #mediya_video_container").css("display","inline-block");
		jQuery("#div_video #mediya_showmore_videos").css("display","block");
	}
	if(img_filter=="All Videos")
	{
		jQuery("#div_video #mediya_showmore_videos").css("display","block");
	}
	else
	{
		jQuery("#div_video #mediya_showmore_videos").css("display","none");
	}
});
 
jQuery("a[id^='videodeleteid_']").live("click",function(){
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
		
		
	   var cur_div = jQuery(this);	
	   var arr = jQuery(this).attr("id").split("_");
	   //var image_type_filter=jQuery("#imagetype").val();
	   var image_type=jQuery(this).attr("image_type");
	   //var src=jQuery(this).attr('src');
	   //var admin_image=jQuery(this).attr('admin_image');
	   var cid= arr[1];
		jQuery.ajax({
		  type: "POST",
		  url: "<?=WEB_PATH?>/merchant/process.php",
		  data: "id=" + cid +"&videoactiondelete=yes",
		  async : false,
		  success: function(msg) {
			if(msg=="success")
			{
				 
				 // 08 01 2015 remove div when delete
				 
				 cur_div.parent().remove();
				 
				 // 08 01 2015 remove div when delete
				 /*
				 jQuery("#div_video .deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]['Msg_video_deleted']; ?>");
				 jQuery("#div_video .deletemsgclass").css("display","block");
				 jQuery("#div_video .deletemsgclass_close").css("display","block");
				 */
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='greenspan'><?php echo $merchant_msg["mediamanagement"]['Msg_video_deleted']; ?></span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
			 
				
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'btnGetVideoOfMerchantAjaxInGallery=true',
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="true") 
						{
							jQuery('#mediya_video_container_gallery').html(obj.html);
						}
					}
				}); 
				
			}
			else
			{
				jQuery("#videonotificationcontainer").html("<div class='videonotification'><span class='vdonotification_close'>X</span><span class='redspan'><?php echo $merchant_msg["mediamanagement"]['Msg_cant_delete_video']; ?></span></div>");
				jQuery("#videonotificationcontainer").show();
				setTimeout(function(){
					jQuery(".videonotification").fadeOut('slow');
				},5000);
			}
				   
			//alert(jQuery(".mediya_img_blk").length);
			
			if(jQuery("#mediya_video_container .mediya_video_blk").length==0)
			{
				//jQuery("#mediya_video_container").append("<div class='mediya_video_blk mediya_upload_new' onclick='call_upload()' ><img src='/assets/images/m/upload_img.png' /></div>")
				jQuery("#div_video .deletemsgclass").html("<?php echo $merchant_msg["mediamanagement"]["Msg_no_videos"];?>");
				jQuery("#div_video .deletemsgclass").css("display","block");
				jQuery("#div_video .deletemsgclass_close").css("display","block");
				jQuery("#mediya_video_container").css("display","none");
			}
			
		  }
	   });
	   close_popup("NotificationLoadingData");
   }
});

jQuery(document).on('click',".delete_url_row",function(){
	jQuery(this).parent().remove();
	var index = urllist.indexOf(jQuery(this).parent().attr('vid'));
	if (index > -1) {
		urllist.splice(index, 1);
	}
});

jQuery(document).on('click',".vdonotification_close",function(){
	jQuery(".videonotification").fadeOut('slow');
});
jQuery(document).on('click',"#upload_image_guideline",function(){
	var linkurl="";
	var msg = "<div style='width: 252px;'> <li>All location images uploaded must be at least 400 pixel x 200 pixel ( width x height).</li><li>All campaigns and gift card images uploaded must be at least 1600 pixel x 1200 pixel ( width x height ).</li><li>Max file size up to 10MB.</li></div>";
	var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Upload image guideline</div>"
	var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
	var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='OK' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
	jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

	jQuery.fancybox({
		content: jQuery('#dialog-message').html(),
		type: 'html',
		openSpeed: 300,
		closeSpeed: 300,
		// topRatio: 0,
		openEffect :'none',
		closeEffect :'none',
		changeFade: 'fast',
		beforeShow: function () {
			$(".fancybox-inner").addClass("msgClass");
		},
		helpers: {
			overlay: {
				opacity: 0.3
			} // overlay
		}
	});
});
</script>
<script type="text/javascript">
							jQuery(function() {
	
	jQuery("#uploader").plupload({  
	//var uploader = new plupload.Uploader({
		
		//container : 'uploader',
		 	
		// General settings
		runtimes : 'html5,flash,silverlight,html4',
		url : '<?=WEB_PATH?>/merchant/plupload.php',

		// User can upload no more then 20 files in one go (sets multiple_queues to false)
		max_file_count: 20,
		
		chunk_size: '10mb',
		/*
		// Resize images on clientside if we can
		resize : {
			width : 200, 
			height : 200, 
			quality : 90,
			crop: true // crop to exact dimensions
		},
		*/
		filters : {
			// Minimum file width
			min_width: 700,
			// Maximum file size
			max_file_size : '10mb',
			//max_file_size : '200kb',
			// Specify what files to browse for
			mime_types: [
				{title : "Image files", extensions : "jpg,gif,jpeg,png"},
				//{title : "Zip files", extensions : "zip"}
			]
		},

		// Rename files by clicking on their titles
		//rename: true,
		//unique_names:true,
		// Sort files
		sortable: true,

		// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
		dragdrop: true,

		// Views to activate
		views: {
			list: true,
			thumbs: true, // Show thumbs
			active: 'list'
		},

		// Flash settings
		flash_swf_url : '<?php echo ASSETS ?>/jquery.ui.plupload/js/Moxie.swf',

		// Silverlight settings
		silverlight_xap_url : '<?php echo ASSETS ?>/jquery.ui.plupload/js/Moxie.xap',
		multipart: true,
		multipart_params : {
			"image_type" : jQuery("#selectimg").val()
		},
		preinit : {
            Init: function(up, info) {
                //log('[Init]', 'Info:', info, 'Features:', up.features);  
            },
 
            UploadFile: function(up, file) {
                //log('[UploadFile]', file);
 
                // You can override settings before the file is uploaded
                // up.setOption('url', 'upload.php?id=' + file.id);
                // up.setOption('multipart_params', {param1 : 'value1', param2 : 'value2'});
            }
        },
		init : {
            PostInit: function() {
                // Called after initialization is finished and internal event handlers bound
                //log('[PostInit]');
                /* 
                document.getElementById('uploadfiles').onclick = function() {
                    uploader.start();
                    return false;
                };
                */
            },
 
            Browse: function(up) {
                // Called when file picker is clicked
                
                //log('[Browse]');
            },
 
            Refresh: function(up) {
                // Called when the position or dimensions of the picker change
                //log('[Refresh]');
            },
  
            StateChanged: function(up) {
                // Called when the state of the queue is changed
                //log('[StateChanged]', up.state == plupload.STARTED ? "STARTED" : "STOPPED");
            },
  
            QueueChanged: function(up) {
                // Called when queue is changed by adding or removing files
                if(jQuery("#selectimg").val()==0)
                {
					console.log("please select image type");
					jQuery("#uploader_start").addClass("ui-state-disabled");
				}
				else
				{
					jQuery("#uploader_start").removeClass("ui-state-disabled");
				}
                //log('[QueueChanged]');
            },
 
            OptionChanged: function(up, name, value, oldValue) {
                // Called when one of the configuration options is changed
                //log('[OptionChanged]', 'Option Name: ', name, 'Value: ', value, 'Old Value: ', oldValue);
            },
 
            BeforeUpload: function(up, file) {
                // Called right before the upload for a given file starts, can be used to cancel it if required
                up.settings.multipart_params = {'image_type': jQuery("#selectimg").val()}
                //log('[BeforeUpload]', 'File: ', file);
            },
  
            UploadProgress: function(up, file) {
                // Called while file is being uploaded
                //log('[UploadProgress]', 'File:', file, "Total:", up.total);
            },
 
            FileFiltered: function(up, file) {
                // Called when file successfully files all the filters
                //log('[FileFiltered]', 'File:', file);
    
            },
  
            FilesAdded: function(up, files) {
                // Called when files are added to queue
                //log('[FilesAdded]');
				
                plupload.each(files, function(file) {
                    //log('  File:', file);
                });
                
            },
  
            FilesRemoved: function(up, files) {
                // Called when files are removed from queue
                //log('[FilesRemoved]');
  
                plupload.each(files, function(file) {
                    //log('  File:', file);
                });
            },
  
            FileUploaded: function(up, file, info) {
                // Called when file has finished uploading
                //log('[FileUploaded] File:', file, "Info:", info);
                
                var response = jQuery.parseJSON(info.response);
				if(typeof(response.error) != "undefined")
				{
					//alert(response.error.message);
					if(response.error.code==500)
					{
						var str_msg='<div class="plupload_message ui-state-error">';
						str_msg+='<span title="Close" class="plupload_message_close ui-icon ui-icon-circle-close"></span>';
						str_msg+='<p><span class="ui-icon ui-icon-alert"></span><strong>Max File upload error.</strong>'; 
						str_msg+='<br><i>'+response.error.message+'</i>';
						str_msg+='</p>';
						str_msg+='</div>';	
						jQuery(".plupload_header").append(str_msg);
					}
					if(response.error.code==501)
					{
						var str_msg='<div class="plupload_message ui-state-error">';
						str_msg+='<span title="Close" class="plupload_message_close ui-icon ui-icon-circle-close"></span>';
						str_msg+='<p><span class="ui-icon ui-icon-alert"></span><strong>File width & height error.</strong>'; 
						str_msg+='<br><i>'+response.error.message+'</i>';
						str_msg+='</p>';
						str_msg+='</div>';	
						jQuery(".plupload_header").append(str_msg);
					}
					if(response.error.code==502)
					{
						var str_msg='<div class="plupload_message ui-state-error">';
						str_msg+='<span title="Close" class="plupload_message_close ui-icon ui-icon-circle-close"></span>';
						str_msg+='<p><span class="ui-icon ui-icon-alert"></span><strong>File width & height error.</strong>'; 
						str_msg+='<br><i>'+response.error.message+'</i>';
						str_msg+='</p>';
						str_msg+='</div>';	
						jQuery(".plupload_header").append(str_msg);
					}
					if(response.error.code==503)
					{
						var str_msg='<div class="plupload_message ui-state-error">';
						str_msg+='<span title="Close" class="plupload_message_close ui-icon ui-icon-circle-close"></span>';
						str_msg+='<p><span class="ui-icon ui-icon-alert"></span><strong>File width & height error.</strong>'; 
						str_msg+='<br><i>'+response.error.message+'</i>';
						str_msg+='</p>';
						str_msg+='</div>';	
						jQuery(".plupload_header").append(str_msg);
					}
				}
				else
				{
					jQuery.ajax({
						type:"POST",
						url:'process.php',
						data :'btnGetImageOfMerchantAjax=true',
						success:function(msg)
						{
							var obj = jQuery.parseJSON(msg);
							if (obj.status=="true") 
							{
								jQuery('#div_image').html(obj.html);
							}
						}
					});
				}
            },
  
            ChunkUploaded: function(up, file, info) {
                // Called when file chunk has finished uploading
                //log('[ChunkUploaded] File:', file, "Info:", info);
            },
 
            UploadComplete: function(up, files) {
                // Called when all files are either uploaded or failed
                up.splice();
                jQuery("#uploader_start").addClass("ui-state-disabled");
                //log('[UploadComplete]');
            },
 
            Destroy: function(up) {
                // Called when uploader is destroyed
                //log('[Destroy] ');
            },
  
            Error: function(up, args) {
                // Called when error occurs
                //log('[Error] ', args);
            }
        }
		
	});
				
	jQuery(".plupload_message_close").live("click",function(){
		jQuery(".plupload_message").remove();
	});
	
});	

function log() {
        var str = "";
 
        plupload.each(arguments, function(arg) {
            var row = "";
 
            if (typeof(arg) != "string") {
                plupload.each(arg, function(value, key) {
                    // Convert items in File objects to human readable form
                    if (arg instanceof plupload.File) {
                        // Convert status to human readable
                        switch (value) {
                            case plupload.QUEUED:
                                value = 'QUEUED';
                                break;
 
                            case plupload.UPLOADING:
                                value = 'UPLOADING';
                                break;
 
                            case plupload.FAILED:
                                value = 'FAILED';
                                break;
 
                            case plupload.DONE:
                                value = 'DONE';
                                break;
                        }
                    }
 
                    if (typeof(value) != "function") {
                        row += (row ? ', ' : '') + key + '=' + value;
                    }
                });
 
                str += row + " ";
            } else {
                str += arg + " ";
            }
        });
 
        var log = $('#log');
        log.append(str + "\n");
        log.scrollTop(log[0].scrollHeight);
    }	
					</script>
<?php
$_SESSION['is_video_added']=0;
?>
