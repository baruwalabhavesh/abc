<?php
check_merchant_session();
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>ScanFlip | Manage Price lists</title>
		<?php require_once(MRCH_LAYOUT."/head.php"); ?>
		<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap-tagsinput.css">
		
		<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/pricelist.css">

		<!--<script src="<?php echo ASSETS ?>/pricelist/js/jquery.js"></script>-->
		<script src="<?php echo ASSETS ?>/pricelist/js/bootstrap.min.js"></script>
		<script src="<?php echo ASSETS ?>/pricelist/js/jquery-ui.js"></script>
		<script src="<?php echo ASSETS ?>/pricelist/js/bootstrap-tagsinput.js"></script>
		<!--<script src="js/service_pricelist.js"></script>
		<script src="js/service_json_generator.js"></script>
		<script src="js/service_preview_generator.js"></script>-->
		
		<script src="<?php echo ASSETS_JS ?>/m/he.js"></script>
	</head>
<body>
	<!---------start header--------------->	
	<div>
	<?
	// include header file from merchant/template directory 
	require_once(MRCH_LAYOUT."/header.php");
	?>
	<!--end header-->
	<link rel="stylesheet" type="text/css" href="<?php echo ASSETS ?>/pricelist/css/jquery-ui.css">
	</div>
	<div id="contentContainer">
		
	<div id="content">
  	<div class="container">
      <div class="clearfix">
        <div id="notification" >Saved successfully</div>
    		<h3 class="pricelist_name fleft">
          <?php

            if(isset($_POST['pricelist_val'])){

              $pricelist_name = $_POST['pricelist_val'];
              echo $pricelist_name;

            }
            else{
              header("Location: pricelists.php");
            }
        
          ?>
        </h3>  
        <button type="button" class="btn fleft btn-primary" style="margin:20px;" id="edit_pricelist_name">Edit Title</button>
      </div>
       <div class="error_messages">
	         <div class="alert alert-warning alert_pricelist_name">Please enter a price list name</div>
			<div class="alert alert-warning alert_html" style="">No HTML allowed</div>
			<div class="alert alert-warning alert_length" style="">Maximum Length should be 50 character</div>
		</div>
      <div id="edit_pricelist_form" style="display:none;"  >
      <form >
          <p class="form_label">Price list name</p>
          <input type="text" name="pricelist_val" id="pricelist_name" class="form-control">
          <p class="hint"><span data-max='50'>50</span> characters remaining | No HTML allowed</p>
          <button type="button" class="btn btn-primary" id="update_pricelist_name">Update</button>
          <button type="button" class="btn btn-primary" id="close_pricelist_form">Cancel</button>
        </form>
       </div>
      <button type="button" class="btn btn-primary" id="preview_button">Preview</button>
      <button type="button" class="btn btn-primary" id="saves">Save</button>
      <button type="button" class="btn btn-primary" id="close_pricelist" onclick="close_confirm('saves')">Close</button>
      <button type="button" class="btn btn-primary" id="cancel_pricelist" onclick="document.location.href='pricelists.php'">Cancel</button>
      <div class="pricelist_container">
        <button class="btn btn-primary add_category">Add Category</button>

        <div class="error_messages">
          <div class="alert alert-warning alert_category_name" style="">Please enter a Category Name</div>
          <div class="alert alert-warning alert_choicecategory_name" style="">Please enter a choice category name</div>
          <div class="alert alert-warning alert_html" style="">No HTML allowed</div>
          <div class="alert alert-warning alert_choicecategory_choice" style="">Please enter atleast one choice</div>
          <div class="alert alert-warning alert_choice_name" style="">Please enter a choice name</div>
          <div class="alert alert-warning alert_duration">Duration can be only number</div>
        </div>
        <div class="choicecategory_form" style="display: none;">
          <p>Add a choice category name</p>
          <input class="choicecategory_name form-control" type="text" maxlength="50">
          <p class="hint"><span data-max="50">50</span> characters remaining | No HTML allowed | Required </p>
          <p for="">Add Choices</p>
          <input class="choice_container bootstrap-tagsinput" data-role="tagsinput"/>
          <p class="hint">Press enter to add choices | Required field </p>
          <div class="choicecontainer_buttons">
            <button class="btn btn-primary  add_choicecategory_submit">Add</button>
             <button class="btn btn-primary cancel">Cancel</button>
          </div>
        </div>
        <div class="category_form" style="display: none;">
          <label>Category name</label>
          <input class="categoryname form-control" maxlength="50" type="text">
          <p class="hint"  ><span data-max="50">50</span> characters remaining | No HTML allowed | Required </p>
          <div class="checkbox ">
            <label>
              <input type="checkbox" class="hide_category" value=""> Hide category
            </label>
          </div>
          <button class="btn btn-primary  add_category_submit">Add</button>
           <button class="btn btn-primary cancel">Cancel</button>
        </div>
        <div class="choicecategories_group">
        </div>
        <div class="categories_group ui-sortable">
        </div>

      </div>
    </div>
    <form action="savedata.php" method="post" id="post_json">
      <input id="json_input" type="hidden" name="json">
      <input type="hidden" name="pricelist_name_val" id="pricelist_name_val">
      <input type="hidden" name="pricelist_id" value="-1" id="pricelist_id">
      <input type="hidden" name="pricelist_type" value="service" id="pricelist_type">
    </form>
    <div class="close_dialog  hidden_div">
      <p>Do you want to save price list before you save.</p>
      <button class="btn btn-primary decline">No</button>
      <button class="btn btn-primary accept">yes</button>
    </div>
    </div>
    <div id="product_preveiw">
      <div class="collapsible_sections">
      </div>
	</div><!-- end of content-->
	</div><!-- end of contentContainer-->
	
	<!---------start footer--------------->
    <div>
	<?
		require_once(MRCH_LAYOUT."/footer.php");
	?>
	<!--end of footer-->
	</div>
  </body>
