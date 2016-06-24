<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();


$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
$str_activated = $str_expired = "";
foreach($months as $month){
	$from_date = date("Y")."-".$month."-01 00:00:00";
	$to_date = date("Y")."-".$month."-31 23:59:59";
	$Sql = "SELECT COUNT(*) as total FROM campaigns WHERE created_date>='$from_date' AND created_date<='$to_date' AND visible='1'";
	//echo $Sql."<hr>";
	$RS = $objDB->Conn->Execute($Sql);
	$str_activated .= $RS->fields['total'].",";
	

	$Sql = "SELECT COUNT(*) as total FROM campaigns WHERE  expiration_date>='$from_date' AND expiration_date<='$to_date'";
	//echo $Sql."<hr>";
	$RS = $objDB->Conn->Execute($Sql);
	$str_expired .= $RS->fields['total'].",";
}
$str_activated = substr($str_activated, 0, strlen($str_activated)-1);
$str_expired = substr($str_expired, 0, strlen($str_expired)-1);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<script src="<?=ASSETS_JS?>/a/jquery.js"></script>
<script src="<?=ASSETS_JS?>/a/highcharts.js"></script>
<script language="javascript">

var chart;
$(document).ready(function() {
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container1',
			type: 'line',
			marginRight: 130,
			marginBottom: 25
		},
		title: {
			text: 'Monthly Average Compaigns',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: ['Jan <?=date("y")?>', 'Feb <?=date("y")?>', 'Mar <?=date("y")?>', 'Apr <?=date("y")?>', 'May <?=date("y")?>', 'Jun <?=date("y")?>',
				'Jul <?=date("y")?>', 'Aug <?=date("y")?>', 'Sep <?=date("y")?>', 'Oct <?=date("y")?>', 'Nov <?=date("y")?>', 'Dec <?=date("y")?>']
		},
		yAxis: {
			title: {
				text: 'Compaigns'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'Activated',
			data: [
			<?
			echo $str_activated;
			?>
				  ]
		}, {
			name: 'Redeemed',
			data: [0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
		}, {
			name: 'Expired',
			data: [
					<?
					echo $str_expired;
					?>
				  ]
		}]
	});
});
</script>
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
	<div id="container1" style="min-width: 250px; height: 350px; margin: 0 auto"></div>
	    <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
