<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

if(isset($_REQUEST['id'])){
    /*
    $l_array = array();
    $l_array['id']= $_REQUEST['id'];
    $RS_Loc = $objDB->Show("locations", $l_array);
    
    $array_where = array();
    $array_where['id'] = $RS_Loc->fields['created_by'];
    $RS_Mer = $objDB->Show("merchant_user", $array_where);
    */
    $array_where1 = array();
    $array_where1['customer_id'] = $_REQUEST['id'];
    $RS_sub_store = $objDB->Show("subscribed_stores", $array_where1);
    
    
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<!--<style>
    
#content div.mer_chant {
    border: 1px dashed;
    display: block;
    overflow: hidden;
    padding: 5px;
}
#content div.mer_chant div.mer_chant_div {
    background: none repeat scroll 0 0 #F5F5F5 ;
    border-bottom: 2px solid #FF9900;
    font-size: 18px;
}
#content div.mer_chant div.mer_chant_div p span {
    font-weight: bold;
}
#content p {
    font-family: Arial,Helvetica,sans-serif;
    font-size: 0.8em;
    padding: 0;
}
</style>-->
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
			<div style="float: left;">
				<h2>Subscription Summary</h2>
			</div>
			<div style="float: right; clear: none">
                            <a href="<?=WEB_PATH?>/admin/users.php">Back to list</a>
			</div>
			<div style="clear: both"></div>
		        	 <div class="mer_chant">
                  <div class="mer_chant_div">
                      <?php
                                    $array_where = array();
                                    $array_where['id'] = $_REQUEST['id'];
                                    $user_name = $objDB->Show("customer_user",$array_where);
                                ?>
				<p><span><b>User Name</b></span><span>&nbsp;:&nbsp;</span><?=$user_name->fields['firstname'];?></p>
                                <?php
                                if($RS_sub_store->RecordCount()>0)
                                {
                                ?>
		    <table width="100%">
				 <tr>
                <th align="left"  >Business Name</th>
		<th align="left"  >location Address</th>
		<th align="left"  >First Redemption date and time</th>
		<th align="left" >Is Subscribe</th>
		
                        </tr>		
                        <?php
                        while($Row = $RS_sub_store->FetchRow()){
                           //echo $Row["id"]; 
                        
                        ?>
			<tr>
                            <td >
                                <?php
                                     $array_whereloc = array();
                                    $array_whereloc['id'] = $Row['location_id'];
                                    $location_id = $objDB->Show("locations",$array_whereloc);
                                    
                                   $array_wheremer = array();
                                    $array_wheremer['id'] = $location_id->fields['created_by'];
                                    $merchant = $objDB->Show("merchant_user",$array_wheremer); 
                                    
                                ?>
				<?=$merchant->fields['business']?>
			    </td>
			    <td >
                                <?=$location_id->fields["location_name"]?>
			    </td>
			   <td>
                               <?php
                               if($Row['first_redeemed_date']!="0000-00-00 00:00:00")
                               {
                                    echo date("Y-m-d g:i:s A", strtotime($Row['first_redeemed_date']));
                               }
                                else 
                               {
                                    echo "";
                               }
                               ?>
			    </td>
                            <td>
				<?php
                                    if($Row['subscribed_status']==1)
                                        echo "Yes";
                                    else {
                                        echo "No";
                                    }
                                ?>
			    </td>
			</tr>
			
			<?php
                        }
                        ?>
			
		    </table>
		<?php
                                }
                                else {
                                    echo "No subscription summary";
                                }
                                ?>
		    
		   
		    
		  </div>
                 	  
		</div>
   
		   
<!--end of Container--></div>


</body>
</html>
