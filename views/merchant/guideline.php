<?php
/**
 * @uses guide line
 * @used in pages :mechant-services.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
//check_merchant_session();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Merchant Account Guideline </title>
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
</head>

<body>
<div>

<!---start header---->
	<div>
		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
     <div id="content">
        <div class="guideline_block">
        	<h1>Merchant Account Guideline</h1>
            <h2>Your Scanflip Account</h2>
            <div class="ownerblock">
              <p><strong>Ownership:</strong> Only business owners or authorized representatives may verify or create their business listings on Scanflip.</p>
            </div>
            <div class="ownerblock">
              <p><strong>Account Email Address:</strong> Use a shared business email account, if multiple users will be updating your business listing. If possible, use an email account under your business domain. For example, if your business website is www.scanflip.com, a matching email address would be </p>
              <a href="#">you@scanflip.com.</a>
            </div>
            <h2>Your Business Listing</h2>
            <div class="usinessListing">
            	<h4>Business Name:</h4>
                <ul>
                 <li> Your title should reflect your business's real-world title.</li>
                </ul>
            </div>
            <div class="usinessListing">
            	<h4>Business Location:</h4>
                <ul>
                 <li>Use a precise, accurate address to describe your business location.</li>
                 <li>P.O. Boxes are not considered accurate physical locations. Your business location should be staffed during its stated hours.</li>
                </ul>
            </div>
            <div class="usinessListing">
            	<h4>Website, Facebook and Google+ page & Phone: </h4>
                <ul>
                 <li>   Provide a phone number that connects to your individual business location as directly as possible, and provide one website that represents your individual business location.</li>
                 <li>Use a local phone number instead of a call center number whenever possible.</li>
                 <li>Do not provide phone numbers or URLs that redirect or "refer" users to landing pages or phone numbers other than those of the actual business.</li>
                </ul>
            </div>
            <div class="usinessListing">
            	<h4>Categories: </h4>
                <ul>
                 <li>Select at least one category from the list of available categories. If you cannot find category representing your business please contact scanflip support team.</li>
                 <li> Only businesses that make in-person contact with customers qualify as Business Location.</li>
                 <li> Businesses that are under construction or that have not yet opened to the public are not eligible for a listing on Scanflip.</li>
                </ul>
            </div>
             <div class="ownerblock1">
              <strong>Photos:</strong>
              <p>All location images uploaded must be at least 400px x 200 px ( width x height).  All campaign images uploaded must be at least 1600 pixel  x 1200 pixel ( width x height ).</p>
            </div>
            <div class="ownerblock1">
              <strong>Marketing,</strong>
              <p>All campaigns should clearly specify terms of the activity and provide clear guidelines and qualifications. All such promises, given or implied, should be adhered to.</p>
            </div>
            <div class="ownerblock1">
              <strong>Disclaimer: </strong>
              <p>Scanflip reserves the right to suspend access to individuals or businesses that violate these guidelines, and may work with law enforcement in the event that the violation is unlawful.
</p>
            </div>
            <div class="ownerblock1">
              <strong>Supported browsers</strong>
              <p>Scanflip works best when you're using the latest version of one of our supported browsers:</p>
              <ul>
                 <li>Chrome</li>
                 <li>Firefox</li>
                 <li>Safari</li>
                 <li> Internet Explorer</li>
                </ul>
                <p>Note that if you're not using one of the latest versions of these browsers, Scanflip Application may not display or function properly.</p>
            </div>
            
            
        </div>
      </div>  
   </div>

<!---------start footer--------------->
       <div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>
		<!--end of footer--></div>
	
</div>





</body>
</html>
