<?
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$RS = $objDB->Show("merchant_user","", " ORDER BY id DESC LIMIT 10");
?>
<h2>Recently Joined Merchants</h2>
<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
		  <tr>
			<th width="35%" align="left">Business Name</th>
			<!--<th width="20%" align="left">Email</th> -->
			<th width="20%" align="left">Country</th>
			<th width="30%" align="left">State</th>
			<th width="15%" align="left">Last Visited </th>
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><?=$Row['firstname']." ".$Row['lastname']?></td>
			<!--<td align="left"><?=$Row['email']?></td> -->
			<td align="left">
			<?=$Row['country']?>
			</td>
			<td align="left"><?=$Row['state']?></td>
			<td align="left">
			<?
			echo date("m-d-Y H:i:s", strtotime($Row['last_login']));
			?>
			</td>
		  </tr>
		  <?
		  }
		 
		  }else{
		  ?>
		  <tr>
			<td colspan="5" align="left">No Merchant is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		</table>
