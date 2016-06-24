<?php

//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH . "/classes/DB.php");
//include_once(SERVER_PATH . "/classes/JSON.php");

//$objDB = new DB('read');
//$objDBWrt = new DB('write');

$array_where = array();
$RS = $objDB->Show("merchant_user", $array_where);

function otherDiffDate($end, $out_in_array = false) {
        $intervalo = date_diff(date_create(), date_create($end));
        $out = $intervalo->format("Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s");
        if (!$out_in_array)
                return $out;
        $a_out = array();
        array_walk(explode(',', $out), function($val, $key) use(&$a_out) {
                $v = explode(':', $val);
                $a_out[$v[0]] = $v[1];
        });
        return $a_out;
}

while ($Row = $RS->FetchRow()) {
        $id = $Row["id"];
        $email = $Row['email'];
        $token = $Row['token'];
        $token_created_at = $Row['token_created_at'];
        if ($token != "" && $token_created_at != "0000-00-00 00:00:00") {

                $date2 = date('Y-m-d H:i:s');
                $date1 = $token_created_at;
                $seconds = strtotime($date2) - strtotime($date1);
                $hours = $seconds / 60 / 60;

                if ($hours > 24) {

                        $array_where1 = array();
                        $array_values1 = array();
                        $array_values1['token'] = '';
                        $array_values1['token_created_at'] = "0000-00-00 00:00:00";
                        $array_where1['email'] = $email;
                        $objDBWrt->Update($array_values1, "merchant_user", $array_where1);
                } else {
                        
                }
        }
}
?>
