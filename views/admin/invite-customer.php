<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
//$where_arr= array();
//$RS = $objDB->Show("press_release",$where_arr);
$Sql_new = "select mu.id,mu.business,count(distinct cu.id) cust_total 
from merchant_subscribs ms,merchant_user mu,merchant_groups mg,customer_user cu,locations l
where mu.id=ms.merchant_id and ms.group_id = mg.id and mu.id=mg.merchant_id and ms.user_id =cu.id and mu.id=l.created_by  and mg.location_id=l.id and l.active=1 group by mu.id";  
$RS = $objDB->Conn->Execute($Sql_new);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS ?>/a/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS ?>/a/fancybox/jquery.fancybox.css" media="screen" />
<script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/a/fancybox/jquery.fancybox.js"></script>
</head>
<style>
.head_msg {
    background: none repeat scroll 0 0 #d5d5d5;
    border-radius: 4px;
    font-size: 18px;
    font-weight: bold;
    height: 40px;
    line-height: 2.1;
    text-align: center;
    width: 100%;
}

.content_msg {
    margin-top: 10px;
    padding: 5px;
    text-align: left;
}
.msg_popup_cancel {
    background-color: #e6e6e6 !important;
    background-image: none !important;
    border: 1px solid #d3d3d3 !important;
    border-radius: 4px !important;
    color: #000 !important;
    cursor: pointer;
}
#btnInvite:hover
{
	cursor:pointer;
}
</style>
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
		  <h2>Invite Customers</h2>
	<form action="process.php" method="post">
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	<tr>
		<td>
			<input type="button" value="Invite" name="btnInvite" id="btnInvite" onclick="callpopup()">
		</td>
	</tr>
	
	</table>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   class="tableAdmin">
		   <tr>
			<td colspan="5">&nbsp;<span style="color:#FF0000; "><?=$_SESSION['msg']?></span></td>
			
		  
		  </tr>
		  <tr>
			 <th width="10%" align="left">&nbsp;</th>
			<th width="50%" align="left">Business Name</th>
			<th width="40%" align="left">Total Customers</th>
		   
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
                        //    print_r($RS);
		  ?>
		  <tr>
			 <td align="left"><input type="radio" name="business" value="<?=$Row['id']?>"  /></td>
			<td><?=$Row['business']?></td> 
            <td><?=$Row['cust_total']?></td>
			
		  </tr>
		  <?
		  }
		  ?>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <?
		  }else{
		  ?>
		  <tr>
			<td colspan="5" align="left">No data found.</td>
		  </tr>
		  <?
		  } 
		  ?>
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<?php 
$_SESSION['msg']="";
?>
<div id="message-window" title="Message Box" style="display:none">

        </div>
</body>
</html>
<script>
function callpopup()
{
	if (jQuery("input[name='business']:checked").length > 0)
	{
		var merchant_id=jQuery("input[name='business']:checked").val();
		
		var message="<center><div>Enter Number Of Customer :<input type='text' id='num_of_cust'' name='num_of_cust' /></div></center>";
		var alert_msg = message;
		var head_msg="<div class='head_msg'>Message</div>";
		var footer_msg="<div><hr><input type='button' merchant_id='"+merchant_id+"' value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupInvite' name='popupInvite' class='msg_popup_cancel'></div>";
		var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
		jQuery( "#message-window" ).html(head_msg + content_msg + footer_msg);
										
		jQuery.fancybox({
			content:jQuery('#message-window').html(),
				
			type: 'html',
			openSpeed  : 300,
								
			closeSpeed  : 300,
			// topRatio: 0,
				
			changeFade : 'fast',  
			beforeShow:function() {
					var newWidth = 345; 
					var newHeight = 200;				
					this.width  = newWidth;
					this.height = newHeight;
				},
			helpers: {
				overlay: {
					closeClick: false,
					opacity: 0.3
				} // overlay
				
			}
		}); 
	}
	else
	{
		var message="Please select business";
		var alert_msg = message;
		var head_msg="<div class='head_msg'>Message</div>";
		var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupSelect' name='popupSelect' class='msg_popup_cancel'></div>";
		var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
		jQuery( "#message-window" ).html(head_msg + content_msg + footer_msg);
										
		jQuery.fancybox({
			content:jQuery('#message-window').html(),
				
			type: 'html',
			openSpeed  : 300,
								
			closeSpeed  : 300,
			// topRatio: 0,
				
			changeFade : 'fast',  
			beforeShow:function() {
					var newWidth = 265; 
					var newHeight = 200;				
					this.width  = newWidth;
					this.height = newHeight;
				},
			helpers: {
				overlay: {
					closeClick: false,
					opacity: 0.3
				} // overlay
				
			}
		}); 
	}

	
}

jQuery(".fancybox-inner #popupSelect").live("click",function(){
	 jQuery.fancybox.close(); 
});	

jQuery(".fancybox-inner #popupCancel").live("click",function(){
	 jQuery.fancybox.close(); 
});	

jQuery(".fancybox-inner #popupInvite").live("click",function(){
	//alert(jQuery(".fancybox-inner input[id='num_of_cust']").val());
	//alert(jQuery(this).attr("merchant_id"));
	
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'btnInviteCustomers=yes&merchant_id='+jQuery(this).attr("merchant_id")+"&num_of_cust="+jQuery(".fancybox-inner input[id='num_of_cust']").val(),
		async:false,
		success:function(msg)
		{			 
			var obj = jQuery.parseJSON(msg);		  
			//alert(obj.status);
			if (obj.status=="true")     
			{
				var message="Customers invited successfully.";
				var alert_msg = message;
				var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
				var head_msg="<div class='head_msg'>Message</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupCancel' name='popupCancel' class='msg_popup_cancel'></div>";
				var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
				jQuery( "#message-window" ).html(head_msg + content_msg + footer_msg);
												
				jQuery.fancybox({
					content:jQuery('#message-window').html(),
						
					type: 'html',
					openSpeed  : 300,
										
					closeSpeed  : 300,
					// topRatio: 0,
						
					changeFade : 'fast',  
					beforeShow:function() {
							var newWidth = 265; 
							var newHeight = 200;				
							this.width  = newWidth;
							this.height = newHeight;
						},
					helpers: {
						overlay: {
							closeClick: false,
							opacity: 0.3
						} // overlay
						
					}
				}); 
			}
			else
			{
				jQuery.fancybox.close();
				/*
				var message="Please add number of customer greater than zero";
				var alert_msg = message;
				var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
				var head_msg="<div class='head_msg'>Message</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupCancel' name='popupCancel' class='msg_popup_cancel'></div>";
				var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
				jQuery( "#message-window" ).html(head_msg + content_msg + footer_msg);
												
				jQuery.fancybox({
					content:jQuery('#message-window').html(),
						
					type: 'html',
					openSpeed  : 300,
										
					closeSpeed  : 300,
					// topRatio: 0,
						
					changeFade : 'fast',  
					beforeShow:function() {
							var newWidth = 265; 
							var newHeight = 200;				
							this.width  = newWidth;
							this.height = newHeight;
						},
					helpers: {
						overlay: {
							closeClick: false,
							opacity: 0.3
						} // overlay
						
					}
				}); 
				*/
			}
		}
	});
	
	
});	

</script>
