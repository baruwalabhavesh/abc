<?php
/******** 
@USE : success order
@PARAMETER : 
@RETURN : 
@USED IN PAGES : review_address.php, header.php, process.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//require_once("classes/Config.Inc.php");
check_customer_session();
//$objDB = new DB();
//echo WEB_PATH.'/process.php?getaddressbookofuser=yes&user_id='.$_SESSION['customer_id'];
$arr_loc=file(WEB_PATH.'/process.php?getoderdetail=yes&user_id='.$_SESSION['customer_id'].'&ordreno='.$_REQUEST['order']);
							if(trim($arr_loc[0]) == "")
							{
								unset($arr_loc[0]);
								$arr_loc = array_values($arr_loc);
							}
							$all_json_str_loc = $arr_loc[0];
							$json_loc = json_decode($arr_loc[0]);
							$total_records1_loc = $json_loc->total_records;
							$records_array1_loc = $json_loc->records;
					
	
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Order Confirmation</title>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?= ASSETS_CSS ?>/c/template.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php
        require_once(CUST_LAYOUT . "/header.php");
        ?>
        <div id="content" class="cantent">
            <div class="my_main_div">
                <div id="contentContainer" class="contentContainer">
					<div class="sucess_order">Your order is placed successfully. Your Order number is <?php echo $_REQUEST['order'] ;?> . To view the status of your order , go to <a href="my-orders.php">My Orders.</a></div>
					<div style="clear:both"></div>
                                
            </div><!--end of my_main_div-->
			<?php require_once(CUST_LAYOUT . "/before-footer.php"); ?>
        </div><!--end of content-->

        <?php require_once(CUST_LAYOUT . "/footer.php"); ?>

    </body>
</html>
<?php
$_SESSION['req_pass_msg'] = "";
?>
