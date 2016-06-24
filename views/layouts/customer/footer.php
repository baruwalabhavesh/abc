<?php
/******** 
@USE : footer
@PARAMETER : 
@RETURN : 
@USED IN PAGES : include in all files
*********/
?>
<div id="footer" class="footer">
	<div class="my_main_div">
		<div class="footer_inner">
			<a href="<?php echo WEB_PATH?>/merchant/index.php" class="business">For Businesses</a>
			<ul>
				<li>
					<a href="<?php echo WEB_PATH?>/terms.php">Terms Of Service</a>	
				</li>
				<li>
					<a href="<?php echo WEB_PATH?>/privacy-assist.php">Privacy Policy</a>
				</li>
				<li>
					<a href="<?php echo WEB_PATH?>/press-release.php">Press Release</a>
				</li>
				<li>
					<a href="#">Support/Help</a> 
				</li>
				<li>
					<a href="<?php echo WEB_PATH?>/contact-us.php">Contact Us</a>
				</li>
			</ul>	
			<div class="footer_social">
				<a href="#" class=" contactus socila-fb" title="Follow us on Facebook" ></a>
				<a href="#" class=" contactus socila-tw" title="Follow us on Twitter" ></a> 
			</div>
			<div class="at_footer">&copy; 2013 Scanflip</div>
		</div>
	</div>
</div>

<script>
function open_loader()
{
	//alert("inn");
	//popup_name = 'Notificationloader';
	popup_name = 'NotificationLoadingData';
	jQuery("#" + popup_name + "FrontDivProcessing").css("display","block");
	jQuery("#" + popup_name + "PopUpContainer").css("display","block");
	jQuery("#" + popup_name + "BackDiv").css("display","block");
}
function close_loader()
{
	//popup_name = 'Notificationloader';
	popup_name = 'NotificationLoadingData';
	jQuery("#" + popup_name + "FrontDivProcessing").css("display","none");
	jQuery("#" + popup_name + "PopUpContainer").css("display","none");
	jQuery("#" + popup_name + "BackDiv").css("display","none");
}
</script>
  <!--end of footer-->
  
  <!---- get body click --->

  
