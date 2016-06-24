<?php
/**
 * @uses add new campaigns
 * @used in pages : add-user.php,apply_filter.php,apply_pointer.php,apply_pointfilter.php,compaigns.php,edit-user.php,process.php,footer.php,templates.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where_act['active']=1;
$RSCat = $objDB->Show("categories",$array_where_act);
$array =array();

  $arr_c=file(WEB_PATH.'/merchant/process.php?num_campaigns_per_month=yes&mer_id='.$_SESSION['merchant_id']);

    $json = json_decode($arr_c[0]);

            $total_campaigns= $json->total_records;
    $addcampaign_status = $json->status;
   
                   if($addcampaign_status=="false") 
                   {
                       header("Location:".WEB_PATH."/merchant/compaigns.php?action=active");
                   }
                   
                   
$array_where_user['id'] = $_SESSION['merchant_id'];
$RS_User = $objDB->Show("merchant_user", $array_where_user);
$m_parent = $RS_User->fields['merchant_parent'];
if($RS_User->fields['merchant_parent'] == 0)
{
    $array['created_by'] = $_SESSION['merchant_id'];
        $array['active'] = 1;
    $RSStore = $objDB->Show("locations", $array);
}
else
{
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
        
    $total_records= $json->total_records;
    $records_array = $json->records;
}


$array = array();
$array['merchant_id'] = $_SESSION['merchant_id'];
$RSGroups = $objDB->Show("merchant_groups",$array);



$pack_data1 = $json_array = array();
$pack_data = $json_array = array();
if($RS_User->fields['merchant_parent'] == 0)
{
    $pack_data['merchant_id'] = $_SESSION['merchant_id'];
    $get_pack_data = $objDB->Show("merchant_billing",$pack_data);
    $pack_data1['id'] = $get_pack_data->fields['pack_id'];
    $get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);
}
 else 
 {
     $arr=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
    if(trim($arr[0]) == "")
    {
        unset($arr[0]);
        $arr = array_values($arr);
    }
    $json = json_decode($arr[0]);
        
    $main_merchant_id= $json->main_merchant_id;
    
        
    $pack_data['merchant_id'] = $main_merchant_id;
    $get_pack_data = $objDB->Show("merchant_billing",$pack_data);
    $pack_data1['id'] = $get_pack_data->fields['pack_id'];
    $get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);    
 }


/* T_10 */
if(isset($_REQUEST['template_id'])){
    $array_where['id'] = $_REQUEST['template_id'];
$RS_template = $objDB->Show("campaigns_template", $array_where);
}
/* T_10 */

$timezoness =array(
    'Pacific/Wake' => '(GMT-12:00) International Date Line West',
    'Pacific/Apia' => '(GMT-11:00) Samoa',
    'US/Hawaii' => '(GMT-10:00) Hawaii',
    'America/Anchorage' => '(GMT-09:00) Alaska',
    'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada); Tijuana',
    'America/Phoenix' => '(GMT-07:00) Arizona',
    'America/Chihuahua' => '(GMT-07:00) Mazatlan',
    'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)',
    'America/Managua' => '(GMT-06:00) Central America',
    'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)',
    'America/Mexico_City' => '(GMT-06:00) Monterrey',
    'America/Regina' => '(GMT-06:00) Saskatchewan',
    'America/Bogota' => '(GMT-05:00) Quito',
    'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)',
    'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
    'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)',
    'America/Caracas' => '(GMT-04:00) La Paz',
    'America/Santiago' => '(GMT-04:00) Santiago',
    'America/St_Johns' => '(GMT-03:30) Newfoundland',
    'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
    'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
    'America/Godthab' => '(GMT-03:00) Greenland',
    'America/Noronha' => '(GMT-02:00) Mid-Atlantic',
    'Atlantic/Azores' => '(GMT-01:00) Azores',
    'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
    'Africa/Casablanca' => '(GMT) Monrovia',
    'Europe/London' => '(GMT) London',
    'Europe/Berlin' => '(GMT+01:00) Vienna',
    'Europe/Belgrade' => '(GMT+01:00) Prague',
    'Europe/Paris' => '(GMT+01:00) Paris',
    'Europe/Sarajevo' => '(GMT+01:00) Zagreb',
    'Africa/Lagos' => '(GMT+01:00) West Central Africa',
    'Europe/Istanbul' => '(GMT+02:00) Minsk',
    'Europe/Bucharest' => '(GMT+02:00) Bucharest',
    'Africa/Cairo' => '(GMT+02:00) Cairo',
    'Africa/Johannesburg' => '(GMT+02:00) Pretoria',
    'Europe/Helsinki' => '(GMT+02:00) Vilnius',
    'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
    'Asia/Baghdad' => '(GMT+03:00) Baghdad',
    'Asia/Riyadh' => '(GMT+03:00) Riyadh',
    'Europe/Moscow' => '(GMT+03:00) Volgograd',
    'Africa/Nairobi' => '(GMT+03:00) Nairobi',
    'Asia/Tehran' => '(GMT+03:30) Tehran',
    'Asia/Muscat' => '(GMT+04:00) Muscat',
    'Asia/Tbilisi' => '(GMT+04:00) Yerevan',
    'Asia/Kabul' => '(GMT+04:30) Kabul',
    'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
    'Asia/Karachi' => '(GMT+05:00) Tashkent',
    'Asia/Calcutta' => '(GMT+05:30) New Delhi',
    'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
    'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
    'Asia/Dhaka' => '(GMT+06:00) Dhaka',
    'Asia/Colombo' => '(GMT+06:00) Sri Jayawardenepura',
    'Asia/Rangoon' => '(GMT+06:30) Rangoon',
    'Asia/Bangkok' => '(GMT+07:00) Jakarta',
    'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
    'Asia/Hong_Kong' => '(GMT+08:00) Urumqi',
    'Asia/Irkutsk' => '(GMT+08:00) Ulaan Bataar',
    'Asia/Singapore' => '(GMT+08:00) Singapore',
    'Australia/Perth' => '(GMT+08:00) Perth',
    'Asia/Taipei' => '(GMT+08:00) Taipei',
    'Asia/Tokyo' => '(GMT+09:00) Tokyo',
    'Asia/Seoul' => '(GMT+09:00) Seoul',
    'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
    'Australia/Adelaide' => '(GMT+09:30) Adelaide',
    'Australia/Darwin' => '(GMT+09:30) Darwin',
    'Australia/Brisbane' => '(GMT+10:00) Brisbane',
    'Australia/Sydney' => '(GMT+10:00) Sydney',
    'Pacific/Guam' => '(GMT+10:00) Port Moresby',
    'Australia/Hobart' => '(GMT+10:00) Hobart',
    'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
    'Asia/Magadan' => '(GMT+11:00) Solomon Is.',
    'Pacific/Auckland' => '(GMT+12:00) Wellington',
    'Pacific/Fiji' => '(GMT+12:00) Marshall Is.',
    'Pacific/Tongatapu' => '(GMT+13:00) Nuku\'alofa',
);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Manage Campaigns</title>
<?php //require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/datepicker.css">

<!-- T_7 -->
<script language="javascript" src="<?=ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
<!-- T_7 -->
<!--// 369-->
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery.form.js"></script>
<!--// 369-->

<!--<script type="text/javascript" src="<?=ASSETS ?>/tinymce/tiny_mce.js"></script>-->
<script src="<?= ASSETS ?>/tinymce4/tinymce.min.js"></script>

<!-- on/off switch -->
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap-switch.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap-switch.css">
<!-- on/off switch -->

