<?php
$canada_state = array('AB' => 'AB', 'BC' => 'BC', 'LB' => 'LB', 'MB' => 'MB', 'NB' => 'NB', 'NF' => 'NF', 'NS' => 'NS', 'NT' => 'NT', 'NU' => 'NU', 'ON' => 'ON', 'PE' => 'PE', 'PQ' => 'PQ', 'QB' => 'QB', 'QC' => 'QC', 'SK' => 'SK', 'YT' => 'YT');
$us_states = array('AK' => 'AK', 'AL' => 'AL', 'AP' => 'AP', 'AR' => 'AR', 'AS' => 'AS', 'AZ' => 'AZ', 'CA' => 'CA', 'CO' => 'CO', 'CT' => 'CT', 'DC' => 'DC', 'DE' => 'DE', 'FL' => 'FL', 'FM' => 'FM', 'GA' => 'GA', 'GS' => 'GS', 'GU' => 'GU', 'HI' => 'HI', 'IA' => 'IA', 'ID' => 'ID', 'IL' => 'IL', 'IN' => 'IN', 'KS' => 'KS', 'KY' => 'KY', 'LA' => 'LA', 'MA' => 'MA', 'MD' => 'MD', 'ME' => 'ME', 'MH' => 'MH', 'MI' => 'MI', 'MN' => 'MN', 'MO' => 'MO', 'MP' => 'MP', 'MS' => 'MS', 'MT' => 'MT', 'NC' => 'NC', 'ND' => 'ND', 'NE' => 'NE', 'NH' => 'NH', 'NJ' => 'NJ', 'NM' => 'NM', 'NV' => 'NV', 'NY' => 'NY', 'OH' => 'OH', 'OK' => 'OK', 'OR' => 'OR', 'PA' => 'PA', 'PR' => 'PR', 'PW' => 'PW', 'RI' => 'RI', 'SC' => 'SC', 'SD' => 'SD', 'TN' => 'TN', 'TX' => 'TX', 'UT' => 'UT', 'VA' => 'VA', 'VI' => 'VI', 'VT' => 'VT');
$country = array('USA' => 'USA', 'Canada' => 'Canada');
?>
<div class="form-group title"><h2><b>Choose Subscription plan</b></h2></div>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
$id = $_SESSION['merchant_id'];
//$packages_res = $objDB->Conn->Execute("Select * from billing_packages where id NOT IN(?) and price<>?", array($_REQUEST['pid'], 0));
$check_customer = $objDB->Conn->Execute("Select stripe_customer_id,address,city,state,zipcode,country from merchant_user where id = ? ", array($id));


$flag = 0;
$country = '';
$next_class = 'choose_subscr_plan';
if ($check_customer->RecordCount() > 0) {
        $customer = $check_customer->FetchRow();
        if (empty($customer['stripe_customer_id'])) {
                $flag = 1;
                $next_class = 'next_add_card';
        }
        $country = $customer['country'];
}

