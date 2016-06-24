<table width="100%"  border="0" cellspacing="2" cellpadding="2" style="margin-top:25px; ">
  <tr>
    <td align="left"><a class="reward-prfl-link" id="manage-reward-zone" link="manage-reward-zone.php" href="javascript:void(0);">Manage Gift Card</a></td>
  </tr>
  <tr>
    <td align="left"><a class="reward-prfl-link"  id="reward-terms" link="reward-terms.php" href="javascript:void(0);">Terms & Conditions</a></td>
  </tr>
<tr>
<td align="left"><a class="reward-prfl-link" id="payment-link" link="manage-campaigns.php" href="javascript:void(0);">Manage Campaigns</a></td>
</tr>
	
</table>
<script type="text/javascript">
	$(".reward-prfl-link").click(function(){
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