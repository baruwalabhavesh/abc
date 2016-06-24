<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("redeemption_fee_charge", $array_where);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
</head>

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
                    <h2>Edit Redeem Fee Charge</h2>
	<form action="process.php" method="post">
		<table width="75%" align="center"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
		  <tr>
		    <th align="right">&nbsp;</th>
		    <th align="left" style="color:#FF0000; "><?=$_SESSION['msg']?></th>
	      </tr>
		  
		  <tr>
			<th width="50%" align="right">Start Value : </th>
                        <th width="50%" align="left"><input type="text" id="start_value" name="start_value" value="<?=$RS->fields['start_value']?>" /></th>
		  </tr>
		   <tr>
			<th width="50%" align="right">End Value : </th>
                        <th width="50%" align="left"><input type="text" id="end_value" name="end_value" value="<?=$RS->fields['end_value']?>" /></th>
		  </tr>
		   <tr>
			<th align="right">Type: </th>
                        <th align="left">
							<select id="type" name="type">
								<option <?php if($RS->fields['type']=="amount") echo "selected" ?> value="amount">Amount</option>
								<option <?php if($RS->fields['type']=="percentage") echo "selected" ?> value="percentage">Percentage</option>
							</select>
						</th>
		  </tr>
		  <tr>
		<th width="50%" align="right">Amount : </th>
                        <th width="50%" align="left"><input type="text" id="amount_value" name="amount_value" value="<?=$RS->fields['amount_value']?>" /></th>
		  </tr>
                  
		  <tr>
			<td>&nbsp;</td>
			<td align="left">
                        <input type="hidden" name="id" value="<?=$RS->fields['id']?>" />
			<input type="submit" id="btnupdate_redeem_fee" name="btnupdate_redeem_fee" value="Save" />
                        <input type="submit" id="can_redeem_fee" name="can_redeem_fee" value="Cancel" />
			</td>
		  </tr>
		 
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


<?
$_SESSION['msg'] = "";
?>

</body>
</html>

<script type="text/javascript">
            
    
jQuery("#btnupdate_redeem_fee").click(function(){ 
     var numericReg = /[0-9]/;
	 var floatReg = /^[-+]?[0-9]*\.?[0-9]+$/;
    alert_msg="";
    var flag="true";
    
    var start_value=parseFloat(jQuery("#start_value").val());
	var end_value=parseFloat(jQuery("#end_value").val());
	var amount=parseFloat(jQuery("#amount_value").val());
    
	 
    if(!floatReg.test(start_value)) 
    {    
		alert_msg+="* Please Enter Valid start value.\n";
		flag="false";
    } 
   
	if(!floatReg.test(end_value)) 
    {    
		alert_msg+="* Please Enter Valid end value.\n";
		flag="false";
    }

	if(!floatReg.test(amount)) 
    {    
		alert_msg+="* Please Enter Valid amount.\n";
		flag="false";
    }
	
    if(flag=="true")
    {
        return true;
    }
    else
    {
        alert(alert_msg);
        return false;
    }
});
    
</script>