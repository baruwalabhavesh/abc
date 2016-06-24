<?php
/**
 * @uses merchant setup
 * @used in pages : process.php,register.php,footer.php,header.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
// 369

//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where['id'] = $_SESSION['merchant_id'];

$RS = $objDB->Show("merchant_user", $array_where);

/*$sub_merchant_id="select merchant_parent from merchant_user where id = '".$_SESSION['merchant_id']."'";
$result_sub_merchant_id=mysql_query($sub_merchant_id);*/
$result_sub_merchant_id=$objDB->Conn->Execute("select merchant_parent from merchant_user where id =?",array($_SESSION['merchant_id']));


//$data_sub_merchant_id = mysql_fetch_assoc($result_sub_merchant_id);
$data_sub_merchant_id = $result_sub_merchant_id->FetchRow();
//$data_sub_merchant_id = $RS->fields['merchant_parent'];

//$Sql = "select firstname from merchant_user where id=7";
//$Rs = $objDB->Conn->Execute($Sql);
//echo "<pre>";
//
//print_r($Rs);
//echo "</pre>";
//exit;

$tab="";
if(isset($_REQUEST['tab']))
{
	$tab=$_REQUEST['tab'];
}

$timezoness =array(
    'Pacific/Wake' => '(GMT-12:00) International Date Line West',
    'Pacific/Apia' => '(GMT-11:00) Samoa',
    'US/Hawaii' => '(GMT-10:00) Hawaii',
    'America/Anchorage' => '(GMT-09:00) Alaska',
    'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada); Tijuana',
    'America/Phoenix' => '(GMT-07:00) Arizona',
    'America/Chihuahua' => '(GMT-07:00) Mazatlan',
    'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)',
    'America/Managua' => '(GMT-06:00) Central America',
    'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)',
    'America/Mexico_City' => '(GMT-06:00) Monterrey',
    'America/Regina' => '(GMT-06:00) Saskatchewan',
    'America/Bogota' => '(GMT-05:00) Quito',
    'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)',
    'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
    'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)',
    'America/Caracas' => '(GMT-04:00) La Paz',
    'America/Santiago' => '(GMT-04:00) Santiago',
    'America/St_Johns' => '(GMT-03:30) Newfoundland',
    'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
    'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
    'America/Godthab' => '(GMT-03:00) Greenland',
    'America/Noronha' => '(GMT-02:00) Mid-Atlantic',
    'Atlantic/Azores' => '(GMT-01:00) Azores',
    'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
    'Africa/Casablanca' => '(GMT) Monrovia',
    'Europe/London' => '(GMT) London',
    'Europe/Berlin' => '(GMT+01:00) Vienna',
    'Europe/Belgrade' => '(GMT+01:00) Prague',
    'Europe/Paris' => '(GMT+01:00) Paris',
    'Europe/Sarajevo' => '(GMT+01:00) Zagreb',
    'Africa/Lagos' => '(GMT+01:00) West Central Africa',
    'Europe/Istanbul' => '(GMT+02:00) Minsk',
    'Europe/Bucharest' => '(GMT+02:00) Bucharest',
    'Africa/Cairo' => '(GMT+02:00) Cairo',
    'Africa/Johannesburg' => '(GMT+02:00) Pretoria',
    'Europe/Helsinki' => '(GMT+02:00) Vilnius',
    'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
    'Asia/Baghdad' => '(GMT+03:00) Baghdad',
    'Asia/Riyadh' => '(GMT+03:00) Riyadh',
    'Europe/Moscow' => '(GMT+03:00) Volgograd',
    'Africa/Nairobi' => '(GMT+03:00) Nairobi',
    'Asia/Tehran' => '(GMT+03:30) Tehran',
    'Asia/Muscat' => '(GMT+04:00) Muscat',
    'Asia/Tbilisi' => '(GMT+04:00) Yerevan',
    'Asia/Kabul' => '(GMT+04:30) Kabul',
    'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
    'Asia/Karachi' => '(GMT+05:00) Tashkent',
    'Asia/Calcutta' => '(GMT+05:30) New Delhi',
    'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
    'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
    'Asia/Dhaka' => '(GMT+06:00) Dhaka',
    'Asia/Colombo' => '(GMT+06:00) Sri Jayawardenepura',
    'Asia/Rangoon' => '(GMT+06:30) Rangoon',
    'Asia/Bangkok' => '(GMT+07:00) Jakarta',
    'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
    'Asia/Hong_Kong' => '(GMT+08:00) Urumqi',
    'Asia/Irkutsk' => '(GMT+08:00) Ulaan Bataar',
    'Asia/Singapore' => '(GMT+08:00) Singapore',
    'Australia/Perth' => '(GMT+08:00) Perth',
    'Asia/Taipei' => '(GMT+08:00) Taipei',
    'Asia/Tokyo' => '(GMT+09:00) Tokyo',
    'Asia/Seoul' => '(GMT+09:00) Seoul',
    'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
    'Australia/Adelaide' => '(GMT+09:30) Adelaide',
    'Australia/Darwin' => '(GMT+09:30) Darwin',
    'Australia/Brisbane' => '(GMT+10:00) Brisbane',
    'Australia/Sydney' => '(GMT+10:00) Sydney',
    'Pacific/Guam' => '(GMT+10:00) Port Moresby',
    'Australia/Hobart' => '(GMT+10:00) Hobart',
    'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
    'Asia/Magadan' => '(GMT+11:00) Solomon Is.',
    'Pacific/Auckland' => '(GMT+12:00) Wellington',
    'Pacific/Fiji' => '(GMT+12:00) Marshall Is.',
    'Pacific/Tongatapu' => '(GMT+13:00) Nuku\'alofa',
);
require LIBRARY.'/fb-sdk/src/facebook.php';

//include_once(LIBRARY."/fb-sdk/src/facebook_secret.php");


$facebook = new Facebook(array(
  'appId'  => facebookappId,
  'secret' => facebooksecret,
));
$user = $facebook->getUser();
if ($user) 
{

						
					
/*
$page_data = $facebook->api('/LanetteamSolution');
echo "<pre>";
print_r($page_data);
echo "</pre>";
*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ScanFlip | Merchant Setup</title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/fancybox/jquery.fancybox.css" media="screen" />

</head>
<style>
                form input[type=url] 
                {
                        width: 300px
                }
                #output img
                {
                        border: 1px solid #ccc;
                        padding: 10px;
                        margin: 10px;
                        display: inline-block;
                }


    h3 {
    color: black !important;
	font-size:14px !important;
    font-weight: lighter;
	background-color:hsl(0, 0%, 93%) !important;
	border:none !important;
}
#tooltip{
	z-index:9999 !important;
	opacity:1.0 !important;
}
#result_new
{
    margin-left: 5px;
    font-weight: bold;
	width:150px;
	float:left;
}
#result_con_new
{
    margin-left: 5px;
    font-weight: bold;
	width:150px;
	float:left;
}
.short
{
    color:#FF0000;
    font-weight: bold;
}
.weak
{
    color:#E66C2C;
    font-weight: bold;
}
.good
{
    color:#2D98F3;
    font-weight: bold;
}
.strong
{
    color:#006400;
    font-weight: bold;
}
.fancybox-close{top:10px !important;right:10px !important;}
        </style>
<body>
<div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div style="width:100%;text-align:center;">
	

<!--<script src="<?=WEB_PATH?>/admin/js/jquery.js"></script>-->
<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery-1.7.2.min.js"></script>-->
 <script type="text/javascript" src="<?=ASSETS_JS?>/m/fancybox/jquery.fancybox-buttons.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/fancybox/jquery.fancybox.js"></script>

<script type="text/javascript" src="<?=ASSETS?>/tinymce/tiny_mce.js"></script>

<!--- tooltip css --->
<script type="text/javascript" src="<?=ASSETS_JS?>/m/bootstrap.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/bootstrap.min.js"></script>
<!--- tooltip css --->

<script type="text/javascript" src="<?=ASSETS_JS?>/m/old_pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/new_pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/con_new_pass_strength.js"></script>

<script type="text/javascript" >
tinyMCE.init({
		// General options
		//mode : "textareas",
		mode : "exact",
		elements:"aboutus,aboutus_short",
		theme : "advanced",
		//plugins : "lists,searchreplace",
		valid_elements :'p,br',
		// Theme options
		//theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		
		//theme_advanced_buttons1 : "replace,|,bullist,numlist,|,outdent,indent,blockquote,|,sub,sup,|,charmap",
		theme_advanced_buttons1 : "",
		
		//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
               
		// Example content CSS (should be your site CSS)
		content_css : "<?=ASSETS?>/tinymce/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "<?=ASSETS?>/tinymce/lists/template_list.js",
		external_link_list_url : "<?=ASSETS?>/tinymce/lists/link_list.js",
		external_image_list_url : "<?=ASSETS?>/tinymce/lists/image_list.js",
		media_external_list_url : "<?=ASSETS?>/tinymce/lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		},
                charLimit:155,
		setup : function(ed) {
        //peform this action every time a key is pressed
        ed.onKeyDown.add(function(ed, e) {
		if(tinyMCE.activeEditor.id=="aboutus_short")
                {
			var textarea = tinyMCE.activeEditor.getContent(); 
			//alert(textarea);
			var lastcontent=textarea;
			//define local variables
			var tinymax, tinylen, htmlcount;
			//manually setting our max character limit
			tinymax = ed.settings.charLimit;
			//grabbing the length of the curent editors content
			tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
			//setting up the text string that will display in the path area
			//htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
			//if the user has exceeded the max turn the path bar red.
			//alert(tinylen);
			//alert(e.keyCode);
			if((tinylen+1>tinymax && e.keyCode == 37) || (tinylen+1>tinymax && e.keyCode == 38) || (tinylen+1>tinymax && e.keyCode == 39) || (tinylen+1>tinymax && e.keyCode == 40))
			 {
				return true;
			 }
			if (tinylen+1>tinymax && e.keyCode != 8)
			{
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
			
		}		
        });
		ed.onKeyUp.add(function(ed, e) {
		
			//alert("up");
			var textarea = tinyMCE.activeEditor.getContent(); 
			//alert(textarea);
			var lastcontent=textarea;
			//define local variables
			var tinymax, tinylen, htmlcount;
			//manually setting our max character limit
			tinymax = ed.settings.charLimit;
			//grabbing the length of the curent editors content
			tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
			//setting up the text string that will display in the path area
			//htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
			
			var l=tinymax-tinylen;
                        
                        if(tinyMCE.activeEditor.id=="aboutus_short")
                            document.getElementById("abt_us_remaining").innerHTML=l+" characters remaining";
                       
		});
		}
	});
</script>
<?php
//if($data_sub_merchant_id['merchant_parent'] == "0")
//{ 
?>
	<script type="text/javascript" language="javascript" src="<?=ASSETS_JS?>/m/jquery.carouFredSel-6.2.1-packed.js"></script>
	 <script type="text/javascript" src="<?=ASSETS_JS?>/m/jquery.timepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/jquery.timepicker.css" />
    <script language="javascript" src="<?=ASSETS_JS?>/m/ajaxupload.3.5.js" ></script>
<?php 
//} 
?>
<!---start header---->
	<div>
		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	
    <div style="margin-left:auto;margin-right:auto;" id="fadeshow11">

	<!--end of slide--></div>
	<div id="content">
	
		<form id="profileform" name="profileform" action="process.php" method="post" enctype="multipart/form-data">
		
		<input type="hidden" name="hdn_web_path" id="hdn_web_path" value="<?php echo WEB_PATH ?>/merchant/process.php" />
 
		 <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?=$RS->fields['merchant_icon']?>" />
		 <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
		 
		 <input type="hidden" name="hdn_image_path_l" id="hdn_image_path_l" value="" />
         <input type="hidden" name="hdn_image_id_l" id="hdn_image_id_l" value="" />
		
		<input type="hidden" name="hdnlc1" id="hdnlc1" value="" />
		<input type="hidden" name="hdnlcat1" id="hdnlcat1" value="" />
		
		<input type="hidden" name="hdnlc2" id="hdnlc2" value="" />
		<input type="hidden" name="hdnlcat2" id="hdnlcat2" value="" />
		
		<input type="hidden" name="hdnlc3" id="hdnlc3" value="" />
		<input type="hidden" name="hdnlcat3" id="hdnlcat3" value="" />
		
		<input type="hidden" name="hdnProcessMerchant" id="hdnProcessMerchant" value="btnAddLocationimage" />
		
				
		<div id="nax_wizard_dialog" class="mvl ptm uiInterstitial uiInterstitialLarge uiBoxWhite">
	
			<div class="uiHeader uiHeaderBottomBorder mhl mts uiHeaderPage interstitialHeader">
				<div class="clearfix uiHeaderTop">
					<div class="accountsetupdiv">
						<h2 aria-hidden="true" class="uiHeaderTitle">
							<?php echo $merchant_msg["profile"]["Field_setup"];?>
						</h2>
					</div>
					<div class="cancelsetupdiv">
						<input type="button" style="cursor:pointer;" class="btnCancelsetup" name="btnCancelsetup" value="Cancel Account Setup" id="btnCancelsetup" >
					</div>
				</div>
			</div>
			
			<div class="phl ptm uiInterstitialContent">
	
				<div class="uiStepList uiStepListSingleLine uiStepListSingleLineWhite">
					<ol>
						<li id="change_password" class="uiStep <?php if($tab=='' || $tab=='change_password') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">1
										</span> Set Account ID
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="business_page" class="uiStep <?php if($tab=='business_page') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">2
										</span>Facebook Business Page
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="update_profile" class="uiStep <?php if($tab=='update_profile') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">3
										</span> Update Profile
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="about_us" class="uiStep <?php if($tab=='about_us') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">4
										</span> About Us
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="business_logo" class="uiStep <?php if($tab=='business_logo') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">5
										</span> Business Logo
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="add_location" class="uiStep <?php if($tab=='add_location') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">6
										</span> Add Location
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="location_hour" class="uiStep">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">7
										</span> Location Hours
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="location_category" class="uiStep uiStepLast">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">8
										</span> Location Category
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<!--
						<li id="location_image" class="uiStep">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">9
										</span> Location Profile Image
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						-->
						<!--
						<li id="location_additional_image" class="uiStep uiStepLast">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">10
										</span> Location Additional Images
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						-->
					</ol>
				</div>
				
				<div class="mtm ptm uiBoxWhite topborder update_merchant_process" style="display: block;">
						
					<div class="complete_change_password" style="display:<?php if($tab=='' || $tab=='change_password'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
							<!--
							<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>
								Change Password
							</div>
							-->
							
								 
								<table style="padding: 10px;width: 100%;">
									<tr>
										<td>
											<?php echo "Email ID : " ;?>
										</td>
										<td>
											<input type="text" style="width: 150px;float:left;" id="emailid" name="emailid" value="<?=$RS->fields['email']?>">
										</td>
									</tr> 
									<tr>
										<td>
											<?php echo $merchant_msg["setup"]["new_password"];?>
										</td>
										<td>
											<input type="password" style="width: 150px;float:left;" id="new_password" name="new_password">
											 <span id="result_new"></span>
										</td>
									</tr>									
									<tr>
										<td>
											<?php echo $merchant_msg["setup"]["con_new_password"];?>
										</td>
										<td>
											<input type="password" style="width: 150px;float:left;" id="con_new_password" name="con_new_password">
											<span id="result_con_new"></span>
										</td>
									</tr>
									<tr>
										<td align="right"> </td>
										<td align="left">
											<div style="float:left;"><input type="checkbox" name="agree" id="agree" style="margin:0px;" /></div>
											<div style="float:left;width:218px;margin-left:5px;">I agree to the Scanflip <a href="<?=WEB_PATH?>/merchant/terms.php" target="_blank">Terms of Service</a> and <a href="<?=WEB_PATH?>/merchant/privacy-assist.php" target="_blank">Privacy Policy.</a></div>                
										</td>
									</tr>									
								</table>
								<table style="width: 20%; float: right;">
									<tr>
										
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnUpdatePassword_f" value="Next" id="btnUpdatePassword_f" class="disabled" disabled >
										</td>
									</tr>
								</table>
								
						</div>
						
					</div>
					
					<div class="complete_business_page" style="display: <?php if($tab=='business_page'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
								 
								<table style="padding: 10px;width: 100%;">									
									<tr>
										<td width="25%">
											<?php echo "Enter Facebook Page Web Address : " ?>
										</td>
										<td>
											<input type="text" style="width: 300px;float:left;margin-top:16px;" id="businesspageurl" name="businesspageurl">
												<img src="<?=ASSETS_IMG?>/m/001.png" class="loctimezonedivimg" alt="" />
											  <div class="loctimezonediv">
												<?php echo $merchant_msg["addlocation"]["businesspage_tooltip"];?>
											  </div>
										</td>
									</tr> 		
								</table>
								<table style="width: 20%; float: right;">
									<tr>
										
										<td >
											<input type="button" style="cursor:pointer;" name="btnBacktochangepassword" value="Back" id="btnBacktochangepassword" >
										</td>
										<td >
											<input type="button" style="cursor:pointer;" class="btnSkip" name="btnSkip" value="Skip" id="btnSkip" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btncallbusinesspage" value="Next" id="btncallbusinesspage" >
										</td>
									</tr>
								</table>
								
						</div>
						
					</div>

					<div class="complete_update_profile" style="display: <?php if($tab=='update_profile'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
							<!--
							<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>
								Update Profile Detail
							</div>
							-->
								 
								<table style="padding: 10px;width: 100%;">
									<tr>
										<td width="40%"><?php echo $merchant_msg['profile']['Field_first_name']; ?></td>
										<td width="60%">
										<input type="text" name="firstname" id="firstname" style="width:200px; " value="<?=$RS->fields['firstname']?>">
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_last_name']; ?></td>
										<td><input type="text" name="lastname" id="lastname" style="width:200px; " value="<?=$RS->fields['lastname']?>"></td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_address']; ?></td>
										<td><input type="text" name="address" id="address" style="width:200px; " value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['address']; }else{ echo $row_data_result['address'];}?>"></td>
									  </tr>
									  
									  
									   <tr>
										<td><?php echo $merchant_msg['profile']['Field_country']; ?></td>
										<td>
										<!--<input type="text" name="country" id="country" style="width:200px; " value="<?=$RS->fields['country']?>">-->
										<?php
											//echo $RS->fields['country']."sharad";
											//echo $row_data_result['country'];
										 ?>
										<select name="country" id="country">
											<option value="0" >Please Select</option>
											<?php
											$array_where = array();
											$array_where['active'] = 1;
											$RS_country = $objDB->Show("country",$array_where," Order By `name` ASC ");
											if($RS_country>0)
											{
												while($Row = $RS_country->FetchRow())
												{
												?>
												<option value="<?php echo $Row['id'] ?>" ><?php echo $Row['name'] ?></option>
												<?php
												}
											}
											?>
										</select>
										
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_state']; ?></td>
										<td>
										<!--<input type="text" name="state" id="state" style="width:200px; " value="<?=$RS->fields['state']?>">-->
										
											<select name="state" id="state" class="" style="display:block">
												<option value='0'>Please Select</option>
											
										</select>
										
										
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_city']; ?></td>
										<td>
											<select name="city" id="city">
												<option value='0'>Please Select</option>
											
												</select>
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_zipcode']; ?></td>
										<td><input type="text" name="zipcode" id="zipcode" style="width:200px; " value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['zipcode']; }else{ echo $row_data_result['zipcode'];}?>"></td>
									  </tr>
									 
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_phone_no']; ?></td>
										<?php 
										$mobileno=$RS->fields['phone_number'];
											$area_code=substr($mobileno,4,3);
											 $mobileno2=substr($mobileno,8,3);
									 $mobileno1=substr($mobileno,12,4);  
										   //$mobileno1=substr($mobileno,8,4);
										   ?>
										<td><!--<input type="text" name="phone_number" id="phone_number" style="width:200px; " value="<?=$RS->fields['phone_number']?>">-->
										<select name="mobile_country_code" id="mobile_country_code" style="display:none;">
											<option value="001">001</option>
										</select>
											<input type="text" name="mobileno_area_code" id="mobileno_area_code" style="width:30px; " value="<?php echo $area_code;?>" maxlength="3">-
										<input type="text" name="mobileno2" id="mobileno2" style="width:30px; " value="<?php echo $mobileno2;?>" maxlength="3">-
										<input type="text" name="mobileno" id="mobileno" style="width:40px; " value="<?php echo $mobileno1;?>" maxlength="4">
										
										</td>
									  </tr>
									  <?php 
										if(isset($_SESSION['merchant_info']['merchant_parent']))
										{
											if($_SESSION['merchant_info']['merchant_parent'] == 0 )
											{
										?>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_business_name']; ?></td>
										<td><input type="text" name="business" id="business" style="width:200px; " value="<?=$RS->fields['business']?>"></td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_business_tag']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_business_tags"]; ?>">&nbsp;&nbsp;&nbsp;</span></td></td>
										<td>
										<br/>
										<!--<input type="text" name="business_tags" id="business_tags" style="width:200px; " value="<?=$RS->fields['business_tags']?>">-->
										<textarea name="business_tags" id="business_tags" style="width:200px; " ><?php echo $RS->fields['business_tags']?></textarea>
										<br/><?php  echo  $merchant_msg["profile"]["Field_business_tag_add_upto"]; ?>
										</td>
									  </tr>
									  <?php
											}
										}
									   ?>
									   		
								</table>
								<table style="width: 20%; float: right;">
									<tr>
										
										<td >
											<input type="button" style="cursor:pointer;" name="btnBacktobusinesspage" value="Back" id="btnBacktobusinesspage" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnUpdateProfile" value="Next" id="btnUpdateProfile" >
										</td>
									</tr>
								</table>
						</div>
						
					</div>
					
					<div class="complete_about_us" style="display: <?php if($tab=='about_us'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<?php
									if(isset($_SESSION['merchant_info']['merchant_parent']))
									{
									  if($_SESSION['merchant_info']['merchant_parent'] == 0 )
									  {
									  ?>
									   <tr>
										<td><?php echo $merchant_msg['profile']['Field_about_us_short']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_aboutus_short"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>
										<td>
											
											<!--<input type="text" name="aboutus" id="aboutus" style="width:200px; " value="<?=$RS->fields['aboutus']?>" onkeyup="changetext2()" maxlength="45"/>-->
											<textarea id="aboutus_short" name="aboutus_short" rows="5" cols="25" style="width:80%;" ><?=$RS->fields['aboutus_short']?></textarea>
											<span id="abt_us_remaining" class="abt_us_remaining" style="float:left;" >Maximum 155 characters</span>
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_about_us']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_aboutus"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>
										<td>
											
											<!--<input type="text" name="aboutus" id="aboutus" style="width:200px; " value="<?=$RS->fields['aboutus']?>" onkeyup="changetext2()" maxlength="45"/>-->
											<textarea id="aboutus" name="aboutus" rows="5" cols="25" style="width:80%;" placeholder="*Add a description with basic info for <?php echo $RS->fields['business'] ?>"><?=$RS->fields['aboutus']?></textarea>
											<!--<span id="abt_us_remaining" class="abt_us_remaining" style="float:left;" >Maximum 600 characters</span>-->
										</td>
									  </tr>
									 
									  <?php
										}
									}
									  ?>
									
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td >
											<input type="button" style="cursor:pointer;" name="btnBacktoupdateprofile" value="Back" id="btnBacktoupdateprofile" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnUpdateAboutus" value="Next" id="btnUpdateAboutus" >
										</td>
									</tr>
								</table>
						</div>
					</div>
					
					<div class="complete_business_logo" style="display: <?php if($tab=='business_logo'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<?php 
								/*
										if($_SESSION['merchant_info']['merchant_parent'] == 0 )
										{
										?>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_merchant_icon']; ?></td>
										<td><div style="float: left;">
											<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
											<div id="upload" >
											<span  >Browse
											</span> 
											</div>
											</div>
										</td>
									  </tr>
									  
									  <tr><td align="right">&nbsp; </td>
											<td>

													<span id="status" ></span>
													<br/>

													<ul id="files" >

													 </ul>
											</td>
									  </tr>
									  <?php 
										}
										*/
										?>
									<tr class="_51mx">
										<td class="business_logo_left" style="width: 23%;vertical-align:top;">
											<div class="nax_profile_pic" id="nax_profile_pic">
												<img src="<?=ASSETS_IMG.'/m/uploadlogo.png'?>" alt="Merchant" id="profilePic" class="profilePic silhouette img">
											</div>
											<div style="margin-top:10px;">Add image at-least(w x h) 144px x 144px.</div>
										</td>
										<td class="business_logo_right">
											<div id="nax_upload_right">
													<div id="upload_business_logo" class="nax_image_uploader" style="display:block;">
														<div class="uploader">
															<div style="float: left;margin-left:15%;">
																<div id="upload" >
																	<span >Browse</span> 
																</div>
															</div>
														</div>
														<!--
														<ul id="nax_list" class="uiList _509- _4ki _6-h _703 _4ks">
															<li>
																<div class="uploader">
																	<div style="float: left;">
																		<div id="upload" class="new_css">
																			<span >Upload From Computer</span> 
																		</div>
																	</div>
																</div>
															</li>
															<li class="last">
																<div class="link">
																	<a id="import_from_website" href="javascript:void(0)" >
																		Import From Website
																	</a>
																</div>
															</li>
														</ul>
														-->
														<span id="status" style=" float: left;font-family: Arial;margin-top: 20px;padding: 5px;width: 50%;"></span>
													<br/>

													<ul id="files" style="display:none;">

													 </ul>
													</div>													
													
													
												
															
											</div>
										</td>
									</tr>	
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td >
											<input type="button" style="cursor:pointer;" name="btnBacktoaboutus" value="Back" id="btnBacktoaboutus" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnUpdateBusinesslogo" value="Next" id="btnUpdateBusinesslogo" >
										</td>
									</tr>
							</table>
						</div>
					</div>
					
					<div class="complete_add_location" style="display: <?php if($tab=='add_location'){echo 'block';}else{echo 'none';}?>;">
						<div style="width: 100%;text-align: left !important">								 
							<table style="padding: 10px;width: 100%;">	
								<tr style="display:none;">
									<td width="20%" align="right"><?php echo $merchant_msg["addlocation"]["Field_location_name"];?></td>
									<td width="80%" align="left">
										<input type="text" name="location_name" id="location_name" value="<?php echo $RS->fields['business']; ?>"/>
										&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chk_is_primary" id="chk_is_primary" value="0"/><?php echo $merchant_msg["addlocation"]["Field_primary_location"];?> <span class="notification_tooltip" title="<?php echo $merchant_msg["addlocation"]["Tooltip_Loction_Primary"]; ?>" >&nbsp;&nbsp;&nbsp</span>
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_address"];?></td>
									<td align="left">
										<input type="text" name="address_l" id="address_l" value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['address']; }else{ echo $row_data_result['address'];}?>">
									</td>
								</tr>
								<tr style="display:none;">
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_country"];?> </td>
									<td align="left">
										<select name="country_l" id="country_l">
											<option value="USA" <?php if($data_sub_merchant_id['merchant_parent'] == "0")
														   {
															if($RS->fields['country'] == "USA")
															{
																echo "selected";
															}
														   }
														   else
														   {
														  if($row_data_result['country'] == "USA")
														  {
															echo "selected";
														  }
														
																		   }?>>USA</option>
											<option value="Canada" <?php if($data_sub_merchant_id['merchant_parent'] == "0")
														   {
															if($RS->fields['country'] == "Canada")
															{
																echo "selected";
															}
														   }
														   else
														   {
														  if($row_data_result['country'] == "Canada")
														  {
															echo "selected";
														  }
														
														   }?> >Canada</option>
										</select>
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_state"];?></td>
									<td align="left">
										<select name="state_l" id="state_l" class="" style="display:block">
											<option value='0'>Please Select</option>											
										</select>
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_city"];?></td>
									<td align="left">
										<select name="city_l" id="city_l" class="" style="display:block">
											<option value='0'>Please Select</option>
										</select>
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_zipcode"];?></td>
									<td align="left">
										<input type="text" name="zip_l" id="zip_l" value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['zipcode']; }else{ echo $row_data_result['zipcode'];}?>">
									</td>
								</tr>
				  
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_website"];?></td>
									<td align="left">
										<input type="text" name="website" id="website" />
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_facebook"];?></td>
									<td align="left">
										<input type="text" name="facebook" id="facebook" />
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_google"];?></td>
									<td align="left">
										<input type="text" name="google" id="google" />
									</td>
								</tr>

								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_email"];?></td>
									<td align="left">
										<input type="text" name="email" id="email" />
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_phone_number"];?></td>
									<td align="left">

										<select name="mobile_country_code_l" id="mobile_country_code_l" style="display:none">
											<option value="001">001</option>
										</select>
										<input type="text" name="mobileno_area_code_l" id="mobileno_area_code_l" style="width:30px; " value="<?php echo $area_code;?>" maxlength="3">-
										<input type="text" name="mobileno2_l" id="mobileno2_l" style="width:30px; " value="<?php echo $mobileno2;?>" maxlength="3">-
										<input type="text" name="mobileno_l" id="mobileno_l" style="width:40px; " value="<?php echo $mobileno1;?>" maxlength="4">
									</td>
								</tr>
								 <tr>
									<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_price_range"];?></td>
									<td align="left">
										<select name="pricerange" id="pricerange">        
												 <option value="0" >Unspecified</option>
												 <option value="1" >$ (Under $10)</option>
												<option value="2" >$$ ($11 - $30)</option>
												<option value="3" >$$$ ($31 - $60)</option>
												<option value="4" >$$$$ (Above $61)</option>		
										 </select>
									</td>
								</tr>
								<tr>
									<td align="right">
									<?php echo $merchant_msg["addlocationdetail"]["Field_parking"];?>
									<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_parking"]; ?>" >&nbsp;&nbsp;&nbsp</span>
									</td>
									<td align="left">
										<input type="checkbox" name="chk_parking[]" value="Garage" id="Garage" /><?php echo "Garage" ?>
										<input type="checkbox" name="chk_parking[]" value="Lot" id="Lot" /><?php echo "Lot" ?>
										<input type="checkbox" name="chk_parking[]" value="Street" id="Street" /><?php echo "Street" ?>
										<input type="checkbox" name="chk_parking[]" value="Valet" id="Valet" /><?php echo "Valet" ?>
									</td>
								</tr>
								<!--
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_time_zone"];?></td>
									<td align="left">
										<select readonly name='timezone' id='timezone' style='width:320px;float:left;margin-right:5px;'>
										<?php
										foreach($timezoness as $key=>$value){
										?>
											<option value='<?php echo $key ?>'  ><?php echo $value; ?></option>
										<?php
										}
										?>
										</select>
										<img src="<?=WEB_PATH?>/merchant/templates/images/001.png" class="loctimezonedivimg" alt="" />
                                        <div class="loctimezonediv">
											<?php echo $merchant_msg["addlocation"]["time_zone_tooltip"];?>
										</div>  
										<div  id='helptext'></div>  
                                        <input type="hidden" name="time_zone_v" id="time_zone_v" value=""/>
										<script type='text/javascript'>TORBIT.dom={get:function(a){return document.getElementsByTagName(a)},gh:function(){return TORBIT.dom.get("head")[0]},ah:function(a){TORBIT.dom.gh().appendChild(a)},ce:function(a){return document.createElement(a)},gei:function(a){return document.getElementById(a)},ls:function(a,b){var c=TORBIT.dom.ce("script");c.type="text/javascript";c.src=a;if("function"==typeof b){c.onload=function(){if(!c.onloadDone){c.onloadDone=true;b()}};c.onreadystatechange=function(){if(("loaded"===c.readyState||"complete"===c.readyState)&&!c.onloadDone){c.onloadDone=true;b()}}}TORBIT.dom.ah(c)}};(function(){var a=window.TORBIT.timing={};var b=function(){if(window.performance==void 0||window.performance.timing==void 0){k(e);h(f);return}h(d)};var c=function(){var b=window.performance.timing;var c=b.navigationStart;for(var d in b){var e=b[d];if(typeof e!="number"||e==0){continue}a[d]=e-c;var f=/(.+)End$/i.exec(d);if(f){a[f[1]+"Elapsed"]=b[d]-b[f[1]+"Start"]}}};var d=function(){c();g()};var e=function(){a.or=(new Date).getTime()-TORBIT.start_time};var f=function(){a.ol=(new Date).getTime()-TORBIT.start_time;g()};var g=function(){var b="/torbit-timing.php?";for(var c in a){b+=c+"="+a[c]+"&"}if(TORBIT.fv==1)b+="fv=1&";if(TORBIT.opt==0)b+="not_opt=1&";TORBIT.dom.ls(b)};var h=function(a){if(typeof window.onload!="function"){return window.onload=a}var b=window.onload;window.onload=function(){b();a()}};var i=false;var j=function(){};var k=function(a){j=l(a);m()};var l=function(a){return function(){if(!i){i=true;a()}}};var m=function(){if(document.addEventListener){document.addEventListener("DOMContentLoaded",j,false)}else if(document.attachEvent){document.attachEvent("onreadystatechange",j);var a=false;try{a=window.frameElement==null}catch(b){}if(document.documentElement.doScroll&&a){n()}}};var n=function(){if(i){return}try{document.documentElement.doScroll("left")}catch(a){setTimeout(n,5);return}j()};b()})();TORBIT.opt=0;TORBIT.fv=1;</script>
										<script type='text/javascript'>    
											jQuery('#timezone').change(function(){
												var text=  jQuery('#timezone :selected').val();    
												jQuery('#time_zone_v').val(text);
											});
										</script>
									 </td>	
								</tr>
								-->
								<!--
								<tr>
                                    <td align="right" style='width:26%'>
                                        <div ><?php echo $merchant_msg["addlocation"]["Field_manage_social_stream"];?> <span class="notification_tooltip" title="<?php echo $merchant_msg["addlocation"]["Tooltip_manage_social_stream"]; ?>" >&nbsp;&nbsp;&nbsp</span></div></td>
                                    <td align="left">                                         
										<table>
											<tr>
												<td>
													Facebook Page :
												</td>
												<td>
													<input type="radio" id="facebookyes" value="1"  checked="checked" name="facebookradio" /> Yes
													<input type="radio" id="facebookno" value="0" name="facebookradio"  /> No
												</td>
											</tr>
											<tr>
												<td>
													Google+ Page : 
												</td>
												<td>
													<input type="radio" id="googleyes" value="1" checked="checked" name="googleradio" /> Yes
													<input type="radio" id="googleno" value="0" name="googleradio"  /> No
												</td>
											</tr>
										</table>
									</td>
                                </tr>
								-->
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktobusinesslogo" value="Back" id="btnBacktobusinesslogo" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationProcess" value="Next" id="btnAddLocationProcess" >
										</td>
									</tr>
							</table>
						</div>
					</div>
					
					<div class="complete_location_hour" style="display: none">
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<tr>
									<td align="right" style="width: 30%;padding-top:10px;">Location Hours : </td>
									<td align="left">
										 <div class="hoursdata" style="display: none">	 
											<input type="hidden" id="addhourdata" weekname="" from="" to="" mon="" tue="" wed="" thu="" fri="" sat="" sun=""/>
											<input type="hidden" class="weeknamehdn" id="monhdn" value="" name="monhdn" />
											<input type="hidden" class="weeknamehdn" id="tuehdn" value="" name="tuehdn"/>
											<input type="hidden" class="weeknamehdn" id="wedhdn" value="" name="wedhdn"/>
											<input type="hidden" class="weeknamehdn" id="thuhdn" value="" name="thuhdn"/>
											<input type="hidden" class="weeknamehdn" id="frihdn" value="" name="frihdn"/>
											<input type="hidden" class="weeknamehdn" id="sathdn" value="" name="sathdn"/>
											<input type="hidden" class="weeknamehdn" id="sunhdn" value="" name="sunhdn"/>	
										 </div>
         
										 <div class="addhoursdiv">
											 <input type="button" id="addhoursid" value="Add Hours" style="background-color:#F2F2F2;background-image:none;background-repeat: none;border:1px solid #DBDBDB; border-radius:5px;color:#0066FF;padding:3px 10px"/>
										</div>
         
										 <div class="timeclass" style="display: none">
											 <div>
											 <script>
										  jQuery(function() {
											jQuery('#defaultValueFrom').timepicker({ 'scrollDefaultNow': true });
														jQuery('#defaultValueTo').timepicker({ 'scrollDefaultNow': true });
										  });
										</script>
										From <input id="defaultValueFrom" name="from" type="text" class="time" style="width: 89px"/>
												To <input id="defaultValueTo" name="to" type="text" class="time" style="width: 89px" />
												
										 
											 </div>
											 <div style="margin-top:5px" id="weekdiv">
													<span id="monspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass"  id="mon" name="mon" value="Mon" />Mon</span>
													<span id="tuespan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="tue" name="tue" value="Tue"/>Tue</span>
													<span id="wedspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="wed" name="wed" value="Wed"/>Wed</span>
													<span id="thuspan" class="weekspan"><input type="checkbox"  from="" to="" class="weelclass" id="thu" name="thu" value="Thu"/>Thu</span>
													<span id="frispan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="fri" name="fri" value="Fri"/>Fri</span>
													<span id="satspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="sat" name="sat" value="Sat"/>Sat</span>
													<span id="sunspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="sun" name="sun" value="Sun"/>Sun</span>
											 </div>
										 <div style="margin-top:10px">
											<input type="button" id="addhourssaveid" value="<?php echo $merchant_msg['index']['btn_save'];?>"  style="padding:3px 10px;background-color:#F2F2F2;background-image:none;background-repeat: none;border:1px solid #DBDBDB; border-radius:5px;color:#0066FF;"/>
											<input type="button" id="addhourscancelid" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" style="padding:3px 10px;background-color:#F2F2F2;background-image:none;background-repeat: none;border:1px solid #DBDBDB; border-radius:5px;color:#0066FF;"/>
										 </div>
										 
										 </div>
         
         
         
         
									</td>
								</tr>
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktoaddlocation" value="Back" id="btnBacktoaddlocation" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationhour" value="Next" id="btnAddLocationhour" >
										</td>
									</tr>
							</table>
						</div>						
					</div>
					
					<div class="complete_location_category" style="display: none">
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<tr id="loc_cat_1">
									<td align="right" style="vertical-align:top;width:16%;">
									<?php echo $merchant_msg["addlocationdetail"]["Field_location_categories"];?>
									<div id="add_cat_tr" style="margin-top:5px;display:none"><a href="javascript:void(0);" id="add_cat" name="add_cat" total="1">Add another category</a></div>
									</td>
									<td align="left">
										<div id="first_lc" >
										<select name="first_cat_first_level" id="first_cat_first_level" size="9">
										<?php
											/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

											$RS_cat_first = $objDB->Conn->Execute($Sql);*/
											$RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

											if($RS_cat_first->RecordCount()>0)
											{
												while($Row_cat_first = $RS_cat_first->FetchRow())
												{
													/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
													$RS1=$objDB->Conn->Execute($Sql);*/
													$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));

													if($RS1->RecordCount()>0)
													{
													?>
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
													<?php
													}
													else
													{
													?>						
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
													<?php
													}
												}
											}	
										?>
										</select>
										</div>
										<div id="second_lc" style="display:none;">
										<select name="second_cat_first_level" id="second_cat_first_level" size="9">
										<?php
											/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

											$RS_cat_first = $objDB->Conn->Execute($Sql);*/
											$RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

											if($RS_cat_first->RecordCount()>0)
											{
												while($Row_cat_first = $RS_cat_first->FetchRow())
												{
													/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
													$RS1=$objDB->Conn->Execute($Sql);*/
													$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));
													if($RS1->RecordCount()>0)
													{
													?>
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
													<?php
													}
													else
													{
													?>						
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
													<?php
													}
												}
											}	
										?>
										</select>
										</div>
										<div id="third_lc" style="display:none;">
										<select name="third_cat_first_level" id="third_cat_first_level" size="9">
										<?php
											/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

											$RS_cat_first = $objDB->Conn->Execute($Sql);*/
											$RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

											if($RS_cat_first->RecordCount()>0)
											{
												while($Row_cat_first = $RS_cat_first->FetchRow())
												{
													/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
													$RS1=$objDB->Conn->Execute($Sql);*/
													$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));
													if($RS1->RecordCount()>0)
													{
													?>
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
													<?php
													}
													else
													{
													?>						
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
													<?php
													}
												}
											}	
										?>
										</select>
										</div>						
									</td>
								  </tr>
								  
								  <tr >
									<td align="right">
										&nbsp;
										
									</td>
									<td align="left">
										<div id="first_lc_delete" class="lc_delete" style="display:none;">
											Selected Category : &nbsp;&nbsp; <span id="first_selected_cat"></span><span id="first_selected_cat_delete" class="selected_delete" catid=""></span>
										</div>
										<div id="second_lc_delete" class="lc_delete" style="display:none;">
											Selected Category : &nbsp;&nbsp; <span id="second_selected_cat"></span><span id="second_selected_cat_delete" class="selected_delete" catid=""></span>
										</div>
										<div id="third_lc_delete" class="lc_delete" style="display:none;">
											Selected Category : &nbsp;&nbsp; <span id="third_selected_cat"></span><span id="third_selected_cat_delete" class="selected_delete" catid=""></span>
										</div>
									</td>
								  </tr>
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktolocationhour" value="Back" id="btnBacktolocationhour" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationcategory" value="Finish" id="btnAddLocationcategory" onclick='validateForm()' >
										</td>
									</tr>
							</table>
						</div>
					</div>
							
					<div class="complete_location_image" style="display: none">
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<tr>
									<td align="right" style="width: 20%;"><?php echo $merchant_msg["addlocation"]["Field_picture"];?></td>
									<td align="left">
										<!-- start of  PAY-508-28033   -->
										<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
										<div style="float: left;">
													<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
													<div id="upload_l" >
													<span  >Browse
													</span> 
													</div>
													</div> <div style="float: left;padding-top: 7px;"> &nbsp;&nbsp;<span style="color:black;">
													<!-- Or select from </span><a class="mediaclass" style="cursor: pointer;color: #0066FF;font-weight: bold" > media library </a></div>--> 
									 <!-- <input type="file" name="business_logo" id="business_logo" />-->
									 <!-- end of  PAY-508-28033   -->
										
									</td>
								  </tr>
								  
								   <!-- T_7 -->
								  <tr><td align="right">&nbsp; </td>
									<td>
							
										<span id="status_l" ></span>
										<br/>
						   
										<ul id="files_l" >
						  
										 </ul>
									</td>
								  </tr>
								  
								  
												  
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktolocationcategory" value="Back" id="btnBacktolocationcategory" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationimage" value="Finish" id="btnAddLocationimage" onclick='validateForm()' />
											<!--
											<input type="submit" style="cursor:pointer;" name="btnAddLocationimage" value="Save" id="btnAddLocationimage" >
											-->
										</td>
									</tr>
							</table>
						</div>
					</div>
					<div class="complete_location_additional_image" style="display: none">
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
							
									<tr>
													  <td align="right" style="width: 20%;"><?php echo $merchant_msg["addlocation"]["Field_add_additional_images"];?> </td>
													  <td align="left">
										<!-- start of  PAY-508-28033   -->
										<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
										<div style="float: left;">
													<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
													<div id="upload_more" style=" background: none repeat scroll 0 0 #F2F2F2;border: 1px solid #CCCCCC;border-radius: 5px 5px 5px 5px;color: #3366CC;cursor: pointer !important;font-family: Arial,Helvetica,sans-serif;font-size: 1.1em;font-weight: bold;height: 15px;padding: 6px;text-align: center;" >
													<span  >Browse
													</span> 
													</div>
																</div>
																<!--
																<div style="float: left;padding-top: 7px;"> &nbsp;&nbsp;<span style="color:black;">Or select from </span><a class="mediaclassmore" style="cursor: pointer;color: #0066FF;font-weight: bold" > media library </a></div>    
																-->
																<div style="float: left;padding-top: 7px;"> &nbsp;&nbsp;<span style="color:black;"></span></div>
																

									 <!-- <input type="file" name="business_logo" id="business_logo" />-->
									 <!-- end of  PAY-508-28033   -->
									</td>
												  </tr>
												  <tr><td align="right">&nbsp; </td>
									<td>
							
										<span id="status_more" style="color:red"></span> 
																<span id="uploading_msg_more" ></span> 
																<!--
																<div class="list_carousel" style="display:none" >
																	<ul id="files_more" style="">

																	</ul>
																	<div class="clearfix"></div>
																	<a id="prev2" class="prev" href="#"><img src="<?=ASSETS_IMG ?>/m/pre_add_campaign.png"></img></a>
																	<a id="next2" class="next" href="#"><img src="<?=ASSETS_IMG ?>/m/next_add_campaign.png"></img></a>
																 </div>
																 -->
																 <div id="additional_images_id">
																	<ul id="files_more">
																	</ul>														
																</div>
																 <div id="additional_images_id_uploaded">
																	
																 </div>
									</td>
								  </tr>						  
								 
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktolocationimage" value="Back" id="btnBacktolocationimage" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationadditionalimage" value="Finish" id="btnAddLocationadditionalimage" onclick='validateForm()'/>
											<!--
											<input type="submit" style="cursor:pointer;" name="btnAddLocationimage" value="Save" id="btnAddLocationimage" >
											-->
										</td>
									</tr>
							</table>
						</div>
					</div>
										
					 <div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
						<div id="NotificationloaderBackDiv" class="divBack">
						</div>
						<div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">

							<div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
								 style="left: 45%;top: 40%;">

								<div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto">
									<img src="<?= ASSETS_IMG ?>/c/128.GIF" style="display: block;" id="image_loader_div"/>
								</div>
							</div>
						</div>
					</div>	
				</div>
	
			</div><!--phl ptm uiInterstitialContent-->
			
		</div><!--nax_wizard_dialog-->
		</form>
