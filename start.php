<?php

	// grab key	
	$key = 'mattsStoreSB';
	if(!empty($_REQUEST['key'])) $key = $_REQUEST['key'];

	require_once ("db.php");
	require_once ("Model/apaAccount.php");

	//create account object 
	$account = new apaAccount();

	//fetch seller info
	$account->loadSellerByPublicKey($db,$key);

	// add required files
	//require_once ("OffAmazonPaymentsService/OffAmazonPaymentsService.config.inc.php" );

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
<html>
    <head>
      <script type='text/javascript'>
        window.onAmazonLoginReady = function() {
          amazon.Login.setClientId('<?php echo $account->getClientID(); ?>');
        };
	var loginResponse;
	
      </script>
      <script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js">
      </script>
      <script type="text/javascript" src="jquery.blockUI.js">
      </script>
      <script type='text/javascript' src='<?php echo($jsURL)?>?sellerId=<?php echo $account->getSellerID(); ?>'>
      </script>
	<style>
		table {border-collapse: collapse; border:0px solid black; margin-left:auto; margin-right:auto; margin-top:10px;}
		td {padding:10px;}
		th {padding:10px; text-align:left;}
		h1,h2 {text-align:center;}
		#readOnlyAddressBookWidgetDiv {width: 400px; height: 185px;}
		#readOnlyWalletWidgetDiv {width: 400px; height: 185px;}
	</style>
    </head>
    <body style="text-align:center;">
	<div style="width:1040px; margin-left:auto; margin-right:auto; text-align:left;">
	<h1>Amazon Payments Test Transaction Tool</h1>
	<h2><?= $account->getSellerName() ?></h2>
	<?php if($account->getIsSandbox()) { ?>
	<h2 style="color:red;">Sandbox Mode</h2>
	<?php } ?>
	<p style="text-align:center;">Make sure <strong><?= $jsOriginURL ?></strong> is a white-listed JS Origin for this LwA Client.</p>
	<div style="text-align:center;">
	<table>
	<tr>
		<td style="width:520px;">
		<div id="AmazonPayButton"></div>
		<script type="text/javascript">
		  var authRequest;
		  OffAmazonPayments.Button("AmazonPayButton", "<?php echo $account->getSellerID(); ?>", {
		    type: "PwA",
			authorization: function() {
		      loginOptions =
			{scope: "<?= $account->getScope() ?>", popup: true};
		      authRequest = amazon.Login.authorize (loginOptions, function(response) {
				console.log('login complete - triggering address book widget');
				triggerAddressBook();
				$("#accessToken").val(response.access_token);
			});
		    },
		    onError: function(error) {
		      // your error handling code
		    }
		  });
		</script>
	</td>
	<td align="right">
		<button onclick="verifyLoggedIn()" style="font-size:18px; font-weight:bold; padding: 10px;">Verify Logged In</button>
		<button onclick="amazon.Login.logout(); location.reload();" style="font-size:18px; font-weight:bold; padding: 10px;">Logout</button>
	</td>
	</tr>

	<tr>
	<td>
        <div id="addressBookWidgetDiv"  ></div>
	</td>
	<td width="500">
        <div id="walletWidgetDiv" >
        </div>
	</td></tr>
	<tr>
		<td colspan="2" >
			<div class="confirm" style="font-weight:bold; font-size:1.25em; display:none;">
			Read Only Widgets
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="readOnlyAddressBookWidgetDiv"></div>
		</td>
		<td>
			<div id="readOnlyWalletWidgetDiv"></div>
		</td>
	</tr>
	<tr>
		<td colspan=2>
		<div id="fetchBuyerInfo" style="display:none; margin:10px;">
			<button onclick="fetchBuyerInformation()">Fetch Buyer Info</button>
			<input type="checkbox" id="useConsentToken" /> Use Address Consent Token
		</div>
		<div id="status"></div>
		<div id="address"></div>
		</td>
	</tr>	
	<tr>
		<td colspan=2>
			<div class="confirm" style="display:none; text-align:center;">
				<p>ORO: <input type="text" id="oro" disabled="disabled" name="oro"></p>
				<p>Access Token (Address Consent Token): <input type="text" id="accessToken" disabled="disabled" name="accessToken"></p>
				<p>Order Total:  $<input type="text" name="orderTotal" id="orderTotal" value="10.00" size="6"></p>
				<p>Decline Order? <input type="checkbox" name="declineOrder" id="declineOrder"></p>
				<p>Decline path? <input type="text" name="declinePath" id="declinePath" value="false" READONLY></p>
				<p><button onclick="confirmOrder()" id="confirmButton" style="font-size:18px; font-weight:bold; padding: 10px;">Confirm Order</button></p>
				<p><a href="confirmOrder.phps" target="_blank">View Confirm Order Backend Source Code</a></p>
		</td>

	</tr>
	</table>
		<div id="orderConfContainer" style="display:none">
			<hr />
			<h2>Order Confirmation</h2>
			<div id="orderConf"></div>
		</div>

	</div>
        <script>
            var currentOrderRefID = null;
	function triggerAddressBook() {
          new OffAmazonPayments.Widgets.AddressBook({
            sellerId: '<?php echo $account->getSellerID(); ?>',
            design: {
              size : { 
                width:'500px', 
                height:'300px'
              }
            },
            onOrderReferenceCreate: function(orderReference) {
              console.log("oro="+orderReference.getAmazonOrderReferenceId());
              currentOrderRefID = orderReference.getAmazonOrderReferenceId();
		$("#oro").val(currentOrderRefID);
            },
            onAddressSelect: function(orderReference) {
              // Optionally render the Wallet Widget 
              console.log("contents dumped to console for oro " + currentOrderRefID);
              console.log("cur order ref = " + currentOrderRefID);
              renderWallet();
		//fetchFullShipAddress();
		enableFetchBuyerInfo();
		refreshReadOnlyAddress('<?php echo $account->getSellerID(); ?>',currentOrderRefID);
            },
            onError: function(error) {
              alert("Error on address book widget render: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
            }
          }).bind("addressBookWidgetDiv");
	}

	function renderWallet () {
            new OffAmazonPayments.Widgets.Wallet({
              sellerId: '<?php echo $account->getSellerID(); ?>',
              design: {
                size : { 
                  width:'500px', 
                  height:'300px'
                }
              },
	      amazonOrderReferenceId: currentOrderRefID,
              onPaymentSelect: function(orderReference) {
                console.log("payment method selected!");
		$(".confirm").show();

		// render read only widget
		refreshReadOnlyWallet('<?php echo $account->getSellerID(); ?>',currentOrderRefID);
              },
              
              onError: function(error) {
		alert("Error on wallet widget render: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
              }
            }).bind("walletWidgetDiv");
	}

	function enableFetchBuyerInfo() {
		$("#fetchBuyerInfo").show();
	}

	function fetchBuyerInformation () {
		$("#status").text("Retrieving buyer info...");		
		var url = "fetchBuyerInformation.php";
		$.ajax(url,{
			cache: false,
			data: {
				orderRefId: currentOrderRefID,
				publicKey: '<?= $key ?>',
				token: $("#accessToken").val(),
				useToken: $( "#useConsentToken:checked" ).length
			}
		}).done(function (data) { 
			console.log(data);
			$("#address").html("Shipping Address JSON: <br><br>" + data +"<br><br><a href='fetchBuyerInformation.phps' target='_blank'>View Backend Source Code</a>" );
		}).always(function () { 
			$("#status").text("");		
		});
	}

	function refreshReadOnlyAddress(merchantId, orderRefId) {
	    new OffAmazonPayments.Widgets.AddressBook({
		sellerId: merchantId,
		amazonOrderReferenceId: orderRefId,
		displayMode: "Read",
		design: {
		    size: { width: '500px', height: '185px' }
		},
		onError: function (error) {
		   alert("Error during readonly address book render: " + error.getErrorCode() + error.getErrorMessage());
		}
	    }).bind("readOnlyAddressBookWidgetDiv");
	}
	

	function refreshReadOnlyWallet(merchantId, orderRefId) {
	    new OffAmazonPayments.Widgets.Wallet({
		sellerId: merchantId,
		amazonOrderReferenceId: orderRefId,
		displayMode: "Read",
		design: {
		    size: { width: '500px', height: '185px' }
			/* designMode: 'responsive' */
		},
		onError: function (error) {
		     alert("Error during readonly wallet render: " + error.getErrorCode() + error.getErrorMessage());
			console.dir(error);
		}
	    }).bind("readOnlyWalletWidgetDiv");
	}

	function confirmOrder () {
		$.ajax('confirmOrder.php',{
				data: {
					oroID: currentOrderRefID,
					publicKey: '<?= $key ?>',
					decline: $( "#declineOrder:checked" ).length,
					orderTotal: $("#orderTotal").val(),
					declinePath: $("#declinePath").val()
				}
			}).done(function (data) {

				var result = JSON.parse(data);
				console.dir(result);
				//alert("Result = " + result.status);
				//alert("Type   = " + result.type);

				if(result.status == 'Declined') {
					// the auth was declined - follow decline path
					// re-render wallet widget (note that the order reference ID is bound to the wallet widget in this function
					renderWallet();
					$("#declinePath").val("true");
					alert("The authorize call was not successful. Scroll up to see the Wallet Widget with decline information.");
				} else {
					$("#confirmButton").prop("disabled",true);
					alert("The authorize call was successful!");

				}

				// construct confirmation msg
				var msg = "Amazon returned " + result.status + " in response to the Authorize operation.";
				if (typeof result.captureID != 'undefined' && result.captureID != "") {
					msg += " Amazon Capture ID = " + result.captureID;
				}

				$("#orderConf").html( msg );
				$("#orderConfContainer").show();
			});

	}

	function verifyLoggedIn() {
		var options =
			{scope: "<?= $account->getScope() ?>", popup: true, interactive: 'never' };
		console.log("verifyLoggedIn() - about to call login.authorize - " + new Date());

		// check if we are logged in
		authRequest = amazon.Login.authorize (options, function(response) {
			// this code is executed ASYNCHRONOUSLY
			
			console.log("verifyLoggedIn() - authorize callback - " + new Date());
			if ( response.error ) {
				// USER NOT LOGGED IN
				console.log("verifyLoggedIn() - SESSION NOT ACTIVE - " + new Date());
				alert("Session NOT Active");
			} else {
				// USER IS LOGGED IN
				console.log("verifyLoggedIn() - SESSION ACTIVE - " + new Date());
				alert("Session IS Active");
			}
		});
		console.log("verifyLoggedIn() - Finished call to login.authorize - " + new Date());
	}

	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);


        </script>

	<hr />
	<table>
		<tr>
			<th>Seller ID:</th>
			<td><?= $account->getSellerID() ?></td>
		</tr>
		<tr>
			<th>Login w/ Amazon Client ID:</th>
			<td><?= $account->getClientID() ?></td>
		</tr>
		<tr>
			<th>Login w/ Amazon Scope:</th>
			<td><?= $account->getScope() ?></td>
		</tr>
		<tr>
			<th>MWS Access Key:</th>
			<td><?= $account->getMwsAccessKey() ?></td>
		</tr>
		<tr>
			<th>MWS Secret Key:</th>
			<td>[ not shown ]</td>
		</tr>
		<tr>
			<th>Sandbox?</th>
			<td><?php if($account->getIsSandbox()) echo "Yes"; else echo "No"; ?></td>
		</tr>
	</table>
	</div>
    </body>
</html>