</html>
<script type="text/javascript">
// service_preview_generator.js
$(document).ready(function(){
var numeric = /^\d+$/;
$('body').on('click','.collapsible_section_header',function(){
  $(this).next('.collapsible_section_body').toggle(300);
  $(this).find('i').toggleClass('preview_collapse_icon');
  $(this).find('i').toggleClass('preview_expand_icon');
})

  var pname,pid,pricelist_type,pricelist_name,json_string;
  
  function generate_preview_service(){
     pricelist_name = $('.pricelist_name').html();
    //console.log(pricelist_name)
    preview_dom_elements = "";
    preview_dom_elements += '<div class="preview_pricelist_name">'+pricelist_name+'</div>';
    pricelist_name = trimming(pricelist_name);
    console.log(pricelist_name)

    var choicecategory_id = 0;
    var category_count = $('.category_container').length;
    var choice_category_count = $('.choicecategory_box').length;

    //checking if categories exist
    if(category_count > 0){

      //looping through categories and adding it to dom
      for(i=0; i<category_count;i++){
        
        var category_name_val= $('.category_container').eq(i).find('.category_name_val').html();
        category_name_val=trimming(category_name_val);
        var hide_category= $('.category_container').eq(i).find('.hide_category').attr('value');
        if(hide_category != "true"){

          console.log(hide_category)
          preview_dom_elements += '<div class="collapsible_section"> <div class="collapsible_section_header  clearfix">\
            <p class="fleft">'+category_name_val+'</p>\
            <i class="fright preview_expand_icon"></i>\
            </div>\
            <div class="collapsible_section_body">';

         
          var category_notes_count = $('.category_container').eq(i).find('.category_notes_container').find('.note').length;
          if(category_notes_count>0){

            //for(p=0;p<category_notes_count;p++){
              var note_val= $('.category_container').eq(i).find('.category_notes_container').find('.note').find('.note_description_val').html();
              note_val=trimming(note_val);
              preview_dom_elements += '<div class="preview_note">'+note_val+'</div>\
              <div class="item_contents">\
              <ul>';

          }    

          var category_item_count = $('.category_item_container').eq(i).find('.item').length;
          //checking if items exist
          if(category_item_count>0){
            //looping through items and adding it to dom
            for(k=0;k<category_item_count;k++){
              var item_name_val= $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.item_name_val').html();
              item_name_val=trimming(item_name_val);
              var item_description_val= $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.item_description_val').html();
              item_description_val=trimming(item_description_val);
              var item_price_val =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.item_price_val').html();
              item_price_val=trimming(item_price_val);
              var hide_item_price =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.hide_item_price').attr('value');
              var hide_item_duration =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.hide_item_duration').attr('value');
              var item_duration =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.item_duration').html();
              var item_node;
              console.log(hide_item_price)
               //if(hide_item_price){
			   if(hide_item_price=="true" || hide_item_price==true)
			   {
                item_node ="";
               }
               else{
                if(numeric.test(item_price_val)){
                   item_price_val += '.00'; 
                }
                item_node='<p class="item_price fright">'+item_price_val+'</p>';
               }
              var choicecategory_id =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.choice_category_id').attr('value');
              //adding it to dom
              preview_dom_elements += '<li class="clearfix">\
                  <div class="iteminfo clearfix fleft">\
                    '+item_node+'\
                    <p class="item_title fleft">'+item_name_val+'</p>';
               console.log("hide_item_duration : "+hide_item_duration);
               //if(hide_item_duration | hide_item_duration=="true")
               if(hide_item_duration=="true" || hide_item_duration==true)
               {
				//alert("true");
				console.log("if hide_item_duration");
               }
               else
               {
				//alert("false");
				console.log("else hide_item_duration");
				preview_dom_elements += '<p class="item_duration fleft">'+item_duration+' mins</p>';
               }      
              
               preview_dom_elements += '<p class="item_description">'+item_description_val+'</p>\
                  </div>\
                  <div class="plus_for_item fleft"></div>\
                  <div class="options">';      
            }
          }
          preview_dom_elements+=  '</li></ul></div></div></div>';
          console.log(preview_dom_elements)
        }
      }
    }
      $('.collapsible_sections').html(preview_dom_elements);
      $('.collapsible_sections').dialog({ minWidth:800, modal:true})






        pname = $('#pricelist_name_val').val();
        pid = $('#pricelist_id').attr('value');
        pricelist_type = $('#pricelist_type').attr('value');

        //console.log(pricelist_type);
       //  console.log(pname)
      //$('#post_json').submit();   
  }

  $('body').on('click','#preview_button',function(){
    generate_preview_service();
  })
})

function trimming(input) {
    var str = input;
  str = str.replace(/\s{2,}/g, ' ').trim();
  return str;
  console.log(str);
}