<!--end of content--></div>

					
<!--end of contentContainer--></div>
<!---------start footer--------------->
       <div>
  <?
  require_once(MRCH_LAYOUT."/footer.php");
  ?>
  <!--end of footer--></div>

	
</div>
  
</body>
</html>
<!--// 369 -->

<script type="text/javascript">
function close_popuploader(popup_name)
    {
		/*$("#" + popup_name + "FrontDivProcessing").css("display","none");
	$("#" + popup_name + "PopUpContainer").css("display","none");
	$("#" + popup_name + "BackDiv").css("display","none");*/
        /*jQuery("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
            jQuery("#" + popup_name + "BackDiv").fadeOut(200, function () {
                jQuery("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
                   jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
                    jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
                    jQuery("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
                });
            });
        });
		*/
		jQuery("#" + popup_name + "FrontDivProcessing").css("display","none");
	jQuery("#" + popup_name + "PopUpContainer").css("display","none");
	jQuery("#" + popup_name + "BackDiv").css("display","none");
	
    }
    function open_popuploader(popup_name)
    {

	jQuery("#" + popup_name + "FrontDivProcessing").css("display","block");
	jQuery("#" + popup_name + "PopUpContainer").css("display","block");
	jQuery("#" + popup_name + "BackDiv").css("display","block");
        /*$("#" + popup_name + "FrontDivProcessing").fadeIn(10, function () {
            $("#" + popup_name + "BackDiv").fadeIn(10, function () {
                $("#" + popup_name + "PopUpContainer").fadeIn(10, function () {         
	
                });
            });
        });*/
	
	
    }
jQuery(".btnCancelsetup").live("click",function(){
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=change_password',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				return false;	
			}
			else
			{						 		 
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				jQuery.ajax({
						  type:"POST",
						  url:'process.php',
						  data :'remove_setup=yes&merchant_id='+merchant_id,
						  async:false,
						  success:function(msg)
						  {
								window.location="<?php echo WEB_PATH?>/merchant/logout-register.php";
						  }
				});
			}
		}
	});
});	

jQuery('#agree').live("change",function() {
    //alert(jQuery(this).is(':checked'));
	if(jQuery(this).is(':checked'))
	{
		jQuery('#btnUpdatePassword_f').removeAttr("disabled");
		jQuery('#btnUpdatePassword_f').removeClass("disabled");
	}
	else
	{
		jQuery('#btnUpdatePassword_f').attr("disabled","");
		jQuery('#btnUpdatePassword_f').addClass("disabled");
	}
});

jQuery(".btnSkip").live("click",function(){
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=business_page',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				return false;	
			}
			else
			{						 		 
				jQuery("li.uiStep").removeClass("uiStepSelected");
				jQuery("li#update_profile").addClass("uiStepSelected");
				
				jQuery(".complete_change_password").css("display","none");
				jQuery(".complete_business_page").css("display","none");
				jQuery(".complete_update_profile").css("display","block");
				jQuery(".complete_about_us").css("display","none");
				jQuery(".complete_business_logo").css("display","none");
				jQuery(".complete_add_location").css("display","none");
				jQuery(".complete_location_hour").css("display","none");
				jQuery(".complete_location_category").css("display","none");
				jQuery(".complete_location_image").css("display","none");
				jQuery(".complete_location_additional_image").css("display","none");
				
				var tab="update_profile";
				var logout_link=jQuery("#logout_ele").attr("href");
				logout_link1=logout_link.split("?"); 
				jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
			}
		}
	});
});
jQuery("#btnUpdatePassword_f").live("click",function(){
	

	
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=change_password',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{						 
				jQuery("#logout_ele").css("display","block");
				
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var new_password=jQuery("#new_password").val();
				var con_new_password=jQuery("#con_new_password").val();
				var emailid=jQuery("#emailid").val();
				
				
				//alert(emailid);
				var flag="true";
				var msgbox="";
				if(email_validation(emailid) == false)
				{
					msgbox +="<div><?php echo $merchant_msg['login_register']['Msg_valid_email']; ?></div>";
					flag="false";
				}
				
				if(flag=="false")
				{	 
					var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
					var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msgbox+"</div>";
					var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
					return false;
				}
				else
				{
					open_popuploader('Notificationloader');
					
					
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
						
					
					
					jQuery.ajax({
						  type:"POST",
						  url:'process.php',
						  data :'btnUpdatePasswordProcess=yes&merchant_id='+merchant_id+'&new_password='+new_password+'&con_new_password='+con_new_password+'&email='+emailid+'&normal_register=0',
						  async:false,
						  success:function(msg)
						  {
								
								close_popuploader('Notificationloader');
								
								var obj = jQuery.parseJSON(msg);
								//alert(obj.status);
								//alert(obj.message);
								if(obj.status=="false")
								{
									var head_msg="<div style='min-width:180px;line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
									var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+obj.message+"</div>";
									var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
									//alert(content_msg);
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
									
									return false;	
								}
								else
								{
									
										
									jQuery("li.uiStep").removeClass("uiStepSelected");
									jQuery("li#business_page").addClass("uiStepSelected");
									
									jQuery(".complete_change_password").css("display","none");
									jQuery(".complete_business_page").css("display","block");
									jQuery(".complete_update_profile").css("display","none");
									jQuery(".complete_about_us").css("display","none");
									jQuery(".complete_business_logo").css("display","none");
									jQuery(".complete_add_location").css("display","none");
									jQuery(".complete_location_hour").css("display","none");
									jQuery(".complete_location_category").css("display","none");
									jQuery(".complete_location_image").css("display","none");
									
									var tab="business_page";
									var logout_link=jQuery("#logout_ele").attr("href");
									logout_link1=logout_link.split("?"); 
									jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
								}
								
						  }
					});
					
					close_popuploader('Notificationloader');
					},1000);
				}
			}
	    }
	});
		
	
});


