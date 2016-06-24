<?php
header('Content-type: text/html; charset=utf-8');
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where = array();
if(isset($_REQUEST['action']))
{
        if($_REQUEST['action'] == "delete"){
                $array_where['id'] = $_REQUEST['id'];
                $objDB->Remove("merchant_user", $array_where);

				$array_where1['created_by'] = $_REQUEST['id'];
                $objDB->Remove("locations", $array_where1);
				
				$array_where2['merchant_id'] = $_REQUEST['id'];
                $objDB->Remove("merchant_groups", $array_where2);
				
				$array_where3['created_by'] = $_REQUEST['id'];
				$objDB->Remove("campaigns", $array_where3);
				
				$array_where4['merchant_id'] = $_REQUEST['id'];
				$objDB->Remove("campaign_groups", $array_where4);
				
                header("Location: ".WEB_PATH."/admin/merchants.php");
                exit();
        }
}
//$array['id'] = 20;
//$RS = $objDB->Show("merchant_user",$array);
//echo $RS->fields['email']."<br/>";
//echo $RS->fields['password'];
 function make_seed() {
                      list($usec, $sec) = explode(' ', microtime());
                      return (float) $sec + ((float) $usec * 100000);
                    }

if(isset($_REQUEST['btnApprove']))
{
   
	foreach($_REQUEST['id'] as $id)
        {
		$array_values = $where_clause = array();
		
                $array['id'] = $id;
                $RS = $objDB->Show("merchant_user",$array);
                
                
                if($RS->fields['approve']==0)
                {
                    $password_length = 9;

                   

                    srand(make_seed());

                    $alfa = "1234567890qwertyuiopasdfghjklzxcvbnm";
                    $token = "";
                    for($i = 0; $i < $password_length; $i ++) {
                      $token .= $alfa[rand(0, strlen($alfa))];
                    }    
                    
                    
               // echo $RS->fields['email'];
                    $mail = new PHPMailer();
                                if($RS->fields['firstname'] == "" && $RS->fields['lastname'] == "")
                                {
                                    $nm = "";
                                }
                                else{
                                    $nm = $RS->fields['firstname']." ".$RS->fields['lastname'];
                                }
								$nm = $RS->fields['firstname'];
				$body='<body bgcolor="#e4e4e4" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" style="-webkit-font-smoothing: antialiased;width:100% !important;background:#e4e4e4;-webkit-text-size-adjust:none;">
	
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#e4e4e4">
    <tr>
        <td bgcolor="#e4e4e4" width="100%">
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="table"
                style="background: #D2D2D2; border: 2px solid #ddd; padding: 20px; border-radius: 10px;">
                <tr>
                    <td width="600" class="cell">
                        <table width="600" cellpadding="0" cellspacing="0" border="0" class="table">
                            <tr>
                                <td width="250" class="logocell">
									<img src="'.ASSETS_IMG.'/m/logo-merchant.png" width="250" height="30" alt="Scanflip Merchant"
                                        style="-ms-interpolation-mode: bicubic; padding: 20px 0;">
                                </td>
                            </tr>
                        </table>
                        <repeater>
			<layout label="New feature">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td bgcolor="#F37E0A" nowrap><img border="0" src="'.ASSETS_IMG.'/c/spacer.gif" width="5" height="1"></td>
				<td width="100%" bgcolor="#ffffff">
			
					<table width="100%" cellpadding="20" cellspacing="0" border="0">
					<tr>
						<td bgcolor="white" class="contentblock">
							<h4 style="color:black; font-size:16px; line-height:24px; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; margin-top:0; margin-bottom:10px; padding-top:0; padding-bottom:0; font-weight:normal;"><strong><singleline label="Title">Hi '.$nm.',</singleline></strong></h4>
							<multiline label="Description">
							<p style=" color: black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">
							Your merchant account has been approved and activated. To view your dashboard and manage account, please <a href="'.WEB_PATH.'/merchant/register.php">login</a> to your scanflip account with your registered email and password.<br/><br/>
							To protect your account, please keep your password safe. We recommend that you change your password periodically.</p></multiline>                 
                    		<h5><p style=" color: black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">Thank You,<br/>Scanflip Support Team.</p></h5>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>  
			</layout>
		</repeater>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>';				
				/*				
                $body = "Hi ".$nm.",<br/>";
				$body .= "<p>Your merchant account has been approved and activated. To view your dashboard and manage account, please <a href='".WEB_PATH."/merchant/register.php'>login</a> to your scanflip account with your registered email and password.<br/><br/>"; 
				$body .= "To protect your account, please keep your password safe. We recommend that you change your password periodically.</p>";
				
				//$body .= "<p>Your temporary Password is <b>".$token."</b> </p>";
                                $body .= "<p>Thank You,<br/>Scanflip Support Team.</p>";
                */                
				$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
				$mail->AddAddress($RS->fields['email']);
				$mail->From = "no-reply@scanflip.com";
				$mail->FromName = "ScanFlip Support";
				$mail->Subject    = "Scanflip Account Activation";
				$mail->MsgHTML($body);
                               //echo $body;
				$mail->Send();
                                
                    $array_values['approve'] = 1;
                    //$array_values['password'] = md5($token);
                    
                    $where_clause['id'] = $id;
                    $objDB->Update($array_values, "merchant_user", $where_clause);
                     
                }    
                
	}

}

