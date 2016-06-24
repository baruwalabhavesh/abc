<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

if(isset($_REQUEST['id'])){
    $l_array = array();
    $l_array['id']= $_REQUEST['id'];
    $RS = $objDB->Show("locations", $l_array);
    
    $array_where = array();
    $array_where['id'] = $RS->fields['created_by'];
    $RS_user = $objDB->Show("merchant_user", $array_where);
    
    $sql = "SELECT c.* FROM campaigns c , campaign_location cl WHERE c.id = cl.campaign_id AND cl.location_id = ".$_REQUEST['id'];
    $RS_campaigns =  $objDB->execute_query($sql);
    
    //$Sql = "SELECT * FROM subscribed_stores ss , customer_user cu WHERE ss.customer_id = cu.id AND ss.location_id =".$_REQUEST['id'];
	$Sql="SELECT * from subscribed_stores ss,customer_user cu where location_id=".$_REQUEST['id']." and subscribed_status=1 and cu.id=ss.customer_id and cu.active=1";
    $RS_locations =  $objDB->execute_query($Sql);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<style>
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
</style>
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
<h2>Location Summary</h2>
</div><div style="float: right; clear: none">
                            <a href="<?=WEB_PATH?>/admin/store-locations.php">Back to list</a></div>
		    <div style="clear: both"></div>
	 <div class="mer_chant">
                  <div class="mer_chant_div">
		    <table width="100%">
			<tr>
			    <td colspan="2">
				<p><span>Merchant Business Name</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['location_name']?></p>	
			    </td>
			
			</tr>
			<tr>
			    <td colspan="2">
				<p><span>Location Address</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['address'].", ".$RS->fields['city'].", ".$RS->fields['state'].", ".$RS->fields['country'].", ".$RS->fields['zip']?></p>
			    </td>
			
			</tr>
			<tr>
			    <td width="50%">
				<p><span>Website</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['website']?></p>
			    </td>
			    <td>
				<p><span>Email</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['email']?></p>
			    </td>
			</tr>
			<tr>
			    <td>
				<p><span>Phone number</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['phone_number']?></p>
			    </td>
			    <td>
				<p><span>Fax number</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['fax_number']?></p>
			    </td>
			</tr>
			<tr>
			    <td>
				<p><span>Business Admin</span><span>&nbsp;:&nbsp;</span><?=$RS_user->fields['firstname']." ".$RS_user->fields['lastname']?></p>
			    </td>
			    <td>
				<p><span>Created Date</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['created_date']?></p>
			    </td>
			</tr>
			<tr>
			    <td>
				<p><span>Modified By</span><span>&nbsp;:&nbsp;</span><?=$RS_user->fields['firstname']." ".$RS_user->fields['lastname']?></p>
			    </td>
			    <td>
				<p><span>Modified Date</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['modified_date']?></p>
			    </td>
			</tr>
			<tr>
			    <td colspan="2">
				<p><span>Location Status</span><span>&nbsp;:&nbsp;</span><?php if($RS->fields['active'] == 1) echo "Active"; else echo "Inactive"; ?></p>
			    </td>
			</tr>  <?php $array_where['id'] = $RS->fields['modified_by'];
    $RS_user = $objDB->Show("merchant_user", $array_where); ?>
			<tr>
			    <td>
				 <p><span>Latitude</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['latitude']?></p>
			    </td>
			    <td>
				<p><span>Longitude</span><span>&nbsp;:&nbsp;</span><?=$RS->fields['longitude']?></p>
			    </td>
			</tr>
			<tr>
			    <td>
				 <p><span>Location Profile Image</span><span>&nbsp;:&nbsp;</span><?//=$RS->fields['picture']?></p>
				 <img src="<?=ASSETS_IMG ?>/m/location/<?=$RS->fields['picture']?>" style="max-width: 200px;float:left;">
			    </td>
				
			</tr>
			
		    </table>
		
		    
		   
		    
		  </div>
		   <div class="mer_chant_div">
			<p><span>Active Campaigns at Location</span><span>&nbsp;:&nbsp;</span>
			<?php
            	//$RS_campaigns->RecordCount(); 	           	
				
				$arr=file(WEB_PATH.'/admin/process.php?btnGetAllactiveCampaignOfMerchantLocation=yes&loc_id='.$_REQUEST['id']);
				if(trim($arr[0]) == "")
				{
						unset($arr[0]);
						$arr = array_values($arr);
				}
				$json = json_decode($arr[0]);
				echo $total_records= $json->total_records;
				$records_array = $json->records;
				
			?>
            </p>
			
			<?php 
			
			if($total_records>0){
			
			?>
			<table>
			<tr style="font-size: 0.8em;">
			   <!-- <th width="20%" align="left">Merchant</th> -->
			    <th width="40%" align="left">Title</th>
			
			    <th width="12%" align="left">Start Date </th>
			    <th width="15%" align="left">Expire Date </th>
			    <!--<th width="5%" align="center">Approved</th>-->
			</tr>
			
                        <?php 
						foreach($records_array as $Row)
						
						{
							$where_clause = array();
							$where_clause['id'] = $Row->created_by;
							$RSMerchant = $objDB->Show("merchant_user", $where_clause);
                    ?>
                            <tr style="font-size: 0.8em;">
                           <!-- <td align="left"><?=$RSMerchant->fields['firstname']." ".$RSMerchant->fields['lastname']?></td> -->
                            <td align="left"><a href="campaign-detail.php?id=<?=$Row->id?>" ><?=$Row->title?></a></td>
                            
                            <td align="left"><?=date("m-d-Y", strtotime($Row->start_date))?></td>
                            <td align="left"><?=date("m-d-Y", strtotime($Row->expiration_date))?></td>
                            <!--<td align="center">
                            <?
                            if($Row->visible == 1) echo "Yes"; else echo "No";
                            ?>
                            </td>-->
		  					</tr>
			

                  <?php }
				  echo "</table>";
                  } ?>

		   </div>
           
		    <div class="mer_chant_div">
			<p><span>Subscribed users of this location</span><span>&nbsp;:&nbsp;</span><?=$RS_locations->RecordCount();?></p>
			<?php if($RS_locations->RecordCount() != 0) {
			    ?>
			  
			     <table width="100%">
		     <tr>
			<!--<th width="27%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;">Email</th>-->
		
			<th width="27%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;">Customer Name</th>
		    <th width="17%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;">Subscribed Date</th>
		    <th width="19%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;"></th>
	
		  </tr>
		     <?php
                        while($Row = $RS_locations->FetchRow()){
                    ?>
		        <tr style="font-size: 0.8em;">
			<!--<td align="left"><?=$Row['emailaddress']?></td>    -->
			<td align="left"><?=$Row['firstname']." ".$Row['lastname']?></td>
			<td align="left"><?=$Row['subscribed_date']?></td>
			<td align="left"></td>
			</tr>
		    
                  <?php }
		  echo "</table>";
                  } ?>

		   </div>
            		  
	 </div>

                     <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>

</body>

 
</html>
