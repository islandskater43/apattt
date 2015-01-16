<?php

class apaAccount {

	public $sellerID;
	public $sellerName;
	public $clientID;
	public $scope;
	public $mwsAccessKey;
	public $mwsSecretKey;
	public $publicKey;
	public $sandbox;

	private function loadData ($ID,$Name,$Client,$Scope,$Access,$Secret,$sbox) {
		$this->sellerID = $ID;
		$this->sellerName = $Name;
		$this->clientID = $Client;
		$this->scope = $Scope;
		$this->mwsAccessKey = $Access;
		$this->mwsSecretKey = $Secret;
		$this->sandbox = $sbox;
	} 

	public function loadSellerByPublicKey($db,$publicKey) {
		$sql = sprintf("select * from apa_account where public_key = '%s'",$publicKey);
		$res = $db->query($sql);
		if(mysqli_num_rows($res) == 0) {
			mysqli_free_result($res);
			$this->loadSellerByPublicKey($db,'mattsStoreSB');
		} else {
			while($d = mysqli_fetch_assoc($res)) {
				$this->loadData($d['seller_id'],$d['seller_name'],$d['client_id'],$d['scope'],$d['mws_access_key'],$d['mws_secret_key'],$d['sandbox']);
			}
			mysqli_free_result($res);
		}
	}

	public function getSellerID () {
		return $this->sellerID;
	}

	public function getSellerName() {
		return $this->sellerName;
	}

	public function getClientID () {
		return $this->clientID;
	}

	public function getScope () {
		return $this->scope;
	}

	public function getMwsAccessKey () {
		return $this->mwsAccessKey;
	}

	public function getMwsSecretKey () {
		return $this->mwsSecretKey;
	}

	public function getIsSandbox() {
		// if sandbox = 1, return true, else false		
		if($this->sandbox == 1) return true;
		return false;
	}
}
?>
