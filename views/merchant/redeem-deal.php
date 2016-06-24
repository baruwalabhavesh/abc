<?php
/**
 * @uses redeem deal
 * @used in pages :my-account-left.php
 * @author Sangeeta Raghavani
 */
session_start();
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
////$objDB = new DB();
?>
<!DOCTYPE HTML >
<html>
    <head>
        <title>ScanFlip | Redeem </title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 
            <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
            <script type="text/javascript" src="<?= ASSETS_JS ?>/m/jquery.form.js"></script>
            <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
			<link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>
			<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css"> 
            <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
			
    </head>

    <body class="redeem_page">
        <div id="dialog-message" title="Message Box" style="display:none">
        </div>
        <div >
            <!-- load from CDN-->
			<?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
            
			
<?php
if (isset($_COOKIE['merchant_' . $_SESSION['merchant_id']])) {
    if ($_COOKIE['merchant_' . $_SESSION['merchant_id']] != "") {
        ?>

                    <?php
                    $lnglat = $_COOKIE['merchant_' . $_SESSION['merchant_id']];
                    $lnglat_arr = explode(";", $lnglat);
                    $curr_longitude = $lnglat_arr[0];
                    $curr_latitude = $lnglat_arr[1];
                }
            } else {
                ?>
                <script>
			
                    /******** GET USER CURRENT LOCATION *********************/
                    function getLocation1()
                    {
                        if (navigator.geolocation)
                        {
                            navigator.geolocation.getCurrentPosition(showPosition1, function (errorCode) {
                                //alert(errorCode);
                                if (errorCode.code == 1) {
                                    var date = new Date();
                                    date.setTime(date.getTime() + (1 * 60 * 60));
                                    setCookie("merchant_<?php echo $_SESSION['merchant_id']; ?>", "", date.toGMTString());
                                    alert("Please enter your curret location or postal code to search offers near you.");
                                }
                                if (errorCode.code == 2)
                                {
                                    var date = new Date();
                                    date.setTime(date.getTime() + (1 * 60 * 60));
                                    setCookie("merchant_<?php echo $_SESSION['merchant_id']; ?>", "", date.toGMTString());
                                    alert("We can't find your current location.Please enter your cuuret location or postal code to search offers near you.");
                                }
                            });
                        }
                        else
                        {
                            alert("Geolocation is not supported by this browser.");
                        }
                    }
                    function showPosition1(position)
                    {
                        var val;
                        val = "Latitude: " + position.coords.latitude +
                                "<br />Longitude: " + position.coords.longitude;
                        var latlng = position.coords.latitude + ";" + position.coords.longitude;
                        var date = new Date();
                        date.setTime(date.getTime() + (1 * 60 * 60));
                        setCookie("merchant_<?php echo $_SESSION['merchant_id']; ?>", latlng, date.toGMTString());
                        window.location.reload(false);
                    }
                    /*************** SET COOKIE ***********************/
                    function setCookie(c_name, value, exdays)
                    {
                        var exdate = new Date();
                        exdate.setDate(exdate.getDate() + exdays);
                        var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
                        document.cookie = c_name + "=" + c_value;
                    }
                </script>
    <?php
}
?>
            <!---start header---->
            <div>
            <?php
            //INCLUDE HEADER FILE
            require_once(MRCH_LAYOUT . "/header.php");
            ?>
              <!--end header-->
             </div>
            <div id="contentContainer">

                <div id="content">


                  <h3><?php echo $merchant_msg["redeem-deal"]["redeem_campaign"]; ?></h3>


                    <div id="redem_contanr">
                        <div id="main_redem_selection">
                            <ul>
                                <li class="redeem_reward_card_popup">
                                    <input type="radio" checked id="redeem_reward_card" name="redem_option" value="reward" /><label for="redeem_reward_card" class= "chk_align">Scanflip Reward Card</label>  
                                </li>
                                <li>  
                                    <input type="radio"  id="redeem_voucher_code" name="redem_option" value="voucher" /><label for="redeem_voucher_code" class= "chk_align">Campaign Code</label>                        
                                </li>
                                <li>
                                    <input type="radio" id="redeem_reward_campaign" name="redem_option" value="rew_camp" /><label for="redeem_redeem_campaign" class= "chk_align">Reward Campaign</label>
                                </li>
                              
                                <li>
                                    <input type="radio" id="redeem_loyalty_reward_card" name="redem_option" value="loy_rew_card" /><label for="redeem_loyalty_reward_card" class= "chk_align">Loyalty Card</label>
                                </li>
                                <li>
                                    <input type="radio" id="redeem_gift_card" name="redem_option" value="gift_card" /><label for="redeem_gift_card" class= "chk_align">Gift Card</label>
                                </li>
                            </ul>
                        </div>
                        <!--Reward card form-->
                        <div id="reward_div" class="wrapper-adjusting" style="display: none">
                            <!-- Modal -->
                            <div class="modal fade get_closed" id="myModal" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-body" style="padding:20px;">
                                            <form role="form">
                                                <div class="form-group">
                                                    <h4>Scanflip Reward Card</h4>
                                                    <input type="text" name="txt_rewardcard" id="txt_rewardcard" autocomplete="off" style="width:200px;    margin: 10px 0;" value="" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_loyalty_card"]; ?>"> 
                                                    <span id="msg_reward_card"></span>
                                                </div>
                                                <input type="button" value="Cancel" name="btn_cancle" id="btn_cancle" class="closetab_src" />
                                                <input type="button" value="Submit" name="btn_get_campaignlist" id="btn_get_campaignlist">
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div> 
                            <div class="">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="button" class="reward_btn" value="Please Enter Scanflip Reward Card" style="display: none"/>
                                    <div class="tabbable tabs-left" id="reward_card" style="display:none">
                                        <div class="row">
                                            <div class="col-md-2" id="reward_info">
                                                <ul class="nav nav-tabs no-margin">
                                                    <li class="active"><a href="#a" data-toggle="tab">Campaigns</a></li>
                                                    <li><a href="#b" data-toggle="tab">Loyalty Cards</a></li>
                                                    <li><a href="#c" data-toggle="tab">Gift Cards</a></li>
                                                    <li><a href="#d" data-toggle="tab">Reward Campaigns</a></li>
                                                   
                                                </ul>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="tab-content ">
                                                    <div class="tab-pane active" id="a">
                                                        <div id="campaign_list_div">
                                                            <span id="msg_campaign"></span>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="b">
														<div class="lct_col col-md-7">
                                                            <div class="lct_tab">
                                                                <table class="table table-bordered" id="loyalty_cards">
                                                                     <thead>
                                                                        <tr>
                                                                            <th>Card Title</th>
                                                                            <th>Total Stamp</th>   
                                                                            <th>Stamp Left</th>   
                                                                            <th>Redeem</th>   
                                                                        </tr>
                                                                    </thead>
                                                                </table>
                                                            </div>
                                                        </div>
															<div id="mer_redeem_loyalty_card" class="lct_rdm col-md-5" style="display:none">
                                                            <!--<input type="button" class="rec_trans" style="margin-bottom: 10px" value="Recent Transactions"/>-->
                                                            <div class="card1 card-block1 text-left">
                                                                <h5 class="text-center"><p>Redeem Loyalty Card</p></h5>
                                                                <div class="form-group">
                                                                            <input type="text" class="form-control" name="lc_card_code" id="lc_card_code" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_barcode"]; ?>" autocomplete="off"/> 
																			<span id="message_in_lc"></span>
                                                                 </div>
                                                                 <div class="form-group">
                                                                            <input type="text" class="form-control" value="" name="lc_card_value" id="lc_card_value" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_total_sales"]; ?>"/>
																			<span id="message_points_in_lc"></span>
																</div>
                                                                 <div class="form-group">
																	 <input type="button" value="Redeem" name="btn_redeem_lc_card" id="btn_redeem_lc_card" class="disabled" disabled/>&nbsp;&nbsp;
																	<input type="button" value="Cancel" name="btn_lc_cancle" id="btn_lc_cancle" onclick="window.location.href = window.location.href;" />
                                                        
                                                                 </div>
                                                                </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="c">
                                                        <div class="gft_col col-md-7">
                                                            <div class="gft_tab">
                                                                <table class="table table-bordered" id="user_giftcards" cellspacing="0" cellpadding="0" >
                                                                     <thead>
                                                                        <tr>
                                                                            <th>Card Title</th>
                                                                            <th>Card Value</th>
                                                                            <th>Card Balance</th>
                                                                            <th>Recent Transactions</th>
                                                                            <th>Redeem</th>   
                                                                        </tr>
                                                                    </thead>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div id="mer_redeem_giftcard" class="gft_rdm col-md-5" style="display: none">
                                                            <!--<input type="button" class="rec_trans" style="margin-bottom: 10px" value="Recent Transactions"/>-->
                                                            <div class="card card-block text-left">
                                                                <h5 class="text-center"><p>Redeem Gift Card</p></h5>
                                                                     <form role="form">
                                                                        <div class="form-group">
                                                                            <input type="text" class="form-control" id="giftcard_code" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_giftcardcard"]; ?>">
                                                                            <span id="message_in"></span>
                                                                        </div>
                                                                         <div class="form-group">
                                                                           <label>Transaction type</label>
                                                                           <div class="radio trans1">
                                                                            <label><input type="radio" name="trans1" checked="checked" value="debit">Debit</label>
                                                                            <label><input type="radio" name="trans1" value="credit">Credit</label>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" min="0" class="form-control" id="giftcard_value"  placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_amount"]; ?>">
                                                                            <span id="message_points_in"></span>
                                                                        </div>
                                                                       
                                                                        </div>
                                                                        <div class="form-group text-center">
                                                                         <input type="button" value="Submit" id="red_gif_card_submit" />
                                                                        </div>
                                                                      </form>
                                                                </div>
                                                        </div>
                                                        
                                                    </div>
                                                        <!--REWARD CAMPAIGN-->
                                                    <div class="tab-pane" id="d">
                                                    <div class="rcd col-md-7">
                                                            <div class="rcd_tab">
                                                                <table class="table table-bordered" id="user_reward_campaigns">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Campaign Title</th>
                                                                            <th>Redeem</th>
                                                                        </tr>
                                                                    </thead>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                    
                                                </div>
                                            </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /tabs -->
                                </div>
                            </div>
                        </div>
                        <!--Campaign code form-->
                        <div id="redem_div" style="display:block;">
                            <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                                <tr>
                                    <td width="100%" align="left" valign="top">
                                        <form action="process.php" method="post" id="campaign_detail_form">


                                            <div align="center">
                                                <h4>Campaign Code</h4>    
                                                <table>
                                                    <tr>
            <!--						<td>
