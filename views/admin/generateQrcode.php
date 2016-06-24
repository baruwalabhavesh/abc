<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/a/jquery.js"></script>


</head>

 <div id="container">

              <!---start header---->
	
		<?
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		<div id="contentContainer">

	
	<div  id="sidebarLeft">
		<?
		require_once(ADMIN_VIEW."/quick-links.php");
		?>
		<!--end of sidebar Left--></div>

		<div id="content">
                    <h2>Add QR Code Group</h2>
                    <div>
                        <span class="span_error_msg"  style="color:#FF0000; "></span>
                    </div>
	<form action="process.php" method="post">
             
		<table border="0" cellspacing="2" cellpadding="2" width="100%" >
		   <tr>
					<td width="40%" align="right">QR Code Group Name: </td>
					<td width="60%" align="left">
						<input type="text" name="name" id="name" />
					</td>
				  </tr>
				 
                   
                                  <tr>
					<td align="right">Assign to Merchant/Business : </td>
					<td align="left">
                                            <?php 
                                            $sql= "Select * from merchant_user where approve=1 and  merchant_parent=0 and id not in(select merchant_id from qrcode_group )";
                                             
                $RS =  $objDB->Conn->Execute($sql);
                                            ?>
                                            <select id="merchant_id" name="merchant_id">
                                                <?php 
                                                while($Row = $RS->FetchRow()){
                                                    if($Row['business'] != ""){
                                                        $bus_nm = $Row['business']."-".$Row['firstname']." ".$Row['lastname'];
                                                    }
                                                    else{
                                                        $bus_nm = $Row['firstname']." ".$Row['lastname'];
                                                    }
                                                    
                                                ?>
                                                <option value="<?php echo $Row['id']?>"><?php echo $bus_nm; ?>
                                                    
                                                </option>
                                                <?php } ?>
                                            </select>
					</td>
				  </tr>
<!--                                  <tr>
                                       <td align="right">ECC : </td>
                                       <td align="left"> <?php   echo  '<select name="level">
            <option value="L"'.(($errorCorrectionLevel=='L')?' selected':'').'>L - smallest</option>
            <option value="M"'.(($errorCorrectionLevel=='M')?' selected':'').'>M</option>
            <option value="Q"'.(($errorCorrectionLevel=='Q')?' selected':'').'>Q</option>
            <option value="H"'.(($errorCorrectionLevel=='H')?' selected':'').'>H - best</option>
        </select>&nbsp;
        Size:&nbsp;<select name="size">';
        
    for($i=1;$i<=10;$i++)
        echo '<option value="'.$i.'"'.(($matrixPointSize==$i)?' selected':'').'>'.$i.'</option>';
        
    echo '</select>&nbsp;'; ?>
                                       </td>
                                  </tr>-->
                 
				  
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<input type="submit" name="btnaddqrcodegroup" value="Save" id="btnaddqrcodegroup" >
						<!--// 369-->
						<input type="submit" name="btnCancelqrcodegrouplist"  value="Cancel" >
                        <!--// 369-->
					</td>
				  </tr>
		 
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<script>
  
    $("#btnaddqrcodegroup").click(function(){
    
       var numbers = /^[0-9]+$/;  
      if($("#no_of_qrcode").val() != "")  
      {
      if($("#no_of_qrcode").val().match(numbers))  
      {  
        var val = parseInt($("#no_of_qrcode").val());
         
           if(val<=0)
               {
                   alert("Enter proper deal value");
                 return false;
               }
               else
                   {
                       return true;
                   }
      }
               else
                   {
                       alert("Enter proper deal value");
                       return false;
                   }
      }
      else{
          alert("Enter proper deal value");
           return false; 
      }
    });
</script>

<?
$_SESSION['msg'] = "";
?>

</body>
</html>