<script language="javascript">

                
/*$(function()
{       
    $('#start_date').datepick({dateFormat: 'mm-dd-yyyy'});
    $('#expiration_date').datepick({dateFormat: 'mm-dd-yyyy'});
});*/
jQuery(document).ready(function(){
    window.tinymce.dom.Event.domLoaded = true;
    /*
    tinyMCE.init({
        // General options
        //mode : "textareas",
                mode : "exact",
                 width: '100%',
        elements:"description,terms_condition",
        theme : "advanced",
        plugins : "lists,searchreplace",
        valid_elements :'p,br,ul,ol,li,sub,sup',
        // Theme options
        //theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons1 : "replace,|,bullist,numlist,|,sub,sup,|,charmap",
        //theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        //theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
        //theme_advanced_toolbar_location : "top",
        //theme_advanced_toolbar_align : "left",
        //theme_advanced_statusbar_location : "bottom",
        //theme_advanced_resizing : true,

        // Example content CSS (should be your site CSS)
        content_css : "<?=ASSETS ?>/tinymce/content.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "<?=ASSETS ?>/tinymce/lists/template_list.js",
        external_link_list_url : "<?=ASSETS ?>/tinymce/lists/link_list.js",
        external_image_list_url : "<?=ASSETS ?>/tinymce/lists/image_list.js",
        media_external_list_url : "<?=ASSETS ?>/tinymce/lists/media_list.js",

        // Style formats
        style_formats : [
            {title : 'Bold text', inline : 'b'},
            {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
            {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
            {title : 'Example 1', inline : 'span', classes : 'example1'},
            {title : 'Example 2', inline : 'span', classes : 'example2'},
            {title : 'Table styles'},
            {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
        ],

        // Replace values for the template plugin
        template_replace_values : {
            username : "Some User",
            staffid : "991234"
        },
                charLimit:1200,
        setup : function(ed) {
        //peform this action every time a key is pressed
        ed.onKeyDown.add(function(ed, e) {
            var textarea = tinyMCE.activeEditor.getContent(); 
            //alert(textarea);
            var lastcontent=textarea;
            //define local variables
            var tinymax, tinylen, htmlcount;
            //manually setting our max character limit
            tinymax = ed.settings.charLimit;
            //grabbing the length of the curent editors content
            tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
            //setting up the text string that will display in the path area
            //htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
            //if the user has exceeded the max turn the path bar red.
            //alert(tinylen);
            if (tinylen+1>tinymax && e.keyCode != 8){
                e.preventDefault();
                e.stopPropagation();
                return false;
            }       
        });
        ed.onKeyUp.add(function(ed, e) {
            //alert("up");
            var textarea = tinyMCE.activeEditor.getContent(); 
            //alert(textarea);
            var lastcontent=textarea;
            //define local variables
            var tinymax, tinylen, htmlcount;
            //manually setting our max character limit
            tinymax = ed.settings.charLimit;
            //grabbing the length of the curent editors content
            tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
            //setting up the text string that will display in the path area
            //htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
            
            var l=tinymax-tinylen;
                        
                        if(tinyMCE.activeEditor.id=="description")
                            document.getElementById("desc_limit").innerHTML=l+" characters remaining | no HTML allowed";
                        if(tinyMCE.activeEditor.id=="terms_condition")
                            document.getElementById("terms_limit").innerHTML=l+" characters remaining | no HTML allowed";
        });
        }
    });
    */
    validHTMLTags  =/^(?:a|abbr|acronym|address|applet|area|article|aside|audio|b|base|basefont|bdi|bdo|bgsound|big|blink|blockquote|body|br|button|canvas|caption|center|cite|code|col|colgroup|data|datalist|dd|del|details|dfn|dir|div|dl|dt|em|embed|fieldset|figcaption|figure|font|footer|form|frame|frameset|h1|h2|h3|h4|h5|h6|head|header|hgroup|hr|html|i|iframe|img|input|ins|isindex|kbd|keygen|label|legend|li|link|listing|main|map|mark|marquee|menu|menuitem|meta|meter|nav|nobr|noframes|noscript|object|ol|optgroup|option|output|p|param|plaintext|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|source|spacer|span|strike|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|track|tt|u|ul|var|video|wbr|xmp)$/i;

function sanitize(txt) {
    var // This regex normalises anything between quotes
        normaliseQuotes = /=(["'])(?=[^\1]*[<>])[^\1]*\1/g,
        normaliseFn = function ($0, q, sym) { 
            return $0.replace(/</g, '&lt;').replace(/>/g, '&gt;'); 
        },
        replaceInvalid = function ($0, tag, off, txt) {
            var invalidTag = 
                    document.createElement(tag) instanceof HTMLUnknownElement
                 || !validHTMLTags.test(tag),
                // Is the tag complete?
                isComplete = txt.slice(off+1).search(/^[^<]+>/) > -1;  
            return invalidTag || !isComplete ? '&lt;' + tag : $0;
        };
    txt = txt.replace(normaliseQuotes, normaliseFn).replace(/<(\w+)/g, replaceInvalid);
    var tmp = document.createElement("DIV");
    tmp.innerHTML = txt;
    return "textContent" in tmp ? tmp.textContent : tmp.innerHTML;
}
    tinymce.init({   
                              selector: "#description,#terms_condition",
                               // plugins:"charwordcount",
                                 plugins: [
                                 "charwordcount advlist autolink lists link charmap searchreplace hr"
                                ],
                                toolbar1: "searchreplace | bullist numlist | subscript superscript | charmap | hr ",
                                menubar: false,
                                toolbar_items_size: 'small',
                                charLimit: 1200,
                                setup: function (editor) {
                                    function f() {
                                        dis_len = c.getCountCharacters();
                                        //editor.theme.panel.find("#wordcount").text(["Words: {0}, Characters: {1}", c.getCount(), c.getCountCharacters()])
                                        //document.getElementById("desc_limit").innerHTML = ["Words: {0}, Characters: {1}", c.getCount(), c.getCountCharacters()] ;
                                    }
                                    var c = this,
                                        d, e;
                                    d = editor.getParam("wordcount_countregex", /[\w\u2019\x27\-]+/g);
                                    e = editor.getParam("wordcount_cleanregex", /[0-9.(),;:!?%#$?\x27\x22_+=\\\/\-]*/g);
                                    editor.on('init', function() {
                                      var a = editor.theme.panel && editor.theme.panel.find("#statusbar")[0];  
                                      a && window.setTimeout(function() {
                                        a.insert({
                                            type: "label",
                                            name: "wordcount",
                                            classes: "wordcount"
                                        }, 0);
                                        editor.on("setcontent beforeaddundo keyup", f)
                                    }, 0)
                                    });
                                        c.getCount = function() {
                                            var a = editor.getContent({
                                                    format: "raw"
                                                }),
                                                c = 0;
                                            a && (a = a.replace(/\.\.\./g, " "), a = a.replace(/<.[^<>]*?>/g, " ").replace(/&nbsp;|&#160;/gi, " "), a = a.replace(/(\w+)(&.+?;)+(\w+)/, "$1$3").replace(/&.+?;/g, " "), a = a.replace(e, ""), a = a.match(d)) && (c = a.length);
                                            return c
                                        };
                                        c.getCountCharacters = function() {
                                            return !0 == editor.settings.charwordcount_include_tags ? editor.getContent({
                                                format: "raw"
                                            }).length : editor.getContent({
                                                format: "raw"
                                            }).replace(/<.[^<>]*?>/g,
                                                "").replace(/&[^;]+;/g, "?").length
                                        }
                                        editor.on('keydown', function (e) {
                                        var textarea = tinyMCE.activeEditor.getContent();
                                        textarea = sanitize(textarea); 
                                       // var textarea = tinymce.get('description').getContent();
                                        //var textarea = tinymce.activeEditor.getContent({format: 'raw'});
                                        var lastcontent = textarea;
                                        //define local variables
                                        var tinymax, tinylen, htmlcount;
                                        //manually setting our max character limit
                                        tinymax = editor.settings.charLimit;
                                        //grabbing the length of the curent editors content
                                        //tinylen = c.getCountCharacters();
                                        tinylen = textarea.length;
                                        if (tinylen + 1 > tinymax && e.keyCode != 8) {
                                           var l = tinymax - tinylen;
                                                if(l<0)
                                                {
                                                    tinyMCE.activeEditor.setContent(textarea.slice(0, l));
                                                }
                                            e.preventDefault();
                                            e.stopPropagation();
                                            e.stopImmediatePropagation();
                                            //alert("1");
                                            return false;
                                        }
                                    });
                                        editor.on('keyup', function (e) {
                                        //alert("up");
                                        var textarea = tinyMCE.activeEditor.getContent();
                                         textarea = sanitize(textarea);
                                        var lastcontent = textarea;
                                        //define local variables
                                        var tinymax, tinylen, htmlcount;
                                        //manually setting our max character limit
                                        tinymax = editor.settings.charLimit;
                                        //grabbing the length of the curent editors content
                                        //tinylen = editor.getContent().replace(/(<([^>]+)>)/ig, "").length;
                                        //tinylen = c.getCountCharacters();
                                        //setting up the text string that will display in the path area
                                        //htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
                                         tinylen = textarea.length;
                                        var l = tinymax - tinylen;
                                        if (tinyMCE.activeEditor.id == "description")
                                            document.getElementById("desc_limit").innerHTML = l + " characters remaining";
                                         if (tinyMCE.activeEditor.id == "terms_condition")
                                            document.getElementById("terms_limit").innerHTML = l + " characters remaining";    
                                    });
                                    editor.on('change', function(e) {
                                        var textarea = tinyMCE.activeEditor.getContent();
                                        textarea = sanitize(textarea);
                                        var tinymax = editor.settings.charLimit;
                                       // var tinylen = c.getCountCharacters();
                                        var tinylen = textarea.length;
                                        var l = tinymax - tinylen;
                                          if (tinyMCE.activeEditor.id == "description")
                                            document.getElementById("desc_limit").innerHTML = l + " characters remaining";
                                           if (tinyMCE.activeEditor.id == "terms_condition")
                                            document.getElementById("terms_limit").innerHTML = l + " characters remaining";  
                                          if (tinylen + 1 > tinymax){
                                            var l = tinymax - tinylen;
                                                if(l<0)
                                                {
                                                    tinyMCE.activeEditor.setContent(textarea.slice(0,l));
                                                }
                                            return false;//console.log("less");
                                            }
                                        });
                                    editor.on('Paste',function(e) {
                                         var textarea = tinyMCE.activeEditor.getContent();
                                         var  tinymax = editor.settings.charLimit;
                                         textarea = sanitize(textarea); 
                                         //var tinylen = c.getCountCharacters();
                                        var tinylen = textarea.length;
                                        //grabbing the length of the curent editors content
                                        if (tinylen + 1 > tinymax){
                                            var l = tinymax - tinylen;
                                                if(l <= 0)
                                                {
                                                    tinyMCE.activeEditor.setContent(textarea.slice(0,l));
                                                }
                                            return false;//console.log("less");
                                            }
                                      });
                                }
                            });
                            
    jQuery(".textarea_loader").css("display","none");
jQuery(".textarea_container").css("display","block");
    });
      //  alert(jQuery("#description_parent").length);
 //jQuery("#description_parent").css("float",'left');
</script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap.css" />
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
<link href="<?=ASSETS_CSS ?>/m/bootstrap_c.min.css" rel="stylesheet" type="text/css">
</head>

<body>
     <div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div >
<!--start header--><div>

        <?
        require_once(MRCH_LAYOUT."/header.php");
        ?>
        <!--end header--></div>
    <div id="contentContainer">
        <div id="content">  
            
        
    <!--// 369-->
    <form action="process.php" method="post" enctype="multipart/form-data" id="add_campaign_form">
            
            <header>
            <div class="row">
                <div class="col-md-1 col-sm-1 col-xs-2">
                    <img src="<?=ASSETS_IMG ?>/m/campaign_site_large.png" class="campaign-img" alt="campaign_site_large" />
                </div>
                <div class="col-md-8 col-sm-8 col-xs-6">
                    <h2><?php  echo  $merchant_msg["edit-compaign"]["Field_add_campaign"]; ?></h2>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-4 text-right">
                    <a href="javascript:void(0)" class="btn gray-btn btnCancelCampaign">Cancel</a>
                </div>
            </div>
            <div class="clearfix"></div>
        </header>
        
            <!-- Campaign-edit Tab CSS  -->
        <section class="campaign-edit-wrapper">
            <div class="row">
                <div class="col-sm-12 campaign-tab-container">
                    <div class="col-md-2 col-sm-3">
                      <ul class="nav nav-tabs list-group">
                        <li tab="SelectTemplate" class="active">    
                            <a href="#a" data-toggle="tab">Select Template</a>
                        </li>
                        <li tab="CampaignBasics" >
                            <a href="#b" data-toggle="tab">Campaign Basics</a>
                        </li>
                        <li tab="CampaignImages" >
                            <a href="#c" data-toggle="tab">Campaign Images</a>
                        </li>
                        <li tab="CampaignAudience" >
                            <a href="#d" data-toggle="tab">Campaign Audience</a>
                        </li>
                        <li tab="CampaignLocations" >
                            <a href="#e" data-toggle="tab">Campaign Locations</a>
                        </li>
                        <li tab="CampaignPolicies" >
                            <a href="#f" data-toggle="tab"> Campaign Policies</a>
                        </li>
                        <li tab="Summary" >
                            <a href="#g" data-toggle="tab">Summary</a>
                        </li>
                      </ul>
                    </div>
                    <div class="col-md-10 col-sm-9">
                        <div class="row">
                            <div class="tab-content">
                                
                                <div class="tab-pane active" id="a">
                                    <div class="select-template">
                                        <div class="col-md-8 col-sm-6 no-padding">
                                            <label class="col-md-2 col-sm-4 no-padding" for="template-category">Filter By :</label>
                                            <div class="col-md-10 col-sm-8">
                                                <select id="template_category" name="template-category" class="form-control category">
                                                    <option value="0" selected="selected">All (Default)</option>
                                                    <?php
                                                    $array_where_act['active']=1;
                                                    $RS_template_cat = $objDB->Show("categories",$array_where_act);
                                                    while($Row = $RS_template_cat->FetchRow()){
                                                    ?>
                                                        <option value="<?=$Row['id']?>"><?=$Row['cat_name']?></option>
                                                    <?
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-3 text-right">
                                            <a id="btnNextTemplate" href="javascript:void(0)" class="btn gray-btn">Next</a>
                                        </div>
                                        <div class="col-md-2 col-sm-3 text-right no-padding">
                                            <a id="btnSkipTemplate" href="javascript:void(0)" class="btn gray-btn">Skip this Step</a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="row v-t-margin template-image-wrapper"> 
                                        <?php
                                        //echo WEB_PATH.'/merchant/process.php?btnGetAllTemplateOfMerchant=yes&mer_id='.$_SESSION['merchant_id'];
                                        $arr_t=file(WEB_PATH.'/merchant/process.php?btnGetAllTemplateOfMerchant=yes&mer_id='.$_SESSION['merchant_id']);
                                        if(trim($arr_t[0]) == "")
                                        {
                                                unset($arr_t[0]);
                                                $arr_t = array_values($arr_t);
                                        }
                                        $json_t = json_decode($arr_t[0]);
                                        $total_records_t= $json_t->total_records;
                                        $records_array_t = $json_t->records;
                                        
                                        $start_index = 0;
                                        $num_of_records = 8;
                                        $next_index = $start_index + $num_of_records;

                                        $total_templates_records = $json_t->total_template_records;
          
                                        if($total_records_t > 0)
                                        {
                                            foreach($records_array_t as $Row)
                                            {
                                        ?>                                  
                                            <div class="col-md-3 col-sm-3" category_id="<?php echo $Row->category_id ?>">
                                                <div class="template-image">
                                                    <img src="<?=ASSETS_IMG ?>/m/campaign/<?=$Row->business_logo?>" alt="tempalate-image" />
                                                </div>
                                            </div>
                                        <?php
                                            }
                                        }
                                        else
                                        {
                                            echo "<br /><p>No pre-define template found</p>";
                                        }
                                        ?>
                                        <!--<div class="clearfix"></div>-->
                                    </div>
                                    <?php
                                    /*
                                    echo "total_records_t = ".$total_records_t;
                                    echo "total_templates_records = ".$total_templates_records;
                                    echo "num_of_records = ".$num_of_records;
                                    */
                                    if($total_records_t>0 && $total_templates_records>$num_of_records)
                                    {
                                        
                                    ?>  
                                        <div class="v-t-margin col-md-12 col-sm-12 text-center">
                                            <a href="javascript:void(0)" id="show_more_templates" class="btn gray-btn show-more-btn" next_index="<?php echo $next_index ?>" num_of_records="<?php echo $num_of_records ?>" total_templates_records="<?php echo $total_templates_records ?>" >Show More</a>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="clearfix"></div>    
                                </div>

                                <div class="tab-pane" id="b">
                                    <div class="campaign-basics">
                                        <div class="col-sm-6 no-padding">
                                            <p class="heading">Basic</p>
                                        </div>
                                        <div class="col-sm-6 text-right no-padding">
                                            <p>Publish Status : <input type="checkbox" id="chkOnOff" name="chkOnOff" ></p>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form">
                                            <div class="row v-b-margin">
                                                <label for="campaign-title" class="col-sm-4"><?php  echo  $merchant_msg["edit-compaign"]["Field_campaign_title"]; ?></label>
                                                <div class="col-sm-8">
                                                    <input type="text" placeholder="Required" name="title" id="title" maxlength="76" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="campaign-keywords" class="col-sm-4"><?php  echo  $merchant_msg["edit-compaign"]["Field_campaign_tag"]; ?></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" placeholder="Enter upto 3 keywords separated by commas " name="campaign_tag" id="campaign_tag">
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="campaign-original-value" class="col-sm-4"><?php  echo  $merchant_msg["edit-compaign"]["Field_deal_value"]; ?></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="deal_value" id="deal_value" placeholder="00">
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="campaign-discount" class="col-sm-4"><?php  echo  $merchant_msg["edit-compaign"]["Field_discount_rate"]; ?></label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="discount" id="discount" value="" placeholder="00"> &nbsp;
                                                    
                                                </div>
                                                <div class="col-sm-4">
                                                    <select name="is_per" id="is_per" class="valid form-control">
                                                        <option value="per">% OFF</option>
                                                        <option value="curr">$ OFF</option>
                                                    </select>
                                                </div>
                                            </div>
                                          
                                            <div class="row v-b-margin">
                                                <label for="campaign-category" class="col-md-4 col-sm-4"><?php  echo  $merchant_msg["edit-compaign"]["Field_category"]; ?></label>
                                                <div class="col-md-4 col-sm-8">
                                                   
                                                    <select name="category_id" id="category_id" class="form-control">
                                                        <option value="0">Select Category</option>
                                                        <?
                                                        while($Row = $RSCat->FetchRow()){
                                                        ?>
                                                            <option value="<?=$Row['id']?>"  <? if(isset($_REQUEST['template_id'])){ if($RS_template->fields['category_id'] == $Row['id']) echo "selected"; }?>><?=$Row['cat_name']?></option>
                                                        <?
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="campaign-category" class="col-md-4 col-sm-4"><?php  echo  $merchant_msg["edit-compaign"]["Field_timezone"]; ?></label>
                                                <div class="col-md-4 col-sm-8">
                                                <select name='timezone' id='timezone' class='timezone_box form-control'>
                                                      <?php
                                                      foreach($timezoness as $key=>$value){
                                                          ?>
                                                      <option value='<?php echo $key ?>'  ><?php echo $value; ?></option>
                                                      <?php
                                                      }
                                                      ?>
                                                </select>
                                                 <!-- 
                                                <select name='timezone' id='timezone' class="form-control" >
                                                    <option value='-12:00,0'>(-12:00) International Date Line West</option>
                                                    <option value='-11:00,0'>(-11:00) Midway Island, Samoa</option>
                                                    <option value='-10:00,0'>(-10:00) Hawaii</option>
                                                    <option value='-09:00,1'>(-09:00) Alaska</option>
                                                    <option value='-08:00,1'>(-08:00) Pacific Time (US & Canada)</option>
                                                    <option value='-07:00,0'>(-07:00) Arizona</option>
                                                    <option value='-07:00,1'>(-07:00) Mountain Time (US & Canada)</option>
                                                    <option value='-06:00,0'>(-06:00) Central America, Saskatchewan</option>
                                                    <option value='-06:00,1'>(-06:00) Central Time (US & Canada), Guadalajara, Mexico city</option>
                                                    <option value='-05:00,0'>(-05:00) Indiana, Bogota, Lima, Quito, Rio Branco</option>
                                                    <option value='-05:00,1'>(-05:00) Eastern time (US & Canada)</option>
                                                    <option value='-04:00,1'>(-04:00) Atlantic time (Canada), Manaus, Santiago</option>
                                                    <option value='-04:00,0'>(-04:00) Caracas, La Paz</option>
                                                    <option value='-03:30,1'>(-03:30) Newfoundland</option>
                                                    <option value='-03:00,1'>(-03:00) Greenland, Brasilia, Montevideo</option>
                                                    <option value='-03:00,0'>(-03:00) Buenos Aires, Georgetown</option>
                                                    <option value='-02:00,1'>(-02:00) Mid-Atlantic</option>
                                                    <option value='-01:00,1'>(-01:00) Azores</option>
                                                    <option value='-01:00,0'>(-01:00) Cape Verde Is.</option>
                                                    <option value='00:00,0'>(00:00) Casablanca, Monrovia, Reykjavik</option>
                                                    <option value='00:00,1'>(00:00) GMT: Dublin, Edinburgh, Lisbon, London</option>
                                                    <option value='+01:00,1'>(+01:00) Amsterdam, Berlin, Rome, Vienna, Prague, Brussels</option>
                                                    <option value='+01:00,0'>(+01:00) West Central Africa</option>
                                                    <option value='+02:00,1'>(+02:00) Amman, Athens, Istanbul, Beirut, Cairo, Jerusalem</option>
                                                    <option value='+02:00,0'>(+02:00) Harare, Pretoria</option>
                                                    <option value='+03:00,1'>(+03:00) Baghdad, Moscow, St. Petersburg, Volgograd</option>
                                                    <option value='+03:00,0'>(+03:00) Kuwait, Riyadh, Nairobi, Tbilisi</option>
                                                    <option value='+03:30,0'>(+03:30) Tehran</option>
                                                    <option value='+04:00,0'>(+04:00) Abu Dhadi, Muscat</option>
                                                    <option value='+04:00,1'>(+04:00) Baku, Yerevan</option>
                                                    <option value='+04:30,0'>(+04:30) Kabul</option>
                                                    <option value='+05:00,1'>(+05:00) Ekaterinburg</option>
                                                    <option value='+05:00,0'>(+05:00) Islamabad, Karachi, Tashkent</option>
                                                    <option value='+05:30,0'>(+05:30) Chennai, Kolkata, Mumbai, New Delhi, Sri Jayawardenepura</option>
                                                    <option value='+05:45,0'>(+05:45) Kathmandu</option>
                                                    <option value='+06:00,0'>(+06:00) Astana, Dhaka</option>
                                                    <option value='+06:00,1'>(+06:00) Almaty, Nonosibirsk</option>
                                                    <option value='+06:30,0'>(+06:30) Yangon (Rangoon)</option>
                                                    <option value='+07:00,1'>(+07:00) Krasnoyarsk</option>
                                                    <option value='+07:00,0'>(+07:00) Bangkok, Hanoi, Jakarta</option>
                                                    <option value='+08:00,0'>(+08:00) Beijing, Hong Kong, Singapore, Taipei</option>
                                                    <option value='+08:00,1'>(+08:00) Irkutsk, Ulaan Bataar, Perth</option>
                                                    <option value='+09:00,1'>(+09:00) Yakutsk</option>
                                                    <option value='+09:00,0'>(+09:00) Seoul, Osaka, Sapporo, Tokyo</option>
                                                    <option value='+09:30,0'>(+09:30) Darwin</option>
                                                    <option value='+09:30,1'>(+09:30) Adelaide</option>
                                                    <option value='+10:00,0'>(+10:00) Brisbane, Guam, Port Moresby</option>
                                                    <option value='+10:00,1'>(+10:00) Canberra, Melbourne, Sydney, Hobart, Vladivostok</option>
                                                    <option value='+11:00,0'>(+11:00) Magadan, Solomon Is., New Caledonia</option>
                                                    <option value='+12:00,1'>(+12:00) Auckland, Wellington</option>
                                                    <option value='+12:00,0'>(+12:00) Fiji, Kamchatka, Marshall Is.</option>
                                                    <option value='+13:00,0'>(+13:00) Nuku'alofa</option>
                                                </select>
                                                -->
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="date-range" class="col-sm-4"><?php  echo  $merchant_msg["edit-compaign"]["Field_date"]; ?></label>
                                                <div class="col-sm-4 campaign-validity">
                                                    <div class="col-sm-12 no-padding">
                                                        <?php  echo  $merchant_msg["edit-compaign"]["Field_start_date"]; ?>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 no-padding">
                                                        <input readonly type="text" name="start_date" class="form-control datetimepicker_sd" id="start_date" placeholder="Required" value="">
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 h-r-padding no-right-padding">
                                                        <input id="defaultValueFrom" name="from" type="text" class=" time form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 campaign-validity">
                                                    <div class="col-sm-12 no-padding">
                                                        <?php  echo  $merchant_msg["edit-compaign"]["Field_end_date"]; ?>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 no-padding">
                                                        <input readonly type="text" name="expiration_date" class="form-control datetimepicker_ed" id="expiration_date" placeholder="Required" value="">
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 h-r-padding no-right-padding">
                                                        <input id="defaultValueTo" name="to" type="text" class="time form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                             <script>
                                              jQuery(function() {
                                                jQuery('#defaultValueFrom').timepicker({ 'scrollDefaultNow': true });
                                                jQuery('#defaultValueTo').timepicker({ 'scrollDefaultNow': true });
                                              });
                                            </script>
                                            <div class="row v-b-margin">
                                                <label for="day-parting" class="col-md-4 col-sm-4">Day Parting :</label>
                                                <div class="col-md-2 col-sm-3 form-radio">
                                                    <input type="radio" name="day-parting" id="day-parting-1" value="0" checked>All Day
                                                    <label for="day-parting-1"><span></span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-5 form-radio">
                                                    <input type="radio" name="day-parting" id="day-parting-2" value="1">Specific Hours
                                                    <label for="day-parting-2"><span></span></label>
                                                </div>
                                                
                                                <div id="specific-hours-div" style="display:none;">
                                                    
                                                <div class="col-md-offset-4 col-md-9 col-sm-12">
                                                    <div class="new-rule-btn">
                                                        <a id="btnAddNewRule" href="javascript:void(0);" class="btn blue-btn"><span class="plus-icon"></span>New Rule</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12">&nbsp;</div>
                                                <div class="specific-hours-table col-md-8 col-sm-12 table-responsive">
                                                    <table class="table-bordered" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="8%">&nbsp;</th>
                                                                <th width="18%" class="left">From</th>
                                                                <th width="18%" class="left">To</th>
                                                                <th width="8%">Mon</th>
                                                                <th width="8%">Tue</th>
                                                                <th width="8%">Wed</th>
                                                                <th width="8%">Thu</th>
                                                                <th width="8%">Fri</th>
                                                                <th width="8%">Sat</th>
                                                                <th width="8%">Sun</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <div class="child-table rulebodydiv">
                                                        <table class="table-bordered rulebody" width="100%">
                                                            <tbody>
                                                                
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <div class="col-md-4 col-sm-12">
                                                    <label class="v-b-margin" for=""><?php  echo  $merchant_msg["edit-compaign"]["Field_deal_description"]; ?></label><br/>
                                                    
                                                </div>
                                                <div class="col-md-8 col-sm-12">
                                                    <textarea id="description" style="height:200px;" name="description" rows="30" cols="80" ></textarea>
                                                    <span id="desc_limit">Maximum 1200 characters | no HTML allowed</span>
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <div class="col-md-4 col-sm-12">
                                                    
                                                    <label for=""><?php  echo  $merchant_msg["edit-compaign"]["Field_terms_condition"]; ?> </label>
                                                </div>
                                                <div class="col-md-8 col-sm-12">
                                                    <textarea id="terms_condition"  style="height:200px;" name="terms_condition" rows="30" cols="80" class="table_th_90"></textarea>
                                                    <span id="terms_limit">Maximum 1200 characters | no HTML allowed</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <a id="btnNextBasic" href="javascript:void(0)" class="btn btn-block gray-btn">Next Step</a>
                                            </div>
                                            <div class="col-sm-6"></div>
                                            <div class="col-sm-3">
                                                <a href="javascript:void(0)" class="btn btn-block gray-btn btnCancelCampaign">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="c">
                                    <div class="campaign-images">
                                        <div class="col-sm-12 no-padding">
                                            <p class="heading">Campaign Images</p>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form">
                                            <div class="row v-b-margin">
                                                <label for="campaign-image" class="col-md-3 col-sm-5">Campaign Images :</label>
                                                <div class="col-md-9 col-sm-7">
                                                    Select from <a href="javascript:void(0)" class="blue-text mediaclass"> Media Library</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <span id="status" ></span>
                                        <br/>
                                        <div class="row v-margin campaign-image-wrapper">   
                                            
                                            <!--                
                                            <div class="col-5">
                                                <div class="template-image">
                                                    <img id="campaign_img1" set="no" src="<?=ASSETS_IMG ?>/m/campaign-thumb-image.png" alt="campaign-thumb-image" />
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="template-image">
                                                    <img id="campaign_img2" set="no" src="<?=ASSETS_IMG ?>/m/campaign-thumb-image.png" alt="campaign-thumb-image" />
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="template-image">
                                                    <img id="campaign_img3" set="no" src="<?=ASSETS_IMG ?>/m/campaign-thumb-image.png" alt="campaign-thumb-image" />
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="template-image">
                                                    <img id="campaign_img4" set="no" src="<?=ASSETS_IMG ?>/m/campaign-thumb-image.png" alt="campaign-thumb-image" />
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="template-image">
                                                    <img id="campaign_img5" set="no" src="<?=ASSETS_IMG ?>/m/campaign-thumb-image.png" alt="campaign-thumb-image" />
                                                </div>
                                            </div>
                                            -->
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <a id="btnNextImages" href="javascript:void(0)" class="btn btn-block gray-btn">Next Step</a>
                                            </div>
                                            <div class="col-sm-6"></div>
                                            <div class="col-sm-3">
                                                <a href="javascript:void(0)" class="btn btn-block gray-btn btnCancelCampaign">Cancel</a>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="d">
                                    <div class="campaign-audience">
                                        <div class="col-sm-12 no-padding">
                                            <p class="heading">Campaign Audience :</p>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form">
                                            <div class="row v-b-margin">
                                                <label for="audience" class="col-sm-3"></label>
                                                <div class="col-sm-12 form-radio">
                                                    <input type="radio" name="audience" id="all-audience" value="all-audience">Visible to all audience at participating locations
                                                    <label for="all-audience"><span></span></label>
                                                </div>
                                                <div class="col-sm-12 form-radio v-t-margin">
                                                    <input type="radio" name="audience" id="targeted-audience" value="targeted-audience">Visible to targeted audience
                                                    <label for="targeted-audience"><span></span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12">
                                            <div class="add-customer-list">
                                                <a href="javascript:void(0)" class="btn green-btn"><span class="plus-icon"></span>Add Customer List</a>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="v-t-margin">
                                            <table class="col-sm-offset-2 col-sm-8 table-bordered table-condensed">
                                                <thead>
                                                    <tr>
                                                        <th>&nbsp;</th>
                                                        <th>Customer List Title</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td align="middle">
                                                            <a href="javascript:void(0)">
                                                                
                                                                <img class="dlt_img_cls" src="<?=ASSETS_IMG ?>/m/mediya_delete.png"/>
                                                            </a>
                                                        </td>
                                                        <td>Customer List Name.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>  
                                        <div class="clearfix"></div>
                                        <div class="row v-margin">
                                            <label class="col-sm-offset-2 col-sm-2 no-padding" for="device-category">Customer Device :</label>
                                            <div class="col-sm-6 no-padding">
                                                <select name="device-category" class="form-control device-category">
                                                    <option value="0" selected="selected">All (Default)</option>
                                                    <option value="1">IOS</option>
                                                    <option value="2">Android</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row v-t-margin">
                                            <div class="col-sm-3">
                                                <a id="btnNextAudience" href="javascript:void(0)" class="btn btn-block gray-btn">Next Step</a>
                                            </div>
                                            <div class="col-sm-6"></div>
                                            <div class="col-sm-3">
                                                <a href="javascript:void(0)" class="btn btn-block gray-btn btnCancelCampaign">Cancel</a>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="e">
                                    <div class="campaign-locations">
                                        <div class="col-sm-12 no-padding">
                                            <p class="heading">Campaign Participating Locations :</p>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12 no-padding">
                                            <div class="add-loaction">
                                                <a href="javascript:void(0)" class="btn green-btn"><span class="plus-icon"></span>Add Locations</a>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <table class="col-sm-12 table-bordered table-condensed v-margin">
                                            <thead>
                                                <tr>
                                                    <th width="50%">&nbsp;</th>
                                                    <th width="50%" align="middle">Participating Locations</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td width="50%" align="middle">
                                                        <a href="javascript:void(0)">
                                                            <img class="dlt_img_cls" src="<?=ASSETS_IMG ?>/m/mediya_delete.png"/>
                                                        </a>
                                                        </td>
                                                    <td width="50%">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td width="50%">&nbsp;</td>
                                                    <td width="50%">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td width="50%">&nbsp;</td>
                                                    <td width="50%">&nbsp;</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="clearfix"></div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <a id="btnNextLocation" href="javascript:void(0)" class="btn btn-block gray-btn">Next Step</a>
                                            </div>
                                            <div class="col-sm-6"></div>
                                            <div class="col-sm-3">
                                                <a href="javascript:void(0)" class="btn btn-block gray-btn btnCancelCampaign">Cancel</a>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="f">
                                    <div class="campaign-basics">
                                        <div class="col-sm-12 no-padding">
                                            <p class="heading">Campaign Policies :</p>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form">
                                            <div class="row v-b-margin">
                                                <label for="redemption-time-zone" class="col-md-3 col-sm-4">Redemption time zone :</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <input type="text" class="form-control" id="redemption-time-zone" name="redemption-time-zone">
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="redemption-expiry-date" class="col-md-3 col-sm-4">Redemption expiry date :</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <input type="text" class="form-control" id="redemption-expiry-date" name="redemption-expiry-date">
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="total-number-of-offers" class="col-md-3 col-sm-4">Total number of offers :</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <input type="text" class="form-control" id="total-number-of-offers" name="total-number-of-offers">
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="redemption-points" class="col-md-3 col-sm-4">Redemption points :</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <input type="text" class="form-control" id="redemption-points" name="redemption-points">
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label for="referral-points" class="col-md-3 col-sm-4">Referral points :</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <input type="text" class="form-control" id="referral-points" name="referral-points">
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label class="col-md-3 col-sm-4" for="redemption-validity">Redemption validity&nbsp;:</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <select name="redemption-validity" class="form-control device-category">
                                                        <option value="0" selected="selected">One per customer</option>
                                                        <option value="1">One per customer-valid for new customer</option>
                                                        <option value="2">One per day</option>
                                                        <option value="3">One per visit</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row v-b-margin">
                                                <label class="col-md-3 col-sm-4" for="total-number-of-offers-left">Total number of offers Left :</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <input type="text" class="form-control" name="total-number-of-offers-left" id="total-number-of-offers-left">
                                                </div>
                                                
                                            </div>
                                            <div class="row v-b-margin">
                                                <label class="col-md-3 col-sm-4" for="total-points-required">Total points required :</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <input type="text" class="form-control" name="total-points-required" id="total-points-required">
                                                </div>
                                                
                                            </div>
                                            <div class="row v-b-margin">
                                                <label class="col-md-3 col-sm-4" for="total-points-blocked">Total points blocked&nbsp;:</label>
                                                <div class="col-md-9 col-sm-8">
                                                    <input type="text" class="form-control" name="total-points-blocked" id="total-points-blocked">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <a id="btnNextPolicy" href="javascript:void(0)" class="btn btn-block gray-btn">Next Step</a>
                                            </div>
                                            <div class="col-sm-6"></div>
                                            <div class="col-sm-3">
                                                <a href="javascript:void(0)" class="btn btn-block gray-btn btnCancelCampaign">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="g">
                                    <div class="summery-info-wrapper">
                                        <div class="col-sm-12 no-padding">
                                            <p class="heading">Summary :</p>
                                        </div>                                                                                  
                                        <div class="col-md-6 col-sm-12">
                                            <div class="col-left">
                                                <p class="head">Basic Options:</p>
                                                <div class="summery-info-left">
                                                    <table class="col-sm-12 table-condensed v-t-margin parent-table" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="75%">Select-template</th>
                                                                <th width="25%">
                                                                    <a id="edt_selecttemplate" href="javascript:void(0)" class="btn gray-btn">
                                                                        <span class="automate-icon"></span>
                                                                        Edit
                                                                    </a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <table class="col-md-12 table-condensed child-table" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Filter By </label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">All</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Template Image:</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">
                                                                        <div class="template-image">
                                                                            <img src="<?=ASSETS_IMG ?>/m/tempalate-image.png" alt="tempalate-image" />
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="clearfix"></div>
                                                    <hr/>
                                                    <table class="col-md-12 table-condensed v-b-margin parent-table" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="75%">Campaign Basics</th>
                                                                <th width="25%">
                                                                    <a id="edt_basics" href="javascript:void(0)" class="btn gray-btn">
                                                                        <span class="automate-icon"></span>
                                                                        Edit
                                                                    </a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <table class="col-md-12 table-condensed child-table" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Campaign Title</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Campaign Keywords</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Campaign Original Value</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Campaign Discount</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Campaign Category</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Date Range</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Day Parting</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">All Day</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Description</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem ipsum</div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="clearfix"></div>
                                                    <hr/>
                                                    <table class="col-md-12 table-condensed v-b-margin parent-table" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="75%">Campaign Images</th>
                                                                <th width="25%">
                                                                    <a id="edt_images" href="javascript:void(0)" class="btn gray-btn">
                                                                        <span class="automate-icon"></span>
                                                                        Edit
                                                                    </a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <table class="col-md-12 table-condensed child-table" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Campaign Images</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">
                                                                        <div class="template-image">
                                                                            <img src="<?=ASSETS_IMG ?>/m/campaign-thumb-image.png" alt="campaign-thumb-image" />
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>                                      
                                        <div class="col-md-6 col-sm-12">
                                            <div class="col-right">
                                                <p class="head">Advanced Options:</p>
                                                <div class="summery-info-right">
                                                    <table class="col-md-12 table-condensed v-t-margin parent-table" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="75%">Campaign Audience</th>
                                                                <th width="25%">
                                                                    <a id="edt_audience" href="javascript:void(0)" class="btn gray-btn">
                                                                        <span class="automate-icon"></span>
                                                                        Edit
                                                                    </a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <table class="col-md-12 table-condensed child-table" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Campaign Audience</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Visible to all audience at participating locations </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Customer List Title</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Customer List Name</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Customer Device</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">All (Default)</div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="clearfix"></div>
                                                    <hr/>
                                                    <table class="col-md-12 table-condensed v-b-margin parent-table" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="75%">Campaign Participating Locations</th>
                                                                <th width="25%">
                                                                    <a id="edt_location" href="javascript:void(0)" class="btn gray-btn">
                                                                        <span class="automate-icon"></span>
                                                                        Edit
                                                                    </a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <table class="col-md-12 table-condensed child-table" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Participating Locations</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="clearfix"></div>
                                                    <hr/>
                                                    <table class="col-md-12 table-condensed v-b-margin parent-table" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="75%">Campaign Policies</th>
                                                                <th width="25%">
                                                                    <a id="edt_policy" href="javascript:void(0)" class="btn gray-btn">
                                                                        <span class="automate-icon"></span>
                                                                        Edit
                                                                    </a>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <table class="col-md-12 table-condensed child-table" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Redemption time zone</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Redemption expiry date</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Total number of offers</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Redemption points</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Referral points</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Redemption validity</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">One per customer</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Total number of offers Left</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Total points available</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Total points required</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">
                                                                    <label class="title">Total points blocked</label>
                                                                </td>
                                                                <td width="75%">
                                                                    <div class="text-field">Lorem</div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>                                      
                                        <div class="clearfix"></div>
                                        <div class="row v-margin">
                                            <div class="col-sm-2">
                                            <a id="btnBackPolicy" href="javascript:void(0)" class="btn gray-btn"> Back </a>
                                        </div>
                                        <div class="col-sm-6 text-left">
                                            <a id="btnLaunchCampaign" href="javascript:void(0)" class="btn green-btn">Launch Campaign</a>
                                        </div>
                                        <div class="col-sm-4 text-right">
                                            <a href="javascript:void(0)" class="btn gray-btn btnCancelCampaign">Cancel</a>
                                        </div>
                                        <div class="clearfix"></div>
                                        </div>                                      
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </section>
    
</div>
<!-- start of image upload popup div PAY-508-28033 -->
 <div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="NotificationBackDiv" class="divBack">
                                        </div>
                                        <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left:30%;top: 11%;">
                                                
                                                                <div class="modal-close-button" style="visibility: visible;">
                                                             
                                                                    <a  tabindex="0" onclick="close_popup('Notification');" id="fancybox-close" style="display: inline;"></a>
                                                                </div>
                                                <div id="NotificationmainContainer" class="innerContainer" style="height:330px;width:600px">
                                                        <div class="main_content">  
                                                         <div style=" background: none repeat scroll 0 0 #222222;color: #CFCFCF !important;padding: 4px;border-radius: 10px 10px 0 0;">
                                <font style="font-family: Arial,Helvetica,sans-serif;font-size: 22px;font-weight: bold;letter-spacing: 1px;line-height: 24px;margin: 0;padding: 0 0 2px;text-shadow:1px 1px 1px #DCAAA1">
                                    <?php  echo  $merchant_msg["edit-compaign"]["Field_add_campaign_logo"]; ?>
                                </font>
                             </div>
                                                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                            <!-- -->
                            <div id="media-upload-header">
                                <ul id="sidemenu">
                                <li id="tab-type" class="tab_from_library"><a class="current" ><?php  echo  $merchant_msg["edit-compaign"]["Field_media_library"]; ?></a></li>
                                
                                </ul>
                            </div>
                            <!-- -->
                                
                                                          
                               <div style="clear: both" ></div>
                               <div style="display: none;padding-left: 13px; padding-right: 13px;" class="div_from_computer">
                                <div  style="padding-top:10px;padding-bottom:10px">Add media from your computer
                                    
                                </div>
                                <div style="clear: both" ></div>
                                <div style="width: 100%;height: 168px;border: dashed 1px black;display: block;" align="center">
                                    <div style="padding-top:20px;">
                                    <!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
                                    <div id="upload" >
                                    <span  >Upload Photo
                                    </span>
                                    </div>
                                    </div>
                                </div>
                                <div  align="center" style="padding-top:10px">
                                                                    <input class="save_btn" type="button" name="btn_save_from_computer" id="btn_save_from_computer" onclick="save_from_computer()"  value="<?php echo $merchant_msg['index']['btn_save'];?>"/>
                                </div>
                               </div>
                               <div style="display:block;padding-left: 13px; padding-right: 13px;" class="div_from_library">
                                <div  style="padding-top:10px;padding-bottom:10px"><?php  echo  $merchant_msg["edit-compaign"]["Field_add_campaign_logo_media_library"]; ?></div>
                                <?php
                                
                                    $flag = true;
                                    $merchant_array = array();
                                    $merchant_array['id'] = $_SESSION['merchant_id'];
                                    $merchant_info = $objDB->Show("merchant_user",$merchant_array);
                                    if($merchant_info->fields['merchant_parent'] != 0)
                                    {
                                        
                                        $media_acc_array = array();
                                        $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];;
                                        $RSmedia = $objDB->Show("merchant_user_role",$media_acc_array);
                                        $media_val = unserialize($RSmedia->fields['media_access']);
                                        if(in_array("view-use",$media_val))
                                        {
                                            $flag = true;
                                        }
                                        else{
                                            $flag = false;  
                                        }
                                    }
                                    else{
                                        $flag = true;
                                    }
                                    
                                    if($flag)
                                    {
                
                                ?>
                                <div id="media_library_listing" style="width:100%;height:180px;border:1px dashed #000;overflow:auto;">
                                    <div style="clear: both"></div>
                                    <ul class="ul_image_list">
                                    <?php
                                        //$query = "select * from merchant_media where image_type='campaign' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'].") order by id desc" ;
                                        //$RSImages = $objDB->execute_query($query);
                                         //if($RSImages->RecordCount()>0){
                                        //while($Row = $RSImages->FetchRow()){
                                                                                //echo WEB_PATH.'/merchant/process.php?btnGetLogosOfMerchant=yes&mer_id='.$_SESSION['merchant_id']."&img_type=campaign&mer_parent_id=".$merchant_info->fields['merchant_parent']; 
                                         $arr123=file(WEB_PATH.'/merchant/process.php?btnGetLogosOfMerchant=yes&mer_id='.$_SESSION['merchant_id']."&img_type=campaign&mer_parent_id=".$merchant_info->fields['merchant_parent']."&start_index=0&num_of_records=12");
                                        
                                                                                //print_r($arr);
                                                                                if(trim($arr123[0]) == "")
                                        {
                                            unset($arr123[0]);
                                            $arr123 = array_values($arr123);
                                        }
                                        $json123 = json_decode($arr123[0]);
                                                                                //echo $json; 
                                        $total_records123= $json123->total_records;
                                        $records_array123 = $json123->records;
                                        //echo $json123->query_new;
                                                                                //echo $json123->query_old;
                                        if($total_records123>0){
                                            foreach($records_array123 as $Row)
                                            {
                                        ?>
                                    
                                        
                                        <li class="li_image_list" id="li_img_<?=$Row->id;?>">
                                            <div>
                                                <img src="<?php echo ASSETS_IMG .'/m/campaign/'.$Row->image;  ?>" height="50px" width="50px" />
                                                <span style="vertical-align: top" id="span_img_text_<?=$Row->id;?>"><?=$Row->image?></span>
                                                <span style="vertical-align: top;float: right"> Use this image&nbsp;<input type="radio" name="use_image" value="<?=$Row->id?>" /></span>
                                            </div>
                                            
                                        </li>
                                        <?php }}?>
                                    </ul>
                                    
                                </div>
                                <div  align="center" style="padding-top:10px">
                                        <input type="button" class="save_btn" name="btn_save_from_library" id="btn_save_from_library" onclick="save_from_library()"  value="<?php echo $merchant_msg['index']['btn_save'];?>"/>
                                    </div>
                               </div>
                               <?php
                                    }
                                    else{
                                        ?>
                                        <div  style="padding-top:10px;padding-bottom:10px">
                                            You don't have access to use media library images.
                                        </div>
                                        <?php
                                    }
                               ?>
                                                            </div>
                                                          
                                                        </div>
                                                 </div>
                                            </div>
                                       </div> 
  </div>
    <?php
        echo file_get_contents(WEB_PATH.'/merchant/import_media_library.php?mer_id='.$_SESSION['merchant_id'].'&img_type=campaign&start_index=0');
    ?>
    <input type="hidden" name="hdn_image_path1" id="hdn_image_path1" value="" />
    <input type="hidden" name="hdn_image_id1" id="hdn_image_id1" value="" />
    <input type="hidden" name="hdn_image_path2" id="hdn_image_path2" value="" />
    <input type="hidden" name="hdn_image_id2" id="hdn_image_id2" value="" />
    <input type="hidden" name="hdn_image_path3" id="hdn_image_path3" value="" />
    <input type="hidden" name="hdn_image_id3" id="hdn_image_id3" value="" />
    <input type="hidden" name="hdn_image_path4" id="hdn_image_path4" value="" />
    <input type="hidden" name="hdn_image_id4" id="hdn_image_id4" value="" />
    <input type="hidden" name="hdn_image_path5" id="hdn_image_path5" value="" />
    <input type="hidden" name="hdn_image_id5" id="hdn_image_id5" value="" />
  </form>
  </div>
  <!---------start footer--------------->
       <div>
        <?
        require_once(MRCH_LAYOUT."/footer.php");
        ?>
        <!--end of footer--></div>
        
 <!-- end of popup div PAY-508-28033 -->
 <script>
    function bind_rule_time()
    { 
      jQuery(function() {
        jQuery('.rulefromtime').timepicker({ 'scrollDefaultNow': true });
        jQuery('.ruletotime').timepicker({ 'scrollDefaultNow': true });
      });
    }                                         
    var dcnt=1; 
    function bind_remove_rule()
    {
        jQuery(".removeRule").click(function(){
            jQuery(this).parent().parent().remove();
        });
    }
    jQuery("#btnAddNewRule").click(function(){
        
        var tr_html='<tr>\
                        <td width="8%">\
                            <div class="close-icon removeRule">\
                                <img src="<?=ASSETS_IMG ?>/m/close-icon.png" alt="close-icon" />\
                            </div>\
                        </td>\
                        <td width="18%" class="left">\
                            <input type="text" name="from-time_day1_'+dcnt+'" class="form-control rulefromtime" placeholder="12:00 am"/>\
                        </td>\
                        <td width="18%" class="left">\
                            <input type="text" name="to-time_day1_'+dcnt+'" class="form-control ruletotime" placeholder="11:59 am"/>\
                        </td>\
                        <td width="8%">\
                            <div class="form-radio">\
                                <input type="checkbox" id="checkboxday1_'+dcnt+'" name="checkboxday1_'+dcnt+'">\
                                <label for="checkboxday1_'+dcnt+'">&nbsp;</label>\
                            </div>\
                        </td>\
                        <td width="8%">\
                            <div class="form-radio">\
                                <input type="checkbox" id="checkboxday2_'+dcnt+'" name="checkboxday2_'+dcnt+'">\
                                <label for="checkboxday2_'+dcnt+'">&nbsp;</label>\
                            </div>\
                        </td>\
                        <td width="8%">\
                            <div class="form-radio">\
                                <input type="checkbox" id="checkboxday3_'+dcnt+'" name="checkboxday3_'+dcnt+'">\
                                <label for="checkboxday3_'+dcnt+'">&nbsp;</label>\
                            </div>\
                        </td>\
                        <td width="8%">\
                            <div class="form-radio">\
                                <input type="checkbox" id="checkboxday4_'+dcnt+'" name="checkboxday4_'+dcnt+'">\
                                <label for="checkboxday4_'+dcnt+'">&nbsp;</label>\
                            </div>\
                        </td>\
                        <td width="8%">\
                            <div class="form-radio">\
                                <input type="checkbox" id="checkboxday5_'+dcnt+'" name="checkboxday5_'+dcnt+'">\
                                <label for="checkboxday5_'+dcnt+'">&nbsp;</label>\
                            </div>\
                        </td>\
                        <td width="8%">\
                            <div class="form-radio">\
                                <input type="checkbox" id="checkboxday6_'+dcnt+'" name="checkboxday6_'+dcnt+'">\
                                <label for="checkboxday6_'+dcnt+'">&nbsp;</label>\
                            </div>\
                        </td>\
                        <td width="8%">\
                            <div class="form-radio">\
                                <input type="checkbox" id="checkboxday7_1" name="checkboxday7_1">\
                                <label for="checkboxday7_1">&nbsp;</label>\
                            </div>\
                        </td>\
                    </tr>';
        jQuery(".table-bordered.rulebody tbody").append(tr_html);
        dcnt++;
        bind_remove_rule();
        bind_rule_time();
    });
     
    jQuery("input[name='day-parting']").change(function(){
        
        console.log(jQuery(this).val());
        if(jQuery(this).val()==1)
            jQuery("#specific-hours-div").css("display","block");
        else
            jQuery("#specific-hours-div").css("display","none");
    });
    
    jQuery("[name='chkOnOff']").bootstrapSwitch();
    
    jQuery('input[name="chkOnOff"]').on('switchChange.bootstrapSwitch', function(event, state) {
      console.log(this); // DOM element
      console.log(event); // jQuery event
      console.log(state); // true | false
    });

    function check_is_walkin(val)
    {
        $av = jQuery.noConflict();
        if(val==0)
            {
               $av ("#group").css("display","");
            }
            else
             {
                $av("#group").css("display","none");
                $av("#one_per_customer").attr("checked", "checked");
             }            
    }
    var file_path = "";
jQuery("#show_more_mediya_browse").live("click",function(){
    var cur_el= jQuery(this);
    var next_index = parseInt(jQuery(this).attr('next_index'));
    var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
    jQuery.ajax({
        type:"POST",
        url:'process.php',
        data :'show_more_media_browse=yes&next_index='+next_index+'&num_of_records='+num_of_records+"&img_type=campaign",
        async:true,
        success:function(msg)
        {
            var obj = jQuery.parseJSON(msg);
            //alert(obj.status);
            jQuery(".fancybox-inner .ul_image_list").append(obj.html);
            cur_el.attr('next_index',next_index + num_of_records);
            if(parseInt(obj.total_records)<num_of_records)
            {
                cur_el.css("display","none");
            }
        }
    });
});

jQuery("#edt_selecttemplate").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='SelectTemplate']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='a'].tab-pane").addClass("active");
});
jQuery("#edt_basics").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignBasics']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='b'].tab-pane").addClass("active");
});
jQuery("#edt_images").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignImages']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='c'].tab-pane").addClass("active");
});
jQuery("#edt_audience").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignAudience']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='d'].tab-pane").addClass("active");
});
jQuery("#edt_location").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignLocations']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='e'].tab-pane").addClass("active");
});
jQuery("#edt_policy").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignPolicies']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='f'].tab-pane").addClass("active");
});