<?php //echo $merchant_msg["redeem-deal"]["Field_enter_barcode"]; ?>:
                                                            </td>-->
                                                        <td>
                                                            <div class="form-group">
                                                            <input type="text" class="form-control" name="txt_barcode" id="txt_barcode" autocomplete="off" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_barcode"]; ?>" onkeyup="gofor_autocomplete(this.value);" style="width:200px;"/> 
                                                            </div>
                                                        
                                                            <div id="autocomplete" class="redeem_autocomplete_div" >
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
            <!--						<td>
<?php //echo $merchant_msg["redeem-deal"]["Field_enter_total_sales"]; ?>:
                                                                    
                                                            </td>-->
                                                        <td>
                                                            <div class="form-group">
                                                            <input type="text" class="form-control" value="" name="txt_redeem_deal_value" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_total_sales"]; ?>" id="txt_redeem_deal_value" style="width:200px;"/>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
            <!--						<td>
                                                                    
                                                            </td>-->
                                                        <td style="padding-top:15px;">

                                                            <input type="submit" value="Redeem" name="btn_redeem" id="btn_redeem" class="disabled" disabled/>&nbsp;&nbsp;
                                                            <input type="button" value="Cancel" name="btn_cancle" id="btn_cancle" onclick="window.location.href = window.location.href;" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <input type="hidden" id="hdn_coupon_code" name="hdn_coupon_code" value="" />
                                        </form>
                                        <div id="div_error_content"></div>
                                        <form action="process.php" method="post" id="redeem_form">
                                            <div id="div_campaign_info">

                                            </div>
                                        </form>	
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!--Reward Campaign form-->
                        <div id="rew_camp_div" style="display:none;">
                            <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                                <tr>
                                    <td width="100%" align="left" valign="top">
                                        <form action="process.php" method="post" id="campaign_detail_form">


                                            <div align="center">
                                                <h4>Redeem Campaign Reward</h4>

                                                <table>
                                                    <tr>
            <!--						<td>
<?php //echo $merchant_msg["redeem-deal"]["Field_enter_barcode"]; ?>:
                                                            </td>-->
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="cp_code" id="cp_code" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_barcode"]; ?>" autocomplete="off" style="width:200px;"/> 
                                                                <span id="reward_campaign_message"></span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
            <!--						<td>
<?php //echo $merchant_msg["redeem-deal"]["Field_enter_total_sales"]; ?>:
                                                            </td>-->
                                                    </tr>
                                                    <tr>
            <!--						<td>
                                                                    
                                                            </td>-->
                                                        <td style="padding-top:15px;">

                                                            <input type="button" value="Redeem" name="btn_redeem_card" id="redeem_rzcampaign"/>&nbsp;&nbsp;
                                                            <input type="button" value="Cancel" name="btn_cancle" id="btn_cancle" onclick="window.location.href = window.location.href;" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <input type="hidden" id="hdn_coupon_code" name="hdn_coupon_code" value="" />
                                        </form>
                                        <div id="div_error_content"></div>
                                        <form action="process.php" method="post" id="redeem_form">
                                            <div id="div_campaign_info">

                                            </div>
                                        </form>	
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--Loyalty reward card form-->
                        <div id="loy_rew_card_div" style="display:none;">
                            <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                                <tr>
                                    <td width="100%" align="left" valign="top">
                                        <form action="process.php" method="post" id="campaign_detail_form">


                                            <div align="center">
                                                <h4>Redeem Loyalty Card</h4>
                                                <table>
                                                    <tr>
            <!--						<td>
