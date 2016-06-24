<?
header('Content-type: text/html; charset=utf-8');
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where = array();
function getmainmercahnt_id($id)
{
  //  echo "<br />In".$id;
    //$objDB = new DB();
   $Sql = "select merchant_parent from merchant_user where id=".$id;
             $rs =$objDB->execute_query($Sql);
      //     echo "<br />".$rs->fields['merchant_parent']."---<br />";
            if($rs->fields['merchant_parent'] == 0)
            {
               // echo "<br />In if".$id;
              //  echo $id;
	      
                return $id;
                
            }
            else
            {
               //  //$objDB = new DB();
            //   echo "In else".$rs->fields['merchant_parent'];
                //$mainid= $rs->fields['merchant_parent'];
              return   getmainmercahnt_id($rs->fields['merchant_parent']);
                //call_user_func("get_main_merchant_id",$mainid);
                
            }
}
if(isset($_REQUEST['active']))
{
    $array_values = $where_clause = array();
    $array_values['active'] = $_REQUEST['active'];
    $where_clause['id'] = $_REQUEST['id'];
    $objDB->Update($array_values, "giftcard_merchant_user", $where_clause);
}
$sql = "select * , (select count(*)  from giftcards g where g.merchant_id = gm.id) as total from giftcard_merchant_user gm order by gm.id desc";
$RS = $objDB->Conn->Execute($sql);
//echo base64_decode("MTIzNDU2");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	 <script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>
	<style type="text/css" title="currentStyle">
			@import "<?php echo ASSETS_CSS?>/a/demo_page.css";
			@import "<?php echo ASSETS_CSS?>/a/demo_table.css";
		</style>
		
		<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#example').dataTable( {
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					 "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                                        "iDisplayLength" : 10,
					
				} );
			} );
		</script>
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
	<form action="" method="post">
	<h2>Manage Gift Card merchants </h2>
		<div style="margin-left: 2px; width: 99%;">
			
			
		</div>
		<table align="right"  cellspacing="10px" >
	<tr><td><a href="add-giftcardmerchant.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a></td>
<!--	<td><a href="edit-category.php?id=<?=$Row['id']?>"><img src="<?php echo ASSETS_IMG; ?>/a/icon-edit.png"></a></td>
	<td><a href="#"><img src="<?php echo ASSETS_IMG; ?>/a/icon-delete.png"></a></td>-->
	</tr>
	
	</table>
	<div style="color:#FF0000; "><?=$_SESSION['msg'];?></div>
		<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
			<thead>
		  <tr>
			<th width="40%" align="left">Name</th>	
			<th width="15%" align="left">Total No. of Gift Cards</th>
			
			<th width="45%" align="left"></th>
            
		  </tr>
		  </thead>
			<tbody>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			              <td align="left">
						<?php
						
							
							
							echo $Row['merchant_name'];
						
						?>
						</td>
			
			<td><?php echo $Row['total'] ; ?></td>
			<td><a href="<?=WEB_PATH?>/admin/add-giftcard.php?merid=<?=$Row['id']?>">Add Gift Card</a>&nbsp; | &nbsp;
			<a href="<?=WEB_PATH?>/admin/edit-giftcardmerchant.php?id=<?=$Row['id']?>">Edit</a>&nbsp; | &nbsp;
            <?php
                                if($Row['active']==1)
                                {
                            ?>
                                    <a href="giftcard-merchants.php?active=0&id=<?=$Row['id']?>">Deactivate</a>        
                            <?php                                  
                                }
                                else
                                {
                                ?>
                                    <a href="giftcard-merchants.php?active=1&id=<?=$Row['id']?>">Activate</a>        
                            <?php
                                }
                            ?>
		   </td>
		  </tr>
		  <?
		  }
		  ?>
		 
		  <?
		  }else{
		  ?>
		  <!--<tr>
			<td colspan="6" align="left">No Merchant is Found.</td>
		  </tr>-->
		  <?
		  }
		  ?>
		  </tbody>
		
		</table>
	  </form>
	                     <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>

<?
$_SESSION['msg'] = "";
?>
</body>
</html>
