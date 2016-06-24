<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY . "/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

$arr = file(WEB_PATH . '/admin/process.php?btngetpaymenthistory=yes');

if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$total_records = $json->total_records;
$records_array = $json->records;
//echo $total_records;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Admin Panel</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="<?= ASSETS_CSS ?>/a/template.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS ?>/a/fancybox/jquery.fancybox-buttons.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS ?>/a/fancybox/jquery.fancybox.css" media="screen" />
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/fancybox/jquery_for_popup.js"></script>
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/fancybox/jquery.fancybox.js"></script> 

        <style type="text/css" title="currentStyle">
            @import "<?php echo ASSETS_CSS ?>/a/demo_page.css";
            @import "<?php echo ASSETS_CSS ?>/a/demo_table.css";
        </style>

        <script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf-8">
                /****** navigate to selected page ***************/
                $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings) {
                    return {
                        "iTotalPages": oSettings._iDisplayLength === -1 ?
                                0 : Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
                    };
                };
                /************* Get current page ****/
                $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings)
                {
                    return {
                        "iStart": oSettings._iDisplayStart,
                        "iEnd": oSettings.fnDisplayEnd(),
                        "iLength": oSettings._iDisplayLength,
                        "iTotal": oSettings.fnRecordsTotal(),
                        "iFilteredTotal": oSettings.fnRecordsDisplay(),
                        "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                        "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
                    };
                };

                jQuery(document).ready(function () {

                    var table = jQuery('#example').dataTable({
                        "sPaginationType": "full_numbers",
                        'bFilter': false,
                        "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                        "iDisplayLength": <?php echo $language_msg['common']['etries_per_page']; ?>,
                        "aaSorting": [],
                        "aoColumns": [
                            {"bSortable": false},
                            {"bSortable": false},
                            {"bSortable": false},
                            {"bSortable": false},
                            {"bSortable": false}
                        ],
                        "fnDrawCallback": function () {
                            if (((this.fnPagingInfo().iPage) + 1) == this.fnPagingInfo().iTotalPages)
                            {
                                jQuery(".get_mores").css("display", "block");
                            }
                            else {
                                jQuery(".get_mores").css("display", "none");
                            }
                            jQuery("#current_page").val((this.fnPagingInfo().iPage) + 1);
                            jQuery("#total_pages").val(this.fnPagingInfo().iTotalPages);
                        }


                    });
                    //	table.fnPageChange(2,true);

                });
        </script>
    </head>

    <body class="order_list">
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
                        <input type="hidden" name="increase_val" id="increase_val" value="<?php echo $language_msg['common']['bydefalt_entries_per_query']; ?>" />
                        <h2>Manage Purchase Point Orders </h2>
                        <!--edit delete icons table-->
                        <div>
                            <table   cellspacing="10px" >
                                <tr><td>Filter By : Year <select id="opt_filter_year">
                                            <?php
                                            for ($y = date('Y') - 2; $y <= date('Y'); $y++) {
                                                    ?>
                                                    <option value="<?php echo $y; ?>" <?php
                                                    if (date('Y') == $y) {
                                                            echo "selected";
                                                    }
                                                    ?> > <?php echo $y; ?> </option>
                                                    <?php }
                                                    ?>
                                        </select>
                                        &nbsp; month : <select id="opt_filter_month" >
                                            <option value="0" >---- ALL ----</option>
                                            <option value="01"  >January</option>
                                            <option value="02"  >February</option>
                                            <option value="03" >March</option>
                                            <option value="04" >April</option>
                                            <option value="05" >May</option>
                                            <option value="06" >Jun</option>
                                            <option value="07" >July</option>
                                            <option value="08" >August</option>
                                            <option value="09" >September</option>
                                            <option value="10" >October</option>
                                            <option value="11" >November</option>
                                            <option value="12" >December</option>
                                        </select>
                                        <?php
                                        $sql_busi = "select id,business from merchant_user;";
                                        $RS_busi = $objDB->Conn->Execute($sql_busi);
                                        ?>
                                        &nbsp; Business : <select id="opt_filter_business" style="width:235px;" >
                                            <option value="0" >---- ALL ----</option>
                                            <?php
                                            if ($RS_busi->RecordCount() > 0) {
                                                    while ($Row_busi = $RS_busi->FetchRow()) {
                                                            ?>
                                                            <option value="<?php echo $Row_busi['id']; ?>"><?php echo $Row_busi['business']; ?></option>
                                                            <?php
                                                    }
                                            }
                                            ?>
                                        </select>

                                        <input type="button" name="btn_submit" id="btn_submit" value="Show Result" /></td>
                <!--	<td><a href="edit-category.php?id=<?= $Row['id'] ?>"><img src="<?php echo ASSETS_IMG; ?>/a/icon-edit.png"></a></td>
                        <td><a href="#"><img src="<?php echo ASSETS_IMG; ?>/a/icon-delete.png"></a></td>-->
                                </tr>


                            </table>
                            <input type="hidden" name="hdn_start_limit" id="hdn_start_limit" value="0" />
                            <input type="hidden" name="hdn_changed_filter_year" id="hdn_changed_filter_year" value="2014" />
                            <input type="hidden" name="hdn_changed_filter_month" id="hdn_changed_filter_month" value="0" />
                            <input type="hidden" name="hdn_changed_filter_business" id="hdn_changed_filter_business" value="0" />

                            <div style="color:#FF0000; "><?= $_SESSION['msg']; ?></div>
                            <!--end edit delete icons table--></div>
                        <br/>
                        <div class="ordreslist_container">
                            <input type="hidden" name="total_pages" id="total_pages" value="" />
                            <input type="hidden" name="current_page" id="current_page" value="" />
                            <table  width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
                                <thead>
                                    <tr>
                                        <th width="15%" align="left">Order Date</th>
                                        <th width="10%" align="left">Order Number</th>
                                        <th width="20%" align="left">Merchant Name</th>
                                        <th width="10%" align="left">Amount</th>
                                        <th width="10%" align="left">Reference Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($total_records > 0) {
                                            foreach ($records_array as $Row) {
                                                    ?>
                                                    <tr>
                                                        <td align="left"><?php echo $Row->date ?></td>
                                                        <td align="left"><?php echo $Row->order_id ?></td>
                                                        <td align="left"><?php echo $Row->business ?></td>
                                                        <td align="left"><?php echo $Row->amount ?></td>
                                                        <td align="left"><?php echo $Row->refrence_number ?></td>
                                                    </tr>	
                                                    <?php
                                            }
                                    } else {
                                            ?>
                                            <tr>
                                                <td colspan="5" align="left">No Order is Found.</td>
                                            </tr>
                                            <?php
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                        <div align="right">
                            <a class="get_mores" href="javascript:void(0)">Get More ...</a>
                        </div>

                    </form>

                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <!--end of Container--></div>

        <?
        $_SESSION['msg'] = "";
        ?>

        <script>

                jQuery("a.get_more").click(function () {
                    jQuery("#hdn_start_limit").val(parseInt(jQuery("#hdn_start_limit").val()) + 5);
                    //alert(jQuery("#hdn_start_limit").val());
                    var redirecting = jQuery("#current_page").val();
                    jQuery.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'addnextorders=true&sartlimit=' + jQuery("#hdn_start_limit").val(),
                        async: false,
                        success: function (msg)
                        {
                            $('#example').dataTable().fnDestroy();
                            jQuery("#example tbody").append(msg);

                            var table = $('#example').dataTable({
                                "sPaginationType": "full_numbers",
                                'bFilter': false,
                                "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                                "iDisplayLength": <?php echo $language_msg['common']['etries_per_page']; ?>,
                                "aaSorting": [],
                                "aoColumns": [
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false}
                                ],
                                "fnDrawCallback": function () {
                                    if (((this.fnPagingInfo().iPage) + 1) == this.fnPagingInfo().iTotalPages)
                                    {
                                        if (msg != "")
                                        {
                                            jQuery(".get_mores").css("display", "block");
                                        }
                                        else {
                                            jQuery(".get_mores").css("display", "none");
                                        }
                                    }
                                    else {
                                        jQuery(".get_mores").css("display", "none");
                                    }
                                    jQuery("#total_pages").val(this.fnPagingInfo().iTotalPages);
                                    jQuery("#current_page").val((this.fnPagingInfo().iPage) + 1);

                                }


                            });
                            table.fnPageChange(parseInt(redirecting) - 1, true);

                        }
                    });



                });


                jQuery(".change_st").live("click", function () {
                    var ele = jQuery(this);
                    jQuery.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'change_order_status=true&order_id=' + jQuery(this).attr("data-orderid") + '&status=' + jQuery(this).attr("data-orderstatus"),
                        async: false,
                        success: function (msg)
                        {
                            if (ele.attr("data-orderstatus") == 2)
                            {
                                ele.attr("data-orderstatus", "1");
                                ele.parent().prev("td").text("Pending");

                            }
                            else
                            {
                                ele.attr("data-orderstatus", "2");
                                ele.parent().prev("td").text("Shipped");
                            }

                        }
                    });
                });


                jQuery("#btn_submit").click(function () {
                    //alert('filter_giftcardorder=true&year='+jQuery("#opt_filter_year").val()+'&month='+jQuery("#opt_filter_month").val()+'&status_act='+jQuery("#opt_filter_status").val()+'&sartlimit=0&shipto='+jQuery("#opt_filter_ship_to").val());
                    jQuery("#hdn_changed_filter_month").val(jQuery("#opt_filter_month").val());
                    jQuery("#hdn_changed_filter_year").val(jQuery("#opt_filter_year").val());
                    jQuery("#hdn_changed_filter_business").val(jQuery("#opt_filter_business").val());
                    jQuery.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'filter_purchasepoint_order=true&year=' + jQuery("#opt_filter_year").val() + '&month=' + jQuery("#opt_filter_month").val() + '&business=' + jQuery("#opt_filter_business").val() + '&sartlimit=0',
                        async: true,
                        success: function (msg)
                        {
                            jQuery("#hdn_start_limit").val(0);
                            //	alert(msg);
                            jQuery(".ordreslist_container").html(msg);
                            var table = $('#example').dataTable({
                                "sPaginationType": "full_numbers",
                                'bFilter': false,
                                "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                                "iDisplayLength": <?php echo $language_msg['common']['etries_per_page']; ?>,
                                "aaSorting": [],
                                "aoColumns": [
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false}
                                ],
                                "fnDrawCallback": function () {
                                    //alert(((this.fnPagingInfo().iPage)+1) + "===="+this.fnPagingInfo().iTotalPages);
                                    if (((this.fnPagingInfo().iPage) + 1) == this.fnPagingInfo().iTotalPages)
                                    {
                                        jQuery(".get_mores").css("display", "block");
                                    }
                                    else {
                                        jQuery(".get_mores").css("display", "none");
                                    }
                                    jQuery("#current_page").val((this.fnPagingInfo().iPage) + 1);
                                    jQuery("#total_pages").val(this.fnPagingInfo().iTotalPages);
                                }


                            });

                        }
                    });

                });

                jQuery("a.get_mores").click(function ()
                {
                    var changed_status = false;
                    var load_from_scratch = 0;
                    //	alert(jQuery("#hdn_start_limit").val());
                    jQuery("#hdn_start_limit").val(parseInt(jQuery("#hdn_start_limit").val()) + parseInt(jQuery("#increase_val").val()));
                    //alert(jQuery("#hdn_start_limit").val());
                    if (jQuery("#hdn_changed_filter_month").val() == jQuery("#opt_filter_month").val() && jQuery("#hdn_changed_filter_year").val() == jQuery("#opt_filter_year").val() && jQuery("#hdn_changed_filter_business").val() == jQuery("#opt_filter_business").val())
                    {
                        /// alert("same");
                    }
                    else {
                        //alert("not same");
                        changed_status = true;
                        load_from_scratch = 1;
                    }
                    if (changed_status)
                    {
                        jQuery("#hdn_start_limit").val(0);
                    }
                    var redirecting = jQuery("#current_page").val();

                    jQuery("#hdn_changed_filter_month").val(jQuery("#opt_filter_month").val());
                    jQuery("#hdn_changed_filter_year").val(jQuery("#opt_filter_year").val());
                    jQuery("#hdn_changed_filter_business").val(jQuery("#opt_filter_business").val());

                    //alert('<?php echo WEB_PATH; ?>/admin/process.php?addnextorderss=true&sartlimit='+jQuery("#hdn_start_limit").val()+'&year='+jQuery("#opt_filter_year").val()+'&month='+jQuery("#opt_filter_month").val()+'&status_act='+jQuery("#opt_filter_status").val()+'&shipto='+jQuery("#opt_filter_ship_to").val());
                    jQuery.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'addnext_purchasepoint_order=true&sartlimit=' + jQuery("#hdn_start_limit").val() + '&year=' + jQuery("#opt_filter_year").val() + '&month=' + jQuery("#opt_filter_month").val() + '&business=' + jQuery("#opt_filter_business").val() + '&load_from_scrtch=' + load_from_scratch,
                        async: false,
                        success: function (msg)
                        {
                            // alert(msg);
                            $('#example').dataTable().fnDestroy();
                            if (changed_status)
                            {
                                jQuery(".ordreslist_container").html(msg);
                            }
                            else {
                                jQuery("#example tbody").append(msg);

                            }

                            var table = $('#example').dataTable({
                                "sPaginationType": "full_numbers",
                                'bFilter': false,
                                "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                                "iDisplayLength": <?php echo $language_msg['common']['etries_per_page']; ?>,
                                "aaSorting": [],
                                "aoColumns": [
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false},
                                    {"bSortable": false}
                                ],
                                "fnDrawCallback": function () {
                                    if (((this.fnPagingInfo().iPage) + 1) == this.fnPagingInfo().iTotalPages)
                                    {
                                        if (msg != "")
                                        {
                                            jQuery(".get_mores").css("display", "block");
                                        }
                                        else {
                                            jQuery(".get_mores").css("display", "none");
                                        }
                                    }
                                    else {
                                        jQuery(".get_mores").css("display", "none");
                                    }
                                    jQuery("#total_pages").val(this.fnPagingInfo().iTotalPages);
                                    jQuery("#current_page").val((this.fnPagingInfo().iPage) + 1);

                                }


                            });
                            table.fnPageChange(parseInt(redirecting) - 1, true);

                        }
                    });



                });

                jQuery(".cancel_st").live("click", function () {
                    var ele = jQuery(this);
                    var orderid = jQuery(this).attr("data-orderid");
                    jQuery.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'change_order_status=true&order_id=' + jQuery(this).attr("data-orderid") + '&status=0',
                        async: false,
                        success: function (msg)
                        {
                            ele.attr("data-orderstatus", "0");
                            ele.parent().prev("td").text("Cancelled");

                            jQuery(".change_st[data-orderid='" + orderid + "']").next().detach();
                            jQuery(".cancel_st[data-orderid='" + orderid + "']").next().detach();
                            jQuery(".change_st[data-orderid='" + orderid + "']").detach();
                            jQuery(".cancel_st[data-orderid='" + orderid + "']").detach();


                        }
                    });
                });
        </script>
    </body>
</html>
