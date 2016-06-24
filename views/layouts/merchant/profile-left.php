<table width="100%"  border="0" cellspacing="2" cellpadding="2" style="margin-top:25px; ">
  <tr>
    <td align="left"><a class="merchant-prfl-link" id="profile-link" link="my-profile.php" href="javascript:void(0);"><?php echo $merchant_msg['index']['manage_profile'];?></a></td>
  </tr>
  <tr>
    <td align="left"><a class="merchant-prfl-link"  id="password-link" link="change-password.php" href="javascript:void(0);"><?php echo $merchant_msg['index']['change_password'];?></a></td>
  </tr>
  <?php 
	if($_SESSION['merchant_info']['merchant_parent'] == 0 )
	{
   ?>
	  <tr>
		<td align="left"><a class="merchant-prfl-link" id="payment-link" link="payment-history.php" href="javascript:void(0);"><?php echo $merchant_msg['index']['payment_history'];?></a></td>
	  </tr>
	<tr>
		<td align="left"><a class="merchant-prfl-link" id="payment-card-link" link="payment-cards.php" href="javascript:void(0);"><?php echo $merchant_msg['index']['payment_cards'];?></a></td>
	  </tr>
  <?php 
	}
  ?>		
</table>
<script type="text/javascript">
	$(".merchant-prfl-link").click(function(){
		var lnk_href=$(this).attr("link");
        jQuery.ajax({
            type:"POST",
			url:'process.php',
			data :'loginornot=true',
			async:false,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="false")     
				{
					window.location.href=obj.link;
				}
				else
				{
					window.location.href='<?=WEB_PATH.'/merchant/'?>'+lnk_href;
					return true;
				}
			}
        });
  });
</script>
