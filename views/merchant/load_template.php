<?php
/**
 * @uses show tempalates in add and edit campaigns
 * @used in pages :add-compaign.php,copy-compaign.php,edit-compaign.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');


$array_where['id'] = $_REQUEST['template_id'];
$RS_t = $objDB->Show("campaigns_template", $array_where);

/*
echo "<pre>";
print_r($RS_t);
echo "</pre>";
*/
$array['fields'] =  (array)$RS_t->fields;

echo json_encode($array);
//echo json_last_error();
//echo json_last_error_msg();

?>
