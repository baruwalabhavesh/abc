<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$card = $objDB->Conn->Execute("Select * from stripe_payment_cards where merchant_id=? and id=?", array($_SESSION['merchant_id'], $_GET['id']));
$canada_state = array('AB' => 'AB', 'BC' => 'BC', 'LB' => 'LB', 'MB' => 'MB', 'NB' => 'NB', 'NF' => 'NF', 'NS' => 'NS', 'NT' => 'NT', 'NU' => 'NU', 'ON' => 'ON', 'PE' => 'PE', 'PQ' => 'PQ', 'QB' => 'QB', 'QC' => 'QC', 'SK' => 'SK', 'YT' => 'YT');
$us_states = array('AK' => 'AK', 'AL' => 'AL', 'AP' => 'AP', 'AR' => 'AR', 'AS' => 'AS', 'AZ' => 'AZ', 'CA' => 'CA', 'CO' => 'CO', 'CT' => 'CT', 'DC' => 'DC', 'DE' => 'DE', 'FL' => 'FL', 'FM' => 'FM', 'GA' => 'GA', 'GS' => 'GS', 'GU' => 'GU', 'HI' => 'HI', 'IA' => 'IA', 'ID' => 'ID', 'IL' => 'IL', 'IN' => 'IN', 'KS' => 'KS', 'KY' => 'KY', 'LA' => 'LA', 'MA' => 'MA', 'MD' => 'MD', 'ME' => 'ME', 'MH' => 'MH', 'MI' => 'MI', 'MN' => 'MN', 'MO' => 'MO', 'MP' => 'MP', 'MS' => 'MS', 'MT' => 'MT', 'NC' => 'NC', 'ND' => 'ND', 'NE' => 'NE', 'NH' => 'NH', 'NJ' => 'NJ', 'NM' => 'NM', 'NV' => 'NV', 'NY' => 'NY', 'OH' => 'OH', 'OK' => 'OK', 'OR' => 'OR', 'PA' => 'PA', 'PR' => 'PR', 'PW' => 'PW', 'RI' => 'RI', 'SC' => 'SC', 'SD' => 'SD', 'TN' => 'TN', 'TX' => 'TX', 'UT' => 'UT', 'VA' => 'VA', 'VI' => 'VI', 'VT' => 'VT');
$country = array('USA' => 'USA', 'Canada' => 'Canada');
if ($card->RecordCount() > 0) {
        $def_card = $card->FetchRow();
        ?>
        <form class="form-horizontal edit_pay_card">
		<h4 class="head_msg">Edit Card</h4>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="exp_month" class="col-sm-4 control-label">Exp. month: </label>
                    <div class="col-sm-8">
                        <select name="exp_month" id="exp_month" class="form-control">
                            <option value="0" >MM</option>
                            <?php
                            for ($em = 01; $em <= 12; $em++) {
                                    ?>
                                    <option value="<?php echo str_pad($em, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($em == $def_card['exp_month']) ? "selected" : "" ?>>
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
                                    <option value="<?php echo $ey; ?>" <?php echo ($ey == $def_card['exp_year']) ? "selected" : "" ?>><?php echo $ey; ?></option>
                                    <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
		<div class="form-group">
		    <label for="cvc" class="col-sm-4 control-label">Cardholder Name  : </label>
		    <div class="col-sm-8">
		        <input type="text" name="card_holder_name" id="card_holder_name" class="form-control" value="<?php echo $def_card['card_holder_name']; ?>">
		    </div>
		</div>
                <div class="form-group">
                    <label for="billing_address1" class="col-sm-4 control-label">Address: </label>
                    <div class="col-sm-8">
                        <input type="text" name="billing_address1" id="billing_address1" class="form-control" value="<?php echo $def_card['billing_address1']; ?>">
                    </div>
                </div>
<!--                <div class="form-group">
                    <label for="billing_address2" class="col-sm-3 control-label">Address2 : </label>
                    <div class="col-sm-9">
                        <input type="text" name="billing_address2" id="billing_address2" class="form-control" value="<?php //echo $def_card['billing_address2']; ?>">
                    </div>
                </div>-->
                <div class="form-group">
                    <label for="billing_city" class="col-sm-4 control-label">City : </label>
                    <div class="col-sm-8">
                        <input type="text" name="billing_city" id="billing_city" class="form-control" value="<?php echo $def_card['billing_city']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="billing_zip" class="col-sm-4 control-label">Zip/Postal : </label>
                    <div class="col-sm-8">
                        <input type="text" name="billing_zip" id="billing_zip" class="form-control" value="<?php echo $def_card['billing_zip']; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="billing_country" class="col-sm-4 control-label">Country : </label>
                    <div class="col-sm-8">
                        <select name="billing_country" id="billing_country" class="form-control">
                            <?php foreach ($country as $cn => $v) {
                                    ?>
                                    <option  value="<?php echo $cn; ?>" <?php
                                    if ($def_card['billing_country'] == $cn) {
                                            echo "selected";
                                    }
                                    ?> ><?php echo $v; ?>
                                    </option>
                                    <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
		<div class="form-group">
                    <label for="billing_state" class="col-sm-4 control-label">State : </label>
                    <div class="col-sm-8">
                        <select name="billing_state" id="billing_state" class="form-control">
                            <?php
                            if ($def_card['billing_country'] == "Canada") {
                                    foreach ($canada_state as $c => $cs) {
                                            ?>
                                            <option value='<?php echo $c; ?>' <?php
                                            if ($c == $def_card['billing_state']) {
                                                    echo "selected";
                                            }
                                            ?>><?php echo $cs; ?></option>
                                                    <?php
                                            }
                                    } else {
                                            foreach ($us_states as $u => $us) {
                                                    ?>
                                            <option value='<?php echo $c; ?>' <?php
                                            if ($u == $def_card['billing_state']) {
                                                    echo "selected";
                                            }
                                            ?>><?php echo $us; ?></option>
                                                    <?php
                                            }
                                    }
                                    ?>   
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="card_id" value="<?php echo $def_card['card_id']; ?>">
                    <input type="hidden" name="id" value="<?php echo $def_card['id']; ?>">
                    <input type="hidden" name="customer_id" value="<?php echo $def_card['customer_id']; ?>">
		    <button type="button" class="btn btn-default btn-no-card">Cancel</button>
                    <button type="button" class="btn btn-primary btn-update">Update</button>
                </div>

            </div>
        </form>
<?php } ?>

