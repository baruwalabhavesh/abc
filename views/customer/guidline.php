<?php
/******** 
@USE : guidelines for scanflip
@PARAMETER : 
@RETURN : 
@USED IN PAGES : location_detail.php
*********/
//require_once("classes/Config.Inc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ScanFlip</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<link href="<?php echo ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">

<body>

<!--start header-->

		<?
		require_once(CUST_LAYOUT."/header.php");
		?>
		<!--end header-->
	
	<div id="content">
	<div class="my_main_div">
	 <div id="contentContainer" class="container guidline_block">
      <div class="services" style="margin-bottom:0px;">
        	<h1>Reviews must be:</h1>
			<ul class="safe">
				<li><span class="bold">Family-friendly - </span>No profanity, threats, prejudiced comments, hate speech, sexually explicit language, or other content that is not appropriate for our community. </li>
				<li><span class="bold">Relevant to other visitors - </span> Content should be relevant, factually correct and should reflect a new experience or interaction with the businesslocation, services/product offered and for visitors research. </li>
				<li>Do not attempt a smear campaign by taking extreme efforts to damage the reputation of a brand or individual or a location, or by inciting the community to do the same.</li>
				<li>No personal political, ethical, or religious opinions, discussion or commentary in reviews.</li>
				<li>No disparaging comments about other reviews, reviewers.</li>
				<li><span class="bold">Original - </span> No substantial quoted material from other sources, including (but not limited to) websites, e-mail correspondence, other reviews, etc.</li>
				<li><span class="bold">Non-commercial - </span> No promotional material of any kind, including self-promotional URLs. We reserve the right to reject any URL, e-mail address, or phone number for any reason.</li>
				<li><span class="bold">Age requirement - </span> No reviews by children under the age of 13.</li>
				<li>No HTML tags and no excessive ALL CAPS, slang, or typographic symbols.</li>
            </ul>
				<p class="normal">Scanflip reserves the right to remove a review at any time for any reason. The reviews posted on Scanflip are individual and highly subjective opinions. The opinions expressed in reviews are those of Scanflip members and not of Scanflip Corp. We do not endorse any of the opinions expressed by reviewers.  We are not affiliated with any establishment listed or reviewed on this web site.</p>

				<p  class="normal">In accordance with our privacy policy, Scanflip does not release anyone's personal contact information.</p>
           
        </div>
		</div>
      
		<?php require_once(CUST_LAYOUT."/before-footer.php");?>
</div><!--end of my_main_div-->
</div><!--end of content-->

<?php require_once(CUST_LAYOUT."/footer.php");?>
</body>
</html>
<?php
$_SESSION['req_pass_msg']="";
?>
