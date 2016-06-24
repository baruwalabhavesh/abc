<?php
/**
 * @uses add new user
 * @used in pages : manage-users.php,my-account-left.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array =array();
$array['created_by'] = $_SESSION['merchant_id'];
$array['active'] = 1;
$RSStore = $objDB->Show("locations", $array);

$array = array();
$array['merchant_id'] = $_SESSION['merchant_id'];
$RSGroups = $objDB->Show("merchant_groups",$array);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Add Location Employee</title>
<?php //require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<script type="text/javascript" src="<?=ASSETS_JS ?>/m/pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/con_pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery.form.js"></script>


<script language="javascript">
function validate_register(){
	var msg="";
	var flag="true";
        var flag_login=0;
        
	if(flag == "true")
	{
		flag_login=1;
		if(document.getElementById("firstname").value == ""){
			
			//alert("Please Enter First Name");
			msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_first_name"];?></div>";
			flag="false";
			//document.getElementById("firstname").focus();
			//return false;
		}
		if(document.getElementById("lastname").value == ""){
			//alert("Please Enter Last Name");
			msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_last_name"];?></div>";
			flag="false";
			//document.getElementById("lastname").focus();
			//return false;
		}
                var flag_email = true;
		if(email_validation(document.getElementById("email").value) == false){
			//alert("Please Enter Valid Email");
			
			msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_valid_email"];?></div>";
			flag="false";
			//document.getElementById("email").focus();
			//return false;
		}
                else{
                    jQuery.ajax({
				type: "POST",
				url: "<?=WEB_PATH?>/merchant/process.php",
				data: 'is_emailaddress_exists=yes&emailaddress='+document.getElementById("email").value,
                                async:false,
				success: function(msg) {
                                    //alert(msg);
                                
                                var obj = jQuery.parseJSON(msg);
                                if(obj.is_exists != 0){
                                 
                                   // msg +="<div>* Email Address Alredy Availble. Try With Diffrent Email Address.</div>";
			//flag="false";
                        flag_email = false;
                        //alert(msg);
                                }
                                }
                                });
                    
                }
                if(! flag_email)
                {
                 // alert("In if condition");
                 msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_email_exist"];?></div>";
			flag="false";
                }
               // alert(msg);
               // return false;
		
		
                        //alert($("#ass_page option:selected").length);
                if($("#ass_page option:selected").length <=0)
                    {
                        msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_atleast_one_permission"];?></div>";
			flag="false";
                    }
                    else
                    {
                    
                    
                           
                             
                              jQuery('.distributionid').each(function(){
                                  
                                    if($("#ass_page .distributionid option:selected").text() != "Add Distribution List")
                                    {
                                        if($("#ass_page .distributionid option:selected").text() == "Copy Distribution List" || $("#ass_page .distributionid option:selected").text() == "Edit Distribution List" || $("#ass_page .distributionid option:selected").text() == "Activate/Deactive Distribution List")
                                            {
                                                 msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                                 flag="false";
                                               return false;
                                            }
                                          else if($("#ass_page .distributionid option:selected").text() == "Edit Distribution ListCopy Distribution ListActivate/Deactive Distribution List")
                                           {
                                               msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                               flag="false";
                                               return false;
                                           }
                                           else if($("#ass_page .distributionid option:selected").text() == "Edit Distribution ListCopy Distribution List")
                                               {
                                                   msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                                   flag="false";
                                               return false;

                                               }
                                           else if($("#ass_page .distributionid option:selected").text() == "Copy Distribution ListActivate/Deactive Distribution List")
                                           {
                                              msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                              flag="false";
                                           return false;

                                           }
                                           else if($("#ass_page .distributionid option:selected").text() == "Edit Distribution ListActivate/Deactive Distribution List")
                                           {
                                              msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                              flag="false";
                                           return false;

                                           }
                                   }  
                              });
                              jQuery('.compaignsclass').each(function(){
                                     if($("#ass_page .compaignsclass option:selected").text() != "Add Campaign")
                                    {

                                          $("#ass_page .compaignsclass option:selected").each(function(){
                                             if($("#ass_page .compaignsclass option:selected").text() == "Copy Campaign" )
                                                 {
                                                      msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_campaign"];?></div>";
                                                      flag="false";
                                                    return false;
                                                 }

                                          });

                                    }
                              });
                              jQuery('.templateclass').each(function(){
                              
                               if($("#ass_page .templateclass option:selected").text() != "Add Template")
                                    {

                                          $("#ass_page .templateclass option:selected").each(function(){
                                             if($("#ass_page .templateclass option:selected").text() == "Delete Template" )
                                                 {
                                                      msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_template"];?></div>";
                                                      flag="false";
                                                    return false;
                                                 }

                                          });

                                    }
                                     
                              });
                              jQuery('.employeeclass').each(function(){
                              
                                if($("#ass_page .employeeclass option:selected").text() != "Add Employee")
                                      {

                                            $("#ass_page .employeeclass option:selected").each(function(){
                                               
                                                 if($("#ass_page .employeeclass option:selected").text() == "Edit Employee" || $("#ass_page .employeeclass option:selected").text() == "Delete Employee")
                                                    {
                                                         msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_employee"];?></div>";
                                                         flag="false";
                                                       return false;
                                                    }
                                                  else if($("#ass_page .employeeclass option:selected").text() == "Edit EmployeeDelete Employee")
                                                   {
                                                       msg +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_employee"];?></div>";
                                                       flag="false";
                                                       return false;
                                                   }
                                            });

                                      }
                                      //alert($("#ass_page .employeeclass option:selected").text()); 
                              });
                              
                              /*var result = items.join(',');
                               var myArray = result.split(',');
                                jQuery.each(myArray, function() {
                                alert(this);
                                    if(this != "add-group.php" || this != "add-compaign.php")
                                        {
                                            alert("Please Select Proper");
                                        }
                                          
                                });*/
                              
                              
                        
                       
                        //if($("#ass_page option:selected").text() == "Add Distribution List")
                        //{
                          //  alert("hi");
                            
                        //}
                    }
		//alert(msg);
		if(flag=="false")
		{
			var head_msg="<div class='head_msg'>Message</div>"
			var content_msg="<div class='content_msg'>"+msg+"</div>";
			var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
			jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
			
			
			jQuery.fancybox({
						content:jQuery('#dialog-message').html(),
						type: 'html',
						openSpeed  : 300,
						closeSpeed  : 300,
						changeFade : 'fast',  
						helpers: {
							overlay: {
							opacity: 0.3
							} // overlay
						}
			});
			return false;
		}
		
	}
        jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'loginornot=true',
                          async:false,
                           success:function(msg)
                           {
                               
                             
                                 var obj = jQuery.parseJSON(msg);
                               
                                 
                                if (obj.status=="false")     
                                {
                                    flag=1;
                                    window.location.href=obj.link;
                                    
                                }
                                
                                
                           }
                           
                     });
                      if(flag == 1)
                        {

                            return false;

                        }
                        else
                        {
                          return true;  
                        }
        
        
	//return true;	
	
	
	
}
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
$(document).ready(function() { 
    // bind form using ajaxForm 
    $('#reg_form').ajaxForm({ 
		beforeSubmit: validate_register,
        dataType:  'json', 
        success:   processRegJson 
    });
    jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
	
});
function processRegJson(data) { 
	 if(data.status == "true"){

  window.location.href='<?=WEB_PATH.'/merchant/manage-users.php'?>';
 }
 else
 {
	//alert(data.message);
			var head_msg="<div class='head_msg'>Message</div>"
			var content_msg="<div class='content_msg'>"+data.message+"</div>";
			var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
			jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
			jQuery.fancybox({
						content:jQuery('#dialog-message').html(),
						type: 'html',
						openSpeed  : 300,
						closeSpeed  : 300,
						changeFade : 'fast',  
						helpers: {
							overlay: {
							opacity: 0.3
							} // overlay
						}
			});
 }
	
     
}
</script>
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
</head>

