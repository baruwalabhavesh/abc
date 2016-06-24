<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
if(isset($_REQUEST['btnApprove'])){
	foreach($_REQUEST['id'] as $id){
		$array_values = $where_clause = array();
		$array_values['active'] = 1;
		$where_clause['id'] = $id;
		$objDB->Update($array_values, "customer_user", $where_clause);
	}

}
if(isset($_REQUEST['active']))
{
    $array_values = $where_clause = array();
    $array_values['status'] = $_REQUEST['active'];
    $where_clause['id'] = $_REQUEST['id'];
    $objDB->Update($array_values, "giftcard_order", $where_clause);
}
$sql = "select o.* , c.firstname , c.lastname , g.title  ,g.redeem_point_value , g.card_value from	
		giftcard_order o inner join customer_user c on c.id = o.user_id inner join giftcards g on g.id = o.giftcard_id 
		where  (o.order_date between '".date('Y')."-01-01 00:00:00' and '".date('Y')."-12-31 23:59:59') 
		order by o.id desc limit 0,".$language_msg['common']['bydefalt_entries_per_query'];
		
$sql_values = "select  IFNULL(sum(g.card_value),0) total_card_value , IFNULL(sum(g.redeem_point_value),0)  total_redeem_value from	
		giftcard_order o inner join customer_user c on c.id = o.user_id inner join giftcards g on g.id = o.giftcard_id 
		where  (o.order_date between '".date('Y')."-01-01 00:00:00' and '".date('Y')."-12-31 23:59:59')  and o.status <>0 
		order by o.id desc";


		$RS = $objDB->Conn->Execute($sql);
		$RS_values = $objDB->Conn->Execute($sql_values);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
 <script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS?>/a/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS?>/a/fancybox/jquery.fancybox.css" media="screen" />
<script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/fancybox/jquery_for_popup.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/fancybox/jquery.fancybox.js"></script> 
	
	<style type="text/css" title="currentStyle">
			@import "<?php echo ASSETS_CSS?>/a/demo_page.css";
			@import "<?php echo ASSETS_CSS?>/a/demo_table.css";
		</style>
		
		<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
		/****** navigate to selected page ***************/
		$.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings){
				return {
					"iTotalPages": oSettings._iDisplayLength === -1 ?
						0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
				};
			};
			/************* Get current page ****/
  $.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
  {
    return {
      "iStart":         oSettings._iDisplayStart,
      "iEnd":           oSettings.fnDisplayEnd(),
      "iLength":        oSettings._iDisplayLength,
      "iTotal":         oSettings.fnRecordsTotal(),
      "iFilteredTotal": oSettings.fnRecordsDisplay(),
      "iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
      "iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
    };
  };

			$(document).ready(function() {
			
				var table = $('#example').dataTable(  {
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					 "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                    "iDisplayLength" : <?php echo $language_msg['common']['etries_per_page']; ?>,
					"aaSorting": [],
					 "aoColumns": [
					  { "bSortable": false },
					    { "bSortable": false },
					   { "bSortable": false },
					  { "bSortable": false },
					    { "bSortable": false }
					  ],
					"fnDrawCallback": function(){
	  if(((this.fnPagingInfo().iPage)+1) == this.fnPagingInfo().iTotalPages)
	   {
			jQuery(".get_mores").css("display","block");
	   }
	   else{
	  		jQuery(".get_mores").css("display","none");
	   }
	   jQuery("#current_page").val((this.fnPagingInfo().iPage)+1);
		jQuery("#total_pages").val(this.fnPagingInfo().iTotalPages);
    }

					
				} );
			//	table.fnPageChange(2,true);
				
			} );
		</script>
</head>

<body class="order_list">
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
		
	<form action="" method="post">
	<input type="hidden" name="increase_val" id="increase_val" value="<?php echo $language_msg['common']['bydefalt_entries_per_query']; ?>" />
	<h2>Manage Gift Card Orders </h2>
	<!--edit delete icons table-->
	<div>
	<table align="right"  cellspacing="10px" >
	<tr><td>Filter By : Year <select id="opt_filter_year">
