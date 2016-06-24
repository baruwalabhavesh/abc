<?php
/**
 * @uses manage marketing material
 * @used in pages : location-for-marketingmaterial.php,merchant-marketing-coupon.php,merchant-marketing-template.php,my-account-left.php
 * @author Sangeeta Raghavani
 */

check_merchant_session();

/* * ******* get all sub merchant of current login employee **************** */
$arr = file(WEB_PATH . '/merchant/process.php?getallsubmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$ids = $json->all_sub_merchant_id;



/* * *************  Get all active campaign which are public and walk in deals. Private deals will not display here.****** */
$arr = file(WEB_PATH . '/merchant/process.php?btnGetAllpublicwalkinCampaignOfMerchant=yes&mer_id=' . $_SESSION['merchant_id'] . '&action=active&ids=' . $ids);
if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$total_records = $json->total_records;
$records_array = $json->records;

//echo WEB_PATH . '/merchant/process.php?btnGetAllCardlistQRCODE=yes';
$arr_lc = file(WEB_PATH . '/merchant/process.php?btnGetAllCardlistQRCODE=yes&mer_id=' . $_SESSION['merchant_id']);

if(trim($arr_lc[0]) == "") 
{
   unset($arr_lc[0]);
   $arr_lc = array_values($arr_lc);
}
$json_lc = json_decode($arr_lc[0]);
$total_records_lc = $json_lc->total_records;
$records_array_lc = $json_lc->records;


if ($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("edit-compaign.php", $ass_page) || in_array("delete-compaign.php", $ass_page)) {
        //array_push($sort_permision_arr, $n_arr);
}

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Manage marketing Material</title>
        <?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/jquery.tooltip.css" />
        
        <script src="<?= ASSETS_JS ?>/m/jquery.tooltip.js" type="text/javascript"></script>
    </head>

    <body>
        <div id="dialog-message" title="Message" style="display:none">
        </div>

        <div >

            <link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
            <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>

            <!---start header---->
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div id="content">
                    <div class="title_header"><?php echo $merchant_msg['marketingmaterial']['manage_marketing_material_title']; ?></div>
                   
                    <div>


                        <div id="media-upload-header" style="margin-top:10px;">
                            <ul id="sidemenu">
                                <?php
                                if (isset($_REQUEST['tab'])) 
                                {
									if($_REQUEST['tab']=="loyalty")
									{
                                ?>
                                        <li id="tab-type" data-hashval="qrcode_camapaign_tab" class="qrcode_div_campaign">
                                            <a >Campaign</a>
                                        </li>
                                        <li id="tab-type" data-hashval="qrcode_loyalty_tab"  class="qrcode_div_loyalty" >
                                            <a class="current">Loyalty Card</a>
                                        </li>
                                        <li id="tab-type" data-hashval="qrcode_location_tab"  class="qrcode_div_location" >
                                            <a >Location</a>
                                        </li>
                                      <?php
									}
									else if($_REQUEST['tab']=="location")
									{
										 ?>
                                        <li id="tab-type" data-hashval="qrcode_camapaign_tab" class="qrcode_div_campaign">
                                            <a >Campaign</a>
                                        </li>
                                        <li id="tab-type" data-hashval="qrcode_loyalty_tab"  class="qrcode_div_loyalty" >
                                            <a >Loyalty Card</a>
                                        </li>
                                        <li id="tab-type" data-hashval="qrcode_location_tab"  class="qrcode_div_location" >
                                            <a class="current">Location</a>
                                        </li>
                                      <?php
									}   
                                       
                                }
                                else
                                {
                                        ?>
                                        <li id="tab-type" data-hashval="qrcode_camapaign_tab" class="qrcode_div_campaign">
                                            <a class="current">Campaign</a>
                                        </li>
                                         <li id="tab-type" data-hashval="qrcode_loyalty_tab"  class="qrcode_div_loyalty" >
                                            <a >Loyalty Card</a>
                                        </li>
                                        <li id="tab-type" data-hashval="qrcode_location_tab"  class="qrcode_div_location" >
                                            <a >Location</a>
                                        </li>
                                       
                                        <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="all_div_container">
                            <?php
                            $visibility = "";
                            
                            if (isset($_REQUEST['tab'])) 
                            {
								$visibility = 'none';
							}
							else
							{
								$visibility = 'block';
							}
                            ?>
                            <div id="qrcode_div_campaign" class="tabs" style="display:<?php echo $visibility ?>;">
                                <?php
//echo "campaign";
                                ?>
                                <div class="datatable_container">
                                    <div id="qrcode_div_campaign">
                                        <table border="0" cellspacing="1" cellpadding="10" class="tableMerchant cls_left" id="qrcode_campaign_table">
                                            <thead>

                                                <tr>
                                                    <th >&nbsp;</th>
                                                    <th >Campaign Title</th>
                                                    <th >QR Code Status</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                /*                                                 * ** itrate through campaign ************ */
                                                if ($total_records > 0) {
                                                        foreach ($records_array as $Row) {
                                                                ?>
                                                                       <?php
                                                                        /*
                                                                        $wh_arr = array();                                                                        
                                                                        $wh_arr['campaign_id'] = $Row->id;                                                                        
                                                                        $rs_qrcode_campaign = $objDB->Show("qrcode_campaign", $wh_arr);
                                                                        */
                                                                        $rs_qrcode_campaign = $objDB->Conn->Execute("select qrcode_id from campaigns where id =? and qrcode_id!=0",array($Row->id));
                                                                        
                                                                        $wh_arr2 = array();
                                                                        $wh_arr2['campaign_id'] = $Row->id;
                                                                        
                                                                        $rs_qrcode_campaign2 = $objDB->Show("activation_codes", $wh_arr2);
                                                                        if ($rs_qrcode_campaign2->RecordCount() != 0) {
                                                                                $activation_code = $rs_qrcode_campaign2->fields['activation_code'];
                                                                        }

                                                                        if ($rs_qrcode_campaign->RecordCount() == 0) {
                                                                                $printclass = "pcoupon";
                                                                                $downloadclass = "downloadqrcode";
                                                                                $linktext = "Link";
                                                                                $qrcodestring = "";
                                                                        } else {
                                                                                $wh_arr1 = array();
                                                                                $wh_arr1['id'] = $rs_qrcode_campaign->fields['qrcode_id'];
                                                                                $rs_qrcode = $objDB->Show("qrcodes", $wh_arr1);
                                                                                $qrcodestring = $rs_qrcode->fields['qrcode'];
                                                                                $printclass = "rcoupon";
                                                                                $downloadclass = " rdownloadqrcode";
                                                                                $linktext = "Un-link";
                                                                        }
                                                                        ?>
                                                                <tr class="tableDeal">
                                                                    <td>
                                                                        <input type="radio" visit="first" activationcode="<?php echo $activation_code; ?>" id="rd_campaigntitle_<?php echo $Row->id; ?>" name="rd_campaigntitle" value="<?php echo $Row->id; ?>" is_walkin="<?php echo $Row->is_walkin; ?>" />
                                                                    </td>

                                                                    <td class="capitalize_heading"><a href="javascript:void(0)" id="showCamp_<?= $Row->id ?>">
                                                                            <?php
                                                                            echo $Row->title;
                                                                            ?>
                                                                        </a></td>
                                                                    <td>

                                                                        <a href="javascript:void(0)" campid="<?= $Row->id ?>" qval="<?php echo $qrcodestring; ?>" class="<?php echo $downloadclass ?>" ><?php echo $linktext; ?></a>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                        }
                                                } else {
                                                        ?>

                                                        <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="cls_clear"></div>
                                <div align="center" class="marign-top-10">
                                    <input type="submit" value="<?php echo $merchant_msg['marketingmaterial']['download_qr_code']; ?>" name="btn_downloadqrcode" id="btn_downloadqrcode" />&nbsp;&nbsp;
                                    <input type="submit" value="<?php echo $merchant_msg['marketingmaterial']['download_marketing_coupon']; ?>" name="btn_downloadmarketingcoupon" id="btn_downloadmarketingcoupon" />&nbsp;&nbsp;
                                    <input type="submit" value="<?php echo $merchant_msg['marketingmaterial']['download_marketing_material']; ?>" name="btn_downloadmarketingmaterial" id="btn_downloadmarketingmaterial" />
                                </div>
                                <div class="clear">&nbsp;</div>

                            </div>

                            <?php
                            $visibility = "";
                            if (isset($_REQUEST['tab']))
                            { 
								if ($_REQUEST['tab']=="location") 
								{
									$visibility = 'block';
								} 
								else 
								{
									$visibility = 'none';
								}
							}
							else
							{
								$visibility = 'none';
							}
                            ?>
                            <div id="qrcode_div_location" class="tabs" style="display:<?php echo $visibility ?>;">
                                <?php
//echo "Location";
                                if ($_SESSION['merchant_id'] != "") {
                                        /* $Sql_loc = "SELECT * FROM locations l WHERE  l.active=1 and l.created_by=".$_SESSION['merchant_id'];  
                                          $records_array_loc = $objDB->execute_query($Sql_loc); */
                                        $records_array_loc = $objDB->Conn->Execute("SELECT * FROM locations l WHERE  l.active=1 and l.created_by=?", array($_SESSION['merchant_id']));

                                        $total_records_loc = $records_array_loc->RecordCount();
                                }
                                ?>
                                <div class="datatable_container">
                                    <table border="0" cellspacing="1" cellpadding="10" class="tableMerchant"  id="qrcode_location_table" >
                                        <thead>		  
                                            <tr>
                                                <th >&nbsp;</th>
                                                <th >Location Address</th>
                                                <th>QR Code Status</th>
                                            </tr>
                                        </thead>
                                        
                                    </table>
                                </div>

                                <div class="cls_clear"></div>
                                <div align="center" class="marign-top-20">
                                    <input type="button" value="Download QR Code" name="btn_downloadqrcode_location" id="btn_downloadqrcode_location" />&nbsp;&nbsp;
                                    <input type="submit" value="<?php echo $merchant_msg['marketingmaterial']['download_marketing_material']; ?>" name="btn_downloadmarketingmaterial_location" id="btn_downloadmarketingmaterial_location" />&nbsp;&nbsp;

                                </div>
                            </div>	
                        
							<?php
                            $visibility = "";
                            if (isset($_REQUEST['tab']))
                            { 
								if ($_REQUEST['tab']=="loyalty") 
								{
									$visibility = 'block';
								} 
								else 
								{
									$visibility = 'none';
								}
							}
							else
							{
								$visibility = 'none';
							}
                            ?>
                            <div id="qrcode_div_loyalty" class="tabs" style="display:<?php echo $visibility ?>;">
                                <?php
//echo "campaign";
                                ?>

								<div class="datatable_container">
                                        <table border="0" cellspacing="1" cellpadding="10" class="tableMerchant cls_left" id="qrcode_loyalty_table">
                                            <thead>

                                                <tr>
                                                    <th >&nbsp;</th>
                                                    <th >Loyalty Card Title</th>
                                                    <th >QR Code Status</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                /*                                                 * ** itrate through campaign ************ */
                                                if ($total_records_lc > 0) {
                                                        foreach ($records_array_lc as $Row) {
                                                                ?>
                                                                <tr class="tableDeal">
                                                                    <td>
                                                                        <input type="radio" activationcode="<?php echo $Row->activationcode; ?>" visit="first" id="rd_loyaltycardtitle_<?php echo $Row->id; ?>" name="rd_loyaltycardtitle" value="<?php echo $Row->id; ?>" />
                                                                    </td>

                                                                    <td class="capitalize_heading"><a href="javascript:void(0)" id="showCard_<?= $Row->id ?>">
                                                                            <?php
                                                                            echo $Row->title;
                                                                            ?>
                                                                        </a></td>
                                                                    <td>
                                                                        <?php
                                                                        
                                                                        if ($Row->qrcode_id == "0")
                                                                        {
                                                                                $printclass = "pcoupon";
                                                                                $downloadclass = "downloadqrcode loyalty";
                                                                                $linktext = "Link";
                                                                                $qrcodestring = "";
                                                                        } 
                                                                        else 
                                                                        {
																			$wh_arr1 = array();
																			$wh_arr1['id'] = $Row->qrcode_id;
																			$rs_qrcode = $objDB->Show("qrcodes", $wh_arr1);
																			$qrcodestring = $rs_qrcode->fields['qrcode'];
																			$printclass = "rcoupon";
																			$downloadclass = " rdownloadqrcode loyalty";
																			$linktext = "Un-link";
                                                                        }
                                                                        ?>
                                                                        <a href="javascript:void(0)" cardid="<?= $Row->id ?>" qval="<?php echo $qrcodestring; ?>" class="<?php echo $downloadclass ?>" ><?php echo $linktext; ?></a>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                        }
                                                } else {
                                                        ?>

                                                        <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                     
								</div>				

                                <div class="cls_clear"></div>
                                <div align="center" class="marign-top-10">
                                    <input type="submit" value="<?php echo $merchant_msg['marketingmaterial']['download_qr_code']; ?>" name="btn_downloadqrcode_loyalty" id="btn_downloadqrcode_loyalty" />&nbsp;&nbsp;
                                    <input type="submit" value="<?php echo $merchant_msg['marketingmaterial']['download_marketing_coupon']; ?>" name="btn_downloadmarketingcoupon_loyalty" id="btn_downloadmarketingcoupon_loyalty" />&nbsp;&nbsp;
                                    <input type="submit" value="<?php echo $merchant_msg['marketingmaterial']['download_marketing_material']; ?>" name="btn_downloadmarketingmaterial_loyalty" id="btn_downloadmarketingmaterial_loyalty" />
                                </div>
                                <div class="clear">&nbsp;</div>

                            </div>
                        </div>

                        <!--end of content--></div>
                    <!--end of contentContainer--></div>

                <!---------start footer--------------->
                <div>
                    <?php
                    require_once(MRCH_LAYOUT . "/footer.php");
                    ?>
                    <!--end of footer--></div>

            </div>

            <input  type="hidden" value="" name="hdn_qrcodedn_location_id" id="hdn_qrcodedn_location_id" />
            <input  type="hidden" value="" name="hdn_qrcodedn_campaign_id" id="hdn_qrcodedn_campaign_id" />
            <input  type="hidden" value="" name="hdn_qrcodedn_card_id" id="hdn_qrcodedn_card_id" />
            <input  type="hidden" value="" name="hdn_qrcode" id="hdn_qrcode" />
            <input type="hidden" value="" name="hdn_dettach_qrcode_campaign_id" id="hdn_dettach_qrcode_campaign_id" />
            <input type="hidden" value="" name="hdn_dettach_qrcode_location_id" id="hdn_dettach_qrcode_location_id" />
            <input type="hidden" value="" name="hdn_dettach_qrcode_card_id" id="hdn_dettach_qrcode_card_id" />
            <input  type="hidden" value="" name="hdn_id_download_or_print" id="hdn_id_download_or_print" />

            <div style="display:none" >
                <div class="QRcode_detail_div" style="">
                </div>
                <div class="locationlist_div" style="">
                </div>
            </div>


            <div id="confirmation_div" style="display:none">

            </div>
            <?php
            $_SESSION['msg'] = "";
            ?>
            <?php
            $confirmationstring = '';
            //$confirmationstring .='<div>';
            $confirmationstring .='<div align="center">';
            $confirmationstring .= $merchant_msg['marketingmaterial']['msg_unlink_qr_code'];
            $confirmationstring .='</div>';
			$confirmationstring .='</div>';
            $confirmationstring .='<div align="center">';
            $confirmationstring .='<input type="submit" name="btn_no" id="btn_no" value="No" > &nbsp;&nbsp; ';
            $confirmationstring .='<input type="submit" name="btn_yes" id="btn_yes" value="Yes" >';

            $confirmationstring .='</div>';

           

            $confirmationstring_loc = '';
            //$confirmationstring_loc .='<div>';
            $confirmationstring_loc .='<div align="center">';
            $confirmationstring_loc .= $merchant_msg['marketingmaterial']['msg_unlink_qr_code'];
            $confirmationstring_loc .='</div>';
			$confirmationstring_loc .='</div>';
            $confirmationstring_loc .='<div align="center">';
            $confirmationstring_loc .='<input type="submit" name="btn_no_loc" id="btn_no_loc" value="No" > &nbsp;&nbsp; ';
            $confirmationstring_loc .='<input type="submit" name="btn_yes_loc" id="btn_yes_loc" value="Yes" >';

            $confirmationstring_loc .='</div>';

            
            
            $confirmationstring_loyalty = '';
            //$confirmationstring_loyalty .='<div>';
            $confirmationstring_loyalty .='<div align="center">';
            $confirmationstring_loyalty .= $merchant_msg['marketingmaterial']['msg_unlink_qr_code'];
            $confirmationstring_loyalty .='</div>';
			$confirmationstring_loyalty .='</div>';
            $confirmationstring_loyalty .='<div align="center">';
            $confirmationstring_loyalty .='<input type="submit" name="btn_no_loyalty" id="btn_no_loyalty" value="No" > &nbsp;&nbsp; ';
            $confirmationstring_loyalty .='<input type="submit" name="btn_yes_loyalty" id="btn_yes_loyalty" value="Yes" >';

            $confirmationstring_loyalty .='</div>';

            
            

            /*             * *** set size options **** */
            $sizeoptions = '';
            $sizeoptions .='<div class="margin-left-20"> ';
            $sizeoptions .='<span>' . $merchant_msg['marketingmaterial']['field_select_size'] . '</span><select name="opt_qrcodesize" id="opt_qrcodesize">';
            $sizeoptions .='<option value="80"> 80 X 80 </option>';
            $sizeoptions .='<option value="120"> 120 X 120 </option>';
            $sizeoptions .='<option value="200"> 200 X 200 </option>';
            $sizeoptions .='<option value="275"> 275 X 275 </option>';
            $sizeoptions .='option value="350"> 350 X 350 </option>';
            $sizeoptions .='</select>';
            $sizeoptions .='<br/><br /> ';
            $sizeoptions .='<span>' . $merchant_msg['marketingmaterial']['field_select_qr_code_fromat'] . '</span><select name="opt_qrcodetype" id="opt_qrcodetype">';
            //$sizeoptions .='<option value="1"> pdf  </option>';
            $sizeoptions .='<option value="2"> jpeg  </option>';
            $sizeoptions .='<option value="3"> Vector  </option>';
            $sizeoptions .='</select>';
            $sizeoptions .='<br/><br /><br /> ';
            $sizeoptions .='<input type="submit" name="btndownloadqrcode" id="btndownloadqrcode" value="' . $merchant_msg['marketingmaterial']['download_qr_code'] . '" >';
            $sizeoptions .='&nbsp;&nbsp;&nbsp;<input type="submit" name="btncancel" id="btncancel" value="' . $merchant_msg['index']['btn_cancel'] . '" ></div>';

            $sizeoptions_loc = '';
            $sizeoptions_loc .='<div class="margin-left-20"> ';
            $sizeoptions_loc .='<span>' . $merchant_msg['marketingmaterial']['field_select_size'] . '</span><select name="opt_qrcodesize" id="opt_qrcodesize">';
            $sizeoptions_loc .='<option value="80"> 80 X 80 </option>';
            $sizeoptions_loc .='<option value="120"> 120 X 120 </option>';
            $sizeoptions_loc .='<option value="200"> 200 X 200 </option>';
            $sizeoptions_loc .='<option value="275"> 275 X 275 </option>';
            $sizeoptions_loc .='option value="350"> 350 X 350 </option>';
            $sizeoptions_loc .='</select>';
            $sizeoptions_loc .='<br/><br /> ';
            $sizeoptions_loc .='<span>' . $merchant_msg['marketingmaterial']['field_select_qr_code_fromat'] . '</span><select name="opt_qrcodetype" id="opt_qrcodetype">';
            //$sizeoptions_loc .='<option value="1"> pdf  </option>';
            $sizeoptions_loc .='<option value="2"> jpeg  </option>';
            $sizeoptions_loc .='<option value="3"> Vector  </option>';
            $sizeoptions_loc .='</select>';
            $sizeoptions_loc .='<br/><br /><br /> ';
            $sizeoptions_loc .='<div style="text-align:center"><input type="submit" name="btndownloadqrcode_location" id="btndownloadqrcode_location" value="' . $merchant_msg['marketingmaterial']['download_qr_code'] . '" >';
            $sizeoptions_loc .='&nbsp;&nbsp;&nbsp;<input type="submit" name="btncancel" id="btncancel" value="' . $merchant_msg['index']['btn_cancel'] . '" ></div>';
            $sizeoptions_loc .='</div>';
			
            $sizeoptions_loyalty = '';
            $sizeoptions_loyalty .='<div class="margin-left-20"> ';
            $sizeoptions_loyalty .='<span>' . $merchant_msg['marketingmaterial']['field_select_size'] . '</span><select name="opt_qrcodesize" id="opt_qrcodesize">';
            $sizeoptions_loyalty .='<option value="80"> 80 X 80 </option>';
            $sizeoptions_loyalty .='<option value="120"> 120 X 120 </option>';
            $sizeoptions_loyalty .='<option value="200"> 200 X 200 </option>';
            $sizeoptions_loyalty .='<option value="275"> 275 X 275 </option>';
            $sizeoptions_loyalty .='option value="350"> 350 X 350 </option>';
            $sizeoptions_loyalty .='</select>';
            $sizeoptions_loyalty .='<br/><br /> ';
            $sizeoptions_loyalty .='<span>' . $merchant_msg['marketingmaterial']['field_select_qr_code_fromat'] . '</span><select name="opt_qrcodetype" id="opt_qrcodetype">';
            //$sizeoptions_loyalty .='<option value="1"> pdf  </option>';
            $sizeoptions_loyalty .='<option value="2"> jpeg  </option>';
            $sizeoptions_loyalty .='<option value="3"> Vector  </option>';
            $sizeoptions_loyalty .='</select>';
            $sizeoptions_loyalty .='<br/><br /><br /> ';
            $sizeoptions_loyalty .='<div style="text-align:center"><input type="submit" name="btndownloadqrcode_loyalty" id="btndownloadqrcode_loyalty" value="' . $merchant_msg['marketingmaterial']['download_qr_code'] . '" >';
            $sizeoptions_loyalty .='&nbsp;&nbsp;&nbsp;<input type="submit" name="btncancel" id="btncancel" value="' . $merchant_msg['index']['btn_cancel'] . '" ></div>';
            $sizeoptions_loyalty .='</div>'
			?>
            <!--- for download qrcode -->
            <script>

                    jQuery("#btn_downloadcancel_location").live("click", function () {
                        //alert("hi");
                        jQuery("li#tab-type.qrcode_div_campaign a").trigger("click");
                    });

                    jQuery("#btndownloadqrcode_location").live("click", function () {


                        var sp_size = jQuery(".fancybox-inner #opt_qrcodesize").val();
                        var sp_type = jQuery(".fancybox-inner #opt_qrcodetype").val();


                        //var qrcodeid = "<?php if (isset($qrcodestring)) echo $qrcodestring; ?>";
						var qrcodeid = jQuery("#qrcode_div_location a[locid="+jQuery("#hdn_qrcodedn_location_id").val()+"]").attr("qval");
                        console.log("qrcodeid = "+qrcodeid);

                        if (sp_type == 1)
                        {
                            window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode.php?id=" + jQuery("#hdn_qrcodedn_location_id").val() + "&size=" + sp_size + "&is_location=1&qrcodeid=" + qrcodeid;
                        }
                        else if (sp_type == 2) {
							/*
                            jQuery.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "get_campaign_qrcode_url=yes&id=" + jQuery("#hdn_qrcodedn_location_id").val() + "&is_location=1",
                                async: false,
                                success: function (msg) {
                                    var obj = jQuery.parseJSON(msg);
                                    //is_location=1&
                                    window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode_jpg.php?id=" + jQuery("#hdn_qrcodedn_location_id").val() + "&size=" + sp_size + "&is_location=1&qrcodeid=" + qrcodeid;
                                    // window.location.href= '<?php echo WEB_PATH . "/QR-Generator-PHP-master/php/qr.php?t=j&d="; ?>'+obj.qrcode_url+"&size="+sp_size+'&download=qrcode&qrcodeid='+qrcodeid;
                                }
                            });
                            */
                            window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode_jpg.php?id=" + jQuery("#hdn_qrcodedn_location_id").val() + "&size=" + sp_size + "&is_location=1&qrcodeid=" + qrcodeid;
                        }
                        else {
                            //document.execCommand('SaveAs',true,'camp_image1.jpg');
							/*	
                            jQuery.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "get_campaign_qrcode_url=yes&id=" + jQuery("#hdn_qrcodedn_location_id").val() + "&is_location=1",
                                async: false,
                                success: function (msg) {

                                    var obj = jQuery.parseJSON(msg);
                                    //   alert( '<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>'+obj.qrcode_url+"&qrcodeid="+qrcodeid+"&size="+sp_size);
                                    //alert('<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>'+obj.qrcode_url+"&size="+sp_size+"&qrcodeid="+qrcodeid);
                                    window.location.href = '<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>' + obj.qrcode_url + "&qrcodeid=" + obj.qrcode_string + "&size=" + sp_size;
                                }
                            });
                            */
                            window.location.href = '<?php echo WEB_PATH . "/merchant/download_qrcode.php?"; ?>' + "qrcodeid=" + qrcodeid + "&size=" + sp_size;
                        }

                        close();
                        close_popup('confirmation');
                        jQuery.fancybox.close();
                    });

                    jQuery('#btn_downloadqrcode_location').click(function () {

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

                                }
                                else
                                {

                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='rd_locationtitle']:checked");

                                    if (selected.length > 0)
                                    {
                                        selectedValue = selected.val();
					
                                        jQuery("#hdn_qrcodedn_location_id").val(selectedValue);

                                        if (jQuery.trim(jQuery("a[locid='" + selectedValue + "']").attr("class")) == "rdownloadqrcode loc")
                                        {
                                            jQuery(".locationlist_div").html('<?php echo $sizeoptions_loc; ?>');
                                            var head_msg = "<div class='head_msg' >Download QR Code</div>"
                                            var content_msg = '<div class="content_msg"><?php echo $sizeoptions_loc; ?></div>';
                                            var footer_msg = "<div><hr><input type='button'  value='OK' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                            jQuery("#btncancel").live("click", function () {
                                                jQuery.fancybox.close();
                                                //close_popup('confirmation');
                                                return false;
                                            });
                                            download_function();
                                        }
                                        else {
                                            
                                            var head_msg = "<div class='head_msg'>Message</div>"
                                            var content_msg = "<div class='content_msg' style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_qr_code_selected_location']; ?></div>";
                                            var footer_msg = "<div><input type='button'  value='OK' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                        }
                                        //  if()

                                    }
                                    else
                                    {

                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "attach_message_location=yes",
                                            async: false,
                                            success: function (msg) {

                                                jQuery(".locationlist_div").html(msg);
                                                //open_popup('confirmation');
                                                var head_msg = "<div class='head_msg' >Message</div>"
                                                var content_msg = "<div class='content_msg' style='text-align:center'> Please select location.</div>";
                                                var footer_msg = "<div><input type='button'  value='OK' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                                jQuery.fancybox({
                                                    content: jQuery('#dialog-message').html(),
                                                    type: 'html',
                                                    openSpeed: 300,
                                                    closeSpeed: 300,
                                                    changeFade: 'fast',
                                                    helpers: {
                                                        overlay: {
                                                            opacity: 0.3
                                                        } // overlay
                                                    }
                                                });
                                                download_function();
                                            }
                                        });

                                    }



                                    return false;

                                }


                            }

                        });



                    });

                    function download_function()
                    {
                        jQuery("#btn_cancel").click(function () {
                            close_popup("confirmation");
                        });

                        jQuery("#btn_continue").click(function () {
                            window.location.href = "<?php echo WEB_PATH ?>/merchant/locations_for_marketingmaterial.php";
                        });
                    }

			var oCache = {
                    iCacheLower: -1
                };

                function fnSetKey(aoData, sKey, mValue)
                {
                    for (var i = 0, iLen = aoData.length; i < iLen; i++)
                    {
                        if (aoData[i].name == sKey)
                        {
                            aoData[i].value = mValue;
                        }
                    }
                }

                function fnGetKey(aoData, sKey)
                {
                    for (var i = 0, iLen = aoData.length; i < iLen; i++)
                    {
                        if (aoData[i].name == sKey)
                        {
                            return aoData[i].value;
                        }
                    }
                    return null;
                }

                function fnDataTablesPipeline(sSource, aoData, fnCallback) {
                    var iPipe = 2; /* Ajust the pipe size */

                    var bNeedServer = false;
                    var sEcho = fnGetKey(aoData, "sEcho");
                    var iRequestStart = fnGetKey(aoData, "iDisplayStart");
                    var iRequestLength = fnGetKey(aoData, "iDisplayLength");
                    var iRequestEnd = iRequestStart + iRequestLength;
                    oCache.iDisplayStart = iRequestStart;

                    /* outside pipeline? */
                    if (oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper)
                    {
                        bNeedServer = true;
                    }

                    /* sorting etc changed? */
                    if (oCache.lastRequest && !bNeedServer)
                    {
                        for (var i = 0, iLen = aoData.length; i < iLen; i++)
                        {
                            if (aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho")
                            {
                                if (aoData[i].value != oCache.lastRequest[i].value)
                                {
                                    bNeedServer = true;
                                    break;
                                }
                            }
                        }
                    }

                    /* Store the request for checking next time around */
                    oCache.lastRequest = aoData.slice();

                    if (bNeedServer)
                    {
                        if (iRequestStart < oCache.iCacheLower)
                        {
                            iRequestStart = iRequestStart - (iRequestLength * (iPipe - 1));
                            if (iRequestStart < 0)
                            {
                                iRequestStart = 0;
                            }
                        }

                        oCache.iCacheLower = iRequestStart;
                        oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
                        oCache.iDisplayLength = fnGetKey(aoData, "iDisplayLength");
                        fnSetKey(aoData, "iDisplayStart", iRequestStart);
                        fnSetKey(aoData, "iDisplayLength", iRequestLength * iPipe);

                        jQuery.getJSON(sSource, aoData, function (json) {
                            /* Callback processing */
                            oCache.lastJson = jQuery.extend(true, {}, json);

                            if (oCache.iCacheLower != oCache.iDisplayStart)
                            {
                                json.aaData.splice(0, oCache.iDisplayStart - oCache.iCacheLower);
                            }
                            json.aaData.splice(oCache.iDisplayLength, json.aaData.length);

                            fnCallback(json)
                        });
                    }
                    else
                    {
                        json = jQuery.extend(true, {}, oCache.lastJson);
                        json.sEcho = sEcho; /* Update the echo for each response */
                        json.aaData.splice(0, iRequestStart - oCache.iCacheLower);
                        json.aaData.splice(iRequestLength, json.aaData.length);
                        fnCallback(json);
                        return;
                    }
                }
                    jQuery(document).ready(function () {
                    
                        var oTable = jQuery('#qrcode_location_table').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bSort": false,
                            "info": false,
                            // "sPaginationType": "full_numbers",
                            "bProcessing": true,
                            "bServerSide": true,
                            "iDisplayLength": 10,
                            "oLanguage": {
                                "sEmptyTable": "No locations founds in the system.",
                                "sZeroRecords": "No locations to display",
                                "sProcessing": "Loading..."
                            },
			"fnPreDrawCallback": function( oSettings ) {
			      jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'loginornot=true',
				success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="false") 
						{
							window.location.href="<?php echo WEB_PATH ?>/merchant/register.php";
						}
					
					}
				});
			},
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "manage_marketing_material_location", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'});

                            },
                            "fnServerData": fnDataTablesPipeline,
                            "aoColumns": [null, {"sClass":"capitalize_heading"}, null]

                        });
                    });
                    jQuery("li#tab-type a").click(function () {

                        //alert(jQuery(this).html());			

                        jQuery("#sidemenu li a").each(function () {
                            jQuery(this).removeClass("current");
                        });

                        jQuery(this).addClass("current");

                        var cls = jQuery(this).parent().attr("class");

                        jQuery(".tabs").each(function () {
                            jQuery(this).css("display", "none");
                        });

                        jQuery('#' + cls).css("display", "block");

                    });

                    /******** bind qr code events after link and unlink qrcode with campaign ************/


                    jQuery("#btnattachqrcode_loc").live("click", function () {
                        // alert("In attchment code");
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

                                }
                                else
                                {
                                    var selectedVal = "";
                                    var selectedValue = "";
                                    var selected = jQuery("input[type='radio'][name='opt_qrcode']:checked");

                                    var ele1 = "";

                                    jQuery(".downloadqrcode").each(function () {

                                        if (jQuery(this).attr("locid") == jQuery("#hdn_qrcode_location_id").val())
                                        {
                                            ele1 = jQuery(this);
                                        }

                                    });

                                    if (selected.length > 0)
                                    {
                                        selectedValue = selected.val();
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "attachQRcode_location=yes&location_id=" + jQuery("#hdn_qrcode_location_id").val() + "&qrcode_id=" + selectedValue,
                                            async: false,
                                            success: function (msg) {

                                                var obj = jQuery.parseJSON(msg);
                                                jQuery.fancybox.close();

                                                jQuery("#hdn_qrcode").val(obj.qval);
                                                ele1.removeClass("downloadqrcode");
                                                ele1.removeClass("loc");
                                                ele1.addClass("rdownloadqrcode");
                                                ele1.addClass("loc");
                                                ele1.text("Unlink");
                                                jQuery(".rdownloadqrcode").each(function () {
                                                    if (jQuery(this).attr("locid") == jQuery("#hdn_qrcode_location_id").val())
                                                    {
                                                        ele2 = jQuery(this);
                                                    }

                                                });
                                                ele2.attr("qval", obj.qval);
                                                jQuery(".rdownloadqrcode").click(function () {
                                                    // open_popup('qrcode');
                                                    jQuery("#hdn_dettach_qrcode_location_id").val(jQuery(this).attr("locid"));

                                                    jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                                                    jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                                                    var head_msg = "<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['unlink_qr_code']; ?></div>"
                                                    var content_msg = '<div class="content_msg"><?php echo $confirmationstring; ?>';
                                                    var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                    jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                                    jQuery.fancybox({
                                                        content: jQuery("#dialog-message").html(),
                                                        type: 'html',
                                                        openSpeed: 300,
                                                        closeSpeed: 300,
                                                        changeFade: 'fast',
                                                        helpers: {
                                                            overlay: {
                                                                opacity: 0.3
                                                            } // overlay
                                                        }
                                                    });
                                                    bind_confirmation_event();
                                                    // bind_event1();
                                                    close();
                                                    //$("#jqReviewHelpfulMessageNotification").html(msg);
                                                });
                                                close_popup('qrcode');

                                            }
                                        });
                                    } else {
                                        //alert("Please Select QR Code");
                                        close_popup('qrcode');
                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_select_qr_code']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }
                                }
                            }
                        });

                    });
					
					jQuery("#btnattachqrcode_loyalty").live("click", function () {
                        // alert("In attchment code");
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

                                }
                                else
                                {
                                    var selectedVal = "";
                                    var selectedValue = "";
                                    var selected = jQuery("input[type='radio'][name='opt_qrcode']:checked");

                                    var ele1 = "";

                                    jQuery(".downloadqrcode").each(function () {

                                        if (jQuery(this).attr("cardid") == jQuery("#hdn_qrcode_loyalty_id").val())
                                        {
                                            ele1 = jQuery(this);
                                        }

                                    });

                                    if (selected.length > 0)
                                    {
                                        selectedValue = selected.val();
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "attachQRcode_loyalty=yes&cardid=" + jQuery("#hdn_qrcode_loyalty_id").val() + "&qrcode_id=" + selectedValue,
                                            async: false,
                                            success: function (msg) {

                                                var obj = jQuery.parseJSON(msg);
                                                jQuery.fancybox.close();

                                                jQuery("#hdn_qrcode").val(obj.qval);
                                                ele1.removeClass("downloadqrcode");
                                                ele1.removeClass("loyalty");
                                                ele1.addClass("rdownloadqrcode");
                                                ele1.addClass("loyalty");
                                                ele1.text("Unlink");
                                                jQuery(".rdownloadqrcode").each(function () {
                                                    if (jQuery(this).attr("cardid") == jQuery("#hdn_qrcode_loyalty_id").val())
                                                    {
                                                        ele2 = jQuery(this);
                                                    }

                                                });
                                                ele2.attr("qval", obj.qval);
                                                jQuery(".rdownloadqrcode").click(function () {
                                                    // open_popup('qrcode');
                                                    jQuery("#hdn_dettach_qrcode_card_id").val(jQuery(this).attr("cardid"));

                                                    jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                                                    jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                                                    var head_msg = "<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['unlink_qr_code']; ?></div>"
                                                    var content_msg = '<div class="content_msg"><?php echo $confirmationstring; ?></div>';
                                                    var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                    jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                                    jQuery.fancybox({
                                                        content: jQuery("#dialog-message").html(),
                                                        type: 'html',
                                                        openSpeed: 300,
                                                        closeSpeed: 300,
                                                        changeFade: 'fast',
                                                        helpers: {
                                                            overlay: {
                                                                opacity: 0.3
                                                            } // overlay
                                                        }
                                                    });
                                                    bind_confirmation_event();
                                                    // bind_event1();
                                                    close();
                                                    //$("#jqReviewHelpfulMessageNotification").html(msg);
                                                });
                                                close_popup('qrcode');

                                            }
                                        });
                                    } 
                                    else 
                                    {
                                        //alert("Please Select QR Code");
                                        close_popup('qrcode');
                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_select_qr_code']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }
                                }
                            }
                        });

                    });
                    
                    function bind_event() {
                    }
                    jQuery("#btnattachqrcode").live("click", function () {

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
                                }
                                else
                                {


                                    //   alert("In attchment code");
                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='opt_qrcode']:checked");

                                    var ele1 = "";

                                    jQuery(".downloadqrcode").each(function () {

                                        if (jQuery(this).attr("campid") == jQuery("#hdn_qrcode_campaign_id").val())
                                        {
                                            ele1 = jQuery(this);
                                        }

                                    });
                                    //alert(jQuery("#hdn_qrcode_campaign_id").val());
                                    if (selected.length > 0)
                                    {
                                        selectedValue = selected.val();
                                        /******** Attach Qr code with campaign *************/
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "attachQRcode_campaign=yes&campaign_id=" + jQuery("#hdn_qrcode_campaign_id").val() + "&qrcode_id=" + selectedValue,
                                            async: false,
                                            success: function (msg) {
                                                jQuery.fancybox.close();
                                                var obj = jQuery.parseJSON(msg);
                                                var head_msg = "<div class='head_msg'>Message</div>"
                                                var content_msg = "<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['qr_code_linked']; ?></div>";
                                                var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                                                jQuery("#hdn_qrcode").val(obj.qval);
                                                ele1.removeClass("downloadqrcode");
                                                ele1.addClass("rdownloadqrcode");
                                                ele1.text("Un-link");
                                                jQuery(".rdownloadqrcode").each(function () {
                                                    if (jQuery(this).attr("campid") == jQuery("#hdn_qrcode_campaign_id").val())
                                                    {
                                                        ele2 = jQuery(this);
                                                    }

                                                });
                                                ele2.attr("qval", obj.qval);
                                                /***** un link the linked qr code from campaign. First get confirmation yes or no ********/
                                                jQuery(".rdownloadqrcode").live("click", function () {
                                                    jQuery("#hdn_dettach_qrcode_campaign_id").val(jQuery(this).attr("campid"));

                                                    jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                                                    var head_msg = "<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['unlink_qr_code']; ?></div>"
                                                    var content_msg = '<div class="content_msg"><?php echo $confirmationstring; ?></div>';
                                                    var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                    jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                                    jQuery.fancybox({
                                                        content: jQuery("#dialog-message").html(),
                                                        type: 'html',
                                                        openSpeed: 300,
                                                        closeSpeed: 300,
                                                        changeFade: 'fast',
                                                        helpers: {
                                                            overlay: {
                                                                opacity: 0.3
                                                            } // overlay
                                                        }
                                                    });
                                                    bind_confirmation_event();
                                                    // bind_event1();
                                                    close();
                                                });
                                                close_popup('qrcode');

                                            }
                                        });
                                    } else {
                                        //alert("Please Select QR Code");
                                        close_popup('qrcode');
                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['please_select_qr_code']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }


                                }
                            }
                        });
                    });
