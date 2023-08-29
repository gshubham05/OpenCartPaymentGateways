<?php

class PhonepeChecksumPG{

	static public function phonepeCalculateChecksum($params, $key, $index){
		$phonepestring = $params . "/pg/v1/pay" .  $key ;
		$hashstring = hash('sha256', $phonepestring) ;
		$hashedstring = $hashstring . "###" . $index ;
		return $hashedstring ;
	}

	static public function phonepeStatusCalculateChecksum($merchantId, $txnid, $key, $index){
		$phonepestring = "/pg/v1/status/" . $merchantId . "/" . $txnid . $key ;
		$hashstring = hash('sha256', $phonepestring) ;
		$hashedstring = $hashstring . "###" . $index ;
		return $hashedstring ;
	}

}

?>