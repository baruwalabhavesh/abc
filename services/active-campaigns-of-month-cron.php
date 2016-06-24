<?php

//$path = '/var/www/vhosts/scanflip.com/httpdocs/test_scanflip';
//require_once($path."/configs/DB.php");
require_once(LIBRARY . "/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");

$array_where_as = array();
$array_where_as['id'] = 7;
$RS = $objDB->Show("admin_settings", $array_where_as);
if ($RS->RecordCount() > 0) {
        if ($RS->fields['action'] == 0) {
                $array_values_as = $array_where_as = array();
                $array_values_as['action'] = 1;
                $array_where_as['id'] = 7;
                $objDBWrt->Update($array_values_as, "admin_settings", $array_where_as);

                $objDBWrt->Conn->Execute("insert into merchant_store set merchant_id=? , location_id=?", array(3, 3));

                $result = $objDB->Conn->Execute("SELECT * FROM merchant_user  WHERE approve=?", array(1));

                while ($row = $result->FetchRow()) {

                        $RS_mb = $objDB->Conn->Execute("SELECT * FROM merchant_billing  WHERE merchant_id=?", array($row['id']));

                        $res = $RS_mb->FetchRow();
                        $RS_bp = $objDB->Conn->Execute("SELECT * FROM billing_packages  WHERE id=?", array($res['pack_id']));

                        $res1 = $RS_bp->FetchRow();
                        $package_total_campaigns = $res1['total_no_of_camp_per_month'];

                        $objDBWrt->Conn->Execute("update merchant_user set total_no_of_campaign=? where id=?", array($package_total_campaigns, $row['id']));
                }
        }

        $array_values_as = $array_where_as = array();
        $array_values_as['action'] = 0;
        $array_where_as['id'] = 7;
        $objDBWrt->Update($array_values_as, "admin_settings", $array_where_as);

        $objDBWrt->Conn->Close();
        $objDB->Conn->Close();
}

?>
