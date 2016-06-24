<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array = array();
$array ['id']=$_REQUEST['id'];
$RS = $objDB->Show("qrcode_group",$array );
//print_r($RS);

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
                    <h2>Add Marketing Material</h2>
                    <div>
                        <span class="span_error_msg"  style="color:#FF0000; "></span>
                    </div>
	<form action="process.php" method="post">
            <input type="hidden" name="group_id" id="group_id" value="<?php echo $_REQUEST['id']; ?>" />
		<table border="0" cellspacing="2" cellpadding="2" width="100%" >
		   <tr>
					<td width="40%" align="right">Group Name: </td>
					<td width="60%" align="left">
						<input type="textbox" value="<?php echo $RS->fields['group_name'] ?>" name="name" id="name" />
					</td>
				  </tr>
				 
                 
                                  <tr>
					<td align="right">Merchant/Business : </td>
					
                                            <?php 
                                            $array = array();
                                             $array['approve'] = 1;
                                            $array['id'] =$RS->fields['merchant_id'];
                                              $array['merchant_parent'] =0;
                                          
                $RS_merchant = $objDB->Show("merchant_user",$array);
              
                                            ?>
                                         
                                        <td align="left">
                                        
<!--                                            <select id="merchant_id" name="merchant_id">-->
                                                 
                                                <?php 
                                                while($Row = $RS_merchant->FetchRow()){
                                                ?>
<!--                                                <option value="<?php echo $Row['id']?>" <?php if($RS->fields['merchant_id'] == $Row['id'] ) { echo "selected"; } ?> >-->
                                                    <?php echo $Row['business']."-".$Row['firstname']." ".$Row['lastname']?>
                                                    
<!--                                                </option>-->
                                                <?php } ?>
<!--                                            </select>-->
					</td>
				  </tr>
                                 
                 
				  
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<input type="submit" name="btneditqrcodegroup" value="Save" id="btneditqrcodegroup" >
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
       alert("In") ;
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