jQuery("#btncallbusinesspage").live("click",function(){
		
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=business_page',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{			
				window.location.href=obj.link;
				return false;	
			}
			else
			{						 
					 
				var businesspageurl=jQuery("#businesspageurl").val();

				//alert(emailid);
				var flag="true";
				var msgbox="";
				if(businesspageurl=="")
				{
					msgbox +="<div><?php echo 'Please enter facebook page web address'; ?></div>";
					flag="false";
				}

				if(flag=="false")
				{	 
					var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
					var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msgbox+"</div>";
					var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
					return false;
				}
				else
				{
					
					var businesspageurl=jQuery("#businesspageurl").val();
					businesspageurl=businesspageurl.substring(businesspageurl.lastIndexOf("/")+1);
					
					open_popuploader('Notificationloader');
					
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
	
					jQuery.ajax({
					   type:"POST",
					   url:'getbusinesspage.php',
					   data :'page='+businesspageurl,
					   async:false,
					   success:function(msg)
					   {	
							close_popuploader('Notificationloader');
							
							var obj = jQuery.parseJSON(msg);
									
							if (obj.status=="true")     
							{
								//alert(obj.data);
								//alert(obj.data['about']);
								//alert(obj.data['location']['street']);
								//jQuery("#aboutus_short").val(tinyMCE.get(obj.data['about']).getContent()]);
								
								if(obj.first_name)
								{
									jQuery("#firstname").val(obj.first_name);
								}
								if(obj.last_name)
								{
									jQuery("#lastname").val(obj.last_name);
								}
								
								if(obj.data['location'])
								{
									if(obj.data['location']['street'])
									{
										jQuery("#address").val(obj.data['location']['street']);
									}
									if(obj.data['location']['country'])
									{
										//jQuery("#country").val(obj.data['location']['country']);
									}
									if(obj.data['location']['country']=='United States')
									{
										//jQuery("#country").val('USA');
										//jQuery("#state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");  
									}
									if(obj.data['location']['country']=='Canada')
									{
										//jQuery("#country").val(obj.data['location']['country']);
										//jQuery("#state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
									}
									if(obj.data['location']['state'])
									{
										//jQuery("#state").val(obj.data['location']['state']);
									}
									
									if(obj.data['location']['city'])
									{
										//jQuery("#city").val(obj.data['location']['city']);
									}
									
									if(obj.data['location']['zip'])
									{
										jQuery("#zipcode").val(obj.data['location']['zip']);
									}
								}
								if(obj.data['phone'])
								{
									var str=obj.data['phone'].replace ( /[^\d.]/g, '' );
									
									jQuery("#mobileno_area_code").val(str.substr(0,3));
									jQuery("#mobileno2").val(str.substr(3,3));
									jQuery("#mobileno").val(str.substr(6,4));
								}
								
								if(obj.data['name'])
								{
									jQuery("#business").val(obj.data['name']);;
								}
								
								if(obj.data['about'])
								{
									tinyMCE.getInstanceById('aboutus_short').setContent(obj.data['about']);
								}
								
								var str_description="";
								
								if(obj.data['founded'])
								{
									str_description+="<p>Founded : "+obj.data['founded']+"</p>";
								}
								if(obj.data['awards'])
								{
									str_description+="<p>Awards : "+obj.data['awards']+"</p>";
								}
								if(obj.data['company_overview'])
								{
									str_description+="<p>Company Overview : <br/>"+obj.data['company_overview']+"</p>";
								}
								if(obj.data['general_info'])
								{
									str_description+="<p>General Info : <br/>"+obj.data['general_info']+"</p>";
								}
								if(obj.data['description'])
								{
									str_description+="<p>"+obj.data['description']+"</p>";
								}
								if(obj.data['mission'])
								{
									str_description+="<p>Mission : "+obj.data['mission']+"</p>";
								}
								if(obj.data['products'])
								{
									str_description+="<p>Products : "+obj.data['products']+"</p>";
								}
								
								if(str_description!="")
								{
									tinyMCE.getInstanceById('aboutus').setContent(str_description);
								}
								
								if(obj.data['location'])
								{
									if(obj.data['location']['street'])
									{
										jQuery("#address_l").val(obj.data['location']['street']);
									}
									if(obj.data['location']['country'])
									{
										//jQuery("#country_l").val(obj.data['location']['country']);
									}
									if(obj.data['location']['country']=='United States')
									{
										//jQuery("#country_l").val('USA');
										//jQuery("#state_l").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");  
									}
									if(obj.data['location']['country']=='Canada')
									{
										//jQuery("#country_l").val(obj.data['location']['country']);
										//jQuery("#state_l").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
									}
									if(obj.data['location']['state'])
									{
										//jQuery("#state_l").val(obj.data['location']['state']);
									}
									
									if(obj.data['location']['city'])
									{
										//jQuery("#city_l").val(obj.data['location']['city']);
									}
									
									if(obj.data['location']['zip'])
									{
										jQuery("#zip_l").val(obj.data['location']['zip']);
									}
								}
								if(obj.data['website'])
								{
									jQuery("#website").val(obj.data['website']);
								}
								if(obj.data['link'])
								{
									jQuery("#facebook").val(obj.data['link']);
								}
								if(obj.data['phone'])
								{
									var str=obj.data['phone'].replace ( /[^\d.]/g, '' );
									jQuery("#mobileno_area_code_l").val(str.substr(0,3));
									jQuery("#mobileno2_l").val(str.substr(3,3));
									jQuery("#mobileno_l").val(str.substr(6,4));
								}
								
								if(obj.data['price_range'])
								{
									if(obj.data['price_range']=='$ (0-10)')
									{
										jQuery("#pricerange").val('1');
									}
									if(obj.data['price_range']=='$$ (10-30)')
									{
										jQuery("#pricerange").val('2');
									}
									if(obj.data['price_range']=='$$$ (30-50)')
									{
										jQuery("#pricerange").val('3');
									}
									if(obj.data['price_range']=='$$$$ (50+)')
									{
										jQuery("#pricerange").val('4');
									}
								}
								if(obj.data['parking'])
								{
									
									if(obj.data['parking']['lot']==1)
									{
										jQuery("#Lot").attr("checked","checked");
									}
									if(obj.data['parking']['street']==1)
									{
										jQuery("#Street").attr("checked","checked");
									}
									if(obj.data['parking']['valet']==1)
									{
										jQuery("#Valet").attr("checked","checked");
									}
									
								}
								
								
								
								jQuery("#hdn_image_path_l").val(obj.location_profile_image);
								
								var image_html='<img src="<?php echo ASSETS_IMG;?>/m/location/'+obj.location_profile_image+'" class="displayimg" />';
								image_html +='<br>';
								image_html +='<div style="margin-top: 10px; display:table"><div style="display:table-row;"><div style="display:table-cell;"><img src="<?php echo ASSETS_IMG; ?>/m/delete.gif" id="'+obj.location_profile_image+'" onclick="rm_image_l(this.id)"></div></div></div>';
								if(obj.location_profile_image)
								{
										jQuery("#files_l").html(image_html);
								}
								
								var profile_image_html='<img src="<?php echo ASSETS_IMG;?>/m/icon/'+obj.icon_profile_image+'" alt="Merchant" id="profilePic" class="profilePic silhouette img img2">';
								
								jQuery("#hdn_image_path").val(obj.icon_profile_image);
								if(obj.icon_profile_image)
								{
										jQuery("#nax_profile_pic").html(profile_image_html);
								}
								
								var additional_images_string=obj.additional_images;
								
								
								var additional_imgs_str = "<ul>";
								for(i=0;i<obj.additional_images.length;i++)
								{
									additional_imgs_str +="<li><img src='"+obj.additional_images[i]+"'><span imgurl='"+obj.additional_images[i]+"' style='display:none'>Upload</span></li>";
									//jQuery("#additional_images_id").append("<img src='"+obj.additional_images[i]+"'><span  style='display:none'>Upload</span>");
								}
								additional_imgs_str += "</ul>";
								jQuery("#additional_images_id_uploaded").append(additional_imgs_str);
								
								
								/*
								jQuery.each(obj.data, function(k, v) {
									//display the key and value pair
									//alert(k + ' is ' + v);
									
									if (typeof v != 'string') 
									{
										jQuery.each(v, function(a, b) {
											//alert(a + ' is ' + b);
										});
									}

								});
								*/
								
								jQuery("li.uiStep").removeClass("uiStepSelected");
								jQuery("li#update_profile").addClass("uiStepSelected");
								
								jQuery(".complete_change_password").css("display","none");
								jQuery(".complete_business_page").css("display","none");
								jQuery(".complete_update_profile").css("display","block");
								jQuery(".complete_about_us").css("display","none");
								jQuery(".complete_business_logo").css("display","none");
								jQuery(".complete_add_location").css("display","none");
								jQuery(".complete_location_hour").css("display","none");
								jQuery(".complete_location_category").css("display","none");
								jQuery(".complete_location_image").css("display","none");
								jQuery(".complete_location_additional_image").css("display","none");
					
								var tab="update_profile";
								var logout_link=jQuery("#logout_ele").attr("href");
								logout_link1=logout_link.split("?"); 
								jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
							}
							else
							{
								var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
								var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+obj.message+"</div>";
								var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
								return false;
							}
						}
					});
					
						close_popuploader('Notificationloader');
                   },1000);
				}
						  
					
			}
	    }
	});
		
})

jQuery('#additional_images_id_uploaded img').live("mouseenter",function(){
   jQuery(this).next().css("display","block");
});

jQuery('#additional_images_id_uploaded img').live("mouseleave",function(){
   jQuery(this).next().css("display","none");
});

jQuery('#additional_images_id_uploaded span').live("mouseenter",function(){
   jQuery(this).css("display","block");
});

jQuery('#additional_images_id_uploaded span').live("click",function(){
   var imgurl=jQuery(this).attr('imgurl');
   //alert(imgurl);
   jQuery("#uploading_msg_more").html("");
   
    if(jQuery('#files_more li').length == 24)
	{

	 jQuery("#uploading_msg_more").html('<?php echo $merchant_msg["addlocation"]["Msg_Max_Image_Upload"];?>');
	  return false;
	}
									 
   open_popuploader('Notificationloader');
   
	timeout=setInterval(function()
	{
		//alert("hi");
		clearTimeout(timeout);
	
   try
	{
		jQuery.ajax({
			type: "POST",
			async:false,
			url: '<?php echo WEB_PATH."/merchant/process.php" ?>',
			data: "import_url=" + imgurl +"&upload_facebook_location_image=yes",
			success: function(msg) 
			{
				close_popuploader('Notificationloader');
				var obj = jQuery.parseJSON(msg);
				//alert(obj.filename);
				if (obj.status=="true")     
				{
					var file_path_l = obj.filename;
                    var arr = file_path_l.split("."); 
									
					//jQuery(".list_carousel").show();
					var img = "<div class='mainmoreclass' style='position:relative' ><div class='imagemoreclass' style=''><img src='<?=ASSETS_IMG?>/m/location/temp_thumb/"+ obj.filename +"' class='displayimg'></div>";
					jQuery('<li style="list-style:none" id="li_'+arr[0]+'"></li>').appendTo('#files_more').append(img +"<a href='javascript:void(0)' style='float: left;margin-top: -18px;position: absolute;right: 2px;top: 0;width: 16px;background-color:#fff;' id='"+file_path_l+"' class='closebuttonclass' onclick='rm_image_more(this.id)' ></a><input type='hidden' name='hdn_more_images[]' value='"+ file_path_l +"' /></div>");	
					/*
					 jQuery('#files_more').carouFredSel({
						auto: false,
						prev: '#prev2',
						next: '#next2',
						pagination: "#pager2",
						mousewheel: true,
						height : 80,
						width :445,
						align:"left",
						swipe: {
								onMouse: true,
								onTouch: true
						}
					});
					jQuery('.list_carousel').css("overflow","inherit");
					*/						
				}
				else
				{
					jQuery("#uploading_msg_more").html(obj.message);
				}
			}
		});
	}
	catch(e)
	{
		alert(e);
	}
			close_popuploader('Notificationloader');
	},1000);	
});  
  
jQuery("#btnUpdateProfile").live("click",function(){

	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=update_profile',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msg_box="";
				
				var mobile_country_code=jQuery("#mobile_country_code").val();
				var mobileno_area_code=jQuery("#mobileno_area_code").val();
				var mobileno=jQuery("#mobileno").val();
				var postal_code=jQuery("#zipcode").val();
				var country=jQuery("#country").val();
				var state=jQuery("#state").val();
				var city=jQuery("#city").val();
				
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'btngetstateofcountry=true&country_id='+country,
					async:false,
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="false")     
						{
							jQuery("#state_l").html(obj.html);
						}
						else
						{
							jQuery("#state_l").html(obj.html);
						}
					}
				});
		
				/*
				if(country == "USA")
				{
					jQuery("#state_l").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");   
				}
				else
				{
					jQuery("#state_l").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
				}
				*/
				
				var mobileno2=jQuery("#mobileno2").val();
				var address=jQuery("#address").val();
				
				var lastname=jQuery("#lastname").val();
				var firstname=jQuery("#firstname").val();
				var business=jQuery("#business").val();
				
				var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
				var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
				var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
				var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
				var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
				var flag="true";
				
				if(firstname == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_first_name']; ?></div>";
					flag="false";
				}
				if(lastname == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_last_name']; ?></div>";
					flag="false";
				}
				if(address == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_address']; ?></div>";
					flag="false";
				}
				console.log("country="+country+" state="+state+" city="+city);
				if(country == "0" || country == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_country']; ?></div>";
					flag="false";
				}
				if(state == "0" || state == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_state']; ?></div>";
					flag="false";
				}
				if(city == "0" || city == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_city']; ?></div>";
					flag="false";
				}
				if(postal_code=="")
				{
					//alert("Please enter postal/zipcode");
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_postal_zipcode']; ?></div>";
					flag="false";
				}
				else
				{
                    postal_code=jQuery.trim(postal_code);
					postal_code=postal_code.toUpperCase();
					if(country=="1")
					{
					   if(!usPostalReg.test(postal_code)) 
					   {
							//alert("Please enter valid postal/zipcode");				
							msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_valid_postal_zipcode']; ?></div>";
							flag="false";
					   }	
						
					}
					else if(country == "2")
					{
									
						if(!canadaPostalReg.test(postal_code)) 
						{				  
							//alert("Please enter valid postal/zipcode");
							msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_valid_postal_zipcode']; ?></div>";
							flag="false";
						}
					}
				}
				if(mobileno_area_code == "" || mobileno =="" || mobileno2 == "" )
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
					 flag="false";
				}
				else 
				{
					if(mobileno_area_code != "")
					{

						if(!numericReg.test(mobileno_area_code)) 
						{
							//alert("Please Input Valid Mobile Number");
							msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
						   flag="false";
							//return false;
						}
						else
						{
							if(mobileno_area_code.length != 3)
							{
								//alert("Please Input Valid Area Code Number");
								msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
							   flag="false";
								//return false;
							}
						}

					}
					else if(mobileno != "")
					{
						if(!numericReg.test(mobileno)) 
						{
							//alert("Please Input Valid Mobile Number");
							msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
						   flag="false";
							//return false;
						}
						else
						{
							if(mobileno.length != 4)
							{
								//alert("Please Input Valid Mobile Number");
								msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
								flag="false";
								//return false;
							} 
						}

					}
					else if(mobileno2 != "")
					{
						if(!numericReg.test(mobileno2)) 
						{
							//alert("Please Input Valid Mobile Number");
							msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
						   flag="false";
							//return false;
						}
						else
						{
							if(mobileno2.length != 3)
							{
								//alert("Please Input Valid Mobile Number");
								msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
								flag="false";
								//return false;
							}
							
						}

					}		   
				}
				
				if(business=="")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_business_name']; ?></div>";
					flag="false";
				}
				
				var tag_value=jQuery("#business_tags").val();
				if(tag_value == "")
				{
					msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_please_add_business_tag"]; ?></div>";
					flag="false";
				}
				else
				{
					if(hastagRef.test(tag_value))
					{
						
						var tag_arr = tag_value.split(",");   
						//alert(tag_arr.length);
						if(tag_arr.length>15)
						{
							msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_add_business_tag"]; ?></div>";
							flag="false";
						}
						
					}
					else
					{
					
						msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_business_tag"]; ?></div>";
						flag="false";
					}
				}
	
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{	  
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
					return false;
				}
				else
				{
				
					open_popuploader('Notificationloader');		
					
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
									
					jQuery.ajax({
					  type:"POST",
					  url:'process.php',
					  data :'btnUpdateProfileProcess=yes&merchant_id='+merchant_id+'&firstname='+firstname+'&lastname='+lastname+'&address='+address
							+'&city='+city+'&state='+state+'&zipcode='+postal_code+'&country='+country+'&mobile_country_code='+mobile_country_code
							+'&mobileno_area_code='+mobileno_area_code+'&mobileno2='+mobileno2+'&mobileno='+mobileno+'&business='+business+"&business_tags="+tag_value,
					  async:false,
					  success:function(msg)
					  {
						close_popuploader('Notificationloader');
							var obj = jQuery.parseJSON(msg);
							//alert(obj.status);
							//alert(obj.message);
							if(obj.status=="true")
							{
								jQuery("li.uiStep").removeClass("uiStepSelected");
								jQuery("li#about_us").addClass("uiStepSelected");
								
								jQuery(".complete_change_password").css("display","none");
								jQuery(".complete_business_page").css("display","none");
								jQuery(".complete_update_profile").css("display","none");
								jQuery(".complete_about_us").css("display","block");
								jQuery(".complete_business_logo").css("display","none");
								jQuery(".complete_add_location").css("display","none");
								jQuery(".complete_location_hour").css("display","none");
								jQuery(".complete_location_category").css("display","none");
								jQuery(".complete_location_image").css("display","none");
								jQuery(".complete_location_additional_image").css("display","none");
								
								var tab="about_us";
									var logout_link=jQuery("#logout_ele").attr("href");
									logout_link1=logout_link.split("?"); 
									jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
									
							}
					  }
					});		
		
						close_popuploader('Notificationloader');
                      },1000);
				}
			}
	    }
	});			
});

jQuery("#btnUpdateAboutus").live("click",function(){

	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=about_us',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msg_box="";
				var flag="true";
				jQuery('#aboutus').val(tinyMCE.get('aboutus').getContent());
				jQuery('#aboutus_short').val(tinyMCE.get('aboutus_short').getContent());
				var aboutus=encodeURIComponent(jQuery("#aboutus").val());
				var aboutus_short=jQuery("#aboutus_short").val();
				//alert(aboutus);
				//alert(aboutus_short);
				if(aboutus_short=="")
				{		
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_about_you_short']; ?></div>";
					flag="false";
				}
				if(aboutus=="")
				{		
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_about_you']; ?></div>";
					flag="false";
				}
				
				
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{	  
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
					return false;
				}
				else
				{
					open_popuploader('Notificationloader');
					
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
					
					
					jQuery.ajax({
					  type:"POST",
					  url:'process.php',
					  data :'btnUpdateAboutusProcess=yes&merchant_id='+merchant_id+'&aboutus='+aboutus+'&aboutus_short='+aboutus_short,
					  async:false,
					  success:function(msg)
					  {
						close_popuploader('Notificationloader');
							var obj = jQuery.parseJSON(msg);
							//alert(obj.status);
							//alert(obj.message);
							if(obj.status=="true")
							{
								jQuery("li.uiStep").removeClass("uiStepSelected");
								jQuery("li#business_logo").addClass("uiStepSelected");
								
								jQuery(".complete_change_password").css("display","none");
								jQuery(".complete_business_page").css("display","none");
								jQuery(".complete_update_profile").css("display","none");
								jQuery(".complete_about_us").css("display","none");
								jQuery(".complete_business_logo").css("display","block");
								jQuery(".complete_add_location").css("display","none");
								jQuery(".complete_location_hour").css("display","none");
								jQuery(".complete_location_category").css("display","none");
								jQuery(".complete_location_image").css("display","none");
								jQuery(".complete_location_additional_image").css("display","none");
								
								var tab="business_logo";
									var logout_link=jQuery("#logout_ele").attr("href");
									logout_link1=logout_link.split("?"); 
									jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
							}
					  }
					});
					
						close_popuploader('Notificationloader');
                    },1000);
				}
			}
	    }
	});				
});

jQuery("#btnUpdateBusinesslogo").live("click",function(){
	
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=business_logo',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msg_box="";
				var flag="true";

				if(jQuery("#hdn_image_path").val()=="")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_upload_merchant_icon']; ?></div>";
					flag="false";
				}
				
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{	  
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
					return false;
				}
				else
				{
					open_popuploader('Notificationloader');
					
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
							
					jQuery.ajax({
					  type:"POST",
					  url:'process.php',
					  data :'btnUpdateBusinessLogoProcess=yes&merchant_id='+merchant_id+'&hdn_image_path='+jQuery("#hdn_image_path").val(),
					  async:false,
					  success:function(msg)
					  {
						close_popuploader('Notificationloader');
							var obj = jQuery.parseJSON(msg);
							//alert(obj.status);
							//alert(obj.message);
							if(obj.status=="true")
							{
								jQuery("li.uiStep").removeClass("uiStepSelected");
								jQuery("li#add_location").addClass("uiStepSelected");
								
								jQuery(".complete_change_password").css("display","none");
								jQuery(".complete_business_page").css("display","none");
								jQuery(".complete_update_profile").css("display","none");
								jQuery(".complete_about_us").css("display","none");
								jQuery(".complete_business_logo").css("display","none");
								jQuery(".complete_add_location").css("display","block");
								jQuery(".complete_location_hour").css("display","none");
								jQuery(".complete_location_category").css("display","none");
								jQuery(".complete_location_image").css("display","none");
								jQuery(".complete_location_additional_image").css("display","none");
								
								var tab="add_location";
									var logout_link=jQuery("#logout_ele").attr("href");
									logout_link1=logout_link.split("?"); 
									jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
							}
					  }
					});					
				
						close_popuploader('Notificationloader');
                    },1000);
				
				
				}
			}
	    }
	});			
});

jQuery("#btnAddLocationProcess").live("click",function(){
	open_popuploader('Notificationloader');
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
				
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msgbox="";
				var flag="true";

				var address1=jQuery("#address_l").val();
				var country=jQuery("#country").val();
				var state1=jQuery('#state_l').val();
				var city1=jQuery('#city_l').val();
				var zipcode1=jQuery('#zip_l').val();
				//var country=jQuery("#country_l").val();
				
								
				var mobileno_area_code=jQuery("#mobileno_area_code_l").val();
				var mobileno_area_code_length=jQuery("#mobileno_area_code_l").val().length;
				var mobileno=jQuery("#mobileno_l").val();
				var mobileno2=jQuery("#mobileno2_l").val();
				var mobileno_length=jQuery("#mobileno_l").val().length;
				var mobileno2_length=jQuery("#mobileno2_l").val().length;
				var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
				var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
				
				var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
				var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
				
				var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
				var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
				zipcode1=zipcode1.toUpperCase();
						
				var flag="";
				var msgbox="";
				if(address1 == "")
				{
				   msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_address"];?></div>";
				   flag="false";
				}
				if(state1 == "0" || state1 == "")
				{
					msgbox +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_state']; ?></div>";
					flag="false";
				}
				if(city1 == "0" || city1 == "")
				{
					msgbox +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_city']; ?></div>";
					flag="false";
				}
				if(zipcode1 == "")
				{
				   msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_postal_zipcode"];?></div>";
				   flag="false";
				}
				else
				{
				  zipcode1=jQuery.trim(zipcode1);
                                    zipcode1=zipcode1.toUpperCase();
				   if(country=="1")
				   {
					   if(!usPostalReg.test(zipcode1)) {
						
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_postal_zipcode"];?></div>";
						flag="false";
					   }	
						
					}
					else if(country == "2")
					{
						if(!canadaPostalReg.test(zipcode1)) {
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_postal_zipcode"];?></div>";
						flag="false";
						}
					}
				}
				
				if(document.getElementById("email").value!="")
				{				
					if(email_validation(document.getElementById("email").value) == false)
					{
						msgbox +="<div><?php echo $merchant_msg['login_register']['Msg_valid_email']; ?></div>";
						flag="false";
					}
				}
				if(mobileno_area_code != "")
				{			
					if(!numericReg.test(mobileno_area_code))
					{		
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
						flag="false";               
					}
					else if(mobileno_area_code_length<=2)
					{
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
						flag="false";
					}                                              
				}
				else
				{
					msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
					flag="false";
				}
				if(mobileno != "" || mobileno2 != "")
				{
					if(!numericReg.test(mobileno) || !numericReg.test(mobileno2)) 
					{
						//alert("Please Input Valid Mobile Number");
						//return false;
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
						flag="false";
					}
					else if(mobileno_length <=3 || mobileno2_length <= 2)
					{
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
						flag="false";
					}
				}
				else
				{
					msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
					flag="false";
				}
								   
				mobile_no = jQuery("#mobile_country_code_l").val()+"-"+jQuery("#mobileno_area_code_l").val()+"-"+jQuery("#mobileno2_l").val()+"-"+jQuery("#mobileno_l").val();

				

				
								 
				var parkingcount=jQuery('input:checkbox[name="chk_parking[]"]:checked').size();
				if(parkingcount==0)
				{
					//alert("<?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_parking"];?>");
					//return false;
					msgbox+="<div><?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_parking"];?></div>";
					flag="false";
				}
					
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msgbox+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{	  
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
					return false;
				}
				else
				{
					
					jQuery("li.uiStep").removeClass("uiStepSelected");
					jQuery("li#location_hour").addClass("uiStepSelected");
					
					jQuery(".complete_change_password").css("display","none");
					jQuery(".complete_business_page").css("display","none");
					jQuery(".complete_update_profile").css("display","none");
					jQuery(".complete_about_us").css("display","none");
					jQuery(".complete_business_logo").css("display","none");
					jQuery(".complete_add_location").css("display","none");
					jQuery(".complete_location_hour").css("display","block");
					jQuery(".complete_location_category").css("display","none");
					jQuery(".complete_location_image").css("display","none");
					jQuery(".complete_location_additional_image").css("display","none");
					
					var tab="add_location";
					var logout_link=jQuery("#logout_ele").attr("href");
					logout_link1=logout_link.split("?"); 
					jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);	
					
				}
			}
	    }
	});
	close_popuploader('Notificationloader');	
});

jQuery("#btnAddLocationhour").live("click",function(){
	open_popuploader('Notificationloader');
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
							
				jQuery("li.uiStep").removeClass("uiStepSelected");
				jQuery("li#location_category").addClass("uiStepSelected");
				
				jQuery(".complete_change_password").css("display","none");
				jQuery(".complete_business_page").css("display","none");
				jQuery(".complete_update_profile").css("display","none");
				jQuery(".complete_about_us").css("display","none");
				jQuery(".complete_business_logo").css("display","none");
				jQuery(".complete_add_location").css("display","none");
				jQuery(".complete_location_hour").css("display","none");
				jQuery(".complete_location_category").css("display","block");
				jQuery(".complete_location_image").css("display","none");
				jQuery(".complete_location_additional_image").css("display","none");
				
				var tab="add_location";
				var logout_link=jQuery("#logout_ele").attr("href");
				logout_link1=logout_link.split("?"); 
				jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);					
			}
	    }
	});
	close_popuploader('Notificationloader');	
});
/*
jQuery("#btnAddLocationcategory").live("click",function(){
	open_popuploader('Notificationloader');
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				jQuery("li.uiStep").removeClass("uiStepSelected");
				jQuery("li#location_image").addClass("uiStepSelected");
				
				jQuery(".complete_change_password").css("display","none");
				jQuery(".complete_business_page").css("display","none");
				jQuery(".complete_update_profile").css("display","none");
				jQuery(".complete_about_us").css("display","none");
				jQuery(".complete_business_logo").css("display","none");
				jQuery(".complete_add_location").css("display","none");
				jQuery(".complete_location_hour").css("display","none");
				jQuery(".complete_location_category").css("display","none");
				jQuery(".complete_location_image").css("display","block");
				jQuery(".complete_location_additional_image").css("display","none");
				
				var tab="add_location";
				var logout_link=jQuery("#logout_ele").attr("href");
				logout_link1=logout_link.split("?"); 
				jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);	
			}
	    }
	});	
	close_popuploader('Notificationloader');	
});
*/
/*
jQuery("#btnAddLocationimage").live("click",function(){
	open_popuploader('Notificationloader');
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msg_box="";
				var flag="true";
				var hdn_image_path_l=jQuery("#hdn_image_path_l").val();
				//alert(aboutus);
				if(hdn_image_path_l=="")
				{		
					msg_box +="<div><?php echo $merchant_msg['addlocation']['Msg_Please_Upload_Location_Image']; ?></div>";
					flag="false";
				}
				
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				//alert(flag);
				if(flag=="false")
				{
						
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
					
					return false;
				}
				else
				{
			
					jQuery("li.uiStep").removeClass("uiStepSelected");
					jQuery("li#location_additional_image").addClass("uiStepSelected");
					
					jQuery(".complete_change_password").css("display","none");
					jQuery(".complete_business_page").css("display","none");
					jQuery(".complete_update_profile").css("display","none");
					jQuery(".complete_about_us").css("display","none");
					jQuery(".complete_business_logo").css("display","none");
					jQuery(".complete_add_location").css("display","none");
					jQuery(".complete_location_hour").css("display","none");
					jQuery(".complete_location_category").css("display","none");
					jQuery(".complete_location_image").css("display","none");
					jQuery(".complete_location_additional_image").css("display","block");
					
					var tab="add_location";
					var logout_link=jQuery("#logout_ele").attr("href");
					logout_link1=logout_link.split("?"); 
					jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);	
				
				}			
			}
	    }
	});
	close_popuploader('Notificationloader');	
});
*/
function validateForm()
{

	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{	
												
				var msg_box='<?php echo $merchant_msg['login_register']['Msg_merchant_register'] ?>';
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel_index' name='popupcancel_index' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				jQuery.fancybox({
					content:jQuery('#dialog-message').html(),
					type: 'html',
					openSpeed  : 300,
					closeSpeed  : 300,
					changeFade : 'fast',
					closeBtn: false,					
					helpers: {
						overlay: {
						closeClick: false,
						opacity: 0.3
						} // overlay
					}
				});
				
			}
	    }
	});				
}

