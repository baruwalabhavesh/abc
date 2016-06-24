<?php

/**
 * @uses check valid csv 
 * @used in pages : edit-distributionlist.php,group_clone_distributionlist,php,import-customers.php
 * @author Sangeeta Raghavani
 */


//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
if(isset($_REQUEST['doAction']))
{
	switch($_REQUEST['doAction'])
	{
		case "FileUpload":
			$merchant_id =  $_SESSION['merchant_id'];
			/*$Sql = "SELECT * from merchant_user WHERE id='$merchant_id'";
			$RS = $objDB->Conn->Execute($Sql);*/
			$RS = $objDB->Conn->Execute("SELECT * from merchant_user WHERE id=?",array($merchant_id));

			if($RS->RecordCount()>0)
			{
				$Row = $RS->FetchRow();
			}
			$mechant_nm = $Row['firstname'];
			$mechant_lnm = $Row['lastname'];

			$dirname = $mechant_nm."_".$merchant_id;    
			$filename = (ROOT."/assets/upload/" . "$dirname" . "/");
  
			if (file_exists($filename)) 
			{        
			} 
			else 
			{
				mkdir(ROOT."/assets/upload/" . "$dirname", 0777);        
			}
			$image_folder = ROOT."/assets/upload/".$dirname."/";
			$uploaded = false;
			//exit;
			if($_FILES['uploadcsvfile']['error'] == 0 )
			{
				$up = move_uploaded_file($_FILES['uploadcsvfile']['tmp_name'], $image_folder.$_FILES['uploadcsvfile']['name']);

				if($up)
				{    
					echo "success"."|".$image_folder.$_FILES['uploadcsvfile']['name']."|".$_FILES['uploadcsvfile']['name'];
				} 
				else 
				{
					echo "error";
					exit;
				}               
			}
        break;  
	}
}
?>
