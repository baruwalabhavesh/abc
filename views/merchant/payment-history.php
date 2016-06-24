<?php
/**
 * @uses payment history
 * @used in pages : profile-left.php
 * @author Sangeeta Raghavani
 */
check_merchant_session();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Payment History</title>
        <?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
        <script type="text/javascript" src="<?= ASSETS_JS ?>/bootstrap.min.js"></script>		
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
        <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>

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

                    $(document).ready(function () {
                        $('#payment_transaction_table').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "info": false,
			    "bSort": false,
                            //"order": [[0, "asc"]],
                            //"aoColumnDefs": [{"bSortable": false, "aTargets": [1, 2, 3]}],
                            "iDisplayLength": 10,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "btngetpaymenthistory", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'}, {"name": "days", "value": $('#filter_transactions').val()});

                            },
				//"fnServerData": fnDataTablesPipeline,
                            "oLanguage": {
                                "sEmptyTable": "No payment transactions founds in the system.",
                                "sZeroRecords": "No payment transactions to display",
                                "sProcessing": "Loading..."
                            },
                        });
                        jQuery(".table_loader").css("display", "none");
                        jQuery(".datatable_container").css("display", "block");
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

                                <div id="upload_business_logo_ajax1" class="upload_business_logo_ajax nax_scrape_indicator hidden_elem" style="display:none;">
                                    <img height="32" width="32" alt="" src="<?php echo ASSETS_IMG . "/24" ?>.GIF" >
                                </div>
                                <div align="center">
                                    <img   src="<?php echo ASSETS_IMG . "/32" ?>.GIF" class="table_loader defaul_table_loader" />
                                </div>
                                <div class="datatable_container" style="display: none;">	
                                    <div class="view_filter">
                                        <span>View : </span>
                                        <select name="filter_transactions" id="filter_transactions">
                                            <option value="0"> -- All -- </option>
                                            <option value="7"> Last 7 days </option>
                                            <option value="90"> Last 90 days </option>
                                            <option value="365"> 1 Year </option>
                                        </select>
                                    </div>
                                    <div id="div_payment_transaction_table">  
                                        <table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant"  id="payment_transaction_table">
                                            <thead>
                                                <tr>
                                                    <th >Order Date</th>
						    <th >Order Number</th>
                                                    <th >Transaction Id</th>
                                                    <th >Amount</th>
						    <th >Invoice</th>
                                                    <!--<th >Reference Number</th>-->
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

            <!---------start footer--------------->
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
                <!--end of footer--></div>
        </div>
        <script type="text/javascript">
               // $("a#myprofile").css("background-color", "orange");
               // $("a#profile-link").css("color", "#0066FF");
                //$("a#password-link").css("color", "#0066FF");
                jQuery("a#payment-link").css("color", "orange");
        
                jQuery("#filter_transactions").change(function () {
                    jQuery('#payment_transaction_table').dataTable().fnDraw();
                });
        </script>
    </body>
</html>
<?php
$_SESSION['msg'] = "";
?>
