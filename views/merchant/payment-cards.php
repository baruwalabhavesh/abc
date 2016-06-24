<?php
/**
 * @uses payment card
 * @used in pages : profile-left.php
 * @author Sangeeta Raghavani
 */
check_merchant_session();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Payment Cards</title>
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
        <style>.fancybox-close { margin-right: 1px !important; margin-top: 1px !important;}</style>

        <!--- tooltip css --->

<!--        <script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>-->
    </head>
    <body>
        <div style="width:100%;text-align:center;">
            <script type="text/javascript" charset="utf-8">
                    /******* initialize jQuery data table ****/
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

                            jQuery.getJSON(sSource, aoData, function (json) {
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

                    function logged_in_or_not() {
                        jQuery.ajax({
                            type: "POST",
                            url: 'process.php',
                            data: 'loginornot=true',
                            success: function (msg)
                            {
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
                        var otable = $('#payment_cards_table').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "info": false,
                            "bSort": false,
                            "iDisplayLength": 10,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "fnPreDrawCallback": function (oSettings) {
                                logged_in_or_not();
                            },
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "get_payment_cards", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'});

                            },
                            "fnServerData": fnDataTablesPipeline,
                            "oLanguage": {
                                "sEmptyTable": "No payment cards founds in the system.",
                                "sZeroRecords": "No payment cards to display",
                                "sProcessing": "Loading..."
                            },
                        });
                        jQuery(".table_loader").css("display", "none");
                        jQuery(".datatable_container").css("display", "block");
                        jQuery(document).on('click', '.card_action', function () {
                            logged_in_or_not();
                            var cardid = jQuery(this).attr('cardid');
                            var data_ac = jQuery(this).attr('data-ac');

                            if (data_ac == 'edit') {
                                jQuery.fancybox({
                                    //content: jQuery('#card_edit_modal').html(),
                                    type: 'html',
                                    minWidth:400,
								    minHeight:400,
									openSpeed: 300,
                                    closeSpeed: 300,
									// topRatio: 0,
                                    changeFade: 'fast',
                                    href: '/merchant/edit-payment-card.php?id=' + cardid,
                                    type: 'ajax',
                                            helpers: {
                                                overlay: {
                                                    closeClick: true,
                                                    opacity: 0.3
                                                } // overlay
                                            }
                                });
                            } else {
                                $('#del_id').val(cardid);
                                jQuery.fancybox({
                                    content: $("#card_del").html(),
                                    type: 'html',
									minWidth:200,
                                    openSpeed: 300,
                                    closeSpeed: 300,
                                    // topRatio: 0,
                                    changeFade: 'fast',
                                    helpers: {
                                        overlay: {
                                            closeClick: false,
                                            opacity: 0.3
                                        } // overlay
                                    }
                                });

                            }
                        });
                        $(document).on('click', '.btn-update', function () {
                            logged_in_or_not();
                            var fm = $('.edit_pay_card').serialize();
                            //console.log(fm);
                            jQuery.ajax({
                                type: "POST",
                                url: 'process.php',
                                data: 'stripe_edit_card=true&' + fm,
                                success: function (msg)
                                {
					var obj = jQuery.parseJSON(msg);
					    if (obj.status == "true")
					    {
						$.fancybox.close();
				                    oCache.iCacheLower = -1;
				                    otable.fnDraw();
					    } else {
						$('.edit_pay_card').prepend(obj.serror);
					    }

                                    
                                }
                            });
                        });

                        $(document).on('click', '.btn-yes-card', function () {
                            logged_in_or_not();
                            jQuery.ajax({
                                type: "POST",
                                url: 'process.php',
                                data: 'stripe_delete_card=true&cid=' + $('#del_id').val(),
                                success: function (msg)
                                {
                                    $.fancybox.close();
                                    oCache.iCacheLower = -1;
                                    otable.fnDraw();
                                }
                            });
                        });

                        $(document).on('click', '.sp_mk_df_card', function () {
                            logged_in_or_not();
                            var cid = $(this).data('id');
                            jQuery.ajax({
                                type: "POST",
                                url: 'process.php',
                                data: 'stripe_default_card=true&cid=' + cid,
                                success: function (msg)
                                {
                                    oCache.iCacheLower = -1;
                                    otable.fnDraw();
                                }
                            });
                        });
			
			$(document).on('click','.btn-no-card',function(){
				$.fancybox.close();
			});
			$(document).on('change','#billing_country',function(){
				var cn = this.value;
				
				jQuery.ajax({
		                        type: "POST",
		                        url: 'get_state.php',
		                        data: 'country=' + cn,
		                        success: function (msg)
		                        {
		                            $('#billing_state').html(msg);
		                        }
		                    });
			});

                        $(document).on('click', '#newCard', function () {
                            logged_in_or_not();
                            jQuery.fancybox({
                                //content: jQuery('#card_edit_modal').html(),
                                type: 'html',
								minWidth:400,
								minHeight:400,
								autoScale:true,
                                openSpeed: 300,
                                closeSpeed: 300,
                                // topRatio: 0,
                                changeFade: 'fast',
                                href: '/merchant/add-payment-card.php',
                                type: 'ajax',
                                        helpers: {
                                            overlay: {
                                                closeClick: true,
                                                opacity: 0.3
                                            } // overlay
                                        }
                            });

                        });

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
                    <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                        <tr>
                            <td width="25%" align="left" valign="top" >
                                <?php
                                require_once(MRCH_LAYOUT . "/profile-left.php");
                                ?>
                            </td>
                            <td width="75%" align="left" valign="top">
							<button id="newCard" class="btn btn-default add_new_card1">Add Card</button>
                                <!--<input type="button" name="newCard" id="newCard" class="btn btn-default" value="Add"/> -->
                                <div class="datatable_container" style="display: none;">	
                                    <div id="div_payment_transaction_table">  
                                        <table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant"  id="payment_cards_table">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Number</th>
                                                    <th>Expires</th>
                                                    <th></th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="clear">&nbsp;</div><br>

                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <div id="card_edit_modal" style="display: none;">

            </div>
            <div id="card_del" style="display: none;">
                <h4 class="head_msg">Please Confirm</h4>
                <input type="hidden" name="del_id" id="del_id">
                <p>Do you want to delete this card ?</p>
                <button type="button" class="btn btn-default btn-no-card">No</button>
                <button type="button" class="btn btn-default btn-yes-card">Yes</button>
            </div>
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
                <!--end of footer--></div>
        </div>
        <!-- Modal -->
        <script type="text/javascript">
                jQuery("a#payment-card-link").css("color", "orange");
        </script>
    </body>
</html>
<?php
$_SESSION['msg'] = "";
?>
