<?php

/**
 * @uses verify email address
 * @used in pages :process.php,header.php
 * 
 */

//require_once("../classes/Config.Inc.php");
?>
<!--<script type="text/javascript" src="https://code.jquery.com/jquery-1.6.2.min.js"></script>-->
<link href="<?php echo ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Validate Email </title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
</head>
<body>
<div class="my_main_div">
	<!--start header-->
		<div class="my_inner_div">
		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		</div>
	<!--end header-->	

	<div id="contentContainer">
		<div id="content">
			<table width="100%" cellpadding="20" cellspacing="0" border="0">
				<tr>
					<td bgcolor="white" class="contentblock">
						<multiline label="Description"><p style=" color: black; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">Your email has been verified.</p></multiline>
                   		<h5><p style=" color: black; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">Thank You,</br>Scanflip Support Team.</p></h5>
					</td>
				</tr>
			</table>
		</div><!--end of content-->
		<div style="clear: both"></div>
	</div>
		<!--start footer-->
			<div>
			<?
			require_once(MRCH_LAYOUT."/footer.php");
			?>
			</div>
		<!--end of footer-->
<!--end of contentContainer--></div>

</body>
</html>
