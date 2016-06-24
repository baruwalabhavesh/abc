<?php
/******** 
@USE : my profile
@PARAMETER : 
@RETURN : 
@USED IN PAGES : my-deals.php, search-deal.php, header.php, profile-left.php
*********/
//require_once("classes/Config.Inc.php");
check_customer_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//$objJSON = new JSON();
$JSON = $objJSON->get_customer_profile();
$RS = json_decode($JSON);

		//include('simpleimage.php');
/********/
/*	$target = "usr_pic" ;
	$uploaddir = './images/'.$target.'/'; 
	$name = 'usr_1385098485.png';
 $filename = $uploaddir . $name;
$image = new SimpleImage();
					$image->load($filename);
					$image->resize(30,30);
					$image->save($filename); */
					$target = "usr_pic" ;
	
//passthru("/usr/bin/convert ".$filename." -resize 30x30 ".$filename); 
/********************/
?>
<!DOCTYPE HTML >
<html>
<head>
<title>ScanFlip | My Profile</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--<script type="text/javascript" src="<?php echo WEB_PATH ?>/js/jquery-1.7.2.min.js"></script>-->
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/jquery.form.js"></script>
<script language="javascript" src="<?php echo ASSETS_JS?>/c/ajaxupload.3.5.js" ></script>
<script language="javascript">
js=jQuery.noConflict();

js(document).ready(function() { 
    // bind form using ajaxForm 
    js('#profile_form').ajaxForm({ 
        dataType:  'json', 
        success:   processUpdateJson 
    });
    js('#country_pp').live("change",function(){
	var change_value=this.value;
	
	if(change_value == "USA")
	{
	    js("#state_pp").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");
	   
	   
	}
	else
	{
	      $("#state_pp").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>"); 
		
	}
    });
   
	
	
});
function processUpdateJson(data) {
        jQuery("#succssbox").show();
        jQuery(".displayimg").attr('src',data.image_url);
        
	document.getElementById("msg_div").innerHTML = data.message;
	
	return false;
        
}
</script>
<link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">


</head>


<body>

