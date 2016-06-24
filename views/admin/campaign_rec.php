<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
if(isset($_REQUEST['action'])){
        if($_REQUEST['action']=="Approve")
        {
            $array_values = $where_clause = array();
            $array_values['visible'] = 1;
            $where_clause['id'] = $_REQUEST['camp_id'];
            $objDB->Update($array_values, "campaigns", $where_clause);
            header("Location: ".WEB_PATH."/admin/campaign_rec.php?camp_id=".$_REQUEST['camp_id']);
            exit();
        }
        if($_REQUEST['action']=="unApprove")
        {
            $array_values = $where_clause = array();
            $array_values['visible'] = 0;
            $where_clause['id'] = $_REQUEST['camp_id'];
            $objDB->Update($array_values, "campaigns", $where_clause);
            header("Location: ".WEB_PATH."/admin/campaign_rec.php?camp_id=".$_REQUEST['camp_id']);
            exit();
        }
        if($_REQUEST['action']=="waiting")
        {
            $array_values = $where_clause = array();
            $array_values['visible'] = 2;
            $where_clause['id'] = $_REQUEST['camp_id'];
            $objDB->Update($array_values, "campaigns", $where_clause);
            header("Location: ".WEB_PATH."/admin/campaign_rec.php?camp_id=".$_REQUEST['camp_id']);
            exit();
        }
        if($_REQUEST['action']=="block")
        {
            $array_values = $where_clause = array();
            $array_values['visible'] = -1;
            $where_clause['id'] = $_REQUEST['camp_id'];
            $objDB->Update($array_values, "campaigns", $where_clause);
            header("Location: ".WEB_PATH."/admin/campaign_rec.php?camp_id=".$_REQUEST['camp_id']);
            exit();
        }
}
$where_clause['id'] = $_REQUEST['camp_id'];
$RS = $objDB->Show("campaigns",$where_clause);
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
<!--            <a href="#">Back to merchant</a>-->
                        <div style="float: left;"><h2>Campaign Summary</h2></div>
                        <div style="float: right; clear: none">
                            <a href="<?=WEB_PATH?>/admin/merchant_rec.php?id=<?=$_REQUEST['camp_id']?>">Back to Merchant</a></div>
                        <div style="float: right; clear: both">
                    <?  
                    $where_clause['id'] = $_REQUEST['camp_id'];
                    $RS_cl= $objDB->Show("campaigns",$where_clause);
                    if($RS_cl->RecordCount()>0){ while($Row_cl = $RS_cl->FetchRow()){ ?>
                            <? if($Row_cl['visible'] == 1){?>
                            <a href="<?=WEB_PATH?>/admin/campaign_rec.php?camp_id=<?=$_REQUEST['camp_id']?>&action=unApprove">UN-Approve</a> /
                            <? }else{?>
                            <a href="<?=WEB_PATH?>/admin/campaign_rec.php?camp_id=<?=$_REQUEST['camp_id']?>&action=Approve">Approve</a> /
                            <? }?>
                            <? if($Row_cl['visible'] != 2){?>
                            <a href="<?=WEB_PATH?>/admin/campaign_rec.php?camp_id=<?=$_REQUEST['camp_id']?>&action=waiting">Waiting</a> / 
                            <?}?>
                            <? if($Row_cl['visible'] != -1){?>
                            <a href="<?=WEB_PATH?>/admin/campaign_rec.php?camp_id=<?=$_REQUEST['camp_id']?>&action=block">Block</a>
                            <?}?>
                       </div>   
                    <?  }  }  ?>     
                    </div>
            <div class="mer_chant">                    
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
				$where_clause = array();
				$where_clause['id'] = $Row['created_by'];
				$RSMerchant = $objDB->Show("merchant_user", $where_clause);
		  ?>
                <div class="mer_chant_1" style="float: left; width: 500px">                    
                    <p><span>Created By</span><span>&nbsp;:&nbsp;</span><?=$RSMerchant->fields['firstname']." ".$RSMerchant->fields['lastname']?></p>
                    <p><span>Campaign Name</span><span>&nbsp;:&nbsp;</span><?=$Row['title']?></p>
                    <p><span>Description</span><span>&nbsp;:&nbsp;</span><?=$Row['description']?></p>
                    <p><span>Campaign Start Date</span><span>&nbsp;:&nbsp;</span><?=date("m-d-Y", strtotime($Row['start_date']))?></p>
                    <p><span>Campaign Expire Date</span><span>&nbsp;:&nbsp;</span><?=date("m-d-Y", strtotime($Row['expiration_date']))?></p>
			<p><span>Campaign Status</span><span>&nbsp;:&nbsp;</span>
			<?
			if($Row['visible'] == 1)
                        { echo "Yes";} 
                        else if($Row['visible'] == 2){
                            echo "Waiting";
                        }
                        else if($Row['visible'] == -1){
                            echo "Block";
                        }
                        else
                        { echo "No";}			
                        ?></p>
                 </div>
                <div class="mer_chant_1" style=" float: left; height: 241px; padding: 5px; width: 250px;"><img style="max-width: 200px;" src="<?=ASSETS_IMG ?>/m/campaign/<?=$Row['business_logo']?>" /> </div>
		<?}}?>
                    
            </div>
	       <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
