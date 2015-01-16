<?php

	if(empty($_REQUEST['publicKey'])) die ("Key is required!");
	if(empty($_REQUEST['oroID'])) die ("ORO ID is required!");
	if(empty($_REQUEST['orderTotal'])) die("orderTotal is required!");

	if(!is_numeric($_REQUEST['orderTotal'])) die("orderTotal must be a valid number!");

	$key = $_REQUEST['publicKey'];
	
	// set the decline flag for testing
	$decline = false;
	if(isset($_REQUEST['decline']) && $_REQUEST['decline'] == 1) {
		$decline = true;
	}

	// set the decline path flag
	$declinePath = false;
	if(isset($_REQUEST['declinePath']) && $_REQUEST['declinePath'] == 'true') {
		$declinePath = true;
	}

	// connect to DB (Optional)
	require_once ("db.php");

	// add required files
	require_once ("OffAmazonPaymentsService/OffAmazonPaymentsService.config.inc.php" );
	require_once ("OffAmazonPaymentsService/Client.php");

	// are these required???
	require_once ("OffAmazonPaymentsService/Model/OrderReferenceAttributes.php");
	require_once ("OffAmazonPaymentsService/Model/OrderTotal.php");
	require_once ("OffAmazonPaymentsService/Model/Price.php");

	// create OffAmazonPayments Client
	$client = new OffAmazonPaymentsService_Client();
	$merchantValues = $client->getMerchantValues();

	// d
	$oro = $_REQUEST['oroID'];

	if(!$declinePath) {

		// Set Order Reference Details

		$setOrderReferenceDetailsRequest = new OffAmazonPaymentsService_Model_SetOrderReferenceDetailsRequest();
		$setOrderReferenceDetailsRequest->setSellerId($merchantValues->getMerchantId());
		$setOrderReferenceDetailsRequest->setAmazonOrderReferenceId($oro);
		$setOrderReferenceDetailsRequest->setOrderReferenceAttributes(new OffAmazonPaymentsService_Model_OrderReferenceAttributes());
		$setOrderReferenceDetailsRequest->getOrderReferenceAttributes()->setOrderTotal(new OffAmazonPaymentsService_Model_OrderTotal());
		$setOrderReferenceDetailsRequest->getOrderReferenceAttributes()->getOrderTotal()->setCurrencyCode("USD");
		$setOrderReferenceDetailsRequest->getOrderReferenceAttributes()->getOrderTotal()->setAmount($_REQUEST['orderTotal']);
		$setOrderReferenceDetailsRequest->getOrderReferenceAttributes()->setSellerNote("This transaction kicks off additional compliance checks. Please reach out to your Amazon Payments Integration Manager for more information.");

		$setOrderResult = $client->setOrderReferenceDetails($setOrderReferenceDetailsRequest);

		debugOut("Set Order Reference Details Request",$setOrderReferenceDetailsRequest) ;
		debugOut("Set Order Reference Details Response",$setOrderResult);

	}
		
	// Confirm Order

	$confirmOrderReferenceRequest = new OffAmazonPaymentsService_Model_ConfirmOrderReferenceRequest();
	$confirmOrderReferenceRequest->setAmazonOrderReferenceId($oro);
	$confirmOrderReferenceRequest->setSellerId($merchantValues->getMerchantId());

	$confirmOrderResult = $client->confirmOrderReference($confirmOrderReferenceRequest);

	debugOut("Confirm Order Reference Details Request",$confirmOrderReferenceRequest) ;
	debugOut("Confirm Order Reference Details Response",$confirmOrderResult) ;

	// Authorize order

	$authorizeRequest = new OffAmazonPaymentsService_Model_AuthorizeRequest();
	$authorizeRequest->setSellerId($merchantValues->getMerchantId());
	$authorizeRequest->setAmazonOrderReferenceId($oro);
	$authorizeRequest->setAuthorizationReferenceId($oro."-Auth-" . substr(md5($oro . microtime() ),-4));
	if($decline) {
		// send in the special seller auth note to decline the order
		$authorizeRequest->setSellerAuthorizationNote('{"SandboxSimulation": {"State":"Declined", "ReasonCode":"InvalidPaymentMethod", "PaymentMethodUpdateTimeInMins":100}}');
	} else {
		$authorizeRequest->setSellerAuthorizationNote("This transaction kicks off additional compliance checks. Please reach out to your Amazon Payments Integration Manager for more information.");
	}

	// expects a price object
	$price = new OffAmazonPaymentsService_Model_Price();
	$price->setAmount($_REQUEST['orderTotal']);
	$price->setCurrencyCode("USD");

	$authorizeRequest->setAuthorizationAmount($price);
	
	$authorizeRequest->setTransactionTimeout(0);
	$authorizeRequest->setCaptureNow(true);

	$authorizeResult = $client->authorize($authorizeRequest);

	debugOut("Authorize Request",$authorizeRequest);
	debugOut("Authorize Response",$authorizeResult);

	$AuthorizationStatus = $authorizeResult->getAuthorizeResult()->getAuthorizationDetails()->getAuthorizationStatus()->getState();

	// returns OffAmazonPaymentsService_Model_IdList Object
	$captureIDList = $authorizeResult->getAuthorizeResult()->getAuthorizationDetails()->getIdList();
	debugOut("Authorize CaptureID List",$captureIDList);

	// fetch id
	$captureIDArray = $captureIDList->getmember();
	$captureID = $captureIDArray[0];

	debugOut("CaptureID",$captureID);

	//print "Success!";

	print '{ "status": "' . $AuthorizationStatus . '", "oroID": "' . $oro . '" , "captureID":"' . $captureID . '"}';

	function debugOut($text,$var) {

		error_log($text . ": " . print_r($var,true));
		// if not in debug mode, exit
		if(!isset($_REQUEST['debug'])) return;
	
		// output debug data
		print "<hr><h2>". $text . "</h2><pre>";
		print_r($var);
		print "</pre>";
	}

?>