// service_json_generator.js
$(document).ready(function(){
	var pname,pid,pricelist_type,pricelist_name,json_string;

	//json generator
	function generate_json(){
		 pricelist_name = $('.pricelist_name').html();
		//console.log(pricelist_name)
		pricelist_name = pricelist_name.replace(/\s{2,}/g, ' ').trim();

		 json_string="";
		var choicecategory_id = 0;
		pricelist_name = pricelist_name.replace(/"/g, '\\dq');
		json_string= json_string + '{ "pricelist_name":"' + pricelist_name+'"';
		var category_count = $('.category_container').length;
		//check if category exist
		if(category_count > 0){

		
			json_string = json_string + ',"category": ['
			//looping through category
			for(i=0; i<category_count;i++){
				
				var category_name_val= $('.category_container').eq(i).find('.category_name_val').html();
				var hide_category= $('.category_container').eq(i).find('.hide_category').attr('value');
				category_name_val = category_name_val.replace(/"/g, '\\dq');
				json_string = json_string + '{"category_name": "'+category_name_val+'",';
				json_string = json_string + '"hide_category": "'+hide_category+'"';

				var category_item_count = $('.category_item_container').eq(i).find('.item').length;

				
				//checking item count
				if(category_item_count>0){
					json_string= json_string + ',"services": ['
					for(k=0;k<category_item_count;k++){
						var item_name_val= $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.item_name_val').html();
						var item_description_val= $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.item_description_val').html();
						var item_price_val =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.item_price_val').html();
						var hide_item_price =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.hide_item_price').attr('value');
						var hide_item_duration =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.hide_item_duration').attr('value');
						var item_duration =  $('.category_container').eq(i).find('.category_item_container').find('.item').eq(k).find('.item_duration').html();

						console.log(item_duration)
						item_name_val = item_name_val.replace(/"/g, '\\dq');
						item_description_val = item_description_val.replace(/"/g, '\\dq');

						json_string = json_string + '{"service_name": "'+item_name_val+'",';
						json_string = json_string + '"service_description": "'+item_description_val+'",';
						json_string = json_string + '"service_price": "'+item_price_val+'",';
						json_string = json_string + '"hide_service_price": "'+hide_item_price+'",';
						json_string = json_string + '"hide_service_duration": "'+hide_item_duration+'",';
						json_string = json_string + '"service_duration": "'+item_duration+'"}';

						if(k< category_item_count-1 ){
							json_string = json_string + ','			
						}
						
					
					}
					json_string = json_string + ']'	
				}
				
				//adding note to json
				var category_notes_count = $('.category_container').eq(i).find('.category_notes_container').find('.note').length;
				if(category_notes_count>0){
					json_string= json_string + ',"note": "'
					//for(p=0;p<category_notes_count;p++){
						var note_val= $('.category_container').eq(i).find('.category_notes_container').find('.note').find('.note_description_val').html();
						note_val = note_val.replace(/"/g, '\\dq');
						json_string= json_string +note_val+'"';
						//if(p<category_notes_count-1){
						//	json_string = json_string + ',';	
						//}
					//}
					//json_string = json_string + ']'	
				}			
				json_string=json_string+'}'
				if(i<category_count-1){
					json_string = json_string + ',';
				}
					
			}
			json_string = json_string + ']'
		}
			json_string = json_string + '}'
			//console.log(json_string);

			$('#json_input').val(json_string)

			$('#pricelist_name_val').val(pricelist_name);
			pname = $('#pricelist_name_val').val();
			pid = $('#pricelist_id').attr('value');
			pricelist_type = $('#pricelist_type').attr('value');

			  //console.log(pricelist_type);
			 //  console.log(pname)


			//$('#post_json').submit();

		
			
	}

	//sending json to process and save
	jQuery('#saves').click(function(){
		
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
				}
				else
				{
					
					generate_json();
					json_string = he.encode(json_string);
					console.log(json_string);
					//return false;
					
					jQuery.ajax({
						type: "POST", // HTTP method POST or GET
						url: "savedata.php", //Where to make Ajax calls
						dataType:"text", // Data type, HTML, json etc.
						data:{json : json_string,name: pname,id: pid , pricelist_type: pricelist_type}, //Form variables
						success:function(response){
							console.log(response);
							jQuery('#pricelist_id').attr('value',response);
							jQuery('#notification').fadeIn(500);
							setTimeout(function(){jQuery('#notification').fadeOut(500); }, 3000);
						},
						error:function (xhr, ajaxOptions, thrownError){
							//alert(thrownError);
						}
					})
        
				}
			}
		});
	})

	//sending json to update
	jQuery('#update').click(function(){
		
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
				}
				else
				{
					
					generate_json();
					json_string = he.encode(json_string);
					console.log(json_string);
					
					jQuery.ajax({
						type: "POST", // HTTP method POST or GET
						url: "updatedata.php", //Where to make Ajax calls
						dataType:"text", // Data type, HTML, json etc.
						data:{json : json_string,name: pname,id: pid , pricelist_type: pricelist_type}, //Form variables
						success:function(response){
							console.log(response);
							if(response == "failed"){
								alert('pricelist doesn\'t exist');
								window.history.go(-1);
							}
							jQuery('#notification').fadeIn(500);
							setTimeout(function(){jQuery('#notification').fadeOut(500); }, 3000);
						},
						error:function (xhr, ajaxOptions, thrownError){
							//alert(thrownError);
						}
					})
        
				}
			}
		});
	})
	
	words_remaining("#pricelist_name")
    //function to count remaining words
    function words_remaining(input){
      $('body').on('input',input,function(){
        var len = $(this).val().length;
        var max = $(this).next().find('span').attr('data-max');
        $(this).next().find('span').html(max-len);
      })
    }
})

// service_pricelist.js

var price_length= "Price can be numbers and maximum length is 7 characters";
var max_length_50 = "<span data-max='50'>50</span> characters remaining | No HTML allowed ";
var max_length_50_required = "<span data-max='50'>50</span> characters remaining | No HTML allowed | Required ";
var max_length_70_required = "<span data-max='70'>70</span> characters remaining | No HTML allowed | Required ";
var max_length_255_required = "<span data-max='255'>255</span> characters remaining | No HTML allowed | Required ";
var max_length_1200_required = "<span data-max='1200'>1200</span> characters remaining | No HTML allowed | Required ";
var max_length_70 = "<span data-max='70'>70</span> characters remaining | No HTML allowed ";
var max_length_255 = "<span data-max='255'>255</span> characters remaining | No HTML allowed ";
var max_length_1200 = "<span data-max='1200'>1200</span> characters remaining | No HTML allowed ";
var duration_hint = "Numeric | Max length 4 | No special characters ";

var price_hint = "numeric, no currency symbol";
//var html_regex=/[<>?,.\/]/;
var html_regex=/[<>?]/;
var numeric = /^\d+$/;
 var regex = /^[0-9]*(?:\.\d{1,2})?$/;
 var choicecategory_id = 0;
 var choicecategory_message = "Press enter to add choices | Required field";

