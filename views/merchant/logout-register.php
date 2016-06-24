<?php
/**
 * @uses logout when merchant setup
 * @used in pages :merchant-setup.php,process.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
$_SESSION = array();
header("Location: ".WEB_PATH."/merchant/register.php");
exit();
?>