jQuery("#btnLaunchCampaign").click(function(){
    console.log("Campaign Launched...");
});

jQuery("#btnBackPolicy").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignPolicies']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='f'].tab-pane").addClass("active");
});

jQuery("#btnNextPolicy").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='Summary']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='g'].tab-pane").addClass("active");
});

jQuery("#btnNextLocation").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignPolicies']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='f'].tab-pane").addClass("active");
});

jQuery("#btnNextAudience").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignLocations']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='e'].tab-pane").addClass("active");
});

jQuery("#btnNextImages").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignAudience']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='d'].tab-pane").addClass("active");
});

jQuery("#btnNextBasic").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignImages']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='c'].tab-pane").addClass("active");
});

jQuery("#btnNextTemplate").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignBasics']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='b'].tab-pane").addClass("active");
});

jQuery("#btnSkipTemplate").click(function(){
    jQuery(".campaign-tab-container .list-group li").removeClass("active");
    jQuery(".campaign-tab-container .list-group li[tab='CampaignBasics']").addClass("active");
    
    jQuery(".tab-content .tab-pane").removeClass("active");
    jQuery(".tab-content div[id='b'].tab-pane").addClass("active");
});




jQuery(".btnCancelCampaign").click(function(){
    window.location.href="<?php echo WEB_PATH ?>/merchant/compaigns.php?action=active";
});