$(document).ready(function(){



function form_submit_on_enter(key,target){

  $('body').on('keypress','.'+key,function(e){
    var key= e.which;
    if(key==13){
    $(this).siblings('.'+target).trigger('click');
    }
  })
}
form_submit_on_enter('categoryname','add_category_submit');
form_submit_on_enter('categoryname','update_category_submit');
form_submit_on_enter('item_name','add_item_submit');
form_submit_on_enter('item_name','update_item_submit');
form_submit_on_enter('notes_description','add_notes_submit');
form_submit_on_enter('notes_description','update_notes_submit');
form_submit_on_enter('option_name','option_submit');
form_submit_on_enter('option_price','update_option_submit');



    /*reveal the form to add a category*/
    $('body').on("click",'.add_category',function(){
      $('.category_form').show(500);
      $('.category_form').find('.categoryname').focus();
    })
  /*cancel button click event to hide the category form*/
    $('body').on('click','.category_form .cancel',function(){
       $(this).parent().siblings('.error_messages').find('.alert_category_name').hide(500);
      $(this).closest('.category_form').hide(500);
      $('.category_form').find('input').val("");
    })
    /*add a category button click event*/
  $('body').on('click','.add_category_submit',function(){
    if($('.category_form').find('input').val().length == 0 || html_regex.test($('.category_form').find('input').val())){/* checks if the form is filled*/
      if($('.category_form').find('input').val().length == 0){
        $(this).parent().siblings('.error_messages').find('.alert_category_name').show(500);
      }
      if( html_regex.test($('.category_form').find('input').val())){
       $(this).parent().siblings('.error_messages').find('.alert_html').show(500);
      }
    }

    else{
      $(this).parent().siblings('.error_messages').find('.alert_html').hide(500);
      $(this).parent().siblings('.error_messages').find('.alert_category_name').hide(500);
      var category_name= $('.category_form').find('.categoryname').val();
      var hide_category;
      if($(this).parent().find('input.hide_category').is(':checked') ) hide_category= "true"; else hide_category= "false";
      console.log('hide:'+ hide_category);
        /*add a category to dom with other forms for adding sub category , item notes*/
      $('.category_form').hide(); 
      $('.category_form').find('input').val("");
      $('.pricelist_container').find('.categories_group').prepend('<div class="category_container">\
        <div class="category_nav clearfix">\
          <span class="glyphicon glyphicon-plus fleft collapse_icon category_expand"></span>\
          <span class="glyphicon glyphicon-minus fleft collapse_icon category_collapse"></span>\
          <div class="category_details" >\
            <h4 class="category_name_val" >'+category_name+'</h4>\
            <div class="hide_category hidden_div" value='+hide_category+'> </div>\
          </div>\
          <button class="btn btn-primary edit_category popover-markup" >Edit</button>\
          <button class="btn btn-primary add_item_category">+Service</button>\
          <button class="btn btn-primary add_notes_category">+Note</button>\
          <span class="glyphicon glyphicon-arrow-up category_up_arrow"></span>\
          <span class="glyphicon glyphicon-arrow-down category_down_arrow"></span>\
          <span class="glyphicon glyphicon-trash delete_parent"></span>\
        </div>  \
        <div class="error_messages">\
          <div class="alert alert-warning alert_category_name">Please enter a Category Name</div>\
          <div class="alert alert-warning alert_subcategory_name">Please enter a Subcategory Name</div>\
          <div class="alert alert-warning alert_html" style="">No HTML allowed</div>\
          <div class="alert alert-warning alert_notes">Please enter a Note</div>\
          <div class="alert alert-warning alert_item_name">Please enter Item name</div>\
          <div class="alert alert-warning alert_item_price">'+price_length+'</div>\
          <div class="alert alert-warning alert_duration">Duration can be only number</div>\
          <div class="alert alert-warning alert_duration_length">Max length 4</div>\
        </div>\
        <div class="hidden_div edit_category_form">\
          <input class="categoryname form-control" type="text" maxlength="50">\
          <p class="hint">'+max_length_50_required+'</p>\
          <div class="checkbox ">\
            <label>\
              <input type="checkbox" class="hide_category" > Hide category\
            </label>\
          </div>\
          <button  class="btn btn-primary  update_category_submit" >Update</button>\
          <button  class="btn btn-primary cancel"   >Cancel</button>\
        </div>\
        <div class="master_container">\
          <div class="subcategory_form" >\
            <label for="">Subcategory name</label>\
            <input class="subcategory_name form-control" type="text" maxlength="70">\
            <p class="hint">'+max_length_70_required+'</p>\
            <button  class="btn btn-primary  add_subcategory_submit" >Add</button>\
            <button  class="cancel btn btn-primary">Cancel</button>\
          </div>\
          <div class="category_item_form" >\
            <p>Service name</p>\
            <input class="item_name form-control" type="text" maxlength="70">\
            <p class="hint">'+max_length_70_required+'</p>\
            <label class="nobold">Service duration</label>\
            <input type="text" class="duration form-control" placeholder="00" />\
            <label class="nobold">Minutes</label>\
            <div class="hint mrtop4-20">'+duration_hint+'</div>\
             <div class="checkbox">\
                  <label>\
                    <input type="checkbox" class="hide_item_duration"> Hide Duration\
                  </label>\
                </div>\
            <p>Description</p>\
            <textarea class="item_description  form-control" placeholder="Optional" name="" id="" cols="30" maxlength="1200" rows="10"></textarea>\
            <p class="hint">'+max_length_1200+'</p>\
            <p>price</p>\
            <input type="text" class="item_price form-control" placeholder="0.00"  maxlength="7">\
            <p class="hint">'+price_hint+' </p>\
            <div class="checkbox">\
              <label>\
                <input type="checkbox" class="hide_item_price"> Hide price\
              </label>\
            </div>\
            <button  class="btn btn-primary add_item_submit" >Add</button>\
            <button  class="cancel btn btn-primary">Cancel</button>\
          </div>\
          <div class="category_notes_form">\
            <label for="textarea"><h4>Add notes</h4></label>\
            <textarea class="notes_description form-control" maxlength="255"></textarea>\
            <p class="hint">'+max_length_255_required+'</p>\
            <button  class="add_notes_submit btn btn-primary">Add</button>\
            <button  class="cancel btn btn-primary">Cancel</button>\
          </div>\
          <ul class="category_notes_container"></ul>\
          <ul class="category_item_container"></ul>\
          <div class="subcategory_container clearfix">\
          </div>\
        </div>');
      //call sortable ui event on categories
      $('.categories_group').sortable();

      
    }
    delete_parent();


  });

//function to add a choice category

  


  note('category');
  item('category'); 
  edit_category();
  edit_note('category')
  edit_item();
  collapse("category");
  arrows('category','category_container');
  arrows('option','option');
  arrows('item','item')
  collapse_item();
  words_remaining('.categoryname');
  words_remaining('.item_name');
  words_remaining('.item_description');
  words_remaining('.notes_description');
})
//document.ready endss
//add item for cateogory and subcategory
   
