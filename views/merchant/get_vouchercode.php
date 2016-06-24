<?php
/**
 * @uses get voucher code
 * @used in pages :reddem-deal.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where = array();
$coupon_code=$_REQUEST['couponname'];
//$Sql = "SELECT * from coupon_codes where coupon_code like '%".$coupon_code."%'";
// change only show campaign hint for login merchant
/*$Sql="SELECT * from coupon_codes c , campaigns cp  where cp.id=c.customer_campaign_code and coupon_code like '%".$coupon_code."%' and cp.created_by=".$_SESSION['merchant_id'];
$RS = $objDB->Conn->Execute($Sql);*/
//echo "SELECT * from coupon_codes c , campaigns cp  where cp.id=c.customer_campaign_code and coupon_code like '%".$coupon_code."%' and cp.created_by=".$_SESSION['merchant_id'];

$RS = $objDB->Conn->Execute("SELECT * from coupon_codes c , campaigns cp  where cp.id=c.customer_campaign_code and coupon_code like '%".$coupon_code."%' and cp.created_by=?",array($_SESSION['merchant_id']));
if($RS->RecordCount()>0)
{
	$RS->MoveFirst();
	/*
	$couponcodes="";
	while($Row = $RS->FetchRow())
	{
		$couponcodes .= $Row['coupon_code'].",";
	}
	$couponcodes=substr($couponcodes,0,strlen($couponcodes)-1);
	echo $couponcodes;
	*/
	$html = '<div style="display:table;border:1px solid #F93;margin-top:-5px;width:205px;">';
	while($Row = $RS->FetchRow())
	{
		$html .='<div style="display:table-row;width:99%;">';
		$html .='<div onclick="repalcevalue(this);" style="cursor:pointer;display:table-cell;padding:2px;" class="autocomplete">'. strtoupper($Row['coupon_code']).'</div>';
		$html .='</div>';
	}
	$html .= '</div>';
	echo $html;
}
?>