jQuery("#template_category").change(function(){
    console.log(jQuery(this).val());
    if(jQuery(this).val()==0)
    {   
        jQuery(".template-image-wrapper .col-md-3").css("display","block");
    }
    else
    {
        jQuery(".template-image-wrapper .col-md-3").css("display","none");
        jQuery(".template-image-wrapper .col-md-3[category_id='"+jQuery(this).val()+"']").css("display","block");
    }
    
});

jQuery("#show_more_templates").live("click",function(){
    open_popup("NotificationLoadingData");
    var flag=0;
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
                window.location.href=obj.link;
                flag=1;
            }
        }
    });
    
    if(flag == 1)
    {
        return false;
    }
    else
    {
        var img_filter = jQuery(".fltr_selected").val();
        //alert(img_filter);
        
        var cur_el= jQuery(this);
        var next_index = parseInt(jQuery(this).attr('next_index'));
        var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
        
        jQuery.ajax({
            type:"POST",
            url:'process.php',
            data :'btnGetTemplateOfMerchant_ajax=yes&next_index='+next_index+'&num_of_records='+num_of_records,
            async:true,
            success:function(msg)
            {
                var obj = jQuery.parseJSON(msg);

                if (obj.status=="true")     
                {
                    jQuery(".template-image-wrapper").append(obj.html);
                    cur_el.attr('next_index',next_index + num_of_records);
                    console.log("records_return="+obj.records_return);
                    console.log("num_of_records="+num_of_records);
                    if(parseInt(obj.records_return)<num_of_records)
                    {
                        cur_el.css("display","none");
                    }

                }
                else
                {
                    if(parseInt(obj.records_return)<num_of_records)
                    {   
                        cur_el.css("display","none");
                    }
                }
            }
        });
    }
    close_popup("NotificationLoadingData"); 
});

