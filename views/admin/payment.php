<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY . "/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
if (isset($_REQUEST['id'])) {
        $array_where['id'] = $_REQUEST['id'];
}
if (isset($_REQUEST['action'])) {
        if ($_REQUEST['action'] == "delete") {
		require_once(LIBRARY.'/stripe-php/config.php');	

                $array_where_bp['pack_id'] = $_REQUEST['id'];
                $RS_bp = $objDB->Show("merchant_billing", $array_where_bp);
		        if ($RS_bp->RecordCount() == 0) {

				$array_where['id'] = $_REQUEST['id'];
				$plan = $objDB->Show("billing_packages", $array_where);
				$slug = $plan->fields['slug'];
				$json_array['status'] = "true";
				try{	
					$plan = \Stripe\Plan::retrieve($slug);
					$plan->delete();
	
				} catch (Exception $e) {
					    $json_array['status'] = "false";
					    $json_array['serror'] = $e->getMessage();
				}

				if($json_array['status'] == "true"){
				        $array_where['id'] = $_REQUEST['id'];
				        $objDBWrt->Remove("billing_packages", $array_where);
				        $_SESSION['msg'] = "Package has been successfully deleted.";
				        header("Location: " . WEB_PATH . "/admin/payment.php?msg=1d");
				        exit();
				}else {
				        $_SESSION['msg'] = $json_array['serror'];
				        header("Location: " . WEB_PATH . "/admin/payment.php");
				        exit();
				}
		       } else {
		                $_SESSION['msg'] = "Package can't deleted because it is assigned to merchant.";
		                header("Location: " . WEB_PATH . "/admin/payment.php");
		                exit();
		       }
		

        }
}
if (isset($_REQUEST['action'])) {
        if ($_REQUEST['action'] == "point_delete") {
                $array_where['id'] = $_REQUEST['id'];
                $objDB->Remove("point_packages", $array_where);
                $_SESSION['msg'] = "Conversation Rate has been successfully deleted.";
                header("Location: " . WEB_PATH . "/admin/payment.php?msg=1d");
                exit();
        }
}

$RS_s = $objDB->Show("billing_packages");
$RS_p = $objDB->Show("point_packages");

$RS_r_f = $objDB->Show("redeemption_fee_charge");