function item(parent){
  //button event for + item button in category and subcategory
  $('body').on('click', '.add_item_'+parent, function(){
    var choicecategory_length = $('.choicecategory_box').length;

    var form = $(this).parent().parent().find('.'+parent+'_item_form');
    form.find('.item_name').val("");
    form.find('.item_description').val("");
    form.find('.item_price').val("");
    form.find('.duration').val("");
    form.show(500);
    form.find('.item_name').focus();
    form.find('input.hide_item_price').is(':checked',false)
    form.find('input.hide_item_duration').is(':checked',false)

  })
  //cancel button event
  $('body').on('click','.'+parent+'_item_form  .cancel',function(){
      $(this).parent().hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_name').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_price').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration_length').hide(500); 
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration').hide(500);
    })
  //item form submit event
  $('body').on('click','.'+parent+'_item_form .add_item_submit',function(){
    var item_duration =$(this).parent().find('.duration').val();
    var regex = /^\d{0,8}(\.\d{1,4})?$/
    if($(this).parent().find('input').val().length == 0  ||  !regex.test($(this).parent().find('.item_price').val()) || html_regex.test($(this).parent().find('.item_name').val()) || !numeric.test(item_duration) || item_duration.length > 4 ){
      if($(this).parent().find('input').val().length == 0){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_name').show(500);
      }
      if(!regex.test($(this).parent().find('.item_price').val())){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_price').show(500);
      }
      if (html_regex.test($(this).parent().find('.item_name').val())) {
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').show(500);
      };
      if(!numeric.test($(this).parent().find('.duration').val())){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration').show(500);
      }
       if(item_duration.length > 4){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration_length').show(500); 
       }

    }

    else{
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration_length').hide(500); 
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_name').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_price').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').hide(500);
      var item_name= $(this).parent().find('.item_name').val();
      var item_description= $(this).parent().find('.item_description').val();
      var item_price=$(this).parent().find('.item_price').val();  
      var display_item_price= $(this).parent().find('input.hide_item_price').is(':checked');
      var display_item_duration= $(this).parent().find('input.hide_item_duration').is(':checked');
      console.log('hide item price:'+display_item_price)
      var selected_choicecategory_id=$(this).closest('.'+parent+'_item_form').find('.select_choice').val();
      var item_duration = $(this).closest('.'+parent+'_item_form').find('.duration').val();
      $('.'+parent+'_item_form').hide();

      //prepending item to item container
      $(this).parent().parent().find('.'+parent+'_item_container').prepend('<li class="item clearfix">\
          <div class="item_nav clearfix">\
            <div class="item_details fleft">\
              <span class="glyphicon glyphicon-plus fleft item_expand"></span>\
              <span class="glyphicon glyphicon-minus fleft item_collapse"></span>\
              <div class="item_bar" >\
              <p class="item_name_val fleft ">'+item_name+'</p>\
              <p class="item_price_val mr20 fleft">'+item_price+'</p>\
              </div>\
              <p class="item_description_val">  '+item_description+'</p>\
              <p class="hide_item_price hidden_div" value="'+display_item_price+'"> </p>\
              <p class="hide_item_duration hidden_div" value="'+display_item_duration+'"> </p>\
              <p class="item_duration hidden_div form-control">'+item_duration+'</p>\
            </div>\
            <div class="fleft clearfix item_buttons">\
              <button class="btn btn-primary edit_item popover-markup" >Edit</button>\
              <span class="glyphicon glyphicon-arrow-up item_up_arrow"></span>\
              <span class="glyphicon glyphicon-arrow-down item_down_arrow"></span>\
              <span class="glyphicon glyphicon-trash delete_item"></span>\
            </div>\
          </div>\
          <div class="error_messages">\
              <div class="alert alert-warning alert_item_name">Please enter Item name</div>\
              <div class="alert alert-warning alert_item_price">Price can not be other than numbers</div>\
              <div class="alert alert-warning alert_option_name">Please enter option name</div>\
              <div class="alert alert-warning alert_option_price">Price can not be other than numbers</div>\
              <div class="alert alert-warning alert_html" style="">No HTML allowed</div>\
              <div class="alert alert-warning alert_duration">Duration can be only number</div>\
              <div class="alert alert-warning alert_duration_length">Max length 4</div>\
          </div>\
          <div class="master_container">\
            <div class="edit_item_form" >\
              <p>Service name</p>\
              <input class="item_name form-control" type="text" maxlength="50">\
              <p class="hint">'+max_length_50_required+'</p>\
              <label class="nobold">Service duration</label>\
             <input type="text" class="duration form-control" placeholder="00" />\
             <label class="nobold">Minutes</label>\
             <div class="hint mrtop4-20">'+duration_hint+'</div>\
              <div class="checkbox">\
                  <label>\
                    <input type="checkbox" class="hide_item_duration"> Hide Duration\
                  </label>\
                </div>\
              <p>Description</p>\
              <textarea class="item_description  form-control" placeholder="Optional" name="" id="" cols="30" rows="10" maxlength="1200">'+item_description+'</textarea>\
              <p class="hint">'+max_length_1200+'</p>\
              <p>price</p>\
              <input type="text" class="item_price form-control" maxlength="7">\
              <p class="hint">'+price_hint+'</p>\
              <div class="checkbox">\
                <label>\
                  <input type="checkbox" class="hide_item_price"> Hide price\
                </label>\
              </div>\
              <button  class="btn btn-primary update_item_submit" >Update</button>\
              <button  class="cancel btn btn-primary">Cancel</button>\
            </div>\
          </div>\
        </li>');
    
      
      $('.subcategory_container').sortable();//makes subcategory container sortable
      $('.'+parent+'_item_container').sortable();
    }
  })  
  delete_parent();
  delete_item();
}

