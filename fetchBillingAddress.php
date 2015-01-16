<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ( "OffAmazonPaymentsService/OffAmazonPaymentsService.config.inc.php" );
require_once ('OffAmazonPaymentsService/Client.php');


$client = new OffAmazonPaymentsService_Client();
$merchantValues = $client->getMerchantValues();


//print("<h1>Fetch Billing Address</h1>");

if(isset($_REQUEST['orderRefId'])) {
    $orderId = $_REQUEST['orderRefId'];
    
    require_once ('OffAmazonPaymentsService/Model/GetOrderReferenceDetailsRequest.php');
    require_once('OffAmazonPaymentsService/Samples/GetOrderReferenceDetailsSample.php');

    $orderRequest = new OffAmazonPaymentsService_Model_GetOrderReferenceDetailsRequest();
    $orderRequest->setAmazonOrderReferenceId($orderId);
    $orderRequest->setSellerId($merchantValues->getMerchantId());
    if(isset($_REQUEST['token'])) {
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
                                //print "                        PhysicalDestination" . PHP_EOL;
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
                        if ($orderReferenceDetails->isSetBillingAddress() && false) {
                        	$billingAddress = $orderReferenceDetails->getBillingAddress();
                        	if ($billingAddress->isSetAddressType()) {
                        		echo ("                        AddressType\n");
                        		echo ("                            " . $billingAddress->getAddressType() . "\n");
                        	}
                        	if ($billingAddress->isSetPhysicalAddress()) {
                        		echo ("                        PhysicalAddress\n");
                        		$physicalAddress = $billingAddress->getPhysicalAddress();
                        		if ($physicalAddress->isSetName()) {
                        			echo ("                            Name\n");
                        			echo ("                                " . $physicalAddress->getName() .
                        					"\n");
                        		}
                        		if ($physicalAddress->isSetAddressLine1()) {
                        			echo ("                            AddressLine1\n");
                        			echo ("                                " .
                        					$physicalAddress->getAddressLine1() . "\n");
                        		}
                        		if ($physicalAddress->isSetAddressLine2()) {
                        			echo ("                            AddressLine2\n");
                        			echo ("                                " .
                        					$physicalAddress->getAddressLine2() . "\n");
                        		}
                        		if ($physicalAddress->isSetAddressLine3()) {
                        			echo ("                            AddressLine3\n");
                        			echo ("                                " .
                        					$physicalAddress->getAddressLine3() . "\n");
                        		}
                        		if ($physicalAddress->isSetCity()) {
                        			echo ("                            City\n");
                        			echo ("                                " . $physicalAddress->getCity() .
                        					"\n");
                        		}
                        		if ($physicalAddress->isSetCounty()) {
                        			echo ("                            County\n");
                        			echo ("                                " . $physicalAddress->getCounty() .
                        					"\n");
                        		}
                        		if ($physicalAddress->isSetDistrict()) {
                        			echo ("                            District\n");
                        			echo ("                                " .
                        					$physicalAddress->getDistrict() . "\n");
                        		}
                        		if ($physicalAddress->isSetStateOrRegion()) {
                        			echo ("                            StateOrRegion\n");
                        			echo ("                                " .
                        					$physicalAddress->getStateOrRegion() . "\n");
                        		}
                        		if ($physicalAddress->isSetPostalCode()) {
                        			echo ("                            PostalCode\n");
                        			echo ("                                " .
                        					$physicalAddress->getPostalCode() . "\n");
                        		}
                        		if ($physicalAddress->isSetCountryCode()) {
                        			echo ("                            CountryCode\n");
                        			echo ("                                " .
                        					$physicalAddress->getCountryCode() . "\n");
                        		}
                        		if ($physicalAddress->isSetPhone()) {
                        			echo ("                            Phone\n");
                        			echo ("                                " . $physicalAddress->getPhone() .
                        					"\n");
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
    
}

?>
