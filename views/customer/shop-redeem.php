<?php
/* * ****** 
  @USE : shop redeem
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : review-address.php, header.php, process.php
 * ******* */
check_customer_session();
?>
<!DOCTYPE HTML>
<html prefix="og: https://ogp.me/ns#" itemscope itemtype="https://schema.org/Thing" itemid="<?php echo $_SERVER['SCRIPT_URI']; ?>"lang="en">

    <head>
        <title>ScanFlip | Reward Zone</title>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1" >
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <meta http-equiv="Cache-control" content="public,max-age=86400,must-revalidate">
        <meta content="NOINDEX" name="ROBOTS">
        <link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
        <link href="<?php echo ASSETS_CSS; ?>/c/cat.css" rel="stylesheet" type="text/css">
        <script  type="text/javascript" src="<?= ASSETS_JS ?>/c/jquery-1.9.0.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>

    </head>
    <?php flush(); ?>
    <body>

        <script>
                jQuery(document).ready(function () {
                    var hashval = window.location.hash;
                    hashval = unescape(hashval);
                    hashval = hashval.substring(2, (hashval.length - 1));
                    var split_arr = hashval.split("|");
                    var giftcard_id = split_arr[1];
                    if (hashval == "")
                    {
                    }
                    else
                    {
                        jQuery("#hdn_reload_pre_selection").val(giftcard_id);
                    }
                });
        </script>
        <?php
        require_once(CUST_LAYOUT . "/header.php");
        ?>
        <script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS; ?>/jquery.dataTables.js"></script>

        <div id="content" class="cantent">
            <input type="hidden" name="hdn_reload_pre_selection" id="hdn_reload_pre_selection" value="" />
            <input type="hidden" name="hdn_user_country" id="hdn_user_country" value="<?php echo $_SESSION['customer_info']['country']; ?>" />
            <div class="my_main_div">
                <div id="contentContainer" class="contentContainer">
                    <h1>Gift Cards</h1>
                    <div class="gft_leftpart">
                        <div class="giftcardfilter giftbox" id="giftcardfilter-rewardtype">
                            <h4>
                                Reward Type
                            </h4>

                            <div class="content">
                                <ul class="common-form content no-scroll">
                                    <?php
                                    $reward_types = $objDB->Conn->Execute('select * from reward_types where id<>3');
                                    if ($reward_types->RecordCount() > 0) {
                                            while ($rt = $reward_types->FetchRow()) {
                                                    ?>
                                                    <li>
                                                        <label>
                                                            <input class="reward_type" type="radio" <?php echo ($rt['id'] == 1) ? 'checked' : ''; ?> value="<?php echo $rt['id']; ?>" name="radio">
                                                            <span>
                                                                <a class="<?php echo ($rt['id'] == 1) ? 'selected' : ''; ?>" href="javascript:void(0)">
                                                                    <?php echo $rt['reward_type']; ?>
                                                                </a>
                                                            </span>
                                                        </label>
                                                    </li>
                                                    <?php
                                            }
                                    }
                                    ?>

                                </ul>
                            </div>
                        </div>
                        <div class="giftcardfilter giftbox" id="giftcardfilter-categories">

                            <h4>
                                Categories
                            </h4>
                            <input type="hidden" value="0" id="cat_id" />
                            <ul class="common-form content no-scroll" id="categories_ids">
                                <li class="cat-selected">
                                    <a href="javascript:void(0)" data-catid="0"> All Categories</a>
                                </li>
                                <?php
                                $sql = "Select * from giftcard_categories where active=1 ";
                                $RS_cat = $objDB->Conn->Execute($sql);
                                while ($Row = $RS_cat->FetchRow()) {
                                        ?>
                                        <li>
                                            <a href="javascript:void(0)" data-catid ="<?php echo $Row['id']; ?>"> <?php echo $Row['cat_name']; ?></a>
                                        </li>
                                <?php } ?>
                            </ul>
                        </div>

                        <div class="giftcardfilter giftbox" id="giftcardfilter-price">
                            <h4>
                                Price Filter
                                <a href="javascript:void(0)" style="visibility:hidden">
                                    <span>Clear</span>
                                </a>
                            </h4>
                            <div class="content">
                                <div class="common-form" id="input-price-range">
                                    <div class="row">
                                        <div class="cell">
                                            <div class="input-price">
                                                <span class="currency-symbol">$</span>
                                                <input type="tel" id="min" placeholder="min" value="" name="min" step="any">
                                            </div>
                                        </div>
                                        <div class="cell">
                                            <span class="price-range-to">

                                                to

                                            </span>
                                        </div>
                                        <div class="cell">
                                            <div class="input-price">
                                                <span class="currency-symbol">$</span>
                                                <input type="tel" id="max" value="" name="max" placeholder="max" step="any">
                                            </div>
                                        </div>
                                        <div class="cell">
                                            <button type="submit" class="btn btn-secondary" id="filter-price" >
                                                <span class="ss-icon ss-directright"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="giftcardfilter giftbox" id="giftcardfilter-shipto">
                            <h4>
                                Ship To
                            </h4>
                            <div class="content">
                                <ul class="common-form content no-scroll">
                                    <li id="shop-location-display">
                                        <a href="javascript:void(0)" class="has-change-link location " id="shop-location-name">
                                            <?php echo $_SESSION['customer_info']['country']; ?> </a>
                                        <a class="change-link" href="javascript:void(0)" id="shop-location-change">Change</a>
                                    </li>
                                    <li style="display:none" class="combo-ship-to">
                                        <select id="ship_to">
                                            <option value="USA" <?php
                                            if (strtolower($_SESSION['customer_info']['country']) == strtolower("USA")) {
                                                    echo "selected";
                                            }
                                            ?> >USA</option>
                                            <option value="Canada" <?php
                                            if (strtolower($_SESSION['customer_info']['country']) == strtolower("Canada")) {
                                                    echo "selected";
                                            }
                                            ?>>Canada</option>
                                        </select>
                                    </li>

                            </div>
                        </div>
                        <div class="giftcardfilter giftbox" id="giftcardfilter-merchant">
                            <h4>
                                Merchant 
                                <a href="javascript:void(0)" style="visibility:hidden">
                                    <span>Clear</span>
                                </a>
                            </h4>
                            <div class="content">
                                <ul class="common-form content no-scroll">
                                    <li><div style="display:table"><div class="cell"><input type="text" value="" name="merchant_name" id="merchant_name" > </div><div class="cell">
                                                <button type="submit" class="btn btn-secondary" id="filter-merchant" >
                                                    <span class="ss-icon ss-directright"></span>
                                                </button>
                                            </div></div></li>

                            </div>
                        </div>
                    </div>
                    <div class="gft_rightpart" >

                        <!-- item detail container --->
                        <div class="giftcrd_detail">

                        </div>
                        <!--- item detail container --->

                        <table id="gft_itemContainer"></table>
                        <!-- item container -->

                        <!-- navigation holder -->
                        <div class="holder"></div>
                    </div>


                </div><!--end of my_main_div-->
                <div class="shp_redeem_note"><span>The merchants represented are not sponsors of Scanflip or otherwise affiliated with Scanflip. The logos and other identifying marks attached are trademarks of and owned by each represented company and/or its affiliates. Please visit each company's website for additional terms and conditions.</span></div>
                <?php require_once(CUST_LAYOUT . "/before-footer.php"); ?>
            </div><!--end of content-->

            <?php require_once(CUST_LAYOUT . "/footer.php"); ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    var rtable = jQuery('#gft_itemContainer').dataTable({
                        "bFilter": false,
                        "sPaginationType": "full_numbers",
                        "bProcessing": true,
                        "bServerSide": true,
                        "oLanguage": {
                            "sProcessing": "Loading..."
                        },
                        "iDisplayLength": 9,
                        "sAjaxSource": "<?php echo WEB_PATH; ?>/process.php",
                        //"iDeferLoading": <?php //echo $total_records1;     ?>,
                        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                            nRow.className = "load_gftcrd";
                            return nRow;
                        },
                        "fnDrawCallback": function (aData) {
                            jQuery("#gft_itemContainer").show();
                            jQuery(".gft_rightpart .giftcrd_detail").hide();
                            jQuery(".dataTables_paginate").show();

                        },
                        "fnServerParams": function (aoData) {
                            aoData.push({"name": "reward_zone", "value": true}, {"name": "reward_type", "value": $('input[type="radio"]:checked').val()}, {"name": "catid", "value": $('#cat_id').val()}, {"name": "min_cur", "value": $('#min').val()}, {"name": "max_cur", "value": $('#max').val()}, {"name": "ship_to", "value": $('#ship_to').val()}, {"name": "merchant_name", "value": $('#merchant_name').val()});
                        },
                        "fnServerData": fnDataTablesPipeline,
                        "aoColumns": [
                            {"bSortable": true},
                            //{"bSortable": true,"sClass":"load_img09"},
                            //{"bSortable": true,"sClass":"load_img09"}
                        ]
                    });

                    jQuery(document).on('click', '#categories_ids li a', function () {
                        jQuery('#categories_ids li').removeClass('cat-selected');
                        var cid = jQuery(this).data('catid');
                        $('#cat_id').val(cid);
                        jQuery(this).parent('li').addClass('cat-selected');
                        rtable.fnDraw();
                    });

                    $(".input-price input").on('keydown', function (event) {
                        if (event.keyCode == 13) {
                            // do stuff
                            jQuery('#filter-price').trigger('click');
                        }
                    });
                    
                    
                    jQuery(document).on('click',"li#tab-type a",function () {

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

                    $("#merchant_name").on('keydown', function (event) {
                        if (event.keyCode == 13) {
                            // do stuff
                            jQuery('#filter-merchant').trigger('click');
                        }
                    })

                    jQuery('#filter-price').on('click', function () {
                        rtable.fnDraw();
                        jQuery("#giftcardfilter-price a").css("visibility", "visible");
                    });

                    jQuery(document).on('click', '#filter-merchant', function () {
                        jQuery("#giftcardfilter-merchant a").css("visibility", "visible");
                        rtable.fnDraw();
                    });

                    jQuery(document).on('change', '#ship_to', function () {
                        jQuery("#shop-location-name").text(this.value);
                        jQuery(".combo-ship-to").css("display", "none");
                        jQuery("#shop-location-display").css("display", "block");
                        rtable.fnDraw();

                    });

                    jQuery(".change-link").click(function () {
                        jQuery(".combo-ship-to").css("display", "block");
                        jQuery("#shop-location-display").css("display", "none");
                    });

                    jQuery("#giftcardfilter-categories li").click(function () {
                        var cat = jQuery(this).find("a").attr("data-catid");
                        jQuery("#giftcardfilter-categories li").removeClass('cat-selected');
                        jQuery(this).addClass("cat-selected");

                    });

                    jQuery("#giftcardfilter-price a").click(function () {
                        jQuery("#min").val("");
                        jQuery("#max").val("");
                        jQuery("#giftcardfilter-price a").css("visibility", "hidden");
                        rtable.fnDraw();
                    });
                    jQuery("#giftcardfilter-merchant a").click(function () {
                        jQuery("#merchant_name").val("");
                        jQuery("#giftcardfilter-merchant a").css("visibility", "hidden");
                        rtable.fnDraw();
                    });

                    jQuery(document).on("click", ".gft_c09", function () {
			
				jQuery(".holder").css("display", "none");
		                jQuery.ajax({
		                    type: "POST",
		                    url: 'process.php',
		                    data: 'load_giftcard_detail=true&giftcardid=' + jQuery(this).attr("data-gift")+'&reward_type='+jQuery(this).data('reward'),
		                    async: false,
		                    success: function (msg)
		                    {
		                        jQuery("#gft_itemContainer").hide();
		                        jQuery(".dataTables_paginate").hide();
		                        jQuery(".gft_rightpart .giftcrd_detail").html(msg);
		                        jQuery(".gft_rightpart .giftcrd_detail").show();
		                        bind_redeem_points();
		                    }
		                });
			
                        
                    });
                    
                    jQuery(document).on('click','.redeem_camp',function(){
                    		jQuery.ajax({
		                    type: "POST",
		                    url: 'process.php',
		                    data: 'view_rewardzone_campaign=true&id=' + jQuery(this).attr('campid')+'&reward_type='+jQuery(this).data('reward'),
		                    //async: false,
		                    success: function (msg)
		                    {
		                        jQuery("#gft_itemContainer").hide();
		                        jQuery(".dataTables_paginate").hide();
		                        jQuery(".gft_rightpart .giftcrd_detail").html(msg);
		                        jQuery(".gft_rightpart .giftcrd_detail").show();
		                        //jQuery("div.tabs:eq(2)").css('visibility','hidden');
		                        jQuery("div.tabs:eq(2)").hide();
		                        
		                    }
		                });	
                    });
                    
                    jQuery(document).on('click','.calltoaction_c a',function(){
                    
                    		$.ajax({
					type: "POST",
					url: 'process.php',
					data: 'redeem_merchant_reward_type=true&id=' + $(this).data('campid')+'&reward_type='+$(this).data('reward'),
					//async: false,
					success: function (msg)
					{
						if(msg>0){
							jQuery.fancybox({
						                content: '<p>You have successfully got the redeem points.</p><p><input type="submit" class="btn btn-primary" value="OK" onclick="load_reward()" /></p>',
						                type: 'html',
						                openSpeed: 300,
						                closeSpeed: 300,
						                // topRatio: 0,
						                changeFade: 'fast',
						                beforeShow: function () {
						                    jQuery(".fancybox-inner").addClass("msgClass");
						                },
						                helpers: {
						                    overlay: {
						                        opacity: 0.3
						                    } // overlay
						                }
						            });
						}
					}
		                });	
                    
                    })

                    jQuery("#popupcancel").live("click", function () {
                        jQuery.fancybox.close();
                    });
                    
                    jQuery('#giftcardfilter-rewardtype ul li a').on('click',function(){
    				var inp = jQuery(this).parent('span').prev('input');
    				inp.trigger('click');
    				
                    })

                    jQuery('.reward_type').on('click', function () {
                        jQuery('#cat_id').val('0');
			jQuery('.gft_leftpart').prev('h1').text(jQuery(this).parent('label').text());
                        if (jQuery(this).val() == 2) {
                            
                            jQuery('#giftcardfilter-shipto').hide();

                        } else {
                            jQuery('#contentContainer').next('h1').text(jQuery(this).parent('label').text());
                            jQuery('#giftcardfilter-shipto').show();
                        }
                        jQuery.ajax({
                            type: "POST",
                            url: 'process.php',
                            data: 'load_rw_category=true&id=' + jQuery(this).val(),
                            //async: false,
                            success: function (msg)
                            {
                                rtable.fnDraw();
                                var obj = jQuery.parseJSON(msg);
                                var htm = '<li class="cat-selected"><a href="javascript:void(0)" data-catid="0"> All Categories</a></li>';
                                jQuery.each(obj, function (index, value) {
                                    htm += '<li><a href="javascript:void(0)" data-catid="' + value.id + '"> ' + value.cat_name + '</a></li>';
                                });

                                jQuery('#categories_ids').html(htm);
                            }
                        });
                    });

                })

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

                /*** redeem function ***************/
                function bind_redeem_points()
                {
                    jQuery(".calltoaction a").click(function () {
                        giftcardid = jQuery(this).attr("data-gftcid");
                        engiftcardid = jQuery(this).attr("data-ftencid");
                        giftcardshipto = jQuery(this).attr("data-gftshipto");
                        
                        var is_mer = jQuery(this).data('merchant');
                        var rewt = jQuery(this).data('reward');
                        
                        
                        /*** check user is login or not ***********/
                        loginstatus = false;
                        $.ajax({
                            type: "POST",
                            url: 'process.php',
                            data: 'shop_redeem_login_or_not=true&giftcardid=' + giftcardid,
                            async: false,
                            success: function (msg)
                            {
                                var obj = jQuery.parseJSON(msg);
                                if (obj.status == "false")
                                {
                                    loginstatus = false;
                                    window.location.href = obj.link;//+obj.hash_link;
                                    //location.reload();

                                }
                                else
                                {
                                    loginstatus = true;
                                }
                            }
                        });
                        
                        if(is_mer == 1){
                        	$.ajax({
					type: "POST",
					url: 'process.php',
					data: 'redeem_merchant_reward_type=true&id=' + giftcardid+'&reward_type='+rewt,
					//async: false,
					success: function (msg)
					{
						if(msg>0){
							jQuery.fancybox({
						                content: '<p>You have successfully got the redeem points.</p><p><input type="submit" class="btn btn-primary" value="OK" onclick="load_reward()" /></p>',
						                type: 'html',
						                openSpeed: 300,
						                closeSpeed: 300,
						                // topRatio: 0,
						                changeFade: 'fast',
						                beforeShow: function () {
						                    jQuery(".fancybox-inner").addClass("msgClass");
						                },
						                helpers: {
						                    overlay: {
						                        opacity: 0.3
						                    } // overlay
						                }
						            });
						}
					}
		                });
                        
                        }else{
                        	if (loginstatus)
		                {
		                    if (jQuery("#hdn_user_country").val() == giftcardshipto)
		                    {
		                        window.location.href = "<?php echo WEB_PATH; ?>/address_select.php?order=" + engiftcardid;
		                    }
		                    else {
		                        //alert("Sorry this giftcard is excluded from your ship to country.");
		                        var msg_box = "Sorry this giftcard is excluded from your ship to country.";
		                        var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
		                        var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg_box + "</div>";
		                        var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"]; ?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
		                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

		                        try
		                        {

		                            jQuery.fancybox({
		                                content: jQuery('#dialog-message').html(),
		                                type: 'html',
		                                openSpeed: 300,
		                                closeSpeed: 300,
		                                // topRatio: 0,
		                                changeFade: 'fast',
		                                beforeShow: function () {
		                                    jQuery(".fancybox-inner").addClass("msgClass");
		                                },
		                                helpers: {
		                                    overlay: {
		                                        opacity: 0.3
		                                    } // overlay
		                                }
		                            });
		                        }
		                        catch (e)
		                        {
		                            //alert(e);
		                        }
		                    }

		                }
                        }

                        
                    });
                }
                function load_reward(){
                
                	jQuery('input[name="radio"]:checked').trigger('click');
                	jQuery.fancybox.close();
                }
            </script>
            <div id="dialog-message" style="display:none;">
            </div>
    </body>
</html>
<?php
$_SESSION['req_pass_msg'] = "";
?>