<body>
	 <div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div>

<!---start header---->
	<div>
		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	
   
	<div id="content">


  <h3><?php echo $merchant_msg["locationemployee"]["Field_add_location_employee_title"];?></h3>
	<form action="process.php" method="post" id="reg_form">
		
				<table width="100%"  border="0" cellspacing="2" cellpadding="2">
				  <tr>
				  	<td></td>
				    <td align="left" class="table_errore_message" id="error_div"><?php if(isset($_REQUEST['msg'])) echo $_REQUEST['msg']; ?></td>
			      </tr>
				  <tr>
					<td width="35%" align="right"><?php echo $merchant_msg["locationemployee"]["Field_first_name"];?></td>
					<td width="65%" align="left">
						<input type="text" name="firstname" id="firstname" size="35" />
					</td>
				  </tr>
				  <tr>
					<td align="right"><?php echo $merchant_msg["locationemployee"]["Field_last_name"];?></td>
					<td align="left">
						<input type="text" name="lastname" id="lastname" size="35"/>
					</td>
				  </tr>
				  <tr>
					<td align="right"><?php echo $merchant_msg["locationemployee"]["Field_email"];?></td>
					<td align="left">
						<input type="text" name="email" id="email" size="35" />
					</td>
				  </tr>
				 
				  
				   
 
  <tr>
    <td align="right"><?php echo $merchant_msg['profile']['Field_phone_no']; ?> </td>

    <td>
	<select name="mobile_country_code" id="mobile_country_code" style="display:none;">
	    <option value="001">001</option>
	</select>
        <input type="text" name="mobileno_area_code" id="mobileno_area_code" class="mobile_code1"  maxlength="3">-
	<input type="text" name="mobileno2" id="mobileno2" class="mobile_code1"  maxlength="3">-
	<input type="text" name="mobileno" id="mobileno" class="mobile_code2"  maxlength="4">
    
    </td>
  </tr>	
				  
                                  <tr>
                                  <td align="right"><?php echo $merchant_msg["locationemployee"]["Field_access_level"];?></td>
                                  <td align="left" style="font-weight:bold;color:#0C69C7;">
                                      <?php 
                                      $arr=file(WEB_PATH.'/merchant/process.php?btnGetMerchantRole=yes&mer_id='.$_SESSION['merchant_id']);
                                                if(trim($arr[0]) == "")
                                                {
                                                        unset($arr[0]);
                                                        $arr = array_values($arr);
                                                }
                                                $json = json_decode($arr[0]);
                                                $total_records1= $json->total_records;
                                                $records_array1 = $json->records;

                                             if($total_records1>0){

                                                foreach($records_array1 as $Row_role)
                                                {
                                                    $Row_role->merchant_user_id;
                                                    $ass_page = unserialize($Row_role->ass_page);
                                                    $ass_role = unserialize($Row_role->ass_role);
                                                }

                                            }
                                            else
                                            {
                                                echo "";
                                            }
                                      
                                      
                                      ?>
                                      <select multiple size="10" name="ass_page[]" id="ass_page">                                          
                                          	<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("redeem-deal.php",$ass_page) || in_array("reports.php",$ass_page) ){ ?>
                                         <optgroup label="My Account" value="my-account.php">My Account
                                             <?php if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("redeem-deal.php",$ass_page)){?><option value="redeem-deal.php">Redeem coupon</option><?php } ?>
					     <?php if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("reports.php",$ass_page)){?><option value="reports.php">Reports</option><?php } ?>
						
                                         </optgroup>
                                          <?php } ?>
                                          <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("manage-groups.php",$ass_page) || in_array("add-group.php",$ass_page)  || in_array("edit-group.php",$ass_page)  || in_array("copy-group.php",$ass_page)   || in_array("delete-group.php",$ass_page) ){ ?>
					 <optgroup label="Manage Distribution List" class="distributionid" value="compaigns.php">Manage Distribution List
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("manage-groups.php",$ass_page)  ){ ?>
<!--                                                    <option value="manage-groups.php">View Distribution List</option>-->
                                                 <?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-group.php",$ass_page)  ){ ?><option value="add-group.php">Add Distribution List</option><?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("edit-group.php",$ass_page) ){ ?>
                                                       <!--<option  value="edit-group.php">Edit Distribution List</option>-->
                                                <?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 ||  in_array("copy-group.php",$ass_page) ){ ?><!--<option  value="copy-group.php">Copy Distribution List</option>--><?php } ?>
                                                <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("delete-group.php",$ass_page) ){ ?><option  value="delete-group.php">Activate/Deactive Distribution List</option><?php } ?>
					</optgroup>
                                          <?php } ?>
                                             <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("compaigns.php",$ass_page) || in_array("add-compaign.php",$ass_page)  || in_array("edit-compaign.php",$ass_page)  || in_array("delete-compaign.php",$ass_page)  ){ ?>
                                        <optgroup label="Compaigns" value="compaigns.php" class="compaignsclass">Campaigns
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("compaigns.php",$ass_page)){ ?>
<!--                                                    <option value="compaigns.php">View Campaigns</option> -->
                                                <?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-compaign.php",$ass_page) ){ ?><option value="add-compaign.php">Add Campaign</option> <?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("edit-compaign.php",$ass_page)){ ?><!--<option value="edit-compaign.php">Edit Campaign</option>--> <?php } ?>
                                                <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("copy-compaign.php",$ass_page)){ ?><option value="copy-compaign.php">Copy Campaign</option> <?php } ?>
                                                
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0  || in_array("delete-compaign.php",$ass_page)  ){ ?>
<!--                                                    <option value="delete-compaign.php">Delete Campaign</option> -->
                                                <?php } ?>
                                        </optgroup>                                          
                                          <?php } ?>
                                              <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("templates.php",$ass_page) || in_array("add-template.php",$ass_page)  || in_array("edit_template.php",$ass_page)  || in_array("delete-template.php",$ass_page)  ){ ?>
					<optgroup label="Templates" value="templates.php" class="templateclass">Templates 
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("templates.php",$ass_page)  ){ ?>
<!--                                                    <option value="templates.php">View Templates</option>-->
                                                <?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-template.php",$ass_page)   ){ ?><option value="add-template.php">Add Template</option><?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 ||  in_array("edit_template.php",$ass_page)   ){ ?><!--<option value="edit_template.php">Edit Template</option>--><?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 ||  in_array("delete-template.php",$ass_page)  ){ ?><option value="delete-template.php">Delete Template</option><?php } ?>
                                          </optgroup>
                                          <?php } ?>
                                         <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("manage-users.php",$ass_page) || in_array("add-user.php",$ass_page)  || in_array("edit-user.php",$ass_page)  || in_array("delete-user.php",$ass_page)  ){ ?>
                                          <optgroup label="Employee" class="employeeclass">Employee
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("manage-users.php",$ass_page)){ ?>
<!--                                                        <option value="manage-users.php">View Employees</option>-->
                                                  <?php } ?>
                                                <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-user.php",$ass_page)    ){ ?><option value="add-user.php">Add Employee</option><?php } ?>
						<?php  if($_SESSION['merchant_info']['merchant_parent'] == 0   || in_array("edit-user.php",$ass_page)  ){ ?><!--<option value="edit-user.php">Edit Employee</option>--><?php } ?>
                                                <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("delete-user.php",$ass_page)  ){ ?><option value="delete-user.php">Delete Employee</option><?php } ?>
					</optgroup>
                                          <?php } ?>
                                      </select>
                                      <?php echo $merchant_msg["locationemployee"]["content_press_ctrl"];?>
                                  </td>
                                  </tr>
				  <!-- T_8 -->
		<tr>
			<td align="right"><?php echo $merchant_msg["locationemployee"]["Field_assigned_location"];?></td>
			<td>
				<?
                                if($_SESSION['merchant_info']['merchant_parent'] == 0){
					if($RSStore->RecordCount()>0){
						?>
						<select name="location_id" id="location_id" >
						<?php
					while($Row = $RSStore->FetchRow()){
					?>
						<option value="<?=$Row['id']?>"> 
                                                    <?php
							if($Row['location_name']!="")
							{
                                                //echo $Row['location_name']."-<br/>";
							}	
							 else {
                                                $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$Row['id']);
                                                if(trim($arr[0]) == "")
                                                     {
                                                             unset($arr[0]);
                                                             $arr = array_values($arr);
                                                     }
                                                     $json = json_decode($arr[0]);
                                                $busines_name  = $json->bus_name;
                                                //echo $busines_name."-<br/>";
                                            }
                                            ?>
                            <?php //echo $Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip']
							$array_where = array();
							$array_where['id'] = $Row['state'];
							$RS_state = $objDB->Show("state", $array_where);

							$array_where = array();
							$array_where['id'] = $Row['city'];
							$RS_city = $objDB->Show("city", $array_where);

							//$Json['aaData'][$k][]=$Row['address'] . ", " . $Row['city'] . ", " . $Row['state'] . ", " . $Row['zip'];
							$location_string = $Row['address'] . ", " . $RS_city->fields['name'] . ", " . $RS_state->fields['short_form'] . ", " . $Row['zip'];
			
							//$location_string = $Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip'];
							$location_string = (strlen($location_string) > 57) ? substr($location_string,0,57).'...' : $location_string;
							echo $location_string;  
							?>
                        </option>
					<?
					  	} ?>
						</select>
						<?php
					  }
                                }
                                else
                                {
                                   
                                    $array_where_user['id'] = $_SESSION['merchant_id'];
                                    $RS_User = $objDB->Show("merchant_user", $array_where_user);
                                    $m_parent = $RS_User->fields['merchant_parent'];
                                    
                                    $media_acc_array = array(); 
                                    $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id']; 
                                    $RSmedia = $objDB->Show("merchant_user_role",$media_acc_array); 
                                    $location_val = $RSmedia->fields['location_access'];


                                    //$sql = "SELECT * FROM locations  WHERE id in (select l.id from locations l where l.created_by=".$m_parent ." and l.id = ".$location_val .")";
                                    //$RSStore = $objDB->execute_query($sql);
                                    $arr=file(WEB_PATH.'/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id='.$m_parent.'&loc_id='.$location_val);
                                    if(trim($arr[0]) == "")
                                    {
                                            unset($arr[0]);
                                            $arr = array_values($arr);
                                    }
                                    $json = json_decode($arr[0]);
                                    $total_records_l= $json->total_records;
                                    $records_array_l = $json->records;
                                    //print_r($records_array_l);
                                    if($total_records_l>0){
					//echo "Sharad";	
					foreach($records_array_l as $Row)
					{
                                            if($Row->location_name!="")
                                                echo $Row->location_name;
                                            else {
                                                $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$Row->id);
                                                if(trim($arr[0]) == "")
                                                     {
                                                             unset($arr[0]);
                                                             $arr = array_values($arr);
                                                     }
                                                     $json = json_decode($arr[0]);
                                                $busines_name  = $json->bus_name;
                                                echo $busines_name;
                                            }
                                            
                                            $array_where = array();
												$array_where['id'] = $Row->state;
												$RS_state = $objDB->Show("state", $array_where);

												$array_where = array();
												$array_where['id'] = $Row->city;
												$RS_city = $objDB->Show("city", $array_where);

												//$Json['aaData'][$k][]=$Row['address'] . ", " . $Row['city'] . ", " . $Row['state'] . ", " . $Row['zip'];
												$location_string = $Row->address . ", " . $RS_city->fields['name'] . ", " . $RS_state->fields['short_form'] . ", " . $Row->zip;
			
                                                //$location_string = $Row->address.", ".$Row->city.", ".$Row->state.", ".$Row->zip;
                                                $location_string = (strlen($location_string) > 57) ? substr($location_string, 0, 57).'...' : $location_string;
                                                
											
							echo $location_string;  
                                            //echo $Row->address.", ".$Row->city.", ".$Row->state.", ".$Row->zip;?>
                                            <input type="hidden" name="location_id" id="location_id"  value="<?php echo $Row->id; ?>" />
                                            
                                        <?php }
                                    }
                                    
                                }
                                $total_records_l="";
                                if($total_records_l>0){
					foreach($records_array_l as $RSStore)
					{
                                            $sub_location_id = $RSStore->id;
                                            ?>
                            
                            <input type="hidden" name="location_id" id="location_id"  value="<?php echo $sub_location_id; ?>" />
                                            <?php
						//echo $RSStore->location_name;
						$location_string = $RSStore->address.", ".$RSStore->city.", ".$RSStore->state.", ".$RSStore->zip;
							$location_string = (strlen($location_string) > 57) ? substr($location_string,0,57).'...' : $location_string;
							echo $location_string; 
						//echo $RSStore->address.", ".$RSStore->city.", ".$RSStore->state.", ".$RSStore->zip;
					}
				  }
					  ?>
			</td>
		</tr>
		<!-- T_8 -->
		<!--
				<tr>
				    <td align="right">Assign Role:</td>
				    <td align="left">						
                                        <?php 
                                        if($_SESSION['merchant_info']['merchant_parent'] == 0 )
                                        { ?>
                                             <input type="checkbox" name="ass_role[]" value="add" />Add &nbsp;&nbsp;
                                        <input type="checkbox" name="ass_role[]" value="update" />Update &nbsp;&nbsp;
                                        <input type="checkbox" name="ass_role[]" value="delete" />Delete &nbsp;&nbsp;
                                        <input type="checkbox" name="ass_role[]" value="view" />View &nbsp;&nbsp;
                                        <?php }else{
                                        $media_acc_array = array(); 
					$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id']; 
					$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array);
					$ass_role_access = unserialize($RSmedia->fields['ass_role']); 
                                        ?>
                                       <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add",$ass_role_access) ){ ?> <input type="checkbox" name="ass_role[]" value="add" />Add &nbsp;&nbsp; <?php } ?>
                                       <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("update",$ass_role_access) ){ ?>  <input type="checkbox" name="ass_role[]" value="update" />Update &nbsp;&nbsp; <?php } ?>
                                       <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("delete",$ass_role_access) ){ ?>  <input type="checkbox" name="ass_role[]" value="delete" />Delete &nbsp;&nbsp; <?php } ?>
                                       <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("view",$ass_role_access) ){ ?>  <input type="checkbox" name="ass_role[]" value="view" />View &nbsp;&nbsp; <?php } ?>
                                        <?php }?>
					</td>
			      </tr>
		-->
                              <tr id="mediaaccess"> 
		<td align="right"><?php echo $merchant_msg["locationemployee"]["Field_media_access"];?></td> 
		<td align="left">	
                    <?php
                  
                                        if($_SESSION['merchant_info']['merchant_parent'] == 0 )
                                        { ?>
											
                                              <input type="checkbox" name="media_access[]" id="viewmedia" value="view-use" /><label for="viewmedia"class="chk_align">View or Use image</label>
                                              <input type="checkbox" name="media_access[]" id="uploadmedia" value="upload" /><label for="uploadmedia" class="chk_align">Upload image</label>
                                              <input type="checkbox" name="media_access[]" id="deletemedia" value="delete" disabled="disabled" /><label for="deletemedia" class="chk_align">Delete image</label>
											
                                        <?php }else{
                                        $media_acc_array = array();
					$media_acc_array['merchant_user_id'] =  $_SESSION['merchant_id']; 
					$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array); 
					$ass_media_access = unserialize($RSmedia->fields['media_access']); 
					
                    ?>
                            <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("view-use",$ass_media_access) ){ ?> <input type="checkbox" name="media_access[]" id="viewmedia" value="view-use" />View or Use image &nbsp;&nbsp; <?php } ?>
                            <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("upload",$ass_media_access) ){ ?><input type="checkbox" name="media_access[]" id="upload" value="upload" />Upload image &nbsp;&nbsp; <?php } ?>
                            <?php  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("delete",$ass_media_access) ){ ?><input type="checkbox" name="media_access[]" id="deletemedia" value="delete" />Delete image &nbsp;&nbsp; <?php } ?>
                    <?php } ?>
                      </td> 