<?php //echo $merchant_msg["redeem-deal"]["Field_enter_barcode"]; ?>:
                                                            </td>-->
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="txt_stampcard" id="txt_stampcard" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_barcode"]; ?>" autocomplete="off" style="width:200px;"/> 
																<span id="message_in_lc1"></span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" value="" name="txt_revenue" id="txt_revenue" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_total_sales"]; ?>" style="width:200px;"/>
																<span id="message_points_in_lc1"></span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding-top:15px;">

                                                            <input type="button" value="Redeem" name="btn_redeem_card" id="btn_redeem_card" class="disabled" disabled/>&nbsp;&nbsp;
                                                            <input type="button" value="Cancel" name="btn_cancle" id="btn_cancle" onclick="window.location.href = window.location.href;" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <input type="hidden" id="hdn_coupon_code" name="hdn_coupon_code" value="" />
                                        </form>
                                        <div id="div_error_content"></div>
                                        <form action="process.php" method="post">
                                            <div id="div_campaign_info">

                                            </div>
                                        </form>	
                                    </td>
                                </tr>
                            </table>
                        </div>
                         <!--gift card form-->
                        <div id="gift_card_div" style="display:none;">
                            <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                                <tr>
                                    <td width="100%" align="left" valign="top">
                                        <form action="" method="post" id="redeem_giftcard">
                                            <div align="center">
                                                <div class="row">
                                                    <div class="gft_blk col-md-3 col-md-offset-3">
                                                        <h4>Redeem Gift Card</h4>
                                                        <div class="form-group text-left">
                                                            <input type="text" class="form-control" name="gc_code" id="gc_code" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_giftcardcard"]; ?>" autocomplete="off" style="width:200px;"/> 
                                                            <span id="message"></span>
                                                        </div>
                                                        <div class="form-group text-left">
                                                            <label>Transaction type</label>
                                                            <div class="radio trans">
                                                                <label><input type="radio" name="trans" checked="checked" value="debit">Debit</label>
                                                                <label><input type="radio" name="trans" value="credit">Credit</label>
                                                            </div>
                                                        </div> 
                                                        <div class="form-group text-left">
                                                            <input type="number" min="0" value="" class="form-control" name="gc_value" id="gc_value" placeholder="<?php echo $merchant_msg["redeem-deal"]["Field_enter_amount"]; ?>" name="txt_revenue" id="txt_revenue" style="width:200px;"/>
                                                            <span id="message_points"></span>
                                                        </div>
                                                       
                                                        <input type="button" value="Submit" id="redeem_user_giftcard" name="redeem_user_giftcard"/>

                                                    </div>
                                                    <div class="gft col-md-6 text-left mt-40">
                                                        <input type="button" class="recentBal" value="Check Card Balance"/>
                                                        <input type="button" class="recentTransaction" value="Recent Transactions"/>
                                                    </div>                                       
                                                    
                                                </div>

                                            </div>
                                            <input type="hidden" id="hdn_coupon_code" name="hdn_coupon_code" value="" />
                                        </form>
                                        <div id="div_error_content"></div>
                                        <form action="process.php" method="post" id="redeem_form">
                                            <div id="div_campaign_info">

                                            </div>
                                        </form>	
                                    </td>
                                </tr>
                            </table>
                        </div>
                         <div class="modal fade get_closed" id="rewardGiftModal" role="dialog" style="display: none">
                             <div class="modal-dialog">

                                 <!-- Modal content-->
                                 <div class="modal-content">
                                     <div class="modal-body" style="padding:40px 50px;">
                                         <div class="col-md-12">
                                             <div class="bs-example">
                                                 <table class="table table-bordered" id="recent_giftcard_transactions">
                                                     <thead>
                                                     <h4>Gift Card Transactions</h4>
                                                         <tr>
                                                             <th>Date / Time</th>
                                                             <th>Location Address</th>                                                             
                                                             <th>Transaction Type</th>
                                                             <th>Transaction Amount</th>
                                                         </tr>
                                                     </thead>
                                                 </table>
                                             </div>
                                         </div>
                                         <div class="closetab_rgm"><input type="button" class ="center-block" value="Close"/></div>
                                     </div>
                                 </div>

                             </div>
                         </div>
                         
                         <!-- Recent balance table modal-->
                         <div class="modal fade get_closed" id="checkbal" role="dialog" style="display: none">
                             <div class="modal-dialog">

                                 <!-- Modal content-->
                                 <div class="modal-content">
                                     <div class="modal-body" style="padding:40px 50px;">
                                         <div class="col-md-12">
                                             <div class="bs-example">
                                                 <table class="table table-bordered" id="giftcard_balance">
                                                     <thead>
                                                         <tr>
                                                             <td colspan="3"><h4 style="float:left;">Gift Card Balance</h4><h6><div id="delete_card_msg" style="float:right;">
																 Certificate ID: <span id= "certificate_id"></span><br>
																 Status: 
                                                             <span id="card_active">Active</span> /
                                                             <span id="card_expired">Expired</span> / <span id="card_deleted">Deleted</span>
                                                              </div></h6></td>
                                                         </tr>
                                                         <tr>
                                                             <th>Card Title</th>
                                                             <th>Card Value</th>
                                                             <th>Card Balance</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody>
                                                         
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                         <input type="button" class="closetab_gcb center-block" value="Close"/>
                                     </div>
                                 </div>
                             </div>
                         </div> 
                       
                        </div>
                        </div>
                        
                        
                        

                    </div>
                   <!-- <div class="clear">&nbsp;</div> -->
                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <!---------start footer--------------->
            <div>
                <?php
                require_once(MRCH_LAYOUT."/footer.php");
                $_SESSION['msg']= "";
                ?>
                <!--end of footer--></div>

        </div>





    </body>