jQuery(".ul_image_list li").live("click",function(){
    
    jQuery(".useradioclass").prop( "checked", false );
    var imgid=jQuery(this).attr("id").split("img_");
    imgid=imgid[1];
    //alert(imgid);
    jQuery(this).find(".useradioclass").prop( "checked", true );
    
    jQuery(".ul_image_list li").removeClass("current");
    jQuery(this).addClass("current");
    
    jQuery(".fancybox-inner .useradioclass").each(function(){
               
        if(jQuery(".fancybox-inner .useradioclass").is(":checked"))
        {
            
            jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
            jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
            jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#3C99F4 !important");
        }
        else
        {
            jQuery(".fancybox-inner #btn_save_from_library").attr("disabled",true);
            jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
            jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#ABABAB !important");
        }
        
    });
            
    
});
 
/* start of script for PAY-508-28033*/
function save_from_library()
{
        $av = jQuery.noConflict();
     var sel_val = $av('input[name=use_image]:checked').val();
         
     <!--// 369-->
     if (sel_val==undefined)
     {
        jQuery.fancybox.close();
     }
     else
     {
        var cur_img_counter = 0;
        
        if(jQuery("#hdn_image_id1").val()=="")
        {
            cur_img_counter =1;
            jQuery("#hdn_image_id1").val(sel_val);
        }
        else if(jQuery("#hdn_image_id2").val()=="")
        {
            cur_img_counter =2;
            jQuery("#hdn_image_id2").val(sel_val);
        }
        else if(jQuery("#hdn_image_id3").val()=="")
        {
            cur_img_counter =3;
            jQuery("#hdn_image_id3").val(sel_val);
        }
        else if(jQuery("#hdn_image_id4").val()=="")
        {
            cur_img_counter =4;
            jQuery("#hdn_image_id4").val(sel_val);
        }
        else if(jQuery("#hdn_image_id5").val()=="")
        {
            cur_img_counter =5;
            jQuery("#hdn_image_id5").val(sel_val);
        }       
        
        
            var sel_src = $av(".fancybox-inner #li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
            
        
        if(jQuery("#hdn_image_path1").val()=="")
        {
            cur_img_counter =1;
            jQuery("#hdn_image_path1").val(sel_src);
        }
        else if(jQuery("#hdn_image_path2").val()=="")
        {
            cur_img_counter =2;
            jQuery("#hdn_image_path2").val(sel_src);
        }
        else if(jQuery("#hdn_image_path3").val()=="")
        {
            cur_img_counter =3;
            jQuery("#hdn_image_path3").val(sel_src);
        }
        else if(jQuery("#hdn_image_path4").val()=="")
        {
            cur_img_counter =4;
            jQuery("#hdn_image_path4").val(sel_src);
        }
        else if(jQuery("#hdn_image_path5").val()=="")
        {
            cur_img_counter =5;
            jQuery("#hdn_image_path5").val(sel_src);
        }
           
        
               
           /* NPE-252-19046 */
           
           /* NPE-252-19046 */
           file_path = "";
           //close_popup('Notification');
               jQuery.fancybox.close();
               
            var img = '<div class="col-5">';
                img+= '<div class="template-image">';
                img+= '<img id="campaign_img'+cur_img_counter+'" set="yes" src="<?=ASSETS_IMG ?>/m/campaign/'+ sel_src +'" class="displayimg"/>';
                img+= '<div class="delete-icon"><img src="<?=ASSETS_IMG ?>/m/mediya_delete.png" onclick="rm_image_lib('+cur_img_counter+')" /></div>';
                img+= '</div>';
                img+= '</div>';
    
              
           $av('.campaign-image-wrapper').append(img);
     }
     <!--// 369-->
}
function rm_image_lib(counter)
{
    $rm = jQuery.noConflict();
    $rm("#hdn_image_path"+counter).val("");
    $rm("#hdn_image_id"+counter).val("");
    $rm("#campaign_img"+counter).parent().parent().remove();
    
}
function rm_image(id)
{
    
    $rm = jQuery.noConflict();
    
    jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'is_image_delete=yes&image_type=campaign&filename='+id,
                          async:false,
                           success:function(msg)
                           {
                                $rm("#hdn_image_path").val("");
                                $rm("#hdn_image_id").val("");
                                $rm('#files').html("");
                           }
                           
                     });
    
}
function save_from_computer()
{
    $as = jQuery.noConflict();
    $as("#hdn_image_path").val(file_path);
    $as("#hdn_image_id").val("");
    /* NPE-252-19046 */
    
    /* NPE-252-19046 */
    
    close_popup('Notification');
    var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ file_path +"' class='displayimg'>";
    $as('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' id='"+file_path+"' class='cancel_remove_image' onclick='rm_image(this.id)' /></div></div></div>");
}
$atab = jQuery.noConflict();
/* NPE-252-19046 */
$atab(document).ready(function(){
    if($atab("#hdn_image_path").val()  != ""){
    var img = "<img src='<?=ASSETS_IMG ?>/m/campaign/"+ $atab("#hdn_image_path").val() +"' class='displayimg'>";
    $atab('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' id='"+$atab("#hdn_image_path").val()+"' class='cancel_remove_image' onclick='rm_image(this.id)' /></div></div></div>");
    }
    
    $atab(".tempate_preview_image").click(function(){
    
        //alert($atab(this).next().next().val());
        
        var tempEle= $atab(this).next().next();
        tempEle.trigger("click");
        
    });
    
    $atab("input:radio[name=template_value]").click(function(){
    var t_val = $atab(this).attr("value");
            $atab.ajax({
                type: "POST",
                url: "<?=WEB_PATH?>/merchant/load_template.php",
                data: "template_id=" + t_val,
                success: function(msg) {
                    
                if(t_val == "0")
                {
                    //alert("come in");
                    $atab('#files').html("");
                    //$atab("#img_preview").attr("src","");
                    $atab("#description").text("");
                     var sel_val = $atab('input[name=use_image]:checked').val();   
                    var sel_src = $atab("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
                    //alert(sel_src );
                    //alert($atab("#description").text());
                    $atab("#description").text("<p></p>");
                    //alert($atab("#description").text());
                                        $atab("#deal_detail_description").text("");
                    $atab("#hdn_image_path").val("");
                    $atab("#img_div").css("display","none");
                }else{
                        var obj = eval('('+msg+')');
                if(obj['fields'].business_logo!="")
                {
                    var img_businesslog = "<?=ASSETS_IMG ?>/m/campaign/"+obj['fields'].business_logo;
                }
                else
                {
                    var img_businesslog = "<?=ASSETS_IMG ?>/m/campaign/Merchant_Offer.png";
                }
                var img = "<img src='"+ img_businesslog +"' class='displayimg'>";
    $atab('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image_lib()' /></div></div></div>");
                    //$atab("#img_preview").attr("src",img_businesslog);
                    $atab("#description").text("");
                                         tinyMCE.activeEditor.setContent(obj['fields'].description);
                    //$atab("#description").text(obj['fields'].description);
                                        $atab("#title").val(obj['fields'].title);
                    $atab("#category_id").val(obj['fields'].category_id);
                    //$atab("#discount").val(obj['fields'].discount);
                    
                    $atab("#hdn_image_path").val(obj['fields'].business_logo);
                                        $atab("#deal_detail_description").text(obj['fields'].print_coupon_description);
                    $atab("#img_div").css("display","block");
                }
                }
                
            });

    });
});
$atab(function(){
        var btnUpload=$atab('#upload');
        var status=$atab('#status');
        
        new AjaxUpload(btnUpload, {
            action: 'merchant_media_upload.php?doAction=FileUpload&img_type=campaign',
            name: 'uploadfile',
            onSubmit: function(file, ext){
                /*
                if($atab('#files').children().length > 0)
                {
                    $atab('#files li').detach();
                }
                */
                 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
                    status.text('Only JPG, PNG or GIF files are allowed');
                    return false;
                }
                status.text('Uploading...');
            },
            onComplete: function(file, response){
                //On completion clear the status
                /*
                var arr = response.split("|");
                
                status.text('');
                //Add uploaded file to list
                file_path = arr[1];
                save_from_computer();
                                */
                               var arr = response.split("|");
                if(arr[1]=="small")
                                {
                                    status.text(arr[0]);
                                }
                                else
                                { 
                                    status.text('');
                                    //Add uploaded file to list
                                    file_path = arr[1];
                                    save_from_computer();
                                }
            }
        });
        
    });
$atab(".tab_from_library a").click(function(){
    $atab("#sidemenu li a").each(function() {
        $atab(this).removeClass("current");
        });
    $atab(this).addClass("current");
    $atab(".div_from_library").css("display","block");
    $atab(".div_from_computer").css("display","none");
    });
$atab(".tab_from_computer a").click(function(){
    $atab("#sidemenu li a").each(function() {
        $atab(this).removeClass("current");
        });
    $atab(this).addClass("current");
    $atab(".div_from_library").css("display","none");
    $atab(".div_from_computer").css("display","block");
    });
/* NPE-252-19046 */


function close_popup(popup_name)
{
$ac = jQuery.noConflict();
    $ac("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
    $ac("#" + popup_name + "BackDiv").fadeOut(200, function () {
         $ac("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
                $ac("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
                $ac("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
                $ac("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
         });
    });
    });
    
}
 function open_popup(popup_name)
{
$ao = jQuery.noConflict();
    if($ao("#hdn_image_id").val()!="")
    {
        $ao('input[name=use_image][value='+$ao("#hdn_image_id").val()+']').attr("checked","checked");
    }
    $ao("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
        $ao("#" + popup_name + "BackDiv").fadeIn(200, function () {
             $ao("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
    
             });
        });
    });
    
    
}
/* end of script for PAY-508-28033*/
jQuery.noConflict();
</script>
<?
$_SESSION['msg'] = "";
?>

<script language="javascript">
// 369
$abc = jQuery.noConflict();
$abc(document).ready(function() { 
    // bind form using ajaxForm 
  $abc('#add_campaign_form').ajaxForm({ 
        dataType:  'json', 
        success:   processAddCampJson 
    });
    
});
function processAddCampJson(data) {
    
    if(data.status == "true"){

        window.location.href='<?=WEB_PATH.'/merchant/compaigns.php?action=active'?>';
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
// 369
$abc(".camp_location").click(function(){
    
  t_val = $abc(this).val() ;
  if($abc(this).is(':checked'))
  {
      if($abc(".chk_private").is(':checked'))
        {
          var private_flag = 1;
        }
        else
        {
                var private_flag = 0;
        }
        
        $abc.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "location_id=" + t_val +"&getlocationwisegroup=yes&public="+private_flag,
                                      success: function(msg) {
                                          
                                          $abc(".location_listing_area").append(msg);
                                          
                                            //19-09-2013
                                            
                                            if($abc(".chk_private").is(':checked'))
                                            {
                                                //$abc(".other_group").css("display","none");
                                                //alert("checked");
                                                //$abc("ul[id^='locationgroup'] li:not(:first)").css("display","none");
                                                $abc("ul[id^='locationgroup']").each(function( index ) {
                                                    //alert("hi");
                                                    $abc(this).find("li:not(:first)").css("display","none");
                                                });
                                            }
                                            else
                                            {
                                                //$abc(".other_group").css("display","block");
                                                //alert("not checked");
                                                //$abc("ul[id^='locationgroup'] li:not(:first)").css("display","block");
                                                $abc("ul[id^='locationgroup']").each(function( index ) {
                                                    //alert("hi");
                                                    $abc(this).find("li:not(:first)").css("display","block");
                                                });
                                            }
                                            
                                            //19-09-2013
                                      }
        });
        
  }
  else
  {
      
      $abc("#locationid_"+$abc(this).val()).detach();
  }
});
$abc(".chk_private").click(function(){
    if($abc(this).is(':checked'))
        {
           $abc("input[type=checkbox][private_group]").each(function(){
                $abc(this).attr("checked","checked") ;
               $abc(this).attr("disabled", false);
           });
           // 8-8-2013

           $abc("input[type=checkbox][class=other_group]").each(function(){
                //$abc(this).attr("checked","unchecked") ;
                $abc(this).prop('checked', false);
                                $abc(this).attr("disabled", true) ;
           });
           jQuery("input[id^='hdn_campaign_type_']" ).val("1");
                   jQuery("input[id^='hdn_campaign_type1_']" ).val("1");
           // 8-8-2013
           
           //19-09-2013
           
           //jQuery("ul[id^='locationgroup'] li:not(:first)").css("display","none");
           jQuery("ul[id^='locationgroup']").each(function( index ) {
                //alert("hi");
                jQuery(this).find("li:not(:first)").css("display","none");
            });
                                                
           //19-09-2013
        }
        else
        {
              $abc("input[type=checkbox][private_group]").each(function(){
                $abc(this).removeAttr("checked") ;
                $abc(this).attr("disabled", false) ;
           });
           $abc("input[type=checkbox][class=other_group]").each(function(){
              
                                $abc(this).attr("disabled", false) ;
           });
           jQuery("input[id^='hdn_campaign_type_']" ).val("0");
                   jQuery("input[id^='hdn_campaign_type1_']" ).val("0");
                   
            //19-09-2013
            
           //jQuery("ul[id^='locationgroup'] li:not(:first)").css("display","block");
            jQuery("ul[id^='locationgroup']").each(function( index ) {
                //alert("hi");
                jQuery(this).find("li:not(:first)").css("display","block");
            });
            
           //19-09-2013    
        }
});
/*
$abc(".private_group").live("click",function(){
    //alert($abc(this).parent().parent().attr("id"));
    var locationidname=$abc(this).parent().parent().attr("id").split("_");
    var locationid=locationidname[1];
    if($abc(".chk_private").is(':checked'))
    {
        if($abc(this).is(':checked'))
        {
            //alert("checked");
            jQuery("#hdn_campaign_type_"+ locationid).val("2");
        }
        else
        {
            //alert("unchecked");
            return false;
        }       
    }
    else
    {
        // 8-8-2013
        var flg=true;
        $abc(this).parent().parent().find('li .other_group').each(function( index ) {
            //alert( index + ": " + $abc(this).text() );
            //alert($abc(this).is(':checked'));
            if($abc(this).is(':checked'))
                flg=false;
            if(!flg)
                return false;
        });
        if(!flg)
            return false;
        else
            jQuery("#hdn_campaign_type_"+ locationid).val("2");
        // 8-8-2013
    }
});
*/
// 8-8-2013
$abc(".other_group").live("click",function(){
       
    var locationidname=$abc(this).parent().parent().attr("id").split("_");
        
    var locationid=locationidname[1];
       
       var oldvalue=jQuery("#hdn_campaign_type1_"+ locationid).val();
       
       var is_checkboxval=parseInt(oldvalue)+1;
       var is_not_checkboxval=parseInt(oldvalue)-1;
                if($abc(this).is(':checked'))
                {
                    
                    jQuery("#hdn_campaign_type1_"+ locationid).val(is_checkboxval);
                }
                else
                {
                       jQuery("#hdn_campaign_type1_"+ locationid).val(is_not_checkboxval);
                }
       
        
        if(jQuery("#hdn_campaign_type1_"+ locationid).val() == 0)
            {
                 $abc(this).parent().parent().find(".private_group").attr("disabled", false);
            }
            else
                {
                    $abc(this).parent().parent().find(".private_group").attr("disabled", true);
                }
    //alert($abc(this).parent().parent().get(0).tagName);
    //alert($abc(this).parent().parent().attr("id"));
    //alert($abc(this).parent().parent().find(".private_group").is(':checked'));
    if($abc(this).parent().parent().find(".private_group").is(':checked'))
    {
        //return false;
               // $abc(".other_group").attr("disabled", true);
    }
    else
    {
        jQuery("#hdn_campaign_type_"+ locationid).val("3");
    }   
});
$abc(".private_group").live("click",function(){
    var locationidname=$abc(this).parent().parent().attr("id").split("_");
        
    var locationid=locationidname[1];
        
     if($abc(".chk_private").is(':checked'))
    {    
        if($abc(this).is(':checked'))
        {
            jQuery("#hdn_campaign_type1_"+ locationid).val("1");
            jQuery("#hdn_campaign_type_"+ locationid).val("2");
             $abc("#locationgroup_"+locationid+" .other_group").attr("disabled", true);
        }
        else
        {

           return false; 
                jQuery("#hdn_campaign_type1_"+ locationid).val("0");


              // $abc("#locationgroup_"+locationid+" .other_group").attr("disabled", false);

        }
    }
    else
        {
            
            if($abc(this).is(':checked'))
            {
                jQuery("#hdn_campaign_type1_"+ locationid).val("1");
                jQuery("#hdn_campaign_type_"+ locationid).val("2");
                 $abc("#locationgroup_"+locationid+" .other_group").attr("disabled", true);
            }
            else
            {

               
                    jQuery("#hdn_campaign_type1_"+ locationid).val("0");


                   $abc("#locationgroup_"+locationid+" .other_group").attr("disabled", false);

            } 
        }
    
})
// 8-8-2013
function changetext1(){
   var v = 76 - $abc("#title").val().length;
   $abc(".span_c1").text(v+" characters remaining");
}
function changetext2(){
   var v = 8 - $abc("#discount").val().length;
   $abc(".span_c2").text(v+" characters remaining");
}
function changetextpcd(){
    var v = 300 - $abc("#deal_detail_description").val().length;
   $abc(".span_pcd").text(v+" characters remaining"); 
}
function getpointvalue1()
{
     var numbers = /^[0-9]+$/; 
    if($abc("#txt_price").val() == "")
        {
            
        }
     
  else if($abc("#txt_price").val().match(numbers))  
   { 
     $abc.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/merchant/process.php",
                                      data: "txt_price=" + $abc("#txt_price").val() +"&getpoints=yes",
                                      success: function(msg) 
                                      {
                                          
                                          $abc("#span_con_point").text(msg);
                                           $abc(".success_msg").text("");
                                      }
        });
        }
        else
            {
                //alert("Please Enter Proper Price");
        var head_msg="<div class='head_msg'>Message</div>"
        var content_msg="<div class='content_msg'><?php echo $merchant_msg['edit-compaign']['please_enter_correct_amount'];?></div>";
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
                $abc("#span_con_point").text("");
                 $abc(".success_msg").text("");
            }
}


