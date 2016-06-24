<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array = array();
$array ['id']=$_REQUEST['id'];
$RS = $objDB->Show("qrcodes",$array );
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
                    <h2>Assign group</h2>
                    <div>
                        <span class="span_error_msg"  style="color:#FF0000; "></span>
                    </div>
	<form action="process.php" method="post">
            <input type="hidden" name="group_id" id="group_id" value="<?php echo $_REQUEST['id']; ?>" />
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align='left' >
		   <tr>
					<td width="20%" align="right">QRcode : </td>
					<td width="80%" align="left">
						<?php echo $RS->fields['qrcode'];?>
					</td>
				  </tr>
				 
                   <tr>
					<td width="20%" align="right">Type : </td>
					<td width="80%" align="left">
						<?php if($RS->fields['reserve']==1) 
                    { echo "Assigned"; }
                    else { echo "Not Assigned"; } ?>&nbsp;
					</td>
                                       
				  </tr>
                                   <tr>
					<td width="20%" align="right">Group Type : </td>
					<td width="80%" align="left">
                                              <?php 
                //check for qrcode group 
                $array = array();
                
                $array['qrcode_id']=$_REQUEST['id'];
                $sql_group = "Select * from qrcodegroup_qrcode qq, qrcode_group qg where qq.qrcodegroup_id = qg.id and qq.qrcode_id = ".$_REQUEST['id'];
          //  echo $sql_group;
                $RS_groupinfo = $objDB->Conn->Execute($sql_group); 
         
                     ?>
						<?php if($RS_groupinfo->fields['merchant_id'] != 0)
                    { echo "Assigned"; }
                    else { echo "Not Assigned"; } ?>&nbsp;
					</td>
                                       
				  </tr>
                                  <tr>
					<td width="20%" align="right">Group : </td>
					<td width="80%" align="left">
                                            <div style="" name="div_existing_group" id="div_existing_group"> 
                                                <?php 
                                                $sql_group = "Select * from qrcodegroup_qrcode qq, qrcode_group qg where qq.qrcodegroup_id = qg.id and qq.qrcode_id = ".$_REQUEST['id'];
                                             //   echo $sql_group;
                                       $RS_groupinfo = $objDB->Conn->Execute($sql_group);  
                                      
                                       
                                                ?>
                                            <select id="qrcode_group_list" name="qrcode_group_list" >
                                            <?php 
                                       // $array['qrcode_id']=$Row['id'];
                                                                  
                                       $RS_group = $objDB->Show("qrcode_group");
                                       
                
                while($Row_group = $RS_group->FetchRow()){ ?>
                                                <option <?php if($RS_groupinfo->fields['qrcodegroup_id']== $Row_group['id']) { echo "selected";} ?>  value="<?php echo $Row_group['id'] ?>"> 
                                                    <?php echo $Row_group['group_name'] ?>
                    </option>
                                                 <?php
                }
                ?>
                                            </select> &nbsp; &nbsp; <span style="font-weight: bold;"> OR </span> <a href="javascript:void(0)" id="disp_new_group" name="disp_new_group" > Create new group </a> </div>
                                             
                                               <div style="display: none" id="div_newgroup">
                                                   <input type="textbox" name="txt_groupname" id="txt_groupname" value="" /> 
                                                    <?php 
                                                    $array_ = array();
                                             $array_['approve'] = 1;
                                              $array_['merchant_parent'] =0;
                $RS = $objDB->Show("merchant_user",$array_);
                                            ?>
                                            <select id="merchant_id" name="merchant_id">
                                                <?php 
                                                while($Row = $RS->FetchRow()){
                                                ?>
                                                <option value="<?php echo $Row['id']?>"><?php echo $Row['business']."-".$Row['firstname']." ".$Row['lastname']?>
                                                    
                                                </option>
                                                <?php } ?>
                                            </select>
                                                   &nbsp; &nbsp; <span style="font-weight: bold;"> OR </span> <a href="javascript:void(0)" id="disp_existing_group" name="disp_existing_group" >from existing group </a>
                                               </div>
					</td>
				  </tr>
                                 
                 
				  
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
                                            <input type="hidden" name="qid" id="qid" value="<?php echo $_REQUEST['id']; ?>" />
                                            <input type="hidden" name="is_existinggroup" id="is_existinggroup" value="1" />
						<input type="submit" name="btneditqrcodes" value="Save" id="btneditqrcodes" >
						<!--// 369-->
						<input type="submit" name="btnCancelqrcodegroup"  value="Cancel" >
                        <!--// 369-->
					</td>
				  </tr>
		 
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<script>
  $("#disp_new_group").click(function(){
      $("#div_newgroup").css("display","block");
      $("#div_existing_group").css("display","none");
      $("#is_existinggroup").val("0");
  });
  $("#disp_existing_group").click(function(){
      $("#div_newgroup").css("display","none");
      $("#div_existing_group").css("display","block");
      $("#is_existinggroup").val("1");
  });
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
