<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$card = $objDB->Conn->Execute("Select * from stripe_payment_cards where merchant_id=?", array($_SESSION['merchant_id']));
if ($card->RecordCount() > 0) {
        
        while ($c = $def_card->FetchRow()) {
                
                ?>
                <option value="<?php echo $c['card_id'] ?>" data-cntry="<?php echo $c['billing_country'] ?>"><?php echo $c['type'] . '  ****' . $c['card_number'] ?></option>
        <?php }
}
?>

