<?php
/******** 
@USE : before footer, download android and iphone app block
@PARAMETER : 
@RETURN : 
@USED IN PAGES : include in all files
*********/
  $web_path=WEB_PATH."/";
  
  $pageURL = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  
  $pageURLs=explode("?",$pageURL);
  $pageURL= $pageURLs[0];
  
  //echo $web_path."index.php";
  //echo $pageURL."index.php";
  //echo $_REQUEST['campaign_id'];
  if($web_path!=$pageURL && $web_path."index.php"!=$pageURL && !isset($_REQUEST['campaign_id']))
  {
  ?>
	  <div class="b_footer">
		<div class="left">
			Discover, save and redeem deals every day on your iPhone or Android devices with ScanFlip App.
		</div>
		<div class="right">
			<div class="left1">
				<a target="_new" href="<?php echo $client_msg["index"]["android_app_link"];?>"><span class="icon-android"></span>Download Android App</a>
			</div>
			<div class="right1">
				<a target="_new" href="<?php echo $client_msg["index"]["iphone_app_link"];?>"><span class="icon-apple"></span>Download iPhone App</a>
			</div>
		</div>
	  </div>
  <?php
  }
?> 