<?php

	for($y = date('Y') - 2; $y <= date('Y'); $y++) {
    ?>
                                        <option value="<?php echo $y; ?>" <?php if (date('Y') == $y) {
                                        echo "selected";
                                    } ?> > <?php echo $y; ?> </option>
                                    <?php }
                                    ?>
            </select>
			&nbsp; month : <select id="opt_filter_month" >
				<option value="0" >---- ALL ----</option>
				<option value="01"  >January</option>
				<option value="02"  >February</option>
				<option value="03" >March</option>
				<option value="04" >April</option>
				<option value="05" >May</option>
				<option value="06" >Jun</option>
				<option value="07" >July</option>
				<option value="08" >August</option>
				<option value="09" >September</option>
				<option value="10" >October</option>
				<option value="11" >November</option>
				<option value="12" >December</option>
			</select>
			&nbsp; Status : <select id="opt_filter_status" >
				<option value="-1" >---- ALL ----</option>
				<option value="1">Shipped</option>
				<option value="2"  >Pending</option>
				<option value="0"  >Cancelled</option>
				</select>
			&nbsp; Ship to : <select id="opt_filter_ship_to" >
			<option value="0" >---- ALL ----</option>
			<option value="USA">USA</option>
			<option value="Canada"  >Canada</option>
			</select>
			<input type="button" name="btn_submit" id="btn_submit" value="Show Result" /></td>