//}

                    /**** if no then  close popup ********/
                    jQuery("#btn_no").live("click", function () {
                        //  close_popup("qrcode");
                        jQuery.fancybox.close();
                    });
                    /********* If user confirm to unlink qrcode then *********/
                    jQuery("#btn_yes").live("click", function () {
                        var val = jQuery("#hdn_dettach_qrcode_campaign_id").val();

                        jQuery(".rdownloadqrcode").each(function () {
                            if (jQuery(this).attr("campid") == jQuery("#hdn_dettach_qrcode_campaign_id").val())
                            {
                                ele1 = jQuery(this);
                            }
                        });
                        jQuery.ajax({
                            type: "POST",
                            url: "<?= WEB_PATH ?>/merchant/process.php",
                            data: "removeqrcodeattachment=yes&campaign_id=" + val,
                            async: false,
                            success: function (msg) {

                                ele1.removeClass("rdownloadqrcode");
                                ele1.addClass("downloadqrcode");
                                ele1.text("Link");
                                jQuery.fancybox.close();

                                /****** open availabel qr code list *********/
                                jQuery(".downloadqrcode").live("click", function () {
                                    ///   alert("In");
                                    // open_popup('qrcode');

                                    jQuery.ajax({
                                        type: "POST",
                                        url: "<?= WEB_PATH ?>/merchant/process.php",
                                        data: "getQRcodelist=yes&campaign_id=" + jQuery(this).attr("campid"),
                                        async: false,
                                        success: function (msg) {

                                            jQuery(".QRcode_detail_div").html(msg);

                                            var data_found = jQuery(".QRcode_detail_div #hdn_qrcode_data_found").val();
                                            if (data_found == "false")
                                            {
                                                var head_msg = "<div class='head_msg'>Message</div>"

                                            }
                                            else
                                            {
                                                var head_msg = "<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['download_qr_code']; ?></div>"
                                            }
                                            var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery("#dialog-message").html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                            bind_event();
                                            close();
                                        }
                                    });

                                    return false;

                                });
                                close_popup('qrcode');
                            }
                        });
                    });
                    // }
                    /*********** cancel event *****/
                    jQuery("#btncancel").live("click", function () {

                        ///alert("Inn");
                        jQuery.fancybox.close();
                        //close_popup('qrcode');
                        return false;
                    });


                    jQuery("#btncancel").click(function () {
                        //close_popup('qrcode');
                        return false;
                    });


                    /********* link download functionality ************/
                    function download_function()
                    {
                        /****** if merchant didnt select any campaign and try to download qrcode then merchant will ask to continue with location ********/
                        jQuery("#btn_continue").live("click", function () {
                            window.location.href = "<?php echo WEB_PATH ?>/merchant/locations_for_marketingmaterial.php";
                        });
                    }

                    /***** before get qrcode list check whether merchant session is active or not *****/
                    jQuery(".downloadqrcode").live("click", function () {

						if (jQuery(this).hasClass("loyalty"))
                        {
                            // location
                            //open_popup('qrcode');
                            var card_id = jQuery(this).attr("cardid");
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
                                    }
                                    else
                                    {
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "getQRcodelist=yes&cardid=" + card_id,
                                            async: false,
                                            success: function (msg) {
                                                jQuery(".QRcode_detail_div").html(msg);
                                                var data_found = jQuery(".QRcode_detail_div #hdn_qrcode_data_found").val();
                                                if (data_found == "false")
                                                {
                                                    var head_msg = "<div class='head_msg'>Message</div>"
                                                }
                                                else
                                                {
                                                    var head_msg = "<div class='head_msg'>Link QR Code</div>"
                                                }
                                                var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                                var footer_msg = "<div><input type='button'  value='OK' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                                jQuery.fancybox({
                                                    content: jQuery("#dialog-message").html(),
                                                    type: 'html',
                                                    openSpeed: 300,
                                                    closeSpeed: 300,
                                                    changeFade: 'fast',
                                                    helpers: {
                                                        overlay: {
                                                            opacity: 0.3
                                                        } // overlay
                                                    }
                                                });
                                                bind_event();
                                                close();
                                            }
                                        });
                                        return false;
                                    }
                                }
                            });
                        }	
                        else if (jQuery(this).hasClass("loc"))
                        {
                            // location
                            //open_popup('qrcode');
                            var loc_id = jQuery(this).attr("locid");
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
                                    }
                                    else
                                    {
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "getQRcodelist=yes&location_id=" + loc_id,
                                            async: false,
                                            success: function (msg) {
                                                jQuery(".QRcode_detail_div").html(msg);
                                                var data_found = jQuery(".QRcode_detail_div #hdn_qrcode_data_found").val();
                                                if (data_found == "false")
                                                {
                                                    var head_msg = "<div class='head_msg'>Message</div>"
                                                }
                                                else
                                                {
                                                    var head_msg = "<div class='head_msg'>Link QR Code</div>"
                                                }
                                                var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                                var footer_msg = "<div><input type='button'  value='OK' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                                jQuery.fancybox({
                                                    content: jQuery("#dialog-message").html(),
                                                    type: 'html',
                                                    openSpeed: 300,
                                                    closeSpeed: 300,
                                                    changeFade: 'fast',
                                                    helpers: {
                                                        overlay: {
                                                            opacity: 0.3
                                                        } // overlay
                                                    }
                                                });
                                                bind_event();
                                                close();
                                            }
                                        });
                                        return false;
                                    }
                                }
                            });
                        }
                        else
                        {
                            // campaign 
                            //open_popup('qrcode');
                            var camp_id = jQuery(this).attr("campid");

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
                                    }
                                    else
                                    {
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "getQRcodelist=yes&campaign_id=" + camp_id,
                                            async: false,
                                            success: function (msg) {
                                                jQuery(".QRcode_detail_div").html(msg);
                                                jQuery(".QRcode_detail_div").html(msg);
                                                var data_found = jQuery(".QRcode_detail_div #hdn_qrcode_data_found").val();
                                                if (data_found == "false")
                                                {
                                                    var head_msg = "<div class='head_msg'>Message</div>"
                                                }
                                                else
                                                {
                                                    var head_msg = "<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['link_qr_code']; ?></div>"
                                                }
                                                var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                                var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                                jQuery.fancybox({
                                                    content: jQuery("#dialog-message").html(),
                                                    type: 'html',
                                                    openSpeed: 300,
                                                    closeSpeed: 300,
                                                    changeFade: 'fast',
                                                    helpers: {
                                                        overlay: {
                                                            opacity: 0.3
                                                        } // overlay
                                                    }
                                                });

                                                bind_event();
                                                close();
                                            }
                                        });
                                        return false;
                                    }
                                }
                            });
                        }
                    });

                    function close()
                    {
                    }
                    /*********** unlink qr code ****/
                    jQuery(".rdownloadqrcode").live("click", function () {

                        //   open_popup('qrcode');
						if (jQuery(this).hasClass("loyalty"))
                        {
                            // location

                            jQuery("#hdn_dettach_qrcode_card_id").val(jQuery(this).attr("cardid"));

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
                                    }
                                    else
                                    {
                                        // open_popup('qrcode');
                                        jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring_loyalty; ?>');
                                        var head_msg = "<div class='head_msg' >Unlink QR Code</div>"
                                        var content_msg = '<div class="content_msg"><?php echo $confirmationstring_loyalty; ?>';
                                        var footer_msg = "<div><input type='button'  value='OK' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);

                                        jQuery.fancybox({
                                            content: jQuery("#dialog-message").html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });

                                        bind_confirmation_event();

                                    }
                                }

                            });
                        }
                        else if (jQuery(this).hasClass("loc"))
                        {
                            // location

                            jQuery("#hdn_dettach_qrcode_location_id").val(jQuery(this).attr("locid"));

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
                                    }
                                    else
                                    {
                                        // open_popup('qrcode');
                                        jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring_loc; ?>');
                                        var head_msg = "<div class='head_msg' >Unlink QR Code</div>"
                                        var content_msg = '<div class="content_msg"><?php echo $confirmationstring_loc; ?>';
                                        var footer_msg = "<div><input type='button'  value='OK' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);

                                        jQuery.fancybox({
                                            content: jQuery("#dialog-message").html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });

                                        bind_confirmation_event();

                                    }
                                }

                            });
                        }
                        else
                        {
                            // campaign

                            jQuery("#hdn_dettach_qrcode_campaign_id").val(jQuery(this).attr("campid"));

                            jQuery(".QRcode_detail_div").html('<?php echo $confirmationstring; ?>');
                            var head_msg = "<div  class='head_msg' ><?php echo $merchant_msg['marketingmaterial']['unlink_qr_code']; ?></div>"
                            var content_msg = '<div class="content_msg"><?php echo $confirmationstring; ?>';
                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                            jQuery.fancybox({
                                content: jQuery("#dialog-message").html(),
                                type: 'html',
                                openSpeed: 300,
                                closeSpeed: 300,
                                changeFade: 'fast',
                                helpers: {
                                    overlay: {
                                        opacity: 0.3
                                    } // overlay
                                }
                            });

                        }
                    });

                    jQuery("#popupcancel").live("click", function () {
                        jQuery.fancybox.close();
                        return false;
                    });

                    function bind_confirmation_event()
                    {
                        jQuery("#btn_no_loc").live("click", function () {
                            // close_popup("qrcode");
                            jQuery.fancybox.close();
                        });
                       
                        jQuery("#btn_yes_loc").live("click", function () {
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

                                    }
                                    else
                                    {
                                        var val = jQuery("#hdn_dettach_qrcode_location_id").val();

                                        jQuery(".rdownloadqrcode").each(function () {
                                            if (jQuery(this).attr("locid") == jQuery("#hdn_dettach_qrcode_location_id").val())
                                            {
                                                ele1 = jQuery(this);
                                            }
                                        });
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "removeqrcodeattachment_location=yes&location_id=" + val,
                                            async: false,
                                            success: function (msg) {

                                                ele1.removeClass("rdownloadqrcode");
                                                ele1.removeClass("loc");
                                                ele1.addClass("downloadqrcode");
                                                ele1.addClass("loc");
                                                ele1.text("Link");
                                                jQuery.fancybox.close();

                                                jQuery(".downloadqrcode").click(function () {

                                                    // open_popup('qrcode');
                                                    jQuery.ajax({
                                                        type: "POST",
                                                        url: "<?= WEB_PATH ?>/merchant/process.php",
                                                        data: "getQRcodelist=yes&location_id=" + jQuery(this).attr("locid"),
                                                        async: false,
                                                        success: function (msg) {
                                                            jQuery(".QRcode_detail_div").html(msg);
                                                            var data_found = jQuery(".QRcode_detail_div #hdn_qrcode_data_found").val();
                                                            if (data_found == "false")
                                                            {
                                                                var head_msg = "<div class='head_msg'>Message</div>"

                                                            }
                                                            else
                                                            {
                                                                var head_msg = "<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['link_qr_code']; ?></div>"
                                                            }
                                                            var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                            jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                                            jQuery.fancybox({
                                                                content: jQuery("#dialog-message").html(),
                                                                type: 'html',
                                                                openSpeed: 300,
                                                                closeSpeed: 300,
                                                                changeFade: 'fast',
                                                                helpers: {
                                                                    overlay: {
                                                                        opacity: 0.3
                                                                    } // overlay
                                                                }
                                                            });
                                                            bind_event();
                                                            close();
                                                        }
                                                    });

                                                    return false;

                                                });
                                                //   close_popup('qrcode');
                                                jQuery.fancybox.close();
                                            }
                                        });
                                    }
                                }
                            });

                        });
                        
                         jQuery("#btn_no_loyalty").live("click", function () {
                            // close_popup("qrcode");
                            jQuery.fancybox.close();
                        });
                        
                        jQuery("#btn_yes_loyalty").live("click", function () {
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

                                    }
                                    else
                                    {
                                        var val = jQuery("#hdn_dettach_qrcode_card_id").val();

                                        jQuery(".rdownloadqrcode").each(function () {
                                            if (jQuery(this).attr("cardid") == jQuery("#hdn_dettach_qrcode_card_id").val())
                                            {
                                                ele1 = jQuery(this);
                                            }
                                        });
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "removeqrcodeattachment_loyalty=yes&cardid=" + val,
                                            async: false,
                                            success: function (msg) {

                                                ele1.removeClass("rdownloadqrcode");
                                                ele1.removeClass("loyalty");
                                                ele1.addClass("downloadqrcode");
                                                ele1.addClass("loyalty");
                                                ele1.text("Link");
                                                jQuery.fancybox.close();

                                                jQuery(".downloadqrcode").click(function () {

                                                    // open_popup('qrcode');
                                                    jQuery.ajax({
                                                        type: "POST",
                                                        url: "<?= WEB_PATH ?>/merchant/process.php",
                                                        data: "getQRcodelist=yes&cardid=" + jQuery(this).attr("cardid"),
                                                        async: false,
                                                        success: function (msg) {
                                                            jQuery(".QRcode_detail_div").html(msg);
                                                            var data_found = jQuery(".QRcode_detail_div #hdn_qrcode_data_found").val();
                                                            if (data_found == "false")
                                                            {
                                                                var head_msg = "<div class='head_msg'>Message</div>"

                                                            }
                                                            else
                                                            {
                                                                var head_msg = "<div class='head_msg'><?php echo $merchant_msg['marketingmaterial']['link_qr_code']; ?></div>"
                                                            }
                                                            var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                            jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                                            jQuery.fancybox({
                                                                content: jQuery("#dialog-message").html(),
                                                                type: 'html',
                                                                openSpeed: 300,
                                                                closeSpeed: 300,
                                                                changeFade: 'fast',
                                                                helpers: {
                                                                    overlay: {
                                                                        opacity: 0.3
                                                                    } // overlay
                                                                }
                                                            });
                                                            bind_event();
                                                            close();
                                                        }
                                                    });

                                                    return false;

                                                });
                                                //   close_popup('qrcode');
                                                jQuery.fancybox.close();
                                            }
                                        });
                                    }
                                }
                            });

                        });
                    }

                    /********* download qr code
                     - first check login or not ?
                     - check campaign is selected or not ?
                     - If campaign is linked with qr code is or not ?
                     - If all conditions are true then merchant can download qr code of campaign
                     - Then give options foe select size
                     **********/
                    jQuery('#btn_downloadqrcode').click(function () {



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
                                }
                                else
                                {

                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='rd_campaigntitle']:checked");
                                    //alert(selected.length);
                                    if (selected.length > 0)
                                    {

                                        selectedValue = selected.val();

                                        jQuery("#hdn_qrcodedn_campaign_id").val(selectedValue);

                                        if (jQuery.trim(jQuery("a[campid='" + selectedValue + "']").attr("class")) == "rdownloadqrcode")
                                        {


                                            jQuery(".locationlist_div").html('<?php echo $sizeoptions; ?>');
                                            //open_popup('confirmation');
                                            var head_msg = "<div class='head_msg' ><?php echo $merchant_msg['marketingmaterial']['download_qr_code']; ?></div>"
                                            var content_msg = '<div class="content_msg"><?php echo $sizeoptions; ?></div>';
                                            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                            jQuery("#btncancel").live("click", function () {
                                                jQuery.fancybox.close();
                                                //close_popup('confirmation');
                                                return false;
                                            });
                                            download_function();

                                        }
                                        else {
                                            //alert("Please link QR Code to Campaign");
                                            var head_msg = "<div class='head_msg' >Message</div>"
                                            var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_qrcode_to_campaign']; ?></div>";
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                        }
                                        //  if()

                                    }
                                    else
                                    {
                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_select_campaign']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }

                                    return false;



                                }
                            }
                        });





                    });
					
					jQuery('#btn_downloadqrcode_loyalty').click(function () {



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
                                }
                                else
                                {

                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='rd_loyaltycardtitle']:checked");
                                    //alert(selected.length);
                                    if (selected.length > 0)
                                    {

                                        selectedValue = selected.val();

                                        jQuery("#hdn_qrcodedn_card_id").val(selectedValue);

                                        if (jQuery.trim(jQuery("a[cardid='" + selectedValue + "']").attr("class")) == "rdownloadqrcode loyalty")
                                        {


                                            jQuery(".locationlist_div").html('<?php echo $sizeoptions_loyalty; ?>');
                                            //open_popup('confirmation');
                                            var head_msg = "<div class='head_msg' ><?php echo $merchant_msg['marketingmaterial']['download_qr_code']; ?></div>"
                                            var content_msg = '<div class="content_msg"><?php echo $sizeoptions_loyalty; ?></div>';
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                            jQuery("#btncancel").live("click", function () {
                                                jQuery.fancybox.close();
                                                //close_popup('confirmation');
                                                return false;
                                            });
                                            download_function();

                                        }
                                        else {
                                            //alert("Please link QR Code to Campaign");
                                            var head_msg = "<div class='head_msg' >Message</div>"
                                            var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_qrcode_to_loyaltycard']; ?></div>";
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                        }
                                        //  if()

                                    }
                                    else
                                    {
                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_select_loyaltycard']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }

                                    return false;



                                }
                            }
                        });





                    });
                    
                    function close_popup(popup_name)
                    {
                        $ac = jQuery.noConflict();
                        $ac("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
                            $ac("#" + popup_name + "BackDiv").fadeOut(200, function () {
                                $ac("#" + popup_name + "PopUpContainer").fadeOut(200, function () {
                                    $ac("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
                                    $ac("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
                                    $ac("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
                                });
                            });
                        });

                    }
                    function open_popup(popup_name)
                    {
                        $ao = jQuery.noConflict();
                        if ($ao("#hdn_image_id").val() != "")
                        {
                            $ao('input[name=use_image][value=' + $ao("#hdn_image_id").val() + ']').attr("checked", "checked");
                        }
                        $ao("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
                            $ao("#" + popup_name + "BackDiv").fadeIn(200, function () {
                                $ao("#" + popup_name + "PopUpContainer").fadeIn(200, function () {

                                });
                            });
                        });


                    }
            </script>
            <!-- end for downloading qrcode -->

            <!-- start for marketing coupon -->
            <script>
                    /*** un selct the selected camapign ***********/
                    jQuery("#btn_unselect").click(function () {

                        jQuery("input[type='radio'][name='rd_campaigntitle']:checked").attr("checked", false);
                    });

                    /********* Download marketing coupon 
                     - First Check Login or not 
                     - Is campaiogn is selected or not 
                     - Is campaign is linked or not *******/
                    jQuery('#btn_downloadmarketingcoupon').click(function () {

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
                                }
                                else
                                {
                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='rd_campaigntitle']:checked");

                                    if (selected.length > 0)
                                    {
                                        //if(selected.attr("is_walkin") == 1){
                                        if (1) {

                                            selectedValue = selected.val();
                                            jQuery("#hdn_qrcodedn_campaign_id").val(selectedValue);
                                            
                                            //var qrcodeid = "<?php if (isset($qrcodestring)) { echo $qrcodestring; } ?>";
            
											var qrcodeid = jQuery("#qrcode_div_campaign a[campid="+jQuery("#hdn_qrcodedn_campaign_id").val()+"]").attr("qval");
        
                                            //var activationcode = "<?php if (isset($activation_code)) { echo $activation_code; } ?>";
											var activationcode = selected.attr("activationcode");			

                                            if (jQuery.trim(jQuery("a[campid='" + selectedValue + "']").attr("class")) == "rdownloadqrcode")
                                            {
												//console.log("<?php echo WEB_PATH ?>/merchant/demopdf/demo.php?id=" + selectedValue + "&activationcode=" + activationcode + "&qrcodeid=" + qrcodeid + "&is_walkin=" + selected.attr("is_walkin"));
                                                window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo.php?id=" + selectedValue + "&activationcode=" + activationcode + "&qrcodeid=" + qrcodeid + "&is_walkin=" + selected.attr("is_walkin");

                                            }
                                            else {
                                                //alert("Please link QR code to campaign");
                                                var head_msg = "<div class='head_msg'>Message</div>"
                                                var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_qrcode_to_campaign']; ?></div>";
                                                var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                                jQuery.fancybox({
                                                    content: jQuery('#dialog-message').html(),
                                                    type: 'html',
                                                    openSpeed: 300,
                                                    closeSpeed: 300,
                                                    changeFade: 'fast',
                                                    helpers: {
                                                        overlay: {
                                                            opacity: 0.3
                                                        } // overlay
                                                    }
                                                });
                                            }
                                        } else {
                                            //alert("Download marketing coupon functionality is only for walkin campaigns");
                                            var head_msg = "<div class='head_msg' >Message</div>"
                                            var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['download_for_only_walking']; ?></div>";
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                        }
                                    } else
                                    {
                                        //alert("Please Select Campaign Title");
                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_select_campaign']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }

                                }
                            }
                        });
                    });
                    
                    jQuery('#btn_downloadmarketingcoupon_loyalty').click(function () {

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
                                }
                                else
                                {
                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='rd_loyaltycardtitle']:checked");

                                    if (selected.length > 0)
                                    {
                                        //if(selected.attr("is_walkin") == 1){
                                        if (1) 
                                        {

												selectedValue = selected.val();
												jQuery("#hdn_qrcodedn_card_id").val(selectedValue);
												
												//var qrcodeid = "<?php if (isset($qrcodestring)) {echo $qrcodestring;}?>";
												var qrcodeid = jQuery("#qrcode_div_loyalty a[cardid="+jQuery("#hdn_qrcodedn_card_id").val()+"]").attr("qval");
												
												//var activationcode = "<?php if (isset($activation_code)) {echo $activation_code;}?>";
												var activationcode = selected.attr("activationcode");

												if (jQuery.trim(jQuery("a[cardid='" + selectedValue + "']").attr("class")) == "rdownloadqrcode loyalty")
												{
													//alert("download");
													//console.log("<?php echo WEB_PATH ?>/merchant/demopdf/demo.php?id=" + selectedValue + "&activationcode=" + activationcode + "&qrcodeid=" + qrcodeid + "&is_walkin=" + selected.attr("is_walkin"));
													window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo.php?id=" + selectedValue + "&activationcode=" + activationcode + "&qrcodeid=" + qrcodeid + "&is_loyaltycard=1";

												}
												else 
												{
													//alert("Please link QR code to campaign");
													var head_msg = "<div class='head_msg'>Message</div>"
													var content_msg = "<div class='content_msg' style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_qrcode_to_loyaltycard']; ?></div>";
													var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
													jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
													jQuery.fancybox({
														content: jQuery('#dialog-message').html(),
														type: 'html',
														openSpeed: 300,
														closeSpeed: 300,
														changeFade: 'fast',
														helpers: {
															overlay: {
																opacity: 0.3
															} // overlay
														}
													});
												}
                                        } 
                                        else 
                                        {
                                            //alert("Download marketing coupon functionality is only for walkin campaigns");
                                            var head_msg = "<div class='head_msg' >Message</div>"
                                            var content_msg = "<div class='content_msg'><?php echo $merchant_msg['marketingmaterial']['download_for_only_walking']; ?></div>";
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                        }
                                    } 
                                    else
                                    {
                                        //alert("Please Select Campaign Title");
                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_select_loyaltycard']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }

                                }
                            }
                        });
                    });
            </script>

            <!-- end for marketing coupon -->

            <!-- start for marketing material -->
            <script>
                    /******** download marketing material
                     - If merchant session is login or not 
                     - If campaign is selected  then check whether campaign is linked with qrcode or not 
                     - If campaign is not selected then download marketing for location 
                     ************/
                    jQuery('#btn_downloadmarketingmaterial').click(function () {
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
                                }
                                else
                                {
                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='rd_campaigntitle']:checked");
                                    
                                    
									//return false;
                                    if (selected.length > 0)
                                    {
										
										//var qrcodeid = "<?php if (isset($qrcodestring)) echo $qrcodestring; ?>";
										var qrcodeid = jQuery("#qrcode_div_campaign a[campid="+jQuery('#hdn_qrcodedn_campaign_id').val()+"]").attr("qval");
										
										//var activationcode = "<?php if (isset($activation_code)) { echo $activation_code; } ?>";
										var activationcode = selected.attr("activationcode");
										console.log("activationcode = "+activationcode);
									
                                        selectedValue = selected.val();
                                        jQuery("#hdn_qrcodedn_campaign_id").val(selectedValue);
                                        //   alert(selectedValue);
                                        //   exit;
                                        if (jQuery.trim(jQuery("a[campid='" + selectedValue + "']").attr("class")) == "rdownloadqrcode")
                                        {
                                            window.location.href = "<?php echo WEB_PATH ?>/merchant/merchant-marketing-template.php?id=" + selectedValue + "&activationcode=" + activationcode + "&qrcodeid=" + qrcodeid;

                                        }
                                        else {
                                            //alert("Please link QR Code to Campaign");
                                            var head_msg = "<div class='head_msg'>Message</div>";
                                            var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_qrcode_to_campaign']; ?></div>";
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                        }
                                    }
                                    else
                                    {

                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_select_campaign']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }

                                }
                            }
                        });
                    });


                    jQuery('#btn_downloadmarketingmaterial_location').click(function () {
			
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
                                }
                                else
                                {
                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='rd_locationtitle']:checked");
                                    
                                   
                                    
                                    if (selected.length > 0)
                                    {

										//var qrcodeid = "<?php if (isset($qrcodestring)) echo $qrcodestring; ?>";
										var qrcodeid = jQuery("#qrcode_div_location a[locid="+jQuery("#hdn_qrcodedn_location_id").val()+"]").attr("qval");
										
										var activationcode = "<?php if (isset($activation_code)) {  echo $activation_code;} ?>";

										var selectedValue = jQuery('input:radio[name=rd_locationtitle]:checked').val();
                                    
                                        if (jQuery.trim(jQuery("a[locid='" + selectedValue + "']").attr("class")) == "rdownloadqrcode loc")
                                        {

                                        }
                                        else
                                        {
                                            //alert("Please Link QR Code Status to Selected Location");
                                            var head_msg = "<div class='head_msg'>Message</div>"
                                            var content_msg = "<div class='content_msg' style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_qr_code_selected_location']; ?></div>";
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                            return false;
                                        }
                                        /*
                                         alert(jQuery.trim(jQuery("a[locid='"+selectedValue+"']").attr("class")));
                                         alert(selected.val());
                                         alert("<?php echo WEB_PATH ?>/merchant/merchant-marketing-template.php?id="+selected.val());
                                         return false;
                                         */
                                        window.location.href = "<?php echo WEB_PATH ?>/merchant/merchant-marketing-template.php?id=" + selected.val();
                                        return false;
                                    }
                                    else
                                    {
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                            data: "attach_message_location=yes",
                                            async: false,
                                            success: function (msg) {

                                                jQuery(".locationlist_div").html(msg);
                                                //open_popup('confirmation');
                                                var head_msg = "<div class='head_msg' >Message</div>"
                                                var content_msg = "<div class='content_msg' style='text-align:center'>Please select location.</div>";
                                                var footer_msg = "<div><input type='button'  value='OK' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                                jQuery.fancybox({
                                                    content: jQuery('#dialog-message').html(),
                                                    type: 'html',
                                                    openSpeed: 300,
                                                    closeSpeed: 300,
                                                    changeFade: 'fast',
                                                    helpers: {
                                                        overlay: {
                                                            opacity: 0.3
                                                        } // overlay
                                                    }
                                                });
                                                download_function();
                                            }
                                        });

                                    }

                                }
                            }
                        });
                    });

					jQuery('#btn_downloadmarketingmaterial_loyalty').click(function () {
			
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
                                }
                                else
                                {
                                    var selectedVal = "";
                                    var selected = jQuery("input[type='radio'][name='rd_loyaltycardtitle']:checked");
                                    

									
                                    if (selected.length > 0)
                                    {
										//var qrcodeid = "<?php if (isset($qrcodestring)) echo $qrcodestring; ?>";
										var qrcodeid = jQuery("#qrcode_div_loyalty a[cardid="+jQuery("#hdn_qrcodedn_card_id").val()+"]").attr("qval");
										
										var activationcode = "<?php if (isset($activation_code)) {echo $activation_code;}?>";
										var activationcode = selected.attr("activationcode");
									
                                        selectedValue = selected.val();
                                        jQuery("#hdn_qrcodedn_card_id").val(selectedValue);

                                        if (jQuery.trim(jQuery("a[cardid='" + selectedValue + "']").attr("class")) == "rdownloadqrcode loyalty")
                                        {
											//console.log("<?php echo WEB_PATH ?>/merchant/merchant-marketing-template-loyalty.php?id=" + selectedValue + "&activationcode=" + activationcode + "&qrcodeid=" + qrcodeid);
                                            window.location.href = "<?php echo WEB_PATH ?>/merchant/merchant-marketing-template-loyalty.php?id=" + selectedValue + "&activationcode=" + activationcode + "&qrcodeid=" + qrcodeid;
                                        }
                                        else 
                                        {
                                            //alert("Please link QR Code to Campaign");
                                            var head_msg = "<div class='head_msg'>Message</div>";
                                            var content_msg = "<div class='content_msg'style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_link_qrcode_to_loyaltycard']; ?></div>";
                                            var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });
                                        }
                                    }
                                    else
                                    {

                                        var head_msg = "<div class='head_msg'>Message</div>"
                                        var content_msg = "<div class='content_msg' style='text-align:center'><?php echo $merchant_msg['marketingmaterial']['please_select_loyaltycard']; ?></div>";
                                        var footer_msg = "<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#dialog-message').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                    }

                                }
                            }
                        });
                    });
					
                    /************** select walkin  deals only ***************/
                    jQuery("#waking_deal").click(function () {

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
                            if (jQuery(this).is(':checked')) {
                                //    alert("In If");
                                var status = "true";
                                var ids = "<?php echo $ids; ?>";
                                var action = "active";
                                var mer_id = "<?php echo $_SESSION['merchant_id']; ?>";
                                //    alert('walkingstatus='+status+'&mer_id='+mer_id+'&action='+action+'&ids='+ids);
                                jQuery.ajax({
                                    type: "POST",
                                    url: 'process.php',
                                    data: 'walkingstatus=' + status + '&mer_id=' + mer_id + '&action=' + action + '&ids=' + ids,
                                    async: true,
                                    success: function (msg)
                                    {


                                        jQuery("#qrcode_div_campaign").html("");
                                        jQuery("#qrcode_div_campaign").html(msg);
                                        jQuery('#qrcode_campaign_table').dataTable().fnDestroy();
                                        jQuery('#qrcode_campaign_table').dataTable({
                                            "sPaginationType": "full_numbers",
                                            'bFilter': false,
                                            "bSort": false,
                                            "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                                            "iDisplayLength": 10,
                                            //"aoColumnDefs":[{"bSortable":false , "aTargets":[0,2 ]}]
                                        });
                                    }
                                });



                            } else {

                                var status = "false";
                                var ids = "<?php echo $ids; ?>";
                                var action = "active";
                                var mer_id = "<?php echo $_SESSION['merchant_id']; ?>";
                                //  alert('walkingstatus='+status+'&mer_id='+mer_id+'&action='+action+'&ids='+ids);
                                jQuery.ajax({
                                    type: "POST",
                                    url: 'process.php',
                                    data: 'walkingstatus=' + status + '&mer_id=' + mer_id + '&action=' + action + '&ids=' + ids,
                                    ac: true,
                                    success: function (msg)
                                    {
                                        jQuery("#qrcode_div_campaign").html("");
                                        jQuery("#qrcode_div_campaign").html(msg);
                                        //  jQuery('#qrcode_campaign_table').dataTable().fnClearTable();
                                        jQuery('#qrcode_campaign_table').dataTable().fnDestroy();
                                        jQuery('#qrcode_campaign_table').dataTable({
                                            "sPaginationType": "full_numbers",
                                            'bFilter': false,
                                            "bSort": false,
                                            "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                                            "iDisplayLength": 10,
                                            //"aoColumnDefs":[{"bSortable":false , "aTargets":[0,2 ]}]
                                        });
                                    }
                                });

                            }

                        }


                    });
                    

                    jQuery("#btndownloadqrcode_loyalty").live("click", function () {


                        var sp_size = jQuery(".fancybox-inner #opt_qrcodesize").val();
                        var sp_type = jQuery(".fancybox-inner #opt_qrcodetype").val();


                        //var qrcodeid = "<?php if (isset($qrcodestring)) echo $qrcodestring; ?>";
						var qrcodeid = jQuery("#qrcode_div_loyalty a[cardid="+jQuery("#hdn_qrcodedn_card_id").val()+"]").attr("qval");
                        console.log("qrcodeid = "+qrcodeid);

                        if (sp_type == 1)
                        {
                            window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode.php?id=" + jQuery("#hdn_qrcodedn_card_id").val() + "&size=" + sp_size + "&is_loyaltycard=1&qrcodeid=" + qrcodeid;
                        }
                        else if (sp_type == 2) 
                        {
							/*
                            jQuery.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "get_campaign_qrcode_url=yes&id=" + jQuery("#hdn_qrcodedn_card_id").val() + "&is_loyaltycard=1",
                                async: false,
                                success: function (msg) {
                                    var obj = jQuery.parseJSON(msg);
                                    //is_location=1&
                                    window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode_jpg.php?id=" + jQuery("#hdn_qrcodedn_card_id").val() + "&size=" + sp_size + "&is_loyaltycard=1&qrcodeid=" + qrcodeid;
                                    // window.location.href= '<?php echo WEB_PATH . "/QR-Generator-PHP-master/php/qr.php?t=j&d="; ?>'+obj.qrcode_url+"&size="+sp_size+'&download=qrcode&qrcodeid='+qrcodeid;
                                }
                            });
                            */
                            window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode_jpg.php?id=" + jQuery("#hdn_qrcodedn_card_id").val() + "&size=" + sp_size + "&is_loyaltycard=1&qrcodeid=" + qrcodeid;
                        }
                        else 
                        {
                            //document.execCommand('SaveAs',true,'camp_image1.jpg');
							/*
                            jQuery.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "get_campaign_qrcode_url=yes&id=" + jQuery("#hdn_qrcodedn_card_id").val() + "&is_location=1",
                                async: false,
                                success: function (msg) {

                                    var obj = jQuery.parseJSON(msg);
                                    //   alert( '<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>'+obj.qrcode_url+"&qrcodeid="+qrcodeid+"&size="+sp_size);
                                    //alert('<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>'+obj.qrcode_url+"&size="+sp_size+"&qrcodeid="+qrcodeid);
                                    //window.location.href = '<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>' + obj.qrcode_url + "&qrcodeid=" + obj.qrcode_string + "&is_loyaltycard=1&size=" + sp_size;
                                    window.location.href = '<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>' + obj.qrcode_url + "&qrcodeid=" + qrcodeid + "&is_loyaltycard=1&size=" + sp_size;
                                }
                            });
                            */
                            window.location.href = '<?php echo WEB_PATH . "/merchant/download_qrcode.php?"; ?>' + "qrcodeid=" + qrcodeid + "&is_loyaltycard=1&size=" + sp_size;
                        }

                        close();
                        close_popup('confirmation');
                        jQuery.fancybox.close();
                    });
                    
                    /********* download qr code according type *************/
                    jQuery("#btndownloadqrcode").live("click", function () {

                        var sp_size = jQuery(".fancybox-inner #opt_qrcodesize").val();
                        var sp_type = jQuery(".fancybox-inner #opt_qrcodetype").val();

                        var qrcodeid = "<?php if (isset($qrcodestring)) echo $qrcodestring; ?>";
                        var qrcodeid = jQuery("#qrcode_div_campaign a[campid="+jQuery("#hdn_qrcodedn_campaign_id").val()+"]").attr("qval");
                        console.log("qrcodeid = "+qrcodeid);
                        
                        //    alert('<?php echo WEB_PATH . "/QR-Generator-PHP-master/php/qr.php?d="; ?>'+jQuery("#hdn_qrcodedn_campaign_id").val()+"&size="+sp_size+'&download=qrcode');
                        //window.location.href="<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode.php?id="+jQuery("#hdn_qrcodedn_campaign_id").val()+"&size="+sp_size;
                        //  window.location.href= '<?php echo WEB_PATH . "/QR-Generator-PHP-master/php/qr.php?d="; ?>'+jQuery("#hdn_qrcodedn_campaign_id").val()+"&size="+sp_size+'&download=qrcode';
                         //alert(sp_type);
                        if (sp_type == 1)
                        {

                            window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode.php?id=" + jQuery("#hdn_qrcodedn_campaign_id").val() + "&size=" + sp_size + "&qrcodeid=" + qrcodeid;
                        }
                        else if (sp_type == 2) {
							/*
                            jQuery.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "get_campaign_qrcode_url=yes&id=" + jQuery("#hdn_qrcodedn_campaign_id").val(),
                                async: false,
                                success: function (msg) {
                                    var obj = jQuery.parseJSON(msg);
                                    window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode_jpg.php?id=" + jQuery("#hdn_qrcodedn_campaign_id").val() + "&size=" + sp_size + "&qrcodeid=" + qrcodeid;
                                    ///window.location.href= '<?php echo WEB_PATH . "/QR-Generator-PHP-master/php/qr.php?t=j&d="; ?>'+obj.qrcode_url+"&size="+sp_size+'&download=qrcode'+"&qrcodeid="+qrcodeid;
                                }
                            });
                            */
                            
                            window.location.href = "<?php echo WEB_PATH ?>/merchant/demopdf/demo_qrcode_jpg.php?id=" + jQuery("#hdn_qrcodedn_campaign_id").val() + "&size=" + sp_size + "&qrcodeid=" + qrcodeid;
                        }
                        else {
							/*							
                            //document.execCommand('SaveAs',true,'camp_image1.jpg');
                            jQuery.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "get_campaign_qrcode_url=yes&id=" + jQuery("#hdn_qrcodedn_campaign_id").val(),
                                async: false,
                                success: function (msg) {
                                    var obj = jQuery.parseJSON(msg);

                                    //http://www.scanflip.com/QR-Generator-PHP-master/php/qr.php?d=http://www.scanflip.com/qr.php?qrcode=GD5OOWG4&size=180
                                    //    alert('<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>'+obj.qrcode_url+"&size="+sp_size+"&qrcodeid="+qrcodeid);
                                    window.location.href = '<?php echo WEB_PATH . "/merchant/download_qrcode.php?imgage_path="; ?>' + obj.qrcode_url + "&size=" + sp_size + "&qrcodeid=" + qrcodeid;
                                }
                            });
                            */
                            
                            window.location.href = '<?php echo WEB_PATH . "/merchant/download_qrcode.php?" ?>' + "size=" + sp_size + "&qrcodeid=" + qrcodeid;
                        }

                        close();
                        // close_popup('confirmation');
                        jQuery.fancybox.close();
                    });

                    jQuery("#btn_cancel").click(function () {
                        close_popup("confirmation");
                    });
                    $a = jQuery.noConflict();
                    /********* initializing data table *************/
                    $a(document).ready(function () {
                        oTable = $a('#qrcode_campaign_table').dataTable({
                            "bFilter": false,
                            "bSort": false,
                            "bLengthChange": false,
                            "info": false,
                            //  "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                            "iDisplayLength": 10,
                            // "aoColumnDefs":[{"bSortable":false , "aTargets":[0,2 ]}]
                        });

						oTable = $a('#qrcode_loyalty_table').dataTable({
                            "bFilter": false,
                            "bSort": false,
                            "bLengthChange": false,
                            "info": false,
                            //  "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                            "iDisplayLength": 10,
                            // "aoColumnDefs":[{"bSortable":false , "aTargets":[0,2 ]}]
                        });
                        
                        jQuery(".table_loader").css("display", "none");
                        jQuery(".datatable_container").css("display", "block");
                    });
                    //alert(jQuery("a id['^showcamp']").length);
                    jQuery(document).on("click","#qrcode_div_campaign input[type='radio'][id^='rd_campaigntitle_']", function () { // for campaigns
						var val_arr = jQuery(this).attr('id').split("_");
                        if (jQuery(this).attr("visit") == "first")
                        {
							jQuery("#hdn_qrcodedn_campaign_id").val(val_arr[2]);
                            jQuery(this).attr("checked", true);
                            jQuery(this).attr("visit", "second");
                        }
                        else {
							jQuery("#hdn_qrcodedn_campaign_id").val("");
                            jQuery("input[type='radio'][name='rd_campaigntitle']:checked").attr("checked", false);
                            jQuery(this).attr("visit", "first");
                        }
                    });
                    jQuery(document).on("click","#qrcode_div_location input[type='radio'][id^='rd_campaigntitle_']", function () { // for locations
						var val_arr = jQuery(this).attr('id').split("_");
                        if (jQuery(this).attr("visit") == "first")
                        {
							jQuery("#hdn_qrcodedn_location_id").val(val_arr[2]);
                            jQuery(this).attr("checked", true);
                            jQuery(this).attr("visit", "second");
                        }
                        else {
							jQuery("#hdn_qrcodedn_location_id").val("");
                            jQuery("input[type='radio'][name='rd_locationtitle']:checked").attr("checked", false);
                            jQuery(this).attr("visit", "first");
                        }
                    });
                    jQuery(document).on("click","input[type='radio'][id^='rd_loyaltycardtitle_']", function () { // for loyalty cards
						var val_arr = jQuery(this).attr('id').split("_");
                        if (jQuery(this).attr("visit") == "first")
                        {
							jQuery("#hdn_qrcodedn_card_id").val(val_arr[2]);
                            jQuery(this).attr("checked", true);
                            jQuery(this).attr("visit", "second");
                        }
                        else {
							jQuery("#hdn_qrcodedn_card_id").val("");
                            jQuery("input[type='radio'][name='rd_loyaltycardtitle']:checked").attr("checked", false);
                            jQuery(this).attr("visit", "first");
                        }
                    });
                    
                    jQuery(document).on("click","#qrcode_div_campaign a[id^='showCamp_']", function () { // for campaigns
			
                        var val_arr = jQuery(this).attr('id').split("_");
                        if (jQuery("#rd_campaigntitle_" + val_arr[1]).attr("checked"))
                        {
							jQuery("#hdn_qrcodedn_campaign_id").val("");
                            jQuery("input[type='radio'][name='rd_campaigntitle']:checked").attr("checked", false);
                            jQuery("input[type='radio'][name='rd_locationtitle']:checked").attr("checked", false);
                            jQuery("#rd_campaigntitle_" + val_arr[1]).attr("visit", "first");
                        }
                        else {
							jQuery("#hdn_qrcodedn_campaign_id").val(val_arr[1]);
                            jQuery("#rd_campaigntitle_" + val_arr[1]).attr("checked", true);
                            jQuery("#rd_campaigntitle_" + val_arr[1]).attr("visit", "second");
                        }
                    });
                    jQuery(document).on("click","#qrcode_div_location a[id^='showCamp_']", function () { // for locations
			
                        var val_arr = jQuery(this).attr('id').split("_");
                        if (jQuery("#rd_campaigntitle_" + val_arr[1]).attr("checked"))
                        {
							jQuery("#hdn_qrcodedn_location_id").val("");
                            jQuery("input[type='radio'][name='rd_campaigntitle']:checked").attr("checked", false);
                            jQuery("input[type='radio'][name='rd_locationtitle']:checked").attr("checked", false);
                            jQuery("#rd_campaigntitle_" + val_arr[1]).attr("visit", "first");
                        }
                        else {
							jQuery("#hdn_qrcodedn_location_id").val(val_arr[1]);
                            jQuery("#rd_campaigntitle_" + val_arr[1]).attr("checked", true);
                            jQuery("#rd_campaigntitle_" + val_arr[1]).attr("visit", "second");
                        }
                    });
					jQuery(document).on("click","a[id^='showCard_']", function () { // for loyalty cards
			
                        var val_arr = jQuery(this).attr('id').split("_");
                        if (jQuery("#rd_loyaltycardtitle_" + val_arr[1]).attr("checked"))
                        {
							jQuery("#hdn_qrcodedn_card_id").val("");
                            jQuery("input[type='radio'][name='rd_loyaltycardtitle']:checked").attr("checked", false);
                            jQuery("#rd_loyaltycardtitle_" + val_arr[1]).attr("visit", "first");
                        }
                        else {
							jQuery("#hdn_qrcodedn_card_id").val(val_arr[1]);
                            jQuery("#rd_loyaltycardtitle_" + val_arr[1]).attr("checked", true);
                            jQuery("#rd_loyaltycardtitle_" + val_arr[1]).attr("visit", "second");
                        }
                    });	
                    
                    jQuery(".fancybox-inner .qrcodelist label[id^='optqrcode_']").live("click", function () {
                        //alert("hi");
                        var val_arr = jQuery(this).attr('id').split("_");
                        //alert(jQuery(".fancybox-inner .qrcodelist #opt_qrcode_"+val_arr[1]).is(':checked'));
                        if (jQuery(".fancybox-inner .qrcodelist #opt_qrcode_" + val_arr[1]).is(':checked'))
                        {
                            //alert("true");	
                            //jQuery(".fancybox-inner .qrcodelist #opt_qrcode_"+val_arr[1]).prop( "checked", false );	
                            jQuery(".fancybox-inner .qrcodelist #opt_qrcode_" + val_arr[1]).attr("checked", false);
                        }
                        else
                        {
                            //alert("false");	
                            //jQuery(".fancybox-inner .qrcodelist #opt_qrcode_"+val_arr[1]).prop( "checked", true );	
                            jQuery(".fancybox-inner .qrcodelist #opt_qrcode_" + val_arr[1]).attr("checked", true);

                        }
                    });

                    function show_tooltip() {
                        jQuery('.notification_tooltip').tooltip({
                            track: true,
                            delay: 0,
                            showURL: false,
                            showBody: "<br>",
                            fade: 250
                        });
                    }
                    show_tooltip();
            </script>
            <!-- end of  marketing material -->
    </body>
</html>