//function to add an option to items


/*function arrows(){
  $('body').on('click','.up_arrow',function(){
    //var element_count= $(this).parent().closest('.item').length;
    console.log('clicked')
    if( $(this).closest('.item').index() ==0){
      alert('x')
    }
  })
}*/


//function to handle edit item form
function edit_item(){
  //button event to show edit item form , focus on it
  $('body').on('click','.edit_item',function(){
      var selected_choicecategory_id= $(this).closest('.item').find('.choice_category_id').attr('value');
    $(this).closest('.item').find('.item_name').val($(this).closest('.item').find('.item_name_val').html());
    $(this).closest('.item').find('.item_price').val($(this).closest('.item').find('.item_price_val').html());
    $(this).closest('.item').find('.duration').val($(this).closest('.item').find('.item_duration').html());
    
     if($(this).closest('.item').find(' p.hide_item_duration').attr('value')=='true' || $(this).closest('.item').find(' p.hide_item_duration').attr('value')==true){
    $(this).closest('.item').find('input.hide_item_duration').prop('checked',true);
      console.log('true')
    }
    else{
      $(this).closest('.item').find('input.hide_item_duration').prop('checked',false);
    }
    if($(this).closest('.item').find(' p.hide_item_price').attr('value')=='true' || $(this).closest('.item').find(' p.hide_item_price').attr('value')==true){
    $(this).closest('.item').find('input.hide_item_price').prop('checked',true);
      console.log('true')
    }
    else{
      $(this).closest('.item').find('input.hide_item_price').prop('checked',false);
    }
    //console.log($(this).closest('.item').find(' p.hide_item_price').attr('value'))
    $(this).closest('.item').find('.item_description').val( $(this).closest('.item').find('.item_description_val').html());
    $(this).closest('.item_nav').siblings('.master_container').find('.edit_item_form').show(500);
    $(this).closest('.item_nav').siblings('.master_container').find('item_name').focus();
  })  
  //button event to cancel edit and hide to form
  $('body').on('click','.edit_item_form .cancel',function(){
    $(this).closest('.edit_item_form').hide(500);
    $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_name').hide(500);
    $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_price').hide(500);
    $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration_length').hide(500); 
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration').hide(500); 
  })
  //event on submit edit form
  $('body').on('click','.update_item_submit',function(){
    var item_name = $(this).siblings('.item_name').val();
    var item_description = $(this).siblings('.item_description').val();
    var item_price = $(this).siblings('.item_price').val();
    var hide_item_price = $(this).siblings().find('.hide_item_price').is(':checked');
    var hide_item_duration = $(this).siblings().find('.hide_item_duration').is(':checked');
    var item_duration = $(this).siblings('.duration').val();
    console.log('hide_item_price'+hide_item_price)
    var regex = /^[0-9]*(?:\.\d{1,2})?$/;
    if($(this).parent().find('input').val().length == 0  ||  !regex.test($(this).parent().find('.item_price').val()) || html_regex.test($(this).parent().find('input').val()) || !numeric.test(item_duration) || item_duration.length > 4 ){
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_name').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_price').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration_length').hide(500);
      if($(this).parent().find('input').val().length == 0){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_name').show(500);
      }
      if(!regex.test($(this).parent().find('.item_price').val())){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_price').show(500);
      }
      if(html_regex.test($(this).parent().find('input').val())){
       $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').show(500); 
      }
      if(!numeric.test(item_duration)){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration').show(500); 
      }
      if(item_duration.length > 4){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration_length').show(500); 
      }
    }
    else{
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration_length').hide(500); 
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_duration').hide(500); 
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_name').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_item_price').hide(500);
      $(this).closest('.master_container').siblings('.item_nav').find('.item_name_val').html(item_name);
      $(this).closest('.master_container').siblings('.item_nav').find('.item_description_val').html(item_description);
      $(this).closest('.master_container').siblings('.item_nav').find('.item_price_val').html(item_price);
      $(this).closest('.master_container').siblings('.item_nav').find('.item_duration').html(item_duration);

      //$(this).closest('.master_container').siblings('.item_nav').find('.hide_item_price').attr('value',hide_item_price);
      $(this).closest('.item').find(' p.hide_item_price').attr('value',hide_item_price);
      $(this).closest('.item').find(' p.hide_item_duration').attr('value',hide_item_duration);
      
      $(this).parent().hide(500);
    }
  })
}
//function to edit category
function edit_category(){
  //event on edit category button
  $('body').on('click','.edit_category',function(){
  $(this).parent().parent().find('.categoryname').val( $(this).closest('.category_container').find('.category_name_val').html());
  if($(this).closest('.category_container').find('div.hide_category').attr('value')=='true'){
    $(this).closest('.category_container').find('input.hide_category').prop('checked',true);
  }
  else{
    $(this).closest('category_container').find('input.hide_category').prop('checked',false);
  }
    $(this).parent().parent().find('.edit_category_form').show(500);
    $(this).parent().parent().find('.categoryname').focus();
  })
  //event on cancel button
  $('body').on('click','.edit_category_form .cancel',function(){
    $(this).parent().siblings('.error_messages').find('.alert_category_name').hide(500);
    $(this).parent('.edit_category_form').hide(500);
  })
  //event on submit update category
  $('body').on('click','.update_category_submit',function(){
    var category_name = $(this).siblings('.categoryname').val();
    var hide_category ;
    if($(this).siblings('.categoryname').val().length == 0 || html_regex.test($(this).siblings('.categoryname').val())){
      //validation
      $(this).parent().siblings('.error_messages').find('.alert_category_name').hide(500);
      $(this).parent().siblings('.error_messages').find('.alert_category_name').hide(500);
      if($(this).siblings('.categoryname').val().length ==0){
        $(this).parent().siblings('.error_messages').find('.alert_category_name').show(500);
      }
      if(html_regex.test($(this).siblings('.categoryname').val())){
        $(this).parent().siblings('.error_messages').find('.alert_html').show(500);

      }
      
    }
    else{
      $(this).parent().siblings('.error_messages').find('.alert_category_name').hide(500);
      $(this).parent().siblings('.error_messages').find('.alert_html').hide(500);
      if($(this).closest('.category_container').find('input.hide_category').is(':checked') ) hide_category= "true"; else hide_category= "false";
      $(this).closest('.category_container').find('.category_name_val').html(category_name);
      $(this).closest('.category_container').find('.hide_category').attr('value',hide_category);
      $(this).parent('.edit_category_form').hide(300);

    }
  })
}