function getmainmercahnt_id($id)
{
    //echo "<br />In".$id;
    //print_r($objDB);
    //exit();
    $objDB = new DB();
   $Sql = "select merchant_parent from merchant_user where id=".$id;
             $rs =$objDB->execute_query($Sql);
             //$rs = $objDB->Conn->Execute($Sql);
      //     echo "<br />".$rs->fields['merchant_parent']."---<br />";
            if($rs->fields['merchant_parent'] == 0)
            {
                //echo "<br />In if 0";
              //  echo $id;
	      
                return $id;
                
            }
            else
            {
				//echo "<br />In else ";
               //  //$objDB = new DB();
            //   echo "In else".$rs->fields['merchant_parent'];
                //$mainid= $rs->fields['merchant_parent'];
              return  getmainmercahnt_id($rs->fields['merchant_parent']);
                //call_user_func("get_main_merchant_id",$mainid);
                
            }
}
$RS = $objDB->Show("merchant_user");
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
					"aoColumns": [ { "bSortable": false },
						null,
						null, 
						null, 
						null, 
						null,
						null,
						 { "bSortable": false },
						null
						]
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
	<h2>Merchants</h2>
		<div style="margin-left: 2px; width: 99%;">
			<div style="float: left; margin-bottom: 10px;">
				<input type="submit" value="Approve" name="btnApprove" id="btnApprove">
			</div>
			
		</div>
		<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
			<thead>
		  <tr>
			<th width="10%">&nbsp;</th>
			
			<th width="25%" align="left">Business Name</th>	
			<th width="19%" align="left">Name</th>
			<th width="15%" align="left">Parent</th>
			
			<th width="5%" align="left"></th>
            <th width="7%" align="left"></th>
			
			<th width="10%" align="left">Account Setup</th>
			<th width="10%" align="left">Email Verified</th>
			<th width="10%" align="left">Approved</th>
		  </tr>
		  </thead>
			<tbody>
		  <?
		  //echo $RS->RecordCount();
		  //exit();
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="center"><input type="checkbox" name="id[]" value="<?=$Row['id']?>"  /></td>
                        <td align="left"><a href="<?=WEB_PATH?>/admin/merchant_rec.php?id=<?=$Row['id']?>">
						<?php
							//echo "1";
							$mid=getmainmercahnt_id($Row['id']);
							//echo "2";
							$arr_mm=array();
							$arr_mm['id'] = $mid;
							$RS_mm = $objDB->Show("merchant_user", $arr_mm);
							echo $RS_mm->fields['business'];
						
						?>
						</a></td>
			<td align="left"><?=$Row['firstname']." ".$Row['lastname']?></td>
			<td align="left">
			<?
			if($Row['merchant_parent'] == 0){
				echo "---";
			}else{
				$array_parent['id'] = $Row['merchant_parent'];
				$RSParent = $objDB->Show("merchant_user", $array_parent);	
				echo $RSParent->fields['firstname']." ".$RSParent->fields['lastname'];
			}
			?>
			</td>
			
			<td><a href="<?=WEB_PATH?>/admin/merchant_detail.php?id=<?=$Row['id']?>">View</a></td>
            <td><a href="<?=WEB_PATH?>/admin/merchants.php?id=<?=$Row['id']?>&action=delete">Delete</a></td>
			
			<td align="left">
			<?
			if($Row['profile_complete'] == 1) echo "Yes"; else echo "No";
			?>
			</td>
			<td align="left">
			<?
			if($Row['email_verified'] == 1) echo "Yes"; else echo "No";
			?>
			</td>
			<td align="left">
			<?
			if($Row['approve'] == 1) echo "Yes"; else echo "No";
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


</body>
</html>