jQuery(".fancybox-inner #popupcancel_index").live("click",function(){
	document.forms[0].submit();		
});

</script>

<?php
if($data_sub_merchant_id['merchant_parent'] == "0")
{ ?>
<script>
var file_path = "";
jQuery(function(){
		var btnUpload=jQuery('#upload');
		var status=jQuery('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUploadMerchant&img_type=icon',
			name: 'uploadfile',
			onSubmit: function(file, ext){
			
				jQuery("#upload_business_logo").css("display","none");
				open_popuploader('Notificationloader');
				
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
				//alert(response);
                
                
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
								
				close_popuploader('Notificationloader');				
				jQuery("#upload_business_logo").css("display","block");
				
				var image = document.getElementById('profilePic');
				var width = image.naturalWidth;
				var height = image.naturalHeight;
				//alert(width);
				//alert(height);
				if(height>=111)
				{
					jQuery("#profilePic").addClass("img1");
				}
				else
				{
					jQuery("#profilePic").addClass("img2");
				}
							
			}
		});
		
	});
</script>
<?php } ?>

<script>
 
/* start of script for PAY-508-28033*/
function save_from_library()
{
	 var sel_val = $('input[name=use_image]:checked').val();
	 <!--// 369-->
	 if (sel_val==undefined)
	 {
	 	close_popup('Notification');
	 }
	 else
	 {
		
		jQuery("#hdn_image_id").val(sel_val);
		var sel_src = jQuery("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
		//alert(sel_src);
	       jQuery("#hdn_image_path").val(sel_src);
	       file_path = "";
	       close_popup('Notification');
	       var img = "<img src='<?=ASSETS_IMG?>/m/campaign/"+ sel_src +"' class='displayimg'>";
	       jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' onclick='rm_image()' /></div></div></div>");
	}
	 <!--// 369-->
	
}
function rm_image()
{
	jQuery("#hdn_image_path").val("");
	jQuery("#hdn_image_id").val("");
	jQuery('#files').html("");
	
}
function save_from_computer()
{
	jQuery("#hdn_image_path").val(file_path);
	jQuery("#hdn_image_id").val("");
	jQuery("#profilePic").attr("src","<?=ASSETS_IMG?>/m/icon/"+ file_path);
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG?>/m/icon/"+ file_path +"' class='displayimg'>";
	jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' onclick='rm_image()' /></div></div></div>");
				
}
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
 function open_popup(popup_name)
{

	if(jQuery("#hdn_image_id").val()!="")
	{
		jQuery('input[name=use_image][value='+jQuery("#hdn_image_id").val()+']').attr("checked","checked");
	}
	jQuery("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		jQuery("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 jQuery("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	
}
jQuery(document).ready(function(){
	if(jQuery("#hdn_image_path").val() != "")
	{
		var img = "<img src='<?=ASSETS_IMG?>/m/icon/"+ jQuery("#hdn_image_path").val() +"' class='displayimg'>";
		jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' onclick='rm_image()' /></div></div></div>");
		jQuery("#profilePic").attr("src","<?=ASSETS_IMG?>/m/icon/"+ jQuery("#hdn_image_path").val());
		
		var image = document.getElementById('profilePic');
		var width = image.naturalWidth;
		var height = image.naturalHeight;
		//alert(width);
		//alert(height);
		if(height>=111)
		{
			jQuery("#profilePic").addClass("img1");
		}
		else
		{
			jQuery("#profilePic").addClass("img2");
		}

	}
	
	jQuery('#country').change(function(){
		var change_value=this.value;
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btngetstateofcountry=true&country_id='+change_value,
			async:false,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="false")     
				{
					jQuery("#state").html(obj.html);
				}
				else
				{
					jQuery("#state").html(obj.html);
				}
			}
		});
		jQuery('#state').trigger("change");
    });
    
    jQuery('#state').change(function(){
		var change_value=this.value;
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btngetcityofstate=true&state_id='+change_value,
			async:false,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="false")     
				{
					jQuery("#city").html(obj.html);
				}
				else
				{
					jQuery("#city").html(obj.html);
				}
			}
		});
    
    });
    
    jQuery('#state_l').change(function(){
		var change_value=this.value;
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btngetcityofstate=true&state_id='+change_value,
			async:false,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="false")     
				{
					jQuery("#city_l").html(obj.html);
				}
				else
				{
					jQuery("#city_l").html(obj.html);
				}
			}
		});
    
    });
	
	
});
jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
});

var file_path_l="";
jQuery(function(){
		var btnUpload=jQuery('#upload_l');
		var status=jQuery('#status_l');

		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUpload&img_type=location',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				open_popuploader('Notificationloader');
				if(jQuery('#files_l').children().length > 0)
				{
					jQuery('#files_l li').detach();
				}
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('<?php echo $merchant_msg["addlocation"]["Msg_Image_Validation"];?>');
					return false;
				}
				status.text('<?php echo $merchant_msg["addlocation"]["Msg_uploading"];?>');
			},
			onComplete: function(file, response){
				close_popuploader('Notificationloader');
				//On completion clear the status
                                /*
				var arr = response.split("|");
				
				status.text('');
				//Add uploaded file to list
				file_path = arr[1];
				save_from_computer();
                                 */
                                //alert(response);
                                var arr = response.split("|");
				if(arr[1]=="small")
                                {
                                    status.text(arr[0]);
                                }
                                else
                                {
                                    status.text('');
                                    //Add uploaded file to list
                                    file_path_l = arr[1];
                                    save_from_computer_l();
                                }
			}
		});
                
                /* More Images Upload Code*/
                var btnUpload=$('#upload_more');
                var uploading=$('#uploading_msg_more');
		var status_more=$('#status_more');
		
		new AjaxUpload(btnUpload, {
			action: 'upload_additional_images.php?doAction=FileUpload&img_type=location',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				open_popuploader('Notificationloader');
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status_more.text('<?php echo $merchant_msg["addlocation"]["Msg_Image_Validation"];?>');
					return false;
				}
                                if(jQuery('#files_more li').length == 25)
                                     {
                                        
                                         status_more.text('<?php echo $merchant_msg["addlocation"]["Msg_Max_Image_Upload"];?>');
                                          return false;
                                     }
                                     status_more.text('');
				uploading.text('<?php echo $merchant_msg["addlocation"]["Msg_uploading"];?>');
			},
			onComplete: function(file, response){
				close_popuploader('Notificationloader');
                                var arr = response.split("|");
                                
				if(arr[1]=="small")
                                {
                                    status_more.text(arr[0]);
                                     uploading.text('');
                                }
                                else
                                {
                                    status_more.text('');
                                     uploading.text('');
                                    //Add uploaded file to list
                                    file_path_l = arr[1];
                                    var arr = file_path_l.split("."); 
                                    //$("#hdn_image_path_more").val(file_path_l);
                                    //$("#hdn_image_id").val("");
                                    //close_popup('Notification');
                                    //jQuery(".list_carousel").show();
                                    var img = "<div class='mainmoreclass' style='position:relative' ><div class='imagemoreclass' style=''><img src='<?=ASSETS_IMG?>/m/location/temp_thumb/"+ file_path_l +"' class='displayimg'></div>";
                                   // jQuery('<li style="list-style:none" id="li_'+arr[0]+'"></li>').appendTo('#files_more').append(img +"<div style='margin-top: 10px; display:table;float:left;width:48px;height:65px;'><img src='<?=ASSETS_IMG ?>/m/delete.gif' id='"+file_path+"' onclick='rm_image_more(this.id)' /></div><input type='hidden' name='hdn_more_images[]' value='"+ file_path +"' /></div>");	
                                  jQuery('<li style="list-style:none" id="li_'+arr[0]+'"></li>').appendTo('#files_more').append(img +"<a href='javascript:void(0)' style='float: left;margin-top: -28px;position: absolute;right: -10px;top: 0;width: 16px;back:#fff' id='"+file_path_l+"' class='closebuttonclass' onclick='rm_image_more(this.id)' ></a><input type='hidden' name='hdn_more_images[]' value='"+ file_path_l +"' /></div>");	 
//save_more_from_computer();
                                        //var set_int=setInterval(function() {
                                             
                                        //}, 2000);
                                       

                                         
                                }
                                /*
                                jQuery('#files_more').carouFredSel({
                                                auto: false,
                                                prev: '#prev2',
                                                next: '#next2',
                                                pagination: "#pager2",
                                                mousewheel: true,
                                                height : 80,
                                                width :445,
                                                align:"left",
                                                swipe: {
                                                        onMouse: true,
                                                        onTouch: true
                                                }
                                            });
                                jQuery('.list_carousel').css("overflow","inherit");
								*/
                                 
			}
		});
                
                /* End More Images Upload Code*/
                
});
function rm_image_more(data)
{
	
      
       var arr1 = data.split(".");
       jQuery.ajax({
                           type:"POST",
                           url:'remove_additional_images.php',
                           data :'imagename='+data,
                          async:false,
                           success:function(msg)
                           {
                             
                             jQuery("#li_"+arr1[0]).remove();
                             /*
							 jQuery('#files_more').carouFredSel({
                                                auto: false,
                                                prev: '#prev2',
                                                next: '#next2',
                                                pagination: "#pager2",
                                                mousewheel: true,
                                                height : 80,
                                                width :445,
                                                align:"left",
                                                swipe: {
                                                        onMouse: true,
                                                        onTouch: true
                                                }
                                            });
                                            jQuery('.list_carousel').css("overflow","inherit");
                            */
                           }
                           
                     });
                     /*
                      jQuery('.list_carousel ul').each(function() {
                          
                        if (jQuery(this).children().length == 0) {
                          jQuery('.list_carousel').hide();
                        }
                      });  
					*/	
}
function save_from_library_l()
{
	 var sel_val = $('input[name=use_image]:checked').val();
	 <!--// 369-->
	 if (sel_val==undefined)
	 {
	 	close_popup('Notification');
	 }
	 else
	 {
		
		jQuery("#hdn_image_id_l").val(sel_val);
		var sel_src = jQuery("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
		//alert(sel_src);
	       jQuery("#hdn_image_path_l").val(sel_src);
	       file_path_l = "";
	       close_popup('Notification');
	       var img = "<img src='<?=ASSETS_IMG?>/m/campaign/"+ sel_src +"' class='displayimg'>";
	       jQuery('#files_l').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' id='"+sel_src+"' onclick='rm_image_l(this.id)' /></div></div></div>");
	}
	 <!--// 369-->
	
}
function rm_image_l(id)
{
	
	jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'is_image_delete=yes&image_type=location&filename='+id,
                          async:false,
                           success:function(msg)
                           {
								jQuery("#hdn_image_path_l").val("");
								jQuery("#hdn_image_id_l").val("");
								jQuery('#files_l').html("");
                           }
                           
                     });
	
}
function save_from_computer_l()
{
	jQuery("#hdn_image_path_l").val(file_path_l);
	jQuery("#hdn_image_id_l").val("");
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG?>/m/location/"+ file_path_l +"' class='displayimg'>";
	jQuery('#files_l').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' id='"+file_path_l+"' onclick='rm_image_l(this.id)' /></div></div></div>");
				
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
	
	jQuery('#country_l').change(function(){
		var change_value=this.value;
		if(change_value == "Canada")
		{
			 jQuery("#state_l").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
		}
		else
		{
		   jQuery("#state_l").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ' >NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>"); 
		}
    });
jQuery('#addhoursid').click(function(){
         jQuery(".timeclass").show();
        jQuery(".addhoursdiv").hide();
        jQuery(".hoursdata").hide();

     var arr_mon_hdn = jQuery("#monhdn").val().split("-");
     var arr_tue_hdn = jQuery("#tuehdn").val().split("-");
     var arr_wed_hdn = jQuery("#wedhdn").val().split("-");
     var arr_thu_hdn = jQuery("#thuhdn").val().split("-");
     var arr_fri_hdn = jQuery("#frihdn").val().split("-");
     var arr_sat_hdn = jQuery("#sathdn").val().split("-");
     var arr_sun_hdn = jQuery("#sunhdn").val().split("-");
     
      jQuery("#defaultValueFrom").val('');
      jQuery("#defaultValueTo").val('');
      jQuery(".weelclass").removeAttr("checked");
     
     if(arr_mon_hdn[0] == "mon")
     {
             jQuery("#monspan").hide();
        
          jQuery("#mon").removeAttr( "checked" );    
     }
     if(arr_tue_hdn[0] == "tue")
     {
             jQuery("#tuespan").hide();
            jQuery("#tue").removeAttr( "checked" ); 
     }
     if(arr_wed_hdn[0] == "wed")
     {
             jQuery("#wedspan").hide();
             
             jQuery("#wed").removeAttr( "checked" ); 
     }
     if(arr_thu_hdn[0] == "thu")
     {
             jQuery("#thuspan").hide();
           jQuery("#thu").removeAttr( "checked" );
            
     }
     if(arr_fri_hdn[0] == "fri")
     {
             jQuery("#frispan").hide();
             jQuery("#fri").removeAttr( "checked" );
     }
     if(arr_sat_hdn[0] == "sat")
     {
             jQuery("#satspan").hide();
             jQuery("#sat").removeAttr( "checked" );
     }
     if(arr_sun_hdn[0] == "sun")
     {
             jQuery("#sunspan").hide();
             jQuery("#sun").removeAttr( "checked" );
     }
     
        var i=1;
      
        jQuery(".weekspan").each(function(index){
            
        
            if (jQuery(this).css('display') == 'none') {
                    
              }
              else
                  {
                      
                       if(jQuery("input[class=weelclass]:checked").length == "0")
                           {
                
                                jQuery("#addhourssaveid").attr("disabled", "disabled");
                                jQuery("#addhourssaveid").addClass("disabled");
                                jQuery("#addhourssaveid").css("color","#ABABAB !important");
                        
                           }
                           else
                           {
                               jQuery("#addhourssaveid").removeAttr("disabled");
                               jQuery("#addhourssaveid").removeClass( "disabled" );
                               jQuery("#addhourssaveid").css("color","#0066FF !important");
                           }    
                      
                  }
                  
        });
         
     });
     jQuery('#addhourscancelid').click(function(){
         jQuery(".timeclass").hide();
         jQuery(".addhoursdiv").show();
         
         //alert(jQuery('.hoursdata').contents().length);
         if(jQuery('.hoursdata').contents().length == "17")
             {
                jQuery(".hoursdata").hide(); 
             }
             else
                 {
                     jQuery(".hoursdata").show();
                 }
         
         
     });
     
     jQuery('#addhourssaveid').click(function(){
         
         
         jQuery(".timeclass").hide();
         jQuery(".addhoursdiv").show();
         jQuery(".hoursdata").show();
         //var from=jQuery("#addhourdata").attr("from");
         //var to=jQuery("#addhourdata").attr("to");
         var from=jQuery("#defaultValueFrom").val();
         var to=jQuery("#defaultValueTo").val();
         //alert(from + to);
         var checkboxval="";
         var totalchecked=jQuery('input[class=weelclass]:checked').size();
         
         
         jQuery("input[class=weelclass]:checked").each(function(index) {
           
        
       var weekname= jQuery(this).val().toLowerCase();
       jQuery("#"+weekname+"hdn").val(weekname+"-"+from+"-"+to);
       jQuery("#"+weekname+"hdn").attr("from",from);
       jQuery("#"+weekname+"hdn").attr("to",to);
       
        checkboxval = jQuery(this).val();
       
        //alert(from + to + checkboxval);
                jQuery(".hoursdata").append("<div style='float:left;' id='"+jQuery(this).val()+"'><div style='width: 300px;float: left;line-height: 48px;'><span id='dis_"+ checkboxval +"'>From : " + from.toUpperCase() +" To : " + to.toUpperCase() + " - "+ checkboxval+"</span></div><div style='float: left;width: 30px;margin-bottom:7px;line-height: 31px'><a href='javascript:void(0)'  class='removeclass closebuttonclass' id='remove_"+ checkboxval+"'></a></div><div>");
               
            });
         
            if(jQuery(".removeclass").length == "7")
                {
                    jQuery("#addhoursid").attr("disabled", "disabled");
                    jQuery("#addhoursid").addClass("disabled");
                    jQuery("#addhoursid").css("color","#ABABAB !important");
                }
                else
                    {
                        
                        jQuery("#addhoursid").removeAttr("disabled");
                        jQuery("#addhoursid").removeClass( "disabled" );
                        jQuery("#addhoursid").css("color","#0066FF !important");
                    }
                    
           
           
           
           
        
         
     });
     jQuery("a[id^='remove_']").live("click",function(){
//alert("in");
   var arr = jQuery(this).attr("id").split("_");
   //alert(arr);
    var cid= arr[1];
       
         //jQuery("#dis_"+cid).remove();
           // jQuery("#remove_"+cid).remove();
           
           jQuery("#"+cid).remove();
            var weekname= arr[1].toLowerCase();
            jQuery("#"+weekname+"hdn").val("0");
            jQuery("#"+weekname+"hdn").attr("from","");
            jQuery("#"+weekname+"hdn").attr("to","");
            jQuery("#"+weekname+"span").show();
            jQuery("#"+weekname).removeAttr("checked");
            jQuery(".weeknamehdn").each(function(index){
               var arr_val=jQuery(this).val().split("-");
               
               if(jQuery(this).val() == "" || arr_val[0] == "0" )
                   {
                       
                       jQuery("#addhoursid").attr("disabled", false);
                       jQuery("#addhoursid").removeClass( "disabled" );
                       jQuery("#addhoursid").css("color","#0066FF !important");
                   }
                   
                   
            });
           
                   if(jQuery(".removeclass").length == "0")
                    {
                        jQuery(".hoursdata").hide();
                    }
                else
                    {
                        jQuery(".hoursdata").show();
                    
                    }
   
    });
    jQuery(".weelclass").change(function(){
        from(); 
    });
     jQuery("#defaultValueFrom").blur(function(){
        from();
    });
    jQuery("#defaultValueTo").blur(function(){
        from();
    });
    jQuery("#defaultValueFrom").change(function(){
        from();
    })
    jQuery("#defaultValueTo").change(function(){
        from();
    });
    function from()
    {
        
        var from=jQuery("#defaultValueFrom").val();
        var to=jQuery("#defaultValueTo").val();
        var dateReg = /^(1[012]|[1-9]):[0-5][0-9](\\s)?(am|pm)+$/;
         var flag_textbox="true";   
        if(from == "" || to=="")
        {
            //alert("nathi avtu");
            //$("#addhourssaveid").attr("disabled", "disabled"); 
            flag_textbox="false"; 
        }
        else
        {
            
             if(!dateReg.test(from) || !dateReg.test(to)) {
                //$("#addhourssaveid").attr("disabled", "disabled"); 
                flag_textbox="false"; 
             }
             else
                 {
                    flag_textbox="true"; 
                 }
                 
                 
        }
        var flag_checkbox="true";
        jQuery(".weelclass").each(function(index){
            var totalchecked=jQuery('input[class=weelclass]:checked').size();
            
                if(totalchecked == 0)
                {
                   //$("#addhourssaveid").attr("disabled", "disabled"); 
                   flag_checkbox="false";
                }
                else
                {
                    //$("#addhourssaveid").removeAttr("disabled");
                    flag_checkbox="true";
                }
           
        });
         if(flag_textbox == "false"  || flag_checkbox == "false")
            {
                jQuery("#addhourssaveid").attr("disabled", "disabled"); 
                jQuery("#addhourssaveid").addClass("disabled");
                jQuery("#addhourssaveid").css("color","#ABABAB !important");
            }
            else
            {
                jQuery("#addhourssaveid").removeAttr("disabled");
                 jQuery("#addhourssaveid").removeClass( "disabled" );
                jQuery("#addhourssaveid").css("color","#0066FF !important");
            }
    }
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}

jQuery('#first_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc1").val(cat_first_id);
	jQuery("#hdnlcat1").val(jQuery("#first_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=1&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#first_cat_second_level").remove();
			jQuery("#first_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#first_cat_first_level").after(obj.html);
				bind_change_event();
			}
			
		}
    });
});

jQuery('#second_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc2").val(cat_first_id);
	jQuery("#hdnlcat2").val(jQuery("#second_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=2&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#second_cat_second_level").remove();
			jQuery("#second_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#second_cat_first_level").after(obj.html);
				
				jQuery("#second_cat_second_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
				
				bind_change_event();
			}
			
		}
    });
});

jQuery('#third_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc3").val(cat_first_id);
	jQuery("#hdnlcat3").val(jQuery("#third_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=3&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#third_cat_second_level").remove();
			jQuery("#third_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#third_cat_first_level").after(obj.html);
				
				jQuery("#third_cat_second_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
				jQuery("#third_cat_second_level option[value='"+jQuery("#hdnlc2").val()+"']").remove();

				bind_change_event();
			}
			
		}
    });
});

function bind_change_event()
{

	jQuery('#first_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc1").val(cat_second_id);
		jQuery("#hdnlcat1").val(jQuery("#first_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=1&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#first_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#first_cat_second_level").after(obj.html);
					//bind_change_event();
				}
				else
				{
					jQuery("#first_cat_second_level").next().html("");
					jQuery("#first_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
					jQuery("#first_lc_delete").css("display","block");
					jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
					jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
					
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#first_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc1").val(cat_third_id);
		jQuery("#hdnlcat1").val(jQuery("#first_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#first_cat_third_level").next().html("");
		jQuery("#first_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
		jQuery("#first_lc_delete").css("display","block");
		jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
		jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
	});

	jQuery('#second_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc2").val(cat_second_id);
		jQuery("#hdnlcat2").val(jQuery("#second_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=2&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#second_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#second_cat_second_level").after(obj.html);
					
					jQuery("#second_cat_third_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
					
					//bind_change_event();
				}
				else
				{
					jQuery("#second_cat_second_level").next().html("");
					jQuery("#second_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
					if(jQuery("#add_cat").attr("total")!=3)
					{
						jQuery("#add_cat_tr").css("display","block");
					}
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#second_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc2").val(cat_third_id);
		jQuery("#hdnlcat2").val(jQuery("#second_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#second_cat_third_level").next().html("");
		jQuery("#second_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
		if(jQuery("#add_cat").attr("total")!=3)
		{
			jQuery("#add_cat_tr").css("display","block");
		}
	});

	jQuery('#third_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc3").val(cat_second_id);
		jQuery("#hdnlcat3").val(jQuery("#third_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=3&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#third_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#third_cat_second_level").after(obj.html);
					
					jQuery("#third_cat_third_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
					jQuery("#third_cat_third_level option[value='"+jQuery("#hdnlc2").val()+"']").remove();
					
					//bind_change_event();
				}
				else
				{
					jQuery("#third_cat_second_level").next().html("");
					jQuery("#third_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
					if(jQuery("#add_cat").attr("total")!=3)
					{
						jQuery("#add_cat_tr").css("display","inline-block");
					}
					
					//jQuery("#third_lc").css("display","none");
					jQuery("#loc_cat_3").css("display","none");
					jQuery("#third_lc_delete").css("display","block");
					jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
					jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
					
					if(jQuery("#hdnlc1").val()!="" && jQuery("#hdnlc2").val()!="" && jQuery("#hdnlc3").val()!="")
                                        {
                                            jQuery("#add_cat_tr").css("display","none");
                                        }
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#third_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	    var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc3").val(cat_third_id);
		jQuery("#hdnlcat3").val(jQuery("#third_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#third_cat_third_level").next().html("");
		jQuery("#third_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
		if(jQuery("#add_cat").attr("total")!=3)
		{
			jQuery("#add_cat_tr").css("display","block");
		}
		
		//jQuery("#third_lc").css("display","none");
		jQuery("#loc_cat_3").css("display","none");
		jQuery("#third_lc_delete").css("display","block");
		jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
		jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
		
                if(jQuery("#hdnlc1").val()!="" && jQuery("#hdnlc2").val()!="" && jQuery("#hdnlc3").val()!="")
                {
                    jQuery("#add_cat_tr").css("display","none");
                }
	});
	
}
jQuery("#add_cat").live("click",function(){
	var total=parseInt(jQuery(this).attr("total"));

	if(total==1)
	{
		
		
		if(jQuery("#hdnlcat1").val()=="" && jQuery("#hdnlcat3").val()!="")
		{
			//jQuery("#loc_cat_1").css("display","table-row");
			jQuery("#first_lc").css("display","block");
			
			jQuery("#second_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat2").val()=="")
		{
			jQuery("#second_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat3").val()=="")
		{
			jQuery("#third_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#second_lc").css("display","none");
		}
	}
	else if(total==2)
	{
		//jQuery("#second_lc").css("display","none");
		jQuery("#loc_cat_2").css("display","none")
		
		if(jQuery("#hdnlcat1").val()=="" && jQuery("#hdnlcat3").val()!="")
		{
			//jQuery("#loc_cat_1").css("display","table-row");
			jQuery("#first_lc").css("display","block");
			jQuery("#second_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat2").val()=="")
		{
			jQuery("#second_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat3").val()=="")
		{
			jQuery("#third_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#second_lc").css("display","none");
		}
	}
	if(jQuery("#hdnlcat1").val()!="")
	{
		jQuery("#first_lc_delete").css("display","block");
		jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
		jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
	}
	if(jQuery("#hdnlcat2").val()!="")
	{
		jQuery("#second_lc_delete").css("display","block");
		jQuery("#second_selected_cat").text(jQuery("#hdnlcat2").val());
		jQuery("#second_selected_cat_delete").attr("catid",jQuery("#hdnlc2").val());
	}
	if(jQuery("#hdnlcat3").val()!="")
	{
		jQuery("#third_lc_delete").css("display","block");
		jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
		jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
	}
	jQuery(this).attr("total",total+1);
	jQuery("#add_cat_tr").css("display","none");
});
jQuery("#first_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc1").val("");
	jQuery("#hdnlcat1").val("");
	jQuery("#first_lc_delete").css("display","none");
	/*
	jQuery("#first_lc").css("display","block");
	jQuery("#second_lc").css("display","none");
	jQuery("#third_lc").css("display","none");
	*/
	jQuery("#first_cat_second_level").remove();
	jQuery("#first_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="" )
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");		
	}
	
});
jQuery("#second_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc2").val("");
	jQuery("#hdnlcat2").val("");
	jQuery("#second_lc_delete").css("display","none");
	/*
	jQuery("#second_lc").css("display","block");
	jQuery("#first_lc").css("display","none");
	jQuery("#third_lc").css("display","none");
	*/
	jQuery("#second_cat_second_level").remove();
	jQuery("#second_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="")
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");
	}
});
jQuery("#third_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc3").val("");
	jQuery("#hdnlcat3").val("");
	jQuery("#third_lc_delete").css("display","none");
	/*
	jQuery("#third_lc").css("display","block");
	jQuery("#first_lc").css("display","none");
	jQuery("#second_lc").css("display","none");
	*/
	jQuery("#third_cat_second_level").remove();
	jQuery("#third_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="")
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");
	}
});	
jQuery("#import_from_website").click(function(){
	jQuery("#upload_business_logo").css("display","none");
	jQuery("#import_iframe").css("display","block");
});
function submitImportform(obj)
{	
	//alert("in call submit function");
	//jQuery('#url-form').submit();
	//jQuery('#url-form').submit(getImagesFromUrl);
	//getImagesFromUrl();
	//alert("out call submit function");
}
jQuery("#btnBacktochangepassword").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#change_password").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","block");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="change_password";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
									
});
jQuery("#btnBacktobusinesspage").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#business_page").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","block");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="business_page";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktoupdateprofile").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#update_profile").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","block");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="update_profile";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktoaboutus").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#about_us").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","block");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="about_us";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktobusinesslogo").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#business_logo").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","block");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="business_logo";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
	
});
jQuery("#btnBacktobusinesslogo").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#business_logo").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","block");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="business_logo";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktoaddlocation").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#add_location").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","block");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="add_location";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktolocationhour").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#location_hour").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","block");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="add_location";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktolocationcategory").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#location_category").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","block");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="add_location";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktolocationimage").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#location_image").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","block");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="add_location";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery(document).ready(function(){
		
	//alert("hi");
	var tab = qs["tab"];
	//alert(tab);
	/*
	if(tab=="change_password")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#change_password").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","block");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");

	}
	else if(tab=="business_page")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#business_page").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","block");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
		
	}
	else if(tab=="update_profile")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#update_profile").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","block");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
		
	}
	else if(tab=="about_us")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#about_us").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","block");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
			
	}
	else if(tab=="business_logo")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#business_logo").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","block");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
			
	}
	else if(tab=="add_location")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#add_location").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","block");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");

	}
	*/
	
	var new_url=window.location;
	var new_url = new String(new_url);
	//alert(new_url);
	new_url=new_url.split("?");
	//alert(new_url[0]);
	history.pushState('', '', new_url[0]);
	
	if(typeof tab === 'undefined')
	{
		tab="change_password";
	}
	
	if(tab=="change_password")
	{
		jQuery("#logout_ele").css("display","none");
	}
	jQuery("#logout_ele").attr("href",jQuery("#logout_ele").attr("href")+ "?last_tab="+tab);
});
var qs = (function(a) {
    if (a == "") return {};
    var b = {};
    for (var i = 0; i < a.length; ++i)
    {
        var p=a[i].split('=');
        if (p.length != 2) continue;
        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
    return b;
})(window.location.search.substr(1).split('&'));
jQuery('.notification_tooltip').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
});
</script>

