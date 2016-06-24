<?php

/**
 * @uses locu venue share
 * @used in pages :locations.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH . "/classes/DB.php");
//$objDB = new DB('read');
//$objDBWrt = new DB('write');

include 'locu.php';

//$API_KEY = '269fe167da30803613598800a3da6e0e590297ac';

$array_as = array();
$array_as['id'] = 13;
$RS_as = $objDB->Show("admin_settings", $array_as);
$API_KEY = $RS_as->fields['value'];

//create api clients
$venue_client = new VenueApiClient($API_KEY);
$menuitem_client = new MenuItemApiClient($API_KEY);

//$result = mysql_query("select * from locations where created_by=".$_REQUEST['customer_id']." and id=".$_REQUEST['location_id']);
$result = $objDB->Conn->Execute("select * from locations where created_by=? and id=?", array($_REQUEST['customer_id'], $_REQUEST['location_id']));

//while( $userData = mysql_fetch_array($result)){
while ($userData = $result->FetchRow()) {

        if ($userData['website'] == "") {

                if ($userData['venue_id'] != "") {
                        $file_name = '../locu_files/locu_' . $userData['venue_id'] . '.txt';
                        unlink($file_name);
                }
                /* $sql_location_update="UPDATE locations SET venue_id='' WHERE id=".$_REQUEST['location_id'];
                  $objDB->Conn->Execute($sql_location_update); */
                $objDBWrt->Conn->Execute("UPDATE locations SET venue_id='' WHERE id=?", array($_REQUEST['location_id']));
                echo "urlblank";
        } else {
                //$website = array('website_url' => $userData['website']);   
                $website = array('website_url' => $userData['website'], 'has_menu' => true, 'country' => $userData['country']);
                $locudata = $venue_client->search($website);
                for ($i = 0; $i < count($locudata); $i++) {


                        if ($locudata[$i]['has_menu'] == "1") {

                                $venue_id = $locudata[$i]['id'];
                                break;
                        }
                }

                if ($venue_id != "") {
                        /* $sql_location_update="UPDATE locations SET venue_id='".$venue_id."' WHERE id=".$_REQUEST['location_id'];
                          $objDB->Conn->Execute($sql_location_update); */
                        $objDBWrt->Conn->Execute("UPDATE locations SET venue_id=? WHERE id=?", array($venue_id, $_REQUEST['location_id']));
                        echo "update";
                } else {
                        if ($userData['venue_id'] != "") {
                                $file_name = '../locu_files/locu_' . $userData['venue_id'] . '.txt';
                                unlink($file_name);
                        }
                        /* $sql_location_update="UPDATE locations SET venue_id='' WHERE id=".$_REQUEST['location_id'];
                          $objDB->Conn->Execute($sql_location_update); */
                        $objDBWrt->Conn->Execute("UPDATE locations SET venue_id='' WHERE id=?", array($_REQUEST['location_id']));
                        echo "noupdate";
                }
        }
}
?>
