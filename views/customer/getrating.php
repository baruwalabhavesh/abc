<?php
/* * ****** 
  @USE : get rating
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : myreviews.php
 * ******* */
//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/DB.php");
?>
<style>
    #reviews_ifr{width:100% !important;display: block !important; }
    .rating-main.common{margin:8px;}

    .rating-main input[type='submit']{background-position: -11px -6px;}
</style>
<link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
<link href="<?php echo ASSETS_CSS; ?>/c/fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css">
<script src="<?php echo ASSETS_JS; ?>/c/jquery-1.9.0.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo ASSETS; ?>/raty/jquery.raty.min.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/tinymce/tiny_mce.js"></script>
<script >
        var default_text = "What do you think about this place?";

        tinyMCE.init({
            // General options
            //mode : "textareas",
            mode: "exact",
            elements: "reviews",
            theme: "advanced",
            oninit: "postInitWork",
            plugins: "lists,searchreplace",
            valid_elements: 'p,br',
            // Theme options
            //theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            //theme_advanced_buttons1 : "replace,|,bullist,numlist,|,outdent,indent,blockquote,|,sub,sup,|,charmap",
            //theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            //theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            // Example content CSS (should be your site CSS)
            content_css: "<?php echo ASSETS; ?>/tinymce/content.css",
            // Drop lists for link/image/media/template dialogs
            template_external_list_url: "<?php echo ASSETS; ?>/tinymce/lists/template_list.js",
            external_link_list_url: "<?php echo ASSETS; ?>/tinymce/lists/link_list.js",
            external_image_list_url: "<?php echo ASSETS; ?>/tinymce/lists/image_list.js",
            media_external_list_url: "<?php echo ASSETS; ?>/tinymce/lists/media_list.js",
            theme_advanced_buttons1: "",
            // Style formats
            valid_elements :'p,br',
                    // Replace values for the template plugin
                    template_replace_values: {
                        username: "Some User",
                        staffid: "991234"
                    },
            charLimit: 1200,
            setup: function (ed) {
                //peform this action every time a key is pressed
                ed.onInit.add(function (ed) {
                    $(ed.getDoc()).contents().find('body').focus(function () {
                        //alert("="+(tinyMCE.activeEditor.getContent()).trim()+"=");
                        if ((tinyMCE.activeEditor.getContent()).trim() == "<p>What do you think about this place?</p>")
                        {
                            // tinyMCE.activeEditor.setContent("");
                            tinyMCE.get('reviews').setContent("");
                        }


                    });

                });


            }
        });
        //tinymce.get('#reviews').getDoc().designMode = 'Off';
        function postInitWork()
        {
            var editor = tinyMCE.getInstanceById('reviews');
            editor.getBody().setAttribute('contenteditable', 'false');
            editor.getBody().style.backgroundColor = "rgb(235, 235, 228)";

        }
        //$('#star1').rating('example.php', {maxvalue: 1});


        $(document).ready(function () {
            $('#rating').raty({half: true,
                hints: ['Poor', 'Fair', 'Good', 'Very good', 'Excellent'],
                click: function (score, evt) {
                    //alert('ID: ' + $(this).attr('id') + "\nscore: " + score + "\nevent: " + evt);
                    $("#rating_val").val(score);
                    $("#reviews").removeAttr("disabled");
                    var editor = tinyMCE.getInstanceById('reviews');
                    editor.getBody().setAttribute('contenteditable', 'true');
                    editor.getBody().style.backgroundColor = "white";
                    //  tinymce.get('reviews').getBody().setAttribute('contenteditable', 'true');
                    $("#btn_submit").removeAttr("disabled");
                    $(".div_msg").css("display", "none");
                }
            });
            jQuery("#reviews").attr("rows", (jQuery("#reviews").val().split("\n").length || 1));

            $("#btn_submit").click(function () {
                /* Check login */
                var editor = tinyMCE.getInstanceById('reviews');
                var content = editor.getContent();
                // alert(content);
                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    async: false,
                    data: 'btncheckloginornot_review=true&link=<?php echo WEB_PATH; ?>/myreviews.php',
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        if (obj.status == "false")
                        {
                            window.parent.location = obj.link;
                        }
                        else
                        {
                            a = (content.trim()).replace("&nbsp;", " ");
                            if (content.trim() == '<p>What do you think about this place?</p>')
                            {
                                review = "";
                            }
                            else
                            {
                                review = a;
                            }

                            if (review == "")
                            {
                                $(".div_msg").css("display", "block");
                                return false;
                            }

                            var succ = true;
                            $.ajax({
                                url: "<?php echo WEB_PATH; ?>/process.php?giveratings=yes&camp_id=" + $("#rated_campaign_id").val() + "&loc_id=" + $("#rated_location_id").val() + "&customer_id=<?php echo $_SESSION['customer_id'] ?>&reviews=" + encodeURIComponent(review) + "&ratings=" + $("#rating_val").val() + "&employee_id=" + $("#rated_employee_id").val(),
                                //   data:"rand="+Math.random(),
                                //cache: false,
                                async: false,
                                success: function (result) {

                                    var notify_review = parseInt($(".review_notification a", parent.document).text());

                                    notify_review = notify_review - 1;
                                    $(".review_notification a", parent.document).text(notify_review);
                                    succ = true;
                                    $("a[campid='" + $("#rated_campaign_id").val() + "'][locid='" + $("#rated_location_id").val() + "']", parent.document).attr("onclick", "return false");
                                    $("a[campid='" + $("#rated_campaign_id").val() + "'][locid='" + $("#rated_location_id").val() + "']", parent.document).removeClass("rateit2");
                                    var text_val = $("a[campid='" + $("#rated_campaign_id").val() + "'][locid='" + $("#rated_location_id").val() + "']", parent.document).text();
                                    var parent_ele = $("a[campid='" + $("#rated_campaign_id").val() + "'][locid='" + $("#rated_location_id").val() + "']", parent.document).parent();
                                    $("a[campid='" + $("#rated_campaign_id").val() + "'][locid='" + $("#rated_location_id").val() + "']", parent.document).detach();
                                    parent_ele.append('<span>' + text_val + '</span>');
                                    $(".div_msg").css("display", "none");


                                    window.top.window.$.fancybox.close();
                                    // parent.fancyBoxClose();

                                }
                            });


                        }
                    }
                });
                /* Check login */
                //var newtext = document.myform.reviews.value;

                //document.myform.outputtext.value += newtext;


            });
            $("#btn_cancel").click(function () {
                window.top.window.$.fancybox.close();
            });

            $("#rating>img").mouseenter(function () {
                var p = jQuery(this).position();

                /*
                 l = p.left -18;
                 t = p.top - 50;
                 */
                l = p.left - 18 + 50;
                t = p.top - 35 + 27;

                var msg = jQuery(this).attr("title");
                jQuery(".cust_attr_tooltip").css("left", l);
                jQuery(".cust_attr_tooltip").css("top", t);
                jQuery(".cust_attr_tooltip").css("display", "block");
                jQuery(".cust_attr_tooltip #star_tooltip").text(msg);
            }).mouseleave(function () {
                jQuery(".cust_attr_tooltip").css("display", "none");
            });

            //tinymce.get('#reviews').getBody().setAttribute('contenteditable', 'false');
        });
        function clickclear_textarea(thisfield, defaulttext)
        {

            /*
             if (thisfield.value == defaulttext) 
             {
             thisfield.value = "";
             }
             */
            // alert( tinyMCE.getContent('reviews'));
            var val = tinyMCE.getContent('reviews');
            if (val == defaulttext)
            {
                thisfield.value = "";
            }

        }
        function clickrecall_textarea(thisfield, defaulttext)
        {

            if (thisfield.value == "")
            {
                thisfield.value = defaulttext;
            }


        }


