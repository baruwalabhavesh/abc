<div id="headerContainer">
<div id="logo"><a href="<?=WEB_PATH?>/admin"><img src="<?=ASSETS_IMG?>/a/logo-admin.png" width="340" height="44" border="0px" alt="ScanFlip Logo"></a><!--end of logo--></div>
<div id="menuContainer">
  <div id="menuBar">
  <ul>
  <li><a href="index.php">Home</a> </li>
  <li><a href="merchants.php">Merchants</a></li>
  <li><a href="users.php">Users</a></li>
  <li><a href="#">Pages</a></li>
  <li> <a href="categories.php">Categories</a></li>
  <li><a href="store-locations.php">Store Locations</a></li> 
  <li><a href="campaigns.php">Campaigns  Summaray </a></li>
  <li><a href="rewards-management.php">Rewards Management</a></li>
  <li><a href="payment.php">Payment Information</a></li>
  <li><a href="logout.php">Logout</a> 
  </ul>
  <!--end menuBar--></div>
<!--end menuContainer--></div>
<!--end of headerConatiner--></div>
<!-- Start Message box popup -->
<div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
    <div id="NotificationloaderBackDiv" class="divBack">
    </div>
    <div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">

        <div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
             >

            <div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto;box-shadow:none">
                <img src="<?= ASSETS_IMG ?>/128.GIF" style="display: block;" id="image_loader_div"/>
            </div>
        </div>
    </div>
</div>
<!-- End Message box popup -->


 <script>
function open_loader()
{
	//alert("inn");
		popup_name = 'Notificationloader';
	jQuery("#" + popup_name + "FrontDivProcessing").css("display","block");
	jQuery("#" + popup_name + "PopUpContainer").css("display","block");
	jQuery("#" + popup_name + "BackDiv").css("display","block");
}
function close_loader()
{
			popup_name = 'Notificationloader';
	jQuery("#" + popup_name + "FrontDivProcessing").css("display","none");
	jQuery("#" + popup_name + "PopUpContainer").css("display","none");
	jQuery("#" + popup_name + "BackDiv").css("display","none");
}
  </script>