<?php
}
else
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ScanFlip | Merchant Setup</title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/fancybox/jquery.fancybox.css" media="screen" />

</head>
<style>
                form input[type=url] 
                {
                        width: 300px
                }
                #output img
                {
                        border: 1px solid #ccc;
                        padding: 10px;
                        margin: 10px;
                        display: inline-block;
                }


    h3 {
    color: black !important;
	font-size:14px !important;
    font-weight: lighter;
	background-color:hsl(0, 0%, 93%) !important;
	border:none !important;
}
#tooltip{
	z-index:9999 !important;
	opacity:1.0 !important;
}
#result_new
{
    margin-left: 5px;
    font-weight: bold;
	width:150px;
	float:left;
}
#result_con_new
{
    margin-left: 5px;
    font-weight: bold;
	width:150px;
	float:left;
}
.short
{
    color:#FF0000;
    font-weight: bold;
}
.weak
{
    color:#E66C2C;
    font-weight: bold;
}
.good
{
    color:#2D98F3;
    font-weight: bold;
}
.strong
{
    color:#006400;
    font-weight: bold;
}
        </style>
<body>
<div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div style="width:100%;text-align:center;">
	

<!--<script src="<?=WEB_PATH?>/admin/js/jquery.js"></script>-->



<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery-1.7.2.min.js"></script>-->
 <script type="text/javascript" src="<?=ASSETS_JS?>/m/fancybox/jquery.fancybox-buttons.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/fancybox/jquery.fancybox.js"></script>

<script type="text/javascript" src="<?=ASSETS?>/tinymce/tiny_mce.js"></script>

<!--- tooltip css --->
<script type="text/javascript" src="<?=ASSETS_JS?>/m/bootstrap.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/bootstrap.min.js"></script>
<!--- tooltip css --->


<script type="text/javascript" src="<?=ASSETS_JS?>/m/old_pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/new_pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/con_new_pass_strength.js"></script>

<script type="text/javascript" >
tinyMCE.init({
		// General options
		//mode : "textareas",
		mode : "exact",
		elements:"aboutus,aboutus_short",
		theme : "advanced",
		//plugins : "lists,searchreplace",
		valid_elements :'p,br',
		// Theme options
		//theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		
		//theme_advanced_buttons1 : "replace,|,bullist,numlist,|,outdent,indent,blockquote,|,sub,sup,|,charmap",
		theme_advanced_buttons1 : "",
		
		//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
               
		// Example content CSS (should be your site CSS)
		content_css : "<?=ASSETS?>/tinymce/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "<?=ASSETS?>/tinymce/lists/template_list.js",
		external_link_list_url : "<?=ASSETS?>/tinymce/lists/link_list.js",
		external_image_list_url : "<?=ASSETS?>/tinymce/lists/image_list.js",
		media_external_list_url : "<?=ASSETS?>/tinymce/lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		},
                charLimit:155,
		setup : function(ed) {
        //peform this action every time a key is pressed
        ed.onKeyDown.add(function(ed, e) {
		if(tinyMCE.activeEditor.id=="aboutus_short")
                {
			var textarea = tinyMCE.activeEditor.getContent(); 
			//alert(textarea);
			var lastcontent=textarea;
			//define local variables
			var tinymax, tinylen, htmlcount;
			//manually setting our max character limit
			tinymax = ed.settings.charLimit;
			//grabbing the length of the curent editors content
			tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
			//setting up the text string that will display in the path area
			//htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
			//if the user has exceeded the max turn the path bar red.
			//alert(tinylen);
			//alert(e.keyCode);
			if((tinylen+1>tinymax && e.keyCode == 37) || (tinylen+1>tinymax && e.keyCode == 38) || (tinylen+1>tinymax && e.keyCode == 39) || (tinylen+1>tinymax && e.keyCode == 40))
			 {
				return true;
			 }
			if (tinylen+1>tinymax && e.keyCode != 8)
			{
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
			
		}		
        });
		ed.onKeyUp.add(function(ed, e) {
		
			//alert("up");
			var textarea = tinyMCE.activeEditor.getContent(); 
			//alert(textarea);
			var lastcontent=textarea;
			//define local variables
			var tinymax, tinylen, htmlcount;
			//manually setting our max character limit
			tinymax = ed.settings.charLimit;
			//grabbing the length of the curent editors content
			tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
			//setting up the text string that will display in the path area
			//htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
			
			var l=tinymax-tinylen;
                        
                        if(tinyMCE.activeEditor.id=="aboutus_short")
                            document.getElementById("abt_us_remaining").innerHTML=l+" characters remaining";
                       
		});
		}
	});
</script>
<?php
//if($data_sub_merchant_id['merchant_parent'] == "0")
//{
?>
	<script type="text/javascript" language="javascript" src="<?=ASSETS_JS?>/m/jquery.carouFredSel-6.2.1-packed.js"></script>
	 <script type="text/javascript" src="<?=ASSETS_JS?>/m/jquery.timepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/jquery.timepicker.css" />
    <script language="javascript" src="<?=ASSETS_JS?>/m/ajaxupload.3.5.js" ></script>
<?php 
//}
?>
<!---start header---->
	<div>
		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	
    <div style="margin-left:auto;margin-right:auto;" id="fadeshow11">

	<!--end of slide--></div>
	<div id="content">
	
		<form id="profileform" name="profileform" action="process.php" method="post" enctype="multipart/form-data">
		
		<input type="hidden" name="hdn_web_path" id="hdn_web_path" value="<?php echo WEB_PATH ?>/merchant/process.php" />
 
		 <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?=$RS->fields['merchant_icon']?>" />
		 <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
		 
		 <input type="hidden" name="hdn_image_path_l" id="hdn_image_path_l" value="" />
         <input type="hidden" name="hdn_image_id_l" id="hdn_image_id_l" value="" />
		
		<input type="hidden" name="hdnlc1" id="hdnlc1" value="" />
		<input type="hidden" name="hdnlcat1" id="hdnlcat1" value="" />
		
		<input type="hidden" name="hdnlc2" id="hdnlc2" value="" />
		<input type="hidden" name="hdnlcat2" id="hdnlcat2" value="" />
		
		<input type="hidden" name="hdnlc3" id="hdnlc3" value="" />
		<input type="hidden" name="hdnlcat3" id="hdnlcat3" value="" />
		
		<input type="hidden" name="hdnProcessMerchant" id="hdnProcessMerchant" value="btnAddLocationimage" />
		
		
				
		<div id="nax_wizard_dialog" class="mvl ptm uiInterstitial uiInterstitialLarge uiBoxWhite">
	
			<div class="uiHeader uiHeaderBottomBorder mhl mts uiHeaderPage interstitialHeader">
				<div class="clearfix uiHeaderTop">
					<div class="accountsetupdiv">
						<h2 aria-hidden="true" class="uiHeaderTitle">
							<?php echo $merchant_msg["profile"]["Field_setup"];?>
						</h2>
					</div>
					<div class="cancelsetupdiv">
						<input type="button" style="cursor:pointer;" class="btnCancelsetup" name="btnCancelsetup" value="Cancel Account Setup" id="btnCancelsetup" >
					</div>
				</div>
			</div>
			
			<div class="phl ptm uiInterstitialContent">
	
				<div class="uiStepList uiStepListSingleLine uiStepListSingleLineWhite">
					<ol>
						<li id="change_password" class="uiStep <?php if($tab=='' || $tab=='change_password') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">1
										</span> Set Account ID
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="business_page" class="uiStep <?php if($tab=='business_page') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">2
										</span> Facebook Business Page
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="update_profile" class="uiStep <?php if($tab=='update_profile') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">3
										</span> Update Profile
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="about_us" class="uiStep <?php if($tab=='about_us') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">4
										</span> About Us
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="business_logo" class="uiStep <?php if($tab=='business_logo') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">5
										</span> Business Logo
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="add_location" class="uiStep <?php if($tab=='add_location') echo 'uiStepFirst uiStepSelected'?>">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">6
										</span> Add Location
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="location_hour" class="uiStep">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">7
										</span> Location Hours
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<li id="location_category" class="uiStep uiStepLast">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">8
										</span> Location Category
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						<!--
						<li id="location_image" class="uiStep">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">9
										</span> Location Profile Image
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						-->
						<!--
						<li id="location_additional_image" class="uiStep uiStepLast">
							<div class="part back">
							</div>
							<div class="part middle">
								<div class="content">
									<span class="title fsm">
										<span class="fwb">10
										</span> Location Additional Images
									</span>
								</div>
							</div>
							<div class="part point">
							</div>
						</li>
						-->
					</ol>
				</div>
				
				<div class="mtm ptm uiBoxWhite topborder update_merchant_process" style="display: block;">
						
					<div class="complete_change_password" style="display:<?php if($tab=='' || $tab=='change_password'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
							<!--
							<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>
								Change Password
							</div>
							-->
							
								 
								<table style="padding: 10px;width: 100%;">
									<tr>
										<td>
											<?php echo $merchant_msg["setup"]["new_password"];?>
										</td>
										<td>
											<input type="password" style="width: 150px;float:left;" id="new_password" name="new_password">
											 <span id="result_new"></span>
										</td>
									</tr>									
									<tr>
										<td>
											<?php echo $merchant_msg["setup"]["con_new_password"];?>
										</td>
										<td>
											<input type="password" style="width: 150px;float:left;" id="con_new_password" name="con_new_password">
											<span id="result_con_new"></span>
										</td>
									</tr>
									<tr>
										<td align="right"> </td>
										<td align="left">
											<div style="float:left;"><input type="checkbox" name="agree" id="agree" style="margin:0px;" /></div>
											<div style="float:left;width:218px;margin-left:5px;">I agree to the Scanflip <a href="<?=WEB_PATH?>/merchant/terms.php" target="_blank">Terms of Service</a> and <a href="<?=WEB_PATH?>/merchant/privacy-assist.php" target="_blank">Privacy Policy.</a></div>                
										</td>
									</tr>			
								</table>
								<table style="width: 20%; float: right;">
									<tr>
										
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnUpdatePassword_f" value="Next" id="btnUpdatePassword_f" class="disabled" disabled >
										</td>
									</tr>
								</table>
								
						</div>
						
					</div>	
					<div class="complete_business_page" style="display:<?php if($tab=='business_page'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
								 
								<table style="padding: 10px;width: 100%;line-height:1.35;">									
									<tr>
										<td width="25%">
											<?php echo "Enter Facebook Page Web Address : " ?>
										</td>
										<td>
											<input type="text" style="width: 300px;float:left;margin-top:16px;" id="businesspageurl" name="businesspageurl">
											 <img src="<?=ASSETS_IMG?>/m/001.png" class="loctimezonedivimg" alt="" />
											  <div class="loctimezonediv">
												<?php echo $merchant_msg["addlocation"]["businesspage_tooltip"];?>
											  </div>
										</td>
									</tr> 		
								</table>
								<table style="width: 20%; float: right;">
									<tr>
										<td >
											<input type="button" style="cursor:pointer;" name="btnBacktochangepassword" value="Back" id="btnBacktochangepassword" >
										</td>
										<td >
											<input type="button" style="cursor:pointer;" class="btnSkip" name="btnSkip" value="Skip" id="btnSkip" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btncallbusinesspage" value="Next" id="btncallbusinesspage" >
										</td>
									</tr>
								</table>
								
						</div>
						
					</div>

					<div class="complete_update_profile" style="display:<?php if($tab=='update_profile'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
							<!--
							<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>
								Update Profile Detail
							</div>
							-->
								 
								<table style="padding: 10px;width: 100%;">
									<tr>
										<td width="40%"><?php echo $merchant_msg['profile']['Field_first_name']; ?></td>
										<td width="60%">
										<input type="text" name="firstname" id="firstname" style="width:200px; " value="<?=$RS->fields['firstname']?>">
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_last_name']; ?></td>
										<td><input type="text" name="lastname" id="lastname" style="width:200px; " value="<?=$RS->fields['lastname']?>"></td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_address']; ?></td>
										<td><input type="text" name="address" id="address" style="width:200px; " value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['address']; }else{ echo $row_data_result['address'];}?>"></td>
									  </tr>
									  
									  
									   <tr>
										<td><?php echo $merchant_msg['profile']['Field_country']; ?></td>
										<td>
										<!--<input type="text" name="country" id="country" style="width:200px; " value="<?=$RS->fields['country']?>">-->
										<?php
											//echo $RS->fields['country']."sharad";
											//echo $row_data_result['country'];
										 ?>
										<select name="country" id="country">
											<option value="0" >Please Select</option>
											<?php
											$array_where = array();
											$array_where['active'] = 1;
											$RS_country = $objDB->Show("country",$array_where," Order By `name` ASC ");
											if($RS_country>0)
											{
												while($Row = $RS_country->FetchRow())
												{
												?>
												<option value="<?php echo $Row['id'] ?>" ><?php echo $Row['name'] ?></option>
												<?php
												}
											}
											?>
										</select>
										
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_state']; ?></td>
										<td>
										<!--<input type="text" name="state" id="state" style="width:200px; " value="<?=$RS->fields['state']?>">-->
										
											<select name="state" id="state" class="" style="display:block">
												<option value='0'>Please Select</option>
											
												</select>
										
										
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_city']; ?></td>
										<td>
											<select name="city" id="city">
												<option value='0'>Please Select</option>
											
												</select>
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_zipcode']; ?></td>
										<td><input type="text" name="zipcode" id="zipcode" style="width:200px; " value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['zipcode']; }else{ echo $row_data_result['zipcode'];}?>"></td>
									  </tr>
									 
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_phone_no']; ?></td>
										<?php 
										$mobileno=$RS->fields['phone_number'];
											$area_code=substr($mobileno,4,3);
											 $mobileno2=substr($mobileno,8,3);
									 $mobileno1=substr($mobileno,12,4);  
										   //$mobileno1=substr($mobileno,8,4);
										   ?>
										<td><!--<input type="text" name="phone_number" id="phone_number" style="width:200px; " value="<?=$RS->fields['phone_number']?>">-->
										<select name="mobile_country_code" id="mobile_country_code" style="display:none;">
											<option value="001">001</option>
										</select>
											<input type="text" name="mobileno_area_code" id="mobileno_area_code" style="width:30px; " value="<?php echo $area_code;?>" maxlength="3">-
										<input type="text" name="mobileno2" id="mobileno2" style="width:30px; " value="<?php echo $mobileno2;?>" maxlength="3">-
										<input type="text" name="mobileno" id="mobileno" style="width:40px; " value="<?php echo $mobileno1;?>" maxlength="4">
										
										</td>
									  </tr>
									  <?php 
										if($_SESSION['merchant_info']['merchant_parent'] == 0 )
										{
										?>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_business_name']; ?></td>
										<td><input type="text" name="business" id="business" style="width:200px; " value="<?=$RS->fields['business']?>"></td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_business_tag']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_business_tags"]; ?>">&nbsp;&nbsp;&nbsp;</span></td></td>
										<td>
										<br/>
										<!--<input type="text" name="business_tags" id="business_tags" style="width:200px; " value="<?=$RS->fields['business_tags']?>">-->
										<textarea name="business_tags" id="business_tags" style="width:200px; " ><?php echo $RS->fields['business_tags']?></textarea>
										<br/><?php  echo  $merchant_msg["profile"]["Field_business_tag_add_upto"]; ?>
										</td>
									  </tr>
									  <?php
									   }
									   ?>
									   		
								</table>
								<table style="width: 20%; float: right;">
								
								
									<tr>
										
										<td >
											<input type="button" style="cursor:pointer;" name="btnBacktobusinesspage" value="Back" id="btnBacktobusinesspage" >
										</td>
										
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnUpdateProfile" value="Next" id="btnUpdateProfile" >
										</td>
									</tr>
								</table>
						</div>
						
					</div>
					
					<div class="complete_about_us" style="display:<?php if($tab=='about_us'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<?php
									   if($_SESSION['merchant_info']['merchant_parent'] == 0 )
									   {
									  ?>
									   <tr>
										<td><?php echo $merchant_msg['profile']['Field_about_us_short']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_aboutus_short"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>
										<td>
											
											<!--<input type="text" name="aboutus" id="aboutus" style="width:200px; " value="<?=$RS->fields['aboutus']?>" onkeyup="changetext2()" maxlength="45"/>-->
											<textarea id="aboutus_short" name="aboutus_short" rows="5" cols="25" style="width:80%;" ><?=$RS->fields['aboutus_short']?></textarea>
											<span id="abt_us_remaining" class="abt_us_remaining" style="float:left;" >Maximum 155 characters</span>
										</td>
									  </tr>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_about_us']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_aboutus"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>
										<td>
											
											<!--<input type="text" name="aboutus" id="aboutus" style="width:200px; " value="<?=$RS->fields['aboutus']?>" onkeyup="changetext2()" maxlength="45"/>-->
											<textarea id="aboutus" name="aboutus" rows="5" cols="25" style="width:80%;" placeholder="*Add a description with basic info for <?php echo $RS->fields['business'] ?>"><?=$RS->fields['aboutus']?></textarea>
											<!--<span id="abt_us_remaining" class="abt_us_remaining" style="float:left;" >Maximum 600 characters</span>-->
										</td>
									  </tr>
									 
									  <?php
										}
									  ?>
									
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td >
											<input type="button" style="cursor:pointer;" name="btnBacktoupdateprofile" value="Back" id="btnBacktoupdateprofile" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnUpdateAboutus" value="Next" id="btnUpdateAboutus" >
										</td>
									</tr>
								</table>
						</div>
					</div>
					
					<div class="complete_business_logo" style="display:<?php if($tab=='business_logo'){echo 'block';}else{echo 'none';}?>;">
						
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<?php 
								/*
										if($_SESSION['merchant_info']['merchant_parent'] == 0 )
										{
										?>
									  <tr>
										<td><?php echo $merchant_msg['profile']['Field_merchant_icon']; ?></td>
										<td><div style="float: left;">
											<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
											<div id="upload" >
											<span  >Browse
											</span> 
											</div>
											</div>
										</td>
									  </tr>
									  
									  <tr><td align="right">&nbsp; </td>
											<td>

													<span id="status" ></span>
													<br/>

													<ul id="files" >

													 </ul>
											</td>
									  </tr>
									  <?php 
										}
										*/
										?>
									<tr class="_51mx">
										<td class="business_logo_left" style="width: 23%;vertical-align:top;">
											<div class="nax_profile_pic" id="nax_profile_pic">
												<img src="<?=ASSETS_IMG.'/m/uploadlogo.png'?>" alt="Merchant" id="profilePic" class="profilePic silhouette img">
											</div>
											<div style="margin-top:10px;">Add image at-least(w x h) 144px x 144px.</div>
										</td>
										<td class="business_logo_right">
											<div id="nax_upload_right">
													<div id="upload_business_logo" class="nax_image_uploader" style="display:block;">
														<div class="uploader">
															<div style="float: left;margin-left:15%;">
																<div id="upload" >
																	<span >Browse</span> 
																</div>
															</div>
														</div>
														<!--
														<ul id="nax_list" class="uiList _509- _4ki _6-h _703 _4ks">
															<li>
																<div class="uploader">
																	<div style="float: left;">
																		<div id="upload" class="new_css">
																			<span >Upload From Computer</span> 
																		</div>
																	</div>
																</div>
															</li>
															<li class="last">
																<div class="link">
																	<a id="import_from_website" href="javascript:void(0)" >
																		Import From Website
																	</a>
																</div>
															</li>
														</ul>
														-->
														<span id="status" style=" float: left;font-family: Arial;margin-top: 20px;padding: 5px;width: 50%;"></span>
													<br/>

													<ul id="files" style="display:none;">

													 </ul>
													</div>													
													
													
												
															
											</div>
										</td>
									</tr>	
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td >
											<input type="button" style="cursor:pointer;" name="btnBacktoaboutus" value="Back" id="btnBacktoaboutus" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnUpdateBusinesslogo" value="Next" id="btnUpdateBusinesslogo" >
										</td>
									</tr>
							</table>
						</div>
					</div>
					
					<div class="complete_add_location" style="display: <?php if($tab=='add_location'){echo 'block';}else{echo 'none';}?>;">
						<div style="width: 100%;text-align: left !important">								 
							<table style="padding: 10px;width: 100%;">	
								<tr style="display:none;">
									<td width="20%" align="right"><?php echo $merchant_msg["addlocation"]["Field_location_name"];?></td>
									<td width="80%" align="left">
										<input type="text" name="location_name" id="location_name" value="<?php echo $RS->fields['business']; ?>"/>
										&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chk_is_primary" id="chk_is_primary" value="0"/><?php echo $merchant_msg["addlocation"]["Field_primary_location"];?> <span class="notification_tooltip" title="<?php echo $merchant_msg["addlocation"]["Tooltip_Loction_Primary"]; ?>" >&nbsp;&nbsp;&nbsp</span>
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_address"];?></td>
									<td align="left">
										<input type="text" name="address_l" id="address_l" value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['address']; }else{ echo $row_data_result['address'];}?>">
									</td>
								</tr>
								<tr style="display:none;">
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_country"];?> </td>
									<td align="left">
										<select name="country_l" id="country_l">
											<option value="USA" <?php if($data_sub_merchant_id['merchant_parent'] == "0")
														   {
															if($RS->fields['country'] == "USA")
															{
																echo "selected";
															}
														   }
														   else
														   {
														  if($row_data_result['country'] == "USA")
														  {
															echo "selected";
														  }
														
																		   }?>>USA</option>
											<option value="Canada" <?php if($data_sub_merchant_id['merchant_parent'] == "0")
														   {
															if($RS->fields['country'] == "Canada")
															{
																echo "selected";
															}
														   }
														   else
														   {
														  if($row_data_result['country'] == "Canada")
														  {
															echo "selected";
														  }
														
														   }?> >Canada</option>
										</select>
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_state"];?></td>
									<td align="left">
										<select name="state_l" id="state_l" class="" style="display:block">
											<option value='0'>Please Select</option>											
										</select>
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_city"];?></td>
									<td align="left">
										<select name="city_l" id="city_l" class="" style="display:block">
											<option value='0'>Please Select</option>
										</select>
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_zipcode"];?></td>
									<td align="left">
										<input type="text" name="zip_l" id="zip_l" value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['zipcode']; }else{ echo $row_data_result['zipcode'];}?>">
									</td>
								</tr>
				  
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_website"];?></td>
									<td align="left">
										<input type="text" name="website" id="website" />
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_facebook"];?></td>
									<td align="left">
										<input type="text" name="facebook" id="facebook" />
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_google"];?></td>
									<td align="left">
										<input type="text" name="google" id="google" />
									</td>
								</tr>

								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_email"];?></td>
									<td align="left">
										<input type="text" name="email" id="email" />
									</td>
								</tr>
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_phone_number"];?></td>
									<td align="left">

										<select name="mobile_country_code_l" id="mobile_country_code_l" style="display:none">
											<option value="001">001</option>
										</select>
										<input type="text" name="mobileno_area_code_l" id="mobileno_area_code_l" style="width:30px; " value="<?php echo $area_code;?>" maxlength="3">-
										<input type="text" name="mobileno2_l" id="mobileno2_l" style="width:30px; " value="<?php echo $mobileno2;?>" maxlength="3">-
										<input type="text" name="mobileno_l" id="mobileno_l" style="width:40px; " value="<?php echo $mobileno1;?>" maxlength="4">
									</td>
								</tr>
								 <tr>
									<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_price_range"];?></td>
									<td align="left">
										<select name="pricerange" id="pricerange">        
												 <option value="0" >Unspecified</option>
												 <option value="1" >$ (Under $10)</option>
												<option value="2" >$$ ($11 - $30)</option>
												<option value="3" >$$$ ($31 - $60)</option>
												<option value="4" >$$$$ (Above $61)</option>		
										 </select>
									</td>
								</tr>
								<tr>
									<td align="right">
									<?php echo $merchant_msg["addlocationdetail"]["Field_parking"];?>
									<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_parking"]; ?>" >&nbsp;&nbsp;&nbsp</span>
									</td>
									<td align="left">
										<input type="checkbox" name="chk_parking[]" value="Garage" id="Garage" /><?php echo "Garage" ?>
										<input type="checkbox" name="chk_parking[]" value="Lot" id="Lot" /><?php echo "Lot" ?>
										<input type="checkbox" name="chk_parking[]" value="Street" id="Street" /><?php echo "Street" ?>
										<input type="checkbox" name="chk_parking[]" value="Valet" id="Valet" /><?php echo "Valet" ?>
									</td>
								</tr>
								<!--
								<tr>
									<td align="right"><?php echo $merchant_msg["addlocation"]["Field_time_zone"];?></td>
									<td align="left">
										<select readonly name='timezone' id='timezone' style='width:320px;float:left;margin-right:5px;'>
										<?php
										foreach($timezoness as $key=>$value){
										?>
											<option value='<?php echo $key ?>'  ><?php echo $value; ?></option>
										<?php
										}
										?>
										</select>
										<img src="<?=WEB_PATH?>/merchant/templates/images/001.png" class="loctimezonedivimg" alt="" />
                                        <div class="loctimezonediv">
											<?php echo $merchant_msg["addlocation"]["time_zone_tooltip"];?>
										</div>  
										<div  id='helptext'></div>  
                                        <input type="hidden" name="time_zone_v" id="time_zone_v" value=""/>
										<script type='text/javascript'>TORBIT.dom={get:function(a){return document.getElementsByTagName(a)},gh:function(){return TORBIT.dom.get("head")[0]},ah:function(a){TORBIT.dom.gh().appendChild(a)},ce:function(a){return document.createElement(a)},gei:function(a){return document.getElementById(a)},ls:function(a,b){var c=TORBIT.dom.ce("script");c.type="text/javascript";c.src=a;if("function"==typeof b){c.onload=function(){if(!c.onloadDone){c.onloadDone=true;b()}};c.onreadystatechange=function(){if(("loaded"===c.readyState||"complete"===c.readyState)&&!c.onloadDone){c.onloadDone=true;b()}}}TORBIT.dom.ah(c)}};(function(){var a=window.TORBIT.timing={};var b=function(){if(window.performance==void 0||window.performance.timing==void 0){k(e);h(f);return}h(d)};var c=function(){var b=window.performance.timing;var c=b.navigationStart;for(var d in b){var e=b[d];if(typeof e!="number"||e==0){continue}a[d]=e-c;var f=/(.+)End$/i.exec(d);if(f){a[f[1]+"Elapsed"]=b[d]-b[f[1]+"Start"]}}};var d=function(){c();g()};var e=function(){a.or=(new Date).getTime()-TORBIT.start_time};var f=function(){a.ol=(new Date).getTime()-TORBIT.start_time;g()};var g=function(){var b="/torbit-timing.php?";for(var c in a){b+=c+"="+a[c]+"&"}if(TORBIT.fv==1)b+="fv=1&";if(TORBIT.opt==0)b+="not_opt=1&";TORBIT.dom.ls(b)};var h=function(a){if(typeof window.onload!="function"){return window.onload=a}var b=window.onload;window.onload=function(){b();a()}};var i=false;var j=function(){};var k=function(a){j=l(a);m()};var l=function(a){return function(){if(!i){i=true;a()}}};var m=function(){if(document.addEventListener){document.addEventListener("DOMContentLoaded",j,false)}else if(document.attachEvent){document.attachEvent("onreadystatechange",j);var a=false;try{a=window.frameElement==null}catch(b){}if(document.documentElement.doScroll&&a){n()}}};var n=function(){if(i){return}try{document.documentElement.doScroll("left")}catch(a){setTimeout(n,5);return}j()};b()})();TORBIT.opt=0;TORBIT.fv=1;</script>
										<script type='text/javascript'>    
											jQuery('#timezone').change(function(){
												var text=  jQuery('#timezone :selected').val();    
												jQuery('#time_zone_v').val(text);
											});
										</script>
									 </td>	
								</tr>
								-->
								<!--
								<tr>
                                    <td align="right" style='width:26%'>
                                        <div ><?php echo $merchant_msg["addlocation"]["Field_manage_social_stream"];?> <span class="notification_tooltip" title="<?php echo $merchant_msg["addlocation"]["Tooltip_manage_social_stream"]; ?>" >&nbsp;&nbsp;&nbsp</span></div></td>
                                    <td align="left">                                         
										<table>
											<tr>
												<td>
													Facebook Page :
												</td>
												<td>
													<input type="radio" id="facebookyes" value="1"  checked="checked" name="facebookradio" /> Yes
													<input type="radio" id="facebookno" value="0" name="facebookradio"  /> No
												</td>
											</tr>
											<tr>
												<td>
													Google+ Page : 
												</td>
												<td>
													<input type="radio" id="googleyes" value="1" checked="checked" name="googleradio" /> Yes
													<input type="radio" id="googleno" value="0" name="googleradio"  /> No
												</td>
											</tr>
										</table>
									</td>
                                </tr>
								-->
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktobusinesslogo" value="Back" id="btnBacktobusinesslogo" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationProcess" value="Next" id="btnAddLocationProcess" >
										</td>
									</tr>
							</table>
						</div>
					</div>
					
					<div class="complete_location_hour" style="display: none">
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<tr>
									<td align="right" style="width: 30%;padding-top:10px;">Location Hours : </td>
									<td align="left">
										 <div class="hoursdata" style="display: none">	 
											<input type="hidden" id="addhourdata" weekname="" from="" to="" mon="" tue="" wed="" thu="" fri="" sat="" sun=""/>
											<input type="hidden" class="weeknamehdn" id="monhdn" value="" name="monhdn" />
											<input type="hidden" class="weeknamehdn" id="tuehdn" value="" name="tuehdn"/>
											<input type="hidden" class="weeknamehdn" id="wedhdn" value="" name="wedhdn"/>
											<input type="hidden" class="weeknamehdn" id="thuhdn" value="" name="thuhdn"/>
											<input type="hidden" class="weeknamehdn" id="frihdn" value="" name="frihdn"/>
											<input type="hidden" class="weeknamehdn" id="sathdn" value="" name="sathdn"/>
											<input type="hidden" class="weeknamehdn" id="sunhdn" value="" name="sunhdn"/>	
										 </div>
         
										 <div class="addhoursdiv">
											 <input type="button" id="addhoursid" value="Add hours" style="background-color:#F2F2F2;background-image:none;background-repeat: none;border:1px solid #DBDBDB; border-radius:5px;color:#0066FF;padding:3px 10px"/>
										</div>
         
										 <div class="timeclass" style="display: none">
											 <div>
											 <script>
										  jQuery(function() {
											jQuery('#defaultValueFrom').timepicker({ 'scrollDefaultNow': true });
														jQuery('#defaultValueTo').timepicker({ 'scrollDefaultNow': true });
										  });
										</script>
										From <input id="defaultValueFrom" name="from" type="text" class="time" style="width: 89px"/>
												To <input id="defaultValueTo" name="to" type="text" class="time" style="width: 89px" />
												
										 
											 </div>
											 <div style="margin-top:5px" id="weekdiv">
													<span id="monspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass"  id="mon" name="mon" value="Mon" />Mon</span>
													<span id="tuespan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="tue" name="tue" value="Tue"/>Tue</span>
													<span id="wedspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="wed" name="wed" value="Wed"/>Wed</span>
													<span id="thuspan" class="weekspan"><input type="checkbox"  from="" to="" class="weelclass" id="thu" name="thu" value="Thu"/>Thu</span>
													<span id="frispan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="fri" name="fri" value="Fri"/>Fri</span>
													<span id="satspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="sat" name="sat" value="Sat"/>Sat</span>
													<span id="sunspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="sun" name="sun" value="Sun"/>Sun</span>
											 </div>
										 <div style="margin-top:10px">
											<input type="button" id="addhourssaveid" value="<?php echo $merchant_msg['index']['btn_save'];?>"  style="padding:3px 10px;background-color:#F2F2F2;background-image:none;background-repeat: none;border:1px solid #DBDBDB; border-radius:5px;color:#0066FF;"/>
											<input type="button" id="addhourscancelid" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" style="padding:3px 10px;background-color:#F2F2F2;background-image:none;background-repeat: none;border:1px solid #DBDBDB; border-radius:5px;color:#0066FF;"/>
										 </div>
										 
										 </div>
         
         
         
         
									</td>
								</tr>
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktoaddlocation" value="Back" id="btnBacktoaddlocation" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationhour" value="Next" id="btnAddLocationhour" >
										</td>
									</tr>
							</table>
						</div>						
					</div>
					
					<div class="complete_location_category" style="display: none">
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<tr id="loc_cat_1">
									<td align="right" style="vertical-align:top;width:16%;">
									<?php echo $merchant_msg["addlocationdetail"]["Field_location_categories"];?>
									<div id="add_cat_tr" style="margin-top:5px;display:none"><a href="javascript:void(0);" id="add_cat" name="add_cat" total="1">Add another category</a></div>
									</td>
									<td align="left">
										<div id="first_lc" >
										<select name="first_cat_first_level" id="first_cat_first_level" size="9">
										<?php
											/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

											$RS_cat_first = $objDB->Conn->Execute($Sql);*/
														$RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

											if($RS_cat_first->RecordCount()>0)
											{
												while($Row_cat_first = $RS_cat_first->FetchRow())
												{
													/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
													$RS1=$objDB->Conn->Execute($Sql);*/
													$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));

													if($RS1->RecordCount()>0)
													{
													?>
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
													<?php
													}
													else
													{
													?>						
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
													<?php
													}
												}
											}	
										?>
										</select>
										</div>
										<div id="second_lc" style="display:none;">
										<select name="second_cat_first_level" id="second_cat_first_level" size="9">
										<?php
											/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

											$RS_cat_first = $objDB->Conn->Execute($Sql);*/
											 $RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

											if($RS_cat_first->RecordCount()>0)
											{
												while($Row_cat_first = $RS_cat_first->FetchRow())
												{
													/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
													$RS1=$objDB->Conn->Execute($Sql);*/
													$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));

													if($RS1->RecordCount()>0)
													{
													?>
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
													<?php
													}
													else
													{
													?>						
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
													<?php
													}
												}
											}	
										?>
										</select>
										</div>
										<div id="third_lc" style="display:none;">
										<select name="third_cat_first_level" id="third_cat_first_level" size="9">
										<?php
											/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

											$RS_cat_first = $objDB->Conn->Execute($Sql);*/
											$RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

											if($RS_cat_first->RecordCount()>0)
											{
												while($Row_cat_first = $RS_cat_first->FetchRow())
												{
													/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
													$RS1=$objDB->Conn->Execute($Sql);*/
													$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));

													if($RS1->RecordCount()>0)
													{
													?>
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
													<?php
													}
													else
													{
													?>						
														<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
													<?php
													}
												}
											}	
										?>
										</select>
										</div>						
									</td>
								  </tr>
								  
								  <tr >
									<td align="right">
										&nbsp;
										
									</td>
									<td align="left">
										<div id="first_lc_delete" class="lc_delete" style="display:none;">
											Selected Category : &nbsp;&nbsp; <span id="first_selected_cat"></span><span id="first_selected_cat_delete" class="selected_delete" catid=""></span>
										</div>
										<div id="second_lc_delete" class="lc_delete" style="display:none;">
											Selected Category : &nbsp;&nbsp; <span id="second_selected_cat"></span><span id="second_selected_cat_delete" class="selected_delete" catid=""></span>
										</div>
										<div id="third_lc_delete" class="lc_delete" style="display:none;">
											Selected Category : &nbsp;&nbsp; <span id="third_selected_cat"></span><span id="third_selected_cat_delete" class="selected_delete" catid=""></span>
										</div>
									</td>
								  </tr>
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktolocationhour" value="Back" id="btnBacktolocationhour" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationcategory" value="Finish" id="btnAddLocationcategory" onclick='validateForm()'>
										</td>
									</tr>
							</table>
						</div>
					</div>
							
					<div class="complete_location_image" style="display: none">
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
								<tr>
									<td align="right" style="width: 20%;"><?php echo $merchant_msg["addlocation"]["Field_picture"];?></td>
									<td align="left">
										<!-- start of  PAY-508-28033   -->
										<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
										<div style="float: left;">
													<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
													<div id="upload_l" >
													<span  >Browse
													</span> 
													</div>
													</div> <div style="float: left;padding-top: 7px;"> &nbsp;&nbsp;<span style="color:black;">
													<!-- Or select from </span><a class="mediaclass" style="cursor: pointer;color: #0066FF;font-weight: bold" > media library </a></div>--> 
									 <!-- <input type="file" name="business_logo" id="business_logo" />-->
									 <!-- end of  PAY-508-28033   -->
										
									</td>
								  </tr>
								  
								   <!-- T_7 -->
								  <tr><td align="right">&nbsp; </td>
									<td>
							
										<span id="status_l" ></span>
										<br/>
						   
										<ul id="files_l" >
						  
										 </ul>
									</td>
								  </tr>
								  
								  
												  
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktolocationcategory" value="Back" id="btnBacktolocationcategory" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationimage" value="Finish" id="btnAddLocationimage" onclick='validateForm()'/>
											<!--
											<input type="submit" style="cursor:pointer;" name="btnAddLocationimage" value="Save" id="btnAddLocationimage" >
											-->
										</td>
									</tr>
							</table>
						</div>
					</div>
					<div class="complete_location_additional_image" style="display: none">
						<div style="width: 100%;text-align: left !important">
							<table style="padding: 10px;width: 100%;">
							
									<tr>
													  <td align="right" style="width: 20%;"><?php echo $merchant_msg["addlocation"]["Field_add_additional_images"];?> </td>
													  <td align="left">
										<!-- start of  PAY-508-28033   -->
										<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
										<div style="float: left;">
													<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
													<div id="upload_more" style=" background: none repeat scroll 0 0 #F2F2F2;border: 1px solid #CCCCCC;border-radius: 5px 5px 5px 5px;color: #3366CC;cursor: pointer !important;font-family: Arial,Helvetica,sans-serif;font-size: 1.1em;font-weight: bold;height: 15px;padding: 6px;text-align: center;" >
													<span  >Browse
													</span> 
													</div>
																</div>
																<!--
																<div style="float: left;padding-top: 7px;"> &nbsp;&nbsp;<span style="color:black;">Or select from </span><a class="mediaclassmore" style="cursor: pointer;color: #0066FF;font-weight: bold" > media library </a></div>    
																-->
																<div style="float: left;padding-top: 7px;"> &nbsp;&nbsp;<span style="color:black;"></span></div>
																

									 <!-- <input type="file" name="business_logo" id="business_logo" />-->
									 <!-- end of  PAY-508-28033   -->
									</td>
												  </tr>
												  <tr><td align="right">&nbsp; </td>
									<td>
							
										<span id="status_more" style="color:red"></span> 
																<span id="uploading_msg_more" ></span> 
																<!--
																<div class="list_carousel" style="display:none" >
																	<ul id="files_more" style="">

																	</ul>
																	<div class="clearfix"></div>
																	<a id="prev2" class="prev" href="#"><img src="<?=ASSETS_IMG ?>/m/pre_add_campaign.png"></img></a>
																	<a id="next2" class="next" href="#"><img src="<?=ASSETS_IMG ?>/m/next_add_campaign.png"></img></a>
																</div>
																-->
																<div id="additional_images_id">
																	<ul id="files_more">
																	</ul>														
																</div>
																<div id="additional_images_id_uploaded">
																	
																 </div>
																 
									</td>
								  </tr>						  
								 
							</table>
							<table style="width: 20%; float: right;">
									<tr>
										
										<td>
											<input type="button" style="cursor:pointer;" name="btnBacktolocationimage" value="Back" id="btnBacktolocationimage" >
										</td>
										<td style="float:right;">
											<input type="button" style="cursor:pointer;" name="btnAddLocationadditionalimage" value="Finish" id="btnAddLocationadditionalimage" onclick='validateForm()'/>
											<!--
											<input type="submit" style="cursor:pointer;" name="btnAddLocationimage" value="Save" id="btnAddLocationimage" >
											-->
										</td>
									</tr>
							</table>
						</div>
					</div>
					
					
					<div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
						<div id="NotificationloaderBackDiv" class="divBack">
						</div>
						<div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">

							<div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
								 style="left: 45%;top: 40%;">

								<div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto">
									<img src="<?= ASSETS_IMG ?>/c/128.GIF" style="display: block;" id="image_loader_div"/>
								</div>
							</div>
						</div>
					</div>	
				</div>
	
			</div><!--phl ptm uiInterstitialContent-->
			
		</div><!--nax_wizard_dialog-->
		</form>
