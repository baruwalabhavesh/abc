<?php
$canada_state = array('AB' => 'AB', 'BC' => 'BC', 'LB' => 'LB', 'MB' => 'MB', 'NB' => 'NB', 'NF' => 'NF', 'NS' => 'NS', 'NT' => 'NT', 'NU' => 'NU', 'ON' => 'ON', 'PE' => 'PE', 'PQ' => 'PQ', 'QB' => 'QB', 'QC' => 'QC', 'SK' => 'SK', 'YT' => 'YT');
$us_states = array('AK' => 'AK', 'AL' => 'AL', 'AP' => 'AP', 'AR' => 'AR', 'AS' => 'AS', 'AZ' => 'AZ', 'CA' => 'CA', 'CO' => 'CO', 'CT' => 'CT', 'DC' => 'DC', 'DE' => 'DE', 'FL' => 'FL', 'FM' => 'FM', 'GA' => 'GA', 'GS' => 'GS', 'GU' => 'GU', 'HI' => 'HI', 'IA' => 'IA', 'ID' => 'ID', 'IL' => 'IL', 'IN' => 'IN', 'KS' => 'KS', 'KY' => 'KY', 'LA' => 'LA', 'MA' => 'MA', 'MD' => 'MD', 'ME' => 'ME', 'MH' => 'MH', 'MI' => 'MI', 'MN' => 'MN', 'MO' => 'MO', 'MP' => 'MP', 'MS' => 'MS', 'MT' => 'MT', 'NC' => 'NC', 'ND' => 'ND', 'NE' => 'NE', 'NH' => 'NH', 'NJ' => 'NJ', 'NM' => 'NM', 'NV' => 'NV', 'NY' => 'NY', 'OH' => 'OH', 'OK' => 'OK', 'OR' => 'OR', 'PA' => 'PA', 'PR' => 'PR', 'PW' => 'PW', 'RI' => 'RI', 'SC' => 'SC', 'SD' => 'SD', 'TN' => 'TN', 'TX' => 'TX', 'UT' => 'UT', 'VA' => 'VA', 'VI' => 'VI', 'VT' => 'VT');
$country = array('USA' => 'USA', 'Canada' => 'Canada');
?>
<form class="form-horizontal add_pay_card" method="POST">
<h4 class="head_msg">Add Card</h4>
    <div class="col-md-12">
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
        <div class="form-group">
            <label for="billing_address1" class="col-sm-4 control-label">Address: </label>
            <div class="col-sm-8">
                <input type="text" name="billing_address1" id="billing_address1" class="form-control" value="">
            </div>
        </div>
        <!--                <div class="form-group">
                            <label for="billing_address2" class="col-sm-4 control-label">Address2 : </label>
                            <div class="col-sm-8">
                                <input type="text" name="billing_address2" id="billing_address2" class="form-control" value="<?php //echo $def_card['billing_address2'];        ?>">
                            </div>
                        </div>-->
        <div class="form-group">
            <label for="billing_city" class="col-sm-4 control-label">City: </label>
            <div class="col-sm-8">
                <input type="text" name="billing_city" id="billing_city" class="form-control" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="billing_zip" class="col-sm-4 control-label">Zip/Postal: </label>
            <div class="col-sm-8">
                <input type="text" name="billing_zip" id="billing_zip" class="form-control" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="billing_country" class="col-sm-4 control-label">Country: </label>
            <div class="col-sm-8">
                <select name="billing_country" id="billing_country" class="form-control">
                    <?php foreach ($country as $cn => $v) {
                            ?>
                            <option  value="<?php echo $cn; ?>" <?php echo ($_SESSION['merchant_info']['billing_country'] == $cn)?'selected':''; ?>><?php echo $v; ?>
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
                                    <option value='<?php echo $c; ?>'><?php echo $cs; ?></option>
                                    <?php
                            }
                    } else {
                            foreach ($us_states as $u => $us) {
                                    ?>
                                    <option value='<?php echo $c; ?>' ><?php echo $us; ?></option>
                                    <?php
                            }
                    }
                    ?>   
                </select>
            </div>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-default btn-no-card">Cancel</button>
            <button type="submit" class="btn btn-primary btn-add-card">Add</button>
        </div>

    </div>
</form>
<script type="text/javascript">
$.validator.addMethod("zipcode", function(value, element){

	var postal_code=jQuery("#billing_zip").val();
	var usPostalReg = /^([0-9]{5})(?:[-\s]*([0-9]{4}))?$/;
	var canadaPostalReg = /^([A-Z][0-9][A-Z])\s*([0-9][A-Z][0-9])$/;
	var postalCodeRegex = /^(?:[A-Z0-9]+([- ]?[A-Z0-9]+)*)?$/;
	var country = jQuery("#billing_country").val();
	console.log(postal_code);
	if(country=="USA")
	{    
		return usPostalReg.test(postal_code);
	   
	}else if(country=="Canada")
	{
		return canadaPostalReg.test(postal_code);
		
	}else{
		return postalCodeRegex.test(postal_code);
		}
}, "Incorrect zip code"); 

        $(".add_pay_card").validate({
            rules: {
		card_holder_name: {
			required:true
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
		    	zipcode:true 
		}
            },
            messages: {
		card_holder_name: {
			required:"Please enter Card Holders name"
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
        $('.add_pay_card').on('submit', function (e) {
            e.preventDefault();
            var fm = $(this).serialize();
            jQuery.ajax({
                type: "POST",
                url: 'process.php',
                data: 'stripe_add_new_card=true&' + fm,
                success: function (msg)
                {
                    var obj = jQuery.parseJSON(msg);
                    if (obj.status == "true")
                    {
                        jQuery.fancybox.close();
                        oCache.iCacheLower = -1;
                        $('#payment_cards_table').dataTable().fnDraw();
			$('.add_pay_card .credit_error').removeClass('error');
                        
                    } else {
                        $('.add_pay_card .credit_error').addClass('error').text(obj.serror);
                    }

                }
            });

        });

</script>
