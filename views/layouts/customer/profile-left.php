<?php
/* * ****** 
  @USE : inside my account, different links
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : my-profile.php, my-orders.php, myreviews.php, mynotification.php, my-emailsettings.php, mypoints.php, change-password.php
 * ******* */
?>
<style type="text/css">
    #email-link:hover{
        color:orange !important;
    }
    #profile-link:hover{
        color:orange !important;
    }

    #password-link:hover{
        color:orange !important;
    }
    #mypoints-link:hover{
        color:orange !important;
    }


</style>

<?php
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");

require LIBRARY . '/fb-sdk/src/facebook.php';

$facebook = new Facebook(array(
    'appId' => '654125114605525',
    'secret' => '2870b451a0e7d287f1899d0e401d3c4e',
        ));
?>
<table width="100%"  border="0" cellspacing="2" cellpadding="2">
    <tr>
        <td align="left"><a id="profile-link" style="color:#0066FF" href="<?= WEB_PATH ?>/my-profile.php"><?php echo $language_msg["profile"]["manage_profile"]; ?></a></td>
    </tr>
    <tr>
        <td align="left"><a id="email-link" style="color:#0066FF" href="<?= WEB_PATH ?>/my-emailsettings.php"><?php echo $language_msg["profile"]["email_setting"]; ?></a></td>
    </tr>
    <?php
    $user = $facebook->getUser();
    //print_r($user);
    //if ($user) 
    if (isset($_SESSION['facebook_usr_login'])) {
            if ($_SESSION['facebook_usr_login'] == 1) {
                    
            } else {
                    ?>
                    <tr>
                        <td align="left"><a id="password-link" style="color:#0066FF"  href="<?= WEB_PATH ?>/change-password.php"><?php echo $language_msg["profile"]["change_password"]; ?></a></td>
                    </tr>
                    <?php
            }
    } else {
            ?>
            <tr>
                <td align="left"><a id="password-link" style="color:#0066FF"  href="<?= WEB_PATH ?>/change-password.php"><?php echo $language_msg["profile"]["change_password"]; ?></a></td>
            </tr>
            <?php
    }
    ?>
    <tr>
        <td align="left"><a id="mypoints-link" style="color:#0066FF"  href="<?= WEB_PATH ?>/mypoints.php"><?php echo $language_msg["profile"]["transactions"]; ?></a></td>
    </tr>
    <tr>
        <td align="left"><a id="myorder-link" style="color:#0066FF"  href="<?= WEB_PATH ?>/my-orders.php"><?php echo $language_msg["profile"]["myorder"]; ?></a></td>
    </tr>	
    <?php
    $arr = file(WEB_PATH . '/process.php?review_notification=yes&customer_id=' . $_SESSION['customer_id'].'&check=true');
    
    if ($arr[0] != 0) {
            ?>
            <tr>
                <td align="left"><a id="mypending-link" style="color:#0066FF"  href="<?= WEB_PATH ?>/myreviews.php"><?php echo $language_msg["profile"]["pending_reviews"]; ?></a></td>
            </tr>
            <?php
    }
    ?>
    <tr>
        <td align="left"><a id="mynotification-link" style="color:#0066FF"  href="<?= WEB_PATH ?>/mynotification.php"><?php echo $language_msg["profile"]["notification_Setting"]; ?></a></td>
    </tr>	

</table>
