<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$where_arr= array();

$RS = $objDB->Show("qrcode_group",$where_arr);

if(isset($_REQUEST['action']) && $_REQUEST['action']== "activate")
{
     $Sql = "Update qrcode_group set active=1 where id=".$_REQUEST['id'];
         $objDB->Conn->Execute($Sql);
           header("Location:QRcodegrouplist.php");
}
else if(isset($_REQUEST['action']) && $_REQUEST['action']== "deactivate")
{
     $Sql = "Update qrcode_group set active=0 where id=".$_REQUEST['id'];
     
     $objDB->Conn->Execute($Sql);
     
           header("Location:QRcodegrouplist.php");
}
else if(isset($_REQUEST['action']) && $_REQUEST['action']== "delete")
{
	$Sql = "delete from qrcode_group where id=".$_REQUEST['id'];
	//echo $Sql;
    $objDB->Conn->Execute($Sql);
     
           header("Location:QRcodegrouplist.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
</head>

<body>
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
		  <h2>Manage QR Code Groups</h2>
	<form action="" method="post">
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	<tr><td><a href="generateQrcode.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png">Add QR Code Group</a></td>
	
	
	</tr>
	
	</table>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   class="tableAdmin">
		   <tr>
			<td colspan="4">&nbsp;<span style="color:#FF0000; "><?=$_SESSION['msg']?></span></td>
			
		  
		  </tr>
		  <tr>
			<th width="40%" align="left">QR Code Groups</th>
			<th width="20%" align="left">No. Of QR Code</th>
  			<th width="20%" align="left">Assigned Merchant</th>
			<th width="20%" align="left">&nbsp;</th>
<!--		    <th width="10%" align="left">&nbsp;</th>-->
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td><?=$Row['group_name']?></td> 
		<td><?php 
                //$Row['no_of_qrcodes']
                $Sql = "select * from qrcodegroup_qrcode where qrcodegroup_id=".$Row['id'];
                $records = $objDB->Conn->Execute($Sql);
                echo $records->RecordCount();
                ?> </td>
                <?php 
                $array = array();
                $array['id']=$Row['merchant_id'];
                $RS_merchantinfo = $objDB->Show("merchant_user",$array); ?>
        <?php  if($Row['merchant_id'] != 0){ ?>
        <td><?php  echo $RS_merchantinfo->fields['business']."-".$RS_merchantinfo->fields['firstname']." ".$RS_merchantinfo->fields['lastname']; ?></td>
                <?php }else{
                    echo "<td>Super Admin</td>";
                }
                
                
                ?>
                
              
        <td>
            <?php if($Row['merchant_id'] != 0) { ?>
            <a href="<?php echo WEB_PATH.'/admin/edit_qrcodegroup.php?id='.$Row['id']; ?>">edit</a>
            <?php $sql = "Select * from qrcodegroup_qrcode where  qrcodegroup_id = ".$Row['id'];
                $data =$objDB->Conn->Execute($sql);
                if($data->RecordCount() == 0){ 
                    if($Row['active'] == 1){
                              ?>
            &nbsp;|&nbsp;<a href="<?php echo WEB_PATH.'/admin/QRcodegrouplist.php?action=deactivate&id='.$Row['id']; ?>">deactivate</a>
            <?php  } else { ?>
            &nbsp;|&nbsp;<a href="<?php echo WEB_PATH.'/admin/QRcodegrouplist.php?action=activate&id='.$Row['id']; ?>">activate</a>
            <?php  }
			?>
			&nbsp;|&nbsp;<a href="<?php echo WEB_PATH.'/admin/QRcodegrouplist.php?action=delete&id='.$Row['id']; ?>">delete</a>
			<?php			} ?>
            <?php } ?>
        </td>
<!--			
		    <td align="left">
			<a href="edit-marketingmaterial.php?id=<?=$Row['id']?>">Edit</a>
                        <a href="marketingmaterial.php?id=<?=$Row['id']?>&action=delete">Delete</a>
			</td>-->
		  </tr>
		  <?
		  }
		  ?>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
                        <td>&nbsp;</td>
<!--		  <td>&nbsp;</td>-->
		  </tr>
		  <?
		  }else{
		  ?>
		  <tr>
			<td colspan="3" align="left">No QRcode is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<?php $_SESSION['msg']=""; ?>

</body>
</html>
