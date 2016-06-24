<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/admin/phpqrcode/qrlib.php"); 
//$objDB = new DB();
$where_arr= array();

//$RS = $objDB->Show("qrcodes",$where_arr," order by created_date desc");


if(isset($_REQUEST['action']) && $_REQUEST['action']="addnew")
{
    
 
      
          
}

function create_campaign_code()
{
    $code_length=8;
    //echo $alfa = "1AB2CD3EF4G5HI6JK7LM8N9OP10QRSTU".$campaign_id."VWXYZ";
    $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $code="";
    for($i = 0; $i < $code_length; $i ++) 
    {
      $code .= $alfa[rand(0, strlen($alfa)-1)];
    } 
    return $code;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">

<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/a/jquery.js"></script>

<!--
<style type="text/css" title="currentStyle">
	@import "<?php echo ASSETS_CSS?>/a/demo_page.css";
	@import "<?php echo ASSETS_CSS?>/a/demo_table.css";
</style>
<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
-->	

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">window.jQuery||document.write('<script src="<?php echo ASSETS_JS?>/jquery-1.7.2.min.js"><\/script>')</script>
<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
<script src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>		
                
<style>
    .dataTables_wrapper{
        width:100%;
    }
    .container_popup {
    color: #333333;
    display: none;
    font-family: Verdana,Arial,sans-serif;
    z-index: 99999;
}
.divBack {
	opacity: 0.3;
	filter:alpha(opacity=30) !important;
	background-color:#000;
	
    height: 100%;
    left: 0;
        position: fixed;
    top: 0;
    width: 100%;
    z-index: 8000;
}
.Processing {
    display: none;
    height: 100%;
    left: 0;
    position: absolute;
    top: 0;
  /*  width: 127%;*/
    z-index: 9000;
}
.imgDivLoading {
    border-radius: 5px 5px 5px 5px;
    height: 80%;
    
    margin: auto;
    padding: inherit;
    position: fixed;
  
    vertical-align: middle;
    visibility: visible;
 /*   width: 39.5%;*/
 height: auto;
}
.closeButton {
    background-image: url("/assets/images/a/Close-button.png");
    background-repeat: no-repeat;
    color: #FFFFFF;
    cursor: pointer;
    float: right;
    font-size: 14px;
    font-weight: bolder;
    height: 20px;
    padding-bottom: 4px;
    padding-top: 7px;
    position: relative;
    width: 30px;
    z-index: 100;
}
.innerContainer {
    background-color: #FFFFFF;
 /*   border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;*/
	border-radius:10px;
    box-shadow: 0 1px 3px #666666;
    margin-top: 30px;
    padding: 0px 0 10px 0px;
    text-align: left;
	height:auto;width: 408px;height:200px;
}
.innerContainer ul {display: block; overflow: hidden; margin: 5px; list-style:disc inside none; padding: 0;}
.innerContainer ul li.tab_from_computer {font-size: 15px;}
.innerContainer ul li.tab_from_library {font-size: 15px;}
/*.innerContainer ul li.tab_from_computer:hover {font-size: 14px;cursor: pointer; color: #000; font-weight: bold;}
.innerContainer ul li.tab_from_library:hover {font-size: 14px;cursor: pointer; color: #000; font-weight: bold;}*/
.popup-title a {
    color: #0066CC;
    font-size: 11px;
    text-decoration: none;
}
.modal-close-button {
    height: 16px;
    position: absolute;
    right: 0;
    top: 11px;
    width: 16px;
    z-index: 1000;
}
.modal-close-button a {
    background: url("/assets/images/a/dialog_close.gif") no-repeat scroll 0 0 transparent;
    border: 0 none;
    display: block;
    height: 16px;
    text-decoration: none !important;
    width: 16px;
}
.element.style {
    width: 674px;
}
.popup-header-content {
    background: url("/assets/images/a/top_middle.png") repeat-x scroll 0 0 transparent;
   /*border: 1px solid #7A7A7A;
   border-top-left-radius : 8px;
   border-top-right-radius : 8px;
   /*background-color: #EBEBEB;
    -moz-box-shadow: 0 1px 3px #666;
	-webkit-box-shadow: 0 1px 3px #666;*/
	left:12px;right: 6px;
    bottom: auto;
    color: #333333;
    font-size: 11px;
    font-weight: normal;
    height: 30px;
    line-height: 29px;
	 margin: 0px;
	 width: auto;
    padding-top: 4px;
    position: absolute;

    top: 0;
	/*background-color: #7A7A7A;color: #FFF;font-weight: bold;*/
}
.ulcorner {
    background: url("/assets/images/a/left_corners.png") no-repeat scroll left top transparent;
    bottom: auto;
    height: 31px;
    left: -3px;
    position: absolute;
    right: auto;
    top: 0;
    width: 16px;
}
.urcorner {
    background: url("/assets/images/a/right_corners.png") no-repeat scroll right top transparent;
    bottom: auto;
    height: 31px;
    left: auto;
    position: absolute;

    top: 0;
    width: 15px;
	right: -4px;
}
 #fancybox-close {
    background: url("/assets/images/a/tb-close.png") repeat scroll 0 0 transparent;
    cursor: pointer;
    display: none;
    height: 16px;
    position: absolute;
    right: 5px;
    top: 20px;
    width: 14px;
    z-index: 1103;
    margin:8px;
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
		  <h2>Manage QR Codes</h2>
	<form action="process.php" method="post">
	<!--edit delete icons table-->
      
	<div>
		<table align="right"  cellspacing="10px" >
			<tr>
				<td>
					<h4 style=" float: left;font-weight: lighter;margin-bottom: 0;margin-left: 10px;margin-right: 0;margin-top: 0;">
						<a href="javascript:void(0)" class="addnewqrcode">
							<img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png">Add QR Codes
						</a>
					</h4>&nbsp;
					<h4 style=" float: left;font-weight: lighter;margin-bottom: 0;margin-left: 10px;margin-right: 0;margin-top: 0;">
						<a href="javascript:void(0)" class="editgroup">
							<img src="<?php echo ASSETS_IMG; ?>/a/icon-edit.png">Assign/Edit QR Codes Group
						</a>
					</h4>
					<h4 style=" float: left;font-weight: lighter;margin-bottom: 0;margin-left: 10px;margin-right: 0;margin-top: 0;">
						<a href="javascript:void(0)" class="deleteqrcode">
							<img src="<?php echo ASSETS_IMG; ?>/a/icon-edit.png">Delete QR Code
						</a>
					</h4>
				</td>
			</tr>
		</table>
		<!--end edit delete icons table--></div>
		<br/>
		<?php 
			$array_ = array();
			$array_['approve'] = 1;
			$array_['merchant_parent'] =0;
			$RS = $objDB->Show("merchant_user",$array_);
		?>
		<div style="float:left">
			&nbsp;&nbsp; Filter By Merchant : &nbsp;
			<select id="filter_merchant_id" name="filter_merchant_id">
				<option value="0">Super Admin</option>
			<?php 
			while($Row = $RS->FetchRow())
			{
			?>
				<option value="<?php echo $Row['id']?>">
					<?php echo $Row['business']."-".$Row['firstname']." ".$Row['lastname']?>
				</option>
			<?php } ?>
			</select>
		</div>												
		<table width="98%"   class="tableAdmin" id="example">
			<thead>
				<tr>
					<td colspan="6">&nbsp;<span style="color:#FF0000; "><?=$_SESSION['msg']?></span></td>
				</tr>
				<tr>
					<th width="5%" align="left"><input type="checkbox" name="chkchangegroups" value="<?php echo 0; ?>" class="checkall" /></th>
					<th width="15%" align="left">Qrcode</th>
					<th width="15%" align="left">Campaign Link Status</th>
					<th width="15%" align="left">Group Link Type</th>
					<th width="20%" align="left">Group Name</th>
					<th width="20%" align="left">Assigned Merchant </th>    
					
				</tr>
            </thead>
		  
		</table>
                
                  <div id="assigngroupPopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="assigngroupBackDiv" class="divBack">
                                        </div>
                                        <div id="assigngroupFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="assigngroupMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left:30%;top: 11%;">
                                                
                                                                <div class="modal-close-button" style="visibility: visible;">
                                                             
                                                                    <a  tabindex="0" onclick="close_popup('assigngroup');" id="fancybox-close" style="display: inline;"></a>
                                                                </div>
                                                <div id="assigngroupmainContainer" class="innerContainer" style="width:690px">
                                                        <div class="main_content"> 	
                                                         <div style=" background: none repeat scroll 0 0 #222222;color: #CFCFCF !important;padding: 4px;border-radius: 10px 10px 0 0;">
								<font style="font-family: Arial,Helvetica,sans-serif;font-size: 19px;font-weight: bold;letter-spacing: 1px;line-height: 24px;margin: 0;padding: 0 0 2px;padding-left:15px">
                                                                        Assign Group
								</font>
							 </div>
                                                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                                                                
								<div class="QRcode_detail_div" style="">
                                                                <table style="padding:35px" align="center">
								    <tr>
									<td>
											<div style="" name="div_existing_group" id="div_existing_group"> 
							   <?php 
							 
						  
							   ?>
							    Select Group :                        
						       <select id="qrcode_group_list" name="qrcode_group_list" >
						       <?php 
						  // $array['qrcode_id']=$Row['id'];
									     
						  $RS_group = $objDB->Show("qrcode_group");
						  
			   
			   while($Row_group = $RS_group->FetchRow()){ ?>
							   <option value="<?php echo $Row_group['id'] ?>"> 
							       <?php echo $Row_group['group_name'] ?>
			       </option>
							    <?php
			   }
			   ?>
						       </select> &nbsp; &nbsp; <span style="font-weight: bold;"> OR </span> <a href="javascript:void(0)" id="disp_new_group" name="disp_new_group" > Create new group </a>
										
										</div>
									</td>
									
								    </tr>
								    <tr>
									<td>
									    <div style="display: none" id="div_newgroup">
											<table>
												<tr>
													<td>
														Group Name : 
													</td>
													<td>
														<input type="textbox" name="txt_groupname" id="txt_groupname" value="" size="35" /> 
													</td>
												</tr>
												<tr>
													<td>
														Assigned to merchant :  
													</td>
													<td>
														<?php 
															$array_ = array();
															$array_['approve'] = 1;
															$array_['merchant_parent'] =0;
															$RS = $objDB->Show("merchant_user",$array_);
														?>
														<select id="merchant_id" name="merchant_id" style="width:100%;">
															<option value="0">Select Merchant</option>
														<?php 
														while($Row = $RS->FetchRow())
														{
														?>
															<option value="<?php echo $Row['id']?>">
																<?php echo $Row['business']."-".$Row['firstname']." ".$Row['lastname']?>
															</option>
														<?php } ?>
														</select>
													</td>
												</tr>
											</table>		
                                        </div>  
									</td>
									
								    </tr>
								     <tr>
									<td>
									    &nbsp;
									</td>
								    </tr>
								    <tr>
									<td>
									    <input type="submit" name="btn_assigngroup" id="btn_assigngroup" value="Assign" />
									     &nbsp; &nbsp; &nbsp; &nbsp;<input type="button" name="btn_cancelassigngroup" id="btn_cancelassigngroup" value="Back" style="display:none;"/>
									</td>
								    </tr>
								   
								</table>
								    
                                             
                                               
                                            <input type="hidden" name="qid" id="qid" value="<?php echo $_REQUEST['id']; ?>" />
                                            <input type="hidden" name="is_existinggroup" id="is_existinggroup" value="1" />
                                            
                                                                </div>
								
                                                          
							
                                                            </div>
                                                          
                                                        </div>
                                                 </div>
                                            </div>
                                       </div> 
  </div>
                <input type="hidden" name="hdn_qrcodelist" id="hdn_qrcodelist" />
            <div id="noofqrcodePopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="noofqrcodeBackDiv" class="divBack">
                                        </div>
                                        <div id="noofqrcodeFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="noofqrcodeMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left:30%;top: 11%;">
                                                
                                                                <div class="modal-close-button" style="visibility: visible;">
                                                             
                                                                    <a  tabindex="0" onclick="close_popup('noofqrcode');" id="fancybox-close" style="display: inline;"></a>
                                                                </div>
                                                <div id="noofqrcodemainContainer" class="innerContainer" style="width:690px">
                                                        <div class="main_content"> 	
                                                         <div style=" background: none repeat scroll 0 0 #222222;color: #CFCFCF !important;padding: 4px;border-radius: 10px 10px 0 0;">
								<font style="font-family: Arial,Helvetica,sans-serif;font-size: 19px;font-weight: bold;letter-spacing: 1px;line-height: 24px;margin: 0;padding: 0 0 2px;padding-left:15px">
                                                                        Add QRcode
								</font>
							 </div>
                                                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
								<table style="padding:35px" align="center">
								    <tr>
									<td>
									    Enter No. of QRCode :
									</td>
									<td>
									    <input type="text" value="" name="txt_no_qrcode" id="txt_no_qrcode" />
									</td>
								    </tr>
								    <tr>
									<td>
									    &nbsp;
									</td>
									<td>
									    &nbsp;
									</td>
									    
								    </tr>
								    <tr>
									<td>
									    <input type="submit" name="btn_addnewqrcode" id="btn_addnewqrcode" value="Generate Qrcode" />&nbsp;&nbsp;
									</td>
									<td>
									    <input type="button" name="btnCancelqrcodegroup" id="btnCancelqrcodegroup" value="Cancel" />   
									</td>
								    </tr>
								</table>
                                                                <div class="QRcode_detail_div" style="">
                                                                     <div style="" name="div_existing_group" id="div_existing_group"> 
                                                                        
                                                                         <br/><br/>
                                                                         
                                                                         
								    </div>
                                             
                                             
                                                                </div>
								
                                                          
							
                                                            </div>
                                                          
                                                        </div>
                                                 </div>
                                            </div>
                                       </div> 
  </div>      
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<?php $_SESSION['msg']=""; ?>

<script type="text/javascript" charset="utf-8">
			/*
			$(document).ready(function() {
				oTable = $('#example').dataTable( {
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					 "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                                        "iDisplayLength" : 25,
                                        "aoColumnDefs": [
                                                  { 'bSortable': false, 'aTargets': [0, 1 ,2,3 ,4,5,6] }
                                               ]
                                                                        } );
			} );
			*/
			
			var oCache = {
                    iCacheLower: -1
                };

                function fnSetKey(aoData, sKey, mValue)
                {
                    for (var i = 0, iLen = aoData.length; i < iLen; i++)
                    {
                        if (aoData[i].name == sKey)
                        {
                            aoData[i].value = mValue;
                        }
                    }
                }

                function fnGetKey(aoData, sKey)
                {
                    for (var i = 0, iLen = aoData.length; i < iLen; i++)
                    {
                        if (aoData[i].name == sKey)
                        {
                            return aoData[i].value;
                        }
                    }
                    return null;
                }

                function fnDataTablesPipeline(sSource, aoData, fnCallback) {
                    var iPipe = 2; /* Ajust the pipe size */

                    var bNeedServer = false;
                    var sEcho = fnGetKey(aoData, "sEcho");
                    var iRequestStart = fnGetKey(aoData, "iDisplayStart");
                    var iRequestLength = fnGetKey(aoData, "iDisplayLength");
                    var iRequestEnd = iRequestStart + iRequestLength;
                    oCache.iDisplayStart = iRequestStart;

                    /* outside pipeline? */
                    if (oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper)
                    {
                        bNeedServer = true;
                    }

                    /* sorting etc changed? */
                    if (oCache.lastRequest && !bNeedServer)
                    {
                        for (var i = 0, iLen = aoData.length; i < iLen; i++)
                        {
                            if (aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho")
                            {
                                if (aoData[i].value != oCache.lastRequest[i].value)
                                {
                                    bNeedServer = true;
                                    break;
                                }
                            }
                        }
                    }

                    /* Store the request for checking next time around */
                    oCache.lastRequest = aoData.slice();

                    if (bNeedServer)
                    {
                        if (iRequestStart < oCache.iCacheLower)
                        {
                            iRequestStart = iRequestStart - (iRequestLength * (iPipe - 1));
                            if (iRequestStart < 0)
                            {
                                iRequestStart = 0;
                            }
                        }

                        oCache.iCacheLower = iRequestStart;
                        oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
                        oCache.iDisplayLength = fnGetKey(aoData, "iDisplayLength");
                        fnSetKey(aoData, "iDisplayStart", iRequestStart);
                        fnSetKey(aoData, "iDisplayLength", iRequestLength * iPipe);

                        $.getJSON(sSource, aoData, function (json) {
                            /* Callback processing */
                            oCache.lastJson = jQuery.extend(true, {}, json);

                            if (oCache.iCacheLower != oCache.iDisplayStart)
                            {
                                json.aaData.splice(0, oCache.iDisplayStart - oCache.iCacheLower);
                            }
                            json.aaData.splice(oCache.iDisplayLength, json.aaData.length);

                            fnCallback(json)
                        });
                    }
                    else
                    {
                        json = jQuery.extend(true, {}, oCache.lastJson);
                        json.sEcho = sEcho; /* Update the echo for each response */
                        json.aaData.splice(0, iRequestStart - oCache.iCacheLower);
                        json.aaData.splice(iRequestLength, json.aaData.length);
                        fnCallback(json);
                        return;
                    }
                }
                
			var oTable = $('#example').dataTable( {
		//"bStateSave": true,
       "bFilter": false,
		"bSort" : false,
		"bLengthChange": false,
		"info": false,
		"iDisplayLength": 25,
		"bProcessing": true,
         "bServerSide": true,
		"oLanguage": {
						"sEmptyTable": "No qrcode founds in the system. Please add at least one.",
						"sZeroRecords": "No qrcode to display",
                        "sProcessing": "Loading..."
                    },
         "sAjaxSource": "<?php echo WEB_PATH; ?>/admin/process.php",            
         "fnServerParams": function (aoData) {
                        aoData.push({"name": "btnGetAllqrcodes", "value": true},{"name": 'mer_id', "value": jQuery("#filter_merchant_id").val()})
						//bind_more_action_click();
                    },
         //"fnServerData": fnDataTablesPipeline,     
         
         "aoColumns": [
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                       
                    ]      
                    
    } );
		jQuery("#filter_merchant_id").live("change",function(){
			oTable.fnDraw();
		});
		</script>
    
    <script>
		jQuery("#btnCancelqrcodegroup").click(function(){
			close_popup("noofqrcode");	
		});
        $("#btn_assigngroup").click(function(){
			if(jQuery("#div_existing_group").css("display")=="none")
			{
				if(jQuery("#txt_groupname").val()=="")
				{
					alert("Please enter group name");
					return false;
				}
				if(jQuery("#merchant_id").val()==0)
				{
					alert("Please select merchant");
					return false;
				}
			}
           // alert("In");
            $(":input[id^='chk_']", oTable.fnGetNodes()).each(function(){
                if($(this).is(':checked'))
                    {
                         //  alert($(this).val()) ;
                           $("#hdn_qrcodelist").val($("#hdn_qrcodelist").val()+$(this).val()+";");
                    }
            });
              //var sData = $(":input[id^='chk_']", oTable.fnGetNodes()).serialize();
              var qrcodelist = $("#hdn_qrcodelist").val();
              $("#hdn_qrcodelist").val(qrcodelist.substring(0,qrcodelist.length-1));
		//alert( "The following data would have been submitted to the server: \n\n"+sData );
		//return false;
        });
        
//          $(":input[id^='chk_']").click(function(){
//          //  alert("in");
//            if($(".checkall").is(':checked'))
//        {
//           
//            $(":input[id^='chk_']").each(function(){
//               $(this).attr("checked","checked") ; 
//            });
//        }
//        else
//        {
//           
//            $(":input[id^='chk_']").each(function(){
//                 $(this).removeAttr("checked") ;
//            });
//        }
//        });
        
        $(".checkall").click(function(){
          //  alert("in");
            if($(".checkall").is(':checked'))
        {
           
            $(":input[id^='chk_']").each(function(){
               $(this).attr("checked","checked") ; 
            });
        }
        else
        {
           
            $(":input[id^='chk_']").each(function(){
                 $(this).removeAttr("checked") ;
            });
        }
        });
        
        jQuery(".deleteqrcode").click(function(){
			console.log("click");
			var qrcode_to_delete = "";
			jQuery(":input[id^='chk_']").each(function(){
                if(jQuery(this).is(':checked'))
                {
					qrcode_to_delete += jQuery(this).val()+"," ;
				}	
            });
            if(qrcode_to_delete=="")
            {
				alert("Please select at least one qrcode to delete");
			}
			else
			{
				qrcode_to_delete = qrcode_to_delete.substring(0, qrcode_to_delete.length - 1);
				console.log(qrcode_to_delete);
				
				jQuery.ajax({
					type:"POST",
					url:'process.php',
					data :'btndeleteqrcodes=yes&qrcodeids='+qrcode_to_delete,
					success:function(msg)
					{
						var obj = jQuery.parseJSON(msg);
						if (obj.status=="true") 
						{
							alert("qrcodes deleted successfully.");
							oTable.fnDraw();
						}
				
					}
				});
			}
		});
        
        $(".editgroup").click(function(){
             var err_msg ="";
      if($(":input[id^='chk_']:checked").length > 0)
        {
         
            open_popup("assigngroup");
        }
        else{
            alert("Select atleast one qrcode");
        }
        });
        
    jQuery("#btn_cancelassigngroup").click(function(){
		jQuery("#btn_cancelassigngroup").css("display","none");
		jQuery("#div_newgroup").css("display","none");
		jQuery("#div_existing_group").css("display","block");
		jQuery("#is_existinggroup").val("1");
	});
 $("#disp_new_group").click(function(){
      $("#div_newgroup").css("display","block");
      $("#div_existing_group").css("display","none");
      $("#is_existinggroup").val("0");
      $("#btn_cancelassigngroup").css("display","inline");
  });
  /*
  $("#disp_existing_group").click(function(){
	  jQuery("#btn_cancelassigngroup").css("display","none");
      $("#div_newgroup").css("display","none");
      $("#div_existing_group").css("display","block");
      $("#is_existinggroup").val("1");
  }); 
  */
function close_popup(popup_name)
{
   /* var close_id=popup_name.substr(12);
    alert(close_id);
   $("#"+close_id).text("");
   $("#txt_share_frnd"+close_id).val("");*/
   
    
	$("#" + popup_name + "FrontDivProcessing").fadeOut(100, function () {
	$("#" + popup_name + "BackDiv").fadeOut(100, function () {
		 $("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				$("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
 function open_popup(popup_name)
{
//alert("In fuction");
//	alert("#" + popup_name + "FrontDivProcessing");
	$("#" + popup_name + "FrontDivProcessing").fadeIn(100, function () {
		$("#" + popup_name + "BackDiv").fadeIn(100, function () {
			 $("#" + popup_name + "PopUpContainer").fadeIn(100, function () {         
	
			 });
		});
	});
	
	
        
}
$(".addnewqrcode").click(function(){
   
    open_popup("noofqrcode");
});

$("#btn_addnewqrcode").click(function(){
    
       var numbers = /^[0-9]+$/;  
      if($("#txt_no_qrcode").val() != "")  
      {
      if($("#txt_no_qrcode").val().match(numbers))  
      {  
        var val = parseInt($("#txt_no_qrcode").val());
         
           if(val<=0)
               {
                   alert("Enter valid number");
                 return false;
               }
               else
                   {
                       return true;
                   }
      }
               else
                   {
                       alert("Enter valid number");
                       return false;
                   }
      }
      else{
          alert("Enter valid number");
           return false; 
      }
    });
    </script>
</body>
</html>