</script>
<!-- <div id="star1" class="rating" >&nbsp;</div> -->
<style>
    .defaultSkin td.mceToolbar {
        display: none;
    }
</style>
<div class="rating-main common" >

    <div style="display:none" id="msg_rating">
        Please give rating to review this location
    </div>
    <div class="div_msg" style="display:none">

        <div class="warning">

            <span class="plz_give_rating" class="div_msg_err">
                Please give raing to review this location
        </div>   
    </div>
    <br>    
    <div class="main_rating_box">
        <div class="rating-inner" >
            <input type="hidden" name="rating_val" id="rating_val" value="0" />
            <input type="hidden" name="rated_employee_id" id="rated_employee_id" value="<?php echo $_REQUEST['emplid']; ?>" />
            <input type="hidden" name="rated_campaign_id" id="rated_campaign_id" value="<?php echo $_REQUEST['camp_id']; ?>" />
            <input type="hidden" name="rated_location_id" id="rated_location_id" value="<?php echo $_REQUEST['loc_id']; ?>" />
            <div class="rating-div" >
                <div class="rating" id="rating" style="">
                </div>
                <div style="display:none;" class="cust_attr_tooltip">
                    <div class="arrow_down"></div>
                    <span id="star_tooltip">Offer Reserved</span>
                </div>
            </div>

            <textarea  onblur="clickrecall_textarea(this, 'What do you think about this place?')" onclick="clickclear_textarea(this, 'What do you think about this place?')" id="reviews" name="reviews"  onkeyup='this.rows = (this.value.split("\n").length || 1);' title="What do you think about this place?" >What do you think about this place?
            </textarea>

        </div>
    </div>
    <div align="right" >
        <input type="submit" onclick="return false" name="btn_submit" id="btn_submit"  value="Post" disabled />
        <input type="submit" onclick="return false" name="btn_cancel" id="btn_cancel" value="Cancel"  />
    </div>
</div>
