<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

        
$where_clause['id'] = $_REQUEST['id'];
$RS = $objDB->Show("merchant_user",$where_clause);

//$Sql = "SELECT cu.* , ms.group_id FROM `merchant_subscribs` ms , customer_user cu WHERE cu.id = ms.`user_id` and ms.`merchant_id` = ".$_REQUEST['id'];
$Sql="SELECT distinct (ss.customer_id),cu.emailaddress,cu.firstname,cu.lastname from subscribed_stores ss,locations l,customer_user cu
where  subscribed_status=1 and l.id=ss.location_id and ss.customer_id=cu.id and cu.active=1 and created_by=".$_REQUEST['id'];
$RS_subscribed_user = $objDB->execute_query($Sql);
//echo base64_decode("MTIzNDU2");
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
                    <div style="display: block; overflow: hidden;">
                        <div style="float: left;"><h2>Merchants</h2></div>
                        <div style="float: right; clear: none">
                            <a href="<?=WEB_PATH?>/admin/merchants.php">Back to list</a></div>
                        <div style="clear: both">
                   
                    </div>
        <div class="mer_chant">
                <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
        <div class="mer_chant_1">
            <p><span>Merchant name</span><span>&nbsp;:&nbsp;</span><?=$Row['firstname']." ".$Row['lastname']?></p>
            <p><span>Merchant Email</span><span>&nbsp;:&nbsp;</span><?=$Row['email']?></p>
            <p><span>Merchnat Address</span><span>&nbsp;:&nbsp;</span><?=$Row['address']?>, <?=$Row['city']?>, <?=$Row['state']?>, <?=$Row['zipcode']?>, <?=$Row['country']?></p>
            <p><span>Approve</span><span>&nbsp;:&nbsp;</span><?
			if($Row['approve'] == 1)
                        { echo "Yes";} 
                        else if($Row['approve'] == 2){
                            echo "Waiting";
                        }
                        else if($Row['approve'] == -1){
                            echo "Block";
                        }
                        else
                        { echo "No";}
			?></p>
        </div>
       
        <div class="mer_chant_2">
            <?php
//            $array_values = $where_clause_v = array();
//            $where_clause_v['created_by'] = $_REQUEST['id'];
//            $where_clause_v['visible'] = "1";
//            $RS_v = $objDB->Show("campaigns",$where_clause_v);
            //print_r($Row);
			//echo $Row['merchant_parent'];
			
            if($Row['merchant_parent'] == 0)
            {
                // 369
                //$Sql = "SELECT * FROM campaigns WHERE (created_by = '$_SESSION[merchant_id]' or created_by in ( select id from merchant_user where merchant_parent = ". $_SESSION[merchant_id] ." )) $Where ORDER BY id DESC";
                $arr=file(WEB_PATH.'/admin/process.php?btnGetAllCampaignOfMerchant=yes&action=active&mer_id='.$_REQUEST['id'].'&parent=0');
                if(trim($arr[0]) == "")
                {
                        unset($arr[0]);
                        $arr = array_values($arr);
                }
                $json = json_decode($arr[0]);
                $total_records= $json->total_records;
                $records_array = $json->records;
				//echo $json->query;
                // 369
            }
            else{
                //$Sql = "SELECT * FROM campaigns WHERE created_by = '$_SESSION[merchant_id]'  $Where ORDER BY id DESC";
                $arr=file(WEB_PATH.'/admin/process.php?btnGetAllCampaignOfMerchant=yes&action=active&mer_id='.$_REQUEST['id']);
                if(trim($arr[0]) == "")
                {
                        unset($arr[0]);
                        $arr = array_values($arr);
                }
                $json = json_decode($arr[0]);
                $total_records= $json->total_records;
                $records_array = $json->records;
            }
            
            
            
            
            ?>
                <p><span>Active Campaigns</span><span>&nbsp;:&nbsp;</span>
                <? echo $total_records; //$RS_v->RecordCount()?></p>
             <?
             
//                if($RS_v->RecordCount()>0){
//		  	while($Row_v = $RS_v->FetchRow()){
//                            echo "<p><a href='".WEB_PATH."/admin/campaign-detail.php?id=".$Row_v['id']."'>".$Row_v['title']."</a></p>";          	
//             } }
              if($total_records>0){
       
       
				foreach($records_array as $Row1)
				{
                   	echo "<p><a href='".WEB_PATH."/admin/campaign-detail.php?id=".$Row1->id."'>".$Row1->title."</a></p>"; 
                }
              }
             
            ?>
        </div>
        
        <div class="mer_chant_3">
            <?
            $array_values = $where_clause_loc = array();
            $where_clause_loc['created_by'] = $_REQUEST['id'];
            $where_clause_loc['active'] = "1";
            $RS_loc = $objDB->Show("locations",$where_clause_loc);?>
                <p><span>Active Locations</span><span>&nbsp;:&nbsp;</span><?echo $RS_loc->RecordCount()?></p>
             <?if($RS_loc->RecordCount()>0){
		  	while($Row_loc = $RS_loc->FetchRow()){
                            //echo "<p>".$Row_loc['location_name']."</p>";
                             echo "<p><a href='location-detail.php?id=".$Row_loc['id']."'>".$Row_loc['location_name']."</a></p>";

		  ?>
             	
            <? } }
            ?>
        </div>
        
		<div class="mer_chant_3">
   
	      <p><span>Number of subscribed user</span><span>&nbsp;:&nbsp;</span>
          <? 
		  	echo $RS_subscribed_user->RecordCount()
		  ?>
          </p>
		    <?
		      if($RS_subscribed_user->RecordCount()>0){ ?>
		    <table width="100%">
		     <tr>
	            <th width="37%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;">Email</th>
		    <th width="27%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;">Name</th>
		   <!-- <th width="8%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;">Group </th>
		    <th width="10%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;">subscribed campaigns</th>-->
		    <th width="10%" align="left" style=" font-family: Arial,Helvetica,sans-serif;font-size: 0.8em;">subscribed Locations</th>
	
		  </tr>
		     
		    <?php
		
		    while($Row = $RS_subscribed_user->FetchRow()){ ?>
                          <tr style="font-size: 0.8em;">
				<td align="left"><?=$Row['emailaddress']?></td>
			<td align="left"><?=$Row['firstname']." ".$Row['lastname']?></td>
			
			<!--<td align="left">
                            <?php 
                                $where_clause1['id'] = $Row['group_id'];
                                $RS1 = $objDB->Show("merchant_groups",$where_clause1);
                                echo $RS1->fields['group_name'];
                           ?>
                       </td>
		    <td align="left">
			<?
			$query  = "SELECT count(*) total FROM customer_campaigns where  customer_id= ".$Row['id']." and campaign_id in (select id from campaigns where created_by = ".$_REQUEST['id']."  )";
			$total_campaigns = $objDB->execute_query($query);
			echo $total_campaigns->fields["total"];
		    ?>
            </td>-->
		    <td align="left"><?
			$query  = "SELECT * from subscribed_stores where customer_id=".$Row['customer_id']." and subscribed_status=1";
			$total_locations = $objDB->execute_query($query);
			echo $total_locations->RecordCount();
		    ?></td>


			</td>
		  </tr>
                         <?php } ?>
			 </table>
		    <?php }else{ //echo "<p> Yet there is no subscribed user.</p>";
			} ?>
        </div>
             <?  }  }  ?>          
        </div>
        <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
