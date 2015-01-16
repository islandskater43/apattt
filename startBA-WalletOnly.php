<?php

	// grab key	
	$key = 'mattsStoreSB';
	if(!empty($_REQUEST['key'])) $key = $_REQUEST['key'];

	// connect to DB (Optional)
	require_once ("db.php");

	// set URL for widgets.js depending on sandbox status
	$jsURL = "https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js";
	if($account->getIsSandbox()) {
		$jsURL = "https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js";
	}

	// build javascript origin url
	$jsOriginURL = "";
	if(empty($_SERVER['HTTPS'])) {
		$jsOriginURL .= 'http://';
	} else {
		$jsOriginURL .= 'https://';
	}
	$jsOriginURL .= $_SERVER['HTTP_HOST'];

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		
		<!--
		<?= $account->getSellerName() ?> INFO:
		SellerID: <?= $account->getSellerID() ?>
		ClientID: <?= $account->getClientID() ?>
		MWS: <?= $account->getMwsAccessKey() ?> 


		==== Billing Agreement Generator - Generic File ====
		Here is a list of lines of codes that need to be updated to include your specific information:
		
		LINE 108: Insert your client ID from Seller Central
		LINE 116: Insert your Seller ID from Seller Central (sometimes called "Merchant ID")
		LINE 133: Insert your Seller ID
		LINE 170: Insert your Seller ID
		LINE 199: Insert URL of home page
		LINE 220: Insert your Seller ID
		
		Make sure to register your site in Seller Central in the "Allowed JavaScript Origins"	
		-->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
		<title>Billing Agreement Generator</title>
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<!-- loading all styling here instead of separate sheet -->
		<style>
			/* RESETS */
			body, body * {
				margin: 0;
				padding: 0;
			}			
			body {
				font-family: Helvetica, Arial, sans-serif;
				font-size: 14px;	
				line-height: 1.4; 
			}
			.wrapper{	
				width: 960px;
				margin: 20px auto;
				padding-bottom: 50px;
			}
			.col1of2{
				width: 450px;
				margin-right: 20px;
				float: left;
				margin-bottom: 20px;
			}
			.col2of2{
				width: 450px;
				float: left;
				margin-bottom: 20px;
			}
			.col1of2a{
				width: 600px;
				margin-right: 20px;
				float: left;
				margin-bottom: 20px;
			}
			.col2of2a{
				width: 300px;
				float: left;
				margin-bottom: 20px;
			}
			#baBox{
				padding-left: 80px;
				margin-top: 20px;
			}
			#logout{
				width: 75px;
				height: 35px;
				margin-bottom: 20px;
			}
			small{
				color: blue;
			}
			.buttonbox{
				float: left;
				margin-right: 10px;
				margin-bottom: 10px;
			}
			.clear{
				clear:both;
			}
			.amazonWidgets{
				border: dashed lightgray 1px;
				width: 400px;
				height: 260px;
				margin-bottom: 20px;
			}
			.amazonReadOnlyWidgets{
				border: dashed lightgray 1px;
				width: 400px;
				height: 185px;
				margin-bottom: 20px;	
			}
			.red{
				color: red;
			}
			p{
				margin-top: 10px;
			}			
		</style>
		
<script>
	var globalAmazonBillingAgreementId ;
