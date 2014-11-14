<?php

	// this code uses the PHP SDK for Amazon's Off-Amazon Payments API
	// More information is available here:
	//
	//	https://developer.amazonservices.com/gp/mws/api.html/178-1041648-7709209?ie=UTF8&group=offamazonpayments&section=offamazonpayments&version=latest
	

	// check for required fields
	if(empty($_REQUEST['publicKey'])) die ("Key is required!");
	if(empty($_REQUEST['orderRefId'])) die ("Order Reference ID is required!");

	$key = $_REQUEST['publicKey'];

	// connect to DB (Optional)
	require_once ("db.php");

	// load amazon client
	require_once ("OffAmazonPaymentsService/OffAmazonPaymentsService.config.inc.php");
	require_once ("OffAmazonPaymentsService/Client.php");

	$client = new OffAmazonPaymentsService_Client();
	$merchantValues = $client->getMerchantValues();

	
	$orderId = $_REQUEST['orderRefId'];

	$orderRequest = new OffAmazonPaymentsService_Model_GetOrderReferenceDetailsRequest();
	$orderRequest->setAmazonOrderReferenceId($orderId);
	$orderRequest->setSellerId($merchantValues->getMerchantId());
	if(isset($_REQUEST['token']) && $_REQUEST['useToken'] == 1) {
		$orderRequest->setAddressConsentToken($_REQUEST['token']);
	}


	try {
		$orderRefResponse = $client->getOrderReferenceDetails($orderRequest);

		$data['oroID']=$orderId;

		if ($orderRefResponse->isSetGetOrderReferenceDetailsResult()) { 
			$getOrderReferenceDetailsResult = $orderRefResponse->getGetOrderReferenceDetailsResult();
			if ($getOrderReferenceDetailsResult->isSetOrderReferenceDetails()) { 
				$orderReferenceDetails = $getOrderReferenceDetailsResult->getOrderReferenceDetails();
			        if ($orderReferenceDetails->isSetBuyer()) { 
			            $buyer = $orderReferenceDetails->getBuyer();
			            if ($buyer->isSetName()) 
			            {
					$data['name']=$buyer->getName();
			            }
			            if ($buyer->isSetEmail()) 
			            {
					$data['email']=$buyer->getEmail();
			            }
			            if ($buyer->isSetPhone()) 
			            {
					$data['phone']=$buyer->getPhone();
			            }
			        } 
			        if ($orderReferenceDetails->isSetDestination()) { 
			            $destination = $orderReferenceDetails->getDestination();
			            if ($destination->isSetDestinationType()) 
			            {
					$data['shipDestType']=$destination->getDestinationType();
			            }
			            if ($destination->isSetPhysicalDestination()) { 
			                $physicalDestination = $destination->getPhysicalDestination();
			                if ($physicalDestination->isSetName()) 
			                {
						$data['shipName']=$physicalDestination->getName() ;
			                }
			                if ($physicalDestination->isSetAddressLine1()) 
			                {
						$data['shipAddress1']=$physicalDestination->getAddressLine1() ;
			                }
			                if ($physicalDestination->isSetAddressLine2()) 
			                {
						$data['shipAddress2']=$physicalDestination->getAddressLine2() ;
			                }
			                if ($physicalDestination->isSetAddressLine3()) 
			                {
						$data['shipAddress3']=$physicalDestination->getAddressLine3() ;
			                }
			                if ($physicalDestination->isSetCity()) 
			                {
						$data['shipCity']=$physicalDestination->getCity() ;
			                }
			                if ($physicalDestination->isSetCounty()) 
			                {
						$data['shipCounty']=$physicalDestination->getCounty() ;
			                }
			                if ($physicalDestination->isSetDistrict()) 
			                {
						$data['shipDistrict']=$physicalDestination->getDistrict() ;
			                }
			                if ($physicalDestination->isSetStateOrRegion()) 
			                {
						$data['shipState']=$physicalDestination->getStateOrRegion() ;
			                }
			                if ($physicalDestination->isSetPostalCode()) 
			                {
						$data['shipPostalCode']=$physicalDestination->getPostalCode() ;
			                }
			                if ($physicalDestination->isSetCountryCode()) 
			                {
						$data['shipCountryCode']=$physicalDestination->getCountryCode() ;
			                }
			                if ($physicalDestination->isSetPhone()) 
			                {
						$data['shipPhone']=$physicalDestination->getPhone() ;
			                }
			            } 
			        }
			}
		}

		$json = json_encode((array)$data);
		print($json);
	} catch (Exception $e) {
		print ("<span style='color:red; font-weight:bold;'>An Error Occurred!</span> " . $e->getMessage());
	}
	    
?>
