<?php

//require_once("../../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

$Sql = "Select material_format from marketing_material where material_size=" . $_REQUEST['size_id'];

$RS = $objDB->Conn->Execute($Sql);

$json_array["html_content"] = str_replace("&lt;", "<", str_replace("&gt;", ">", $RS->fields['material_format']));


$address = "";
$Sql = "Select * from locations where id=" . $_REQUEST['locid'];
$RS_locations = $objDB->Conn->Execute($Sql);

$Sql_merchant_icon = "select merchant_icon , business from merchant_user where id=" . $RS_locations->fields['created_by'];


$RS_merchant_icon = $objDB->Conn->Execute($Sql_merchant_icon);
$address .= $RS_merchant_icon->fields['business'] . "<br />";
$phno = explode("-", $RS_locations->fields['phone_number']);
$newphno = "(" . $phno[1] . ") " . $phno[2] . "-" . $phno[3];

$array_where = array();
$array_where['id'] = $RS_locations->fields['country'];
$RS_country = $objDB->Show("country", $array_where);
					
$array_where = array();
$array_where['id'] = $RS_locations->fields['state'];
$RS_state = $objDB->Show("state", $array_where);

$array_where = array();
$array_where['id'] = $RS_locations->fields['city'];
$RS_city = $objDB->Show("city", $array_where);
			
$address .="" . $RS_locations->fields['address'] . ", <br />" . $RS_city->fields['name'] . ", " . $RS_state->fields['short_form'] . ", " . $RS_country->fields['name'] . ", " . $RS_locations->fields['zip'] . " <br />Phone:" . $newphno;
$json_array["location_address"] = $address;

if ($RS_merchant_icon->fields['merchant_icon'] != "") {
        $img_src = ASSETS_IMG . "/m/icon/" . $RS_merchant_icon->fields['merchant_icon'];
} else {
        $img_src = ASSETS_IMG . "/m/default_campaign.jpg";
}

$json_array["location_image"] = $img_src;

if(isset($_REQUEST['is_loyalty']))
 {
	 	$Sql = "Select qrcode from qrcodes q, merchant_loyalty_card mlc where q.id=mlc.qrcode_id and mlc.id=".$_REQUEST['campid'];
 }
 else
 {
	if ($_REQUEST['campid'] != 0) 
	{
		//$Sql = "Select qrcode from qrcodes where id in(select qrcode_id from qrcode_campaign where campaign_id=" . $_REQUEST['campid'] . " )";
		$Sql = "Select qrcode from qrcodes where id = (select qrcode_id from campaigns where id=" . $_REQUEST['campid'] . " )";
	} 
	else 
	{
		//$Sql = "Select qrcode from qrcodes where id in(select qrcode_id from qrcode_location where location_id=" . $_REQUEST['locid'] . " )";
		$Sql = "Select qrcode from qrcodes where id = (select qrcode_id from locations where id=" . $_REQUEST['locid'] . " )";
	}
}

$RS_qrcode = $objDB->Conn->Execute($Sql);
$url = WEB_PATH . "/qr.php?qrcode=" . base64_encode($RS_qrcode->fields['qrcode']);
$json_array["qrcode"] = $url;
$json = json_encode($json_array);

$filename = UPLOAD_IMG . "/m/campaign/" . $_REQUEST['image'];

list($Actualwidth, $Actualheight, $type, $attr) = getimagesize($filename);
$w = 0;
$width = $Actualwidth;
$height = $Actualheight;
$qrcode_size = 100;

if ($_REQUEST['size_id'] == 1) {
        $Xmax = 549;
        $Ymax = 130;
        $qrcode_size = 70;
} else if ($_REQUEST['size_id'] == 2) {
        $qrcode_size = 100;
        $Xmax = 800;
        $Ymax = 150;
} else if ($_REQUEST['size_id'] == 3) {
        $qrcode_size = 120;
        $Xmax = 800;
        $Ymax = 235;
} else if ($_REQUEST['size_id'] == 5) {
        $qrcode_size = 90;
        $Xmax = 670;
        $Ymax = 140;
}