</html>
<script>


 //var reward_card_number;
    jQuery("#btn_redeem_card").click(function () {
		var lc_code = jQuery("#txt_stampcard").val();
        var lc_value = jQuery("#txt_revenue").val();
        var code_msg_div = '#message_in_lc1';
        var pts_msg_div = '#message_points_in_lc1';
		if(lc_code.length == 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(code_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_loyalty_card"];?>').css("color","red");
            return false;
        }
        
        if(lc_value.length == 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_loyalty"];?>').css("color","red");
            return false;
        }
        if(jQuery.isNumeric(lc_value) == false){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
         if(lc_value <= 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
        if(lc_value.indexOf("-") != -1){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
        jQuery(code_msg_div).html('');
        jQuery(pts_msg_div).html('');
        
        var merchant_id = "<?php echo $_SESSION['merchant_id'] ?>";
        var flag = 0;
        jQuery.ajax({
            type: "POST",
            url: 'process.php',
            data: 'loginornot=true',
            async: false,
            success: function (msg)
            {
                var obj = jQuery.parseJSON(msg);
                if (obj.status == "false")
                {
                    window.location.href = obj.link;
                    flag = 1;
                }
            }
        });

        if (flag == 1)
        {
            return false;
        }
        else
        {
            var stampcard_number = jQuery("#txt_stampcard").val();
            var revenue = jQuery("#txt_revenue").val();
            if (stampcard_number == "")
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'>Please enter card</div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

                jQuery.fancybox({
                    content: jQuery('#dialog-message').html(),
                    type: 'html',
                    openSpeed: 300,
                    closeSpeed: 300,
                    openEffect :'none',
					closeEffect :'none',
                    changeFade: 'fast',
                    helpers: {
                        overlay: {
                            opacity: 0.3
                        } // overlay
                    }
                });
                return false;
            }
            else if (revenue == "")
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'>Please enter revenue</div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

                jQuery.fancybox({
                    content: jQuery('#dialog-message').html(),
                    type: 'html',
                    openSpeed: 300,
                    closeSpeed: 300,
                    openEffect :'none',
					closeEffect :'none',
                    changeFade: 'fast',
                    helpers: {
                        overlay: {
                            opacity: 0.3
                        } // overlay
                    }
                });
                return false;
            }
            else
            {
                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'punchcard_for_user=yes&merchant_id=' + merchant_id + "&code=" + stampcard_number + "&revenue=" + revenue,
                    async: false,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        if (obj.status == "true")
                        {
                            var head_msg = "<div class='head_msg'>Message</div>"
                            var content_msg = "<div class='content_msg'>" + obj.message + "</div>";
                            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            //alert(content_msg);

                            jQuery.fancybox({
                                content: jQuery('#dialog-message').html(),
                                type: 'html',
                                openSpeed: 300,
                                closeSpeed: 300,
                                openEffect :'none',
								closeEffect :'none',
                                changeFade: 'fast',
                                helpers: {
                                    overlay: {
                                        opacity: 0.3
                                    } // overlay
                                }
                            });

                            jQuery("#txt_stampcard").val('');
                            jQuery("#txt_revenue").val('');
                            return false;
                        }
                        else
                        {
                            var head_msg = "<div class='head_msg'>Message</div>";
                            var content_msg = "<div class='content_msg'>" + obj.message + "</div>";
                            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            //alert(content_msg);

                            jQuery.fancybox({
                                content: jQuery('#dialog-message').html(),
                                type: 'html',
                                openSpeed: 300,
                                closeSpeed: 300,
                                openEffect :'none',
								closeEffect :'none',
                                changeFade: 'fast',
                                helpers: {
                                    overlay: {
                                        opacity: 0.3
                                    } // overlay
                                }
                            });
                            return false;
                        }
                    }
                });
            }
        }
    });
   
    jQuery("#btn_get_campaignlist").click(function () {
		
    processLoginorNot();
        var flag = 0;
        jQuery.ajax({
            type: "POST",
            url: 'process.php',
            data: 'loginornot=true',
            async: false,
            success: function (msg)
            {
                var obj = jQuery.parseJSON(msg);
                if (obj.status == "false")
                {
                    window.location.href = obj.link;
                    flag = 1;
                }
            }
        });

        if (flag == 1)
        {
            return false;
        }
        else
        {
            var reward_card_number = jQuery("#txt_rewardcard").val();
            if (reward_card_number == "")
            {
                
                jQuery("#msg_reward_card").html("<?php echo $merchant_msg["redeem-deal"]["Msg_enter_reward_card"]; ?>").css("color" , "red");

                //alert(content_msg);

                return false;
            }
            else
            {
			//Loyalty Cards
			  var ltable ='';
      $('#loyalty_cards').dataTable().fnDestroy();
        
        ltable = $('#loyalty_cards').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": false,
                            "info": false,
                            "bSort": false,
							"bAutoWidth":false,
							"iDisplayLength":5,
							"fixedColumns": true,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "oLanguage": {
                                "sEmptyTable": "No transactions founds in the system.",
                                "sZeroRecords": "Sorry currently customer does not have any Loyalty cards.",
                                "sProcessing": "Loading..."
                            },
                            "fnPreDrawCallback": function (oSettings) {
                                //logged_in_or_not();
                            },
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "view_user_loyalty_cards", "value": true}, {"name": 'reward_card_number', "value": reward_card_number});

                            },
                        });	
         //GIFTCARD TABLE       
        var otable ='';
      $('#user_giftcards').dataTable().fnDestroy();
        
        otable = $('#user_giftcards').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": false,
                            "bInfo": false,
                            "bSort": false,
							"bAutoWidth":false,
							"iDisplayLength":5,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "oLanguage": {
                                "sEmptyTable": "No transactions founds in the system.",
                                "sZeroRecords": "Sorry currently customer does not have any gift cards.",
                                "sProcessing": "Loading..."
                            },
                            "fnPreDrawCallback": function (oSettings) {
                                //logged_in_or_not();
                            },
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "view_user_giftcards", "value": true}, {"name": 'reward_card_number', "value": reward_card_number});

                            },
                        });
               
                //REWARD CAMPAIGN TABLE
                var rctable ='';
        $('#user_reward_campaigns').dataTable().fnDestroy();
        
        rctable = $('#user_reward_campaigns').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": false,
                            "info": false,
                            "bSort": false,
							"bAutoWidth":false,
                            "iDisplayLength":5,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "oLanguage": {
                                "sEmptyTable": "No transactions founds in the system.",
                                "sZeroRecords": "Sorry currently customer does not have any active reward campaigns.",
                                "sProcessing": "Loading..."
                            },
                            "fnPreDrawCallback": function (oSettings) {
                                //logged_in_or_not();
                            },
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "view_user_reward_campaigns", "value": true}, {"name": 'reward_card_number', "value": reward_card_number});

                            },
                        });
                
                //CAMPAIGN TABLE
                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'getcamplist_from_rewardcard=true&reward_card_number=' + reward_card_number,
                    async: false,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        if (obj.code_status == 1)
                        {
                           jQuery(".reward_btn").css("display", "none"); 
                           jQuery("#reward_card").css("display", "block");
                        }
                        if (obj.status == "true")
                        {
                            jQuery("#enter_reward_number").css("display", "none");
                            jQuery("#campaign_list_div").html(obj.html);
                            jQuery("#campaign_list_div").css("display", "block");
                            bind_campaign_select();
                        }
                        else
                        {
                            jQuery("#campaign_list_div").css("display", "block");
                            jQuery("#msg_campaign").html('<?php echo $merchant_msg["redeem-deal"]["Msg_no_saved_campaigns"];?>');

                            //alert(content_msg);

                            return false;
                        }
                    }
                });
            }
        }
        jQuery('#myModal').modal('toggle');
    });

    function bind_campaign_select()
    {
        jQuery("input[id^=campaign_select_]").click(function () {
            //alert(jQuery(this).attr('coupon_code'));
            var v_code = jQuery(this).attr('coupon_code');
            jQuery("#campaign_list_div").css("display", "none");
            jQuery("#txt_barcode").val(v_code);
            jQuery("#redem_div").css("display", "block");
        });

        jQuery("#goback_rewardcard").click(function () {
            jQuery("#campaign_list_div").css("display", "none");
            jQuery("#enter_reward_number").css("display", "block");
        });

        jQuery("input[class=fltr_button]").click(function () {
            var fltr_option = jQuery(this).attr("id");
            /*
             alert(fltr_option);
             alert(jQuery("div.campaign_inner_div").size());	
             alert(jQuery("div.campaign_inner_div[camp='1']").size());	
             alert(jQuery("div.campaign_inner_div[camp='2']").size());	
             */

            jQuery("div.filters input").removeClass("fltr_selected");
            if (fltr_option == "fltr_all")
            {
                jQuery(this).addClass("fltr_selected");
                jQuery("div.campaign_inner_div").css("display", "block");
            }
            else if (fltr_option == "fltr_campaign")
            {
                jQuery(this).addClass("fltr_selected");
                jQuery("div.campaign_inner_div").css("display", "none");
                jQuery("div.campaign_inner_div[camp='1']").css("display", "block");
            }
            else if (fltr_option == "fltr_stamp")
            {
                jQuery(this).addClass("fltr_selected");
                jQuery("div.campaign_inner_div").css("display", "none");
                jQuery("div.campaign_inner_div[camp='2']").css("display", "block");
            }
        });
    }
    jQuery("input[type=radio][name=redem_option]").click(function () {
        var redem_option = jQuery(this).val();
        //alert(redem_option);
        if (redem_option == "voucher")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#redem_div").css("display", "block");

        }
        else if (redem_option == "reward")
        {
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#reward_div").css("display", "block");
            jQuery("#campaign_list_div").css("display", "none");
            jQuery("#enter_reward_number").css("display", "block");

        }
        else if (redem_option == "stamp")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "block");
        }

    });


   
    /***** IF VOUCHER CODE IS ENTERD AND VOUCHER CODE LENGTH MUST BE GREATER THEN 10 AND REDEEM DEAL VALUE MUST NE ENTERD THEN ENABLE THE REDEEM BUTTON ACTIVE********/
    jQuery("input[type='text']").keyup(function () {

        if (jQuery("#txt_barcode").val().length == 8 && jQuery("#txt_redeem_deal_value").val().length > 0)
        {
            jQuery('#btn_redeem').removeAttr("disabled");
            jQuery('#btn_redeem').removeClass("disabled");
        }
        else
        {
            jQuery('#btn_redeem').attr("disabled", "");
            jQuery('#btn_redeem').addClass("disabled");
        }

        if (jQuery("#txt_stampcard").val().length == 8 && jQuery("#txt_revenue").val().length > 0)
        {
            jQuery('#btn_redeem_card').removeAttr("disabled");
            jQuery('#btn_redeem_card').removeClass("disabled");
        }
        else
        {
            jQuery('#btn_redeem_card').attr("disabled", "");
            jQuery('#btn_redeem_card').addClass("disabled");
        }
        if (jQuery("#lc_card_code").val().length == 8 && jQuery("#lc_card_value").val().length > 0)
        {
            jQuery('#btn_redeem_lc_card').removeAttr("disabled");
            jQuery('#btn_redeem_lc_card').removeClass("disabled");
        }
        else
        {
            jQuery('#btn_redeem_lc_card').attr("disabled", "");
            jQuery('#btn_redeem_lc_card').addClass("disabled");
        }
    });

    jQuery(".fancybox-inner #popupredeem").live("click", function () {
        window.location = "redeem-deal.php";
    });

    /***** IF CURRENT MERCAHNT SESSION TIME OUT THEN SEND TO REGISTER PAGE AND AFTER REGISTRATION AGAIN COME TO REDEEM DEAL PAGE , 
     OTHERWISE CONTINUE WITH REDEEM FUNCTIONALITY
     BEFORE SUBMITING FROM CHECK WHETHER VOUCHER CODE IS ENETER AND IS PROPER REDEEM VALUE IS ENTERED OR NOT AND GIVE PROPER ERROR MESSAGE
     *****/
    function loginornot()
    {
        var flag = 0;
        jQuery.ajax({
            type: "POST",
            url: 'process.php',
            data: 'loginornot=true',
            async: false,
            success: function (msg)
            {
                var obj = jQuery.parseJSON(msg);
                if (obj.status == "false")
                {
                    window.location.href = obj.link;
                    flag = 1;
                }
            }
        });

        if (flag == 1)
        {
            return false;
        }
        else
        {
            var barcode = jQuery("#txt_barcode").val();
            if (barcode == "")
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_enter_proper_coupon_code"]; ?></div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

                jQuery.fancybox({
                    content: jQuery('#dialog-message').html(),
                    type: 'html',
                    openSpeed: 300,
                    closeSpeed: 300,
                    changeFade: 'fast',
                    openEffect :'none',
					closeEffect :'none',
                    helpers: {
                        overlay: {
                            opacity: 0.3
                        } // overlay
                    }
                });
                return false;
            }
            var deal_value = jQuery("#txt_redeem_deal_value").val();
            var numbers = /^[0-9]+$/;
            var float_value = /^[+-]?\d+(\.\d+)?$/;
            /*
             if(deal_value.match(float_value))
             {
             alert("match");
             }
             else
             {
             alert("not match");
             }
             return false;
             */
            if (deal_value.match(float_value) && deal_value != "")
            {

                if (deal_value <= 0)
                {
                    //alert("Enter proper deal value");
                    var head_msg = "<div class='head_msg'>Message</div>"
                    var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"]; ?></div>";
                    var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                    jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                    //alert(content_msg);

                    jQuery.fancybox({
                        content: jQuery('#dialog-message').html(),
                        type: 'html',
                        openSpeed: 300,
                        closeSpeed: 300,
                        openEffect :'none',	
						closeEffect :'none',
                        changeFade: 'fast',
                        helpers: {
                            overlay: {
                                opacity: 0.3
                            } // overlay
                        }
                    });
                    return false;

                }
                else
                {
                    return true;
                }
            }
            else
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"]; ?></div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

                jQuery.fancybox({
                    content: jQuery('#dialog-message').html(),
                    type: 'html',
                    openSpeed: 300,
                    closeSpeed: 300,
                    openEffect :'none',
					closeEffect :'none',
                    changeFade: 'fast',
                    helpers: {
                        overlay: {
                            opacity: 0.3
                        } // overlay
                    }
                });
                return false;
            }
        }
    }
    
    function processLoginorNot()
    {
                        var flag=0;
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
                                    flag=1;
                                    
                                }
                                
                           }
                     });
                    
                     if(flag == 1)
                        {
                           return false;
                        }
                        else
                        {
                                 return true;
                        }
    }
    
    function processLogJson(data)
    {
        // alert(data.status);
        $("#err_msg").html("");
        if (data.status == "true")
        {

            //  window.location = "redeem-deal.php?id="+data.id;

            //$("#div_error_content").html("<p>"+data.point_message+"</p>");

            var head_msg = "<div class='head_msg'>Message</div>"
            var content_msg = "<div class='content_msg'>" + "* " + data.point_message + "</div>";
            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);


            jQuery.fancybox({
                content: jQuery('#dialog-message').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                openEffect :'none',
				closeEffect :'none',
                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });
            $.ajax({
                type: "POST",
                url: 'process.php',
                data: 'id=' + data.id + '&btn_campaign_detail=true&coupon_code=' + $("#txt_barcode").val(),
                success: function (msg)
                {
                    $("#div_campaign_info").html(msg);
                    $("#btn_submit").css("display", "none");
                }
            });

        }
        else
        {
            // window.location = "redeem-deal.php";
            //$("#div_error_content").html("<p>"+data.message+"</p>");
            var head_msg = "<div class='head_msg'>Message</div>"
            var content_msg = "<div class='content_msg'>" + "* " + data.message + "</div>";
            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);


            jQuery.fancybox({
                content: jQuery('#dialog-message').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                openEffect :'none',
				closeEffect :'none',
                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });
            $("#div_campaign_info").html("");
            return false;
        }
    }
    function precheckingRedeem(formData, jqForm, options)
    {

        var flag = 0;

        jQuery.ajax({
            type: "POST",
            url: 'process.php',
            data: 'loginornot=true',
            async: false,
            success: function (msg)
            {


                var obj = jQuery.parseJSON(msg);


                if (obj.status == "false")
                {

                    window.location.href = obj.link;
                    flag = 1;
                }


            }
        });

        if (flag == 1)
        {
            return false;
        }
        else
        {


            var val = parseInt($("#txt_redeem_deal_value").val());

            if (val <= 0)
            {
                //alert("Enter proper deal value");
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"]; ?></div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

                jQuery.fancybox({
                    content: jQuery('#dialog-message').html(),
                    type: 'html',
                    openSpeed: 300,
                    closeSpeed: 300,
                    openEffect :'none',
					closeEffect :'none',
                    changeFade: 'fast',
                    helpers: {
                        overlay: {
                            opacity: 0.3
                        } // overlay
                    }
                });
                return false;
            } else
            {
                return true;
            }
        }


    }
    function processRedeemJson(data)
    {
        if (data.status == "true") {
            var head_msg = "<div class='head_msg'>Message</div>"
            var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_coupon_redeemed"]; ?></div>";
            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupredeem' name='popupredeem' class='msg_popup_cancel'></div>";
            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);


            jQuery.fancybox({
                content: jQuery('#dialog-message').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                openEffect :'none',
				closeEffect :'none',
                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });
            //window.location = "redeem-deal.php";
        } else
        {
            //$("#div_error_content").html("<p>"+data.message+"</p>");

            var head_msg = "<div class='head_msg'>Message</div>"
            var content_msg = "<div class='content_msg'>" + data.message + "</div>";
            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

            //alert(content_msg);

            jQuery.fancybox({
                content: jQuery('#dialog-message').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                openEffect :'none',
				closeEffect :'none',
                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });

            $("#div_campaign_info").html("");
            return false;
        }
    }

    $("a#redeem-coupons").css("background-color", "orange");


    /****** AUTO COMPLETE VOUCHER CODE TEXT BOX FROM DATABASE *******/
    function gofor_autocomplete(val)
    {
        var flag = 0;
        jQuery.ajax({
            type: "POST",
            url: 'process.php',
            data: 'loginornot=true',
            async: true,
            success: function (msg)
            {
                var obj = jQuery.parseJSON(msg);
                if (obj.status == "false")
                {
                    window.location.href = obj.link;
                    flag = 1;
                }
            }
        });

        if (flag == 0)
        {
            if (val.length >= 4)
            {
                //alert(val);
                url = '<?= WEB_PATH ?>/merchant/get_vouchercode.php';
                //alert(url);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "couponname=" + val,
                    async: true,
                    beforeSend: function () {
                        //closePopup(200);
                        //open_popup('Wait');				   				  
                    },
                    success: function (result) {

                        //alert(result);
                        document.getElementById("autocomplete").style.display = "block";
                        document.getElementById("autocomplete").innerHTML = result;


                    }
                });
            }
            else
            {
                document.getElementById("autocomplete").style.display = "none";
                document.getElementById("div_error_content").innerHTML = "";
            }
        }
    }
   
    jQuery("#popupcancel").live("click", function () {
        jQuery.fancybox.close();
        return false;
    });

    function repalcevalue(val)
    {
        var val = val.innerHTML;
        document.getElementById("txt_barcode").value = val;
        document.getElementById("autocomplete").style.display = "none";

        if (jQuery("#txt_barcode").val().length >= 10 && jQuery("#txt_redeem_deal_value").val().length > 0)
        {
            jQuery('#btn_redeem').removeAttr("disabled");
            jQuery('#btn_redeem').removeClass("disabled");
        }
        else
        {
            jQuery('#btn_redeem').attr("disabled", "");
            jQuery('#btn_redeem').addClass("disabled");
        }
    }
