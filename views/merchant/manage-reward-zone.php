<?php
/**
 * @uses manage reward zone
 * @used in pages : reward-profile-left.php
 * @author Sangeeta Raghavani
 */
check_merchant_session();
/*
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
*/ 
$pack = $objDB->Conn->Execute('SELECT mb.id,mb.merchant_id,mb.pack_id,b.pack_name,b.reward_zone_active_campaign,b.reward_zone_active_gift_card FROM merchant_billing mb Inner join billing_packages b on b.id=mb.pack_id and mb.merchant_id=?', array($_SESSION['merchant_id']));
$reward_zone_active_gift_card = $pack->fields['reward_zone_active_gift_card'];
$reward_zone_active_campaign = $pack->fields['reward_zone_active_campaign'];
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Reward Zone</title>
        <?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <!--- tooltip css --->
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
        <!--<script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.js"></script>-->
        <script type="text/javascript" src="<?= ASSETS_JS ?>/bootstrap.min.js"></script>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
        <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
        <style>
            //.fancybox-close { margin-right: 1px !important; margin-top: 1px !important;}
            .add {float: right;margin-bottom: 10px;font-weight: bold;}
            //#div_rewardzone_campaign_table, #div_gift_card_table {width: 70%;margin-left: 200px;}
        </style>
    </head>
    <body>
        <div style="width:100%;text-align:center;">
            <script type="text/javascript" charset="utf-8">
                    /******* initialize jQuery data table ****/

                    function logged_in_or_not() {
                        jQuery.ajax({
                            type: "POST",
                            url: 'process.php',
                            data: 'loginornot=true',
                            success: function (msg)
                            {
								//alert(msg);
                                var obj = jQuery.parseJSON(msg);
                                if (obj.status == "false")
                                {
                                    window.location.href = "<?php echo WEB_PATH ?>/merchant/register.php";

                                } else {
                                    return true;
                                }

                            }
                        });
                    }

                    $(document).ready(function () {
                        var otable = $('#gift_card_table').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": true,
                            "info": false,
                            "bSort": false,
                            "paging": true,
                            "searching": false,
                            "iDisplayLength": 5,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "oLanguage": {
                                "sEmptyTable": "No gift cards founds in the system.",
                                "sZeroRecords": "No gift cards to display",
                                "sProcessing": "Loading..."
                            },
                            "fnPreDrawCallback": function (oSettings) {
                                logged_in_or_not();
								//$('#filterrwdCard').trigger('click');
                            },
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "get_gift_cards", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'});

                            },
                        });


                        var rtable = $('#rewardzone_campaign_table').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": true,
                            "info": false,
                            "bSort": false,
                            "iDisplayLength": 10,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "oLanguage": {
                                "sEmptyTable": "No campaign founds in the system.",
                                "sZeroRecords": "No campaign to display",
                                "sProcessing": "Loading..."
                            },
                            "fnPreDrawCallback": function (oSettings) {
                                logged_in_or_not();
                            },
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "get_rewardzone_campaigns", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'}, {"name": "camp_filter_year", "value": $('#camp_filter_year').val()}, {"name": "camp_filter_category", "value": $('#camp_filter_category').val()}, {"name": "camp_filter_status", "value": $('#camp_filter_status').val()});

                            },
                        });


                        $(document).on('click', '.card_action', function () {
                            var actn = $(this).data('ac');
                            var cardid = $(this).data('cardid');

                            if (actn == 'delete') {

				jQuery.fancybox({
                                            content: '<div class="head_msg">Error</div><div class="content_msg">Are you sure you want to delete this gift card ?</div><hr><input type="button" value="Yes" id="yes_del_gf" class="msg_popup_cancel">&nbsp;<input type="button" value="No" id="no_del_gf" class="msg_popup_cancel">',
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
											openEffect :'none',
											closeEffect :'none',
                                            minWidth: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
				$('#yes_del_gf').on('click',function(){
					jQuery.fancybox.close();
					$.ajax({
                                        type: "POST",
                                        url: "<?= WEB_PATH ?>/merchant/process.php",
                                        data: "del_rewardzone_giftcard=true&id=" + cardid,
                                        success: function (msg) {
                                            otable.fnDraw();
                                            $('#filterrwdCard').trigger('click');
                                        }
                                    });
				});

				$('#no_del_gf').on('click',function(){
					jQuery.fancybox.close();
				});


                            } else if (actn == 'activ_deactiv') {
                                var actv = $(this).data('active');
                                if (actv == 1) {
                                    actv = 0;
                                } else if (actv == 0) {
                                    var ttl_gf = <?php echo $reward_zone_active_gift_card; ?>;
                                    if ($('.gt_cad').text() < ttl_gf) {
                                        actv = 1;
                                    } else {
                                        jQuery.fancybox({
                                            content: '<div class="head_msg">Error</div><div class="content_msg">You have reached maximum activate gift card limit '+ttl_gf+'</div><hr><input type="button" value="OK" id="popupcancel" name="popupcancel" class="msg_popup_cancel" onclick="jQuery.fancybox.close();">',
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
						openEffect :'none',
					closeEffect :'none',
                                            minWidth: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });
                                        return false;
                                    }

                                }
                                $.ajax({
                                    type: "POST",
                                    url: "<?= WEB_PATH ?>/merchant/process.php",
                                    data: "activ_deactiv_rewardzone_giftcard=true&id=" + cardid + "&active=" + actv,
                                    async:false,
                                    success: function (msg) {
                                        otable.fnDraw();
                                        $('#filterrwdCard').trigger('click');
                                    }
                                });
                            }
                        });
                        $(document).on('click', '.camp_action', function () {
                            var actn = $(this).data('ac');
                            var cardid = $(this).data('campid');

                            if (actn == 'delete') {

				jQuery.fancybox({
                                            content: '<div class="head_msg">Error</div><div class="content_msg">Are you sure you want to delete this campaign ?</div><hr><input type="button" value="Yes" id="yes_del_cmp" class="msg_popup_cancel">&nbsp;<input type="button" value="No" id="no_del_cmp" class="msg_popup_cancel">',
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
						openEffect :'none',
					closeEffect :'none',
                                            minWidth: 300,
                                            changeFade: 'fast',
                                            helpers: {
                                                overlay: {
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                        });

				$('#yes_del_cmp').on('click',function(){
					jQuery.fancybox.close();
					$.ajax({
		                                type: "POST",
		                                url: "<?= WEB_PATH ?>/merchant/process.php",
		                                data: "del_rewardzone_campaign=true&id=" + cardid,
		                                success: function (msg) {
		                                    rtable.fnDraw();
		                                    $('#filterrwdcgt').trigger('click');
		                                }
		                            });
				});

				$('#no_del_cmp').on('click',function(){
					jQuery.fancybox.close();
				});

                                /*if (confirm('Are you sure you want to delete this campaign?')) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?= WEB_PATH ?>/merchant/process.php",
                                        data: "del_rewardzone_campaign=true&id=" + cardid,
                                        success: function (msg) {
                                            rtable.fnDraw();
                                            $('#filterrwdcgt').trigger('click');
                                        }
                                    });
                                }*/
                            } else if (actn == 'active-deactive') {
                                var actv = $(this).data('active');
                                if (actv == 1) {
                                    actv = 0;
                                } else if (actv == 0) {
                                    var ttl_cmp = <?php echo $reward_zone_active_campaign; ?>;
					
                                    if ($('.gt_cca').text() < ttl_cmp) {
                                        actv = 1;
                                    } else {
                                        jQuery.fancybox({
                                            content: '<div class="head_msg">Error</div><div class="content_msg">You have reached maximum activate campaign limit '+ttl_cmp+'</div><hr><input type="button" value="OK" id="popupcancel" name="popupcancel" class="msg_popup_cancel" onclick="jQuery.fancybox.close();">',
                                            type: '',
		                    openSpeed: 300,
		                    closeSpeed: 300,
					openEffect :'none',
					closeEffect :'none',
	// topRatio: 0,

		                    changeFade: 'fast',
		                    helpers: {
		                        overlay: {
		                            opacity: 0.3
		                        } // overlay
		                    }
                                        });
                                        return false;
                                    }
                                }
                                $.ajax({
                                    type: "POST",
                                    url: "<?= WEB_PATH ?>/merchant/process.php",
                                    data: "activ_deactiv_rewardzone_campaign=true&id=" + cardid + "&active=" + actv,
                                    async:false,
                                    success: function (msg) {
                                        rtable.fnDraw();
                                        $('#filterrwdcgt').trigger('click');
                                    }
                                });
                            }
                        });
			
                      $('#filterrwdCard').on('click', function () {
                            //otable.fnDraw();
                            $.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "filter_reward_gift_total=true",
                                success: function (msg) {
                                    var res = jQuery.parseJSON(msg);
                                    if (res.status == "true") {
                                        $('.grnd_total_card .gt_cas').text(res.total_card_sold);
										$('.grnd_total_card .gt_cad').text(res.total_card_active);
                                        $('.gt_pate').text(res.total_points_avail_earn);
                                        $('.gt_ped').text(res.total_points_earn);
                                        $('.gt_total').text(res.currency+" "+res.total_card_sold_val);
                                    } else {
                                        window.location.href = "<?php echo WEB_PATH ?>/merchant/register.php";
                                    }
                                }
                            });
                        })

                     $('#filterrwdcgt').on('click', function () {

                            $.ajax({
                                type: "POST",
                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                data: "filter_reward_camp_total=true",
                                success: function (msg) {
                                    var res = jQuery.parseJSON(msg);
                                    if (res.status == "true") {
                                        $('.grnd_total_camp .gt_cca').text(res.total_camp_active);
										$('.grnd_total_camp .gt_ccs').text(res.total_camp_sold);
                                        $('.gt_ccr').text(res.total_campagins_redeem);
                                        $('.gt_ccpe').text(res.total_points_earn);
                                        $('.gt_tcsv').text(res.currency+" "+res.total_camp_sold_val);
                                    } else {
                                        window.location.href = "<?php echo WEB_PATH ?>/merchant/register.php";
                                    }
                                }
                            });
                      })

                        $('#filterrwdCampaign').on('click', function () {
                            rtable.fnDraw();
                        })

                        $('#gift_card_terms_cond_sbt').on('click', function () {
                            var term = $.trim($('#gift_card_terms_cond').val());
                            var id = $.trim($('#gf_id').val());

                            if (term.length > 0) {
                                $('.gf_term_error').remove();
                                $.ajax({
                                    type: "POST",
                                    url: "<?= WEB_PATH ?>/merchant/process.php",
                                    data: "gift_card_terms_conditions=true&id=" + id + "&term=" + term,
                                    success: function (msg) {
                                        if (msg == '0') {

                                            window.location.href = '<?= WEB_PATH ?>/merchant/register.php';
                                        } else if (msg > 0) {

                                            $('#gf_id').val(msg);
                                        }

                                    }
                                });
                            } else {
                                $('#gift_card_terms_cond').after('<span class="gf_term_error">Please enter Terms and conditions</span>');
                            }
                        })
			
			var myParam = window.location.href.split("#")[1];
			console.log(myParam);
			if(myParam == 'campaign'){
				jQuery("#sidemenu li a").each(function () {
                                	jQuery(this).removeClass("current");
	                            });
				jQuery("li#tab-type.div_campaign a").addClass("current");
				jQuery('#div_giftcard').hide();
				jQuery('#div_campaign').show();
			}

                        jQuery("li#tab-type a").click(function () {

                            //alert(jQuery(this).html());			

                            jQuery("#sidemenu li a").each(function () {
                                jQuery(this).removeClass("current");
                            });

                            jQuery(this).addClass("current");

                            var cls = jQuery(this).parent().attr("class");

                            jQuery(".tabs").each(function () {
                                jQuery(this).css("display", "none");
                            });

                            jQuery('#' + cls).css("display", "block");

                        });
			jQuery('.notification_tooltip').tooltip({
                        content: function () {
                            return jQuery(this).attr('title');
                        },
                        track: true,
                        delay: 0,
                        showURL: false,
                        showBody: "<br>",
                        fade: 250

                    });
                        $('#filterrwdCard').trigger('click');
                        $('#filterrwdcgt').trigger('click');

                    });
            </script>
            <!---start header---->
            <div>
                <?php