<?php
require_once(CUST_LAYOUT."/header.php");
?>
<div id="content" class="cantent">
	<div class="my_main_div">
		<div id="contentContainer" class="contentContainer">
		
	<div style="min-height:530px;" class="manage_profile">
	
	<table width="100%"  border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td width="25%" align="left" valign="top">
	<?
		require_once(CUST_LAYOUT."/profile-left.php");
	?>
	</td>
    <td width="75%" align="left" valign="top">
		<form action="<?php echo WEB_PATH; ?>/process.php" method="post" id="profile_form" class="profilepage">
		<table width="100%"  border="0" cellspacing="2" cellpadding="2">
  <tr>
    
    <td colspan="2" align="left">
	
		<div class="success" id="succssbox" style="display: none">
		<img src="<?php echo ASSETS_IMG; ?>/c/hoory.png" alt="" />
		<span id="msg_div">
			<?php
				if(isset($_SESSION['msg']))
					echo $_SESSION['msg'];
			?>
		</span>
		</div>    
	
	
    </td>
  </tr>
  <tr>
    <td width="35%"><?php echo $language_msg["profile"]["first_name"];?></td>
    <td width="65%">
	<input type="text" name="firstname_pp" id="firstname_pp" style="width:200px; " value="<?=$RS[0]->firstname?>">
	</td>
  </tr>
  <tr>
    <td><?php echo $language_msg["profile"]["last_name"];?></td>
    <td><input type="text" name="lastname_pp" id="lastname_pp" style="width:200px; " value="<?=$RS[0]->lastname?>"></td>
  </tr>
  <tr>
    <td><?php echo "*".$language_msg["profile"]["gender"];?></td>
    <td>
	<select id="gender_pp" name="gender_pp">
	        <option></option>
		<option value="1" <? if($RS[0]->gender == 1) echo "selected";?>>Male</option>
		<option value="2" <? if($RS[0]->gender == 2) echo "selected";?>>Female</option>
	</select>
	</td>
  </tr>
   
  <tr>
    <td><?php echo "*".$language_msg["profile"]["date_of_birth"];?></td>
    <td>
	<select name="dob_month_pp" id="dob_month_pp">
	         <option></option>
		<?
		
		
		for($i=1; $i<=12; $i++){	
		?>
			<option value="<?=$i?>" <? if($RS[0]->dob_month == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
		<?
		}
		?>
	</select>
	-
	<select name="dob_day_pp" id="dob_day_pp">
	         <option></option>
		<?
		for($i=1; $i<=31; $i++){	
		?>
			<option value="<?=$i?>" <? if($RS[0]->dob_day == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
		<?
		}
		?>
	</select>
	- 
	<select name="dob_year_pp" id="dob_year_pp">
	     <option></option>
		<?
		for($i=date("Y")-60; $i<=date("Y"); $i++){	
		?>
			<option value="<?=$i?>" <? if($RS[0]->dob_year == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
		<?
		}
		?>
	</select>
	</td>
  </tr>
   
  
   <tr>
    <td>Mobile Phone</td>
    <?php 
    
    		$mobileno=substr($RS[0]->mobileno,4);
       	$area_code=substr($mobileno,0,3);
       	$mobileno2=substr($mobileno,4,3);
 			$mobileno1=substr($mobileno,8,4);      
//$mobileno2=substr($mobileno,9,4);
       
    ?>
    <td>
	<select name="mobile_country_code_pp" id="mobile_country_code_pp" style="display:none;">
	    <option value="001">001</option>
	</select>
        <input type="text" name="mobileno_area_code_pp" id="mobileno_area_code_pp" style="width:30px; " value="<?php echo $area_code;?>" maxlength="3">-
	<input type="text" name="mobileno2_pp" id="mobileno2_pp" style="width:30px; " value="<?php echo $mobileno2;?>" maxlength="3">-
	<input type="text" name="mobileno_pp" id="mobileno_pp" style="width:40px; " value="<?php echo $mobileno1;?>" maxlength="4">
       
    </td>
  </tr>
   <tr>
    <td>*Country</td>
    <td>
	<select name="country_pp" id="country_pp">
	         <option></option>
		<option value="USA" <? if($RS[0]->country == "USA") echo "selected";?>>USA</option>
		<option value="Canada" <? if($RS[0]->country == "Canada") echo "selected";?>>Canada</option>
	   
	</select>
       
    </td>
    
  </tr>
   <tr>
    <td>*Postal Code</td>
    <td>
	<input type="text" name="postalcode1_pp" id="postalcode1_pp" style="width:120px;" value="<?=$RS[0]->postalcode?>">
       
    </td>
    
  </tr>
   <tr>
    <td>City</td>
    <td>
	<input type="text" name="city_pp" id="city_pp" style="width:120px;" value="<?=$RS[0]->city?>">
       
    </td>
    
  </tr>
   <tr>
    <td>State</td>
    <td>
	<!--<input type="text" name="state" id="state" style="width:120px;" value="<?=$RS[0]->state?>">-->
	<select name="state_pp" id="state_pp" class="" style="display:block">
	     <option></option>
	    <?php
	    
	    if($RS[0]->country == "USA")
	    { ?>
		    <option value="AK" <? if($RS[0]->state == "AK") echo "selected";?> >AK</option>
		    <option value="AL" <? if($RS[0]->state == "AL") echo "selected";?>>AL</option>
		    <option value="AP" <? if($RS[0]->state == "AP") echo "selected";?>>AP</option>
		    <option value="AR" <? if($RS[0]->state == "AR") echo "selected";?>>AR</option>
		    <option value="AS" <? if($RS[0]->state == "AS") echo "selected";?>>AS</option>
		    <option value="AZ" <? if($RS[0]->state == "AZ") echo "selected";?>>AZ</option>
		    <option value="CA" <? if($RS[0]->state == "CA") echo "selected";?>>CA</option>
		    <option value="CO" <? if($RS[0]->state == "CO") echo "selected";?>>CO</option>
		    <option value="CT" <? if($RS[0]->state == "CT") echo "selected";?>>CT</option>
		    <option value="DC" <? if($RS[0]->state == "DC") echo "selected";?>>DC</option>
		    <option value="DE" <? if($RS[0]->state == "DE") echo "selected";?>>DE</option>
		    <option value="FL" <? if($RS[0]->state == "FL") echo "selected";?>>FL</option>
		    <option value="FM" <? if($RS[0]->state == "FM") echo "selected";?>>FM</option>
		    <option value="GA" <? if($RS[0]->state == "GA") echo "selected";?>>GA</option>
		    <option value="GS" <? if($RS[0]->state == "GS") echo "selected";?>>GS</option>
		    <option value="GU" <? if($RS[0]->state == "GU") echo "selected";?>>GU</option>
		    <option value="HI" <? if($RS[0]->state == "HI") echo "selected";?>>HI</option>
		    <option value="IA" <? if($RS[0]->state == "IA") echo "selected";?>>IA</option>
		    <option value="ID" <? if($RS[0]->state == "ID") echo "selected";?>>ID</option>
		    <option value="IL" <? if($RS[0]->state == "IL") echo "selected";?>>IL</option>
		    <option value="IN" <? if($RS[0]->state == "IN") echo "selected";?>>IN</option>
		    <option value="KS" <? if($RS[0]->state == "KS") echo "selected";?>>KS</option>
		    <option value="KY" <? if($RS[0]->state == "KY") echo "selected";?>>KY</option>
		    <option value="LA" <? if($RS[0]->state == "LA") echo "selected";?>>LA</option>
		    <option value="MA" <? if($RS[0]->state == "MA") echo "selected";?>>MA</option>
		    <option value="MD" <? if($RS[0]->state == "MD") echo "selected";?>>MD</option>
		    <option value="ME" <? if($RS[0]->state == "ME") echo "selected";?>>ME</option>
		    <option value="MH" <? if($RS[0]->state == "MH") echo "selected";?>>MH</option>
		    <option value="MI" <? if($RS[0]->state == "MI") echo "selected";?>>MI</option>
		    <option value="MN" <? if($RS[0]->state == "MN") echo "selected";?>>MN</option>
		    <option value="MO" <? if($RS[0]->state == "MO") echo "selected";?>>MO</option>
		    <option value="MP" <? if($RS[0]->state == "MP") echo "selected";?>>MP</option>
		    <option value="MS" <? if($RS[0]->state == "MS") echo "selected";?>>MS</option>
		    <option value="MT" <? if($RS[0]->state == "MT") echo "selected";?>>MT</option>
		    <option value="NC" <? if($RS[0]->state == "NC") echo "selected";?>>NC</option>
		    <option value="ND" <? if($RS[0]->state == "ND") echo "selected";?>>ND</option>
		    <option value="NE" <? if($RS[0]->state == "NE") echo "selected";?>>NE</option>
		    <option value="NH" <? if($RS[0]->state == "NH") echo "selected";?>>NH</option>
		    <option value="NJ" <? if($RS[0]->state == "NJ") echo "selected";?>>NJ</option>
		    <option value="NM" <? if($RS[0]->state == "NM") echo "selected";?>>NM</option>
		    <option value="NV" <? if($RS[0]->state == "NV") echo "selected";?>>NV</option>
		    <option value="NY" <? if($RS[0]->state == "NY") echo "selected";?>>NY</option>
		    <option value="OH" <? if($RS[0]->state == "OH") echo "selected";?>>OH</option>
		    <option value="OK" <? if($RS[0]->state == "OK") echo "selected";?>>OK</option>
		    <option value="OR" <? if($RS[0]->state == "OR") echo "selected";?>>OR</option>
		    <option value="PA" <? if($RS[0]->state == "PA") echo "selected";?>>PA</option>
		    <option value="PR" <? if($RS[0]->state == "PR") echo "selected";?>>PR</option>
		    <option value="PW" <? if($RS[0]->state == "PW") echo "selected";?>>PW</option>
		    <option value="RI" <? if($RS[0]->state == "RI") echo "selected";?>>RI</option>
		    <option value="SC" <? if($RS[0]->state == "SC") echo "selected";?>>SC</option>
		    <option value="SD" <? if($RS[0]->state == "SD") echo "selected";?>>SD</option>
		    <option value="TN" <? if($RS[0]->state == "TN") echo "selected";?>>TN</option>
		    <option value="TX" <? if($RS[0]->state == "TX") echo "selected";?>>TX</option>
		    <option value="UT" <? if($RS[0]->state == "UT") echo "selected";?>>UT</option>
		    <option value="VA" <? if($RS[0]->state == "VA") echo "selected";?>>VA</option>
		    <option value="VI" <? if($RS[0]->state == "VI") echo "selected";?>>VI</option>
		    <option value="VT" <? if($RS[0]->state == "VT") echo "selected";?>>VT</option>
		    <option value="WA" <? if($RS[0]->state == "WA") echo "selected";?>>WA</option>
		    <option value="WI" <? if($RS[0]->state == "WI") echo "selected";?>>WI</option>
		    <option value="WV" <? if($RS[0]->state == "WV") echo "selected";?>>WV</option>
		    <option value="WY" <? if($RS[0]->state == "WY") echo "selected";?>>WY</option>
		    
	     <?php }
	    else if($RS[0]->country == "Canada")
	    { ?>
		
		   <option value='AB' <? if($RS[0]->state == "AB") echo "selected";?>>AB</option>
		<option value='BC' <? if($RS[0]->state == "BC") echo "selected";?>>BC</option>
		<option value='LB' <? if($RS[0]->state == "LB") echo "selected";?>>LB</option>
		<option value='MB' <? if($RS[0]->state == "MB") echo "selected";?>>MB</option>
		<option value='NB' <? if($RS[0]->state == "NB") echo "selected";?>>NB</option>
		<option value='NF' <? if($RS[0]->state == "NF") echo "selected";?>>NF</option>
		<option value='NS' <? if($RS[0]->state == "NS") echo "selected";?>>NS</option>
		<option value='NT' <? if($RS[0]->state == "NT") echo "selected";?>>NT</option>
		<option value='NU' <? if($RS[0]->state == "NU") echo "selected";?>>NU</option>
		<option value='ON' <? if($RS[0]->state == "ON") echo "selected";?>>ON</option>
		<option value='PE' <? if($RS[0]->state == "PE") echo "selected";?>>PE</option>
		<option value='PQ' <? if($RS[0]->state == "PQ") echo "selected";?>>PQ</option>
		<option value='QB' <? if($RS[0]->state == "QB") echo "selected";?>>QB</option>
		<option value='QC' <? if($RS[0]->state == "QC") echo "selected";?>>QC</option>
		<option value='SK' <? if($RS[0]->state == "SK") echo "selected";?>>SK</option>
		<option value='YT' <? if($RS[0]->state == "YT") echo "selected";?>>YT</option>
		    
	     <?php 
	 }
	else
	{ ?>
	
		    <option value="AK" <? if($RS[0]->state == "AK") echo "selected";?> >AK</option>
		    <option value="AL" <? if($RS[0]->state == "AL") echo "selected";?>>AL</option>
		    <option value="AP" <? if($RS[0]->state == "AP") echo "selected";?>>AP</option>
		    <option value="AR" <? if($RS[0]->state == "AR") echo "selected";?>>AR</option>
		    <option value="AS" <? if($RS[0]->state == "AS") echo "selected";?>>AS</option>
		    <option value="AZ" <? if($RS[0]->state == "AZ") echo "selected";?>>AZ</option>
		    <option value="CA" <? if($RS[0]->state == "CA") echo "selected";?>>CA</option>
		    <option value="CO" <? if($RS[0]->state == "CO") echo "selected";?>>CO</option>
		    <option value="CT" <? if($RS[0]->state == "CT") echo "selected";?>>CT</option>
		    <option value="DC" <? if($RS[0]->state == "DC") echo "selected";?>>DC</option>
		    <option value="DE" <? if($RS[0]->state == "DE") echo "selected";?>>DE</option>
		    <option value="FL" <? if($RS[0]->state == "FL") echo "selected";?>>FL</option>
		    <option value="FM" <? if($RS[0]->state == "FM") echo "selected";?>>FM</option>
		    <option value="GA" <? if($RS[0]->state == "GA") echo "selected";?>>GA</option>
		    <option value="GS" <? if($RS[0]->state == "GS") echo "selected";?>>GS</option>
		    <option value="GU" <? if($RS[0]->state == "GU") echo "selected";?>>GU</option>
		    <option value="HI" <? if($RS[0]->state == "HI") echo "selected";?>>HI</option>
		    <option value="IA" <? if($RS[0]->state == "IA") echo "selected";?>>IA</option>
		    <option value="ID" <? if($RS[0]->state == "ID") echo "selected";?>>ID</option>
		    <option value="IL" <? if($RS[0]->state == "IL") echo "selected";?>>IL</option>
		    <option value="IN" <? if($RS[0]->state == "IN") echo "selected";?>>IN</option>
		    <option value="KS" <? if($RS[0]->state == "KS") echo "selected";?>>KS</option>
		    <option value="KY" <? if($RS[0]->state == "KY") echo "selected";?>>KY</option>
		    <option value="LA" <? if($RS[0]->state == "LA") echo "selected";?>>LA</option>
		    <option value="MA" <? if($RS[0]->state == "MA") echo "selected";?>>MA</option>
		    <option value="MD" <? if($RS[0]->state == "MD") echo "selected";?>>MD</option>
		    <option value="ME" <? if($RS[0]->state == "ME") echo "selected";?>>ME</option>
		    <option value="MH" <? if($RS[0]->state == "MH") echo "selected";?>>MH</option>
		    <option value="MI" <? if($RS[0]->state == "MI") echo "selected";?>>MI</option>
		    <option value="MN" <? if($RS[0]->state == "MN") echo "selected";?>>MN</option>
		    <option value="MO" <? if($RS[0]->state == "MO") echo "selected";?>>MO</option>
		    <option value="MP" <? if($RS[0]->state == "MP") echo "selected";?>>MP</option>
		    <option value="MS" <? if($RS[0]->state == "MS") echo "selected";?>>MS</option>
		    <option value="MT" <? if($RS[0]->state == "MT") echo "selected";?>>MT</option>
		    <option value="NC" <? if($RS[0]->state == "NC") echo "selected";?>>NC</option>
		    <option value="ND" <? if($RS[0]->state == "ND") echo "selected";?>>ND</option>
		    <option value="NE" <? if($RS[0]->state == "NE") echo "selected";?>>NE</option>
		    <option value="NH" <? if($RS[0]->state == "NH") echo "selected";?>>NH</option>
		    <option value="NJ" <? if($RS[0]->state == "NJ") echo "selected";?>>NJ</option>
		    <option value="NM" <? if($RS[0]->state == "NM") echo "selected";?>>NM</option>
		    <option value="NV" <? if($RS[0]->state == "NV") echo "selected";?>>NV</option>
		    <option value="NY" <? if($RS[0]->state == "NY") echo "selected";?>>NY</option>
		    <option value="OH" <? if($RS[0]->state == "OH") echo "selected";?>>OH</option>
		    <option value="OK" <? if($RS[0]->state == "OK") echo "selected";?>>OK</option>
		    <option value="OR" <? if($RS[0]->state == "OR") echo "selected";?>>OR</option>
		    <option value="PA" <? if($RS[0]->state == "PA") echo "selected";?>>PA</option>
		    <option value="PR" <? if($RS[0]->state == "PR") echo "selected";?>>PR</option>
		    <option value="PW" <? if($RS[0]->state == "PW") echo "selected";?>>PW</option>
		    <option value="RI" <? if($RS[0]->state == "RI") echo "selected";?>>RI</option>
		    <option value="SC" <? if($RS[0]->state == "SC") echo "selected";?>>SC</option>
		    <option value="SD" <? if($RS[0]->state == "SD") echo "selected";?>>SD</option>
		    <option value="TN" <? if($RS[0]->state == "TN") echo "selected";?>>TN</option>
		    <option value="TX" <? if($RS[0]->state == "TX") echo "selected";?>>TX</option>
		    <option value="UT" <? if($RS[0]->state == "UT") echo "selected";?>>UT</option>
		    <option value="VA" <? if($RS[0]->state == "VA") echo "selected";?>>VA</option>
		    <option value="VI" <? if($RS[0]->state == "VI") echo "selected";?>>VI</option>
		    <option value="VT" <? if($RS[0]->state == "VT") echo "selected";?>>VT</option>
		    <option value="WA" <? if($RS[0]->state == "WA") echo "selected";?>>WA</option>
		    <option value="WI" <? if($RS[0]->state == "WI") echo "selected";?>>WI</option>
		    <option value="WV" <? if($RS[0]->state == "WV") echo "selected";?>>WV</option>
		    <option value="WY" <? if($RS[0]->state == "WY") echo "selected";?>>WY</option>
		    
	     <?php }	?>
	</select>
        <!--<select name="state" id="state_canada" class="state" style="display:none">
	    <option value="AK">AK</option>
	    <option value="AL">AL</option>
	    <option value="ME">ME</option>
	    <option value="MH">MH</option>
	    <option value="MS">MS</option>
	</select>-->
    </td>
    
  </tr>
  <tr>
    <td><?php echo $language_msg["profile"]["email_notification"];?></td>
    <td>
	<select name="emailnotification">
		<option value="1" <? if($RS[0]->emailnotification == 1) echo "selected";?>>Yes</option>
		<option value="0" <? if($RS[0]->emailnotification == 0) echo "selected";?>>No</option>
	</select>
	</td>
  </tr>
  <tr>
    <td><?php echo $language_msg["profile"]["current_location"];?></td>
    <input type="hidden" name="hdn_current_address" id="hdn_current_address" value="<?=$RS[0]->current_location?>" />
    <input type="hidden" name="hdn_current_user_id" id="hdn_current_user_id" value="<?=$RS[0]->id?>" />
    <td><input type="text" name="current_location" id="current_location" style="width:200px; " value="<?=$RS[0]->current_location?>" disabled></td>
  </tr>
  <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?=$RS[0]->profile_pic?>" />

  <tr>
  <td>Profile Picture:</td>
  <td>
  
  <div id="upload" style="width:100px" >
									<span  >Upload Photo
									</span>
									</div>
									
  </td>

  </tr>
 
  <tr><td align="right">&nbsp; </td>
					<td>
			
						<span id="status" ></span>
						<br/>
           
						<ul id="files" >
						<?php if($RS[0]->profile_pic != "") 
						{  
                                                    
							$pic_var = explode("/", $RS[0]->profile_pic);
							$twitter_pic_var=explode("/",$RS[0]->profile_pic );
							if(isset($pic_var[2]))
							{ 	
								if($pic_var[2] == "graph.facebook.com")
								{
									 ?>
									<li>

											<img class="displayimg" src="<?php echo $RS[0]->profile_pic;?>" >
									</li>
							   <?php
								 } 
							   else if($pic_var[2] == "pbs.twimg.com")
								{
									 ?>
									<li>

											<img class="displayimg" src="<?php echo $RS[0]->profile_pic;?>" >
									</li>
							   <?php
								 }
							   else
							   {
								?>
									<li>

									<img class="displayimg" src="<?php echo ASSETS_IMG; ?>/c/usr_pic/<?php echo $RS[0]->profile_pic;?>">
									</li>
							<?php
							   }
							}
							else
							   {
								?>
									<li>

									<img class="displayimg" src="<?php echo ASSETS_IMG; ?>/c/usr_pic/<?php echo $RS[0]->profile_pic;?>">
									</li>
							<?php
							   }
                         }
                         ?>
						 </ul>
					</td>
				  </tr>
 


     <tr>
    <td>&nbsp;</td>
    <td>
		<input type="submit" name="btnUpdateProfile" value="Save" id="btnUpdateProfile" >
                <script>function btncanprofile(){                                                
                                                window.location="<?=WEB_PATH?>/my-deals.php";}
                                                
                                                </script>
         <input type="submit" name="btncancelprofile" value="Cancel" onClick="btncanprofile()"  >
	</td>
  </tr>
</table>
</form>
	</td>
  </tr>
</table>
	</div>
</div> <!--end of contentContainer-->
<?php require_once(CUST_LAYOUT."/before-footer.php");?>
</div><!--end of my_main_div-->
</div><!--end of content-->

<?php require_once(CUST_LAYOUT."/footer.php");?>
     <div id="dialog-message" title="Message Box" style="display:none">

    </div>

</body>
</html>
<?
$_SESSION['msg'] = "";
?>
<script>
	var file_path = "";
$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'upload_user_profile.php?doAction=FileUpload&img_type=usr_pic',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				if($('#files').children().length > 0)
				{
					$('#files li').detach();
				}
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('<?php echo $merchant_msg["addlocation"]["Msg_Image_Validation"];?>');
					return false;
				}
				status.text('<?php echo $merchant_msg["addlocation"]["Msg_uploading"];?>');
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
                                //alert(response);
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
                                   $("#hdn_image_path").val(file_path);
	$("#hdn_image_id").val("");
	var img = "<li><img src='<?php echo ASSETS_IMG; ?>/c/usr_pic/"+ file_path +"' class='displayimg'></li>";
	$('#files').html(img +"<br/><div style='margin-left: 35px;margin-top: -91px;position: absolute;display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=WEB_PATH?>/merchant/images/large_close.png' onclick='rm_image()' /></div></div></div>");
                                }
			}
		});
                
               
                /* End More Images Upload Code*/
                
});
function rm_image()
{
	jQuery("#hdn_image_path").val("");
	jQuery("#hdn_image_id").val("");
	jQuery('#files').html("");
	
}
jQuery('input').focus(function() {
	//alert('Handler for .focus() called.');
	//jQuery("#succssbox").css("display","none");
	jQuery("#succssbox").fadeOut("slow");
});
jQuery('select').change(function() {
	//alert('Handler for .change() called.');
	//jQuery("#succssbox").css("display","none");
	jQuery("#succssbox").fadeOut("slow");
});
jQuery("#btnUpdateProfile").click(function(){
    
    var age_limit="14";
	var msg_box="";
var flag="true";
	
    var dob_month=jQuery("#dob_month_pp").val();
     var dob_day=jQuery("#dob_day_pp").val();
      var dob_year=jQuery("#dob_year_pp").val();
      var first_name=jQuery("#firstname_pp").val();
      var last_name=jQuery("#lastname_pp").val();
      var postal_code=jQuery("#postalcode1_pp").val();
      var country=jQuery("#country_pp").val();
	  var state=jQuery("#state_pp").find('option:selected').text();
	  var city = jQuery("#city_pp").val();
      var mobileno_area_code=jQuery("#mobileno_area_code_pp").val();
      var mobileno=jQuery("#mobileno_pp").val()
      var gender = jQuery("#gender_pp").find('option:selected').text();
      
    
      var mobileno2=jQuery("#mobileno2_pp").val()
      
      var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
      var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
     
      var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
      var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
	  
      var millisPerYear = 1000 * 60 * 60 * 24 * 365;
var birthdate = new Date(dob_year, dob_month, dob_day); 

var age = ((new Date().getTime()) - birthdate.getTime()) / millisPerYear;

//        if(first_name == "")
//	{
//	    alert("Please Enter A First Name");
//	    return false;
//	}
//	else if(last_name == "")
//	{
//	    alert("Please Enter A Last Name");
//	    return false;
//	}
	/*else if(postal_code == "")
	{
	    
	    
	    alert("Please Enter A Postal Code");
	    return false;
	}*/
	
	if (first_name == "") {
		msg_box+="<div><?php echo $client_msg['login_register']['Msg_First_Name'];?></div>";
        flag="false";
	}
	if (last_name == "") {
		msg_box+="<div><?php echo $client_msg['login_register']['Msg_Last_Name'];?></div>";
        flag="false";
	}
	if (gender == "") {    
	    msg_box+="<div><?php echo $client_msg['login_register']['Msg_Select_Gender'];?></div>";
		flag="false";
	}
	if (dob_month == "" || dob_day == "" || dob_year == "") {
	    msg_box+="<div><?php echo $client_msg['login_register']['Msg_Select_Date_Of_Birth'];?></div>";
		flag="false";		
	}
	if (country == "") {
	    msg_box+="<div><?php echo $client_msg['login_register']['Msg_Select_Country'];?></div>";
		flag="false";
	}
	if (state == "") {
	    msg_box+="<div><?php echo $client_msg['login_register']['Msg_Select_State'];?></div>";
		flag="false";
	}
	if (city == "") {
	    msg_box+="<div><?php echo $client_msg['login_register']['Msg_Select_City'];?></div>";
		flag="false";
	}
	if(postal_code=="")
	{
		//alert("Please enter postal/zipcode");
                msg_box+="<div><?php echo $client_msg['login_register']['Msg_Enter_Postal_Code'];?></div>";
                flag="false";
		//return false;
	}
        else
        {
                postal_code=jQuery.trim(postal_code);
			postal_code=postal_code.toUpperCase();
                //alert(country);
                //alert(postal_code);
                if(country=="USA")
                {
                   if(!usPostalReg.test(postal_code)) {

                        //alert("Please enter valid postal/zipcode");
                        msg_box+="<div><?php echo $client_msg['login_register']['Msg_Input_Valid_Postal_Code'];?></div>";
                        flag="false";
                        //return false;
                   }	

                }
                else if(country == "Canada")
                {
                    if(!canadaPostalReg.test(postal_code)) {
                        //alert("Please enter valid postal/zipcode");
                        msg_box+="<div><?php echo $client_msg['login_register']['Msg_Input_Valid_Postal_Code'];?></div>";
                        flag="false";
                        //return false;
                    }
                }
        }
		
	if(mobileno_area_code != "" || mobileno != "" || mobileno2 != "")
	{	  
	    if(mobileno_area_code.length != 3)
		{
			//alert("Please update your phone number");
				msg_box+="<div><?php echo $client_msg['my_profile']['Msg_Enter_Update_Phone_Number'];?></div>";
				flag="false";
			
		   // return false;
		}
			else if(mobileno.length != 4)
		{
			//alert("Please update your phone number");
				msg_box+="<div><?php echo $client_msg['my_profile']['Msg_Enter_Update_Phone_Number'];?></div>";
				flag="false";
			//return false;
		}
			else if(mobileno2.length != 3)
		{
		   // alert("Please update your phone number");
				msg_box+="<div><?php echo $client_msg['my_profile']['Msg_Enter_Update_Phone_Number'];?></div>";
				flag="false";
		   // return false;
		}    
	}
		
            if(flag == "false")
                {
                    var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                    var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg_box+"</div>";
                     var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                         jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
       
                        jQuery.fancybox({
                                            content:jQuery('#dialog-message').html(),

                                            type: 'html',
					    

                                            openSpeed  : 300,

                                            closeSpeed  : 300,
                                            // topRatio: 0,

                                            changeFade : 'fast',  
                                            beforeShow : function(){
                                                $(".fancybox-inner").addClass("msgClass");
                                            },

                                            helpers: {
                                                    overlay: {
                                                    opacity: 0.3
                                                    } // overlay
                                            }
                    }); 
                    return false;
                }
                else
                {
                       return true; 
                }
            
            
    });
    jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });

$("a#myprofile").css("background-color","orange");
$("a#profile-link").css("color","orange");
$("a#password-link").css("color","#0066FF");
</script>