<!--// 369-->

    var merchant_ = '<?php echo $_SESSION['merchant_info']['merchant_parent']; ?>';
//alert(merchant_);
    if (merchant_ == 0)
    {
        var merchant_redeem = '<?php echo $_SESSION['merchant_info']['redeem_location']; ?>';
        //alert(merchant_redeem);
        if (merchant_redeem == "")
        {
            var head_msg = "<div class='head_msg'>Message</div>";
            var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_select_location"]; ?></div>";
            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' onclick='btncanprofile()' class='msg_popup_cancel'></div>";
            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

            //alert(content_msg);

            jQuery.fancybox({
                content: jQuery('#dialog-message').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                openEffect :'none',
				closeEffect :'none',
                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });
        }
    }
    function btncanprofile()
    {
        window.location = "<?= WEB_PATH ?>/merchant/my-profile.php";
    }

    jQuery("#main_redem_selection ul li").click(function () {
        processLoginorNot();
        jQuery("#main_redem_selection ul input").prop("checked", "false");
        jQuery(this).find("input").prop("checked", "true");
        //jQuery(this).find("input").trigger("click");

        var redem_option = jQuery(this).find("input").val();
        if (redem_option == "voucher")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#redem_div").css("display", "block");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "none");
        }
        else if (redem_option == "reward")
        {
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#reward_div").css("display", "block");
            jQuery("#campaign_list_div").css("display", "none");
            jQuery("#enter_reward_number").css("display", "block");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "none");

        }
        else if (redem_option == "stamp")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "block");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "none");
        }
        else if (redem_option == "rew_camp")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#rew_camp_div").css("display", "block");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "none");
        }
        else if (redem_option == "loy_rew_card")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "block");
            jQuery("#gift_card_div").css("display", "none");
        }
        else if (redem_option == "gift_card")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "block");
        }

    });