<!--end of content--></div>

					
<!--end of contentContainer--></div>
<!---------start footer--------------->
       <div>
  <?
  require_once(MRCH_LAYOUT."/footer.php");
  ?>
  <!--end of footer--></div>
	
</div>
  
</body>
</html>
<!--// 369 -->

<script type="text/javascript">
function close_popuploader(popup_name)
    {
		/*$("#" + popup_name + "FrontDivProcessing").css("display","none");
	$("#" + popup_name + "PopUpContainer").css("display","none");
	$("#" + popup_name + "BackDiv").css("display","none");*/
        jQuery("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
            jQuery("#" + popup_name + "BackDiv").fadeOut(200, function () {
                jQuery("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
                    jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
                    jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
                    jQuery("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
                });
            });
        });
	
    }
    function open_popuploader(popup_name)
    {

	jQuery("#" + popup_name + "FrontDivProcessing").css("display","block");
	jQuery("#" + popup_name + "PopUpContainer").css("display","block");
	jQuery("#" + popup_name + "BackDiv").css("display","block");
        /*$("#" + popup_name + "FrontDivProcessing").fadeIn(10, function () {
            $("#" + popup_name + "BackDiv").fadeIn(10, function () {
                $("#" + popup_name + "PopUpContainer").fadeIn(10, function () {         
	
                });
            });
        });*/
	
	
    }

jQuery(".btnCancelsetup").live("click",function(){
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=change_password',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				return false;	
			}
			else
			{						 		 
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				jQuery.ajax({
						  type:"POST",
						  url:'process.php',
						  data :'remove_setup=yes&merchant_id='+merchant_id,
						  async:false,
						  success:function(msg)
						  {
								window.location="<?php echo WEB_PATH?>/merchant/logout-register.php";
						  }
				});
			}
		}
	});
});	

jQuery('#agree').live("change",function() {
    //alert(jQuery(this).is(':checked'));
	if(jQuery(this).is(':checked'))
	{
		jQuery('#btnUpdatePassword_f').removeAttr("disabled");
		jQuery('#btnUpdatePassword_f').removeClass("disabled");
	}
	else
	{
		jQuery('#btnUpdatePassword_f').attr("disabled","");
		jQuery('#btnUpdatePassword_f').addClass("disabled");
	}
});
	
jQuery(".btnSkip").live("click",function(){
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=business_page',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				window.location.href=obj.link;
				return false;	
			}
			else
			{						 		 
				jQuery("li.uiStep").removeClass("uiStepSelected");
				jQuery("li#update_profile").addClass("uiStepSelected");
				
				jQuery(".complete_change_password").css("display","none");
				jQuery(".complete_business_page").css("display","none");
				jQuery(".complete_update_profile").css("display","block");
				jQuery(".complete_about_us").css("display","none");
				jQuery(".complete_business_logo").css("display","none");
				jQuery(".complete_add_location").css("display","none");
				jQuery(".complete_location_hour").css("display","none");
				jQuery(".complete_location_category").css("display","none");
				jQuery(".complete_location_image").css("display","none");
				jQuery(".complete_location_additional_image").css("display","none");
				
				var tab="update_profile";
				var logout_link=jQuery("#logout_ele").attr("href");
				logout_link1=logout_link.split("?"); 
				jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
			}
		}
	});
});		
jQuery("#btnUpdatePassword_f").live("click",function(){
	

	
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=change_password',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{						 
				jQuery("#logout_ele").css("display","block");
				
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var new_password=jQuery("#new_password").val();
				var con_new_password=jQuery("#con_new_password").val();			
				
				//alert(emailid);
				var flag="true";
				var msgbox="";

				if(flag=="false")
				{	 
					var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
					var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msgbox+"</div>";
					var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
					return false;
				}
				else
				{
					open_popuploader('Notificationloader');
					
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
					
					jQuery.ajax({
						  type:"POST",
						  url:'process.php',
						  data :'btnUpdatePasswordProcess=yes&merchant_id='+merchant_id+'&new_password='+new_password+'&con_new_password='+con_new_password+'&normal_register=1',
						  async:false,
						  success:function(msg)
						  {
								close_popuploader('Notificationloader');
								var obj = jQuery.parseJSON(msg);
								//alert(obj.status);
								//alert(obj.message);
								if(obj.status=="false")
								{
									var head_msg="<div style='min-width:180px;line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
									var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+obj.message+"</div>";
									var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
									//alert(content_msg);
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
									return false;	
								}
								else
								{
									
										
									jQuery("li.uiStep").removeClass("uiStepSelected");
									jQuery("li#business_page").addClass("uiStepSelected");
									
									jQuery(".complete_change_password").css("display","none");
									jQuery(".complete_business_page").css("display","block");
									jQuery(".complete_update_profile").css("display","none");
									jQuery(".complete_about_us").css("display","none");
									jQuery(".complete_business_logo").css("display","none");
									jQuery(".complete_add_location").css("display","none");
									jQuery(".complete_location_hour").css("display","none");
									jQuery(".complete_location_category").css("display","none");
									jQuery(".complete_location_image").css("display","none");
									
									var tab="business_page";
									var logout_link=jQuery("#logout_ele").attr("href");
									logout_link1=logout_link.split("?"); 
									jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
								}
						  }
					});
				
						close_popuploader('Notificationloader');
                    },1000);
				
				}
			}
	    }
	});
		
	
});

jQuery("#btncallbusinesspage").live("click",function(){
		
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=business_page',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{			
				window.location.href=obj.link;
				return false;	
			}
			else
			{						 
					 
				var businesspageurl=jQuery("#businesspageurl").val();

				//alert(emailid);
				var flag="true";
				var msgbox="";
				if(businesspageurl=="")
				{
					msgbox +="<div><?php echo 'Please enter facebook page web address'; ?></div>";
					flag="false";
				}

				if(flag=="false")
				{	 
					var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
					var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msgbox+"</div>";
					var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
					return false;
				}
				else
				{
					
					var businesspageurl=jQuery("#businesspageurl").val();
					businesspageurl=businesspageurl.substring(businesspageurl.lastIndexOf("/")+1);
					
					open_popuploader('Notificationloader');
					
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
					
					jQuery.ajax({
					   type:"POST",
					   url:'getbusinesspage.php',
					   data :'page='+businesspageurl,
					   async:false,
					   success:function(msg)
					   {	
							close_popuploader('Notificationloader');
							
							var obj = jQuery.parseJSON(msg);
							
							if (obj.status=="true")     
							{
								//alert(obj.data);
								//alert(obj.data['about']);
								//alert(obj.data['location']['street']);
								//jQuery("#aboutus_short").val(tinyMCE.get(obj.data['about']).getContent()]);
								if(obj.first_name)
								{
									jQuery("#firstname").val(obj.first_name);
								}
								if(obj.last_name)
								{
									jQuery("#lastname").val(obj.last_name);
								}
								
								if(obj.data['location'])
								{
									if(obj.data['location']['street'])
									{
										jQuery("#address").val(obj.data['location']['street']);
									}
									if(obj.data['location']['country'])
									{
										//jQuery("#country").val(obj.data['location']['country']);
									}
									if(obj.data['location']['country']=='United States')
									{
										//jQuery("#country").val('USA');
										//jQuery("#state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");  
									}
									if(obj.data['location']['country']=='Canada')
									{
										//jQuery("#country").val(obj.data['location']['country']);
										//jQuery("#state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
									}
									if(obj.data['location']['state'])
									{
										//jQuery("#state").val(obj.data['location']['state']);
									}
									
									if(obj.data['location']['city'])
									{
										//jQuery("#city").val(obj.data['location']['city']);
									}
									
									if(obj.data['location']['zip'])
									{
										jQuery("#zipcode").val(obj.data['location']['zip']);
									}
								}
								if(obj.data['phone'])
								{
									var str=obj.data['phone'].replace ( /[^\d.]/g, '' );
									
									jQuery("#mobileno_area_code").val(str.substr(0,3));
									jQuery("#mobileno2").val(str.substr(3,3));
									jQuery("#mobileno").val(str.substr(6,4));
								}
								
								if(obj.data['name'])
								{
									jQuery("#business").val(obj.data['name']);;
								}
								
								if(obj.data['about'])
								{
									tinyMCE.getInstanceById('aboutus_short').setContent(obj.data['about']);
								}
								
								var str_description="";
								
								if(obj.data['founded'])
								{
									str_description+="<p>Founded : "+obj.data['founded']+"</p>";
								}
								if(obj.data['awards'])
								{
									str_description+="<p>Awards : "+obj.data['awards']+"</p>";
								}
								if(obj.data['company_overview'])
								{
									str_description+="<p>Company Overview : <br/>"+obj.data['company_overview']+"</p>";
								}
								if(obj.data['general_info'])
								{
									str_description+="<p>General Info : <br/>"+obj.data['general_info']+"</p>";
								}
								if(obj.data['description'])
								{
									str_description+="<p>"+obj.data['description']+"</p>";
								}
								if(obj.data['mission'])
								{
									str_description+="<p>Mission : "+obj.data['mission']+"</p>";
								}
								if(obj.data['products'])
								{
									str_description+="<p>Products : "+obj.data['products']+"</p>";
								}
								
								if(str_description!="")
								{
									tinyMCE.getInstanceById('aboutus').setContent(str_description);
								}
								
								if(obj.data['location'])
								{
									if(obj.data['location']['street'])
									{
										jQuery("#address_l").val(obj.data['location']['street']);
									}
									if(obj.data['location']['country'])
									{
										//jQuery("#country_l").val(obj.data['location']['country']);
									}
									if(obj.data['location']['country']=='United States')
									{
										//jQuery("#country_l").val('USA');
										//jQuery("#state_l").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");  
									}
									if(obj.data['location']['country']=='Canada')
									{
										//jQuery("#country_l").val(obj.data['location']['country']);
										//jQuery("#state_l").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
									}
									if(obj.data['location']['state'])
									{
										//jQuery("#state_l").val(obj.data['location']['state']);
									}
									
									if(obj.data['location']['city'])
									{
										//jQuery("#city_l").val(obj.data['location']['city']);
									}
									
									if(obj.data['location']['zip'])
									{
										jQuery("#zip_l").val(obj.data['location']['zip']);
									}
								}
								if(obj.data['website'])
								{
									jQuery("#website").val(obj.data['website']);
								}
								if(obj.data['link'])
								{
									jQuery("#facebook").val(obj.data['link']);
								}
								if(obj.data['phone'])
								{
									var str=obj.data['phone'].replace ( /[^\d.]/g, '' );
									jQuery("#mobileno_area_code_l").val(str.substr(0,3));
									jQuery("#mobileno2_l").val(str.substr(3,3));
									jQuery("#mobileno_l").val(str.substr(6,4));
								}
								
								if(obj.data['price_range'])
								{
									if(obj.data['price_range']=='$ (0-10)')
									{
										jQuery("#pricerange").val('1');
									}
									if(obj.data['price_range']=='$$ (10-30)')
									{
										jQuery("#pricerange").val('2');
									}
									if(obj.data['price_range']=='$$$ (30-50)')
									{
										jQuery("#pricerange").val('3');
									}
									if(obj.data['price_range']=='$$$$ (50+)')
									{
										jQuery("#pricerange").val('4');
									}
								}
								if(obj.data['parking'])
								{
									
									if(obj.data['parking']['lot']==1)
									{
										jQuery("#Lot").attr("checked","checked");
									}
									if(obj.data['parking']['street']==1)
									{
										jQuery("#Street").attr("checked","checked");
									}
									if(obj.data['parking']['valet']==1)
									{
										jQuery("#Valet").attr("checked","checked");
									}
									
								}
								
								
								
								jQuery("#hdn_image_path_l").val(obj.location_profile_image);
								
								var image_html='<img src="<?php echo ASSETS_IMG;?>/m/location/'+obj.location_profile_image+'" class="displayimg" />';
								image_html +='<br>';
								image_html +='<div style="margin-top: 10px; display:table"><div style="display:table-row;"><div style="display:table-cell;"><img src="<?php echo ASSETS_IMG; ?>/m/delete.gif" id="'+obj.location_profile_image+'" onclick="rm_image_l(this.id)"></div></div></div>';
								if(obj.location_profile_image)
								{
										jQuery("#files_l").html(image_html);
								}
								
								var profile_image_html='<img src="<?php echo ASSETS_IMG;?>/m/icon/'+obj.icon_profile_image+'" alt="Merchant" id="profilePic" class="profilePic silhouette img img2">';
								
								jQuery("#hdn_image_path").val(obj.icon_profile_image);
								if(obj.icon_profile_image)
								{
										jQuery("#nax_profile_pic").html(profile_image_html);
								}
								
								var additional_images_string=obj.additional_images;
								
								
								var additional_imgs_str = "<ul>";
								for(i=0;i<obj.additional_images.length;i++)
								{
									additional_imgs_str +="<li><img src='"+obj.additional_images[i]+"'><span imgurl='"+obj.additional_images[i]+"' style='display:none'>Upload</span></li>";
									//jQuery("#additional_images_id").append("<img src='"+obj.additional_images[i]+"'><span  style='display:none'>Upload</span>");
								}
								additional_imgs_str += "</ul>";
								jQuery("#additional_images_id_uploaded").append(additional_imgs_str);
								
								
								/*
								jQuery.each(obj.data, function(k, v) {
									//display the key and value pair
									//alert(k + ' is ' + v);
									
									if (typeof v != 'string') 
									{
										jQuery.each(v, function(a, b) {
											//alert(a + ' is ' + b);
										});
									}

								});
								*/
								
								jQuery("li.uiStep").removeClass("uiStepSelected");
								jQuery("li#update_profile").addClass("uiStepSelected");
								
								jQuery(".complete_change_password").css("display","none");
								jQuery(".complete_business_page").css("display","none");
								jQuery(".complete_update_profile").css("display","block");
								jQuery(".complete_about_us").css("display","none");
								jQuery(".complete_business_logo").css("display","none");
								jQuery(".complete_add_location").css("display","none");
								jQuery(".complete_location_hour").css("display","none");
								jQuery(".complete_location_category").css("display","none");
								jQuery(".complete_location_image").css("display","none");
								jQuery(".complete_location_additional_image").css("display","none");
								
								var tab="update_profile";
								var logout_link=jQuery("#logout_ele").attr("href");
								logout_link1=logout_link.split("?"); 
								jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
					
							}
							else
							{
								var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
								var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+obj.message+"</div>";
								var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
								return false;
							}
						}
					});
					
						close_popuploader('Notificationloader');
                    },1000);
					
				}
						  
					
			}
	    }
	});
		
})

