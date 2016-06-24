<?php
  require_once('init.php');
  $stripe = array(
    'secret_key'      => 'sk_test_tV8FvhTEEBTGgsfHSFhLUCnW',
    'publishable_key' => 'pk_test_ZYKGkhMQCP2RAjEAwEM5MqaO'
    );
  \Stripe\Stripe::setApiKey($stripe['secret_key']);
?>