if (isset($_REQUEST['btnsave'])) {
        //echo $_REQUEST['number_of_locations'];
        //echo $_REQUEST["id"];
        $array_where_mu['id'] = $_REQUEST['mer_name'];
        $RS_mu = $objDB->Show("merchant_user", $array_where_mu);
        $array_insert = array();
        $array_insert['date'] = date("Y-m-d H:i:s");
        $array_insert['order_id'] = $_REQUEST['mer_name'] . strtotime(date("Y-m-d H:i:s"));
        $array_insert['merchant_id'] = $_REQUEST['mer_name'];
        $array_insert['refrence_number'] = "001";
        $array_insert['amount'] = $_REQUEST['points'];
        if ($_REQUEST['billing_country'] == "USA") {
                $array_insert['currency'] = "USD";
        } else {
                $array_insert['currency'] = "CAD";
        }
        $array_insert['points_before_order'] = $RS_mu->fields['available_point'];
        //print_r($array_insert);
        //exit();
        $objDB->Insert($array_insert, "purchase_point_order");
        $where_clause_pp['id'] = 8;
        $RS_pp = $objDB->Show("point_packages", $where_clause_pp);
        //echo $RS_pp->fields['points'];
        $array_values = $where_clause = array();
        $array_values['available_point'] = $RS_mu->fields['available_point'] + ( $_REQUEST['points'] * $RS_pp->fields['points']);
        $where_clause['id'] = $_REQUEST['mer_name'];
        $objDB->Update($array_values, "merchant_user", $where_clause);

        // 03 10 2013
        $group_array = array();
        $group_array['merchant_id'] = $_REQUEST['mer_name'];
        $group_array['purchased_point'] = $_REQUEST['points'] * $RS_pp->fields['points'];
        $group_array['purchased_date'] = date("Y-m-d H:i:s");
        $objDB->Insert($group_array, "merchant_points");
        // 03 10 2013


        header("Location: " . WEB_PATH . "/admin/payment.php");
        exit();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Admin Panel</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="<?= ASSETS_CSS ?>/a/template.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/jquery-1.7.2.min.js"></script>
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

                    <form action="" method="post">    <? 
                        if(isset($_REQUEST['msg']))
                        {
                        if($_REQUEST['msg']=="1")
                        {$msg = "New Package has been added successfully";}                        
                        if($_REQUEST['msg']=="1d")
                        { $msg = "Package has been delete successfully"; }                        
                        }
                        ?>
                        <div style="color:#FF0000; "><?= $_SESSION['msg'] ?></div>
                        <div><table align="right"  cellspacing="10px" >
                                <tr><td><a href="create_package.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png">  Add New Package</a></td>	
                                </tr>

                            </table>
                            <!--end edit delete icons table--></div>
                        <br/>                
                        <table width="100%"   class="tableAdmin">

                            <tr><td style="color:#FF0000; text-align: center;" colspan="7"></td></tr>
                            <tr>                      
                                <th width="20%" align="left">Package Name</th>
                                <th width="5%" align="left">Price</th>
				<th width="5%" align="left">Currency</th>
                                <th width="10%" align="left">No. Location</th>
                                <th width="10%" align="left">No. Of Active Campaign Per Location</th>
                                <th width="10%" align="left">Total No. Of Campaign Per Month</th>
                                <th width="10%" align="left">Transaction Fee</th>
                                <th width="10%" align="left">Enable Coupon Redemption Fee</th>
                                <th width="20%" align="left">&nbsp;</th>
                            </tr>
                            <?
				$del_msg = "If you delete this plan, you won\'t be able to add new subscriptions. However, existing subscribers will continue to be charged on a recurring basis until their subscriptions are canceled.";
                            if($RS_s->RecordCount()>0){
                            while($Row_s = $RS_s->FetchRow()){                        
                            ?>
                            <tr>
                                <td align="left"><?= $Row_s['pack_name'] ?></td>
                                <td align="left"><?= $Row_s['price'] ?></td>
				<td align="left"><?= $Row_s['currency'] ?></td>
                                <td align="left"><?= $Row_s['no_of_loca']; ?></td>
                                <td align="left"><?= $Row_s['no_of_active_camp_per_loca'] ?></td>
                                <td align="left"><?= $Row_s['total_no_of_camp_per_month'] ?></td>
                                <td align="left"><?= $Row_s['transaction_fees'] ?></td> 
                                <td align="left">
                                    <?php
                                    if ($Row_s['enable_coupon_redeemption_fee'] == 1)
                                            echo "Yes";
                                    else
                                            echo "No";
                                    ?>
                                </td> 
                                <td align="left">
                                    <a style="margin: 0 5px;" href="edit-package.php?id=<?= $Row_s['id'] ?>">Edit</a> |
                                    <a style="margin-left: 5px;" href="payment.php?id=<?= $Row_s['id'] ?>&action=delete" onclick="return confirm('<?php echo $del_msg;?>')">Delete</a>
                                </td>
                            </tr>
                            <?
                            }
                            ?>

                            <?
                            }else{
                            ?>
                            <tr>
                                <td colspan="5" align="left">No package is Found.</td>
                            </tr>
                            <?
                            }
                            ?>
                        </table>
                        <h2>Redeemption Fee Charge</h2>
                        <table width="100%"   class="tableAdmin">

                            <tr><td style="color:#FF0000; text-align: center;" colspan="7"></td></tr>
                            <tr>                      
                                <th width="15%" align="left">Start Value</th>
                                <th width="15%" align="left">End Value</th>
                                <th width="25%" align="left">Type</th>
                                <th width="15%" align="left">Amount</th>

                                <th width="*" align="left">&nbsp;</th>
                            </tr>
                            <?
                            if($RS_r_f->RecordCount()>0){
                            while($Row_r_f = $RS_r_f->FetchRow()){                        
                            ?>
                            <tr>
                                <td align="left"><?= $Row_r_f['start_value'] ?></td>
                                <td align="left"><?= $Row_r_f['end_value'] ?></td>
                                <td align="left"><?= $Row_r_f['type']; ?></td>
                                <td align="left"><?= $Row_r_f['amount_value'] ?></td>
                                <td align="left">
                                    <a style="margin: 0 5px;" href="edit-redeemption-fee.php?id=<?= $Row_r_f['id'] ?>">Edit</a>
                                </td>
                            </tr>
                            <?
                            }
                            ?>

                            <?
                            }else{
                            ?>
                            <tr>
                                <td colspan="5" align="left">No package is Found.</td>
                            </tr>
                            <?
                            }
                            ?>
                        </table>

                        <div>
                            <?php
                            if ($RS_p->RecordCount() <= 0) {
                                    ?>
                                    <table align="right"  cellspacing="10px" >

                                        <tr><td >
                                                <a href="create_point_package.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png">  Add New Conversation Rate</a>
                                            </td>	
                                        </tr>

                                    </table>
                                    <?php
                            } else {
                                    ?>
                                    <h2>Conversation Rate</h2>
                                    <?php
                            }
                            ?>


                            <!--end edit delete icons table--></div>

                        <table width="100%"   class="tableAdmin">

                            <tr><td style="color:#FF0000; text-align: center;" colspan="3"></td></tr>
                            <tr>                      
                                <th width="40%" align="left">Conversation Rate</th>
                                <th width="10%" align="left">No. Points</th>
                                <th width="20%" align="left">&nbsp;</th>
                            </tr>
                            <?
                            if($RS_p->RecordCount()>0){
                            while($Row_s = $RS_p->FetchRow()){                        
                            ?>
                            <tr>

                                <td align="left"><?= $Row_s['price'] ?></td>
                                <td align="left"><?= $Row_s['points']; ?></td>
                                <td align="left">
                                    <a style="margin: 0 5px;" href="edit_point_package.php?id=<?= $Row_s['id'] ?>">Edit</a> <!-- |
                                     <a style="margin-left: 5px;" href="payment.php?id=<?= $Row_s['id'] ?>&action=point_delete">Delete</a>-->
                                </td>
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
                                <td colspan="3" align="left">No conversation rate is Found.</td>
                            </tr>
                            <?
                            }
                            ?>
                        </table>
                        <h2>Credit</h2>
                        <table>
                            <tr>
                                <td>
                                    Merchant
                                </td>
                                <td>
                                    <select id="mer_name" name="mer_name">
                                        <?php
                                        $array_where_mu['merchant_parent'] = 0;
                                        $RS_MER = $objDB->Show("merchant_user", $array_where_mu);


                                        while ($Row1 = $RS_MER->FetchRow()) {
                                                ?>
                                                <option value="<?php echo $Row1['id']; ?>" ><?php echo $Row1['business']; ?></option>
                                        <?php } ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Credit
                                </td>
                                <td>
                                    $&nbsp;<input type="textbox" name="points" id="points" size="10" maxlength="5" />
                                    <input type="submit" id="btnsave" name="btnsave" value="Save"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <!--end of Container--></div>


    </body>
</html>
<?php
$_SESSION['msg'] = "";
?>

<script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery("#btnsave").click(function () {
                if (jQuery("#points").val() <= 0)
                {

                    alert("Enter Credit Greater Than Zero");
                    //flag="false";
                    return false;
                }
            });
        })


</script>
