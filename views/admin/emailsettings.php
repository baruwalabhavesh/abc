<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$where_arr= array();
$where_arr['is_default'] = 1;
$RS = $objDB->Show("campaigns_template",$where_arr);
if(isset($_REQUEST['action']) == "delete")
{
     $Sql = "delete FROM campaigns_template WHERE id=".$_REQUEST['id'];
        
           $objDB->Conn->Execute($Sql);
           header("Location:campaigntemplates.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
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
		  <h2>Email Settings</h2>
	<form action="process.php" method="post">
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	
	</table>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   >
   <?php
        $admin_settings = array();
	$admin_settings['setting'] = "Email Frequency";
	$RSAdmin = $objDB->Show("admin_settings",$admin_settings);
	$e_s_1 = $RSAdmin->fields['action'];
        
        $admin_settings = array();
	$admin_settings['setting'] = "New Scanflip merchants campaign";
	$RSAdmin = $objDB->Show("admin_settings",$admin_settings);
	$e_s_2 = $RSAdmin->fields['action'];
   
   ?>
		     <tr>
    <td><?php echo  "Email Frequency";?></td>
    <td> 
        <select name="email_frequency">
            <option value="1"  <?php if($e_s_1 == 1) { echo "selected"; } ?> >every 3 hrs</option>
             <option value="2"  <?php if($e_s_1 == 2) { echo "selected"; } ?>  >every 6 hrs</option>
             <option value="3"  <?php if($e_s_1 == 3) { echo "selected"; } ?>  >every 12 hrs</option>
             <option value="4"  <?php if($e_s_1 == 4) { echo "selected"; } ?> >daily</option>
        </select>
    </td>
  </tr>
  <tr>
    <td><?php echo "New Scanflip merchants campaign ";?></td>
    <td> <input type="radio" name="rd_scanflip_merchant_campaign" value="1"  <?php if($e_s_2==1) echo "checked"; ?>  > ON &nbsp;&nbsp;
        <input type="radio" name="rd_scanflip_merchant_campaign" value="0"  <?php if($e_s_2==0) echo "checked"; ?> > OFF</td>
  </tr>
   <tr>
    <td>&nbsp;</td>
    <td>
		<input type="submit" name="btnUpdateemailsettings" value="Save" id="btnUpdateemailsettings" >
                <script>function btncanprofile(){                                                
                                                window.location="<?=WEB_PATH?>/my-deals.php";}
                                                
                                                </script>
         <input type="submit" name="btncancelemailsettings" value="Cancel" onClick="btncanprofile()"  >
	</td>
  </tr>
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<?php $_SESSION['msg']=""; ?>

</body>
</html>
