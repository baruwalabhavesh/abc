<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();
//$objJSON = new JSON();

$admin_setting_arr = array();
$admin_setting_data = $objDB->Show("admin_settings");

if(isset($_REQUEST['btnsave_general_setting']))
{
	
	if(isset($_REQUEST['server_path']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 14;
		$array_values['value'] = $_REQUEST['server_path'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['web_path']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 15;
		$array_values['value'] = $_REQUEST['web_path'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	
}

if(isset($_REQUEST['btnsave_api_setting']))
{
	
	if(isset($_REQUEST['gcm_api_key']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 17;
		$array_values['value'] = $_REQUEST['gcm_api_key'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['timezone_api_key']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 16;
		$array_values['value'] = $_REQUEST['timezone_api_key'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['locu_api_key']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 13;
		$array_values['value'] = $_REQUEST['locu_api_key'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	
}

if(isset($_REQUEST['btnsave_email_setting']))
{
	
	if(isset($_REQUEST['smtp_host']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 18;
		$array_values['value'] = $_REQUEST['smtp_host'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['smtp_username']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 19;
		$array_values['value'] = $_REQUEST['smtp_username'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['smtp_password']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 20;
		$array_values['value'] = md5($_REQUEST['smtp_password']);
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['smtp_port']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 22;
		$array_values['value'] = $_REQUEST['smtp_port'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['email_batch_size']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 21;
		$array_values['value'] = $_REQUEST['email_batch_size'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['default_admin_email']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 23;
		$array_values['value'] = $_REQUEST['default_admin_email'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['administrator_name']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 24;
		$array_values['value'] = $_REQUEST['administrator_name'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['email_method']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 25;
		$array_values['value'] = $_REQUEST['email_method'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['smtp_ssl_tls']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 26;
		$array_values['value'] = $_REQUEST['smtp_ssl_tls'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['smtp_debug_mode']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 27;
		$array_values['value'] = $_REQUEST['smtp_debug_mode'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
}
if(isset($_REQUEST['btnsave_bounce_email_setting']))
{
	
	if(isset($_REQUEST['default_site_bounce_email']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 28;
		$array_values['value'] = $_REQUEST['default_site_bounce_email'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['server_type']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 29;
		$array_values['value'] = $_REQUEST['server_type'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['server_name']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 30;
		$array_values['value'] = $_REQUEST['server_name'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['server_port']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 31;
		$array_values['value'] = $_REQUEST['server_port'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['username']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 32;
		$array_values['value'] = $_REQUEST['username'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['password']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 33;
		$array_values['value'] = md5($_REQUEST['password']);
		$objDB->Update($array_values, "admin_settings",$where_clause); 
	}
	if(isset($_REQUEST['encryption']))
	{
		$where_clause = $array_values = array();
		$where_clause['id'] = 34;
		$array_values['value'] = $_REQUEST['encryption'];
		$objDB->Update($array_values, "admin_settings",$where_clause); 
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
                        <div ><h2>Config Settings</h2></div>
           <div class="mer_chant">            
            <form method="post">                
				<div class="mer_chant_4">					
					<p>
						<span>General Setting</span><span>&nbsp;:&nbsp;</span>     
					</p>
					<table style="font-size:14px !important">
						<tr>
							<td>
								Server Path : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   14;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="server_path" name="server_path" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Web Path : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   15;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="web_path" name="web_path" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="submit" id="btnsave_general_setting" name="btnsave_general_setting" value="Save"/>
							</td>
						</tr>
					</table>
                
				</div>
				<div class="mer_chant_4">					
					<p>
						<span>API Setting</span><span>&nbsp;:&nbsp;</span>     
					</p>
					<table style="font-size:14px !important">
						<tr>
							<td>
								GCM API Key : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   17;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="gcm_api_key" name="gcm_api_key" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Timezone API Key : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   16;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="timezone_api_key" name="timezone_api_key" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Locu API Key : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   13;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="locu_api_key" name="locu_api_key" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="submit" id="btnsave_api_setting" name="btnsave_api_setting" value="Save"/>
							</td>
						</tr>
					</table>
                
				</div>
				<div class="mer_chant_4">					
					<p>
						<span>Email Setting</span><span>&nbsp;:&nbsp;</span>     
					</p>
					<table style="font-size:14px !important">
						<tr>
							<td>
								Default site admin email : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   23;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="default_admin_email" name="default_admin_email" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Administrator name : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   24;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="administrator_name" name="administrator_name" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Email method : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   25;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<select id="email_method" name="email_method">
									<option <?php if($admin_setting_data->fields['value']=="mail") echo "selected='selected'";?> value="mail">PHP (default)</option>
									<option <?php if($admin_setting_data->fields['value']=="smtp") echo "selected='selected'";?> value="smtp">SMTP</option>
									<option <?php if($admin_setting_data->fields['value']=="sendmail") echo "selected='selected'";?> value="sendmail">Sendmail</option>
									<option <?php if($admin_setting_data->fields['value']=="qmail") echo "selected='selected'";?> value="qmail">Qmail</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								SMTP host : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   18;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="smtp_host" name="smtp_host" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								SMTP Port : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   22;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="smtp_port" name="smtp_port" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								SMTP Username : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   19;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="smtp_username" name="smtp_username" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								SMTP Password : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   20;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="password" id="smtp_password" name="smtp_password" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								SMTP SSL/TLS : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   26;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<select id="smtp_ssl_tls" name="smtp_ssl_tls">
									<option <?php if($admin_setting_data->fields['value']=="off") echo "selected='selected'";?> value="off">Off</option>
									<option <?php if($admin_setting_data->fields['value']=="ssl") echo "selected='selected'";?> value="ssl">SSL</option>
									<option <?php if($admin_setting_data->fields['value']=="tls") echo "selected='selected'";?> value="tls">TLS</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								SMTP debug mode : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   27;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<select id="smtp_debug_mode" name="smtp_debug_mode">
									<option <?php if($admin_setting_data->fields['value']=="Off") echo "selected='selected'";?> value="Off">Off</option>
									<option <?php if($admin_setting_data->fields['value']=="on_errors") echo "selected='selected'";?> value="on_errors">On errors</option>
									<option <?php if($admin_setting_data->fields['value']=="always") echo "selected='selected'";?> value="always">Always</option>
								</select>
							</td>
						</tr>	
						<tr>
							<td>
								Email batch size : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   21;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="email_batch_size" name="email_batch_size" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="submit" id="btnsave_email_setting" name="btnsave_email_setting" value="Save"/>
							</td>
						</tr>
					</table>
                
				</div>
				<div class="mer_chant_4">					
					<p>
						<span>Bounce Email Setting</span><span>&nbsp;:&nbsp;</span>     
					</p>
					<table style="font-size:14px !important">
						<tr>
							<td>
								Default site bounce email : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   28;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="default_site_bounce_email" name="default_site_bounce_email" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Server type : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   29;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<select id="server_type" name="server_type">
									<option <?php if($admin_setting_data->fields['value']=="off") echo "selected='selected'";?> value="off">Off</option>
									<option <?php if($admin_setting_data->fields['value']=="IMAP") echo "selected='selected'";?> value="IMAP">IMAP</option>
									<option <?php if($admin_setting_data->fields['value']=="POP") echo "selected='selected'";?> value="POP">POP</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								Server name : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   30;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="server_name" name="server_name" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Server port : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   31;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="server_port" name="server_port" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Username : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   32;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="text" id="username" name="username" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Password : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   33;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<input type="password" id="password" name="password" size="50" value="<?php echo  $admin_setting_data->fields['value']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Encryption type : 
							</td>
							<td align="left">
								<?php
									$admin_setting_arr = array();
									$admin_setting_arr['id'] =   34;
									$admin_setting_data = $objDB->Show("admin_settings",$admin_setting_arr);
								?>
								<select id="encryption" name="encryption">
									<option <?php if($admin_setting_data->fields['value']=="off") echo "selected='selected'";?> value="off">Off</option>
									<option <?php if($admin_setting_data->fields['value']=="ssl") echo "selected='selected'";?> value="ssl">SSL</option>
									<option <?php if($admin_setting_data->fields['value']=="tls") echo "selected='selected'";?> value="tls">TLS</option>
								</select>
							</td>
						</tr>						
						<tr >
							<td>
								&nbsp;
							</td>
							<td align="left"> 
								<input type="submit" id="btnsave_bounce_email_setting" name="btnsave_bounce_email_setting" value="Save"/>
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
   
</script>