jQuery('#additional_images_id_uploaded img').live("mouseenter",function(){
   jQuery(this).next().css("display","block");
});

jQuery('#additional_images_id_uploaded img').live("mouseleave",function(){
   jQuery(this).next().css("display","none");
});

jQuery('#additional_images_id_uploaded span').live("mouseenter",function(){
   jQuery(this).css("display","block");
});

jQuery('#additional_images_id_uploaded span').live("click",function(){
   var imgurl=jQuery(this).attr('imgurl');
   //alert(imgurl);
   jQuery("#uploading_msg_more").html("");
   
    if(jQuery('#files_more li').length == 24)
	{

	 jQuery("#uploading_msg_more").html('<?php echo $merchant_msg["addlocation"]["Msg_Max_Image_Upload"];?>');
	  return false;
	}
									 
   open_popuploader('Notificationloader');
   
   timeout=setInterval(function()
	{
		//alert("hi");
		clearTimeout(timeout);
   
   try
	{
		jQuery.ajax({
			type: "POST",
			async:false,
			url: '<?php echo WEB_PATH."/merchant/process.php" ?>',
			data: "import_url=" + imgurl +"&upload_facebook_location_image=yes",
			success: function(msg) 
			{
				close_popuploader('Notificationloader');
				var obj = jQuery.parseJSON(msg);
				//alert(obj.filename);
				if (obj.status=="true")     
				{
					var file_path_l = obj.filename;
                    var arr = file_path_l.split("."); 
									
					//jQuery(".list_carousel").show();
					var img = "<div class='mainmoreclass' style='position:relative' ><div class='imagemoreclass' style=''><img src='<?=ASSETS_IMG?>/m/location/temp_thumb/"+ obj.filename +"' class='displayimg'></div>";
					jQuery('<li style="list-style:none" id="li_'+arr[0]+'"></li>').appendTo('#files_more').append(img +"<a href='javascript:void(0)' style='float: left;margin-top: -18px;position: absolute;right: 2px;top: 0;width: 16px;background-color:#fff;' id='"+file_path_l+"' class='closebuttonclass' onclick='rm_image_more(this.id)' ></a><input type='hidden' name='hdn_more_images[]' value='"+ file_path_l +"' /></div>");	
					/*
					 jQuery('#files_more').carouFredSel({
						auto: false,
						prev: '#prev2',
						next: '#next2',
						pagination: "#pager2",
						mousewheel: true,
						height : 80,
						width :445,
						align:"left",
						swipe: {
								onMouse: true,
								onTouch: true
						}
					});
					jQuery('.list_carousel').css("overflow","inherit");
					*/						
				}
				else
				{
					jQuery("#uploading_msg_more").html(obj.message);
				}
			}
		});
	}
	catch(e)
	{
		alert(e);
	}
		
		close_popuploader('Notificationloader');
	},1000);

	
}); 
jQuery("#btnUpdateProfile").live("click",function(){

	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=update_profile',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msg_box="";
				
				var mobile_country_code=jQuery("#mobile_country_code").val();
				var mobileno_area_code=jQuery("#mobileno_area_code").val();
				var mobileno=jQuery("#mobileno").val();
				var postal_code=jQuery("#zipcode").val();
				var country=jQuery("#country").val();
				var state=jQuery("#state").val();
				var city=jQuery("#city").val();
				
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'btngetstateofcountry=true&country_id='+country,
					async:false,
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="false")     
						{
							jQuery("#state_l").html(obj.html);
						}
						else
						{
							jQuery("#state_l").html(obj.html);
						}
					}
				});
				
				/*
				if(country == "USA")
				{
					jQuery("#state_l").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");   
				}
				else
				{
					jQuery("#state_l").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
				}
				*/
				
				var mobileno2=jQuery("#mobileno2").val();
				var address=jQuery("#address").val();
				
				var lastname=jQuery("#lastname").val();
				var firstname=jQuery("#firstname").val();
				var business=jQuery("#business").val();
				
				var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
				var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
				var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
				var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
				var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
				var flag="true";
				
				if(firstname == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_first_name']; ?></div>";
					flag="false";
				}
				if(lastname == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_last_name']; ?></div>";
					flag="false";
				}
				if(address == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_address']; ?></div>";
					flag="false";
				}
				console.log("country="+country+" state="+state+" city="+city);
				if(country == "0" || country == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_country']; ?></div>";
					flag="false";
				}
				if(state == "0" || state == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_state']; ?></div>";
					flag="false";
				}
				if(city == "0" || city == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_city']; ?></div>";
					flag="false";
				}
				if(postal_code=="")
				{
					//alert("Please enter postal/zipcode");
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_postal_zipcode']; ?></div>";
					flag="false";
				}
				else
				{
                    postal_code=jQuery.trim(postal_code);
					postal_code=postal_code.toUpperCase();
					if(country=="1")
					{
					   if(!usPostalReg.test(postal_code)) 
					   {
							//alert("Please enter valid postal/zipcode");				
							msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_valid_postal_zipcode']; ?></div>";
							flag="false";
					   }	
						
					}
					else if(country == "2")
					{
									
						if(!canadaPostalReg.test(postal_code)) 
						{				  
							//alert("Please enter valid postal/zipcode");
							msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_valid_postal_zipcode']; ?></div>";
							flag="false";
						}
					}
				}
				if(mobileno_area_code == "" || mobileno =="" || mobileno2 == "" )
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
					 flag="false";
				}
				else 
				{
					if(mobileno_area_code != "")
					{

						if(!numericReg.test(mobileno_area_code)) 
						{
							//alert("Please Input Valid Mobile Number");
							msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
						   flag="false";
							//return false;
						}
						else
						{
							if(mobileno_area_code.length != 3)
							{
								//alert("Please Input Valid Area Code Number");
								msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
							   flag="false";
								//return false;
							}
						}

					}
					else if(mobileno != "")
					{
						if(!numericReg.test(mobileno)) 
						{
							//alert("Please Input Valid Mobile Number");
							msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
						   flag="false";
							//return false;
						}
						else
						{
							if(mobileno.length != 4)
							{
								//alert("Please Input Valid Mobile Number");
								msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
								flag="false";
								//return false;
							} 
						}

					}
					else if(mobileno2 != "")
					{
						if(!numericReg.test(mobileno2)) 
						{
							//alert("Please Input Valid Mobile Number");
							msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
						   flag="false";
							//return false;
						}
						else
						{
							if(mobileno2.length != 3)
							{
								//alert("Please Input Valid Mobile Number");
								msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
								flag="false";
								//return false;
							}
							
						}

					}		   
				}
				
				if(business=="")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_business_name']; ?></div>";
					flag="false";
				}
				
				var tag_value=jQuery("#business_tags").val();
				if(tag_value == "")
				{
					msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_please_add_business_tag"]; ?></div>";
					flag="false";
				}
				else
				{
					if(hastagRef.test(tag_value))
					{
						
						var tag_arr = tag_value.split(",");   
						//alert(tag_arr.length);
						if(tag_arr.length>15)
						{
							msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_add_business_tag"]; ?></div>";
							flag="false";
						}
						
					}
					else
					{
					
						msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_business_tag"]; ?></div>";
						flag="false";
					}
				}
	
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{	  
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
					return false;
				}
				else
				{
				
					open_popuploader('Notificationloader');

					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
					
					jQuery.ajax({
					  type:"POST",
					  url:'process.php',
					  data :'btnUpdateProfileProcess=yes&merchant_id='+merchant_id+'&firstname='+firstname+'&lastname='+lastname+'&address='+address
							+'&city='+city+'&state='+state+'&zipcode='+postal_code+'&country='+country+'&mobile_country_code='+mobile_country_code
							+'&mobileno_area_code='+mobileno_area_code+'&mobileno2='+mobileno2+'&mobileno='+mobileno+'&business='+business+"&business_tags="+tag_value,
					  async:false,
					  success:function(msg)
					  {
						close_popuploader('Notificationloader');
							var obj = jQuery.parseJSON(msg);
							//alert(obj.status);
							//alert(obj.message);
							if(obj.status=="true")
							{
								jQuery("li.uiStep").removeClass("uiStepSelected");
								jQuery("li#about_us").addClass("uiStepSelected");
								
								jQuery(".complete_change_password").css("display","none");
								jQuery(".complete_business_page").css("display","none");
								jQuery(".complete_update_profile").css("display","none");
								jQuery(".complete_about_us").css("display","block");
								jQuery(".complete_business_logo").css("display","none");
								jQuery(".complete_add_location").css("display","none");
								jQuery(".complete_location_hour").css("display","none");
								jQuery(".complete_location_category").css("display","none");
								jQuery(".complete_location_image").css("display","none");
								jQuery(".complete_location_additional_image").css("display","none");
								
								var tab="about_us";
									var logout_link=jQuery("#logout_ele").attr("href");
									logout_link1=logout_link.split("?"); 
									jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
							}
					  }
					});		
		
						close_popuploader('Notificationloader');
                    },1000);
				}
			}
	    }
	});			
});

jQuery("#btnUpdateAboutus").live("click",function(){

	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=about_us',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msg_box="";
				var flag="true";
				jQuery('#aboutus').val(tinyMCE.get('aboutus').getContent());
				jQuery('#aboutus_short').val(tinyMCE.get('aboutus_short').getContent());
				var aboutus=encodeURIComponent(jQuery("#aboutus").val());
				var aboutus_short=jQuery("#aboutus_short").val();
				//alert(aboutus);
				//alert(aboutus_short);
				if(aboutus_short=="")
				{		
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_about_you_short']; ?></div>";
					flag="false";
				}
				if(aboutus=="")
				{		
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_about_you']; ?></div>";
					flag="false";
				}
				
				
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{	  
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
					return false;
				}
				else
				{
					open_popuploader('Notificationloader');
				
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
					
					jQuery.ajax({
					  type:"POST",
					  url:'process.php',
					  data :'btnUpdateAboutusProcess=yes&merchant_id='+merchant_id+'&aboutus='+aboutus+'&aboutus_short='+aboutus_short,
					  async:false,
					  success:function(msg)
					  {
						close_popuploader('Notificationloader');
							var obj = jQuery.parseJSON(msg);
							//alert(obj.status);
							//alert(obj.message);
							if(obj.status=="true")
							{
								jQuery("li.uiStep").removeClass("uiStepSelected");
								jQuery("li#business_logo").addClass("uiStepSelected");
								
								jQuery(".complete_change_password").css("display","none");
								jQuery(".complete_business_page").css("display","none");
								jQuery(".complete_update_profile").css("display","none");
								jQuery(".complete_about_us").css("display","none");
								jQuery(".complete_business_logo").css("display","block");
								jQuery(".complete_add_location").css("display","none");
								jQuery(".complete_location_hour").css("display","none");
								jQuery(".complete_location_category").css("display","none");
								jQuery(".complete_location_image").css("display","none");
								jQuery(".complete_location_additional_image").css("display","none");
								
								var tab="business_logo";
									var logout_link=jQuery("#logout_ele").attr("href");
									logout_link1=logout_link.split("?"); 
									jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
									
							}
					  }
					});
					
						close_popuploader('Notificationloader');
                    },1000);
				}
			}
	    }
	});				
});

jQuery("#btnUpdateBusinesslogo").live("click",function(){
	
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=business_logo',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msg_box="";
				var flag="true";

				if(jQuery("#hdn_image_path").val()=="")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_upload_merchant_icon']; ?></div>";
					flag="false";
				}
				
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{	  
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
					return false;
				}
				else
				{
					open_popuploader('Notificationloader');
					
					timeout=setInterval(function()
					{
						//alert("hi");
						clearTimeout(timeout);
					
					jQuery.ajax({
					  type:"POST",
					  url:'process.php',
					  data :'btnUpdateBusinessLogoProcess=yes&merchant_id='+merchant_id+'&hdn_image_path='+jQuery("#hdn_image_path").val(),
					  async:false,
					  success:function(msg)
					  {
						close_popuploader('Notificationloader');
							var obj = jQuery.parseJSON(msg);
							//alert(obj.status);
							//alert(obj.message);
							if(obj.status=="true")
							{
								jQuery("li.uiStep").removeClass("uiStepSelected");
								jQuery("li#add_location").addClass("uiStepSelected");
								
								jQuery(".complete_change_password").css("display","none");
								jQuery(".complete_business_page").css("display","none");
								jQuery(".complete_update_profile").css("display","none");
								jQuery(".complete_about_us").css("display","none");
								jQuery(".complete_business_logo").css("display","none");
								jQuery(".complete_add_location").css("display","block");
								jQuery(".complete_location_hour").css("display","none");
								jQuery(".complete_location_category").css("display","none");
								jQuery(".complete_location_image").css("display","none");
								jQuery(".complete_location_additional_image").css("display","none");
								
								var tab="add_location";
								var logout_link=jQuery("#logout_ele").attr("href");
								logout_link1=logout_link.split("?"); 
								jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);	
							}
					  }
					});					
				
						close_popuploader('Notificationloader');
                    },1000);	
				}
			}
	    }
	});			
});

jQuery("#btnAddLocationProcess").live("click",function(){
	open_popuploader('Notificationloader');
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msgbox="";
				var flag="true";

				var address1=jQuery("#address_l").val();
				var country=jQuery("#country").val();
				var state1=jQuery('#state_l').val();
				var city1=jQuery('#city_l').val();
				var zipcode1=jQuery('#zip_l').val();
				//var country=jQuery("#country_l").val();
				
				var mobileno_area_code=jQuery("#mobileno_area_code_l").val();
				var mobileno_area_code_length=jQuery("#mobileno_area_code_l").val().length;
				var mobileno=jQuery("#mobileno_l").val();
				var mobileno2=jQuery("#mobileno2_l").val();
				var mobileno_length=jQuery("#mobileno_l").val().length;
				var mobileno2_length=jQuery("#mobileno2_l").val().length;
				var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
				var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
				
				var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
				var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
				
				var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
				var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
				zipcode1=zipcode1.toUpperCase();
						
				var flag="";
				var msgbox="";
				if(address1 == "")
				{
				   msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_address"];?></div>";
				   flag="false";
				}
				if(state1 == "0" || state1 == "")
				{
					msgbox +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_state']; ?></div>";
					flag="false";
				}
				if(city1 == "0" || city1 == "")
				{
					msgbox +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_city']; ?></div>";
					flag="false";
				}
				
				if(zipcode1 == "")
				{
				   msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_postal_zipcode"];?></div>";
				   flag="false";
				}
				else
				{
				  zipcode1=jQuery.trim(zipcode1);
                                    zipcode1=zipcode1.toUpperCase();
				   if(country=="1")
				   {
					   if(!usPostalReg.test(zipcode1)) {
						
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_postal_zipcode"];?></div>";
						flag="false";
					   }	
						
					}
					else if(country == "2")
					{
						if(!canadaPostalReg.test(zipcode1)) {
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_postal_zipcode"];?></div>";
						flag="false";
						}
					}
				}
				
				if(document.getElementById("email").value!="")
				{				
					if(email_validation(document.getElementById("email").value) == false)
					{
						msgbox +="<div><?php echo $merchant_msg['login_register']['Msg_valid_email']; ?></div>";
						flag="false";
					}
				}
				if(mobileno_area_code != "")
				{			
					if(!numericReg.test(mobileno_area_code))
					{		
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
						flag="false";               
					}
					else if(mobileno_area_code_length<=2)
					{
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
						flag="false";
					}                                              
				}
				else
				{
					msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
					flag="false";
				}
				if(mobileno != "" || mobileno2 != "")
				{
					if(!numericReg.test(mobileno) || !numericReg.test(mobileno2)) 
					{
						//alert("Please Input Valid Mobile Number");
						//return false;
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
						flag="false";
					}
					else if(mobileno_length <=3 || mobileno2_length <= 2)
					{
						msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
						flag="false";
					}
				}
				else
				{
					msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
					flag="false";
				}
								   
				mobile_no = jQuery("#mobile_country_code_l").val()+"-"+jQuery("#mobileno_area_code_l").val()+"-"+jQuery("#mobileno2_l").val()+"-"+jQuery("#mobileno_l").val();

				

				
								 
				var parkingcount=jQuery('input:checkbox[name="chk_parking[]"]:checked').size();
				if(parkingcount==0)
				{
					//alert("<?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_parking"];?>");
					//return false;
					msgbox+="<div><?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_parking"];?></div>";
					flag="false";
				}
					
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msgbox+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{	  
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
					return false;
				}
				else
				{
					
					jQuery("li.uiStep").removeClass("uiStepSelected");
					jQuery("li#location_hour").addClass("uiStepSelected");
					
					jQuery(".complete_change_password").css("display","none");
					jQuery(".complete_business_page").css("display","none");
					jQuery(".complete_update_profile").css("display","none");
					jQuery(".complete_about_us").css("display","none");
					jQuery(".complete_business_logo").css("display","none");
					jQuery(".complete_add_location").css("display","none");
					jQuery(".complete_location_hour").css("display","block");
					jQuery(".complete_location_category").css("display","none");
					jQuery(".complete_location_image").css("display","none");
					jQuery(".complete_location_additional_image").css("display","none");
					
					var tab="add_location";
								var logout_link=jQuery("#logout_ele").attr("href");
								logout_link1=logout_link.split("?"); 
								jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);	
					
				}
			}
	    }
	});
	close_popuploader('Notificationloader');	
});

jQuery("#btnAddLocationhour").live("click",function(){
	open_popuploader('Notificationloader');
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
							
				jQuery("li.uiStep").removeClass("uiStepSelected");
				jQuery("li#location_category").addClass("uiStepSelected");
				
				jQuery(".complete_change_password").css("display","none");
				jQuery(".complete_business_page").css("display","none");
				jQuery(".complete_update_profile").css("display","none");
				jQuery(".complete_about_us").css("display","none");
				jQuery(".complete_business_logo").css("display","none");
				jQuery(".complete_add_location").css("display","none");
				jQuery(".complete_location_hour").css("display","none");
				jQuery(".complete_location_category").css("display","block");
				jQuery(".complete_location_image").css("display","none");
				jQuery(".complete_location_additional_image").css("display","none");
				
				var tab="add_location";
								var logout_link=jQuery("#logout_ele").attr("href");
								logout_link1=logout_link.split("?"); 
								jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);	
										
			}
	    }
	});
	close_popuploader('Notificationloader');	
});
/*
jQuery("#btnAddLocationcategory").live("click",function(){
	open_popuploader('Notificationloader');
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
			
				jQuery("li.uiStep").removeClass("uiStepSelected");
				jQuery("li#location_image").addClass("uiStepSelected");
				
				jQuery(".complete_change_password").css("display","none");
				jQuery(".complete_business_page").css("display","none");
				jQuery(".complete_update_profile").css("display","none");
				jQuery(".complete_about_us").css("display","none");
				jQuery(".complete_business_logo").css("display","none");
				jQuery(".complete_add_location").css("display","none");
				jQuery(".complete_location_hour").css("display","none");
				jQuery(".complete_location_category").css("display","none");
				jQuery(".complete_location_image").css("display","block");
				jQuery(".complete_location_additional_image").css("display","none");
				
				var tab="add_location";
								var logout_link=jQuery("#logout_ele").attr("href");
								logout_link1=logout_link.split("?"); 
								jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);	
							
			}
	    }
	});
	close_popuploader('Notificationloader');	
});
*/
/*
jQuery("#btnAddLocationimage").live("click",function(){
	open_popuploader('Notificationloader');
	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{
				var merchant_id="<?php echo $_SESSION['merchant_id'] ?>";
				var msg_box="";
				var flag="true";
				var hdn_image_path_l=jQuery("#hdn_image_path_l").val();
				//alert(aboutus);
				if(hdn_image_path_l=="")
				{		
					msg_box +="<div><?php echo $merchant_msg['addlocation']['Msg_Please_Upload_Location_Image']; ?></div>";
					flag="false";
				}
				
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				//alert(flag);
				if(flag=="false")
				{
						
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
					
					return false;
				}
				else
				{
			
					jQuery("li.uiStep").removeClass("uiStepSelected");
					jQuery("li#location_additional_image").addClass("uiStepSelected");
					
					jQuery(".complete_change_password").css("display","none");
					jQuery(".complete_business_page").css("display","none");
					jQuery(".complete_update_profile").css("display","none");
					jQuery(".complete_about_us").css("display","none");
					jQuery(".complete_business_logo").css("display","none");
					jQuery(".complete_add_location").css("display","none");
					jQuery(".complete_location_hour").css("display","none");
					jQuery(".complete_location_category").css("display","none");
					jQuery(".complete_location_image").css("display","none");
					jQuery(".complete_location_additional_image").css("display","block");
					
					var tab="add_location";
								var logout_link=jQuery("#logout_ele").attr("href");
								logout_link1=logout_link.split("?"); 
								jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);	
				}			
			}
	    }
	});	
	close_popuploader('Notificationloader');
});
*/
function validateForm()
{

	jQuery.ajax({
	   type:"POST",
	   url:'process.php',
	   data :'loginornot=true&tab=add_location',
	   async:false,
	   success:function(msg)
	   {			 
			var obj = jQuery.parseJSON(msg);	 
			if (obj.status=="false")     
			{
				
				window.location.href=obj.link;
				return false;	
			}
			else
			{	
												
				var msg_box='<?php echo $merchant_msg['login_register']['Msg_merchant_register'] ?>';
				var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
				var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel_index' name='popupcancel_index' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				jQuery.fancybox({
					content:jQuery('#dialog-message').html(),
					type: 'html',
					openSpeed  : 300,
					closeSpeed  : 300,
					changeFade : 'fast',
					closeBtn: false,					
					helpers: {
						overlay: {
						closeClick: false,
						opacity: 0.3
						} // overlay
					}
				});
				
			}
	    }
	});				
}

jQuery(".fancybox-inner #popupcancel_index").live("click",function(){
	document.forms[0].submit();		
});

</script>

<?php
if($data_sub_merchant_id['merchant_parent'] == "0")
{ ?>
<script>
var file_path = "";
jQuery(function(){
		var btnUpload=jQuery('#upload');
		var status=jQuery('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUploadMerchant&img_type=icon',
			name: 'uploadfile',
			onSubmit: function(file, ext){
			
				jQuery("#upload_business_logo").css("display","none");
				open_popuploader('Notificationloader');
				
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
				//alert(response);
                
                
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
								
				close_popuploader('Notificationloader');					
				jQuery("#upload_business_logo").css("display","block");
				
				var image = document.getElementById('profilePic');
				var width = image.naturalWidth;
				var height = image.naturalHeight;
				//alert(width);
				//alert(height);
				if(height>=111)
				{
					jQuery("#profilePic").addClass("img1");
				}
				else
				{
					jQuery("#profilePic").addClass("img2");
				}
							
			}
		});
		
	});
</script>
<?php } ?>

<script>
 
/* start of script for PAY-508-28033*/
function save_from_library()
{
	 var sel_val = $('input[name=use_image]:checked').val();
	 <!--// 369-->
	 if (sel_val==undefined)
	 {
	 	close_popup('Notification');
	 }
	 else
	 {
		
		jQuery("#hdn_image_id").val(sel_val);
		var sel_src = jQuery("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
		//alert(sel_src);
	       jQuery("#hdn_image_path").val(sel_src);
	       file_path = "";
	       close_popup('Notification');
	       var img = "<img src='<?=ASSETS_IMG?>/m/campaign/"+ sel_src +"' class='displayimg'>";
	       jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' onclick='rm_image()' /></div></div></div>");
	}
	 <!--// 369-->
	
}
function rm_image()
{
	jQuery("#hdn_image_path").val("");
	jQuery("#hdn_image_id").val("");
	jQuery('#files').html("");
	
}
function save_from_computer()
{
	jQuery("#hdn_image_path").val(file_path);
	jQuery("#hdn_image_id").val("");
	jQuery("#profilePic").attr("src","<?=ASSETS_IMG?>/m/icon/"+ file_path);
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG?>/m/icon/"+ file_path +"' class='displayimg'>";
	jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' onclick='rm_image()' /></div></div></div>");
				
}
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
 function open_popup(popup_name)
{

	if(jQuery("#hdn_image_id").val()!="")
	{
		jQuery('input[name=use_image][value='+jQuery("#hdn_image_id").val()+']').attr("checked","checked");
	}
	jQuery("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		jQuery("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 jQuery("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	
}
jQuery(document).ready(function(){
	if(jQuery("#hdn_image_path").val() != "")
	{
		var img = "<img src='<?=ASSETS_IMG?>/m/icon/"+ jQuery("#hdn_image_path").val() +"' class='displayimg'>";
		jQuery('#files').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' onclick='rm_image()' /></div></div></div>");
		jQuery("#profilePic").attr("src","<?=ASSETS_IMG?>/m/icon/"+ jQuery("#hdn_image_path").val());
		
		var image = document.getElementById('profilePic');
		var width = image.naturalWidth;
		var height = image.naturalHeight;
		//alert(width);
		//alert(height);
		if(height>=111)
		{
			jQuery("#profilePic").addClass("img1");
		}
		else
		{
			jQuery("#profilePic").addClass("img2");
		}

	}
	
	jQuery('#country').change(function(){
		var change_value=this.value;
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btngetstateofcountry=true&country_id='+change_value,
			async:false,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="false")     
				{
					jQuery("#state").html(obj.html);
				}
				else
				{
					jQuery("#state").html(obj.html);
				}
			}
		});
		jQuery('#state').trigger("change");
    });
    
    jQuery('#state').change(function(){
		var change_value=this.value;
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btngetcityofstate=true&state_id='+change_value,
			async:false,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="false")     
				{
					jQuery("#city").html(obj.html);
				}
				else
				{
					jQuery("#city").html(obj.html);
				}
			}
		});
    
    });
    
    jQuery('#state_l').change(function(){
		var change_value=this.value;
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btngetcityofstate=true&state_id='+change_value,
			async:false,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="false")     
				{
					jQuery("#city_l").html(obj.html);
				}
				else
				{
					jQuery("#city_l").html(obj.html);
				}
			}
		});
    
    });
	
	
});
jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
});

