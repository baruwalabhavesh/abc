<?php
/* * ****** 
  @USE : zip code block on search deal, my deal and mymerchant page
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : header.php
 * ******* */
?>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/modernizr.custom.26450.js"></script>
<script language="javascript">

        jQuery("body").on("click", "#zip", function (event) {


            jQuery(this).select();

            //jQuery("#zipcode_close").show();
            jQuery("#zipcode_close").css("display", "block");
            //  alert("1");
            // start logic last 3 searched location

            url = '<?= WEB_PATH ?>/get_addresses.php';
            //alert(url);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: "couponname=last3",
                async: true,
                beforeSend: function () {
                    //closePopup(200);
                    //open_popup('Wait');				   				  
                },
                success: function (result) {
                    // alert("1"); 

                    //alert(result);
                    var ar_searched = result.split("###");
                    //alert(ar_searched[0]);
                    //alert(ar_searched[1]);
                    if (ar_searched[0] > 0)
                    {
                        document.getElementById("mainclass_autocomplete").style.display = "block";
                        document.getElementById("autocomplete").style.display = "block";
                        document.getElementById("autocomplete_close").style.display = "block";
                        document.getElementById("autocomplete").innerHTML = ar_searched[1];
                    }


                }
            });

            // end logic last 3 searched location	


        });

        jQuery("body").on("click", "#zipcode_close", function () {
            jQuery("#zip").val("");

            jQuery("#zipcode_close").hide();

        });

        jQuery("body").on("click", "#autocomplete_close", function () {


            jQuery("#zipcode_close").hide();
            jQuery("#autocomplete").hide();
            jQuery("#mainclass_autocomplete").hide();

        });
        var WEB_PATH = "<?= WEB_PATH ?>";
        function inputKeyUp()
        {
            //  alert("hi");
            /*if(e.keyCode==13)
             {
             //search_deal();
             }*/
        }
        function myFunction(txt_val, e)
        {
            if (txt_val.length >= 1)
            {
                document.getElementById("zipcode_close").style.display = "block";
            }
            else
            {
                document.getElementById("zipcode_close").style.display = "none";
            }
            if (txt_val.length >= 3)
            {
                var n = txt_val.indexOf(",");
                if (n >= 0)
                {
                    var txt_val = txt_val.split(",");
                    txt_val = txt_val[0];
                }
                url = '<?= WEB_PATH ?>/get_addresses.php';
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "couponname=" + txt_val,
                    async: true,
                    beforeSend: function () {
                        //closePopup(200);
                        //open_popup('Wait');				   				  
                    },
                    success: function (result) {

                        //alert(result);
                        var ar_searched = result.split("###");
                        //alert(ar_searched[0]);
                        //alert(ar_searched[1]);
                        if (ar_searched[0] > 0)
                        {
                            document.getElementById("mainclass_autocomplete").style.display = "block";
                            document.getElementById("autocomplete").style.display = "block";
                            document.getElementById("autocomplete_close").style.display = "block";
                            document.getElementById("autocomplete").innerHTML = ar_searched[1];
                        }

                    }

                });
            }
            else
            {
                document.getElementById("autocomplete").style.display = "none";
                document.getElementById("mainclass_autocomplete").style.display = "none";

                document.getElementById("autocomplete_close").style.display = "none";
                //document.getElementById("div_error_content").innerHTML="";

            }


            if (e.keyCode == 13)
            {
                search_deal();

            }

        }

        function repalcevalue(val)
        {
            var val = val.innerHTML;
            document.getElementById("zip").value = val;
            document.getElementById("autocomplete").style.display = "none";
            document.getElementById("mainclass_autocomplete").style.display = "none";
            search_deal();
        }


        function getCookie(c_name)
        {
            var i, x, y, ARRcookies = document.cookie.split(";");
            for (i = 0; i < ARRcookies.length; i++)
            {
                x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
                y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
                x = x.replace(/^\s+|\s+$/g, "");
                if (x == c_name)
                {
                    return unescape(y);
                }
            }
        }

        function setCookie(c_name, value, exdays)
        {
            //alert(c_name);
            //alert(value);
            //alert(exdays);

            var exdate = new Date();
            exdate.setDate(exdate.getDate() + exdays);
            var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
            document.cookie = c_name + "=" + c_value;
        }

        function checkCookie()
        {
            //alert("check cookie");
//            var catid = jQuery("#slider_iframe").contents().find(".current").attr("id");
//                if(jQuery("#slider_iframe").contents().find(".current").length == 0)
//                 {
//                        catid= 0;
//                 }
//                 else
//                     {
//                         catid =jQuery("#slider_iframe").contents().find(".current").attr("id")
//                     }

            var val = "<?php if (isset($_SESSION['customer_id'])) echo $_SESSION['customer_id'] ?>";

            //var zip = document.getElementById('zip').value;

            var zip = "<?php if (isset($_COOKIE['searched_location'])) echo $_COOKIE['searched_location']; ?>";
            //alert(zip+"====");
            var mymiles = document.getElementById("hdnmiles").value;
            var zoominglevel = "<?php if (isset($_COOKIE['zoominglevel'])) echo $_COOKIE['zoominglevel']; ?>";

            if (zoominglevel == "")
            {
                zoominglevel = "10";

            }
            //alert(zip);
            //alert(catid);
            //alert(mymiles);
            //document.getElementById("slider_iframe").src = "<?= WEB_PATH ?>/search-deal-map.php?zip="+zip+"&category_id="+catid+"&miles="+mymiles+"&zoominglevel="+zoominglevel;

            //document.getElementById("slider_iframe").src = "<?= WEB_PATH ?>/search-deal-map.php?zip="+zip+"&category_id="+catid;;
            // alert(val);
            if (val != "")
            {

                //var value=getCookie("searched_location");
                var value = zip;
                //alert("onload "+value);
                setCookie("searched_location", zip, 365);
                if (value == null || value == "")
                {

                    //document.getElementById('zip').value = "Enter Your Zipcode Here";
                    //document.getElementById('hdn_serach_value').value = "Enter Your Zipcode Here";	
                } else {

                    document.getElementById('zip').value = value;
                    document.getElementById('hdn_serach_value').value = value;
                }
            }
            else {

                //var value=getCookie("searched_location");
                var value = zip;
                //alert("onload "+value);
                setCookie("searched_location", zip, 365);
                if (value != null && value != "")
                {

                    document.getElementById('zip').value = value;
                    document.getElementById('hdn_serach_value').value = value;
                }
                else
                {

                    document.getElementById('zip').value = "Enter Your Zipcode Here";
                    document.getElementById('hdn_serach_value').value = "Enter Your Zipcode Here";
                }
            }
        }