</script>
      <script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js">
      </script>

		<!-- Client ID is from "Login with Amazon" page of Amazon Seller Central -->
		<script type='text/javascript'>
			window.onAmazonLoginReady = function() {
				amazon.Login.setClientId('<?= $account->getClientID() ?>'); 	//Replace with your Client ID from Seller Central
																						//you created this under Login with Amazon
				amazon.Login.setUseCookie(true);
				console.dir('amazon.Login Object...');
				console.dir(amazon.Login);
			};
		</script>
		
		<!-- seller ID is merchant ID, this can be found under Seller Central, Settings, Integration Settings -->
		<script type='text/javascript' src='<?= $jsURL ?>?sellerId=<?= $account->getSellerID() ?>'></script>

	</head>
	<body>
		<div class="wrapper">
			<h1>Billing Agreement Generator - <?= $account->getSellerName() ?></h1><br>
			<?php if($account->getIsSandbox()) { ?>
			<h2 style="color:red;">Sandbox Mode</h2>
			<?php } ?>
			<p style="text-align:center;">Make sure <strong><?= $jsOriginURL ?></strong> is a white-listed JS Origin for this LwA Client.</p>
			
			<!-- === top row === -->
			<div class="col1of2">
				<h3 class="red">Step 1: Click</h3>
				<div class="buttonbox">
					<div id="AmazonPayButton">&nbsp;</div> <!-- / button -->					
				</div>
				
				<!-- script for Amazon button, be sure to include your Seller ID -->
	            <script type="text/javascript">
	                OffAmazonPayments.Button("AmazonPayButton", "<?= $account->getSellerID() ?>", { 
	                    type: "PwA",
	                    size: 'small',
	                    authorization: function() {
	                        loginOptions = {scope: "<?= $account->getScope() ?>", popup: true}; 
	                        
	                        //after login is complete, this calls function to display the address book widget
	                        authRequest = amazon.Login.authorize(loginOptions, doLoginStuff); 
	                        //console.dir('authRequest...');
	                        //console.dir(authRequest);
	                    },
	                    onError: function(error) {
	                    	console.dir('error Object...');
	                    	console.dir(error);
	                    	alert('There\'s an error!');
	                    	//your error handling code
	                    }, 
	                });

	                function doLoginStuff(resp) {
	                	console.dir('doLoginStuff resp...');
	                	console.dir(resp);
	                	//openAddressBook();
				openWallet();
	                }
	            </script>	
	            	
			</div><!-- / col1of2 top -->
			
			<div class="col2of2"  style="text-align:right">
				<h3 class="red">Step 2: Billing Agreement is displayed below</h3>
				<div id="baBox">
					<p><a href="#" onclick="amazon.Login.logout(); return false">Logout</a></p>
					<h3>Billing Agreement ID:</h3>
					<div id="ba"></div>					
				</div>																	
			</div><!-- / col2of2 top -->
			
			<!-- === bottom row === -->
			<div class="col1of2 clear">		
						
				<!-- === address widget === -->
				<div class="amazonWidgets">
					<div id="addressBookWidgetDiv">Address Book Widget</div>										
				</div>

				<!-- script to load Address Book Widget -->
	            <script>
					function openAddressBook(){
		                new OffAmazonPayments.Widgets.AddressBook({ 
							sellerId: '<?= $account->getSellerID() ?>',
							agreementType: 'BillingAgreement',
							onBillingAgreementCreate: function(billingAgreement) {
								var ba = billingAgreement;
								console.dir('billingAgreement Object...');
								console.dir(ba);
								var baID = billingAgreement.getAmazonBillingAgreementId();
								globalAmazonBillingAgreementId = baID;
								//takes the stored BillingAgreement and displays it on page
								document.getElementById("ba").innerHTML = baID;
							},           
		                    onAddressSelect: function() {
		                    	//once an address has been selected (selects first by default),
		                    	//calls function to open the Wallet Widget
		                        openWallet();
		                    },
		                    design: {
		                        size : {width:'400px', height:'260px'}
		                    },
		                    onError: function(error) {
		                    	console.dir('error Object...');
	                    		console.dir(error);
	                    		alert('There\'s an error!');
		                    } 
		                }).bind("addressBookWidgetDiv");
					}
	            </script>
								
				<div><!-- logout button -->
					<input type="submit" name="logout" id="logout" value="Logout!">
					
					<!-- script for logout button, includes redirect to initial page, make sure to customize that URL -->
		            <script type="text/javascript"> 
		                document.getElementById('logout').onclick = function() {
		                	console.dir('amazon.Login Object...');
		                	console.dir(amazon.Login);
		                    amazon.Login.logout(); 
		                    window.location="";
		                };
		            </script>
				</div>
				<div>
					<h3>To Generate the BillingAgreement</h3>
					<p>Step One: Click the Login with Amazon button. It will request an email address and a password. Since this is in Sandbox, you need to use the credentials of a test account created in Seller Central.</p>
					<p>Step Two: If login is successful, the BillingAgreement will be displayed in the upper right above the Wallet Widget.</p>
				</div>		
			</div><!-- / col1of2 bottom -->
			
			<div class="col2of2">				
				<!-- wallet widget -->
				<div class="amazonWidgets">
					<div id="walletWidgetDiv">Wallet Widget</div>
				</div>	
				
				<!-- script to load the Wallet Widget -->
				<script>
					//var amznBillingAgreementID;
					function openWallet(){
						new OffAmazonPayments.Widgets.Wallet({
							sellerId: '<?= $account->getSellerID() ?>',
							onReady: function(billingAgreement) {
								//var billingAgreementId = billingAgreement.getAmazonBillingAgreementId();
								//var ba = billingAgreement;
								//console.dir('billingAgreement Object...');
								//console.dir(ba);
								var baID = billingAgreement.getAmazonBillingAgreementId();
								globalAmazonBillingAgreementId = baID;
								//takes the stored BillingAgreement and displays it on page
								document.getElementById("ba").innerHTML = baID;
								$("#amznObjID").val(globalAmazonBillingAgreementId);
							},
							agreementType: 'BillingAgreement',
							amazonBillingAgreementId: globalAmazonBillingAgreementId,
							onPaymentSelect: function() {
								// Replace this code with the action that you want to perform
								// after the payment method is selected.
								openConsent();
							},
							design: {
								size : {width:'400px', height:'260px'}
							},		
							onError: function(error) {
								// your error handling code			    						    
							}
						}).bind("walletWidgetDiv");	
					}
				</script>

				<!-- consent widget -->
				<div class="amazonWidgets">
					<div id="consentWidgetDiv">Consent Widget</div>
				</div>

				<script>
				function openConsent(){
					new OffAmazonPayments.Widgets.Consent({
					  sellerId: '<?= $account->getSellerID() ?>',
					  // amazonBillingAgreementId obtained from the Amazon Address Book widget. 
					  amazonBillingAgreementId: globalAmazonBillingAgreementId, 
					  design: {
					    size : {width:'400px', height:'140px'}
					  },
					  onReady: function(billingAgreementConsentStatus){
					    // Called after widget renders
					    buyerBillingAgreementConsentStatus =
					      billingAgreementConsentStatus.getConsentStatus();
					    // getConsentStatus returns true or false
					    // true – checkbox is selected
					    // false – checkbox is unselected - default
					  },
					  onConsent: function(billingAgreementConsentStatus) {
					    buyerBillingAgreementConsentStatus =
					      billingAgreementConsentStatus.getConsentStatus();
					    // getConsentStatus returns true or false
					    // true – checkbox is selected – buyer has consented
					    // false – checkbox is unselected – buyer has not consented

					    // Replace this code with the action that you want to perform
					    // after the consent checkbox is selected/unselected.
					   },
					  onError: function(error) {
					    // your error handling code
						console.log(error.getErrorMessage());
					   }
					}).bind("consentWidgetDiv ");
				}
				</script>

			</div><!-- / col2of2 bottom -->

			<div style="clear:both;"></div>
		</div> <!-- / wrapper -->
		<div class="wrapper" style="clear:left;">
			<h3>Advanced values:</h3>
			<p>Object ID: <input type="text" id="amznObjID" value=""></p>
			<p>Object Type: <input type="text" id="amznObjType" value="ba"></p>
			<p>Decline path active?: <input type="text" id="amznDeclinePath" name="amznDeclinePath" value="false"></p>
			Billing Agreement Amount: <input type="text" id="orderAmount" name="amount" /><br />
			<p>Decline Order? <input type="checkbox" name="declineOrder" id="declineOrder"></p>
			<p>Create ORO? <input type="checkbox" name="createORO" id="createORO"></p>
		</div>
		<div class="wrapper">
			<input type="button" id="submitButton" value="Submit!" />
		</div>


	<script type="text/javascript">

		
		$("#submitButton").click(function () {
			//alert('start submit');
			$.ajax({
				url: 'confirmOrderBA.php',
				data: {
					baID: $("#ba").text(),
					publicKey: '<?= $key ?>',
					decline: $( "#declineOrder:checked" ).length,
					createORO: $( "#createORO:checked" ).length,
					orderTotal: $("#orderAmount").val(),
					amznDeclinePath: $("#amznDeclinePath").val(),
					amznObjectType: $("#amznObjType").val(),
					amznObjectID: $("#amznObjID").val()
				}
			}).done(function (data,status,jqxhr) {

				console.dir(data);
				alert(data);
				var result = JSON.parse(data);
				console.dir(result);
				alert("Result = " + result.status);
				alert("Type   = " + result.type);

				if(result.status == 'Declined') {
					// re-render wallet
					if(result.type=="oro") {
						// it's no longer a billing agreement 
						$("#amznObjID").val(result.oroID);
						$("#amznObjType").val("oro");
						$("#amznDeclinePath").val("true");
						// re-render wallet using ORO

						new OffAmazonPayments.Widgets.Wallet({
							sellerId: '<?= $account->getSellerID() ?>',
							/*onReady: function(billingAgreement) {
								//var billingAgreementId = billingAgreement.getAmazonBillingAgreementId();
								//var ba = billingAgreement;
								//console.dir('billingAgreement Object...');
								//console.dir(ba);
								var baID = billingAgreement.getAmazonBillingAgreementId();
								globalAmazonBillingAgreementId = baID;
								//takes the stored BillingAgreement and displays it on page
								document.getElementById("ba").innerHTML = baID;
							},*/
							//agreementType: 'BillingAgreement',
							//amazonBillingAgreementId: globalAmazonBillingAgreementId,
							amazonOrderReferenceId: result.oroID,
							onPaymentSelect: function() {
								// Replace this code with the action that you want to perform
								// after the payment method is selected.
								//openConsent();
							},
							design: {
								size : {width:'400px', height:'260px'}
							},		
							onError: function(error) {
								// your error handling code			    						    
							}
						}).bind("walletWidgetDiv");	


					} else {
						// it's still a billing agreement - just re-render wallet
						openWallet();
					}
				}
			});
		});

	</script>

	</body>	
</html>