<!--	<td><a href="edit-category.php?id=<?=$Row['id']?>"><img src="<?php echo ASSETS_IMG; ?>/a/icon-edit.png"></a></td>
	<td><a href="#"><img src="<?php echo ASSETS_IMG; ?>/a/icon-delete.png"></a></td>-->
	</tr>
	
	
	</table>
	<input type="hidden" name="hdn_start_limit" id="hdn_start_limit" value="0" />
	<input type="hidden" name="hdn_changed_filter_year" id="hdn_changed_filter_year" value="2014" />
	<input type="hidden" name="hdn_changed_filter_month" id="hdn_changed_filter_month" value="0" />
	<input type="hidden" name="hdn_changed_filter_status" id="hdn_changed_filter_status" value="-1" />
	<input type="hidden" name="hdn_changed_filter_shipto" id="hdn_changed_filter_shipto" value="0" />
	
	<div style="color:#FF0000; "><?=$_SESSION['msg'];?></div>
		<!--end edit delete icons table--></div>
		<br/>
		<div class="ordreslist_container">
		<div class="orderpoints">
			<div><b>Total card value redeemed : </b> <?php echo $RS_values->fields['total_card_value'] ;?></div>
			<div><b>Total scanflip points redeemed : </b> <?php echo $RS_values->fields['total_redeem_value'] ;  ?></div>
		</div>
		<table  width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
		  <thead>
		  <input type="hidden" name="total_pages" id="total_pages" value="" />
		  <input type="hidden" name="current_page" id="current_page" value="" />
		  
		  <tr>
			<th width="10%" align="left">Order No.</th>
			<th width="10%" align="left">Order Date</th>
			<th width="20%" align="left">Order Description</th>
			<th width="8%" align="left">Status</th>
			<th width="20%" align="left">&nbsp;</th>
		  </tr>
		  </thead>
		  <tbody>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><a href="javascript:void(0)" class="order_detail" data-orderid="<?=$Row['id']?>" data-giftcardid="<?=$Row['giftcard_id']?>"><?=$Row['order_number']?></a>
			<div style="display:none" class="orderdetail_td">
			<div class="fancybox_order_detail_wrapper">
		<div id="ship_to">
			<span class="fancybox_orderpopup_title">Ship to :</span> <span id="address"><?php echo $Row['ship_to_address'] ; ?></span>
		</div>
		<div id="order_detail">
			<div id="reward_detail">
				
				<span class="fancybox_orderpopup_title">Reward Detail :</span> <span id="reward_dtail"><?php echo $Row['title']; ?> </span>
			</div>
			<div id="reward_value">
				<span class="fancybox_orderpopup_title">Reward Value :</span> <span id="reward_vlue">$<?php echo $Row['redeem_point_value']; ?></span>
			</div>
			<div id="point_redeemed">
				<span class="fancybox_orderpopup_title">Points Redeemed :</span> <span id="point_redeem"><?php echo $Row['card_value']; ?></span>
			</div>
		</div>
		<div id="notes">
			<span class="fancybox_orderpopup_title">Notes :</span> <span class="note" id="ordernote_<?php echo $Row['id']; ?>">
			<?php 
			$note = $Row['order_note'];
			if(strlen($note)>413)
			{
				$notes = substr($note, 0, 413).'...';
			}
			echo $notes; ?></span>
		</div>
	</div>
	</div>
			<!--<div class="fancybox_order_detail_wrapper">
			<div id="ship_to">
				<div><b>User Name : </b><?php echo $Row['firstname']." ",$Row['lastname']; ?></div>
				<div><b>Shipping Address : </b> <?php echo $Row['ship_to_address'] ; ?></div>
				</div>
				<div id="order_detail">
				<div><b>Order Detail : </b> </div>
				
				<hr/>
				<div ><b><?php echo $Row['title']; ?></b></div>
				<div ><b>Redeem Points Required : </b><?php echo $Row['redeem_point_value']; ?></div>
				<div ><b>Card Value : </b><?php echo $Row['card_value']; ?></div>
				</div>
				<div id	="notes">
				<div><b>Notes : </b></div>
				<hr/>
				<div id="ordernote_<?php echo $Row['id']; ?>"><?php echo $Row['order_note']; ?></div>
				</div>
			</div> -->
			</td>
			<td align="left"><?=$Row['order_date']?></td>
			<td align="left"><?=$Row['title']?></td>
			
			<td align="left"><?php
			if($Row['status'] == 0)
			{
				echo "Cancelled";
			}
			else if($Row['status'] == 2)
			{
				echo "pending";
			}
			else 
			{
				echo "Shipped";
			}
			?></td>
		    <td align="left">
                        <?php
			if($Row['status'] == 0)
			{
			
			}
			else if($Row['status'] == 2)
			{
			?>
			  <a class="change_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Change Status</a><span>&nbsp;|&nbsp;</span>
			  <a class="cancel_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Cancel Order</a><span>&nbsp;|&nbsp;</span>
              <?php } else { ?>        
			  <a class="change_st"  href="javascript:void(0);" data-orderstatus="2" data-orderid="<?=$Row['id']?>">Change Status</a><span>&nbsp;|&nbsp;</span>
			   <a class="cancel_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Cancel Order</a><span>&nbsp;|&nbsp;</span>
			  <?php  } ?>
                          <a href="javascript:void(0)" odr_id="<?php echo $Row['id']; ?>" class="addNote">Note</a>
                    </td> 
		  </tr>
		  <?
		  }
		  ?>
	
		  
		  <?
		  }else{
		  ?>
		  <tr>
			<td colspan="5" align="left">No Order is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
		  </tbody>
		
		</table>
		</div>
		<div align="right">
		  <a class="get_mores" href="javascript:void(0)">Get More ...</a>
		  </div>
		<div id="myDivID_3" style="display:none">
		<input  type="hidden" name="hdn_note_id" id="hdn_note_id" value="" />
		
		<div style="margin:10px"> 
		<div>
	<b> Add Note for related this order. So user can easily track the order </b>
	</div>
	<div style="margin-top:10px;margin-bottom:10px">
	<textarea name="txt_add_note" id="txt_add_note" style="width:452px;height:140px;"></textarea>
	
	</div>
	<div align="center" >
	<input type="button" name="submit_note" id="submit_note" value="Add Note" />
		</div>
		</div>
		</div>
		<div id="order_section" style="display:none">
		 hello
		</div>
	  </form>
	
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>

<?
$_SESSION['msg'] = "";
?>
		
<script>
jQuery(".fancybox-inner #submit_note").live("click",function(){

jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'addordernote=true&order_id='+jQuery(".fancybox-inner #hdn_note_id").val()+'&note='+jQuery(".fancybox-inner #txt_add_note").val(),
                  async:false,
		  success:function(msg)
		  {
		
			jQuery("#ordernote_"+jQuery(".fancybox-inner #hdn_note_id").val()).html(jQuery(".fancybox-inner #txt_add_note").val());
			jQuery.fancybox.close();
                   }
              });
});
jQuery(".addNote").live("click",function(){

	var odr_id = jQuery(this).attr("odr_id");
			jQuery.fancybox({
				//href: this.href,
				//href: $(val).attr('mypopupid'),
				content:jQuery('#myDivID_3').html(),
				width: 400,
				height: 340,
				openEffect : 'elastic',
				openSpeed  : 300,
                                scrolling : 'no',
				closeEffect : 'elastic',
				closeSpeed  : 300,
                                beforeShow:function(){
                                   	jQuery(".fancybox-inner #hdn_note_id").val(odr_id);
										
                                  
                                },
								afterShow:function(){
									jQuery(".fancybox-inner #hdn_note_id").val(odr_id);
								},
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
                                // helpers
			}); 
});