// include header file from merchant/template directory 
                require_once(MRCH_LAYOUT . "/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">
                <div id="content">
                    <div class="title_header">Reward Zone</div>

                    <div id="media-upload-header">
                        <ul id="sidemenu">
                            <li id="tab-type" data-hashval="giftcarddiv" class="div_giftcard">
                                <a class="current">Gift Cards</a>
                            </li>
                            <!--                            <li id="tab-type" data-hashval="termsdiv"  class="div_terms" >
                                                            <a >Terms & Conditions</a>
                                                        </li>-->
                            <li id="tab-type" data-hashval="campaigndiv"  class="div_campaign" >
                                <a >Campaigns</a>
                        </ul>
                    </div>

                    <div class="all_div_container col-md-offset-1 col-md-10">
                        <div id="div_giftcard" class="tabs" style="display:block;">
                            <div class="act_outer tab_inner11 disply_09">
                                <div class="search_area01 disply_09 text-center">
                                   <!-- <label>Year:</label>
                                    <select id="card_filter_year">
                                        <?php
                                      /*  for ($y = date('Y') - 2; $y <= date('Y'); $y++) {
                                                ?>
                                                <option value="<?php echo $y; ?>" 
                                                <?php
                                                if ($y == date('Y')) {
                                                        echo "selected";
                                                }
                                                ?> > <?php echo $y; ?> </option>
                                                <?php }
                                                ?>
                                    </select>
                                    <label>Month:</label>
                                    <select id="card_filter_month">
                                        <option value="0">All</option>
                                        <?php
                                        for ($m = 1; $m <= 12; $m++) {
                                                $month = date("F", mktime(0, 0, 0, $m, 1, 2000));
                                                $m = date("m", mktime(0, 0, 0, $m, 1, 2000));
                                                ?>
                                                <option value="<?php echo $m; ?>"><?php echo $month; ?></option>
                                                <?php
                                        }*/
                                        ?> 
                                    </select>

                                    <input type="button" value="Filter" name="filterrwdCard" id="filterrwdCard" />-->
					<span id="filterrwdCard"></span>
                                </div>
                                <div class="col-sm-12 grnd_total_card">
					
                                    <div class="col-sm-3 ">
                                        <div class="tab_inner_bg">
                                            <label>Total Cards Sold</label>
                                            <p class="gt_cas">0</p><p class="gt_cad hide">0</p>
                                        </div>				
                                    </div>
                                    <div class="col-sm-3 ">
                                        <div class="tab_inner_bg">
                                            <label>Total Cards Values Sold </label>
                                            <p class="gt_total">0</p>
                                        </div>				
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="tab_inner_bg">
                                            <label>Total Points Available To Earn</label>
                                            <p class="gt_pate">0</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="tab_inner_bg">
                                            <label>Total Points Earned</label>
                                            <p class="gt_ped">0</p>
                                        </div>				
                                    </div>
								<span data-toggle="tooltip" data-placement="right" class="notification_tooltip notification_tooltip_managerewardzone" title="Displays last 18 month's records." data-html="true" data-original-title="Displays last 18 month's records." style="display: none;">&nbsp;&nbsp;&nbsp;</span>
                                    
                                </div>
                            </div>
                            <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                                <tr>
                                    <td align="left" valign="top">
                                        <div class="disply_09"><a href="<?php WEB_PATH ?>/merchant/add-gift-card.php" class="add">Add Gift Card</a></div>


                                        <div class="datatable_container">	
                                            <div id="div_gift_card_table">  
                                                <table width="100%"  border="0" class="tableMerchant"  id="gift_card_table" style="table-layout: fixed;">
                                                    <thead>
                                                        <tr>
                                                            <th>Title</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            </table>
                            <!--<div class="clear">&nbsp;</div>
                            <div class="disply_09 mg_tc_05">

                                <label class="col-sm-2 control-label">Terms & Conditions :</label>
                                <div class="col-sm-9">
                            <?php
                            /* $id = 0;
                              $term_c = '';
                              $gf_trm = $objDB->Conn->Execute('Select * From giftcard_terms_conds where merchant_id=?', array($_SESSION['merchant_id']));
                              if ($gf_trm->RecordCount() > 0) {
                              $id = $gf_trm->fields['id'];
                              $term_c = $gf_trm->fields['condition'];
                              } */
                            ?>
                                    <input type="hidden" id="gf_id" value="<?php //echo $id;          ?>" />
                                    <textarea class="form-control" name="gift_card_terms_cond" id="gift_card_terms_cond"><?php //echo $term_c;          ?></textarea>

                                </div>
                                <input type="button" id="gift_card_terms_cond_sbt" value="Save"  />

                            </div>-->
                            <!--end of div_giftcard-->
                        </div>

                        <div id="div_campaign" class="tabs" style="display:none;">
                            <div class="act_outer tab_inner11 disply_09">
                                <div class="search_area01 disply_09 text-center">
                                 <!--   <label>Year:</label>
                                    <select id="cgt_filter_year">
                                        <?php
                                       /* for ($y = date('Y') - 2; $y <= date('Y'); $y++) {
                                                ?>
                                                <option value="<?php echo $y; ?>" 
                                                <?php
                                                if ($y == date('Y')) {
                                                        echo "selected";
                                                }
                                                ?> > <?php echo $y; ?> </option>
                                                <?php }
                                                ?>
                                    </select>
                                    <label>Month:</label>
                                    <select id="cgt_filter_month">
                                        <option value="0">All</option>
                                        <?php
                                        for ($m = 1; $m <= 12; $m++) {
                                                $month = date("F", mktime(0, 0, 0, $m, 1, 2000));
                                                $m = date("m", mktime(0, 0, 0, $m, 1, 2000));
                                                ?>
                                                <option value="<?php echo $m; ?>"><?php echo $month; ?></option>
                                                <?php
                                        }*/
                                        ?> 
                                    </select>

                                    <input type="button" value="Filter" name="filterrwdcgt" id="filterrwdcgt" /> -->
					<span id="filterrwdcgt"></span>
                                </div>
				
                                <div class="col-sm-12 grnd_total_camp">
                                    <div class="col-sm-3 ">
                                        <div class="tab_inner_bg">
                                            <label>Total Campaign Sold</label>
                                            <p class="gt_ccs">0</p>
                                             <p class="gt_cca hide">0</p>
                                        </div>	
                                        </div>
                                     <div class="col-sm-3 ">
                                        <div class="tab_inner_bg">
                                            <label>Total Campaign Values Sold</label>
                                            <p class="gt_tcsv">0</p>
                                        </div>
                                 				
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="tab_inner_bg">
                                            <label>Total Campaign Redeemed</label>
                                            <p class="gt_ccr">0</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="tab_inner_bg">
                                            <label>Total Points Earned</label>
                                            <p class="gt_ccpe">0</p>
                                        </div>				
                                    </div>
					<span data-toggle="tooltip" data-placement="right" class="notification_tooltip notification_tooltip_managerewardzone" title="Displays last 18 month's records." data-html="true" data-original-title="Displays last 18 month's records." style="display: none;">&nbsp;&nbsp;&nbsp;</span>
                                </div>
                            </div>

                            <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                                <tr>
                                    <td align="left" valign="top">
                                        <div class="disply_09"><a href="/merchant/add-rewardzone-campaign.php" class="add">Add Campaign</a></div>
                                        <div class="tab_inner11">
                                            <div class="search_area01 disply_09">
                                                <label>Year:</label>
                                                <select id="camp_filter_year">
                                                    <?php
                                                    for ($y = date('Y') - 2; $y <= date('Y'); $y++) {
                                                            ?>
                                                            <option value="<?php echo $y; ?>" 
                                                            <?php
                                                            if ($y == date('Y')) {
                                                                    echo "selected";
                                                            }
                                                            ?> > <?php echo $y; ?> </option>
                                                            <?php }
                                                            ?>
                                                </select>
                                                <!--<label>Month:</label>
                                                <select id="camp_filter_month">
                                                    <option value="0">All</option>
                                                <?php
                                                /* for ($m = 1; $m <= 12; $m++) {
                                                  $month = date("F", mktime(0, 0, 0, $m, 1, 2000));
                                                  $m = date("m", mktime(0, 0, 0, $m, 1, 2000));
                                                  ?>
                                                  <option value="<?php echo $m; ?>"><?php echo $month; ?></option>
                                                  <?php
                                                  } */
                                                ?> 
                                                </select>-->
                                                <label>Category:</label>
                                                <select id="camp_filter_category" >
                                                    <?php
                                                    $array_cat = array();
                                                    $array_cat['active'] = 1;
                                                    $RSCat = $objDB->Show("categories", $array_cat);
                                                    ?>
                                                    <option value="0" selected="selected"  >ALL</option>
                                                    <?php
                                                    while ($Row = $RSCat->FetchRow()) {
                                                            ?>
                                                            <option value="<?= $Row['id'] ?>"  ><?= $Row['cat_name'] ?></option>
                                                            <?php
                                                    }
                                                    ?>
                                                </select> 
                                                <label>Campaign Status:</label>
                                                <select id="camp_filter_status">
													active
                                                    <option value="3" selected="selected" >All</option>
                                                    <option value="1" >Published</option>
                                                    <option value="0">Not Published</option>
                                                    <!--<option value="2">Expired</option>-->
                                                </select> 
                                                <input type="button" value="Filter" name="filterrwdCampaign" id="filterrwdCampaign" />
                                            </div>
                                            <div class="datatable_container">	
                                                <div id="div_rewardzone_campaign_table"> 		
                                                    <table width="100%"  border="0" class="tableMerchant"  id="rewardzone_campaign_table">
                                                        <thead>
                                                            <tr>
                                                                <th>Title</th>
                                                                <th>Status</th>
                                                                <th>value</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <!--End of div_campaign --></div>
                        <!--end of all_div_container--></div>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
                <!--end of footer-->
            </div>
        </div>
    </body>
</html>
<?php
$_SESSION['msg'] = "";
?>
