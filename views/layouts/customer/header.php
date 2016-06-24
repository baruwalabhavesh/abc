<!--<meta name="viewport" content="width=device-width, user-scalable=no">-->
<?php
/* * ****** 
  @USE : header
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : include in all files
 * ******* */

function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
                $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
                $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
                $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
                $bname = 'Internet Explorer';
                $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
                $bname = 'Mozilla Firefox';
                $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
                $bname = 'Google Chrome';
                $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
                $bname = 'Apple Safari';
                $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
                $bname = 'Opera';
                $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
                $bname = 'Netscape';
                $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
                // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
                //we will have two since we are not using 'other' argument yet
                //see if version is before or after the name
                if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                        $version = $matches['version'][0];
                } else {
                        $version = $matches['version'][1];
                }
        } else {
                $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
                $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
}

// now try it
$ua = getBrowser();

$yourPlatform = "Your Platform: " . $ua['platform'] . "<br/>";
$yourBrowser = "Your Browser: " . $ua['name'] . "<br/>";
$yourVersion = "Your Version: " . $ua['version'] . "<br/>";

if ($ua['name'] == "Mozilla Firefox") {
        echo "<link href='" . ASSETS_CSS . "/c/mozila.css' rel='stylesheet' type='text/css' >";
}
if ($ua['name'] == "Google Chrome") {
        echo "<link href='" . ASSETS_CSS . "/c/chrome.css' rel='stylesheet' type='text/css' >";
}
if ($ua['name'] == "Apple Safari") {
        echo "<link href='" . ASSETS_CSS . "/c/safari.css' rel='stylesheet' type='text/css' >";
}
if ($ua['name'] == "Opera") {
        echo "<link href='" . ASSETS_CSS . "/c/opera.css' rel='stylesheet' type='text/css' >";
}
if ($ua['name'] == "Internet Explorer" && $ua['version'] == "10.0") {
        echo "<link href='" . ASSETS_CSS . "/c/ie10.css' rel='stylesheet' type='text/css' >";
}
if ($ua['name'] == "Internet Explorer" && $ua['version'] == "9.0") {
        echo "<link href='" . ASSETS_CSS . "/c/ie9.css' rel='stylesheet' type='text/css' >";
}
if ($ua['name'] == "Internet Explorer" && $ua['version'] == "8.0") {
        echo "<link href='" . ASSETS_CSS . "/c/ie8.css' rel='stylesheet' type='text/css' >";
}
?>
<?php

//$objDB = new DB('read');
//$objJSON = new JSON();
//echo "test";exit;
$JSON = $objJSON->get_customer_profile();
$RS_customer_data = json_decode($JSON);

$arr1 = array();

if (isset($_SESSION['customer_id'])) {
        $custid = $_SESSION['customer_id'];
} else {
        $custid = "";
}
if (isset($_SESSION['customer_id']) && $_SESSION['customer_id'] != "") {

        $arr1 = file(WEB_PATH . '/process.php?getuserpointsbalance=yes&customer_id=' . $custid);
        if (trim($arr1[0]) == "") {
                unset($arr1[0]);
                $arr1 = array_values($arr1);
        }
        $all_json_str1 = $arr1[0];
        $json1 = json_decode($arr1[0]);
        $point_balance = $json1->point_balance;
}

$web_path = WEB_PATH . "/";
$pageURL = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

// 15 10 2013
$pageURLs = explode("?", $pageURL);
$pageURL = $pageURLs[0];
// 15 10 2013
//echo $web_path."index.php";
//echo $pageURL."index.php";

if ($pageURL == $web_path . "success-order.php" || $pageURL == $web_path . "terms.php" || $pageURL == $web_path . "press-release.php" || $pageURL == $web_path . "press-release-detail.php"
) {
        ?>
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/c/fancybox/jquery.fancybox.js"></script>
        <?php
} else {
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS ?>/c/fancybox/jquery.fancybox-buttons.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS ?>/c/fancybox/jquery.fancybox.css" media="screen" />

        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/c/auto-clear-and-current-location.js"></script>
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/c/fancybox/jquery_for_popup.js"></script>
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/c/fancybox/jquery.fancybox.js"></script> 
        <script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/jquery.raty.min.js"></script>	

        <!--- tooltip css --->
        <!--<script type="text/javascript" src="<?php echo WEB_PATH ?>/merchant/js/bootstrap.js"></script> -->
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/bootstrap.min.js"></script>
        <!--- tooltip css --->
        <?php
}
?>
<?php
$url1 = $_SERVER['REQUEST_URI'];
//echo $url1;
setCookie("get_url1", $url1, time() + 3600, "/");
?>


<!--
<script src="https://apis.google.com/js/client.js"></script>
<script src="https://apis.google.com/js/client:plusone.js"></script>
-->

<div class="header">
    <div class="my_main_div">
        <div class="ur-pro-logo">

            <div id="logo" class="logo">
                <a href="<?php echo WEB_PATH ?>">       
                    <img  alt="ScanFlip Logo" class="firstlogo" src="<?php echo ASSETS_IMG ?>/c/logo.png">
                    <img alt="ScanFlip Logo" class="secondlogo" src="<?php echo ASSETS_IMG ?>/c/panchlinelogo.png">
                </a>
            </div>
			<div class="header_set_loc_text">
			<p>
				<span class="wide">Set your location<span>
				<span class="narrow">Set location</span>
			</p>
			<p class="curent_loc_icon-arrow_down">&#x25BC; </p>
			</div>
            <div id="loginDiv" class="loginDiv" >
<?php
$web_path = WEB_PATH . "/";

$pageURL = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