//edit function for subcategory.
//add a note function.
function note(parent)
{
  //add notes button event for both category and sub category.
  //parent can be category or subcategory.
  //unhides the form.
  $('body').on('click', '.add_notes_'+parent, function() {
      $(this).parent().parent().find('.notes_description').val("");
    $(this).parent().parent().find('.'+parent+'_notes_form').show(500);
    $(this).parent().parent().find('.notes_description').focus();
  });
  //cancel button event for note.
  $('body').on('click','.'+parent+'_notes_form  .cancel',function(){
      $(this).parent().hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_notes').hide(500);
    })
  //submit button for note.
  $('body').on('click', '.'+parent+'_notes_form .add_notes_submit', function() {
    if($(this).parent().find('.notes_description').val().length == 0 || html_regex.test($(this).parent().find('.notes_description').val())){
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_notes').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').hide(500);
      if($(this).parent().find('.notes_description').val().length==0){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_notes').show(500);
      }
      if(html_regex.test($(this).parent().find('.notes_description').val())){
        $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').show(500);
      }
    }
    else{
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_notes').hide(500);
      $(this).closest('.master_container').siblings('.error_messages').find('.alert_html').hide(500);
      var notes_description= $(this).parent().find('.notes_description').val();   
      $(this).parent().hide();
      //append the not to container.
      $(this).parent().parent().find('.'+parent+'_notes_container').prepend('<li class="note clearfix">\
        <label for="note_description_val" style="float:left;">Note:</label>\
        <p class="note_description_val">' +notes_description+'</p>\
        <button class="btn btn-primary edit_note popover-markup" >Edit</button>\
        <span class="glyphicon glyphicon-trash delete_note fright"></span>\
        <div class="error_messages">    \
          <div class="alert alert-warning alert_notes">Please enter a Note</div>\
          <div class="alert alert-warning alert_html" style="">No HTML allowed</div>\
        </div>\
        <div class="edit_notes_form">\
          <label for="textarea"><h4>Add notes</h4></label>\
          <textarea class="notes_description form-control" maxlength="255"></textarea>\
          <div class="hint">'+max_length_255_required+'</div>\
          <button  class="update_notes_submit btn btn-primary">Update</button>\
          <button  class="cancel btn btn-primary">Cancel</button>\
        </div>\
        </li>');
      //sortable ui for subcategory

      $(this).closest('.category_container').find('.add_notes_category').attr('disabled',true);
      $('.subcategory_container').sortable();
    }
    $('body').on('click','.delete_note',function(){
      $(this).closest('.category_container').find('.add_notes_category').removeAttr('disabled');
      $(this).closest('.note').remove();
    })
  })      
}
//edit note function.
function edit_note(parent){

  $('body').on('click','.edit_note',function(){
    $(this).closest('.note').find('.notes_description').val($(this).closest('.note').find('.note_description_val').html());
    $(this).parent().find('.edit_notes_form').show(500);
    $(this).parent().find('.notes_description').focus();

  })
  $('body').on('click','.edit_notes_form .cancel',function(){
    $(this).parent('.edit_notes_form').hide(500);
    $(this).parent().siblings('.error_messages').find('.alert_notes').hide(500);
  })
  $('body').on('click','.update_notes_submit',function(){
    var note = $(this).siblings('.notes_description').val();
    if(note.length == 0 || html_regex.test(note)){
      $(this).closest('.edit_notes_form').siblings('.error_messages').find('.alert_notes').hide(500);
      $(this).closest('.edit_notes_form').siblings('.error_messages').find('.alert_html').hide(500);
      if(note.length==0){
        $(this).closest('.edit_notes_form').siblings('.error_messages').find('.alert_notes').show(500);
      }
      if(html_regex.test(note)){
        $(this).closest('.edit_notes_form').siblings('.error_messages').find('.alert_html').show(500);
      }
    }
    else{
      $(this).closest('.edit_notes_form').siblings('.error_messages').find('.alert_notes').hide(500);
      $(this).closest('.edit_notes_form').siblings('.error_messages').find('.alert_html').hide(500);
      $(this).parent().parent().find('.note_description_val').html(note);
      $(this).parent('.edit_notes_form').hide(300);

    }
  })
}
/*
function update_category(){
  if($(this).parent().find('.categoryname').val().length == 0){
    alert('error')
  }
}
function edit(){
  $('body').on('click', '.edit_category', function(){
    $(this).parent().find('.category_name').popover({
      html: true,
      content: function () {
        return $(this).parent().find('.hidden_category_form').html();
      }
    });
    $(this).parent().find('.category_name').popover('show')
    $(this).parent().find('.popover .cancel ').on('click',function(){
      $(this).parent().find('.category_name').popover('toggle')
    })
    $(this).parent().find('.popover .add_category_submit ').on('click',function(){
      var cat_name= $(this).parent().find('.categoryname');
      if(cat_name.val().length==0){
        alert('category name cannot be empty')
      }
      else{
        $(this).closest('.category_nav').find('.category_name_val').html($(this).siblings('.popover .categoryname').val())
        close_popover();
      }
    })
  })

}
function close_popover(){
  $('.category_name').popover('hide')
}
*/
function arrows(arrow,obj){
  $('body').on('click','.'+arrow+'_up_arrow',function(){
    var element_count= $(this).closest('.'+obj).length;
    if($(this).closest('.'+obj).index() ==0){
      console.log('cant move up');
    }
    else{
      $(this).closest('.'+obj).insertBefore($(this).closest('.'+obj).prev());
    }
  })
  $('body').on('click','.'+arrow+'_down_arrow',function(){
    var element_count= $(this).parent().closest('.'+obj).length;
   
      $(this).closest('.'+obj).insertAfter($(this).closest('.'+obj).next());
    
  })
}
// function arrows1(){
//   $('body').on('click','.item .up_arrow',function(){
//     var element_count= $(this).parent().closest('.item').length;
//     if($(this).closest('.item').index() ==0){
//       console.log('cant move up');
//     }
//     else{
//       $(this).closest('.item').insertBefore($(this).closest('.item').prev());
//     }
//   })
//   $('body').on('click','.item .down_arrow',function(){
//     var element_count= $(this).parent().closest('.item').length;
   