</script>
<!-- start datepicker -->

<!--<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.js"></script>-->

<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.ui.core.js"></script>


<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.ui.datepicker.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery-ui-timepicker-addon.js"></script>


  <script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery.timepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS ?>/m/jquery.timepicker.css" />
  
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.ui.datepicker.css">
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.ui.theme.css">
<!-- end datepicker -->

<!--- tooltip css --->

<!--- tooltip css --->

<script type="text/javascript">
jQuery(document).ready(function(){
    

jQuery("a#compaigns").css("background-color","orange");
/*
jQuery("#expiration_date").datetimepicker({onSelect: function(){
           
}});

jQuery("#start_date").datetimepicker({ onSelect: function(dateText,inst){
        
        var date = jQuery("#start_date").datepicker( 'getDate' );
        
         var actualDate = new Date(date);
         
         var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+30);
         
         
         
    jQuery('#expiration_date').datepicker('option', {
        
                minDate: jQuery(this).datepicker( 'getDate' ),
                //maxDate : newDate
                
        
            });
        
}});
*/
jQuery("#expiration_date").datepicker({onSelect: function(){
           
}});

jQuery("#start_date").datepicker({ onSelect: function(dateText,inst){
        
        var date = jQuery("#start_date").datepicker( 'getDate' );
        
         var actualDate = new Date(date);
         
         var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+30);
         
         
         
    jQuery('#expiration_date').datepicker('option', {
        
                minDate: jQuery(this).datepicker( 'getDate' ),
                //maxDate : newDate
                
        
            });
        
}});
jQuery("#walkinid").click(function(){
        if(jQuery('#walkinid').is(':checked'))
    {
        jQuery("#one_per_customer_per_day").attr("disabled", "disabled");
        jQuery("#multiple_use").attr("disabled", "disabled");
    }
    
});
jQuery("#groupid").click(function(){
        if(jQuery('#groupid').is(':checked'))
    {
        jQuery("#one_per_customer_per_day").removeAttr("disabled");
        jQuery("#multiple_use").removeAttr("disabled");
    }
    
});

jQuery(".fancybox-inner .useradioclass").live("change",function(){
             
            jQuery(".fancybox-inner .useradioclass").each(function(){
               
                if(jQuery(".fancybox-inner .useradioclass").is(":checked"))
                    {
                        
                        jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
                        jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
                        jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#3C99F4 !important");
                    }
                    else
                    {
                        jQuery(".fancybox-inner #btn_save_from_library").attr("disabled",true);
                        jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
                        jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#ABABAB !important");
                    }
            })
        });

jQuery('.notification_tooltip').tooltip({
content: function() {
        return jQuery(this).attr('title');
    },
    track: true,
    delay: 0,
    showURL: false,
    showBody: "<br>",
    fade: 250
    
});

jQuery('input:radio[name="number_of_use"]').change(function(){
       //alert(jQuery(this).parent().parent().next().html());
       //alert(jQuery(this).val());
       if(jQuery(this).val()=="2" || jQuery(this).val()=="3")
       {
            jQuery(this).parent().parent().next().css('display','none');
       }
       else
       {
            jQuery(this).parent().parent().next().css('display','table-row');
       }
});

jQuery('.mediaclass').click(function(){
    
        if(jQuery("#hdn_image_id1").val()!="" && jQuery("#hdn_image_id2").val()!="" && jQuery("#hdn_image_id3").val()!="" && 
        jQuery("#hdn_image_id4").val()!="" && jQuery("#hdn_image_id5").val()!="")
        {
            alert("you can add only 5 images");
        }
        else
        {
            jQuery.fancybox({
                    content:jQuery('#mediablock').html(),

                    type: 'html',
                    width:600,
                    height:500,
                    autoSize : false,
                    openSpeed  : 300,

                    closeSpeed  : 300,
                    // topRatio: 0,

                    changeFade : 'fast',  

                    helpers: {
                            overlay: {
                            opacity: 0.3
                            } // overlay
                    }

            });
        }
});

});

