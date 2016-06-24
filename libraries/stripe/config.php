<?php
  require_once(LIBRARY.'/stripe/lib/Stripe.php');
  $stripe = array(
    'secret_key'      => 'sk_test_tV8FvhTEEBTGgsfHSFhLUCnW',
    'publishable_key' => 'pk_test_ZYKGkhMQCP2RAjEAwEM5MqaO'
    );
  Stripe::setApiKey($stripe['secret_key']);
?>