var file_path_l="";
jQuery(function(){
		var btnUpload=jQuery('#upload_l');
		var status=jQuery('#status_l');

		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUpload&img_type=location',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				open_popuploader('Notificationloader');
				if(jQuery('#files_l').children().length > 0)
				{
					jQuery('#files_l li').detach();
				}
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('<?php echo $merchant_msg["addlocation"]["Msg_Image_Validation"];?>');
					return false;
				}
				status.text('<?php echo $merchant_msg["addlocation"]["Msg_uploading"];?>');
			},
			onComplete: function(file, response){
				close_popuploader('Notificationloader');
				//On completion clear the status
                                /*
				var arr = response.split("|");
				
				status.text('');
				//Add uploaded file to list
				file_path = arr[1];
				save_from_computer();
                                 */
                                //alert(response);
                                var arr = response.split("|");
				if(arr[1]=="small")
                                {
                                    status.text(arr[0]);
                                }
                                else
                                {
                                    status.text('');
                                    //Add uploaded file to list
                                    file_path_l = arr[1];
                                    save_from_computer_l();
                                }
			}
		});
                
                /* More Images Upload Code*/
                var btnUpload=$('#upload_more');
                var uploading=$('#uploading_msg_more');
		var status_more=$('#status_more');
		
		new AjaxUpload(btnUpload, {
			action: 'upload_additional_images.php?doAction=FileUpload&img_type=location',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				open_popuploader('Notificationloader');
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status_more.text('<?php echo $merchant_msg["addlocation"]["Msg_Image_Validation"];?>');
					return false;
				}
                                if(jQuery('#files_more li').length == 25)
                                     {
                                        
                                         status_more.text('<?php echo $merchant_msg["addlocation"]["Msg_Max_Image_Upload"];?>');
                                          return false;
                                     }
                                     status_more.text('');
				uploading.text('<?php echo $merchant_msg["addlocation"]["Msg_uploading"];?>');
			},
			onComplete: function(file, response){
				close_popuploader('Notificationloader');
                                var arr = response.split("|");
                                
				if(arr[1]=="small")
                                {
                                    status_more.text(arr[0]);
                                     uploading.text('');
                                }
                                else
                                {
                                    status_more.text('');
                                     uploading.text('');
                                    //Add uploaded file to list
                                    file_path_l = arr[1];
                                    var arr = file_path_l.split("."); 
                                    //$("#hdn_image_path_more").val(file_path_l);
                                    //$("#hdn_image_id").val("");
                                    //close_popup('Notification');
                                    //jQuery(".list_carousel").show();
                                    var img = "<div class='mainmoreclass' style='position:relative' ><div class='imagemoreclass' style=''><img src='<?=ASSETS_IMG?>/m/location/temp_thumb/"+ file_path_l +"' class='displayimg'></div>";
                                   // jQuery('<li style="list-style:none" id="li_'+arr[0]+'"></li>').appendTo('#files_more').append(img +"<div style='margin-top: 10px; display:table;float:left;width:48px;height:65px;'><img src='<?=ASSETS_IMG ?>/m/delete.gif' id='"+file_path+"' onclick='rm_image_more(this.id)' /></div><input type='hidden' name='hdn_more_images[]' value='"+ file_path +"' /></div>");	
                                  jQuery('<li style="list-style:none" id="li_'+arr[0]+'"></li>').appendTo('#files_more').append(img +"<a href='javascript:void(0)' style='float: left;margin-top: -28px;position: absolute;right: -10px;top: 0;width: 16px;back:#fff' id='"+file_path_l+"' class='closebuttonclass' onclick='rm_image_more(this.id)' ></a><input type='hidden' name='hdn_more_images[]' value='"+ file_path_l +"' /></div>");	 
//save_more_from_computer();
                                        //var set_int=setInterval(function() {
                                             
                                        //}, 2000);
                                       

                                         
                                }
                                /*
                                jQuery('#files_more').carouFredSel({
                                                auto: false,
                                                prev: '#prev2',
                                                next: '#next2',
                                                pagination: "#pager2",
                                                mousewheel: true,
                                                height : 80,
                                                width :445,
                                                align:"left",
                                                swipe: {
                                                        onMouse: true,
                                                        onTouch: true
                                                }
                                            });
                                            jQuery('.list_carousel').css("overflow","inherit");
                                */ 
			}
		});
                
                /* End More Images Upload Code*/
                
});
function rm_image_more(data)
{
	
      
       var arr1 = data.split(".");
       jQuery.ajax({
                           type:"POST",
                           url:'remove_additional_images.php',
                           data :'imagename='+data,
                          async:false,
                           success:function(msg)
                           {
                             
                             jQuery("#li_"+arr1[0]).remove();
                             /*
							 jQuery('#files_more').carouFredSel({
                                                auto: false,
                                                prev: '#prev2',
                                                next: '#next2',
                                                pagination: "#pager2",
                                                mousewheel: true,
                                                height : 80,
                                                width :445,
                                                align:"left",
                                                swipe: {
                                                        onMouse: true,
                                                        onTouch: true
                                                }
                                            });
                               jQuery('.list_carousel').css("overflow","inherit");
                            */
                           }
                           
                     });
                     /*
                      jQuery('.list_carousel ul').each(function() {
                          
                        if (jQuery(this).children().length == 0) {
                          jQuery('.list_carousel').hide();
                        }
                      });  
					*/	
}
function save_from_library_l()
{
	 var sel_val = $('input[name=use_image]:checked').val();
	 <!--// 369-->
	 if (sel_val==undefined)
	 {
	 	close_popup('Notification');
	 }
	 else
	 {
		
		jQuery("#hdn_image_id_l").val(sel_val);
		var sel_src = jQuery("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
		//alert(sel_src);
	       jQuery("#hdn_image_path_l").val(sel_src);
	       file_path_l = "";
	       close_popup('Notification');
	       var img = "<img src='<?=ASSETS_IMG?>/m/campaign/"+ sel_src +"' class='displayimg'>";
	       jQuery('#files_l').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' id='"+sel_src+"' onclick='rm_image_l(this.id)' /></div></div></div>");
	}
	 <!--// 369-->
	
}
function rm_image_l(id)
{
	jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'is_image_delete=yes&image_type=location&filename='+id,
                          async:false,
                           success:function(msg)
                           {
								jQuery("#hdn_image_path_l").val("");
								jQuery("#hdn_image_id_l").val("");
								jQuery('#files_l').html("");
                           }
                           
                     });
	
}
function save_from_computer_l()
{
	jQuery("#hdn_image_path_l").val(file_path_l);
	jQuery("#hdn_image_id_l").val("");
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG?>/m/location/"+ file_path_l +"' class='displayimg'>";
	jQuery('#files_l').html(img +"<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/delete.gif' id='"+file_path_l+"' onclick='rm_image_l(this.id)'  /></div></div></div>");
				
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
	
	jQuery('#country_l').change(function(){
		var change_value=this.value;
		if(change_value == "Canada")
		{
			 jQuery("#state_l").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
		}
		else
		{
		   jQuery("#state_l").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ' >NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>"); 
		}
    });
jQuery('#addhoursid').click(function(){
         jQuery(".timeclass").show();
        jQuery(".addhoursdiv").hide();
        jQuery(".hoursdata").hide();

     var arr_mon_hdn = jQuery("#monhdn").val().split("-");
     var arr_tue_hdn = jQuery("#tuehdn").val().split("-");
     var arr_wed_hdn = jQuery("#wedhdn").val().split("-");
     var arr_thu_hdn = jQuery("#thuhdn").val().split("-");
     var arr_fri_hdn = jQuery("#frihdn").val().split("-");
     var arr_sat_hdn = jQuery("#sathdn").val().split("-");
     var arr_sun_hdn = jQuery("#sunhdn").val().split("-");
     
      jQuery("#defaultValueFrom").val('');
      jQuery("#defaultValueTo").val('');
      jQuery(".weelclass").removeAttr("checked");
     
     if(arr_mon_hdn[0] == "mon")
     {
             jQuery("#monspan").hide();
        
          jQuery("#mon").removeAttr( "checked" );    
     }
     if(arr_tue_hdn[0] == "tue")
     {
             jQuery("#tuespan").hide();
            jQuery("#tue").removeAttr( "checked" ); 
     }
     if(arr_wed_hdn[0] == "wed")
     {
             jQuery("#wedspan").hide();
             
             jQuery("#wed").removeAttr( "checked" ); 
     }
     if(arr_thu_hdn[0] == "thu")
     {
             jQuery("#thuspan").hide();
           jQuery("#thu").removeAttr( "checked" );
            
     }
     if(arr_fri_hdn[0] == "fri")
     {
             jQuery("#frispan").hide();
             jQuery("#fri").removeAttr( "checked" );
     }
     if(arr_sat_hdn[0] == "sat")
     {
             jQuery("#satspan").hide();
             jQuery("#sat").removeAttr( "checked" );
     }
     if(arr_sun_hdn[0] == "sun")
     {
             jQuery("#sunspan").hide();
             jQuery("#sun").removeAttr( "checked" );
     }
     
        var i=1;
      
        jQuery(".weekspan").each(function(index){
            
        
            if (jQuery(this).css('display') == 'none') {
                    
              }
              else
                  {
                      
                       if(jQuery("input[class=weelclass]:checked").length == "0")
                           {
                
                                jQuery("#addhourssaveid").attr("disabled", "disabled");
                                jQuery("#addhourssaveid").addClass("disabled");
                                jQuery("#addhourssaveid").css("color","#ABABAB !important");
                        
                           }
                           else
                           {
                               jQuery("#addhourssaveid").removeAttr("disabled");
                               jQuery("#addhourssaveid").removeClass( "disabled" );
                               jQuery("#addhourssaveid").css("color","#0066FF !important");
                           }    
                      
                  }
                  
        });
         
     });
     jQuery('#addhourscancelid').click(function(){
         jQuery(".timeclass").hide();
         jQuery(".addhoursdiv").show();
         
         //alert(jQuery('.hoursdata').contents().length);
         if(jQuery('.hoursdata').contents().length == "17")
             {
                jQuery(".hoursdata").hide(); 
             }
             else
                 {
                     jQuery(".hoursdata").show();
                 }
         
         
     });
     
     jQuery('#addhourssaveid').click(function(){
         
         
         jQuery(".timeclass").hide();
         jQuery(".addhoursdiv").show();
         jQuery(".hoursdata").show();
         //var from=jQuery("#addhourdata").attr("from");
         //var to=jQuery("#addhourdata").attr("to");
         var from=jQuery("#defaultValueFrom").val();
         var to=jQuery("#defaultValueTo").val();
         //alert(from + to);
         var checkboxval="";
         var totalchecked=jQuery('input[class=weelclass]:checked').size();
         
         
         jQuery("input[class=weelclass]:checked").each(function(index) {
           
        
       var weekname= jQuery(this).val().toLowerCase();
       jQuery("#"+weekname+"hdn").val(weekname+"-"+from+"-"+to);
       jQuery("#"+weekname+"hdn").attr("from",from);
       jQuery("#"+weekname+"hdn").attr("to",to);
       
        checkboxval = jQuery(this).val();
       
        //alert(from + to + checkboxval);
                jQuery(".hoursdata").append("<div style='float:left;' id='"+jQuery(this).val()+"'><div style='width: 300px;float: left;line-height: 48px;'><span id='dis_"+ checkboxval +"'>From : " + from.toUpperCase() +" To : " + to.toUpperCase() + " - "+ checkboxval+"</span></div><div style='float: left;width: 30px;margin-bottom:7px;line-height: 31px'><a href='javascript:void(0)'  class='removeclass closebuttonclass' id='remove_"+ checkboxval+"'></a></div><div>");
               
            });
         
            if(jQuery(".removeclass").length == "7")
                {
                    jQuery("#addhoursid").attr("disabled", "disabled");
                    jQuery("#addhoursid").addClass("disabled");
                    jQuery("#addhoursid").css("color","#ABABAB !important");
                }
                else
                    {
                        
                        jQuery("#addhoursid").removeAttr("disabled");
                        jQuery("#addhoursid").removeClass( "disabled" );
                        jQuery("#addhoursid").css("color","#0066FF !important");
                    }
                    
           
           
           
           
        
         
     });
     jQuery("a[id^='remove_']").live("click",function(){
//alert("in");
   var arr = jQuery(this).attr("id").split("_");
   //alert(arr);
    var cid= arr[1];
       
         //jQuery("#dis_"+cid).remove();
           // jQuery("#remove_"+cid).remove();
           
           jQuery("#"+cid).remove();
            var weekname= arr[1].toLowerCase();
            jQuery("#"+weekname+"hdn").val("0");
            jQuery("#"+weekname+"hdn").attr("from","");
            jQuery("#"+weekname+"hdn").attr("to","");
            jQuery("#"+weekname+"span").show();
            jQuery("#"+weekname).removeAttr("checked");
            jQuery(".weeknamehdn").each(function(index){
               var arr_val=jQuery(this).val().split("-");
               
               if(jQuery(this).val() == "" || arr_val[0] == "0" )
                   {
                       
                       jQuery("#addhoursid").attr("disabled", false);
                       jQuery("#addhoursid").removeClass( "disabled" );
                       jQuery("#addhoursid").css("color","#0066FF !important");
                   }
                   
                   
            });
           
                   if(jQuery(".removeclass").length == "0")
                    {
                        jQuery(".hoursdata").hide();
                    }
                else
                    {
                        jQuery(".hoursdata").show();
                    
                    }
   
    });
    jQuery(".weelclass").change(function(){
        from(); 
    });
     jQuery("#defaultValueFrom").blur(function(){
        from();
    });
    jQuery("#defaultValueTo").blur(function(){
        from();
    });
    jQuery("#defaultValueFrom").change(function(){
        from();
    })
    jQuery("#defaultValueTo").change(function(){
        from();
    });
    function from()
    {
        
        var from=jQuery("#defaultValueFrom").val();
        var to=jQuery("#defaultValueTo").val();
        var dateReg = /^(1[012]|[1-9]):[0-5][0-9](\\s)?(am|pm)+$/;
         var flag_textbox="true";   
        if(from == "" || to=="")
        {
            //alert("nathi avtu");
            //$("#addhourssaveid").attr("disabled", "disabled"); 
            flag_textbox="false"; 
        }
        else
        {
            
             if(!dateReg.test(from) || !dateReg.test(to)) {
                //$("#addhourssaveid").attr("disabled", "disabled"); 
                flag_textbox="false"; 
             }
             else
                 {
                    flag_textbox="true"; 
                 }
                 
                 
        }
        var flag_checkbox="true";
        jQuery(".weelclass").each(function(index){
            var totalchecked=jQuery('input[class=weelclass]:checked').size();
            
                if(totalchecked == 0)
                {
                   //$("#addhourssaveid").attr("disabled", "disabled"); 
                   flag_checkbox="false";
                }
                else
                {
                    //$("#addhourssaveid").removeAttr("disabled");
                    flag_checkbox="true";
                }
           
        });
         if(flag_textbox == "false"  || flag_checkbox == "false")
            {
                jQuery("#addhourssaveid").attr("disabled", "disabled"); 
                jQuery("#addhourssaveid").addClass("disabled");
                jQuery("#addhourssaveid").css("color","#ABABAB !important");
            }
            else
            {
                jQuery("#addhourssaveid").removeAttr("disabled");
                 jQuery("#addhourssaveid").removeClass( "disabled" );
                jQuery("#addhourssaveid").css("color","#0066FF !important");
            }
    }
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}

jQuery('#first_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc1").val(cat_first_id);
	jQuery("#hdnlcat1").val(jQuery("#first_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=1&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#first_cat_second_level").remove();
			jQuery("#first_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#first_cat_first_level").after(obj.html);
				bind_change_event();
			}
			
		}
    });
});

jQuery('#second_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc2").val(cat_first_id);
	jQuery("#hdnlcat2").val(jQuery("#second_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=2&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#second_cat_second_level").remove();
			jQuery("#second_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#second_cat_first_level").after(obj.html);
				
				jQuery("#second_cat_second_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
				
				bind_change_event();
			}
			
		}
    });
});

jQuery('#third_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc3").val(cat_first_id);
	jQuery("#hdnlcat3").val(jQuery("#third_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=3&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#third_cat_second_level").remove();
			jQuery("#third_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#third_cat_first_level").after(obj.html);
				
				jQuery("#third_cat_second_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
				jQuery("#third_cat_second_level option[value='"+jQuery("#hdnlc2").val()+"']").remove();

				bind_change_event();
			}
			
		}
    });
});

function bind_change_event()
{

	jQuery('#first_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc1").val(cat_second_id);
		jQuery("#hdnlcat1").val(jQuery("#first_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=1&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#first_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#first_cat_second_level").after(obj.html);
					//bind_change_event();
				}
				else
				{
					jQuery("#first_cat_second_level").next().html("");
					jQuery("#first_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
					jQuery("#first_lc_delete").css("display","block");
					jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
					jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
					
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#first_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc1").val(cat_third_id);
		jQuery("#hdnlcat1").val(jQuery("#first_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#first_cat_third_level").next().html("");
		jQuery("#first_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
		jQuery("#first_lc_delete").css("display","block");
		jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
		jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
	});

	jQuery('#second_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc2").val(cat_second_id);
		jQuery("#hdnlcat2").val(jQuery("#second_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=2&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#second_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#second_cat_second_level").after(obj.html);
					
					jQuery("#second_cat_third_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
					
					//bind_change_event();
				}
				else
				{
					jQuery("#second_cat_second_level").next().html("");
					jQuery("#second_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
					if(jQuery("#add_cat").attr("total")!=3)
					{
						jQuery("#add_cat_tr").css("display","block");
					}
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#second_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc2").val(cat_third_id);
		jQuery("#hdnlcat2").val(jQuery("#second_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#second_cat_third_level").next().html("");
		jQuery("#second_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
		if(jQuery("#add_cat").attr("total")!=3)
		{
			jQuery("#add_cat_tr").css("display","block");
		}
	});

	jQuery('#third_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc3").val(cat_second_id);
		jQuery("#hdnlcat3").val(jQuery("#third_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=3&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#third_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#third_cat_second_level").after(obj.html);
					
					jQuery("#third_cat_third_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
					jQuery("#third_cat_third_level option[value='"+jQuery("#hdnlc2").val()+"']").remove();
					
					//bind_change_event();
				}
				else
				{
					jQuery("#third_cat_second_level").next().html("");
					jQuery("#third_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
					if(jQuery("#add_cat").attr("total")!=3)
					{
						jQuery("#add_cat_tr").css("display","inline-block");
					}
					
					//jQuery("#third_lc").css("display","none");
					jQuery("#loc_cat_3").css("display","none");
					jQuery("#third_lc_delete").css("display","block");
					jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
					jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
					
					if(jQuery("#hdnlc1").val()!="" && jQuery("#hdnlc2").val()!="" && jQuery("#hdnlc3").val()!="")
                                        {
                                            jQuery("#add_cat_tr").css("display","none");
                                        }
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#third_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	    var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc3").val(cat_third_id);
		jQuery("#hdnlcat3").val(jQuery("#third_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#third_cat_third_level").next().html("");
		jQuery("#third_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
		if(jQuery("#add_cat").attr("total")!=3)
		{
			jQuery("#add_cat_tr").css("display","block");
		}
		
		//jQuery("#third_lc").css("display","none");
		jQuery("#loc_cat_3").css("display","none");
		jQuery("#third_lc_delete").css("display","block");
		jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
		jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
		
                if(jQuery("#hdnlc1").val()!="" && jQuery("#hdnlc2").val()!="" && jQuery("#hdnlc3").val()!="")
                {
                    jQuery("#add_cat_tr").css("display","none");
                }
	});
	
}
jQuery("#add_cat").live("click",function(){
	var total=parseInt(jQuery(this).attr("total"));

	if(total==1)
	{
		
		
		if(jQuery("#hdnlcat1").val()=="" && jQuery("#hdnlcat3").val()!="")
		{
			//jQuery("#loc_cat_1").css("display","table-row");
			jQuery("#first_lc").css("display","block");
			
			jQuery("#second_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat2").val()=="")
		{
			jQuery("#second_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat3").val()=="")
		{
			jQuery("#third_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#second_lc").css("display","none");
		}
	}
	else if(total==2)
	{
		//jQuery("#second_lc").css("display","none");
		jQuery("#loc_cat_2").css("display","none")
		
		if(jQuery("#hdnlcat1").val()=="" && jQuery("#hdnlcat3").val()!="")
		{
			//jQuery("#loc_cat_1").css("display","table-row");
			jQuery("#first_lc").css("display","block");
			jQuery("#second_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat2").val()=="")
		{
			jQuery("#second_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat3").val()=="")
		{
			jQuery("#third_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#second_lc").css("display","none");
		}
	}
	if(jQuery("#hdnlcat1").val()!="")
	{
		jQuery("#first_lc_delete").css("display","block");
		jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
		jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
	}
	if(jQuery("#hdnlcat2").val()!="")
	{
		jQuery("#second_lc_delete").css("display","block");
		jQuery("#second_selected_cat").text(jQuery("#hdnlcat2").val());
		jQuery("#second_selected_cat_delete").attr("catid",jQuery("#hdnlc2").val());
	}
	if(jQuery("#hdnlcat3").val()!="")
	{
		jQuery("#third_lc_delete").css("display","block");
		jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
		jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
	}
	jQuery(this).attr("total",total+1);
	jQuery("#add_cat_tr").css("display","none");
});
jQuery("#first_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc1").val("");
	jQuery("#hdnlcat1").val("");
	jQuery("#first_lc_delete").css("display","none");
	/*
	jQuery("#first_lc").css("display","block");
	jQuery("#second_lc").css("display","none");
	jQuery("#third_lc").css("display","none");
	*/
	jQuery("#first_cat_second_level").remove();
	jQuery("#first_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="" )
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");		
	}
	
});
jQuery("#second_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc2").val("");
	jQuery("#hdnlcat2").val("");
	jQuery("#second_lc_delete").css("display","none");
	/*
	jQuery("#second_lc").css("display","block");
	jQuery("#first_lc").css("display","none");
	jQuery("#third_lc").css("display","none");
	*/
	jQuery("#second_cat_second_level").remove();
	jQuery("#second_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="")
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");
	}
});
jQuery("#third_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc3").val("");
	jQuery("#hdnlcat3").val("");
	jQuery("#third_lc_delete").css("display","none");
	/*
	jQuery("#third_lc").css("display","block");
	jQuery("#first_lc").css("display","none");
	jQuery("#second_lc").css("display","none");
	*/
	jQuery("#third_cat_second_level").remove();
	jQuery("#third_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="")
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");
	}
});	
jQuery("#import_from_website").click(function(){
	jQuery("#upload_business_logo").css("display","none");
	jQuery("#import_iframe").css("display","block");
});
function submitImportform(obj)
{	
	//alert("in call submit function");
	//jQuery('#url-form').submit();
	//jQuery('#url-form').submit(getImagesFromUrl);
	//getImagesFromUrl();
	//alert("out call submit function");
}
jQuery("#btnBacktochangepassword").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#change_password").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","block");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="change_password";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
									
});
jQuery("#btnBacktobusinesspage").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#business_page").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","block");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="business_page";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
	
});
jQuery("#btnBacktoupdateprofile").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#update_profile").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","block");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="update_profile";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktoaboutus").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#about_us").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","block");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="about_us";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktobusinesslogo").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#business_logo").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","block");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="business_logo";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktobusinesslogo").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#business_logo").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","block");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="business_logo";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktoaddlocation").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#add_location").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","block");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="add_location";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktolocationhour").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#location_hour").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","block");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="add_location";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktolocationcategory").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#location_category").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","block");
	jQuery(".complete_location_image").css("display","none");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="add_location";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery("#btnBacktolocationimage").click(function(){
	jQuery("li.uiStep").removeClass("uiStepSelected");
	jQuery("li#location_image").addClass("uiStepSelected");
	
	jQuery(".complete_change_password").css("display","none");
	jQuery(".complete_business_page").css("display","none");
	jQuery(".complete_update_profile").css("display","none");
	jQuery(".complete_about_us").css("display","none");
	jQuery(".complete_business_logo").css("display","none");
	jQuery(".complete_add_location").css("display","none");
	jQuery(".complete_location_hour").css("display","none");
	jQuery(".complete_location_category").css("display","none");
	jQuery(".complete_location_image").css("display","block");
	jQuery(".complete_location_additional_image").css("display","none");
	
	var tab="add_location";
	var logout_link=jQuery("#logout_ele").attr("href");
	logout_link1=logout_link.split("?"); 
	jQuery("#logout_ele").attr("href",logout_link1[0] + "?last_tab="+tab);
});
jQuery(document).ready(function(){
	
	//alert("hi");
	var tab = qs["tab"];
	//alert(tab);
	/*
	if(tab=="change_password")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#change_password").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","block");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");

	}
	else if(tab=="business_page")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#business_page").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","block");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
		
	}
	else if(tab=="update_profile")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#update_profile").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","block");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
	}
	else if(tab=="about_us")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#about_us").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","block");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
	}
	else if(tab=="business_logo")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#business_logo").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","block");
		jQuery(".complete_add_location").css("display","none");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
	}
	else if(tab=="add_location")
	{
		jQuery("li.uiStep").removeClass("uiStepSelected");
		jQuery("li#add_location").addClass("uiStepSelected");
		
		jQuery(".complete_change_password").css("display","none");
		jQuery(".complete_business_page").css("display","none");
		jQuery(".complete_update_profile").css("display","none");
		jQuery(".complete_about_us").css("display","none");
		jQuery(".complete_business_logo").css("display","none");
		jQuery(".complete_add_location").css("display","block");
		jQuery(".complete_location_image").css("display","none");
		jQuery(".complete_location_additional_image").css("display","none");
	}
	*/
	var new_url=window.location;
	var new_url = new String(new_url);
	//alert(new_url);
	new_url=new_url.split("?");
	//alert(new_url[0]);
	history.pushState('', '', new_url[0]);
	
	if(typeof tab === 'undefined')
	{
		tab="change_password";
	}
	
	jQuery("#logout_ele").attr("href",jQuery("#logout_ele").attr("href")+ "?last_tab="+tab);
	
});
var qs = (function(a) {
    if (a == "") return {};
    var b = {};
    for (var i = 0; i < a.length; ++i)
    {
        var p=a[i].split('=');
        if (p.length != 2) continue;
        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
    return b;
})(window.location.search.substr(1).split('&'));
jQuery('.notification_tooltip').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
});
				
</script>
<?php
}
?>