if ($width > $Xmax || $height > $Ymax) {

        $AR = $Xmax / $Ymax;
// $filename =  SERVER_PATH."/merchant/images/logo/campaign_1373603010.png";
//    list($Actualwidth, $Actualheight, $type, $attr) = getimagesize($filename);
        $Applywidth = 0;
        $Applyheight = 0;
        if ($Actualwidth > $Xmax && $Actualheight > $Ymax) {

                $Applywidth = $Xmax;
                $Applyheight = $Xmax / $AR;
        } else if ($Actualwidth <= $Xmax && $Actualheight > $Ymax) {

                $Applywidth = $Actualwidth;
                $Applyheight = $Actualwidth / $AR;
        } else if ($Actualwidth <= $Xmax && $Actualheight <= $Ymax) {

                $Applywidth = $Actualwidth;
                $Applyheight = $Actualheight;
        } else {

                $Applywidth = $Actualwidth;
                $Applyheight = $Actualheight;
        }
        $newWidth = round($Applywidth, 2);
        $newHeight = round($Applyheight, 2);
} else {


        if ($width == $Xmax && $height == $Ymax) {
                $newHeight = $height;
                $newWidth = $width;
        } else {

                if ($width > $Xmax || $height > $Ymax) {
                        if ($width >= $height) {

                                if ($width <= $Ymax && $height <= $Xmax) {
                                        return image;  // no resizing required
                                }
                                $wRatio = $Ymax / $width;

                                $hRatio = $Xmax / $height;
                        } else {

                                if ($height <= $Ymax && $width <= $Xmax) {
                                        //return image; // no resizing required
                                        $newHeight = $height;
                                        $newWidth = $width;
                                }

                                $wRatio = $Xmax / $width;
                                $hRatio = $Ymax / $height;
                        }
                        $resizeRatio = Min($wRatio, $hRatio);
                        $newHeight = $height * $resizeRatio;
                        $newWidth = $width * $resizeRatio;
                } else {

                        $newHeight = $height;
                        $newWidth = $width;
                }
        }
}
if ($height <= $Ymax && $width <= $Xmax) {

        $newHeight = $height;
        $newWidth = $width;
} else {

        $ratio1 = $Xmax / $width;
        $ratio2 = $Ymax / $height;
        $ratio = min($ratio1, $ratio2);
        $newWidth = $width * $ratio;
        $newHeight = $height * $ratio;
}
$merchant_id = $_REQUEST['merchant_id'];


$dirname = $_REQUEST['merchant_id'] . "_upload";
$filename1 = (LIBRARY . "/demopdf/" . $dirname . "/");

if (file_exists($filename1)) {
        
} else {
        mkdir(LIBRARY . "demopdf/" . "$dirname", 0777);
}
$image_folder = LIBRARY . "/demopdf/" . $dirname . "/";

passthru("/usr/bin/convert " . $filename . " -resize " . $newWidth . "x" . $newHeight . "! " . $image_folder . "resized_image.jpg");

// create jpgg image //  
$filename_eps_path = $image_folder . "template-qrcode-eps.eps";
$filename = $image_folder . "template-qrcode.jpg";
$fname = WEB_PATH . "/QR-Generator-PHP-master/php/qr.php?d='" . $json_array["qrcode"] . "&size=" . $qrcode_size . "'";
passthru("/usr/bin/convert " . $fname . "  " . $filename_eps_path);
passthru("/usr/bin/convert " . $filename_eps_path . "  " . $filename);
// create jpg image //


$html = $json_array["html_content"];
$regex = 'http://www.silkenthomas.com/system/assets/3 3/custom/squires-burger-deal.jpg?1300377282';
$result = str_replace("http://www.silkenthomas.com/system/assets/33/custom/squires-burger-deal.jpg?1300377282", WEB_PATH . "/libraries/demopdf/" . $dirname . "/resized_image.jpg", $html);
$regex = 'http://startupfashion.com/wp-content/uploads/2012/01/StartUp-FASHION-QR-Code.png';
$result = str_replace($regex, WEB_PATH . "/libraries/demopdf/" . $dirname . "/template-qrcode.jpg", $result);
$result = str_replace("http://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Mcdonalds-90s-logo.svg/220px-Mcdonalds-90s-logo.svg.png", $json_array["location_image"], $result);
$result = str_replace("locationAddresscomehere", $json_array["location_address"], $result);
$result = str_replace("http://www.ebrary.com/corp/images/app_store1.png", ASSETS_IMG . "/m/app-store.png", $result);
$result = str_replace("http://www.ebrary.com/corp/images/app_store2.png", ASSETS_IMG . "/m/anroid.png", $result);
$result = str_replace("http://1.bp.blogspot.com/-4vylr5lt90k/UMadDEAFmBI/AAAAAAAAA0g/qZWBngwhobo/s1600/Apple+Logo+1.jpg", ASSETS_IMG . "/m/scanplip-logo.png", $result);
$result = str_replace("descriptioncontent", urldecode($_REQUEST['text']), $result);


$qrcode_img_src = WEB_PATH . "/QR-Generator-PHP-master/php/qr.php?d='" . $json_array["qrcode"];
$html = $result; //.'<img height="0px" width="0px" src="'.$qrcode_img_src.'" style="display:none" />';
echo $html;
?>