if ($custid == "") {
        if ($pageURL != $web_path . "register.php") {
                ?>
                                <div class="login_regi">
                                    <a id="loginlink" href="javascript:void(0);">
                                        Sign In
                                    </a>
                                    <div id="lgn_popup">
                                        <a class="cta-button" href="<?php echo WEB_PATH ?>/register.php">Scanflip Customer</a>
                                        <a class="cta-button" href="<?php echo WEB_PATH ?>/merchant/register.php">Scanflip Merchant</a>
                                    </div>
                                </div>
                                <?
                                }
                                }
                                else
                                {

                                $array_where_cust['id'] = $custid;

                                $RS_cust = $objDB->Show("customer_user", $array_where_cust);

                                echo "<div class='usr-nam'>";

                                $str_noti_hdr="";
                                $str_noti_body="";
                                $str_noti_ftr="";

                                if($RS_cust->fields['notification_setting']==1)
                                {
                                // start for mydeals expiring today notification

                                $customer_id = $custid;
                                $expire_data_query = "SELECT t.id 'id',t.counter 'counter' FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=1 and t.is_read=0 and t.customer_id=".$customer_id;
                                $expire_data=$objDB->Conn->Execute($expire_data_query);
                                if($expire_data->RecordCount()>0)
                                {
                                while($Row = $expire_data->FetchRow())
                                {
                                if($Row['counter']==1)
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'>".$Row['counter']." campaign reserved by you is expiring today.</div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                else
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'>".$Row['counter']." campaigns reserved by you are expiring today.</div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                }
                                }

                                // end for mydeals expiring today notification

                                // start for new campaign notification

                                $customer_id = $custid;
                                $new_data_query = "SELECT t.id 'id',t.counter 'counter' FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=2 and t.is_read=0 and t.customer_id=".$customer_id;
                                $new_data=$objDB->Conn->Execute($new_data_query);
                                if($new_data->RecordCount()>0)
                                {
                                while($Row = $new_data->FetchRow())
                                {
                                if($Row['counter']==1)
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'>Today ".$Row['counter']." new campaign is launched by scanflip merchant near by you.</div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                else
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'>Today ".$Row['counter']." new campaigns were launched by scanflip merchants near by you.</div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                }
                                }

                                // end for new campaign notification

                                // start for pending review notification

                                $customer_id = $custid;
                                $pending_data_query = "SELECT t.id 'id',t.counter 'counter' FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=3 and t.is_read=0 and t.customer_id=".$customer_id;
                                $pending_data=$objDB->Conn->Execute($pending_data_query);
                                if($pending_data->RecordCount()>0)
                                {
                                while($Row = $pending_data->FetchRow())
                                {
                                if($Row['counter']==1)
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'><a href='".WEB_PATH."/myreviews.php'>You have ".$Row['counter']." pending review for your recent visit.</a></div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                else
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'><a href='".WEB_PATH."/myreviews.php'>You have ".$Row['counter']." pending reviews for your recent visits.</a></div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                }
                                }

                                // end for pending review notification

                                // start for earned recent visit notification

                                $customer_id = $custid;
                                $earned_redeem_data_query = "SELECT t.id 'id',t.counter 'counter' FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=4 and t.is_read=0 and t.customer_id=".$customer_id;
                                $earned_redeem_data=$objDB->Conn->Execute($earned_redeem_data_query);
                                if($earned_redeem_data->RecordCount()>0)
                                {
                                while($Row = $earned_redeem_data->FetchRow())
                                {
                                if($Row['counter']==1)
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'>You earned ".$Row['counter']." scanflip point from scanflip merchant in last 24 hour.</div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                else
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'>You earned ".$Row['counter']." scanflip points from scanflip merchants in last 24 hour.</div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                }
                                }

                                // end for earned recent visit notification

                                // start for earned new customer referral notification

                                $customer_id = $custid;
                                $earned_referral_data_query = "SELECT t.id 'id',t.counter 'counter' FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=5 and t.is_read=0 and t.customer_id=".$customer_id;
                                $earned_referral_data=$objDB->Conn->Execute($earned_referral_data_query);
                                if($earned_referral_data->RecordCount()>0)
                                {
                                while($Row = $earned_referral_data->FetchRow())
                                {
                                if($Row['counter']==1)
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'>You earned ".$Row['counter']." scanflip point for new customer referral in last 24 hour.</div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                else
                                {
                                $str_noti_body.="<div class='notificationsecond expire_notification_second'>
                                <div class='content'>You earned ".$Row['counter']." scanflip points for new customer referral in last 24 hour.</div>
                                <div class='clos expire_close_btn' notification_id='".$Row['id']."' > <a href='#'><img src='".WEB_PATH."/templates/images/clos.png' border='0px' alt='close'> </a></div>
                                </div>";
                                }
                                }
                                }

                                // end for earned new customer referral notification
                                }


                                echo "<div class='bell-d'>";

                                if(isset($_SESSION['facebook_usr_login']))
                                {

                                echo "<div class='usr_photo'><img  class='displayimg' src='". ASSETS_IMG."/c/usr_pic/".$RS_cust->fields['profile_pic']."'></div><div class='user-name-welcome'>".$client_msg['header']['label_Welcome']." ".ucfirst($RS_cust->fields['firstname'])."</div>";
                                }
                                else
                                {
                                $pic_var = explode("/", $RS_cust->fields['profile_pic']);
                                $twitter_pic_var=explode("/", $RS_cust->fields['profile_pic']);
                                //echo "<pre>";
                                //print_r($pic_var);
                                //echo "</pre>";

                                if(isset($pic_var[2]))
                                {
                                if($pic_var[2] == "graph.facebook.com" || $pic_var[2] == "fbcdn-profile-a.akamaihd.net")
                                {
                                echo "<div class='usr_photo'><img class='displayimg' src='".$RS_cust->fields['profile_pic']."'></div><div class='user-name-welcome'>".$client_msg['header']['label_Welcome']." ".ucfirst($RS_cust->fields['firstname'])."</div>";
                                }
                                //else if($twitter_pic_var[2] == "pbs.twimg.com")
                                else if($pic_var[2] == "pbs.twimg.com")
                                {
                                echo "<div class='usr_photo'><img class='displayimg' src='".$RS_cust->fields['profile_pic']."'></div><div class='user-name-welcome'>".$client_msg['header']['label_Welcome']." ".ucfirst($RS_cust->fields['firstname'])."</div>";
                                }
                                else
                                {
                                $pos = strpos($RS_cust->fields['profile_pic'], "googleusercontent.com");

                                if($pos === false)
                                {

                                if($RS_cust->fields['profile_pic'] != "")
                                {
                                echo "<div class='usr_photo'><img class='displayimg' src='".ASSETS_IMG."/c/usr_pic/".$RS_cust->fields['profile_pic']."'></div><div class='user-name-welcome'>".$client_msg['header']['label_Welcome']." ".ucfirst($RS_cust->fields['firstname'])."</div>"; 
                                }
                                else
                                {
                                echo "<div class='usr_photo'><img class='displayimg' src='". ASSETS_IMG."/c/default_small_user.jpg'></div><div class='user-name-welcome'>".$client_msg['header']['label_Welcome']." ".ucfirst($RS_cust->fields['firstname'])."</div>"; 
                                }
                                }
                                else
                                {
                                echo "<div class='usr_photo'><img class='displayimg' src='".$RS_cust->fields['profile_pic']."'></div><div class='user-name-welcome'>".$client_msg['header']['label_Welcome']." ".ucfirst($RS_cust->fields['firstname'])."</div>";
                                }
                                }
                                }
                                else
                                {
                                if($RS_cust->fields['profile_pic'] != "")
                                {
                                echo "<div class='usr_photo'><img class='displayimg' src='". ASSETS_IMG."/c/usr_pic/".$RS_cust->fields['profile_pic']."'></div><div class='user-name-welcome'>".$client_msg['header']['label_Welcome']." ".ucfirst($RS_cust->fields['firstname'])."</div>"; 
                                }
                                else
                                {
                                echo "<div class='usr_photo'><img class='displayimg' src='". ASSETS_IMG."/c/default_small_user.jpg'></div><div class='user-name-welcome'>".$client_msg['header']['label_Welcome']." ".ucfirst($RS_cust->fields['firstname'])."</div>"; 
                                }
                                }

                                }



                                if($str_noti_body!="")
                                {
                                echo "<div class='icon-bell_div'><i class='icon-bell'></i></div>";
                                }
                                else
                                {
                                echo "<div class='icon-bell_div'><i class='icon-bell-empty'></i></div>";
                                }
                                echo "<div class='notificationclass' style='display:none;' disp='0'>";
                                echo "<div class='notificationfirst'>Notifications</div>";
                                echo $str_noti_body;
                                echo "</div>"; // notificationclass div close

                                echo "<div></div>";

                                echo "</div>"; // bell-d div close



                                echo "<div class='pic-img'>";
                                //echo "<img style='padding-left:5px; border:0px;' src='./images/setting-user.png' id='setting_btn'/>";
                                echo "<span id='setting_btn'></span>";
                                echo "</div>"; // pic-img div close


                                // location page mlatitude mlongitude problem

                                if(isset($_COOKIE['mycurrent_lati']))
                                {
                                $mycurrent_lati_s = $_COOKIE['mycurrent_lati'];
                                }
                                else
                                {
                                $mycurrent_lati_s = "";
                                }
                                if(isset($_COOKIE['mycurrent_long']))
                                {
                                $mycurrent_long_s = $_COOKIE['mycurrent_long'];
                                }
                                else
                                {
                                $mycurrent_long_s = "";
                                }
                                if(isset($_COOKIE['myck']))
                                {
                                $myck=$_COOKIE['myck'];   
                                // echo "<br/>".$_COOKIE['mycurrent_lati']."=========".$_COOKIE['test']."<br/>";
                                if($myck=="true")
                                {
                                //  echo $_COOKIE['mycurrent_lati']."====".$_COOKIE['mycurrent_long'];
                                setcookie("myck","",time()-36000);
                                $mlatitude =  $mycurrent_lati_s;
                                $mlongitude = $mycurrent_long_s;

                                }
                                else
                                {
                                $mlatitude =  $mycurrent_lati_s;
                                $mlongitude = $mycurrent_long_s;
                                }
                                }
                                else
                                {
                                $mlatitude =  $mycurrent_lati_s;
                                $mlongitude = $mycurrent_long_s;
                                }
                                if(isset($_COOKIE['cat_remember']))
                                {
                                $cat_remember_s =  $_COOKIE['cat_remember'];
                                }
                                else
                                {
                                $cat_remember_s =0;
                                }
                                ?>
                                <div id="mysetting" style="display:none;" disp="0">

                                    <div id="mysetting_logout">
                                        <div class="logout-box">
                                            <div class="balance"> <?php echo $client_msg['header']['label_Point']; ?>
                                                <p><?php echo $point_balance; ?> </p>
                                            </div>
                                            <div class="sign-out">      
                                                <div class="out"> 
                                                    <a href="<?php echo WEB_PATH ?>/logout.php"><?php echo $client_msg['header']['label_Logout']; ?></a>	
                                                </div>

                                                <div class="my-account"> 
                                                    <a href="<?php echo WEB_PATH ?>/my-profile.php"><?php echo $client_msg['header']['label_Myprofile']; ?> </a>	
                                                </div>
                                            </div>
                                        </div>			
                                    </div>
                                </div>
                            </div>
                            <?
                            }
                            ?>
                        </div>
                    </div>

                    <style type="text/css">
                        .newclass body
                        {
                            overflow: hidden;
                        }
                    </style>
                    <div id="menuContainer" class="menuContainer">
                        <div class="imgbg_top">
                            <div class="header-corner"> 

                            </div>
                <?php
                
                if ($web_path == $pageURL || $web_path . "index.php" == $pageURL) {
                        ?> 
                                    <div id="activationCode" class="activationCode">
                                        <form action="<?php echo WEB_PATH ?>/process.php" method="GET" id="search_frm">
                                            <script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true&.js"></script>
                                            <script type='text/javascript' src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>
                                            <input class="acform" type="text"  value="Enter Activation Code" name="activation_code" id="activation_code" onclick="clickclear(this, 'Enter Activation Code')" onblur="clickrecall(this, 'Enter Activation Code')" />

                        <?php if (isset($_SESSION['customer_id'])) { ?>
                                                    <input type="submit" name="btnActivationCode" id="btnActivationCode" value="Activate My Offer" class="fancybox_login" />
                        <?php } else { ?>
                                                    <input type="submit" name="btnActivationCode_login" href="<?php echo WEB_PATH ?>/login.php?page=&code=12345" class="fancybox_login" id="btnActivationCode_login" value="Activate My Offer" class="activatemyoffer" />
                        <?php } ?>
                                            <input type="hidden" style="width:370px;" value="" name="txt_activation_code_temp" id="txt_activation_code_temp" />
                                            <input type="hidden" name="hp" id="hp" value="yes" />
                                            <input type="submit" class="search1_frm" name="search1_frm" value="Browse Scanflip" id="search1_frm" />	 
                                        </form>
                                    </div>
                        <?php
                }
                ?>
                        </div>


                <?php
                //echo $web_path;
                //echo $pageURL;
                if ($web_path != $pageURL && $web_path . "index.php" != $pageURL) {
                        ?>
                                <div id="registeredMenu" >
                        <?php
                        if ($pageURL != $web_path . "search-deal.php") {
                                
                        }

                        if (isset($_COOKIE['mycurrent_lati'])) {
                                $mycurrent_lati_s = $_COOKIE['mycurrent_lati'];
                        } else {
                                $mycurrent_lati_s = "";
                        }
                        if (isset($_COOKIE['mycurrent_long'])) {
                                $mycurrent_long_s = $_COOKIE['mycurrent_long'];
                        } else {
                                $mycurrent_long_s = "";
                        }
                        if (isset($_COOKIE['myck'])) {
                                $myck = $_COOKIE['myck'];

                                if ($myck == "true") {

                                        setcookie("myck", "", time() - 36000);
                                        $mlatitude = $mycurrent_lati_s;
                                        $mlongitude = $mycurrent_long_s;
                                } else {
                                        $mlatitude = $mycurrent_lati_s;
                                        $mlongitude = $mycurrent_long_s;
                                }
                        } else {
                                $mlatitude = $mycurrent_lati_s;
                                $mlongitude = $mycurrent_long_s;
                        }
                        if (isset($_COOKIE['cat_remember'])) {
                                $cat_remember_s = $_COOKIE['cat_remember'];
                        } else {
                                $cat_remember_s = 0;
                        }

                        if ($custid != "") {
                                ?>
                                            <div class="mainregisterdiv">
											<div class="mainregisterdiv">
														<div class="scn_main_flt_blk">
														<div class="main_flt_blk">Campaigns</div>
														<div class="mid_span">|</div>
														<div class="main_flt_blk">Loyalty cards</div>
											</div>
                                                <div id="regiMenuBar" align="right" class="registerMenuBar" >
                                                    <div class="right_manu">
                                            <?php
                                            if ($pageURL == $web_path . "my-deals.php") {
                                                    ?>
                                                                <a class="active" href="javascript:void(0);" target="_top" title="Saved Offers"><?php echo $client_msg['header']['label_My_Offers']; ?></a>
                                                    <?php
                                            } else if ($pageURL == $web_path . "mymerchants.php") {
                                                    ?>
                                                                <a class="active" href="javascript:void(0);" target="_top" title="Subscribed Merchants"><?php echo $client_msg['header']['label_My_Merchant']; ?></a>	
                                                    <?php
                                            }
                                            ?>

                                                        <a id="right_manu_button" href="javascript:void(0);"><span class="pictogram"></span></a>
                                                        <ul class="right_sub_manu" style="display:none;">
                                                            <li><a id="browsedeals" href="<?php echo WEB_PATH ?>/search-deal.php" target="_top" title="Browse Scanflip"><?php echo "Browse Scanflip"; ?></a></li>
                                                            <li><a href="<?php echo WEB_PATH ?>/my-deals.php" target="_top" title="Saved Offers"><?php echo $client_msg['header']['label_My_Offers']; ?></a></li>
                                                            <li><a href="<?php echo WEB_PATH ?>/mymerchants.php" target="_top" title="Subscribed Merchants"><?php echo $client_msg['header']['label_My_Merchant']; ?></a></li>
                                                            <li><a href="<?php echo WEB_PATH ?>/shop-redeem.php" target="_top"><?php echo $client_msg['header']['label_Shop']; ?></a></li>
                                                        </ul>

                                                    </div>

                                                </div>	


                                            </div>

                                            <?php
                                    } else {
                                            /*
                                              echo $pageURL;
                                              echo "</br>";
                                              echo $_SERVER["REQUEST_URI"];
                                              echo "</br>";
                                              echo basename($_SERVER['PHP_SELF']);
                                             */
                                             //echo basename($_SERVER['REQUEST_URI']);
                                              $fle_nm = basename($_SERVER['REQUEST_URI']);
                                              $fle_nm_arr = explode("?", $fle_nm);  
                                              //echo  $fle_nm_arr[0];
                                            if ($pageURL != $web_path . "register.php" && $pageURL != $web_path . "terms.php" && $pageURL != $web_path . "privacy-assist.php" && $pageURL != $web_path . "press-release.php" && $pageURL != $web_path . "contact-us.php" && $fle_nm_arr[0] != "press-release-detail.php") {
                                                    ?>
                                                    <div class="mainregisterdiv">
														<div class="scn_main_flt_blk">
														<div class="main_flt_blk">Campaigns</div>
														<div class="mid_span">|</div>
														<div class="main_flt_blk">Loyalty cards</div>
														</div>
                                                        <div class="right_manu">
                                                            <a id="right_manu_button" href="javascript:void(0);"><span class="pictogram"></span></a>
                                                            <ul class="right_sub_manu" style="display:none;">
                                                                <li><a id="browsedeals" href="<?php echo WEB_PATH ?>/search-deal.php" target="_top" title="Browse Scanflip"><?php echo "Browse Scanflip"; ?></a></li>
                                                                <li><a target="_top" id="mydeals" href="<?php echo WEB_PATH ?>/login.php?page=my-deals.php" mylink="<?php echo WEB_PATH ?>/my-deals.php" class="fancybox"><?php echo $client_msg['header']['label_My_Offers'] ?></a></li>
                                                                <li><a target="_top" id="mymerchants" href="<?php echo WEB_PATH ?>/login.php?page=mymerchants.php" mylink="<?php echo WEB_PATH ?>/mymerchants.php" class="fancybox"><?php echo $client_msg['header']['label_My_Merchant'] ?></a></li>
                                                                <li><a target="_top" id="mypoints" href="<?php echo WEB_PATH ?>/login.php?page=shop-redeem.php" mylink="<?php echo WEB_PATH ?>/shop-redeem.php" class="fancybox"><?php echo $client_msg['header']['label_Shop'] ?></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <!--
                                                    <div id="regiMenuBar" align="right" class="registerMenuBar" style="display:none;">
                                                            <a id="mydeals" href="<?php echo WEB_PATH ?>/login.php?page=my-deals.php" target="_top" mylink="<?php echo WEB_PATH ?>/my-deals.php" class="fancybox" ><?php echo $client_msg['header']['label_My_Offers'] ?></a>  
                                                            <a id="mymerchants" href="<?php echo WEB_PATH ?>/login.php?page=mymerchants.php" target="_top" mylink="<?php echo WEB_PATH ?>/mymerchants.php" class="fancybox" ><?php echo $client_msg['header']['label_My_Merchant'] ?></a>
                                                            <a id="mypoints" href="<?php echo WEB_PATH ?>/login.php?page=#" target="_top" mylink="<?php echo WEB_PATH ?>/#" class="fancybox"><?php echo $client_msg['header']['label_Shop'] ?></a>                            
                                                    </div>
                                                    -->		
                                                                <?php
                                                        }
                                                }
                                                echo "</div>" // registeredMenu div close
                                                ?>

                                                <?php
                                        }
                                        ?>
                            <!--end menuContainer--></div>
                                        <?php
                                        
                                        require_once(CUST_LAYOUT . "/zipcodediv.php");
                                        ?>
                        <!--end of my_main_div--></div>
                    <!--end of header--></div>

                <div style="display:none"id="activation_div"> 
                    <div align="center" id="div_msg">

                    </div>
                    <div id="div_locations">
                    </div>

                    <div><br />

                        <input type="text" style="width:370px;" value="" name="txt_activation_code" id="txt_activation_code" />

                        <br />
                        <input type="submit" name="activatedeal" id="activatedeal" value="<?php echo $language_msg["activation"]["activation_code"]; ?>" />  
                    </div>
                </div>
                            <?php
                            if (isset($_SESSION['customer_id'])) {
                                    $JSON_customer_profile = $objJSON->get_customer_profile();
                                    $RS1_customer_profile = json_decode($JSON_customer_profile);
                                    ?>
                        <div class="updateprofile1" id="updateprofile1" style="display:none;padding:11px;" >
                            <div class="update_profile_detail" style="margin-left: 26px;margin-bottom:5px;margin-top: 23px;" >
                                <div id="modal-login" style="height:310px;text-align:left;width:386px;line-height:17px">
                                    <h2 id="modal-login-title"><?php echo $client_msg['login_register']['label_Update_Profile']; ?></h2>

                                    <div class="unit100" style="height:30px;">
                                    </div>
                                    <div id="form_login">
                                        <form class="form_vertical" action="<?php echo WEB_PATH; ?>/process.php" method="post"  id="login_frm">

                                            <label for="email-modal"><?php echo "* " . $client_msg['login_register']['label_Gender']; ?></label>
                                            <select name="gender" id="gender" class="genderclass"   style="padding:0.231em;margin:0 0 0.154em;" class="js-focus"  >
                                                <option></option>
                                                <option value="1" <?php if ($RS1_customer_profile[0]->gender == 1) {
                                echo "selected";
                        } ?> >Male</option>
                                                <option value="2" <?php if ($RS1_customer_profile[0]->gender == 2) {
                                echo "selected";
                        } ?> >Female</option>
                                            </select>
                                            <div class="err_gender" style="color:red;height: 18px"></div>

                                            <label for="dob"><?php echo "* " . $client_msg['login_register']['label_Date_Of_Birth']; ?></label>
                                            <select name="dob_month" id="dob_month" class="dateofmonth"  style="margin:0 0 0.154em;">
                                                <option></option>
                                                <?


                                                for($i=1; $i<=12; $i++){	
                                                ?>
                                                <option value="<?php echo $i ?>" <? if($RS1_customer_profile[0]->dob_month == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
                                                <?
                                                }
                                                ?>
                                            </select>
                                            -
                                            <select name="dob_day" id="dob_day" class="dateofday"  style="margin:0 0 0.154em;">
                                                <option></option>
                                                <?
                                                for($i=1; $i<=31; $i++){	
                                                ?>
                                                <option value="<?php echo $i ?>" <? if($RS1_customer_profile[0]->dob_day == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
                                                <?
                                                }
                                                ?>
                                            </select>
                                            - 
                                            <select name="dob_year" id="dob_year" class="dateofyear"  style="margin:0 0 0.154em;">
                                                <option ></option>
                                                <?
                                                for($i=date("Y")-60; $i<=date("Y"); $i++){	
                                                ?>
                                                <option value="<?php echo $i ?>" <? if($RS1_customer_profile[0]->dob_year == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
                                                <?
                                                }
                                                ?>
                                            </select>
                                            <div class="err_dob" style="color:red;height: 18px"></div>

                                            <label for="country"><?php echo "* " . $client_msg['login_register']['label_Country']; ?></label>
                                            <select name="country" id="country"  class="countryclass" style="margin:0 0 0.154em;">
                                                <option ></option>
                                                <option value="USA" <? if($RS1_customer_profile[0]->country == "USA") echo "selected";?> country_code="US">USA</option>
                                                <option value="Canada" <? if($RS1_customer_profile[0]->country == "Canada") echo "selected";?>  country_code="CA">Canada</option>

                                            </select>
                                            <div class="err_country" style="color:red;height: 18px"></div>

                                            <label for="postalcode"><?php echo "* " . $client_msg['login_register']['label_Postal_Code']; ?> </label>
                                            <input type="text" maxlength="15" class="postalcodeclass"  name="postalcode" id="postalcode" class="js-focus" style="padding:0.231em;margin:0 0 0.154em;" value="<?php echo $RS1_customer_profile[0]->postalcode ?>">
                        <!--                                   <input type="text" name="postalcode" id="postalcode" style="width:120px;" value=""  class="unit100" style="padding:0.231em;" >-->


                                            <div class="err_postalcode" style="color:red;height: 18px"></div>



                                            <div>

                                                <p class="actions" style="" align="center">
                                                    <input type="button" id="btnupdateprofile" name="btnupdateprofile" value="Save" onClick="">
                                                    <input type="button" class="btnsharecancelbutton" value="Cancel" id="btncancelprofile"  />
                                                </p>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if ($pageURL == $web_path . "my-profile.php") {
                                
                        } else {
                                ?>
                                <div id="notprofilesetid" class="notprofilesetid" style="display:none">
                                    <form id="login_frm" method="post" action="<?php echo WEB_PATH; ?>/process.php" class="form_vertical">

                                        <label for="email-modal">* First Name :</label>
                                        <input type="text" value="<?php echo $RS_customer_data[0]->firstname; ?>" style="padding:0.231em;margin:0 0 0.154em;" id="firstname" name="firstname" class="firstnameclass" maxlength="15">
                                        <div class="err_firstname" style="color:red;height: 18px"></div>
                                        <label for="email-modal">* Last Name :</label>
                                        <input type="text" value="<?php echo $RS_customer_data[0]->lastname; ?>" style="padding:0.231em;margin:0 0 0.154em;" id="lastname" name="lastname" class="lastnameclass" maxlength="15">	
                                        <div class="err_lastname" style="color:red;height: 18px"></div>
                                        <label for="email-modal">* Gender :</label>
                                        <select style="padding:0.231em;margin:0 0 0.154em;" class="genderclass" id="gender" name="gender">
                                            <option></option>
                                            <option value="1" <?php if ($RS_customer_data[0]->gender == 1) echo "selected"; ?>>Male</option>
                                            <option value="2" <?php if ($RS_customer_data[0]->gender == 2) echo "selected"; ?>>Female</option>
                                        </select>
                                        <div style="color:red;height: 18px" class="err_gender"></div>

                                        <label for="dob">* Date of Birth :</label>
                                        <select style="margin:0 0 0.154em;" class="dateofmonth" id="dob_month" name="dob_month">
                                            <option></option>
                                            <?
                                            for($i=1; $i<=12; $i++){	
                                            ?>
                                            <option value="<?php echo $i ?>" <? if($RS_customer_data[0]->dob_month == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
                                            <?
                                            }
                                            ?>
                                        </select>
                                        -
                                        <select style="margin:0 0 0.154em;" class="dateofday" id="dob_day" name="dob_day">
                                            <option></option>
                                            <?
                                            for($i=1; $i<=31; $i++){	
                                            ?>
                                            <option value="<?php echo $i ?>" <? if($RS_customer_data[0]->dob_day == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
                                            <?
                                            }
                                            ?>
                                        </select>
                                        - 
                                        <select style="margin:0 0 0.154em;" class="dateofyear" id="dob_year" name="dob_year">
                                            <option></option>
                                            <?
                                            for($i=date("Y")-60; $i<=date("Y"); $i++){	
                                            ?>
                                            <option value="<?php echo $i ?>" <? if($RS_customer_data[0]->dob_year == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
                                            <?
                                            }
                                            ?>
                                        </select>
                                        <div style="color:red;height: 18px" class="err_dob"></div>

                                        <label for="country">* Country :</label>
                                        <select style="margin:0 0 0.154em;" class="countryclass" id="country" name="country">
                                            <option></option>
                                            <option country_code="US" value="USA" <?php if ($RS_customer_data[0]->country == "USA") echo "selected"; ?>>USA</option>
                                            <option country_code="CA" value="Canada" <?php if ($RS_customer_data[0]->country == "Canada") echo "selected"; ?>>Canada</option>

                                        </select>
                                        <div style="color:red;height: 18px" class="err_country"></div>
                                        <label for="state" style="float:left">* State :</label>
                                        <select name="state" id="state" class="" style="display:block">


                                            <option></option>
                                <?php
                                if ($RS_customer_data[0]->country == "USA") {
                                        ?>
                                                    <option value="AK" <? if($RS_customer_data[0]->state == "AK") echo "selected";?> >AK</option>
                                                    <option value="AL" <? if($RS_customer_data[0]->state == "AL") echo "selected";?>>AL</option>
                                                    <option value="AP" <? if($RS_customer_data[0]->state == "AP") echo "selected";?>>AP</option>
                                                    <option value="AR" <? if($RS_customer_data[0]->state == "AR") echo "selected";?>>AR</option>
                                                    <option value="AS" <? if($RS_customer_data[0]->state == "AS") echo "selected";?>>AS</option>
                                                    <option value="AZ" <? if($RS_customer_data[0]->state == "AZ") echo "selected";?>>AZ</option>
                                                    <option value="CA" <? if($RS_customer_data[0]->state == "CA") echo "selected";?>>CA</option>
                                                    <option value="CO" <? if($RS_customer_data[0]->state == "CO") echo "selected";?>>CO</option>
                                                    <option value="CT" <? if($RS_customer_data[0]->state == "CT") echo "selected";?>>CT</option>
                                                    <option value="DC" <? if($RS_customer_data[0]->state == "DC") echo "selected";?>>DC</option>
                                                    <option value="DE" <? if($RS_customer_data[0]->state == "DE") echo "selected";?>>DE</option>
                                                    <option value="FL" <? if($RS_customer_data[0]->state == "FL") echo "selected";?>>FL</option>
                                                    <option value="FM" <? if($RS_customer_data[0]->state == "FM") echo "selected";?>>FM</option>
                                                    <option value="GA" <? if($RS_customer_data[0]->state == "GA") echo "selected";?>>GA</option>
                                                    <option value="GS" <? if($RS_customer_data[0]->state == "GS") echo "selected";?>>GS</option>
                                                    <option value="GU" <? if($RS_customer_data[0]->state == "GU") echo "selected";?>>GU</option>
                                                    <option value="HI" <? if($RS_customer_data[0]->state == "HI") echo "selected";?>>HI</option>
                                                    <option value="IA" <? if($RS_customer_data[0]->state == "IA") echo "selected";?>>IA</option>
                                                    <option value="ID" <? if($RS_customer_data[0]->state == "ID") echo "selected";?>>ID</option>
                                                    <option value="IL" <? if($RS_customer_data[0]->state == "IL") echo "selected";?>>IL</option>
                                                    <option value="IN" <? if($RS_customer_data[0]->state == "IN") echo "selected";?>>IN</option>
                                                    <option value="KS" <? if($RS_customer_data[0]->state == "KS") echo "selected";?>>KS</option>
                                                    <option value="KY" <? if($RS_customer_data[0]->state == "KY") echo "selected";?>>KY</option>
                                                    <option value="LA" <? if($RS_customer_data[0]->state == "LA") echo "selected";?>>LA</option>
                                                    <option value="MA" <? if($RS_customer_data[0]->state == "MA") echo "selected";?>>MA</option>
                                                    <option value="MD" <? if($RS_customer_data[0]->state == "MD") echo "selected";?>>MD</option>
                                                    <option value="ME" <? if($RS_customer_data[0]->state == "ME") echo "selected";?>>ME</option>
                                                    <option value="MH" <? if($RS_customer_data[0]->state == "MH") echo "selected";?>>MH</option>
                                                    <option value="MI" <? if($RS_customer_data[0]->state == "MI") echo "selected";?>>MI</option>
                                                    <option value="MN" <? if($RS_customer_data[0]->state == "MN") echo "selected";?>>MN</option>
                                                    <option value="MO" <? if($RS_customer_data[0]->state == "MO") echo "selected";?>>MO</option>
                                                    <option value="MP" <? if($RS_customer_data[0]->state == "MP") echo "selected";?>>MP</option>
                                                    <option value="MS" <? if($RS_customer_data[0]->state == "MS") echo "selected";?>>MS</option>
                                                    <option value="MT" <? if($RS_customer_data[0]->state == "MT") echo "selected";?>>MT</option>
                                                    <option value="NC" <? if($RS_customer_data[0]->state == "NC") echo "selected";?>>NC</option>
                                                    <option value="ND" <? if($RS_customer_data[0]->state == "ND") echo "selected";?>>ND</option>
                                                    <option value="NE" <? if($RS_customer_data[0]->state == "NE") echo "selected";?>>NE</option>
                                                    <option value="NH" <? if($RS_customer_data[0]->state == "NH") echo "selected";?>>NH</option>
                                                    <option value="NJ" <? if($RS_customer_data[0]->state == "NJ") echo "selected";?>>NJ</option>
                                                    <option value="NM" <? if($RS_customer_data[0]->state == "NM") echo "selected";?>>NM</option>
                                                    <option value="NV" <? if($RS_customer_data[0]->state == "NV") echo "selected";?>>NV</option>
                                                    <option value="NY" <? if($RS_customer_data[0]->state == "NY") echo "selected";?>>NY</option>
                                                    <option value="OH" <? if($RS_customer_data[0]->state == "OH") echo "selected";?>>OH</option>
                                                    <option value="OK" <? if($RS_customer_data[0]->state == "OK") echo "selected";?>>OK</option>
                                                    <option value="OR" <? if($RS_customer_data[0]->state == "OR") echo "selected";?>>OR</option>
                                                    <option value="PA" <? if($RS_customer_data[0]->state == "PA") echo "selected";?>>PA</option>
                                                    <option value="PR" <? if($RS_customer_data[0]->state == "PR") echo "selected";?>>PR</option>
                                                    <option value="PW" <? if($RS_customer_data[0]->state == "PW") echo "selected";?>>PW</option>
                                                    <option value="RI" <? if($RS_customer_data[0]->state == "RI") echo "selected";?>>RI</option>
                                                    <option value="SC" <? if($RS_customer_data[0]->state == "SC") echo "selected";?>>SC</option>
                                                    <option value="SD" <? if($RS_customer_data[0]->state == "SD") echo "selected";?>>SD</option>
                                                    <option value="TN" <? if($RS_customer_data[0]->state == "TN") echo "selected";?>>TN</option>
                                                    <option value="TX" <? if($RS_customer_data[0]->state == "TX") echo "selected";?>>TX</option>
                                                    <option value="UT" <? if($RS_customer_data[0]->state == "UT") echo "selected";?>>UT</option>
                                                    <option value="VA" <? if($RS_customer_data[0]->state == "VA") echo "selected";?>>VA</option>
                                                    <option value="VI" <? if($RS_customer_data[0]->state == "VI") echo "selected";?>>VI</option>
                                                    <option value="VT" <? if($RS_customer_data[0]->state == "VT") echo "selected";?>>VT</option>
                                                    <option value="WA" <? if($RS_customer_data[0]->state == "WA") echo "selected";?>>WA</option>
                                                    <option value="WI" <? if($RS_customer_data[0]->state == "WI") echo "selected";?>>WI</option>
                                                    <option value="WV" <? if($RS_customer_data[0]->state == "WV") echo "selected";?>>WV</option>
                                                    <option value="WY" <? if($RS_customer_data[0]->state == "WY") echo "selected";?>>WY</option>
                                            <?php } else if ($RS_customer_data[0]->country == "Canada") { ?>
                                                    <option value='AB' <? if($RS_customer_data[0]->state == "AB") echo "selected";?>>AB</option>
                                                    <option value='BC' <? if($RS_customer_data[0]->state == "BC") echo "selected";?>>BC</option>
                                                    <option value='LB' <? if($RS_customer_data[0]->state == "LB") echo "selected";?>>LB</option>
                                                    <option value='MB' <? if($RS_customer_data[0]->state == "MB") echo "selected";?>>MB</option>
                                                    <option value='NB' <? if($RS_customer_data[0]->state == "NB") echo "selected";?>>NB</option>
                                                    <option value='NF' <? if($RS_customer_data[0]->state == "NF") echo "selected";?>>NF</option>
                                                    <option value='NS' <? if($RS_customer_data[0]->state == "NS") echo "selected";?>>NS</option>
                                                    <option value='NT' <? if($RS_customer_data[0]->state == "NT") echo "selected";?>>NT</option>
                                                    <option value='NU' <? if($RS_customer_data[0]->state == "NU") echo "selected";?>>NU</option>
                                                    <option value='ON' <? if($RS_customer_data[0]->state == "ON") echo "selected";?>>ON</option>
                                                    <option value='PE' <? if($RS_customer_data[0]->state == "PE") echo "selected";?>>PE</option>
                                                    <option value='PQ' <? if($RS_customer_data[0]->state == "PQ") echo "selected";?>>PQ</option>
                                                    <option value='QB' <? if($RS_customer_data[0]->state == "QB") echo "selected";?>>QB</option>
                                                    <option value='QC' <? if($RS_customer_data[0]->state == "QC") echo "selected";?>>QC</option>
                                                    <option value='SK' <? if($RS_customer_data[0]->state == "SK") echo "selected";?>>SK</option>
                                                    <option value='YT' <? if($RS_customer_data[0]->state == "YT") echo "selected";?>>YT</option>
                                <?php } else {
                                        ?>
                                                    <option value="AK" <? if($RS_customer_data[0]->state == "AK") echo "selected";?> >AK</option>
                                                    <option value="AL" <? if($RS_customer_data[0]->state == "AL") echo "selected";?>>AL</option>
                                                    <option value="AP" <? if($RS_customer_data[0]->state == "AP") echo "selected";?>>AP</option>
                                                    <option value="AR" <? if($RS_customer_data[0]->state == "AR") echo "selected";?>>AR</option>
                                                    <option value="AS" <? if($RS_customer_data[0]->state == "AS") echo "selected";?>>AS</option>
                                                    <option value="AZ" <? if($RS_customer_data[0]->state == "AZ") echo "selected";?>>AZ</option>
                                                    <option value="CA" <? if($RS_customer_data[0]->state == "CA") echo "selected";?>>CA</option>
                                                    <option value="CO" <? if($RS_customer_data[0]->state == "CO") echo "selected";?>>CO</option>
                                                    <option value="CT" <? if($RS_customer_data[0]->state == "CT") echo "selected";?>>CT</option>
                                                    <option value="DC" <? if($RS_customer_data[0]->state == "DC") echo "selected";?>>DC</option>
                                                    <option value="DE" <? if($RS_customer_data[0]->state == "DE") echo "selected";?>>DE</option>
                                                    <option value="FL" <? if($RS_customer_data[0]->state == "FL") echo "selected";?>>FL</option>
                                                    <option value="FM" <? if($RS_customer_data[0]->state == "FM") echo "selected";?>>FM</option>
                                                    <option value="GA" <? if($RS_customer_data[0]->state == "GA") echo "selected";?>>GA</option>
                                                    <option value="GS" <? if($RS_customer_data[0]->state == "GS") echo "selected";?>>GS</option>
                                                    <option value="GU" <? if($RS_customer_data[0]->state == "GU") echo "selected";?>>GU</option>
                                                    <option value="HI" <? if($RS_customer_data[0]->state == "HI") echo "selected";?>>HI</option>
                                                    <option value="IA" <? if($RS_customer_data[0]->state == "IA") echo "selected";?>>IA</option>
                                                    <option value="ID" <? if($RS_customer_data[0]->state == "ID") echo "selected";?>>ID</option>
                                                    <option value="IL" <? if($RS_customer_data[0]->state == "IL") echo "selected";?>>IL</option>
                                                    <option value="IN" <? if($RS_customer_data[0]->state == "IN") echo "selected";?>>IN</option>
                                                    <option value="KS" <? if($RS_customer_data[0]->state == "KS") echo "selected";?>>KS</option>
                                                    <option value="KY" <? if($RS_customer_data[0]->state == "KY") echo "selected";?>>KY</option>
                                                    <option value="LA" <? if($RS_customer_data[0]->state == "LA") echo "selected";?>>LA</option>
                                                    <option value="MA" <? if($RS_customer_data[0]->state == "MA") echo "selected";?>>MA</option>
                                                    <option value="MD" <? if($RS_customer_data[0]->state == "MD") echo "selected";?>>MD</option>
                                                    <option value="ME" <? if($RS_customer_data[0]->state == "ME") echo "selected";?>>ME</option>
                                                    <option value="MH" <? if($RS_customer_data[0]->state == "MH") echo "selected";?>>MH</option>
                                                    <option value="MI" <? if($RS_customer_data[0]->state == "MI") echo "selected";?>>MI</option>
                                                    <option value="MN" <? if($RS_customer_data[0]->state == "MN") echo "selected";?>>MN</option>
                                                    <option value="MO" <? if($RS_customer_data[0]->state == "MO") echo "selected";?>>MO</option>
                                                    <option value="MP" <? if($RS_customer_data[0]->state == "MP") echo "selected";?>>MP</option>
                                                    <option value="MS" <? if($RS_customer_data[0]->state == "MS") echo "selected";?>>MS</option>
                                                    <option value="MT" <? if($RS_customer_data[0]->state == "MT") echo "selected";?>>MT</option>
                                                    <option value="NC" <? if($RS_customer_data[0]->state == "NC") echo "selected";?>>NC</option>
                                                    <option value="ND" <? if($RS_customer_data[0]->state == "ND") echo "selected";?>>ND</option>
                                                    <option value="NE" <? if($RS_customer_data[0]->state == "NE") echo "selected";?>>NE</option>
                                                    <option value="NH" <? if($RS_customer_data[0]->state == "NH") echo "selected";?>>NH</option>
                                                    <option value="NJ" <? if($RS_customer_data[0]->state == "NJ") echo "selected";?>>NJ</option>
                                                    <option value="NM" <? if($RS_customer_data[0]->state == "NM") echo "selected";?>>NM</option>
                                                    <option value="NV" <? if($RS_customer_data[0]->state == "NV") echo "selected";?>>NV</option>
                                                    <option value="NY" <? if($RS_customer_data[0]->state == "NY") echo "selected";?>>NY</option>
                                                    <option value="OH" <? if($RS_customer_data[0]->state == "OH") echo "selected";?>>OH</option>
                                                    <option value="OK" <? if($RS_customer_data[0]->state == "OK") echo "selected";?>>OK</option>
                                                    <option value="OR" <? if($RS_customer_data[0]->state == "OR") echo "selected";?>>OR</option>
                                                    <option value="PA" <? if($RS_customer_data[0]->state == "PA") echo "selected";?>>PA</option>
                                                    <option value="PR" <? if($RS_customer_data[0]->state == "PR") echo "selected";?>>PR</option>
                                                    <option value="PW" <? if($RS_customer_data[0]->state == "PW") echo "selected";?>>PW</option>
                                                    <option value="RI" <? if($RS_customer_data[0]->state == "RI") echo "selected";?>>RI</option>
                                                    <option value="SC" <? if($RS_customer_data[0]->state == "SC") echo "selected";?>>SC</option>
                                                    <option value="SD" <? if($RS_customer_data[0]->state == "SD") echo "selected";?>>SD</option>
                                                    <option value="TN" <? if($RS_customer_data[0]->state == "TN") echo "selected";?>>TN</option>
                                                    <option value="TX" <? if($RS_customer_data[0]->state == "TX") echo "selected";?>>TX</option>
                                                    <option value="UT" <? if($RS_customer_data[0]->state == "UT") echo "selected";?>>UT</option>
                                                    <option value="VA" <? if($RS_customer_data[0]->state == "VA") echo "selected";?>>VA</option>
                                                    <option value="VI" <? if($RS_customer_data[0]->state == "VI") echo "selected";?>>VI</option>
                                                    <option value="VT" <? if($RS_customer_data[0]->state == "VT") echo "selected";?>>VT</option>
                                                    <option value="WA" <? if($RS_customer_data[0]->state == "WA") echo "selected";?>>WA</option>
                                                    <option value="WI" <? if($RS_customer_data[0]->state == "WI") echo "selected";?>>WI</option>
                                                    <option value="WV" <? if($RS_customer_data[0]->state == "WV") echo "selected";?>>WV</option>
                                                    <option value="WY" <? if($RS_customer_data[0]->state == "WY") echo "selected";?>>WY</option>
                                            <?php }
                                            ?>
                                        </select>

                                        <div style="color:red;height: 18px" class="err_state"></div>

                                        <label for="state">* City :</label>
                                        <input type="text" name="city" id="city" style="width:120px;" value="<?php echo $RS_customer_data[0]->city; ?>">
                                        <div style="color:red;height: 18px" class="err_city"></div>


                                        <label for="postalcode">* Postal Code : </label>
                                        <input type="text" value="<?php echo $RS_customer_data[0]->postalcode; ?>" style="padding:0.231em;margin:0 0 0.154em;" id="postalcode" name="postalcode" class="postalcodeclass" maxlength="15">
                                <!--                                   <input type="text" name="postalcode" id="postalcode" style="width:120px;" value=""  class="unit100" style="padding:0.231em;" >-->


                                        <div style="color:red;height: 18px" class="err_postalcode"></div>



                                        <div>

                                            <p align="center" style="" class="actions">
                                                <input type="button" onclick="" value="Save" name="btnupdateprofile" id="btnupdateprofile">
                                                <input type="button" id="btn_cancel_forgot" value="Cancel" class="btnsharecancelbutton">
                                            </p>
                                        </div>
                                    </form>
                                </div>

                                <?php
                        }
                }
                ?>
                <script type="text/javascript">
                        if (typeof String.prototype.trim !== 'function') {
                            String.prototype.trim = function () {
                                return this.replace(/^\s+|\s+$/g, '');
                            }
                        }

                        /*
                         jQuery('#right_manu_button').mouseenter(function(){
                         jQuery('.right_sub_manu').show();
                         });
                         jQuery('#right_manu_button').mouseleave(function(){
                         jQuery('.right_sub_manu').hide();
                         });
                         */

                        $("#setting_btn").click(function () {

                            if ($("#mysetting").attr("disp") == 0)
                            {
                                $("#mysetting").attr("disp", "1");
                                $("#mysetting").show();
                            }
                            else
                            {
                                $("#mysetting").attr("disp", "0");
                                $("#mysetting").hide();
                            }

                        });

                        $('.fancybox_login').fancybox({
                            href: this.href,
                            //href: $(val).attr('mypopupid'),
                            //content:#myDivID_3,
                            width: 400,
                            height: 340,
                            type: 'iframe',
                            openEffect: 'elastic',
                            openSpeed: 300,
                            scrolling: 'no',
                            closeEffect: 'elastic',
                            closeSpeed: 300,
                            beforeShow: function () {
                                //alert(this.href);
                                //alert(jQuery("#activation_code").val());
                                setCookie("code", jQuery("#activation_code").val(), 365);
                                $(".fancybox-inner").addClass("Class_fancy_ie_login");
                            },
                            helpers: {
                                overlay: {
                                    opacity: 0.3
                                } // overlay
                            }
                            // helpers
                        });
                        jQuery('.fancybox').fancybox({
                            href: this.href,
                            //href: $(val).attr('mypopupid'),
                            //content:#myDivID_3,
                            width: 400,
                            height: 320,
                            type: 'iframe',
                            openEffect: 'elastic',
                            openSpeed: 300,
                            scrolling: 'no',
                            closeEffect: 'elastic',
                            closeSpeed: 300,
                            beforeShow: function () {
                                //alert("hi");
                                $(".fancybox-inner").addClass("Class_fancy_ie_login");
                                jQuery.ajax({
                                    type: "POST",
                                    url: '<?php echo WEB_PATH; ?>/process.php',
                                    async: false,
                                    data: 'btncheckloginornot=true&link=' + this.href,
                                    // async:false,
                                    success: function (msg)
                                    {
                                        var obj = jQuery.parseJSON(msg);
                                        //alert(obj.status);
                                        //alert(obj.link);

                                        if (obj.status == "true")
                                        {
                                            var mylink = obj.link;
                                            var mylnk = mylink.split("=");
                                            var redirecturl = "<?php echo WEB_PATH ?>/" + mylnk[1];
                                            //alert(redirecturl);
                                            jQuery.fancybox.close();
                                            parent.window.location.href = redirecturl;
                                        }
                                        else
                                        {

                                        }

                                    }
                                });
                            },
                            helpers: {
                                overlay: {
                                    opacity: 0.3
                                } // overlay
                            }
                            // helpers
                        }); // fancybox
                //$("#btnActivationCode").click(function(){
                        // return false;
                //});
                        jQuery(document).ready(function () {

                            var hashval = window.location.hash;

                            hashval = hashval.substring(1, (hashval.length));

                            if (hashval.length != 0)
                            {

                                var st = "";
                                var err_msg = "";
                                window.location.hash = '';
                                $("#txt_activation_code").attr("value", hashval);
                                $("#activation_code").val(hashval);

                                return false;
                            }
                        });
                        $("#btnActivationCode").click(function () {

                            var st = "";
                            $.ajax({
                                type: "POST",
                                url: "<?php echo WEB_PATH; ?>/process.php",
                                data: "btnactivationcodeloginornot=true&activationcode=" + $("#activation_code").val().trim(),
                                async: false,
                                success: function (msg) {

                                    var obj1 = jQuery.parseJSON(msg);
                                    st = obj1.status;

                                    if (st == "false")
                                    {
                                        window.location.href = obj1.link;
                                        return false;
                                    }

                                }
                            });
                            if (st == "false")
                            {
                                return false;
                            }

                            /***********/
                            //  alert('is_userprofileset=true&custome_id=<?php if (isset($_SESSION['customer_id'])) echo $_SESSION['customer_id']; ?>');
                            var flag = false;
                            $.ajax({
                                type: "POST",
                                url: '<?php echo WEB_PATH; ?>/process.php',
                                data: 'is_userprofileset=true&custome_id=<?php if (isset($_SESSION['customer_id'])) echo $_SESSION['customer_id']; ?>',
                                async: false,
                                success: function (msg)
                                {
                                    var obj = jQuery.parseJSON(msg);

                                    if (obj.is_profileset == 1)
                                    {
                                        flag = true;
                                    }
                                    else {
                                        // jQuery("#profile_view").val("subscribe");
                                        flag = false;
                                        jQuery.fancybox({
                                            //href: this.href,

                                            //href: $(val).attr('mypopupid'),

                                            content: jQuery('#updateprofile1').html(),
                                            width: 435,
                                            height: 345,
                                            type: 'html',
                                            openEffect: 'elastic',
                                            openSpeed: 300,
                                            closeEffect: 'elastic',
                                            closeSpeed: 300,
                                            // topRatio: 0,

                                            changeFade: 'fast',
                                            beforeShow: function () {

                                            },
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }

                                        });
                                        //alert("You Profile IS not set. First set it");
                                    }
                                }
                            });
                            if (!flag)
                            {
                                return false;
                            }
                            /***********/


                            //return false;

                            // alert("In Click");
                            //alert($("#activation_code").val().trim());
                            //alert("click");
                            //
                            //alert("btnActivationCode_=yes&activation_code="+$("#activation_code").val().trim()+"&hp="+$("#hp").val());
                            var r_url = "";
                            var st = "";
                            var err_msg = "";
                            // $(".fancybox-inner #txt_activation_code").val($("#activation_code").val());
                            $("#txt_activation_code").attr("value", $("#activation_code").val());
                            $("#txt_activation_code_temp").attr("value", $("#activation_code").val());
                            // alert('<?php echo WEB_PATH ?>/process.php?btnActivationCode_=yes&activation_code="+$("#activation_code").val().trim()+"&hp="+$("#hp").val());
                            //return false;
                            $.ajax({
                                type: "POST",
                                url: "<?php echo WEB_PATH ?>/process.php",
                                data: "btnActivationCode_=yes&activation_code=" + $("#activation_code").val().trim() + "&hp=" + $("#hp").val(),
                                async: false,
                                success: function (msg) {
                                    // //alert(msg);
                                    var obj = jQuery.parseJSON(msg);
                                    st = obj.status;
                                    err_msg = obj.error_msg;
                                    if (st == "true") {
                                        cid_ = obj.campaign_id;
                                        lid_ = obj.l_id;
                                        r_url = obj.permalink;
                                    }
                                }
                            });
                            if (st == "true")
                            {
                                //alert("In");
                                //alert("<?php echo WEB_PATH; ?>/campaign.php?campaign_id="+cid_ +"&l_id="+lid_);
                                //return false;
                                window.lovation.href = r_url;
                            }

                            //alert(st);
                            if (st == "false")
                            {
                                $.ajax({
                                    type: "POST",
                                    url: "<?php echo WEB_PATH ?>/process.php",
                                    data: "checkactivationcode=yes&activationcode=" + $("#activation_code").val().trim() + "&hp=" + $("#hp").val(),
                                    async: false,
                                    success: function (msg) {
                                        // alert(msg);
                                        if (msg.indexOf('###true###') !== -1)
                                        {
                                            flag = false;
                                            $("#div_locations").html(msg);
                                            $("#div_locations").css("display", "block");
                                            //  $("#div_locations").css("display","none");
                                        }
                                        else if (msg.indexOf('###false###') !== -1)
                                        {
                                            // alert("infalse");
                                            flag = false;
                                            $("#div_locations").html("");
                                            // $("#div_locations").css("display","none");
                                        }
                                        else
                                        {
                                            flag = false;
                                            $("#div_locations").html(msg);
                                            $("#div_locations").css("display", "block");
                                            // return false;
                                        }
                                    }
                                });
                            }
                            //alert(err_msg);
                            // alert("<?php echo WEB_PATH ?>/process.php?getMapForActivationCode=yes&activationcode=" +$("#activation_code").val().trim()+"&message="+err_msg);
                            if (typeof err_msg !== 'undefined' && err_msg !== null) {

                                jQuery.fancybox({
                                    //content:jQuery('#activation_div').html(),
                                    href: "<?php echo WEB_PATH ?>/process.php?getMapForActivationCode=yes&activationcode=" + $("#activation_code").val().trim() + "&message=" + err_msg,
                                    width: 335,
                                    height: 145,
                                    type: 'iframe',
                                    openEffect: 'elastic',
                                    openSpeed: 300,
                                    closeEffect: 'elastic',
                                    closeSpeed: 300,
                                    // topRatio: 0,
                                    scrolling: 'no',
                                    changeFade: 'fast',
                                    beforeShow: function () {
                                        jQuery(".fancybox-inner").addClass("Class_for_activation_error");


                                    },
                                    /*afterComplete: function(){
                                     alert("complete!");
                                     },
                                     afterShow:function(){
                                     
                                     jQuery('body',jQuery('.fancybox-iframe').contents()).addClass("newclass");},*/
                                    helpers: {
                                        overlay: {
                                            opacity: 0.3
                                        } // overlay
                                    }
                                });
                            }
                            else {
                                // alert('In else---');
                                //  alert('In if---');
                                jQuery.fancybox({
                                    //content:jQuery('#activation_div').html(),
                                    href: "<?php echo WEB_PATH; ?>/process.php?getMapForActivationCode=yes&activationcode=" + $("#activation_code").val().trim() + "&message=",
                                    width: 435,
                                    height: 345,
                                    type: 'iframe',
                                    openEffect: 'elastic',
                                    openSpeed: 300,
                                    closeEffect: 'elastic',
                                    closeSpeed: 300,
                                    // topRatio: 0,

                                    changeFade: 'fast',
                                    beforeShow: function () {
                                        jQuery(".fancybox-inner").addClass("Class_for_activation");
                                    },
                                    helpers: {
                                        overlay: {
                                            opacity: 0.3
                                        } // overlay
                                    }
                                });
                            }
                            //  $("#div_msg").html(err_msg);


                            return false;
                        });

                        jQuery("body").on("click", ".fancybox-inner .update_profile_detail #btnupdateprofile", function () {
                            var flag = true;
                            var country_code = jQuery(".fancybox-inner #country option:selected").attr("country_code");
                            var zip = jQuery(".fancybox-inner #postalcode").val().toUpperCase();

                            //var gender=jQuery(".genderclass option:selected").val();
                            var gender = jQuery(".genderclass").find('option:selected').text();
                            var dob_month = jQuery(".dateofmonth").find('option:selected').text();
                            var dob_day = jQuery(".dateofday").find('option:selected').text();
                            var dob_year = jQuery(".dateofyear").find('option:selected').text();
                            var country = jQuery(".countryclass").find('option:selected').text();
                            //var postalcode=jQuery(".postalcodeclass").val();






                            /*if(isValidPostalCode(zip,country_code)){
                             flag = true;
                             
                             jQuery(".fancybox-inner .err_postalcode").text("");
                             }
                             else{
                             
                             jQuery(".fancybox-inner .err_postalcode").text("Please Input Valid Postal Code");
                             flag = false;
                             }*/
                            if (gender == "") {


                                jQuery(".fancybox-inner .err_gender").text("<?php echo $client_msg['login_register']['Msg_Select_Gender'] ?>");
                                flag = false;
                            }
                            else
                            {

                                jQuery(".fancybox-inner .err_gender").text("");
                                flag = true;
                            }
                            if (dob_month == "" || dob_day == "" || dob_year == "") {

                                jQuery(".fancybox-inner .err_dob").text("<?php echo $client_msg['login_register']['Msg_Select_Date_Of_Birth'] ?>");
                                flag = false;
                            }
                            else
                            {
                                jQuery(".fancybox-inner .err_dob").text("");
                                flag = true;
                            }

                            if (country == "") {
                                jQuery(".fancybox-inner .err_country").text("<?php echo $client_msg['login_register']['Msg_Select_Country'] ?>");
                                flag = false;
                            }
                            else
                            {
                                jQuery(".fancybox-inner .err_country").text("");
                                flag = true;
                            }
                            if (zip == "") {
                                jQuery(".fancybox-inner .err_postalcode").text("<?php echo $client_msg['login_register']['Msg_Enter_Postal_Code'] ?>");
                                flag = false;
                            }
                            else
                            {
                                if (isValidPostalCode(zip, country_code)) {
                                    flag = true;

                                    jQuery(".fancybox-inner .err_postalcode").text("");
                                }
                                else {

                                    jQuery(".fancybox-inner .err_postalcode").text("<?php echo $client_msg['login_register']['Msg_Input_Valid_Postal_Code'] ?>");
                                    flag = false;
                                }
                                //jQuery(".fancybox-inner .err_postalcode").text("");
                                //flag = true;
                            }
                            if (gender == "" || dob_month == "" || dob_day == "" || dob_year == "" || country == "")
                            {
                                flag = false;
                            }



                            if (flag)
                            {
                                $.ajax({
                                    type: "POST",
                                    url: '<?php echo WEB_PATH; ?>/process.php',
                                    data: 'btnUpdateProfile_compulsary_field=true&customer_id=<?php if (isset($_SESSION['customer_id'])) echo $_SESSION['customer_id'] ?>&country=' + $(".fancybox-inner #country").val() + '&gender=' + $(".fancybox-inner #gender").val() + '&dob_month=' + $(".fancybox-inner #dob_month").val() + '&dob_day=' + $(".fancybox-inner #dob_day").val() + '&dob_year=' + $(".fancybox-inner #dob_year").val() + '&postalcode=' + $(".fancybox-inner #postalcode").val(),
                                    async: false,
                                    success: function (msg)
                                    {
                                        jQuery.fancybox.close();
                                        jQuery("#btnActivationCode").trigger("click");

                                    }
                                });
                            }
                        });
                        jQuery("body").on("click", ".fancybox-inner .update_profile_detail #btncancelprofile", function () {

                            jQuery.fancybox.close();
                        });
                </script>

                <?php
                if (isset($_SESSION['msg'])) {
                        $message = $_SESSION['msg'];
                } else {
                        $message = "";
                }
                $mymsg_activation = ucwords($message);
                if ($mymsg_activation != "") {
                        ?>
                        <script type="text/javascript">
                                //alert('<?php echo $mymsg_activation ?>')
                        </script>
                        <?php
                        $_SESSION['msg'] = "";
                }
                //echo $_SESSION['msg'];
                //	$_SESSION['msg'] = ""; 
                ?>
                <script type="text/javascript">
                        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                            jQuery("#right_manu_button").toggle(function () {
                                jQuery(".right_sub_manu").show();
                            }, function () {
                                jQuery(".right_sub_manu").hide();
                            });
                        }
                        else
                        {
                            jQuery('#right_manu_button,.right_sub_manu').mouseenter(function () {
                                jQuery('.right_sub_manu').show();
                            });
                            jQuery('#right_manu_button,.right_sub_manu').mouseleave(function () {
                                jQuery('.right_sub_manu').hide();
                            });

                        }






                        jQuery(".expire_close_btn").click(function () {
                            //alert("close");
                            var notification_id = jQuery(this).attr("notification_id");
                            var customer_id = "<?php if (isset($_SESSION['customer_id'])) {
                        echo $_SESSION['customer_id'];
                } else {
                        echo '';
                } ?>";
                            jQuery(this).parent('.expire_notification_second').remove();
                            setCookie("expire_notification_" + notification_id, "yes", 365);

                            jQuery.ajax({
                                type: "POST",
                                url: "<?php echo WEB_PATH ?>/process.php",
                data: "set_read_notofication=yes&notification_id=" + notification_id + "&customer_id=" + customer_id,
                async: false,
                success: function (msg) {
                    var obj = jQuery.parseJSON(msg);
                    //alert(obj.status);
                    if (obj.status == "false")
                    {

                    }
                    else
                    {

                    }
                }
            });
            //alert(getCookie("read_notification"));

            var num_of_notificaton = jQuery(".notificationsecond").length;
            if (num_of_notificaton == 0)
            {
                jQuery(".icon-bell").addClass("icon-bell-empty");
            }
        });

        jQuery(".recent_visit_close_btn").click(function () {
            //alert("close");
            var notification_id = jQuery(this).attr("notification_id");
            var customer_id = jQuery(this).attr("customer_id");
            var total_redeem_point = jQuery(this).attr("total_redeem_point");
            var created_by = jQuery(this).attr("created_by");

            jQuery(".earned_visit_notification_second").remove();
            setCookie("recent_visit_notification_" + customer_id + "_" + total_redeem_point + "_" + created_by, "yes", 365);

            //alert(getCookie("earned_recent_visit_notification"));

            var num_of_notificaton = jQuery(".notificationsecond").length;
            if (num_of_notificaton == 0)
            {
                jQuery(".icon-bell").addClass("icon-bell-empty");
            }
        });

        function setCookie(c_name, value, exdays)
        {
            var exdate = new Date();
            exdate.setDate(exdate.getDate() + exdays);
            var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
            document.cookie = c_name + "=" + c_value;
        }
        function getCookie(c_name)
        {
            var i, x, y, ARRcookies = document.cookie.split(";");
            for (i = 0; i < ARRcookies.length; i++)
            {
                x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
                y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
                x = x.replace(/^\s+|\s+$/g, "");
                if (x == c_name)
                {
                    return unescape(y);
                }
            }
        }
        $(".icon-bell_div").click(function () {
            if ($(".notificationclass").attr("disp") == 0)
            {
                $(".notificationclass").attr("disp", "1");
                $(".notificationclass").show();
            }
            else
            {
                $(".notificationclass").attr("disp", "0");
                $(".notificationclass").hide();
            }

        });
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


<script type="text/javascript">

        /*$(window).on('load', function() {
         var imgheight = jQuery("#fadeshow11").children().children().height();
         //alert(imgheight+"===image height1");
         jQuery("#fadeshow11").css("height",imgheight+"px");
         }); */

        jQuery(window).resize(function () {
            var imgheight = jQuery("#fadeshow11").children().children().height();
            //alert(imgheight+"===image height2");
            jQuery("#fadeshow11").css("height", imgheight + "px");
        });

        var isMobile = {
            Android: function () {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function () {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function () {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function () {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function () {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function () {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };
        if (isMobile.any())
        {

            jQuery('#loginlink').toggle(function () {
                //alert("open");
                //alert(jQuery('#lgn_popup').css("display"));
                jQuery('#lgn_popup').css("display", "block");
                //alert(jQuery('#lgn_popup').css("display"));
                //alert('my mobile');
            }, function () {
                //alert("close");
                //alert(jQuery('#lgn_popup').css("display"));
                jQuery('#lgn_popup').css("display", "none");
                //alert(jQuery('#lgn_popup').css("display"));
                //alert('my mobile');
            });

            /*if (window.confirm('Really go to Scanflip app?'))
             {
             // alert('You Are Using Mobile Version!');
             window.location = 'scanflip://';
             }
             else
             {
             die();
             }*/


        }
        else
        {

            jQuery('.header .loginDiv').mouseenter(function () {
                jQuery('#lgn_popup').show();
            });
            jQuery('.header .loginDiv').mouseleave(function () {
                jQuery('#lgn_popup').hide();
            });
        }
</script>

