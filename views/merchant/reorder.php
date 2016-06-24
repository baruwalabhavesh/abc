<?php 
check_merchant_session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ScanFlip | Manage Pricelists</title>
	<?php require_once(MRCH_LAYOUT."/head.php"); ?>
	<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/pricelist.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/reorder.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">

	<!--<script src="<?php echo ASSETS ?>/pricelist/js/jquery.js"></script>-->
	<script src="<?php echo ASSETS ?>/pricelist/js/bootstrap.min.js"></script>
  	<script src="<?php echo ASSETS ?>/pricelist/js/jquery-ui.js"></script>
	<script src="<?php echo ASSETS ?>/pricelist/js/jquery.dataTables.min.js"></script>

	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
	<!---------start header--------------->	
	<div>
	<?
	// include header file from merchant/template directory 
	require_once(MRCH_LAYOUT."/header.php");
	?>
	<!--end header-->
	</div>
	<div id="contentContainer">
		
	<div id="content">	
	<h4 id="header-title" >Reorder Price-list Display</h4>
	<div class="reorder_container">
		
		 <form action="saveorder.php" method="post" class="reorder_form hidden">
		 	<input type="hidden" name="key" id="reorder_key_array">
		 	<input type="hidden" name="value" id="reorder_value_array">
		 </form>
		<div class="grid_container clearfix">
				
	    	
	    	
			<table class="table display" id="pricelist_table">
				<thead>
					<tr>
						<th>Name</th>
						<th>Display Order</th>

						<th>Re-order</th>
				</tr>
				</thead>
				<tbody>
					
					<?php 
						$errors=0;
						//include("dbconnect.php");
						//mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
						//mysql_select_db(DATABASE_NAME);
						$TableName= "pricelists";
						if($errors ==0){
							$SQLstring = 'SELECT * FROM '.$TableName.' where merchant_id='.$_SESSION['merchant_id'].' ORDER BY display_order ASC';
							
							//$QueryResult = mysql_query($SQLstring);
							$QueryResult = $objDB->Conn->Execute($SQLstring);
							
							if($QueryResult !=0){
								//while($Row = mysql_fetch_row($QueryResult)){
								while($Row = $QueryResult->FetchRow()){
								$publish;
								 if($Row[4] == 0 ){ 
								 	$publish= 'publish';
								 }
								 else{
								  $publish ='unpublish';
								};
								$publish_status;
								 if($Row[4] == 0 ){ 
								 	$publish_status= 'not published';
								 }
								 else{
								  $publish_status ='published';
								};
								//$pricelist_type = mysql_fetch_array(mysql_query('select name from pricelist_type where id ='.$Row[7]));
								echo '<tr data-id="'.$Row[0].'" class="pricelist">
										<td>'.str_replace("dq",'"',$Row[1]).'</td>
										<td>'.$Row[5].'</td>
										<td>
											<span class="glyphicon glyphicon-arrow-up pricelist_up_arrow "></span>
											<span class="glyphicon glyphicon-arrow-down pricelist_down_arrow"></span>

										</td>

									</tr>';
									
								}
							}
							//mysql_close($DBConnect);
						}
					
					 ?>

				</tbody>
			</table>
		</div>
		<p class="hint">Use arrows to reorder.</p>
		<div class="clearfix" style="margin-top: 20px;">
			<button type="button" class="btn btn-primary fleft" id="save_order">Save</button>
			<button type="button" class="btn btn-primary fleft" id="cancel_reorder">Cancel</button>
		</div>

	</div>
	</div><!-- end of content-->
	
	</div><!-- end of contentContainer-->
	
	<!---------start footer--------------->
    <div>
	<?
		require_once(MRCH_LAYOUT."/footer.php");
	?>
	<!--end of footer-->
	</div>
</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){
	// $('#rearrange').click(function(){
	// 	$('#reorder').attr('disabled',false);
		
	$('#pricelist_table').dataTable( {
        "bSort" : false,
        "bFilter": false,
		"bPaginate": false
    } )


	// })
	$('#save_order').click(function(){
		reorder();
		  
	})
	$('#cancel_reorder').click(function(){
		history.go(-1)
	})
	arrows("pricelist","pricelist");
})
	function reorder(){
		var key="";
		var value="";
		var length= $('.pricelist').length;
		for(i=0;i<length;i++){
			key += $('.pricelist').eq(i).attr('data-id');
			value += ($('.pricelist').eq(i).index()+1);
			if(i<length-1){
				key += ',';
				value+=',';
			}
		}
		var key_array= key.split(",");
		var value_array= value.split(",");
		console.log(key_array)
		console.log(value_array)
		

		$('#reorder_key_array').attr('value',key_array);
		$('#reorder_value_array').attr('value',value_array);
		$('.reorder_form').submit();
		// jQuery.ajax({
  //           type: "POST", // HTTP method POST or GET
  //           url: "reorder.php", //Where to make Ajax calls
  //           dataType:"text", // Data type, HTML, json etc.
  //           data:key, //Form variables
  //           success:function(response){


  //           },
  //           error:function (xhr, ajaxOptions, thrownError){
  //               $("#FormSubmit").show(); //show submit button
  //               $("#LoadingImage").hide(); //hide loading image
  //               alert(thrownError);
  //           }
  //       })
	}


function ui_reorder(){
  var length = $('#pricelist_table').find('tr').length;
  for(i=0;i<length;i++){
    var row_index =$('#pricelist_table').find('tr').eq(i).index();
   $('#pricelist_table').find('tr').eq(i).find('td').eq(1).html(row_index+1) 
  }
}



	function arrows(arrow,obj){
  $('body').on('click','.'+arrow+'_up_arrow',function(){
    var element_count= $(this).closest('.'+obj).length;
    if($(this).closest('.'+obj).index() ==0){
      console.log('cant move up');
    }
    else{
      $(this).closest('.'+obj).insertBefore($(this).closest('.'+obj).prev());
      ui_reorder();
    }
  })
  $('body').on('click','.'+arrow+'_down_arrow',function(){
    var element_count= $(this).parent().closest('.'+obj).length;
   
      $(this).closest('.'+obj).insertAfter($(this).closest('.'+obj).next());
      ui_reorder();
    
  })
}
</script>
