<?php
$web_path = WEB_PATH . "/";
$pageURL = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
?>
<div id="footer">
    <div class="wrapper">

        <?php
        if ($pageURL != $web_path . "merchant/merchant-setup.php") {
                ?>
                <div id="copyright">
                    <a href="<?= WEB_PATH ?>/merchant/privacy-assist.php">Privacy Assist </a>
                    |   <a href="<?= WEB_PATH ?>/merchant/terms.php">Terms Of Services</a> 
                    |   <a href="<?= WEB_PATH ?>/merchant/press-release.php">Press Release</a>
                </div>
                <?php
        }
        ?>

        <?php
	$file_info = pathinfo($_SERVER['REQUEST_URI']);
	$action = array('add-compaign','edit-compaign','copy-compaign','purchase-points');
	if(in_array($file_info['filename'],$action)){
        //if (basename($_SERVER['REQUEST_URI']) == "add-compaign.php" || basename($_SERVER['REQUEST_URI']) == "edit-compaign.php" || basename($_SERVER['REQUEST_URI']) == "copy-compaign.php" || basename($_SERVER['REQUEST_URI']) == "purchase-points.php?action=active") {
                require_once(LIBRARY . '/stripe-php/config.php');
                ?>
                <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
                <script type="text/javascript">
                        Stripe.setPublishableKey("<?php echo $stripe['publishable_key']; ?>");

                        function stripeResponseHandler(status, response) {
                            if (response.error) {
                                // re-enable the submit button
                                jQuery('.point_block_parent').show();
                                jQuery('.point_block_parent1').hide();
                                // show the errors on the form
                                jQuery(".fancybox-inner #errmessage_card_number").html(response.error.message);
                            } else {

                                var form$ = jQuery(".fancybox-inner #purchase_now");
                                // token contains id, last4, and card type
                                var token = response['id'];
				jQuery('.fancybox-inner #stripeToken').remove();
                                // insert the token into the form so it gets submitted to the server
                                form$.append("<input type='hidden' name='stripeToken' id='stripeToken' value='" + token + "' />");
                                // and submit
                                //genrate_card(response,jQuery('.fancybox-inner #card_csv').val());
				order_place();
                            }
                        }
			function genrate_card(response,csv){
				//console.log(response);
				jQuery.ajax({
		                    type: "POST",
		                    url: "<?= WEB_PATH ?>/merchant/process.php",
		                    data: "payment_cards=yes&csv="+jQuery('.fancybox-inner #card_csv').val()+"&"+jQuery.param( response ),
		                    success: function (msg)
		                    {

		                    }
		                });
			}
                </script>
                <div id="point_block" style="display:none;">
                    <div class="head_msg">
                        <?php echo $merchant_msg["edit-compaign"]["Field_purchase_scanflip_points"]; ?>
                    </div>
			<?php

			$def_card = $objDB->Conn->Execute("Select * from stripe_payment_cards where merchant_id=? order by is_default desc",array($_SESSION['merchant_id']));
			//$def_card = $def_card->FetchRow();
						$cards='';
						$add_card = 0;
						if($def_card->RecordCount()>0){
							$cards='display:none;';
							$add_card = 1;
							
							//$def_country = $def_card['billing_country'];
						}
			?>
                    <div class="point_block_parent">


                        <div class="err_msg_div_40">
                            <div class="purchasepointclass status_more">

                            </div>

                        </div>
                        <?php
                        $arr = file(WEB_PATH . '/merchant/process.php?get_point_package=yes');
                        if (trim($arr[0]) == "") {
                                unset($arr[0]);
                                $arr = array_values($arr);
                        }
                        $json = json_decode($arr[0]);
                        $total_records = $json->total_records;
                        $records_array = $json->records;
                        if ($total_records > 0) {
                                foreach ($records_array as $Row) {
                                        ?>
                                        <?php
                                }
                        }

			
                        ?>
			<style>
					
					</style>
                        <div class="purchase_padding">
				<div class="ad_cd_hd">
						<?php if($def_card->RecordCount()>0){ ?>
						 Default Card: <select id="def_payment_card" name="def_payment_card">
					
						<?php while($c = $def_card->FetchRow()){ ?>
							<option value="<?php echo $c['card_id'] ?>" data-cntry="<?php echo $c['billing_country'] ?>"><?php echo $c['type'].'  ****'.$c['card_number'] ?></option>
						<?php }  ?>
						</select>
						<?php }  ?>
				</div>
                            <label for="txt_price">Amount : $ </label>
                            <input type="text" name="txt_price" id="txt_price" />
                            <div id="errmessage_price" class="fielderrormessage" ></div>
				<?php if($add_card == 1){ ?>
					<div class="add_new_card">
						<span onclick="jQuery('.enter_n_card').toggle();jQuery('.ad_cd_hd').toggle();jQuery(this).text(jQuery(this).text() == 'Add New Card'?'Back':'Add New Card');">Add New Card</span>
					</div>
					<div class="ad_cd_hd">
						
						<input type="hidden" name="def_country" id="def_country" value="<?php //echo $def_country; ?>" >
				            <input type="button" name="Purchase" id="purchase_now1" value="Purchase Now" onclick="getdefaultpurchasenow()">
				            <input type="button" name="Cancel" id="purchase_cancel" value="Cancel" onclick="getcancelnow()">
				        </div>
				<?php } ?>
                            <span id="span_con_point" style="display:none" ></span>
                            <!--                            <label for="card_type">Select Credit Card : </label>
                                                                                    <select name="card_type" id="card_type" >
                                                                                        <option value="0" >Select Credit Card Type</option>
                                                                                        <option value="visa" >Visa</option>
                                                                                        <option value="mastercard">Master Card</option>
                                                                                    </select>
                                                        <div id="errmessage_card_type" class="fielderrormessage" ></div>-->
				
				<div class="enter_n_card" style="<?php echo $cards; ?>">
                            <label for="card_number">Card Number :  </label>
                            <input type="text" name="card_number" id="card_number" />
                            <div id="errmessage_card_number" class="fielderrormessage" ></div>
                            <label for="expirydate">Expiry Date : </label>
                            <select name="exp_month" id="exp_month" >
                                <option value="0" >MM</option>
                                <?php
                                for ($em = 01; $em <= 12; $em++) {
                                        ?>
                                        <option value="<?php echo str_pad($em, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($em == date('m')) ? "selected" : "" ?>>
                                            <?php echo str_pad($em, 2, '0', STR_PAD_LEFT); ?>
                                        </option>
                                        <?php
                                }
                                ?>
                            </select>
                            -
                            <select name="exp_year" id="exp_year" >
                                <option  value="0" >YYYY</option>
                                <?php
                                for ($ey = date("Y"); $ey <= date("Y") + 20; $ey++) {
                                        ?>
                                        <option value="<?php echo $ey; ?>" <?php echo ($ey == date('Y')+1) ? "selected" : "" ?>><?php echo $ey; ?></option>
                                        <?php
                                }
                                ?>
                            </select>
                            <div id="errmessage_expirydate" class="fielderrormessage" ></div>
                            <label for="card_csv">Enter CSV<span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_csv"]; ?>">&nbsp;&nbsp;&nbsp;</span> : </label>
                            <input type="text" name="card_csv" id="card_csv" maxlength="4"/>
                            <div id="errmessage_cardcsv" class="fielderrormessage" ></div>
                        
                        <div class="cc_purchasenow">
                            <input type="button" name="purchase_now" id="purchase_now" value="Continue" onclick="getpurchasenow()"/>
                            <input type="button" name="purchase_cancel" id="purchase_cancel" value="<?php echo $merchant_msg['index']['btn_cancel']; ?>" onclick="getcancelnow()"/>
</div>
                        </div>
			</div>
                    </div>
                    <div class="point_block_parent1" style="display:none;">
                        <div class="purchase_padding">
                            <!--<label>Name on credit card : </label>
                            <input type="text" name="credit_card_name" id="credit_card_name" />		<div id="errmessage_credit_card_name" class="fielderrormessage" ></div>-->	
				            <label>Card-holder Name : </label>
                            <input type="text" name="card_holder_name" id="card_holder_name" />		<div id="errmessage_card_holder_name" class="fielderrormessage" ></div>								
                            <label for="billing_address">Billing Address :  </label>
                            <input type="text" name="billing_address" id="billing_address" value="<?php echo $_SESSION['merchant_info']['billing_address']; ?>" />		
                            <div id="errmessage_billing_address" class="fielderrormessage" ></div>									
                            <label for="billing_country">Country :  </label>
                            <select name="billing_country" id="billing_country" >
                                <option  value="USA" country_code="US" currency_code="USD" <?php
                                if ($_SESSION['merchant_info']['billing_country'] == "USA") {
                                        echo "selected";
                                }
                                ?> >USA</option>
                                <option value="Canada" country_code="CA" currency_code="CAD" <?php
                                if ($_SESSION['merchant_info']['billing_country'] == "Canada") {
                                        echo "selected";
                                }
                                ?>>Canada</option>
                            </select>
                            <div id="errmessage_billing_country" class="fielderrormessage" ></div>									
                            <label for="billing_state">State</label>
                            <select name="billing_state" id="billing_state" >
                                <?php if ($_SESSION['merchant_info']['billing_country'] == "Canada") { ?>
                                        <option value='AB' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "AB") {
                                                echo "selected";
                                        }
                                        ?>>AB</option>
                                        <option value='BC' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "BC") {
                                                echo "selected";
                                        }
                                        ?>>BC</option>
                                        <option value='LB' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "LB") {
                                                echo "selected";
                                        }
                                        ?>>LB</option>
                                        <option value='MB' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MB") {
                                                echo "selected";
                                        }
                                        ?>>MB</option>
                                        <option value='NB' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NB") {
                                                echo "selected";
                                        }
                                        ?>>NB</option>
                                        <option value='NF' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NF") {
                                                echo "selected";
                                        }
                                        ?>>NF</option>
                                        <option value='NS' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NS") {
                                                echo "selected";
                                        }
                                        ?>>NS</option>
                                        <option value='NT' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NT") {
                                                echo "selected";
                                        }
                                        ?>>NT</option>
                                        <option value='NU' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NU") {
                                                echo "selected";
                                        }
                                        ?>>NU</option>
                                        <option value='ON' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "ON") {
                                                echo "selected";
                                        }
                                        ?>>ON</option>
                                        <option value='PE' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "PE") {
                                                echo "selected";
                                        }
                                        ?>>PE</option>
                                        <option value='PQ' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "PQ") {
                                                echo "selected";
                                        }
                                        ?>>PQ</option>
                                        <option value='QB' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "QB") {
                                                echo "selected";
                                        }
                                        ?>>QB</option>
                                        <option value='QC' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "QC") {
                                                echo "selected";
                                        }
                                        ?>>QC</option>
                                        <option value='SK' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "SK") {
                                                echo "selected";
                                        }
                                        ?>>SK</option>
                                        <option value='YT' <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "YT") {
                                                echo "selected";
                                        }
                                        ?>>YT</option>
                                        <?php } else { ?>

                                        <option value="AK" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "AK") {
                                                echo "selected";
                                        }
                                        ?> >AK</option>
                                        <option value="AL" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "AL") {
                                                echo "selected";
                                        }
                                        ?>>AL</option>
                                        <option value="AP" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "AP") {
                                                echo "selected";
                                        }
                                        ?>>AP</option>
                                        <option value="AR" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "AR") {
                                                echo "selected";
                                        }
                                        ?>>AR</option>
                                        <option value="AS" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "AS") {
                                                echo "selected";
                                        }
                                        ?>>AS</option>
                                        <option value="AZ" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "AZ") {
                                                echo "selected";
                                        }
                                        ?>>AZ</option>
                                        <option value="CA" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "CA") {
                                                echo "selected";
                                        }
                                        ?>>CA</option>
                                        <option value="CO" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "CO") {
                                                echo "selected";
                                        }
                                        ?>>CO</option>
                                        <option value="CT" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "CT") {
                                                echo "selected";
                                        }
                                        ?>>CT</option>
                                        <option value="DC" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "DC") {
                                                echo "selected";
                                        }
                                        ?>>DC</option>
                                        <option value="DE" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "DE") {
                                                echo "selected";
                                        }
                                        ?>>DE</option>
                                        <option value="FL" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "FL") {
                                                echo "selected";
                                        }
                                        ?>>FL</option>
                                        <option value="FM" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "FM") {
                                                echo "selected";
                                        }
                                        ?>>FM</option>
                                        <option value="GA" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "GA") {
                                                echo "selected";
                                        }
                                        ?>>GA</option>
                                        <option value="GS" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "GS") {
                                                echo "selected";
                                        }
                                        ?>>GS</option>
                                        <option value="GU" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "GU") {
                                                echo "selected";
                                        }
                                        ?>>GU</option>
                                        <option value="HI" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "HI") {
                                                echo "selected";
                                        }
                                        ?>>HI</option>
                                        <option value="IA" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "IA") {
                                                echo "selected";
                                        }
                                        ?>>IA</option>
                                        <option value="ID" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "ID") {
                                                echo "selected";
                                        }
                                        ?>>ID</option>
                                        <option value="IL" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "IL") {
                                                echo "selected";
                                        }
                                        ?>>IL</option>
                                        <option value="IN" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "IN") {
                                                echo "selected";
                                        }
                                        ?>>IN</option>
                                        <option value="KS" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "KS") {
                                                echo "selected";
                                        }
                                        ?>>KS</option>
                                        <option value="KY" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "KY") {
                                                echo "selected";
                                        }
                                        ?>>KY</option>
                                        <option value="LA" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "LA") {
                                                echo "selected";
                                        }
                                        ?>>LA</option>
                                        <option value="MA" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MA") {
                                                echo "selected";
                                        }
                                        ?>>MA</option>
                                        <option value="MD" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MD") {
                                                echo "selected";
                                        }
                                        ?>>MD</option>
                                        <option value="ME" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "ME") {
                                                echo "selected";
                                        }
                                        ?>>ME</option>
                                        <option value="MH" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MH") {
                                                echo "selected";
                                        }
                                        ?>>MH</option>
                                        <option value="MI" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MI") {
                                                echo "selected";
                                        }
                                        ?>>MI</option>
                                        <option value="MN" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MN") {
                                                echo "selected";
                                        }
                                        ?>>MN</option>
                                        <option value="MO" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MO") {
                                                echo "selected";
                                        }
                                        ?>>MO</option>
                                        <option value="MP" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MP") {
                                                echo "selected";
                                        }
                                        ?>>MP</option>
                                        <option value="MS" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MS") {
                                                echo "selected";
                                        }
                                        ?>>MS</option>
                                        <option value="MT" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "MT") {
                                                echo "selected";
                                        }
                                        ?>>MT</option>
                                        <option value="NC" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NC") {
                                                echo "selected";
                                        }
                                        ?>>NC</option>
                                        <option value="ND" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "ND") {
                                                echo "selected";
                                        }
                                        ?>>ND</option>
                                        <option value="NE" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NE") {
                                                echo "selected";
                                        }
                                        ?>>NE</option>
                                        <option value="NH" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NH") {
                                                echo "selected";
                                        }
                                        ?>>NH</option>
                                        <option value="NJ" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NJ") {
                                                echo "selected";
                                        }
                                        ?>>NJ</option>
                                        <option value="NM" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NM") {
                                                echo "selected";
                                        }
                                        ?>>NM</option>
                                        <option value="NV" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NV") {
                                                echo "selected";
                                        }
                                        ?>>NV</option>
                                        <option value="NY" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "NY") {
                                                echo "selected";
                                        }
                                        ?>>NY</option>
                                        <option value="OH" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "OH") {
                                                echo "selected";
                                        }
                                        ?>>OH</option>
                                        <option value="OK" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "OK") {
                                                echo "selected";
                                        }
                                        ?>>OK</option>
                                        <option value="OR" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "OR") {
                                                echo "selected";
                                        }
                                        ?>>OR</option>
                                        <option value="PA" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "PA") {
                                                echo "selected";
                                        }
                                        ?>>PA</option>
                                        <option value="PR" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "PR") {
                                                echo "selected";
                                        }
                                        ?>>PR</option>
                                        <option value="PW" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "PW") {
                                                echo "selected";
                                        }
                                        ?>>PW</option>
                                        <option value="RI" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "RI") {
                                                echo "selected";
                                        }
                                        ?>>RI</option>
                                        <option value="SC" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "SC") {
                                                echo "selected";
                                        }
                                        ?>>SC</option>
                                        <option value="SD" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "SD") {
                                                echo "selected";
                                        }
                                        ?>>SD</option>
                                        <option value="TN" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "TN") {
                                                echo "selected";
                                        }
                                        ?>>TN</option>
                                        <option value="TX" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "TX") {
                                                echo "selected";
                                        }
                                        ?>>TX</option>
                                        <option value="UT" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "UT") {
                                                echo "selected";
                                        }
                                        ?>>UT</option>
                                        <option value="VA" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "VA") {
                                                echo "selected";
                                        }
                                        ?>>VA</option>
                                        <option value="VI" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "VI") {
                                                echo "selected";
                                        }
                                        ?>>VI</option>
                                        <option value="VT" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "VT") {
                                                echo "selected";
                                        }
                                        ?>>VT</option>
                                        <option value="WA" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "WA") {
                                                echo "selected";
                                        }
                                        ?>>WA</option>
                                        <option value="WI" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "WI") {
                                                echo "selected";
                                        }
                                        ?>>WI</option>
                                        <option value="WV" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "WV") {
                                                echo "selected";
                                        }
                                        ?>>WV</option>
                                        <option value="WY" <?php
                                        if ($_SESSION['merchant_info']['billing_state'] == "WY") {
                                                echo "selected";
                                        }
                                        ?>>WY</option>

                                <?php } ?>
                            </select>
                            <div id="errmessage_billing_state" class="fielderrormessage" ></div>

                            <label for="billing_city">City :  </label>
                            <input type="text" name="billing_city" id="billing_city" value=<?php echo $_SESSION['merchant_info']['billing_city']; ?> />
                            <div id="errmessage_billing_city" class="fielderrormessage" ></div>
                            <label for="billing_postalcode">Postal Code :</label>
                            <input type="text" maxlength="15"  name="billing_postalcode" id="billing_postalcode" value ="<?php echo $_SESSION['merchant_info']['billing_zip']; ?>" />
                            <div id="errmessage_billing_postalcode" class="fielderrormessage" ></div>
                            <input type="checkbox"  name="save_billing" id="save_billing" checked />Save this billing address to my Scanflip account 
                        </div>
                        <div class="cc_purchasenow">
                            <input type="button" name="Purchase" id="purchase_now1" value="<?php echo $merchant_msg['edit-compaign']['button_purchase_now']; ?>" onclick="getpurchasenow1()"/>
                            <input type="button" name="Cancel" id="purchase_cancel" value="<?php echo $merchant_msg['index']['btn_cancel']; ?>" onclick="getcancelnow()"/>
                            <input type="button" name="Back" id="purchase_back" value="<?php echo "Back"; ?>" onclick="getbacknow()"/>
                        </div>
                    </div>
                    <div class="point_success_div" style="display:none;">
                        <div class="success_msg" >
                        </div>
                        <div><input type="button" name="purchase_cancel" id="purchase_cancel" value="<?php echo "OK"; ?>" onclick="getcancelnow()"/></div>
                    </div>
                    <div class="point_error_div" style="display:none;">
                        <div class="error_msg" ></div>
                        <div><input type="button" name="purchase_cancel" id="purchase_cancel" value="<?php echo "OK"; ?>" onclick="getcancelnow()"/>&nbsp;&nbsp;<input type="button" name="Back" id="purchase_back" value="<?php echo "Back"; ?>" onclick="getbacknow()"/></div>
                    </div>
                </div>

                <?php
        }
        ?>
        <script type="text/javascript">

                function open_loader()
                {
                    //alert("inn");
                    popup_name = 'Notificationloader';
                    jQuery("#" + popup_name + "FrontDivProcessing").css("display", "block");
                    jQuery("#" + popup_name + "PopUpContainer").css("display", "block");
                    jQuery("#" + popup_name + "BackDiv").css("display", "block");
                }
                function close_loader()
                {
                    timeout = setInterval(function ()
                    {
                        //alert("hi");
                        clearTimeout(timeout);
                        popup_name = 'Notificationloader';
                        jQuery("#" + popup_name + "FrontDivProcessing").css("display", "none");
                        jQuery("#" + popup_name + "PopUpContainer").css("display", "none");
                        jQuery("#" + popup_name + "BackDiv").css("display", "none");
                    }, 1000);
                }

                bind_moreactionlink();
                function bind_moreactionlink()
                {
                    if (jQuery(".actiontd").length != 0)
                    {
                        jQuery(".actiontd").each(function () {
                            //alert(jQuery(this).find("action_wrapper").length);
                            if (jQuery(this).find(".inactive_moreaction").length == 1 || jQuery(this).find(".disable_moreaction").length == 1 || jQuery(this).find(".action_wrapper").length == 0)
                            {
                                jQuery(this).addClass("cursor_default");
                            }
                        });
                    }
                }

                jQuery(".notification_tooltip").each(function () {
                    jQuery(this).attr("data-toggle", "tooltip");
                    jQuery(this).attr("data-placement", "right");
                    jQuery(this).attr("data-html", "true");
                });
		jQuery(document).on('change','.fancybox-inner #billing_country',function(){
				var cn = this.value;
				
				jQuery.ajax({
		                        type: "POST",
		                        url: 'get_state.php',
		                        data: 'country=' + cn,
		                        success: function (msg)
		                        {
		                            jQuery('.fancybox-inner #billing_state').html(msg);
		                        }
		                    });
			});

                /*******  ********/
                jQuery(document).on("click", ".p_btn", function () {
                    jQuery.fancybox({
                        content: jQuery('#point_block').html(),
                        type: 'html',
                        openSpeed: 300,
                        closeSpeed: 300,
						minHeight:300,
						minWidth:400,
                        // topRatio: 0,

                        changeFade: 'fast',
                        afterShow: function () {
                            jQuery('.notification_tooltip').tooltip({
                                content: function () {
                                    return jQuery(this).attr('title');
                                },
                                track: true,
                                delay: 0,
                                showURL: false,
                                showBody: "<br>",
                                fade: 250

                            });
                        },
                        helpers: {
                            overlay:
                                    {
                                        closeClick: false,
                                        opacity: 0.3
                                    } // overlay
                        }
                    });

			jQuery(document).on('click','#btnPurchasePoints',function(){
				jQuery(".fancybox-inner .enter_n_card").hide();
				jQuery('.ad_cd_hd').show();
				
				
			})

			jQuery(document).on('change','.fancybox-inner #def_payment_card',function(){
				jQuery('.fancybox-inner #def_country').val(this.val);

			})
			
                });

                function  getcancelnow()
                {
			
			
			//jQuery('.fancybox-inner .enter_n_card').css("display","none");
                    jQuery.fancybox.close();
			
                }
                function  getbacknow()
                {
                    jQuery(".fancybox-inner .point_block_parent1").css("display", "none");
                    jQuery(".fancybox-inner .point_block_parent").css("display", "block");
                    jQuery(".fancybox-inner .point_success_div").css("display", "none");
                    jQuery(".fancybox-inner .point_error_div").css("display", "none");
                }

		function getdefaultpurchasenow(){
			var flag = true;
			var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
		            jQuery(".fielderrormessage").html("");
		            if (jQuery(".fancybox-inner #txt_price").val() == "")
		            {
		                flag = false;
		                jQuery(".fancybox-inner #errmessage_price").html("<?php echo $merchant_msg["common"]["purchase_point_price_msg"]; ?>");
		            }
		            else {
		                if (!numericReg.test(jQuery(".fancybox-inner #txt_price").val())) {

		                    flag = false;
		                    jQuery(".fancybox-inner #errmessage_price").html("<?php echo $merchant_msg["common"]["purchase_point_proper_price_msg"]; ?>");
		                }
		                else {
		                    if (parseInt(jQuery(".fancybox-inner #txt_price").val()) < 10)
		                    {
		                        flag = false;
		                        jQuery(".fancybox-inner #errmessage_price").html("<?php echo $merchant_msg["common"]["purchase_point_greaterthen_10_price_msg"]; ?>");
		                    }
		                }
		            }
			if (flag)
		    	{
		
				jQuery.ajax({
		                    type: "POST",
		                    url: "<?= WEB_PATH ?>/merchant/process.php",
		                    data: "txt_price=" + jQuery(".fancybox-inner #txt_price").val() + "&btnPurchasePointsnow=yes&billing_country="+jQuery(".fancybox-inner #def_country").val()+'&def_card='+jQuery('.fancybox-inner #def_payment_card').val(),
		                    success: function (msg)
		                    {

		                        var obj = jQuery.parseJSON(msg);		
		                        if (obj.status == "true")
		                        {
		                            var new_points_purchase = msg - ($(".available_point_span").text());
		                            $(".available_point_span").text(obj.available_points);
		                            $(".fancybox-inner .success_msg").text("You Have Successfully Purchased " + obj.purchased_points + " Points.");
		                            jQuery(".fancybox-inner .point_block_parent1").css("display", "none");
		                            jQuery(".fancybox-inner  .point_block_parent").css("display", "none");
		                            jQuery(".fancybox-inner .point_success_div").css("display", "block");
		                            jQuery(".fancybox-inner .point_error_div").css("display", "none");
		                            jQuery(".fancybox-inner .success_msg").html("Order ID  for this purchase is " + obj.order_id + ". Your Credit Card will be charged $" + obj.amount + ". We have credited " + obj.points + " Scanflip points to your account. To view recent history for your purchases go to Payment History. ");
		                            
		                        }
		                        else {
		                            jQuery(".fancybox-inner .error_msg").html("Credit Card authorization failed. Please check the credit card detail and try again. If problem persists please contact Scanflip support team.");
		                            jQuery(".fancybox-inner .point_block_parent1").css("display", "none");
		                            jQuery(".fancybox-inner .point_block_parent").css("display", "none");
		                            jQuery(".fancybox-inner .point_success_div").css("display", "none");
		                            jQuery(".fancybox-inner .point_error_div").css("display", "block");
		                            
		                        }

		                    }
		                });
			}

		}
                function  getpurchasenow1()
                {
                    /****** check field validation ***************/
                    var flag = true;
                    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
                    var country_code = jQuery(".fancybox-inner #billing_country option:selected").attr("country_code");
                    //alert(country_code);
                    jQuery(".fielderrormessage").html("");
                    if (jQuery(".fancybox-inner #credit_card_name").val() == "")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_credit_card_name").html("<?php echo $merchant_msg["common"]["purchase_point_credit_card_name"]; ?>");
                    }
			if (jQuery(".fancybox-inner #card_holder_name").val() == "")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_card_holder_name").html("Please enter credit cardholder name");
                    }

                    if (jQuery(".fancybox-inner #billing_postalcode").val() == "")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_billing_postalcode").html("<?php echo $merchant_msg["common"]["purchase_point_enter_postal_code"]; ?>");
                    }
                    else {
                        if (isValidPostalCode(jQuery(".fancybox-inner #billing_postalcode").val().toUpperCase(), country_code)) {
                            jQuery(".fancybox-inner #errmessage_billing_postalcode").html("");
                        }
                        else {
                            jQuery(".fancybox-inner #errmessage_billing_postalcode").html("<?php echo $merchant_msg["common"]["purchase_point_proper_postal_code"]; ?>");
                            flag = false;
                        }
                    }
                    if (jQuery(".fancybox-inner #billing_address").val() == "")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_billing_address").html("<?php echo $merchant_msg["common"]["purchase_point_enter_address"]; ?>");
                    }
                    if (jQuery(".fancybox-inner #billing_city").val() == "")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_billing_city").html("<?php echo $merchant_msg["common"]["purchase_point_enter_city"]; ?>");
                    }
                    if (flag)
                    {

			Stripe.createToken({
		            number: jQuery('.fancybox-inner #card_number').val(),
		            cvc: jQuery('.fancybox-inner #card_csv').val(),
		            exp_month: jQuery('.fancybox-inner #exp_month').val(),
		            exp_year: jQuery('.fancybox-inner #exp_year').val(),
			address_line1:jQuery('.fancybox-inner #billing_address').val(),
			address_state:jQuery('.fancybox-inner #billing_state').val(),
			address_city:jQuery('.fancybox-inner #billing_city').val(),
			address_zip:jQuery('.fancybox-inner #billing_postalcode').val(),
			address_country:jQuery('.fancybox-inner #billing_country').val(),
                        }, stripeResponseHandler);


                        
                    }
                }

		function order_place(){
			jQuery.ajax({
                            type: "POST",
                            url: "<?= WEB_PATH ?>/merchant/process.php",
                            data: "txt_price=" + $(".fancybox-inner #txt_price").val() + "&btnPurchasePointsnow=yes&exp_month=" + jQuery(".fancybox-inner  #exp_month").val() + "&exp_year=" + jQuery(".fancybox-inner  #exp_year").val() + "&card_csv=" + jQuery(".fancybox-inner #card_csv").val() + "&card_number=" + jQuery(".fancybox-inner #card_number").val() + "&card_type=" + jQuery(".fancybox-inner #card_type").val() + "&credit_card_name=" + jQuery(".fancybox-inner #credit_card_name").val() +"&card_holder_name=" + jQuery(".fancybox-inner #card_holder_name").val() + "&billing_address=" + jQuery(".fancybox-inner #billing_address").val() + "&billing_country=" + jQuery(".fancybox-inner #billing_country").val() + "&billing_state=" + jQuery(".fancybox-inner #billing_state").val() + "&billing_city=" + jQuery(".fancybox-inner #billing_city").val() + "&billing_postalcode=" + jQuery(".fancybox-inner #billing_postalcode").val() + "&save_billing=" + jQuery(".fancybox-inner #save_billing").val() + "&stripeToken=" + jQuery(".fancybox-inner #stripeToken").val(),
                            success: function (msg)
                            {

                                var obj = jQuery.parseJSON(msg);		
                                if (obj.status == "true")
                                {
					
                                    var new_points_purchase = msg - ($(".available_point_span").text());
                                    $(".available_point_span").text(obj.available_points);
                                    $(".fancybox-inner .success_msg").text("You Have Successfully Purchased " + obj.purchased_points + " Points.");
                                    jQuery(".fancybox-inner .point_block_parent1").css("display", "none");
                                    jQuery(".fancybox-inner  .point_block_parent").css("display", "none");
                                    jQuery(".fancybox-inner .point_success_div").css("display", "block");
                                    jQuery(".fancybox-inner .point_error_div").css("display", "none");
                                    jQuery(".fancybox-inner .success_msg").html("Order ID  for this purchase is " + obj.order_id + ". Your Credit Card will be charged $" + obj.amount + ". We have credited " + obj.points + " Scanflip points to your account. To view recent history for your purchases go to Payment History. ");
                                    //alert("Order ID  for this purchase is "+obj.order_id+". Your Credit Card will be charged $"+obj.amount+". We have credited "+obj.points+" Scanflip points to your account. To view recent history for your purchases go to Payment History. ");
                                }
                                else {
                                    /*jQuery(".fancybox-inner .error_msg").html("Credit Card authorization failed. Please check the credit card detail and try again. If problem persists please contact Scanflip support team.");*/
					jQuery(".fancybox-inner .error_msg").html(obj.serror);
                                    jQuery(".fancybox-inner .point_block_parent1").css("display", "none");
                                    jQuery(".fancybox-inner .point_block_parent").css("display", "none");
                                    jQuery(".fancybox-inner .point_success_div").css("display", "none");
                                    jQuery(".fancybox-inner .point_error_div").css("display", "block");
                                    ///  alert("Credit Card authorization failed. Please check the credit card detail and try again. If problem persists please contact Scanflip support team.");
                                }

                            }
                        });
		}

                function  getpurchasenow()
                {
                    /****** check field validation ***************/
                    var flag = true;
                    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
		    
                    jQuery(".fielderrormessage").html("");
                    if (jQuery(".fancybox-inner #txt_price").val() == "")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_price").html("<?php echo $merchant_msg["common"]["purchase_point_price_msg"]; ?>");
                    }
                    else {
                        if (!numericReg.test(jQuery(".fancybox-inner #txt_price").val())) {

                            flag = false;
                            jQuery(".fancybox-inner #errmessage_price").html("<?php echo $merchant_msg["common"]["purchase_point_proper_price_msg"]; ?>");
                        }
                        else {
                            if (parseInt(jQuery(".fancybox-inner #txt_price").val()) < 10)
                            {
                                flag = false;
                                jQuery(".fancybox-inner #errmessage_price").html("<?php echo $merchant_msg["common"]["purchase_point_greaterthen_10_price_msg"]; ?>");
                            }
                        }
                    }
                    if (jQuery(".fancybox-inner #exp_year").val() == "0" || jQuery(".fancybox-inner .fancybox-inner #exp_month").val() == "0")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_expirydate").html("<?php echo $merchant_msg["common"]["purchase_point_expiry_msg"]; ?>");
                    }
                    /* if (jQuery(".fancybox-inner #card_type").val() == "0")
                     {
                     flag = false;
                     jQuery(".fancybox-inner #errmessage_card_type").html("<?php echo $merchant_msg["common"]["purchase_point_card_type"]; ?>");
                     }*/
                    else {
                        if (jQuery('.fancybox-inner #card_number').val() != "")
                        {
                            /* if (jQuery(".fancybox-inner #card_type").val() == "visa")
                             {
                             if (!((jQuery('.fancybox-inner #card_number').val()).startsWith("4")))
                             {
                             flag = false;
                             jQuery(".fancybox-inner #errmessage_card_number").html("<?php echo $merchant_msg["common"]["purchase_point_card_number"]; ?>");
                             }
                             
                             }
                             else
                             {
                             
                             if (!((jQuery('.fancybox-inner #card_number').val()).startsWith("5")))
                             {
                             flag = false;
                             jQuery(".fancybox-inner #errmessage_card_number").html("<?php echo $merchant_msg["common"]["purchase_point_card_number"]; ?>");
                             }
                             }*/
                        }
                    }
                    if (jQuery(".fancybox-inner #card_number").val() == "")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_card_number").html("<?php echo $merchant_msg["common"]["purchase_point_card_no"]; ?>");
                    }
                    else {
                        if (!numericReg.test(jQuery(".fancybox-inner #card_number").val())) {

                            flag = false;
                            jQuery(".fancybox-inner #errmessage_card_number").html("<?php echo $merchant_msg["common"]["purchase_point_card_number"]; ?>");
                        }
                    }
                    if (jQuery(".fancybox-inner #card_csv").val() == "")
                    {
                        flag = false;
                        jQuery(".fancybox-inner #errmessage_cardcsv").html("<?php echo $merchant_msg["common"]["purchase_point_enter_csv"]; ?>");
                    }
                    else {
                        if (!numericReg.test(jQuery(".fancybox-inner #card_csv").val())) {

                            flag = false;
                            jQuery(".fancybox-inner #errmessage_cardcsv").html("<?php echo $merchant_msg["common"]["purchase_point_enter_proper_csv"]; ?>");
                        }
                    }
                    if (flag)
                    {
                        jQuery(".fancybox-inner .point_block_parent").css("display", "none");
                        jQuery(".fancybox-inner .point_block_parent1").css("display", "block");

                        jQuery(".fancybox-inner .point_success_div").css("display", "none");
                        jQuery(".fancybox-inner .point_error_div").css("display", "none");

                        
                    }
                    var numbers = /^[0-9]+$/;
                }
                jQuery(document).on("keyup", '.fancybox-inner input#txt_price', function (e) {
                    if (e.keyCode == '13')
                    {
                        jQuery.fancybox.close();
                    }
                    else
                    {
                        var numbers = /^[0-9]+$/;
                        if (jQuery(".fancybox-inner #txt_price").val() == "")
                        {
                            jQuery(".fancybox-inner .purchasepointclass").html("");
                        }
                        else if (jQuery(".fancybox-inner #txt_price").val().match(numbers))
                        {
                            jQuery.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "txt_price=" + jQuery(".fancybox-inner #txt_price").val() + "&getpoints=yes",
                                success: function (msg)
                                {

                                    jQuery(".fancybox-inner #span_con_point").text(msg);
                                    jQuery(".fancybox-inner .success_msg").text("");

                                }
                            });
                        }
                        else
                        {

                        }
                    }
                });
                /*********** *************/
                //});
                function isValidPostalCode(postalCode, countryCode) {
                    switch (countryCode) {
                        case "US":
                            postalCodeRegex = /^([0-9]{5})(?:[-\s]*([0-9]{4}))?$/;
                            break;
                        case "CA":
                            postalCodeRegex = /^([A-Z][0-9][A-Z])\s*([0-9][A-Z][0-9])$/;
                            break;
                        default:
                            postalCodeRegex = /^(?:[A-Z0-9]+([- ]?[A-Z0-9]+)*)?$/;
                    }
                    return postalCodeRegex.test(postalCode);
                }
        </script>
        <!--end of copyright--></div>
    <!--end of footer--></div>
</div>
