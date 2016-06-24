<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY . "/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("press_release", $array_where);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Admin Panel</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="<?= ASSETS_CSS ?>/a/template.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/jquery-1.7.2.min.js"></script>

        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/a/jquery.ui.core.js"></script>
        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/a/jquery.ui.datepicker.js"></script>
        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/a/jquery-ui-timepicker-addon.js"></script>
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/a/jquery.ui.datepicker.css">
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/a/jquery.ui.theme.css">
        <style>
            .defaultSkin table.mceLayout tr.mceFirst td
            {
                display:none;
            }
        </style>
        <script type="text/javascript" src="<?= ASSETS ?>/tinymce/tiny_mce.js"></script>
        <script>
                tinyMCE.init({
                    // General options
                    //mode : "textareas",
                    mode: "exact",
                    elements: "description",
                    theme: "advanced",
                    plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",
                    // Theme options
                    /*
                     theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                     theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                     theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                     theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
                     */
                    theme_advanced_buttons1: "",
                    theme_advanced_toolbar_location: "top",
                    theme_advanced_toolbar_align: "left",
                    theme_advanced_statusbar_location: "bottom",
                    theme_advanced_resizing: true,
                    // Example content CSS (should be your site CSS)
                    content_css: "<?= ASSETS ?>/tinymce/content.css",
                    // Drop lists for link/image/media/template dialogs
                    template_external_list_url: "<?= ASSETS ?>/tinymce/lists/template_list.js",
                    external_link_list_url: "<?= ASSETS ?>/tinymce/lists/link_list.js",
                    external_image_list_url: "<?= ASSETS ?>/tinymce/lists/image_list.js",
                    media_external_list_url: "<?= ASSETS ?>/tinymce/lists/media_list.js",
                    // Style formats
                    style_formats: [
                        {title: 'Bold text', inline: 'b'},
                        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                        {title: 'Example 1', inline: 'span', classes: 'example1'},
                        {title: 'Example 2', inline: 'span', classes: 'example2'},
                        {title: 'Table styles'},
                        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                    ],
                    // Replace values for the template plugin
                    template_replace_values: {
                        username: "Some User",
                        staffid: "991234"
                    }
                });
        </script>
        <style>
            .displayimg {
                max-height: 70px;
                max-width: 90px;
            }
            #upload {
                background: none repeat scroll 0 0 #F2F2F2;
                border: 1px solid #CCCCCC;
                border-radius: 5px 5px 5px 5px;
                color: #3366CC;
                cursor: pointer !important;
                font-family: Arial,Helvetica,sans-serif;
                font-size: 1.1em;
                font-weight: bold;
                height: 15px;
                padding: 6px;
                text-align: center;
                width: 60px;
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
                    <h2>Edit Press Release</h2>
                    <form action="process.php" method="post">

                        <table border="0" cellspacing="2" cellpadding="2" style="width:100%;">
                            <tr>
                                <td width="40%" align="right">Title</td>
                                <td width="60%" align="left">
                                    <input style ="width:inherit;" type="text" name="title" id="title" value="<?= $RS->fields['title'] ?>"/>
                                </td>
                            </tr>


                            <tr>
                                <td align="right">Description</td>
                                <td align="left">

                                    <textarea id="description" name="description" rows="15" cols="80" style="width: 80%"><?php echo $RS->fields['description'] ?></textarea>
                <!--<input type="hidden" name="hdndescription" id="hdndescription" value="" />-->
                                </td>
                            </tr>

                            <tr>
                                <td align="right">Status</td>
                                <td align="left">
                                    <select name="status" id="status">
                                        <option value="1" <?php if ($RS->fields['status'] == 1) echo "selected"; ?> >Active</option>
                                        <option value="0" <?php if ($RS->fields['status'] == 0) echo "selected"; ?> >Deactive</option>
                                    </select>

                                </td>
                            </tr>

                            <tr>                                  
                                <td align="right">Date: </td></td>
                                <td align="left">
                                    <input type="text" name="release_date" class="datetimepicker_sd" id="release_date" style="width:100px; " value="<?= $RS->fields['release_date'] ?>">

                                </td>
                            </tr>

                            <tr>
                                <td>&nbsp;</td>
                                <td align="left">
                                    <input type="submit" name="btnEditPressrelease" value="Save" >
                                    <input type="hidden" name="hdn_id" id="hdn_id" value="<?= $_REQUEST['id'] ?>" >
                                    <!--// 369-->
                                    <input type="submit" name="btnCancelPressrelease"  value="Cancel" >
                                    <!--// 369-->
                                </td>
                            </tr>

                        </table>
                    </form>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <!--end of Container--></div>

        <script>


        </script>
        <?
        $_SESSION['msg'] = "";
        ?>

    </body>
</html>
<script>
        jQuery.noConflict();
        jQuery(document).ready(function () {
            jQuery("#release_date").datepicker();
        });
</script>