</tr>
				 
				  <tr>
					<td align="left">&nbsp;</td>
					<td align="left" >
                                            <table> 
                                                <tr> 
                                                    <td> <input type="submit" id="btnAddUser" name="btnAddUser" value="<?php echo $merchant_msg['index']['btn_save'];?>" ></td>
                                                    <td> <form action="process.php" method="post" id="cancel_form" >
                                                            <input type="hidden" id="countryid"  name="country" value="" />
							<input type="hidden" name="hdn_redirectpath" id="hdn_redirectpath" value="<?=WEB_PATH.'/merchant/manage-users.php'?>" />
							<input type="button" name="btnCancel" id="btnCancel" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" ></form> </td>
                                                </tr>
                                            
                                            </table>
						<!-- T_5 -->
                                                <div class="rap-b"> 
                                                <div class="sav">
						
                                                </div>
                                                <div class="canc"> 
							
						</div>
                                                    </div>
                                                    <!-- T_5 -->
					</td>
                                        <td align="left">&nbsp;</td>
				  </tr>
				</table>
	  

		<div class="clear">&nbsp;</div>
<!--end of content--></div>
<!--end of contentContainer--></div>

<!---------start footer--------------->
       <div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>
		<!--end of footer--></div>
	
</div>

</body>
</html>
<script type="text/javascript">
    
