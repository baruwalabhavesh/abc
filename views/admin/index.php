<?php
////require_once("../classes/Config.Inc.php");
check_admin_session();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<link href="<?php echo ASSETS_CSS; ?>/a/template.css" rel="stylesheet" type="text/css">
</head>

<body>
     <div id="container">

              <!---start header---->
	
		<?php
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		<div id="contentContainer">

	
	<div  id="sidebarLeft">
		<?php
		require_once(ADMIN_VIEW."/quick-links.php");
		?>
		<!--end of sidebar Left--></div>

		<div id="content">
	
		
		<?php
		require_once(ADMIN_VIEW."/recent-merchants.php");
		require_once(ADMIN_VIEW."/recent-campaigns.php");
		?>

                     <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>




</body>
</html>