jQuery("#btn_redeem_lc_card").click(function () {
	
			var lc_code = jQuery("#lc_card_code").val();
            var lc_value = jQuery("#lc_card_value").val();
            var code_msg_div = '#message_in_lc';
            var pts_msg_div = '#message_points_in_lc';
		if(lc_code.length == 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(code_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_loyalty_card"];?>').css("color","red");
            return false;
        }
        
        if(lc_value.length == 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_loyalty"];?>').css("color","red");
            return false;
        }
        if(jQuery.isNumeric(lc_value) == false){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
         if(lc_value <= 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
        if(lc_value.indexOf("-") != -1){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
        jQuery(code_msg_div).html('');
        jQuery(pts_msg_div).html('');
        
        var merchant_id = "<?php echo $_SESSION['merchant_id'] ?>";
        var flag = 0;
        jQuery.ajax({
            type: "POST",
            url: 'process.php',
            data: 'loginornot=true',
            async: false,
            success: function (msg)
            {
                var obj = jQuery.parseJSON(msg);
                if (obj.status == "false")
                {
                    window.location.href = obj.link;
                    flag = 1;
                }
            }
        });

        if (flag == 1)
        {
            return false;
        }
        else
        {
            var stampcard_number = jQuery("#lc_card_code").val();
            var revenue = jQuery("#lc_card_value").val();
            if (stampcard_number == "")
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'>Please enter card</div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

                jQuery.fancybox({
                    content: jQuery('#dialog-message').html(),
                    type: 'html',
                    openSpeed: 300,
                    closeSpeed: 300,
                    openEffect :'none',
					closeEffect :'none',
                    changeFade: 'fast',
                    helpers: {
                        overlay: {
                            opacity: 0.3
                        } // overlay
                    }
                });
                return false;
            }
            else if (revenue == "")
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'>Please enter revenue</div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

                jQuery.fancybox({
                    content: jQuery('#dialog-message').html(),
                    type: 'html',
                    openSpeed: 300,
                    closeSpeed: 300,
                    openEffect :'none',
					closeEffect :'none',
                    changeFade: 'fast',
                    helpers: {
                        overlay: {
                            opacity: 0.3
                        } // overlay
                    }
                });
                return false;
            }
            else
            {
                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'punchcard_for_user=yes&merchant_id=' + merchant_id + "&code=" + stampcard_number + "&revenue=" + revenue,
                    async: false,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        if (obj.status == "true")
                        {
                            var head_msg = "<div class='head_msg'>Message</div>"
                            var content_msg = "<div class='content_msg'>" + obj.message + "</div>";
                            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel pop_cancel_loyalty_card'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            //alert(content_msg);

                            jQuery.fancybox({
                                content: jQuery('#dialog-message').html(),
                                type: 'html',
                                openSpeed: 300,
                                closeSpeed: 300,
                                openEffect :'none',
								closeEffect :'none',
                                changeFade: 'fast',
                                helpers: {
                                    overlay: {
                                        opacity: 0.3
                                    } // overlay
                                }
                            });
							 jQuery('#loyalty_cards').dataTable().fnDraw();
                            jQuery("#lc_card_code").val('');
                            jQuery("#lc_card_value").val('');
                            
                            return false;
                        }
                        else
                        {
                            var head_msg = "<div class='head_msg'>Message</div>";
                            var content_msg = "<div class='content_msg'>" + obj.message + "</div>";
                            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            //alert(content_msg);

                            jQuery.fancybox({
                                content: jQuery('#dialog-message').html(),
                                type: 'html',
                                openSpeed: 300,
                                closeSpeed: 300,
                                openEffect :'none',
								closeEffect :'none',
                                changeFade: 'fast',
                                
                                helpers: {
                                    overlay: {
                                        opacity: 0.3
                                    } // overlay
                                }
                            });
                            return false;
                        }
                    }
                });
            }
        }
    });
     jQuery(document).ready(function () {
		
        /****** SUBMIT FORM WITHOUT REFRESHING PAGE **************/
        jQuery('#campaign_detail_form').ajaxForm({
            dataType: 'json',
            beforeSubmit: loginornot, // CALL REDEEEM FUNCTION
            success: processRedeemJson  /// IF ERROR RETURN BY REDEEM FUNCTION THEN HADLE BY THIS METHOD
        });
         $('#giftcard_value,#gc_value').keypress(function (e) {
			 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
       
               return false;
    }
        
    });
        //ON KEYUP VALIDATION AND PROCESS OF GIFTCARD  
        jQuery("#giftcard_value,#gc_value").on('keyup',function(e){
        if(this.id == 'giftcard_value'){
            var gc_code = jQuery.trim(jQuery("#giftcard_code").val());
            var gc_value = jQuery("#giftcard_value").val();
            var trans = jQuery(".trans1 input[type='radio']:checked").val();
            var code_msg_div = '#message_in';
            var pts_msg_div = '#message_points_in';
        }else if(this.id == 'gc_value'){
            var gc_code =  jQuery.trim(jQuery("#gc_code").val());
            var gc_value = jQuery("#gc_value").val();
            var trans = jQuery(".trans input[type='radio']:checked").val();
            var code_msg_div = '#message';
            var pts_msg_div = '#message_points';
        }
        if(gc_code.charAt(0) != "G" && gc_code.charAt(1) != "C" && gc_code.length != 10){
            jQuery(code_msg_div).html('');
            jQuery(code_msg_div).html('Enter a valid Gift card code').css("color","red");
            return false;
        }
        if (e.which != 8 && e.which != 0 && (e.which < 46 || e.which > 57) && (e.which < 96 || e.which > 105)){
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_positive_numbers"];?>').css("color","red");
                console.log('1');        
            return false;
        }else{
            console.log('else');
            jQuery(pts_msg_div).html('');
            jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'check_info_ajax=true&gc_code='+gc_code+'&gc_value='+gc_value+'&trans='+trans,
                    success: function (msg)
                    {
                        if(msg != ''){
                        var obj = jQuery.parseJSON(msg);
                        jQuery(pts_msg_div).html(obj.msg).css("color","red");
                    }
                    }
                });
            return true;
            jQuery(pts_msg_div).html('');
            jQuery(code_msg_div).html('');
        }
        
        
                return false;
        jQuery(pts_msg_div).html('');
        jQuery(code_msg_div).html('');
        
        
        
        });
        
       
        jQuery("#lc_card_value,#txt_revenue,#txt_stampcard").on('keyup',function(e){
			//alert(this.id);
        if(this.id == 'lc_card_value'){
            var lc_code = jQuery("#lc_card_code").val();
            var lc_value = jQuery("#lc_card_value").val();
            var code_msg_div = '#message_in_lc';
            var pts_msg_div = '#message_points_in_lc';
        }else if(this.id == 'txt_revenue' || this.id == 'txt_stampcard'){
			console.log("here");
            var lc_code = jQuery("#txt_stampcard").val();
            var lc_value = jQuery("#txt_revenue").val();
            var code_msg_div = '#message_in_lc1';
            var pts_msg_div = '#message_points_in_lc1';
        }
        if(lc_code.charAt(0) != "L" && lc_code.charAt(1) != "C" && lc_code.length != 8){
            jQuery(code_msg_div).html('');
            jQuery(code_msg_div).html('Enter a valid loyalty card code').css("color","red");
            return false;
        }
        if (e.which != 8 && e.which != 0 && (e.which < 46 || e.which > 57) && (e.which < 96 || e.which > 105)){
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_positive_numbers"];?>').css("color","red");
			console.log('key');        
            return false;
        }else{
            console.log('else');
            jQuery(pts_msg_div).html('');
            jQuery(code_msg_div).html('');
        }       
        return false;
        jQuery(pts_msg_div).html('');
        jQuery(code_msg_div).html('');
        });
        
       
        
        
        jQuery( ".radio" ).change(function() {
        jQuery("#gc_value").val('');
        jQuery("#giftcard_value").val('');
        jQuery("#message_points").html('');
        jQuery("#message_points_in").html('');
        jQuery("#message").html('');
        jQuery("#messagein").html('');
        });
        
        //CLEAR ALL INPUT VALUES ON TAB SELECTION
        jQuery(document).on('click','#main_redem_selection ul li,#reward_info',function(){
            $("input[type='text']").val("");
            $("input[type='number']").val("");
            jQuery('.rec_trans,.redeem_gc,.redeem_cp').css("color" , "black");
            
        });
        

      //ON CLICK VALIDATION AND PROCESS OF GIFTCARD  
    jQuery(document).on('click','#redeem_user_giftcard,#red_gif_card_submit',function(){
        processLoginorNot();
        if(this.id == 'redeem_user_giftcard'){
        var gc_code = jQuery("#gc_code").val();
        var gc_value = jQuery("#gc_value").val();
        var trans = jQuery(".trans input[type='radio']:checked").val();
        var code_msg_div = '#message';
        var pts_msg_div = '#message_points';
    }else if(this.id == 'red_gif_card_submit'){
        var gc_code = jQuery("#giftcard_code").val();
        var gc_value = jQuery("#giftcard_value").val();
        var trans = jQuery(".trans1 input[type='radio']:checked").val();
        var code_msg_div = '#message_in';
        var pts_msg_div = '#message_points_in';
    }
    
        if(gc_code.length == 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(code_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard"];?>').css("color","red");
            return false;
        }
        
        if(gc_value.length == 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_points"];?>').css("color","red");
            return false;
        }
        if(jQuery.isNumeric(gc_value) == false){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
         if(gc_value <= 0){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
        if(gc_value.indexOf("-") != -1){
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"];?>').css("color","red");
            return false;
        }
        jQuery(code_msg_div).html('');
        jQuery(pts_msg_div).html('');
        jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'redeem_user_giftcard=true&gc_code='+gc_code+'&gc_value='+gc_value+'&trans='+trans,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        //alert(obj.msg);                         
                        var head_msg = "<div class='head_msg'>Message</div>"
                        var content_msg = "<div class='content_msg'>"+obj.msg+"</div>";
                        var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel giftcard_ok'></div>";
                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                        jQuery('#user_giftcards').dataTable().fnDraw();
						jQuery.fancybox({
                            content: jQuery('#dialog-message').html(),
                            type: 'html',
                            openSpeed: 300,
                            closeSpeed: 300,
                            openEffect :'none',
							closeEffect :'none',
                            changeFade: 'fast',
                            helpers: {
                                overlay: {
                                    opacity: 0.3
                                } // overlay
                            }
                        });
                       
                        return false;
                    }
                });
                return false;
            });
            
            jQuery(document).on('click',".giftcard_ok",function(){
						jQuery("#giftcard_code").val("");
						jQuery("#giftcard_value").val("");
				});
                      
        //jQuery("#redeem_rzcampaign,.redeem_cp").click(function(){
        jQuery(document).on('click','#redeem_rzcampaign,.redeem_cp',function(){
        var cp_code = jQuery("#cp_code").val();
        var sel_class = jQuery(this).attr("class");
        if(sel_class == 'redeem_cp'){
            jQuery(this).css("color" , "blue");
            var cp_code = jQuery(this).attr("data-value");
        }
        if(cp_code.length === 0){
            jQuery('#reward_campaign_message').html('');
            jQuery('#reward_campaign_message').html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_reward_campaign_code"];?>').css("color","red");
            return false;
        }
        jQuery('#reward_campaign_message').html('');
        jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'redeem_reward_campaign=true&cp_code='+cp_code,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                            //alert(obj.msg);
                        var obj = jQuery.parseJSON(msg);
                            //alert(obj.msg);                         
                        var head_msg = "<div class='head_msg'>Message</div>"
                        var content_msg = "<div class='content_msg'>"+obj.msg+"</div>";
                        var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                        jQuery('#user_reward_campaigns').dataTable().fnDraw();
                        jQuery.fancybox({
                            content: jQuery('#dialog-message').html(),
                            type: 'html',
                            openSpeed: 300,
                            closeSpeed: 300,
                            openEffect :'none',
							closeEffect :'none',
                            changeFade: 'fast',
                            helpers: {
                                overlay: {
                                    opacity: 0.3
                                } // overlay
                            }
                        });
                        
                        return false;
                    }
                });
                return false;
            });
            
    jQuery(document).on('click','.redeem_reward_card_popup,.reward_btn',function(){
      processLoginorNot();
      jQuery(".reward_btn").css("display", "block");
      jQuery("#reward_card").css("display", "none");
      jQuery("#txt_rewardcard").val('');
      jQuery("#msg_reward_card").html('');
      jQuery("#myModal").modal();
        });
    jQuery(".redeem_reward_card_popup").trigger( "click" );
    jQuery(".closetab_src").click(function(){
      jQuery('#myModal').modal('toggle');
        });
        
        // RECENT TRANSACTIONS VIEW
        jQuery(document).on('click','.recentTransaction,.rec_trans',function(){
        processLoginorNot();
        var sel_class = jQuery(this).attr("class");
        if(sel_class == 'recentTransaction'){
        var gc_code = $.trim(jQuery("#gc_code").val());
        var code_msg_div = '#message';
        var pts_msg_div = '#message_points';
    }else if(sel_class == 'rec_trans'){
        jQuery('.rec_trans').css("color" , "black");
        jQuery(this).css("color" , "blue");
        var gc_code = jQuery(this).attr("data-value");
         var code_msg_div = '#message_in';
        var pts_msg_div = '#message_points_in';
    }
        if(gc_code.length === 0){
            jQuery(pts_msg_div).html('');
            jQuery(code_msg_div).html('');
            jQuery(code_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard"];?>').css("color","red");
            return false;
        }
        jQuery(pts_msg_div).html('');
        jQuery(code_msg_div).html('');
        var otable ='';
        //if(gc_code)
        $('#recent_giftcard_transactions').dataTable().fnDestroy();
        
        otable = $('#recent_giftcard_transactions').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": true,
                            "info": false,
                            "bSort": false,
                            "paging": true,
                            "searching": false,
                            "iDisplayLength": 5,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "oLanguage": {
                                "sEmptyTable": "No transactions founds in the system.",
                                "sZeroRecords": "No transactions to display",
                                "sProcessing": "Loading..."
                            },
                            "fnPreDrawCallback": function (oSettings) {
                                //logged_in_or_not();
                            },
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "recent_giftcard_transactions", "value": true}, {"name": 'gc_code', "value": gc_code});

                            },
                        });
        
                
            jQuery('#rewardGiftModal').modal('show');
            
            });
            
     jQuery(".closetab_rgm").click(function(){
      jQuery('#rewardGiftModal').modal('toggle');
        });
        
        jQuery('.recentBal').on('click',function(){
        processLoginorNot();
        var gc_code = jQuery("#gc_code").val();
        console.log(gc_code.length);
        if(gc_code.length === 0){
            jQuery('#message_points').html('');
            jQuery('#message').html('');
            jQuery('#message').html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard"];?>').css("color","red");
            return false;
        }
        jQuery('#message_points').html('');
        jQuery('#message').html('');
        //var reward_card_num = jQuery("#txt_rewardcard").val();
        //alert(reward_card_number);
        jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'giftcard_balance=true&gc_code='+gc_code,
                    success: function (msg)
                    {
						jQuery("#certificate_id").text("");
						jQuery("#card_expired").removeClass("color_exp");
						jQuery("#card_deleted").removeClass("color_del");
						jQuery("#card_active").removeClass("color_act");
                        var obj = jQuery.parseJSON(msg);
							//alert(obj);
                        if(obj.status == 1){
							if(obj.flag == 1){
								 //$("#delete_card_msg #card_expired").css('color', 'red');
								 $("#card_expired").addClass("color_exp");
							}
							if(obj.flag == 2){
								// $("#delete_card_msg #card_deleted").css('color', 'red');
								 $("#card_deleted").addClass("color_del");
							}
							if(obj.flag == 3){
								 //$("#delete_card_msg #card_active").css('color', 'green');
								 $("#card_active").addClass("color_act");
							}
							if(obj.flag == 4){
								
							}
							jQuery("#certificate_id").text(obj.c_id);
							jQuery('#giftcard_balance tbody').html('<tr><td>'+obj.title+'</td><td>'+obj.value+'</td><td>'+obj.balance+'</td></tr>');
                    }else{
                        jQuery('#giftcard_balance tbody').html('<tr><td colspan="3"><h4>'+obj.msg+'</h4></td><tr>');
                    }
                        jQuery('#checkbal').modal('show');
                    }
                });
                
                
    });
    
    jQuery(".closetab_gcb").click(function(){
      jQuery('#checkbal').modal('toggle');
        });
        
        //GIFTCARD REDEEM FROM MERCHANT SIDE
        $(document).on('click','.redeem_gc',function(){
        jQuery('.redeem_gc').css("color" , "black");
        jQuery('.redeem_gc').parent('td').removeClass('tabactive');
        jQuery(this).css("color" , "blue");
        jQuery(this).parent('td').addClass('tabactive');
        var code = jQuery(this).attr("data-value");
        jQuery("#giftcard_code").val('');
        jQuery("#giftcard_code").val(code);
        jQuery("#mer_redeem_giftcard").show();
    });
    $(document).on('click','.redeem_lc',function(){
        jQuery('.redeem_lc').css("color" , "black");
        jQuery('.redeem_lc').parent('td').removeClass('tabactive');
        jQuery(this).css("color" , "blue");
        jQuery(this).parent('td').addClass('tabactive');
        var code = jQuery(this).attr("data-value");
        jQuery("#lc_card_code").val('');
        jQuery("#lc_card_value").val('');
        jQuery("#lc_card_code").val(code);
        jQuery("#mer_redeem_loyalty_card").show();
    });

    });


    
</script>
