<?php
/******** 
@USE : display location chart
@PARAMETER : 
@RETURN : 
@USED IN PAGES : process.php
*********/
require_once("classes/Config.Inc.php");
//check_customer_session();
include_once(SERVER_PATH."/classes/DB.php");
include_once(SERVER_PATH."/classes/func_print_coupon.php");
include_once(SERVER_PATH."/classes/JSON.php");
$locid=$_REQUEST['locid'];
?>
<link href="<?=WEB_PATH?>/templates/template.css" rel="stylesheet" type="text/css">
<script src="<?php echo WEB_PATH ?>/js/jquery-1.9.0.min.js" type="text/javascript"></script>
<script src="<?php echo WEB_PATH ?>/js/highcharts.js"></script>
<script src="<?php echo WEB_PATH ?>/js/exporting.js"></script>

<div class="location_popup_rating">
<div class="rating_heading_top" style="display:block;">
	<div class="rating_heading_half_left"> Visitors Rating</div>
	<span class="rating_heading_seperator">|</span>
	<div class="rating_heading_half_right"> Rating Trend
	</div>
</div>
<!-- start of rating div -->
<div id="visitorrating_<?php echo $locid; ?>" class="visitorrating" style="display:block;">
	<div class="rting_strip">
	<div class="rating_heading" >Excellent : </div><div class="mainratingdiv_containere" id="">
	<div class="div_container excellent"></div>
											</div><span></span></div>

	<div class="rting_strip">
	<div class="rating_heading">Very Good : </div><div class="mainratingdiv_containere" id="">
	<div class="div_container verygood"></div>
	</div><span></span></div>

	<div class="rting_strip">
	<div class="rating_heading"> Good : </div><div class="mainratingdiv_containere" id="">
	<div class="div_container good"></div>
	</div><span></span></div>

	<div class="rting_strip">
	<div class="rating_heading"> Fair : </div><div class="mainratingdiv_containere" id="">
	<div class="div_container fair"></div>
	</div><span></span></div>


	<div class="rting_strip">
	<div class="rating_heading"> Poor : </div><div class="mainratingdiv_containere" id="">
	<div class="div_container poor"></div>
	</div><span></span></div>
</div>
<!-- end of rating div -->
<div id="container_<?php echo $locid; ?>" class="ratingtrend" style="display:block;"></div>  
</div>
<script>
var locid='<?php echo $locid; ?>';
jQuery.ajax({
		type:"POST",
		url:'<?php echo WEB_PATH; ?>/rating_trend_chart.php',
		async:false,
		data :'locid='+locid,
		success:function(msg)
		{
			obj = jQuery.parseJSON(msg);
			//alert(obj.rating_info);
			all_rating = obj.rating_info;
			//alert(obj.status);
			st_yr = obj.start_year;
			st_mnt = obj.start_month;
			visitor_detail = obj.visitor_detail;
			ratings = obj.rating_values;
			max_rating = obj.max_rating;
			max_rating_heading = obj.max_rating_heading;							
	   }
});

jQuery("#visitorrating_"+locid+" .excellent").parent().next().text(ratings[4]);
jQuery("#visitorrating_"+locid+" .verygood").parent().next().text(ratings[3]);
jQuery("#visitorrating_"+locid+" .good").parent().next().text(ratings[2]);
jQuery("#visitorrating_"+locid+" .fair").parent().next().text(ratings[1]);
jQuery("#visitorrating_"+locid+" .poor").parent().next().text(ratings[0]);

jQuery("#visitorrating_"+locid+" .excellent").animate({
width:visitor_detail[4]+"%" }, 500, function() {
});
jQuery("#visitorrating_"+locid+" .verygood").animate({
width:visitor_detail[3]+"%" }, 500, function() {
});
jQuery("#visitorrating_"+locid+" .good").animate({
width:visitor_detail[2]+"%" }, 500, function() {
});
jQuery("#visitorrating_"+locid+" .fair").animate({
width:visitor_detail[1]+"%" }, 500, function() {
});
jQuery("#visitorrating_"+locid+" .poor").animate({
width:visitor_detail[0]+"%" }, 500, function() {
});

var test = "["+all_rating+"]";
var parsedTest = JSON.parse(test);

jQuery('#container_'+locid).highcharts({
            chart: {
                zoomType: 'null',
                spacingRight: 20
            },
             credits :{
						enabled:false
					},
        exporting: {
        buttons: {
            contextButtons: {
                enabled: false,
                menuItems: null
            }
        },
         enabled: false
    },
             title: {
                text: 'Rating Trend',
				style: {
                display: 'none'
                        }
            },
            
            xAxis: {
                
                type: 'datetime',
                title: {
                    text: null
                }
            },
            yAxis: {
                min:1,
                max:5,
                title: {
                    text: null
                }
            },
            tooltip: {
               shared: true
            },
            legend: {
                enabled: false
            },
             plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    lineWidth: 1,
                    marker: {
                        enabled: false
                    },
                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
    
            series: [{
                type: 'area',
                name: 'Current Rating',
                pointInterval: 24 * 3600 * 1000 ,//point interval in data is for every days average rating
                pointStart: Date.UTC(st_yr, st_mnt, 01),
                //avg rating for last 3 months
                data: parsedTest ,
                enableMouseTracking: false
            }]
        });				
</script>