jQuery(".order_detail").live("click",function(){
var ele = jQuery(this).next();
jQuery("#order_section").html(ele.html());
jQuery.fancybox({
				//href: this.href,
				//href: $(val).attr('mypopupid'),
				content:jQuery('#order_section').html(),
				openEffect : 'elastic',
				openSpeed  : 300,
                scrolling : 'no',
				closeEffect : 'elastic',
				closeSpeed  : 300,
				beforeShow:function(){
					jQuery(".fancybox-wrap").addClass("dsiplay_not");
				 //   	jQuery(".fancybox-inner #hdn_note_id").val(odr_id);
				   
				},
				afterShow:function(){
				//	jQuery(".fancybox-inner #hdn_note_id").val(odr_id);
				},
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
                                // helpers
			}); 
/* alert('addordeDetail=true&giftcardid='+jQuery(this).attr("data-giftcardid")+'&orderid='+jQuery(this).attr("data-orderid"));
jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'addordeDetail=true&giftcardid='+jQuery(this).attr("data-giftcardid")+'&orderid='+jQuery(this).attr("data-orderid"),
                  async:false,
		  success:function(msg)
		  {
				alert(msg);
					jQuery("#order_section").html(msg);
                   }
              });
	jQuery.fancybox({
				//href: this.href,
				//href: $(val).attr('mypopupid'),
				content:jQuery('#order_section').html(),
				width: 400,
				height: 340,
				openEffect : 'elastic',
				openSpeed  : 300,
                                scrolling : 'no',
				closeEffect : 'elastic',
				closeSpeed  : 300,
                                beforeShow:function(){
                                 //   	jQuery(".fancybox-inner #hdn_note_id").val(odr_id);
                                   
                                },
								afterShow:function(){
								//	jQuery(".fancybox-inner #hdn_note_id").val(odr_id);
								},
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
                                // helpers
			});  */
});
jQuery("a.get_more").click(function(){
	jQuery("#hdn_start_limit").val(parseInt(jQuery("#hdn_start_limit").val())+25);
	//alert(jQuery("#hdn_start_limit").val());
	var redirecting = jQuery("#current_page").val();
	jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'addnextorders=true&sartlimit='+jQuery("#hdn_start_limit").val(),
          async:false,
		  success:function(msg)
		  {
		   $('#example').dataTable().fnDestroy();
		jQuery("#example tbody").append(msg);
			
	   var table = $('#example').dataTable({
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					 "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                    "iDisplayLength" :  <?php echo $language_msg['common']['etries_per_page']; ?>,
					"aaSorting": [],
					 "aoColumns": [
					  { "bSortable": false },
					    { "bSortable": false },
					   { "bSortable": false },
					  { "bSortable": false },
					    { "bSortable": false }
					  ],
					"fnDrawCallback": function(){
				  if(((this.fnPagingInfo().iPage)+1) == this.fnPagingInfo().iTotalPages)
				   {
				    if(msg != "")
					{
						jQuery(".get_mores").css("display","block");
					}
				   else{
						jQuery(".get_mores").css("display","none");
				   }
				   }
				   else{
						jQuery(".get_mores").css("display","none");
				   }
				 	jQuery("#total_pages").val(this.fnPagingInfo().iTotalPages);
					 jQuery("#current_page").val((this.fnPagingInfo().iPage)+1);
					
				}
		
								
							});
						table.fnPageChange(parseInt(redirecting)-1,true);	
				
                   }
              });
	
	
	
});
jQuery(".change_st").live("click",function(){
var ele = jQuery(this);
jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'change_order_status=true&order_id='+jQuery(this).attr("data-orderid")+'&status='+jQuery(this).attr("data-orderstatus"),
          async:false,
		  success:function(msg)
		  {
		 		if(ele.attr("data-orderstatus") == 2)
				{
				ele.attr("data-orderstatus","1");
					ele.parent().prev("td").text("Pending");
					
				}
				else
				{
					ele.attr("data-orderstatus","2");
					ele.parent().prev("td").text("Shipped");
				}
					
                   }
              });
});
	jQuery("#btn_submit").click(function(){
	//alert('filter_giftcardorder=true&year='+jQuery("#opt_filter_year").val()+'&month='+jQuery("#opt_filter_month").val()+'&status_act='+jQuery("#opt_filter_status").val()+'&sartlimit=0&shipto='+jQuery("#opt_filter_ship_to").val());
	jQuery("#hdn_changed_filter_month").val(jQuery("#opt_filter_month").val());
	jQuery("#hdn_changed_filter_year").val(jQuery("#opt_filter_year").val());
	jQuery("#hdn_changed_filter_status").val(jQuery("#opt_filter_status").val());
	jQuery("#hdn_changed_filter_shipto").val(jQuery("#opt_filter_ship_to").val());
		jQuery.ajax({
				   type:"POST",
				   url:'process.php',
				  data :'filter_giftcardorder=true&year='+jQuery("#opt_filter_year").val()+'&month='+jQuery("#opt_filter_month").val()+'&status_act='+jQuery("#opt_filter_status").val()+'&sartlimit=0&shipto='+jQuery("#opt_filter_ship_to").val(),
				  async:true,
				   success:function(msg)
				   {
					  jQuery("#hdn_start_limit").val(0);
					//	alert(msg);
						jQuery(".ordreslist_container").html(msg);
						 var table = $('#example').dataTable(  {
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					 "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                    "iDisplayLength" : <?php echo $language_msg['common']['etries_per_page']; ?>,
					"aaSorting": [],
					 "aoColumns": [
					  { "bSortable": false },
					    { "bSortable": false },
					   { "bSortable": false },
					  { "bSortable": false },
					    { "bSortable": false }
					  ],
					"fnDrawCallback": function(){
					//alert(((this.fnPagingInfo().iPage)+1) + "===="+this.fnPagingInfo().iTotalPages);
						  if(((this.fnPagingInfo().iPage)+1) == this.fnPagingInfo().iTotalPages)
						   {
								jQuery(".get_mores").css("display","block");
						   }
						   else{
								jQuery(".get_mores").css("display","none");
						   }
						   jQuery("#current_page").val((this.fnPagingInfo().iPage)+1);
							jQuery("#total_pages").val(this.fnPagingInfo().iTotalPages);
						}

					
				} ); 
							
					}
		});

	});
	jQuery("a.get_mores").click(function(){
	
	var changed_status = false;
	var load_from_scratch = 0;
//	alert(jQuery("#hdn_start_limit").val());
	jQuery("#hdn_start_limit").val(parseInt(jQuery("#hdn_start_limit").val())+parseInt(jQuery("#increase_val").val()));
	//alert(jQuery("#hdn_start_limit").val());
	
 if(jQuery("#hdn_changed_filter_month").val() == jQuery("#opt_filter_month").val() && jQuery("#hdn_changed_filter_year").val() == jQuery("#opt_filter_year").val() && jQuery("#hdn_changed_filter_status").val() == jQuery("#opt_filter_status").val() && jQuery("#hdn_changed_filter_shipto").val() == jQuery("#opt_filter_ship_to").val())
 {
	 
 }
 else{
 
   changed_status = true;
   load_from_scratch = 1;
 }
 if(changed_status)
 {
	jQuery("#hdn_start_limit").val(0);
 }
	var redirecting = jQuery("#current_page").val();
	
	jQuery("#hdn_changed_filter_month").val(jQuery("#opt_filter_month").val());
	jQuery("#hdn_changed_filter_year").val(jQuery("#opt_filter_year").val());
	jQuery("#hdn_changed_filter_status").val(jQuery("#opt_filter_status").val());
	jQuery("#hdn_changed_filter_shipto").val(jQuery("#opt_filter_ship_to").val());	
//	alert('<?php echo WEB_PATH; ?>/admin/process.php?addnextorderss=true&sartlimit='+jQuery("#hdn_start_limit").val()+'&year='+jQuery("#opt_filter_year").val()+'&month='+jQuery("#opt_filter_month").val()+'&status_act='+jQuery("#opt_filter_status").val()+'&shipto='+jQuery("#opt_filter_ship_to").val());
	jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'addnextorderss=true&sartlimit='+jQuery("#hdn_start_limit").val()+'&year='+jQuery("#opt_filter_year").val()+'&month='+jQuery("#opt_filter_month").val()+'&status_act='+jQuery("#opt_filter_status").val()+'&shipto='+jQuery("#opt_filter_ship_to").val()+'&load_from_scrtch='+load_from_scratch,
          async:false,
		  success:function(msg)
		  {
	//	  alert(msg);
			   $('#example').dataTable().fnDestroy();
			   if(changed_status)
				{	
					jQuery(".ordreslist_container").html(msg);
				}
				else{
					jQuery("#example tbody").append(msg);
					
				}
			
	   var table = $('#example').dataTable({
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					 "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                    "iDisplayLength" : <?php echo $language_msg['common']['etries_per_page']; ?>,
					"aaSorting": [],
					 "aoColumns": [
					  { "bSortable": false },
					    { "bSortable": false },
					   { "bSortable": false },
					  { "bSortable": false },
					    { "bSortable": false }
					  ],
					"fnDrawCallback": function(){
		//			alert(this.fnPagingInfo().iTotalPages+"==total pages--"+((this.fnPagingInfo().iPage)+1));
				  if(((this.fnPagingInfo().iPage)+1) == this.fnPagingInfo().iTotalPages)
				   {
				    if(msg != "")
					{
						jQuery(".get_mores").css("display","block");
					}
				   else{
						jQuery(".get_mores").css("display","none");
				   }
				   }
				   else{
						jQuery(".get_mores").css("display","none");
				   }
				 	jQuery("#total_pages").val(this.fnPagingInfo().iTotalPages);
					 jQuery("#current_page").val((this.fnPagingInfo().iPage)+1);
					
						}
		
								
							});
						table.fnPageChange(parseInt(redirecting)-1,true);	
				
                   }
              });
	
	
	
});

jQuery(".cancel_st").live("click",function(){
var ele = jQuery(this);
var orderid = jQuery(this).attr("data-orderid");
jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'change_order_status=true&order_id='+jQuery(this).attr("data-orderid")+'&status=0',
          async:false,
		  success:function(msg)
		  {
		 		ele.attr("data-orderstatus","0");
					ele.parent().prev("td").text("Cancelled");
					
					jQuery(".change_st[data-orderid='"+orderid+"']").next().detach();
					jQuery(".cancel_st[data-orderid='"+orderid+"']").next().detach();
					jQuery(".change_st[data-orderid='"+orderid+"']").detach();
					jQuery(".cancel_st[data-orderid='"+orderid+"']").detach();
					
					
                   }
              });
}); 
</script>
</body>
</html>
