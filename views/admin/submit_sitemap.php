<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();
//$objJSON = new JSON();

if(isset($_REQUEST['btn_submit_sitemap']))
{
	$sitemap_url = WEB_PATH."/sitemap.xml";
	
	ping($sitemap_url);
	
}
function ping($sitemap_url)
{
	//$objDB = new DB();
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 35;
	$sitemap_campaign = $objDB->Show("admin_settings",$admin_setting_arr);
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 36;
	$sitemap_location = $objDB->Show("admin_settings",$admin_setting_arr);
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 37;
	$sitemap_geositemap = $objDB->Show("admin_settings",$admin_setting_arr);
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 38;
	$sitemap_static = $objDB->Show("admin_settings",$admin_setting_arr);
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 39;
	$sitemap_index = $objDB->Show("admin_settings",$admin_setting_arr);
	
	if($sitemap_campaign->fields['action']==0 && $sitemap_location->fields['action']==0 && $sitemap_geositemap->fields['action']==0 && $sitemap_static->fields['action']==0 && $sitemap_index->fields['action']==0)
	{
		//echo $sitemap_url;
		
		@file_get_contents("http://www.google.com/webmasters/sitemaps/ping?sitemap=" . $sitemap_url);
		@file_get_contents("http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=YahooDemo&url=" . $sitemap_url);
		@file_get_contents("http://submissions.ask.com/ping?sitemap=" . $sitemap_url);
		@file_get_contents("http://www.bing.com/webmaster/ping.aspx?siteMap=" . $sitemap_url);
		
	}
		
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>
<style>

#content div.mer_chant div.mer_chant_4 {
    background: none repeat scroll 0 0 #dddddd;
    border-bottom: 2px solid #ff9900;
    font-size: 18px;
}

#content div.mer_chant div.mer_chant_4 p span {
    font-weight: bold;
}
.mer_chant_4 table tr {
    display: inline-block;
    width: 100%;
}
.mer_chant_4 table tr:last-child td {
    width: 165px;
}
.mer_chant_4 table tr td:first-child {
    width: 170px;
}
#btn_submit_sitemap,#btn_campaign_sitemap,#btn_location_sitemap,#btn_static_sitemap,#btn_locationkml_sitemap,#btn_index_sitemap
{
	cursor:pointer;
	width: 250px;
    text-align: left;
}
}
</style>
<!-- Message box popup -->

<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS ?>/a/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS ?>/a/fancybox/jquery.fancybox.css" media="screen" />

<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/a/fancybox/jquery.fancybox-buttons.js"></script>-->
<script type="text/javascript" src="<?=ASSETS_JS ?>/a/fancybox/jquery.fancybox.js"></script>

<!-- End Message box popup -->

</head>

<body>
     <div id="container">

              <!---start header---->
	
		<?
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		<div id="contentContainer">

	
	<div  id="sidebarLeft">
		<?
		require_once(ADMIN_VIEW."/quick-links.php");
		?>
		<!--end of sidebar Left--></div>

		<div id="content">	
                    <div style="display: block; overflow: hidden;">
                        <div ><h2>Submit Sitemap</h2></div>
           <div class="mer_chant_l">            
            <form method="post">                
				<div class="mer_chant_l">					
					<table style="font-size:14px !important">						
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="submit" id="btn_submit_sitemap" name="btn_submit_sitemap" value="Submit sitemap to search engine"/>
							</td>
							
						</tr>
					</table>
                
				</div>
				<div class="mer_chant_l">					
					<table style="font-size:14px !important">						
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="button" id="btn_campaign_sitemap" name="btn_campaign_sitemap" value="Generate Campaign sitemap"/>
							</td>
							<td align="right"> 
								<div id="campaign_sitemap_message"></div>
							</td>
						</tr>
					</table>
                
				</div>
				<div class="mer_chant_l">					
					<table style="font-size:14px !important">						
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="button" id="btn_location_sitemap" name="btn_location_sitemap" value="Generate location sitemap"/>
							</td>
							<td align="right"> 
								<div id="location_sitemap_message"></div>
							</td>
						</tr>
					</table>
                
				</div>
				<div class="mer_chant_l">					
					<table style="font-size:14px !important">						
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="button" id="btn_static_sitemap" name="btn_static_sitemap" value="Generate Static sitemap"/>
							</td>
							<td align="right"> 
								<div id="static_sitemap_message"></div>
							</td>
						</tr>
					</table>
                
				</div>
				<div class="mer_chant_l">					
					<table style="font-size:14px !important">						
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="button" id="btn_locationkml_sitemap" name="btn_locationkml_sitemap" value="Generate Location kml sitemap"/>
							</td>
							<td align="right"> 
								<div id="geositemap_sitemap_message"></div>
							</td>
						</tr>
					</table>
                
				</div>
				<div class="mer_chant_l">					
					<table style="font-size:14px !important">						
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="button" id="btn_index_sitemap" name="btn_index_sitemap" value="Generate Sitemap Index"/>
							</td>
							<td align="right"> 
								<div id="index_sitemap_message"></div>
							</td>
						</tr>
					</table>
                
				</div>
			</form>
		   </div>		
        </div>
        <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>

</body>
</html>

<script type="text/javascript">

jQuery("#btn_campaign_sitemap").click(function(){
	jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'generate_campaign_sitemap=true',
          async:true,
		  success:function(msg)
		  {
			   var obj = jQuery.parseJSON(msg);
			   jQuery("#campaign_sitemap_message").html("Campaign sitemap created successfully.");
			   //alert(obj.status);
		  }
     });
});
jQuery("#btn_location_sitemap").click(function(){
	jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'generate_location_sitemap=true',
          async:true,
		  success:function(msg)
		  {
			   var obj = jQuery.parseJSON(msg);
			   jQuery("#location_sitemap_message").html("Location sitemap created successfully.");
			   //alert(obj.status);
		  }
     });
}); 
jQuery("#btn_static_sitemap").click(function(){
	jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'generate_static_sitemap=true',
          async:true,
		  success:function(msg)
		  {
			   var obj = jQuery.parseJSON(msg);
			   jQuery("#static_sitemap_message").html("Static sitemap created successfully.");
			   //alert(obj.status);
		  }
     });
});
jQuery("#btn_locationkml_sitemap").click(function(){
	jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'generate_locationkml_sitemap=true',
          async:true,
		  success:function(msg)
		  {
			   var obj = jQuery.parseJSON(msg);
			   jQuery("#geositemap_sitemap_message").html("Location kml sitemap created successfully.");
			   //alert(obj.status);
		  }
     });
});
jQuery("#btn_index_sitemap").click(function(){
	jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'generate_index_sitemap=true',
          async:true,
		  success:function(msg)
		  {
			   var obj = jQuery.parseJSON(msg);
			   jQuery("#index_sitemap_message").html("Index sitemap created successfully.");
			   //alert(obj.status);
		  }
     });
});  
</script>