$packages_res = $objDB->Conn->Execute("Select b.*,c.country_name,c.currency_code from billing_packages b Inner join currencies c on c.currency_code=b.currency and c.country_name=? where b.id NOT IN(?) AND b.price <>?", array($country, $_REQUEST['pid'], 0));
?>
<div class="ss_outer">
    
    <div class="load" style="display:none;">Please wait ...</div>
    <div class="load_outr" style="display:none;"></div>
    <?php
    if ($packages_res->RecordCount() > 0) {
		
		$cj = $packages_res->RecordCount();
		$cj = ($cj < 4)?12/$cj:'3';
            ?>

            <div class="plan_outer col-md-12">
                <?php
                while ($p = $packages_res->FetchRow()) {
			$interval = $p['interval'];
			if($interval == 'q'){
				$interval = 'Quaterly';
				$interval_count = 3;
				$price = number_format($p['price']/3, 2, '.', '');
			}else if($interval == 'a'){
				$interval = 'Annualy';
				$interval_count = 1;
				$price = number_format($p['price']/12, 2, '.', '');
			}else{
				$interval = 'Monthly';
				$interval_count = 1;
				$price = number_format($p['price'], 2, '.', '');
			}
			$trial = $p['trial_period_days'];
                        ?>
                        <div id="splan_<?php echo $p['id']; ?>" class="col-sm-<?php echo $cj; ?> splan_div_06">
                            <div class="splan_div">
                                <h4><?php echo $p['pack_name']; ?></h4>
                                <p>Price : <strong><?php echo $price.' '.$p['currency']; ?></strong>/month</p>
				<p>Billed : <?php echo $interval; ?> </p>
                                <p>Number of locations : <?php echo $p['no_of_loca']; ?></p>
                                <p>Coupon (Transaction) Fee : <?php echo $p['transaction_fees']; ?> Points</p>
                                <p><input type="button" class="btn btn-primary <?php echo $next_class; ?>" id="<?php echo $p['id']; ?>" value="Subscribe Now"/> <p>
				<?php if($trial > 0){?>
				<p>Start trial : <strong><?php echo $trial; ?></strong> Days</p>
				<?php } ?>
                            </div>
                        </div>
                        <?php
                }
                ?>
            </div>
            <?php
    }
    if ($flag == 1) {
            ?>

                                                                                                                        <!--<input type="button" class="btn btn-primary" id="add_card" value="Next"/>-->
            <div class="next_add_card_div" style="display:none;">
                <form class="form-horizontal add_pay_card" method="POST">
                    <h4 class="head_msg">Add Card</h4>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="credit_error"></div>
                            <div class="form-group">
                                <label for="card_number" class="col-sm-4 control-label">Card Number: </label>
                                <div class="col-sm-8">
                                    <input type="text" name="card_number" id="card_number" class="form-control creditcard" required value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cvc" class="col-sm-4 control-label">CVV: </label>
                                <div class="col-sm-8">
                                    <input type="text" name="cvc" id="cvc" class="form-control " required value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cvc" class="col-sm-4 control-label">Cardholder Name: </label>
                                <div class="col-sm-8">
                                    <input type="text" name="card_holder_name" id="card_holder_name" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exp_month" class="col-sm-4 control-label">Exp. month: </label>
                                <div class="col-sm-8">
                                    <select name="exp_month" id="exp_month" class="form-control">
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
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exp_year" class="col-sm-4 control-label">Exp. year: </label>
                                <div class="col-sm-8">
                                    <select name="exp_year" id="exp_year" class="form-control">
                                        <option  value="0" >YYYY</option>
                                        <?php
                                        for ($ey = date("Y"); $ey <= date("Y") + 20; $ey++) {
                                                ?>
                                                <option value="<?php echo $ey; ?>" <?php echo ($ey == date('Y')) ? "selected" : "" ?>><?php echo $ey; ?></option>
                                                <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="billing_address1" class="col-sm-4 control-label">Address: </label>
                                <div class="col-sm-8">
                                    <input type="text" name="billing_address1" id="billing_address1" class="form-control" value="<?php echo $customer['address']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_country" class="col-sm-4 control-label">Country: </label>
                                <div class="col-sm-8">
                                    <select name="billing_country" id="billing_country" class="form-control">
                                        <?php foreach ($country as $cn => $v) {
                                                ?>
                                                <option  value="<?php echo $cn; ?>" <?php echo ($customer['country'] == $cn) ? 'selected' : ''; ?>><?php echo $v; ?>
                                                </option>
                                                <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_state" class="col-sm-4 control-label">State: </label>
                                <div class="col-sm-8">
                                    <select name="billing_state" id="billing_state" class="form-control">
                                        <?php
                                        if ($_SESSION['merchant_info']['billing_country'] == "Canada") {
                                                foreach ($canada_state as $c => $cs) {
                                                        ?>
                                                        <option value='<?php echo $c; ?>' <?php echo ($customer['state'] == $c) ? 'selected' : ''; ?>><?php echo $cs; ?></option>
                                                        <?php
                                                }
                                        } else {
                                                foreach ($us_states as $u => $us) {
                                                        ?>
                                                        <option value='<?php echo $c; ?>' <?php echo ($customer['state'] == $c) ? 'selected' : ''; ?>><?php echo $us; ?></option>
                                                        <?php
                                                }
                                        }
                                        ?>   
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_city" class="col-sm-4 control-label">City: </label>
                                <div class="col-sm-8">
                                    <input type="text" name="billing_city" id="billing_city" class="form-control" value="<?php echo $customer['city']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_zip" class="col-sm-4 control-label">Zip/Postal: </label>
                                <div class="col-sm-8">
                                    <input type="text" name="billing_zip" id="billing_zip" class="form-control" value="<?php echo $customer['zipcode']; ?>">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="choose_subscr_plan" id="" />
                        <div class="">
                            <div class="form-group">
                                <button type="button" class="btn btn-default btn-no-card">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-add-card">Add</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <?php
    } else {
            ?>
            <!--<input type="button" class="btn btn-primary" id="choose_subscr_plan" value="Subscribe"/> -->
            <?php
    }
    ?>

</div>
<script type="text/javascript">

        jQuery(document).ready(function () {

            var flag = <?php echo $flag; ?>;
            if (flag == 1) {
                jQuery('.next_add_card').on('click', function () {
                    jQuery(".plan_outer").hide();
                    jQuery('.next_add_card_div').show();

                    jQuery(".choose_subscr_plan").attr('id', this.id);
                });

                jQuery(".add_pay_card").validate({
                    rules: {
                        card_holder_name: {
                            required: true
                        },
                        card_number: {
                            required: true,
                            creditcard: true
                        },
                        cvc: {
                            required: true,
                            number: true,
                            maxlength: 4
                        },
                        billing_zip: {
                            required: true,
                            zipcode: true
                        }
                    },
                    messages: {
                        card_holder_name: {
                            required: "Please enter Card Holders name"
                        },
                        card_number: {
                            required: "Please enter a credit card number",
                            creditcard: "Please enter a valid credit card number."
                        },
                        cvc: {
                            required: "Please enter CVV number",
                            maxlength: "CVV must be at least 3 characters long"
                        },
                        billing_zip: {
                            required: "Please enter Zip Code"
                        }
                    }
                });

                jQuery.validator.addMethod("zipcode", function (value, element) {

                    var postal_code = jQuery("#billing_zip").val();
                    var usPostalReg = /^([0-9]{5})(?:[-\s]*([0-9]{4}))?$/;
                    var canadaPostalReg = /^([A-Z][0-9][A-Z])\s*([0-9][A-Z][0-9])$/;
                    var postalCodeRegex = /^(?:[A-Z0-9]+([- ]?[A-Z0-9]+)*)?$/;
                    var country = jQuery("#billing_country").val();
                    
                    if (country == "USA")
                    {
                        return usPostalReg.test(postal_code);

                    } else if (country == "Canada")
                    {
                        return canadaPostalReg.test(postal_code);

                    } else {
                        return postalCodeRegex.test(postal_code);
                    }
                }, "Incorrect zip code");

                jQuery('.add_pay_card').on('submit', function (e) {
                    e.preventDefault();
			jQuery('.load,load_outr').show();
                    var fm = jQuery(this).serialize();
                    jQuery.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'stripe_add_new_card=true&' + fm,
                        success: function (msg)
                        {
                            var obj = jQuery.parseJSON(msg);
                            if (obj.status == "true")
                            {
                                //jQuery.fancybox.close();

                                jQuery('.choose_subscr_plan').trigger('click');
                            } else {
				jQuery('.load,load_outr').hide();
                            }

                        }
                    });
                });

            }

            jQuery('.choose_subscr_plan').on('click', function () {

                var sub_id = this.id;
		jQuery('.load,.load_outr').show();
                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'subscribe_plan=true&pid=' + sub_id,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        if (obj.status == "true")
                        {
				jQuery('#subscr_package').data('id',obj.plan.id);
				jQuery('#cancel_subscr_package').data('id',obj.plan.id);

                                jQuery('#pack_name').val(obj.plan.pack_name);
                                jQuery('#pack_price').val(obj.plan.price);
                                jQuery('#no_of_loca').val(obj.plan.no_of_loca);
                                jQuery('#no_of_active_camp_per_loca').val(obj.plan.no_of_active_camp_per_loca);
                                jQuery('#total_no_of_camp_per_month').val(obj.plan.total_no_of_camp_per_month);
                                jQuery('#total_no_of_camp_left_per_month').val(obj.plan.total_no_of_campaign - obj.plan.total_no_of_camp_per_month);
                                jQuery('#min_share_point').val(obj.plan.min_share_point);
                                jQuery('#min_reward_point').val(obj.plan.min_reward_point);
                                jQuery('#transaction_fees').val(obj.plan.transaction_fees+' Points');
                                jQuery.fancybox.close();
				jQuery('.load,.load_outr').hide();
                        } else {
                            	jQuery('.load').before(obj.serror);
				jQuery('.load,.load_outr').hide();
                        }

                    }
                });
            })

        })

</script>