//       $(this).closest('.item').insertAfter($(this).closest('.item').next());
    
//   })
// }
/*
function arrows(){
  $('.category_container').find('.up_arrow').show();
  $('.category_container').find('.down_arrow').show();
  $('.category_container').first().find('.up_arrow').hide();
  $('.category_container').last().find('.down_arrow').hide();
  $('.up_arrow').click(function(){
    var index=$(this).parent().index();
    
    //$('.category_container').eq(index+1).after($(this).parent())
    $(this).parent().parent().insertBefore($(this).parent().parent().prev())
  })
  $('.down_arrow').click(function(){
    var index=$(this).parent().index();
    
    //$('.category_container').eq(index+1).after($(this).parent())
    $(this).parent().parent().insertAfter($(this).parent().parent().next())
  })
}
*/
//deleting the container above two steps
function delete_parent(){ 
  $('body').on('click','.delete_parent',function(){
    $(this).parent().parent().remove();
  })
}

function delete_item(){ 
  //a delete button with delete class will remove parent element when clicked
  $('body').on('click','.delete',function(){
    $(this).parent().remove();
  })

  $('body').on('click','.delete_item',function(){
    //custom delete option for item 
    $(this).parent().parent().parent().remove();
  })
}
//collapse function for category and subcategory, doesn't work for items.
function collapse(target){
  $('body').on('click', '.'+target+'_collapse', function(){
    $(this).parent().siblings('.master_container').hide(500);
    $(this).hide();
    $(this).siblings('.'+target+'_expand').show();
  })
  $('body').on('click', '.'+target+'_expand', function(){
    $(this).parent().siblings('.master_container').show(500);
    $(this).hide();
    $(this).siblings('.'+target+'_collapse').show();
  })
}

//collapse function for item.
function collapse_item(){
  $('body').on('click', '.item_collapse', function(){
  
    ///$(this).siblings('.item_description_val').hide(500);
    //$(this).siblings('.item_price_val').hide(500);
    $(this).hide();
    $(this).parent().parent().siblings('.option_container').hide(500);
    $(this).siblings('.item_expand').show();
  })
  $('body').on('click', '.item_expand', function(){
    //$(this).siblings('.item_description_val').show(500);
    //$(this).siblings('.item_price_val').show(500);
    $(this).parent().parent().siblings('.option_container').show(500);

    $(this).hide();
    $(this).siblings('.item_collapse').show();
  })
}
//count number of words remaining
function words_remaining(input){
  $('body').on('input',input,function(){
    var len = $(this).val().length;
    var max = $(this).next().find('span').attr('data-max');
    $(this).next().find('span').html(max-len);
  })
}
//close confirmation function

function close_confirm(button){
  $('.close_dialog').dialog({ dialogClass: 'noTitleStuff', modal:true });
  $('.decline').click(function(){
     document.location.href='pricelists.php';
  })
  $('.accept').click(function(){
    document.getElementById(button).click();
    document.location.href='pricelists.php';
  })
}

$(document).ready(function(){
	$('#edit_pricelist_name').click(function(){
        $('#edit_pricelist_form').show(500);
        $('#pricelist_name').focus();
        words_remaining("#pricelist_name")
         var len = $("#pricelist_name").val().length;
        var max = $("#pricelist_name").next().find('span').attr('data-max');
        $("#pricelist_name").next().find('span').html(max-len);
    })
    $('#edit_pricelist_form #close_pricelist_form').click(function(){
    	$('#edit_pricelist_form').hide(500);
    	$(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_pricelist_name').hide(500);
    	 $(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_length').hide(500);
         $(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_html').hide(500);
    })
    $('#update_pricelist_name').click(function(){
		pricelist_name = $('#pricelist_name').val();
		if(pricelist_name.length == 0 || html_regex.test(pricelist_name) || pricelist_name.length > 50){
            console.log(pricelist_name.length)
            if (pricelist_name.length == 0)
            {
                $(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_pricelist_name').show(500);
            }
            else
            {
				$(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_pricelist_name').hide(500);
			}
            if (pricelist_name.length>50)
            {
                $(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_length').show(500);
            }
            else
            {
				$(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_length').hide(500);
			}
            if (html_regex.test(pricelist_name))
            {
                $(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_html').show(500);
            }
            else
            {
				$(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_html').hide(500);
			}
        }
        else
        {
			$(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_pricelist_name').hide(500);
			$(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_length').hide(500);
            $(this).closest('#edit_pricelist_form').siblings('.error_messages').find('.alert_html').hide(500);
            
			$('#edit_pricelist_form').hide(500);
			var pricelist_name_val= $('#pricelist_name').val();
			console.log('')
			$('.pricelist_name').html(pricelist_name_val);
		}

    })

})

</script>