$("a#createuser").css("background-color","orange");

$(document).ready(function(){
    var location_id=jQuery('#location_id option:selected').val();
          
    jQuery("#countryid").val(location_id);
    
	var viewmedia=$('#viewmedia').length;
	var upload=$('#uploadmedia').length;
	var deletemedia=$('#deletemedia').length;
	//alert(viewmedia +"====="+upload+"====="+deletemedia);
	if (viewmedia == 0 && upload == 0 && deletemedia == 0 ) {
		$("#mediaaccess").hide();
	} else {
		$("#mediaaccess").show();
	}
        
        jQuery("#location_id").change(function(){
     var location_id=jQuery('#location_id option:selected').val();
          
    jQuery("#location_id").val(location_id);
});

        jQuery("#ass_page").click(function(){
           
          
           var selected_text=jQuery("#ass_page option:selected").text();
           var find_str_add_campaign=selected_text.indexOf("Add Campaign");
           var find_str_add__templated=selected_text.indexOf("Add Template");
           var find_str_both=selected_text.indexOf("Add CampaignAdd Template");
           
          
           if(find_str_add_campaign != -1 || find_str_add__templated != -1 || find_str_both != -1 ){
                jQuery("#viewmedia").attr("checked","checked");
            }
            else
                {
                      if (jQuery('#uploadmedia').is(':checked')) {
                        jQuery("#viewmedia").attr("checked",true);
                    }
                    else
                        {
                            jQuery("#viewmedia").attr("checked",false);
                        }
                }
           
        });
		
		jQuery("#uploadmedia").click(function(){
				if (jQuery(this).is(':checked')) {
						jQuery("#deletemedia").attr("disabled",false);  
                                                jQuery("#viewmedia").attr("checked",true);
						
					} else {
         //$(this).prop('checked',true);
						 var selected_text=jQuery("#ass_page option:selected").text();
                                            var find_str_add_campaign=selected_text.indexOf("Add Campaign");
                                             var find_str_add__templated=selected_text.indexOf("Add Template");
                                                var find_str_both=selected_text.indexOf("Add CampaignAdd Template");
           
          
                                        if(find_str_add_campaign != -1 || find_str_add__templated != -1 || find_str_both != -1 ){
                                             jQuery("#viewmedia").attr("checked","checked");
                                             
                                         }
                                         else
                                             {
                                                  jQuery("#viewmedia").attr("checked",false);
                                                 
                                             }
                                                   
                                                   jQuery("#deletemedia").attr("disabled",true);
                                                 jQuery("#deletemedia").attr("checked",false);
				}
		});
                jQuery("#viewmedia").click(function(){
                    
				
                                                  var selected_text=jQuery("#ass_page option:selected").text();
           var find_str_add_campaign=selected_text.indexOf("Add Campaign");
           var find_str_add__templated=selected_text.indexOf("Add Template");
           var find_str_both=selected_text.indexOf("Add CampaignAdd Template");
           
          
           if(find_str_add_campaign != -1 || find_str_add__templated != -1 || find_str_both != -1 ){
                return false;
            }
            else if(jQuery("#uploadmedia").is(':checked')) {
                return false;
            }
            else
                {
                     return true;
                }
						
				
		});
		
		$('#country').change(function(){
	var change_value=this.value;
	
	if(change_value == "USA")
	{
	    $("#state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");
	    
	   
	}
	else
	{
	     $("#state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
	}
    });
		
		jQuery("#btnAddUser").click(function(){
			
			var alert_msg="<?php  echo  $merchant_msg["index"]["validating_data"]; ?>";
var head_msg="<div class='head_msg'>Message</div>";
var content_msg="<div class='content_msg validatingdata' style='background:white;'>"+alert_msg+"</div>";
jQuery("#NotificationloadermainContainer").html(content_msg);
jQuery("#NotificationloaderFrontDivProcessing").css("display","block");
jQuery("#NotificationloaderBackDiv").css("display","block");
jQuery("#NotificationloaderPopUpContainer").css("display","block");
			
		    var msg_box="";
		    var lastname=jQuery("#lastname").val();
            var firstname=jQuery("#firstname").val();
			var email=jQuery("#email").val();
			
			
			
			var mobileno_area_code=jQuery("#mobileno_area_code").val();
		    var mobileno=jQuery("#mobileno").val();
			var mobileno2=jQuery("#mobileno2").val();
			
			
			 var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
		    var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
			
			
			var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
			
			var flag="true";
			
            
				if(firstname == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_first_name']; ?></div>";
					flag="false";
				}
				if(lastname == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_last_name']; ?></div>";
					flag="false";
				}
				var flag_email = true;
				if(email_validation(email) == false){
			
			
					msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_valid_email"];?></div>";
					flag="false";
			
				}
                else{
                    jQuery.ajax({
					type: "POST",
					url: "<?=WEB_PATH?>/merchant/process.php",
					data: 'is_emailaddress_exists=yes&emailaddress='+document.getElementById("email").value,
                     async:false,
					success: function(msg) {
                                    //alert(msg);
                                
                                var obj = jQuery.parseJSON(msg);
                                if(obj.is_exists != 0){
                                 
                                   // msg +="<div>* Email Address Alredy Availble. Try With Diffrent Email Address.</div>";
			//flag="false";
                        flag_email = false;
                        //alert(msg);
                                }
                                }
                                });
                    
                }
				if(! flag_email)
                {
                 // alert("In if condition");
					msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_email_exist"];?></div>";
					flag="false";
                }
				
				
				
					
		    
				if(mobileno_area_code == "" || mobileno =="" || mobileno2 == "" )
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
					 flag="false";
				}
				else 
				{
							if(mobileno_area_code != "")
							{

								if(!numericReg.test(mobileno_area_code)) {
									//alert("Please Input Valid Mobile Number");
									msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
								   flag="false";
									//return false;
								}
								else
									{
										if(mobileno_area_code.length != 3)
										{
											//alert("Please Input Valid Area Code Number");
											msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
										   flag="false";
											//return false;
										}
									}

							}
							else if(mobileno != "")
							{
								if(!numericReg.test(mobileno)) {
									//alert("Please Input Valid Mobile Number");
									msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
								   flag="false";
									//return false;
								}
								else
									{
										if(mobileno.length != 4)
										{
											//alert("Please Input Valid Mobile Number");
											msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
											flag="false";
											//return false;
										} 
									}

							}
							else if(mobileno2 != "")
							{
								if(!numericReg.test(mobileno2)) {
									//alert("Please Input Valid Mobile Number");
									msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
								   flag="false";
									//return false;
								}
								else
									{
										if(mobileno2.length != 3)
										{
											//alert("Please Input Valid Mobile Number");
											msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
											flag="false";
											//return false;
										}
										
									}

							}

							
						   
						   
				}
				
				if($("#ass_page option:selected").length <=0)
                    {
                        msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_atleast_one_permission"];?></div>";
						flag="false";
                    }
                    else
                    {
                    
                    
                           
                             
                              jQuery('.distributionid').each(function(){
                                  
                                    if($("#ass_page .distributionid option:selected").text() != "Add Distribution List")
                                    {
                                        if($("#ass_page .distributionid option:selected").text() == "Copy Distribution List" || $("#ass_page .distributionid option:selected").text() == "Edit Distribution List" || $("#ass_page .distributionid option:selected").text() == "Activate/Deactive Distribution List")
                                            {
                                                 msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                                 flag="false";
                                               return false;
                                            }
                                          else if($("#ass_page .distributionid option:selected").text() == "Edit Distribution ListCopy Distribution ListActivate/Deactive Distribution List")
                                           {
                                               msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                               flag="false";
                                               return false;
                                           }
                                           else if($("#ass_page .distributionid option:selected").text() == "Edit Distribution ListCopy Distribution List")
                                               {
                                                   msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                                   flag="false";
                                               return false;

                                               }
                                           else if($("#ass_page .distributionid option:selected").text() == "Copy Distribution ListActivate/Deactive Distribution List")
                                           {
                                              msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                              flag="false";
                                           return false;

                                           }
                                           else if($("#ass_page .distributionid option:selected").text() == "Edit Distribution ListActivate/Deactive Distribution List")
                                           {
                                              msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_distlist"];?></div>";
                                              flag="false";
                                           return false;

                                           }
                                   }  
                              });
                              jQuery('.compaignsclass').each(function(){
                                     if($("#ass_page .compaignsclass option:selected").text() != "Add Campaign")
                                    {

                                          $("#ass_page .compaignsclass option:selected").each(function(){
                                             if($("#ass_page .compaignsclass option:selected").text() == "Copy Campaign" )
                                                 {
                                                      msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_campaign"];?></div>";
                                                      flag="false";
                                                    return false;
                                                 }

                                          });

                                    }
                              });
                              jQuery('.templateclass').each(function(){
                              
                               if($("#ass_page .templateclass option:selected").text() != "Add Template")
                                    {

                                          $("#ass_page .templateclass option:selected").each(function(){
                                             if($("#ass_page .templateclass option:selected").text() == "Delete Template" )
                                                 {
                                                      msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_template"];?></div>";
                                                      flag="false";
                                                    return false;
                                                 }

                                          });

                                    }
                                     
                              });
                              jQuery('.employeeclass').each(function(){
                              
                                if($("#ass_page .employeeclass option:selected").text() != "Add Employee")
                                      {

                                            $("#ass_page .employeeclass option:selected").each(function(){
                                               
                                                 if($("#ass_page .employeeclass option:selected").text() == "Edit Employee" || $("#ass_page .employeeclass option:selected").text() == "Delete Employee")
                                                    {
                                                         msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_employee"];?></div>";
                                                         flag="false";
                                                       return false;
                                                    }
                                                  else if($("#ass_page .employeeclass option:selected").text() == "Edit EmployeeDelete Employee")
                                                   {
                                                       msg_box +="<div><?php echo $merchant_msg["locationemployee"]["Msg_select_add_employee"];?></div>";
                                                       flag="false";
                                                       return false;
                                                   }
                                            });

                                      }
                                     
                              });
                              
                              
                    }
            
				var head_msg="<div class='head_msg'>Message</div>"
				var content_msg="<div class='content_msg'>"+msg_box+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(lastname == "" || firstname == "" || mobileno_area_code == "" || mobileno == "" || mobileno2 == "" || email == "" )
				{
					flag="false";
				}
					
				if(flag=="false")
				{
				  close_popup("Notificationloader");
				jQuery.fancybox({
							content:jQuery('#dialog-message').html(),
							type: 'html',
							openSpeed  : 300,
							closeSpeed  : 300,
							changeFade : 'fast',  
							helpers: {
								overlay: {
								opacity: 0.3
								} // overlay
							}
							});
							return false;
				}
				else
				{
					var flaglogin=0;
				 jQuery.ajax({
				   type:"POST",
				   url:'process.php',
				   data :'loginornot=true',
				  async:false,
				   success:function(msg)
				   {
					   
					 
						 var obj = jQuery.parseJSON(msg);
					   
						 
						if (obj.status=="false")     
						{
							flaglogin=1;
							window.location.href=obj.link;
							
						}
						else if(obj.approve == 0)
						{

							 var alert_msg="Merchant Status: Blocked , Please contact Scanflip";
							  var head_msg="<div class='head_msg'>Message</div>"
							   var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
							   var href="<?php echo WEB_PATH; ?>/merchant/logout.php";
							   var footer_msg="<div><hr><a href='"+href+"' class='msg_popup_cancel_anchor'><?php echo $merchant_msg['index']['fancybox_ok'];?></a></div>";
							   jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);


							   jQuery.fancybox({
													   content:jQuery('#dialog-message').html(),

													   type: 'html',

													   openSpeed  : 300,

													   closeSpeed  : 300,
													   // topRatio: 0,

													   changeFade : 'fast',  

													   helpers: {
															   overlay: {
															   opacity: 0.3
															   } // overlay
													   },
													   afterClose: function () {
															   window.location.href=href;
													   }

							   });

							   flaglogin=1;

						}
						else if(obj.approve == 2)
						{
								var alert_msg="Merchant Status: Pending , Please contact Scanflip";
							  var head_msg="<div class='head_msg'>Message</div>"
							   var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
							   var href="<?php echo WEB_PATH; ?>/merchant/my-account.php";
							   var footer_msg="<div><hr><a href='"+href+"' class='msg_popup_cancel_anchor'><?php echo $merchant_msg['index']['fancybox_ok'];?></a></div>";
							   jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);


							   jQuery.fancybox({
													   content:jQuery('#dialog-message').html(),

													   type: 'html',

													   openSpeed  : 300,

													   closeSpeed  : 300,
													   // topRatio: 0,

													   changeFade : 'fast',  

													   helpers: {
															   overlay: {
															   opacity: 0.3
															   } // overlay
													   },
													   afterClose: function () {
															   window.location.href=href;
													   }

							   });

							   flaglogin=1;
							
						}
						else
						{
						   flaglogin=0;
						}
						
						
				   }
				   
				 });

				 if(flaglogin == 1)
				 {
					  return false;
				 }
				 else
				 {
					var alert_msg="<?php  echo  $merchant_msg["index"]["saving_data"]; ?>";
					var head_msg="<div class='head_msg'>Message</div>";
					var content_msg="<div class='content_msg savingdata' style='background:white;'>"+alert_msg+"</div>";
					jQuery("#NotificationloadermainContainer").html(content_msg);

					return true;
					
				 }
				}

		});
		
		
               jQuery("#btnCancel").click(function(){
                  location.href="<?php echo WEB_PATH;?>/merchant/manage-users.php";
               });
		
});
function close_popup(popup_name)
{

	jQuery("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
	jQuery("#" + popup_name + "BackDiv").fadeOut(200, function () {
		 jQuery("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				jQuery("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
 function open_popup(popup_name)
{

	if($("#hdn_image_id").val()!="")
	{
		$('input[name=use_image][value='+$("#hdn_image_id").val()+']').attr("checked","checked");
	}
	$("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		$("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	
}
</script>
