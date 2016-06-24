<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where['id'] = $_REQUEST['id'];
$sql = "select o.* , c.firstname , c.lastname , g.title from 
		giftcard_order o inner join customer_user c on c.id = o.user_id inner join giftcards g on g.id = o.giftcard_id where o.id=".$_REQUEST['id'];

$RS = $objDB->Conn->Execute($sql);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<style>
form li{list-style:none;}
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
<h2>Giftcard Order Management </h2>
	<form action="process.php" method="post">
		<h4>Order Detail</h4>
		<div><b>Order Number : </b> <?php echo $RS->fields['order_number']; ?></div>
		<div><b>User: </b> <?php echo $RS->fields['firstname']." ".$RS->fields['lastname'] ?></div>
		<div><b>Ordered Date : </b> <?php echo $RS->fields['order_date']; ?></div>
		<div><b>Gift Crad : </b> <?php echo $RS->fields['title']; ?></div>
		<div><b>Shipping Adrees : </b> <?php echo $RS->fields['ship_to_address']; ?> </div>
		<div><b> Shipping Status : </b> <span><?php if($RS->fields['status'] == 2){ echo "pending";}else { echo "Delivered";} ?></span> &nbsp; &nbsp;  <a href="edit-giftcardorder.php?action=delivered" > Change Ststus To Delivered </a></div>
		<div><b>Note : </b><?php echo $RS->fields['order_note']; ?><span>Add Note</span></div>
		
	 </form>
	    <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<?
$_SESSION['msg'] = "";
?>

</body>
</html>