</script>
<!--
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35713507-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
-->
<!--for scroller start-->

<!-- start of zipCodeDiv2-->
<?php
if (basename($_SERVER['REQUEST_URI']) == "my-deals.php" || basename($_SERVER['REQUEST_URI']) == "mymerchants.php" || basename($_SERVER['REQUEST_URI']) == "search-deal.php") {
        ?>

        <div id="zipCodeDiv2" class="zipCodeDiv2" >

            <div id="zipCodeDivSearch" style="">
                <div class="div_zip_zipcode_close">
                    <div class="div_zipcode_close" id="zipcode_close">
                    </div>		

                    <div class="div_zip">
                        <input onkeyup="myFunction(this.value, event)" placeholder="Try using city,zip.." type="text" name="zip" id="zip" autocomplete="off" size="35px" value="<?php echo $language_msg["search"]["enter_your_zipcode_here"]; ?>" />
                    </div>
                </div>

                <div id="mainclass_autocomplete" class="autocomplete_mainclass" >
                    <div id="autocomplete" class="autocompletecontent" >	
                    </div>
                    <div class="mytextclose">
                        <input type="button" id="autocomplete_close"  ></input>
                    </div>
                </div>
				
                <input type="button" name="btnSubmit" value="<?php if (basename($_SERVER['REQUEST_URI']) == "my-deals.php") echo "Go";
        else if (basename($_SERVER['REQUEST_URI']) == "mymerchants.php") echo "Go";
        else if (basename($_SERVER['REQUEST_URI']) == "search-deal.php") echo "Go"; ?>" onClick="search_deal(0);" class="mybtnclass">

                <?php
                // if($supported == 1)
                // {
                ?>   
                <button style="display:none" type="button" onclick="getLocation1()" class="mybtnclass" id="get_current_location" ><?php echo $language_msg["search"]["use_your_current_location"]; ?></button>
                <?php
                // }
                ?>

                <input type="hidden" value="<?php echo $language_msg["search"]["enter_your_zipcode_here"]; ?>" id="hdn_serach_value" name="hdn_search_value" >


                <input type="hidden" name="my_latitude" id="my_latitude" value="">
                <input type="hidden" name="my_longitude" id="my_longitude" value="">
                <input type="hidden" name="hdnmiles" id="hdnmiles" value="">

                <div class="div-rap" style="display:none">                         
                    <div class="filterclass"><a href="javascript:void(0)" rel="shareit" class="filterbutton">Filter</a></div>

                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '>
                                <a id="viewlink" href='javascript:void(0)' > View &nbsp;<span class="arov"> </span></a>
                            </li>

                            <div class="pop-display">    
                                <ul>
                                    <li class="mapview"><a id="mapview" href='javascript:void(0)'> Map <span  class="map-icon"> </span></a></li>							
                                    <li class="gridview"><a id="gridview" style="border-bottom:0px;" href='javascript:void(0)'> Grid<span class="grid-icon"></span></a></li>
                                </ul>
                            </div> 
                        </ul>	
                    </div>
                </div> <!-- end of div-rap -->
				<div class="set_location_delete_icon"></div>
            </div>  <!-- end of zipCodeDivSearch -->             
        </div><!--end of zipCodeDiv2-->

        <?php
}
// start to solve spped issue
$zoominglevel = "";
if (isset($_COOKIE['cat_remember'])) {
        $cat_coki = $_COOKIE['cat_remember'];
        if ($cat_coki == "") {
                $cat_coki = 0;
        }
}
if (isset($_COOKIE['searched_location'])) {
        $zip = $_COOKIE['searched_location'];
}
$mymiles = 50;
if (isset($_COOKIE['zoominglevel'])) {
        $zoominglevel = $_COOKIE['zoominglevel'];
}
if ($zoominglevel == "") {
        $zoominglevel = "10";
}
?>

<script type="text/javascript">

        $('[placeholder]').focus(function () {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
        }).blur(function () {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur().parents('form').submit(function () {
            $(this).find('[placeholder]').each(function () {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            })
        });
//alert("hi");

        function call_to_nearest_search(city)
        {
            document.getElementById("zip").value = city;
            search_deal();
        }
</script>
