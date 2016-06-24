<?php
/**
 * @uses generate qr code, marketing coupon
 * @used in pages :demopdf/demo.php
 */
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");

//$objDB = new DB();
//$objJSON = new JSON();

//$RS = $objDB->Conn->Execute($Sql);
$array = array();

if(isset($_REQUEST['is_loyaltycard']))
{
	//echo "1";
	
	$array = array();
	$array['id'] = $_REQUEST['id'];
	$RS_card = $objDB->Show("merchant_loyalty_card",$array);
	$card_title = $RS_card->fields['title'];
	$activation_code = $RS_card->fields['activationcode'];
	
	//echo "2";
	
	//$str ="";
	//$str = "Limit <b>one per customer</b>," ;
	//$str.="Not valid with other offers.Not for re-sale,Non-refundable,Void where prohibited.No cash value.Offer subject to change without notice.";
	
	$array = array();
	$array['id'] = $_REQUEST['mer_id'];
	$RSMerchant = $objDB->Show("merchant_user",$array);
	
	//echo "3";
	
	$array_qrcode = array();
	$array_qrcode['id'] = $_REQUEST['id'];
	
	$sql_qrcode = "Select qrcode from qrcodes q, merchant_loyalty_card mlc where q.id=mlc.qrcode_id and mlc.id=".$_REQUEST['id'];
	$rs_qrcode = $objDB->Conn->Execute($sql_qrcode);
	$url = WEB_PATH."/qr.php?qrcode=".base64_encode($rs_qrcode->fields['qrcode']);

	//echo "4";
	
	//$qrcode_img_src= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d=".$url."&size=70";
	$merchant_icon = $RSMerchant->fields['merchant_icon'];
	
	if($merchant_icon!="")
	{
		$img_src=ASSETS_IMG."/m/icon/".$merchant_icon;
	}
	else 
	{
		$img_src=ASSETS_IMG."/m/default_merchant_icon.jpg";
	}
	
	//echo "5";
	/** Generate Loyalty Card Coupon **/
	?>
		<head>
		<style>
		.main_wrapper{width:395px;text-align:justify; height:249px;}
        body,p{margin:0px;padding:0px;font-family:Arial,Helvetica,sans-serif;font-size:12px; color:#333;}
        .front{margin-bottom:20px;background-color:#fff;margin-left:200px;margin-top:200px;border:1px solid #333;}
        @font-face {font-family: "HandPrinting"; src: url("font/handprinting.eo");src: url("font/handprinting.eot?#iefix") format("embedded-opentype"), url("font/handprinting.woff") format("woff"), url("font/handprinting.ttf") format("truetype"),url("font/handprinting.svg#handprinting") format("svg"); font-weight: normal;    font-style: normal;  }
        .coupon_description {height:85px; padding:20px 20px 0 20px;}
		.coupon_blk_margin_top {width:320px; border:1px dashed #333;border-width:1px 1px 0px;padding:7px;margin:10px 10px 0px 10px;}
		.coupon_title {font-size:11px;font-style:italic;display:block; margin-top: -5px;}
		.coupon_discount {line-height:1.2;text-transform:capitalize;padding:2px 0 0;height:24px;width:254px;font-weight:bold;}
		.deal_value {display:block;margin-top:10px;}
		.merchant_top_blk_left {padding:0 3px 0 0}
		.merchant_top_blk_left img {width:60px;height:60px;}
		.merchant_top_blk_right {width:280px;  vertical-align: top;}
		.coupon_blk_margin_bottom{width:320px;height:100px;border:1px dashed #333;border-width:0px 1px 1px;padding:7px;margin-left:30px;margin-right:29px;border-top:0.8px solid #333;}
		.qr_code_blk img {width:100px;height:100px;}
		.coupon_description_blk {width: 280px; vertical-align: top;line-height: 1.2;}
		</style>
		</head>
        <body>
			<div class="main_wrapper front" >
				<div class="coupon_description">
					<div class="coupon_blk_margin_top">   
						<table>
							<tr>
								<td class="merchant_top_blk_left">
									<img src="<?php echo $img_src ?>"/>
								</td>
								<td class="merchant_top_blk_right">
									<div class="coupon_title">
									<?php 
									if(strlen($RSMerchant->fields['business'])>45)
										echo substr($RSMerchant->fields['business'],0,45)."..."; 
									else
										echo $RSMerchant->fields['business']; 
									?>
									</div>
									<div class="coupon_discount" ><?php echo $card_title; ?></div>
									<div class="deal_value" ><em>Activation Code:</em><?php echo $activation_code; ?></div>
								</td>
							</tr>
						</table> 			   
					</div>
				</div>
				
                <div class="coupon_blk_margin_bottom">   
						<table>
							<tbody><tr>
								<td class="qr_code_blk">
									<?php
								$sql_qrcode = "Select qrcode from qrcodes q, merchant_loyalty_card mlc where q.id=mlc.qrcode_id and mlc.id=".$_REQUEST['id'];
								$rs_qrcode = $objDB->Conn->Execute($sql_qrcode);

								/*
								$url = WEB_PATH."/qr.php?qrcode=".base64_encode($rs_qrcode->fields['qrcode']);
								$filename= LIBRARY."/demopdf/coupon-qrcode.jpg";
								$fname= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=70'";
								passthru("/usr/bin/convert ".$fname."  ".$filename);
								*/
								$filename1 = $dirname ='';

								if(isset($_SESSION['merchant_id']) || isset($_REQUEST['mer_id']))
								{
									$merchant_id = $_REQUEST['mer_id'];
									$dirname = $_REQUEST['mer_id']."_upload";    
									$filename1 = (LIBRARY."/demopdf/".$dirname."/");
								}

								if (file_exists($filename1)) 
								{        
								} 
								else 
								{
									mkdir(LIBRARY."/demopdf/" . "$dirname", 0777);        
								}
								$image_folder = LIBRARY."/demopdf/".$dirname."/"; 

								passthru("/usr/bin/convert ".$filename." -resize ".$newWidth."x".$newHeight."! ".$image_folder."resized_image.jpg"); 

								// create jpgg image //  
								$filename_eps_path = $image_folder."template-qrcode-eps.eps";
								$filename= $image_folder."template-qrcode.jpg";
								$qrcode_size = "150";
								$fname= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url."&size=".$qrcode_size."'";
								passthru("/usr/bin/convert ".$fname." ".$filename_eps_path );
								passthru("/usr/bin/convert ".$filename_eps_path." ".$filename );
								// create jpg image //

								/******* ********/
								?>
									<img src="<?php echo WEB_PATH."/libraries/demopdf/".$dirname."/template-qrcode.jpg";?>"  />
								</td>
								<td class="coupon_description_blk">
									<p>Scan QR code to activate your</p>
									<p>loyalty card or enter activation</p>
									<p>code on <a href="https://test.scanflip.com">www.scanflip.com</a> to</p>
									<p>activate your card.</p>
									<div style="color: rgb(212, 106, 8);margin-left:105px;margin-top:20px;font-style: italic;">Powered by Scanflip</div>
								</td>
							</tr>
						</tbody></table> 			   
					</div>
					
				

			</div>
		
		</body>
<?php	
}
else
{	
	//$objJSON = new JSON();
	$JSON = $objJSON->get_compain_details($_REQUEST['id']);
	$RS1 = json_decode($JSON);
	//echo md5("123456");
	$array = array();
	$array['campaign_id'] = $_REQUEST['id'];
	$RSCode = $objDB->Show("activation_codes",$array);

	$str ="";
	if($RS1[0]->number_of_use==1)
	{ 
		$str = "Limit <b>one per customer</b>," ;
	}
	elseif($RS1[0]->number_of_use==2)
	{
	   $str = "Limit <b>one per customer per day</b>, ";
	} 
	if($RS1[0]->new_customer==3)
	{ 
		$str .= " <b>Valid for new customer only</b>, " ;
	}



	$str.="Not valid with other offers.Not for re-sale,Non-refundable,Void where prohibited.No cash value.Offer subject to change without notice.";
	$array = array();
	$Rownew="";
	$array['id'] = $_REQUEST['mer_id'];
	$RSMerchant = $objDB->Show("merchant_user",$array);
	$array_qrcode = array();
	$array_qrcode['id'] = $_REQUEST['id'];
	$sql_qrcode = "Select qrcode from qrcodes q, qrcode_campaign qc where q.id=qc.qrcode_id and qc.campaign_id=".$_REQUEST['id'];
	$rs_qrcode = $objDB->Conn->Execute($sql_qrcode);
	$url = WEB_PATH."/qr.php?qrcode=".base64_encode($rs_qrcode->fields['qrcode']);
	//$qrcode_img_src= WEB_PATH."/".$rs_qrcode->fields['qrcode_image'];
	$qrcode_img_src= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d=".$url."&size=70";
	$merchant_icon = $RSMerchant->fields['merchant_icon'];
					
	//                            if($Rownew->picture!="")
	//                            {
	//                                $img_src=ASSETS_IMG ."/m/location/".$Rownew->picture;
	//                            }
	//                            else 
									if($merchant_icon!="")
								{
									$img_src=ASSETS_IMG."/m/icon/".$merchant_icon;
								}
								else 
								{
									$img_src=ASSETS_IMG."/m/default_merchant_icon.jpg";
								}
	?>
			<style>.main_wrapper{width:395px;text-align:justify; height:249px;}
				body,p{margin:0px;padding:0px;font-family:Arial,Helvetica,sans-serif}
				.front{margin-bottom:20px;background-color:#fff;border:1px solid #000;margin-left:100px;margin-top:200px;}
				@font-face {   font-family: "HandPrinting"; src: url("font/handprinting.eo");
							   src: url("font/handprinting.eot?#iefix") format("embedded-opentype"), url("font/handprinting.woff") format("woff"), url("font/handprinting.ttf") format("truetype"),url("font/handprinting.svg#handprinting") format("svg"); font-weight: normal;    font-style: normal;  }
			</style></head>
			<body>
				<div style="width:600px;padding:0px;margin:0px auto;">
					<div class="main_wrapper front">
						<div class="coupon_description" style="background: #fff;height:90px; padding:20px 20px 0 20px;">
												<div style="width:320px; border:1px dashed #231f20;border-width:1px 1px 0px;padding:7px;margin:10px 10px 0px 10px;">   <table>
		<tr>
		<td style="width:60px;height:60px;padding:0 3px 0 0">
		 <img src="<?php echo $img_src ?>" width="60px" height="60px"/>
					   </td>
		<td style="width:280px;  vertical-align: top;">
		 <div class="coupon_title" style="font-size:14px;color:#000000;font-style:italic;display:block; margin-top: -5px;">
		 <?php 
			if(strlen($RSMerchant->fields['business'])>35)
				echo substr($RSMerchant->fields['business'],0,35)."..."; 
			else
				echo $RSMerchant->fields['business']; 
		 ?>
		 </div>
		 <div class="coupon_discount" style="color:#231F20;font-size:12px;line-height:15px;text-transform:capitalize;padding:2px 0 0;height:30px;width:254px"><?php echo $RS1[0]->title; ?></div>
			
		 <div class="deal_value" style="font-size:14px;padding:0px;color:#231F20;display:block;"><em>Activation Code:</em><?php echo $RSCode->fields['activation_code']; ?></div>          
		
		</td>
		
		</tr>
		</table> 
						   
						</div>
						</div>
						<div class="offer_coupon" style="height:119px;overflow:hidden;background:#231f20; padding: 0 20px 20px 20px;">
							<div style="height:105px;width:328px;color:#fff;overflow:hidden;margin:0px 10px 10px 10px;padding:3px;border:1px dashed #fff;border-width:0px 1px 1px;">
								  <div class="desc" style="font-size:10px;padding:0px 5px;line-height:13px;position:relative;"> 
								   <ul style="padding:0px;margin:0px;list-style:none;">
								   <li style="padding:0px 0 0;">Expiry Date :<?php echo date("F d,Y h:i:s A", strtotime($RS1[0]->expiration_date)); ?></li>
								   <li style="padding:0px 0 0;"><?php echo $str; ?></li>
									<li style="padding:0px 0 0">Where to Redeem : Scan QR Code (See Reverse) to find nearest participating location for this offer.</li>
									<li style="padding:2px 0 0px;line-height:16px;font-size:11px;text-align:left;">How to activate this offer(See Reverse for instruction)</li>
									<li style="padding:2px 0 2px;line-height:06px;font-size:10px;text-align:right;">
									<div style="color:#D46A08;float:right;font-style:italic;font-size:12px;right:0px;bottom:0px; line-height:5px;">Powered by Scanflip</div></li></ul>       
									
								</div>
							</div>
						 </div>   
				  </div>
					
					<div style="height:655px">&nbsp;</div>
					<div style="height:11px;" > </div>
					<div style="height:10px;"></div>
			 
				  <div class="main_wrapper back" style="border: 1px solid #000;">
					  
					  
					  <div style=" height: auto;">
				   <div style="height:85px;margin: 30px 30px 0 30px;border-bottom:0px dashed #231f20;border-left: 1px dashed #231f20;border-top: 1px dashed #231f20;border-right: 1px dashed #231f20;"> 
					<div style="float:left;">   <table>
		<tr>
		<td style="width:60px;height:50px;padding:0 5px 0 0; vertical-align: top;" >
			<?php
			$sql_qrcode = "Select qrcode from qrcodes q, qrcode_campaign qc where q.id=qc.qrcode_id and qc.campaign_id=".$_REQUEST['id'];
	$rs_qrcode = $objDB->Conn->Execute($sql_qrcode);

	$url = WEB_PATH."/qr.php?qrcode=".base64_encode($rs_qrcode->fields['qrcode']);
	$filename= LIBRARY."/demopdf/coupon-qrcode.jpg";
	$fname= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=70'";
	passthru("/usr/bin/convert ".$fname."  ".$filename);
	//$qrcode_img_src= WEB_PATH."/".$rs_qrcode->fields['qrcode_image'];
	/******* *******/
		$filename1 = $dirname ='';

		if(isset($_SESSION['merchant_id']) || isset($_REQUEST['mer_id'])){
			  $merchant_id = $_REQUEST['mer_id'];
		
		
			  $dirname = $_REQUEST['mer_id']."_upload";    
			  $filename1 = (LIBRARY."/demopdf/".$dirname."/");
		}
	  
		if (file_exists($filename1)) {        
		} else {
			mkdir(LIBRARY."/demopdf/" . "$dirname", 0777);        
		}
	$image_folder = LIBRARY."/demopdf/".$dirname."/"; 
		   
	  passthru("/usr/bin/convert ".$filename." -resize ".$newWidth."x".$newHeight."! ".$image_folder."resized_image.jpg"); 
	  
	// create jpgg image //  
	$filename_eps_path = $image_folder."template-qrcode-eps.eps";
	 $filename= $image_folder."template-qrcode.jpg";
	 $qrcode_size = 70;
	$fname= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url."&size=".$qrcode_size."'";
	passthru("/usr/bin/convert ".$fname."  ".$filename_eps_path );
	passthru("/usr/bin/convert ".$filename_eps_path."  ".$filename );
	// create jpg image //

	/******* ********/
		   
			?>
		 <img src="<?php   echo  WEB_PATH."/libraries/demopdf/".$dirname."/template-qrcode.jpg";?>"  />
		   </td>
		<td style="width:280px; vertical-align: top;"> 
			<span style="font-size:16px;font-weight:normal;padding:0px 0 0;line-height:16px;display:block;color:#914456;font-style:italic;">To receive your offer :</span>
							
								<ul style="list-style-type:none;margin:0px;padding:0px;display:block; vertical-align: top;">
								  <li style="overflow:hidden;font-size:10px;line-height:12px;"><b>-</b> Download Scanflip App or Go to www.scanflip.com</li>
								  <li style="overflow:hidden;font-size:10px;line-height:12px;"><b>-</b> Enter your activation code(reverse side)</li>
								  <li style="overflow:hidden;font-size:10px;line-height:12px;"><b>-</b> Reserve to activate your offer </li>
								  <li style="overflow:hidden;font-size:10px;line-height:12px;"><b>-</b> Share campaign with all your friends and family and earn referral reward points! </li>
								</ul>         
							</td>
		</tr>
		</table>                                              
					</div>
		   </div>
				   <div style="background: #231f20;color: #fff;height:133px;">
				  <div style="height:107px; margin: 0px 30px 30px 30px;padding:0px 7px;border-bottom: 1px dashed #fff;border-right: 1px dashed #fff;border-left: 1px dashed #fff;border-top:0px dashed #fff;">
					<p style="font-size:11px;padding:3px 0px 1px;line-height:12px;text-align:left;font-weight:normal;color:#fff;font-style:italic;">Scan QR code to view your offer.</p>
					<p style="font-size:11px;padding:0px;line-height:12px;">To redeem your offer,please present printable or mobile app voucher at the merchant location.To reserve offer , you must have Scanflip account.</p>
					<p style="font-size:11px;padding:0px;line-height:12px;margin-bottom:1px;">To register an account, you must be over the age of 13.Additional terms and condition apply. Please visit www.scanflip.com/terms</p>
					<div style="width:190px;padding:0px; height:40px;" class="scanflip_logo_image">
						<img src="<?php echo ASSETS_IMG; ?>/m/logo-dlll.png" style="width:150px;"/></div>
				  </div>
			</div></div></div></div></body></html>			
<?php			
}
?>