</script>
<script type="text/javascript">
     jQuery.noConflict();
    jQuery(document).ready(function(){
    //jQuery('.list_carousel').css("height","145px");
    
    
    });

/*
jQuery("#discount").blur(function(){
    jQuery("#discount").val(jQuery("#discount").val().trim());
    var deal_value=jQuery("#deal_value").val();
    var discount=jQuery("#discount").val();
    
    var n=deal_value.indexOf("$");
    if(n!="-1")
    {
        //alert("found");
        deal_value =deal_value.substring(n+1,deal_value.length);
    }
    //alert(deal_value);
    
    if(deal_value!="0")
    {
        if(discount!="" && discount!="NaN")
        {
            var saving=(parseFloat(deal_value)*parseFloat(discount))/100;
            if(isNaN(saving))
            {
                jQuery("#saving").val("");  
            }
            else
            {
                jQuery("#saving").val(Math.round(100*saving)/100);  
            }
        }
    }
}); 
jQuery("#saving").blur(function(){
    jQuery("#saving").val(jQuery("#saving").val().trim());
    var deal_value=jQuery("#deal_value").val();
    var saving=jQuery("#saving").val();
    
    var n=deal_value.indexOf("$");
    if(n!="-1")
    {
        //alert("found");
        deal_value =deal_value.substring(n+1,deal_value.length);
    }
    //alert(deal_value);
    
    if(deal_value!="0")
    {
        if(saving!="" && saving!="NaN")
        {
            var discount=(parseFloat(saving)*100)/parseFloat(deal_value);
            if(isNaN(discount))
            {
                jQuery("#discount").val("");    
            }
            else
            {
                jQuery("#discount").val(Math.round(100*discount)/100);  
            }
        }
    }
}); 
jQuery("#deal_value").blur(function(){
    jQuery("#deal_value").val(jQuery("#deal_value").val().trim());
    var deal_value=jQuery("#deal_value").val();
    var discount=jQuery("#discount").val();
    var saving=jQuery("#saving").val();
    
    var n=deal_value.indexOf("$");
    if(n!="-1")
    {
        //alert("found");
        deal_value =deal_value.substring(n+1,deal_value.length);
    }
    //alert(deal_value);                                                            
    
    if(deal_value=="0")
    {
        jQuery("#discount").val("0");   
        jQuery("#saving").val("0"); 
    }
    if(deal_value!="" && deal_value!="NaN")
    {
        if(discount!="" && discount!="NaN")
        {
            var saving=(parseFloat(deal_value)*parseFloat(discount))/100;
            if(isNaN(saving))
            {
                jQuery("#saving").val("");  
            }
            else
            {
                jQuery("#saving").val(Math.round(100*saving)/100);  
            }
        }
        
        if(saving!="" && saving!="NaN")
        {
            var discount=(parseFloat(saving)*100)/parseFloat(deal_value);
            if(isNaN(discount))
            {
                jQuery("#discount").val("");    
            }
            else
            {
                jQuery("#discount").val(Math.round(100*discount)/100);  
            }
        }
    }
    
    if(deal_value!="")
    {
        jQuery("#tr_discount").css("display","table-row");
        jQuery("#tr_saving").css("display","table-row");
        jQuery("#discount").focus();
    }
    else
    {
        jQuery("#tr_discount").css("display","none");
        jQuery("#tr_saving").css("display","none");
        jQuery("#deal_value").focus();
    }
}); 
*/
/*************** ****************************/
    jQuery(document).ready(function(){
  function add() {
    if(jQuery(this).val() === ''){
      jQuery(this).val(jQuery(this).attr('placeholder')).addClass('placeholder');
    }
  }

  function remove() {
    if(jQuery(this).val() === jQuery(this).attr('placeholder')){
      jQuery(this).val('').removeClass('placeholder');
    }
  }

  // Create a dummy element for feature detection
  if (!('placeholder' in jQuery('<input>')[0])) {

    // Select the elements that have a placeholder attribute
    jQuery('input[placeholder], textarea[placeholder]').blur(add).focus(remove).each(add);

    // Remove the placeholder text before the form is submitted
    jQuery('form').submit(function(){
      jQuery(this).find('input[placeholder], textarea[placeholder]').each(remove);
    });
  }
});
 jQuery('body').on("change","#billing_country",function () {

            var change_value=this.value;
            if(change_value == "USA")
            {
                jQuery(".fancybox-inner #billing_state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");
               
               
            }
            else
            {
                  jQuery(".fancybox-inner #billing_state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>"); 
                
            }
          //jQuery(this).attr("selected","selected");
        });
    
  </script>
<div class="validating_data" style="display:none;">Validating data, please wait...</div> 
</body>
</html>
