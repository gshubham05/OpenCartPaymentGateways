<?php
require_once(DIR_SYSTEM . 'library/phonepePG/PhonepeConfig.php');

class PhonepeHelperPG{

	/**
	* include timestap with order id
	*/
	public static function getPhonepeOrderId($order_id){
		if($order_id && PhonepeConstantsPG::APPEND_TIMESTAMP){
			return $order_id . '_' . date("YmdHis");
		}else{
			return $order_id;
		}
	}
	/**
	* exclude timestap with order id
	*/
	public static function getOrderId($order_id){		
		if(($pos = strrpos($order_id, '_')) !== false && PhonepeConstantsPG::APPEND_TIMESTAMP) {
			$order_id = substr($order_id, 0, $pos);
		}
		return $order_id;
	}

	/**
	* exclude timestap with order id
	*/
	public static function getTransactionURL($isProduction = 0){		
		if($isProduction == 1){
			return PhonepeConstantsPG::TRANSACTION_URL_PRODUCTION;
		}else{
			return PhonepeConstantsPG::TRANSACTION_URL_STAGING;			
		}
	}
	

	public static function getScript($isProduction = 0){		
		if($isProduction == 1){
			return PhonepeConstantsPG::Prod_Script;
		}else{
			return PhonepeConstantsPG::Test_Script;			
		}
	}
	/**
	* exclude timestap with order id
	*/
	public static function getTransactionStatusURL($isProduction = 0){		
		if($isProduction == 1){
			return PhonepeConstantsPG::TRANSACTION_STATUS_URL_PRODUCTION;
		}else{
			return PhonepeConstantsPG::TRANSACTION_STATUS_URL_STAGING;			
		}
	}
	/**
	* check and test cURL is working or able to communicate properly with Phonepe
	*/
	public static function validateCurl($transaction_status_url = ''){		
		if(!empty($transaction_status_url) && function_exists("curl_init")){
			$ch 	= curl_init(trim($transaction_status_url));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
			$res 	= curl_exec($ch);
			curl_close($ch);
			return $res !== false;
		}
		return false;
	}

	public static function getCurlVersion(){		
		if(function_exists('curl_version')){
			$curl_version = curl_version();
			if(!empty($curl_version['version'])){
				return $curl_version['version'];
			}
		}
		return false;
	}

	public static function executeCurl($apiURL, $requestParamList) {
		$responseParamList = array();
		$JsonData = json_encode($requestParamList);
		$postData = 'JsonData='.urlencode($JsonData);
		$ch = curl_init($apiURL);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PhonepeConstantsPG::CONNECT_TIMEOUT);
		curl_setopt($ch, CURLOPT_TIMEOUT, PhonepeConstantsPG::TIMEOUT);
		
		/*
		** default value is 2 and we also want to use 2
		** so no need to specify since older PHP version might not support 2 as valid value
		** see https://curl.haxx.se/libcurl/c/CURLOPT_SSL_VERIFYHOST.html
		*/

		// TLS 1.2 or above required

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json', 
			'Content-Length: ' . strlen($postData))
		);
		$jsonResponse = curl_exec($ch);   

		if (!curl_errno($ch)) {
			return json_decode($jsonResponse, true);
		} else {
			return false;
		}
	}

public static function executeCurlPhonePe($apiURL, $requestParamList, $xverify) {
		$responseParamList = array();

        
        $JsonData = json_encode($requestParamList);
        
        $encodedPayload = base64_encode($JsonData);
 
        $reqParams = array("request" => $encodedPayload);

        $postData = json_encode($reqParams);
		
		$ch = curl_init($apiURL);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PhonepeConstantsPG::CONNECT_TIMEOUT);
		curl_setopt($ch, CURLOPT_TIMEOUT, PhonepeConstantsPG::TIMEOUT);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json', 
			'Content-Length: ' . strlen($postData),
		    'X-VERIFY: '. $xverify ,
			'X-MERCHANT-DOMAIN: https://'. $_SERVER['SERVER_NAME'],
		    'X-OPENCART-PLUGIN: ' . '1.0.0'

		)
		);
		$jsonResponse = curl_exec($ch);   

		if (!curl_errno($ch)) {
			return json_decode($jsonResponse, true);
		} else {
			return false;
		}
	}

	public static function executeCurlPhonePeStatus($apiURL, $xverify, $mId, $txnid) {
		$apiURL = $apiURL . $mId . "/" . $txnid ;
		$ch = curl_init($apiURL);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PhonepeConstantsPG::CONNECT_TIMEOUT);
		curl_setopt($ch, CURLOPT_TIMEOUT, PhonepeConstantsPG::TIMEOUT);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json', 
			'X-MERCHANT-ID:'.$mId,
		    'X-VERIFY: '. $xverify)
		);
		$jsonResponse = curl_exec($ch);   

		if (!curl_errno($ch)) {
			return json_decode($jsonResponse, true);
		} else {
			return false;
		}
	}

	public static function handleCallback($payload, $headers, $merchant_key, $key_index){
		$checksum = $headers["HTTP_X_VERIFY"];
    
        $data = base64_decode($payload);
        $data = json_decode($data, true);

        //SHA256(response + salt key) + ### + salt index
        $hashString = hash('sha256', $payload.$merchant_key) ; 
        
        $hashedString = $hashString . "###" . $key_index ;

        $generated_checksum = $hashedString;
        if($checksum != $generated_checksum) return null;
        return $data;
	}

	

    public static function calculateEventChecksum($params, $key, $index){
        $phonepeString = $params . "/plugin/ingest-event" .  $key ;
   
        $hashString = hash('sha256', $phonepeString) ; 
       
        $hashedString = $hashString . "###" . $index ;
        return $hashedString ;
    }

	public static function getEventUrl($isProduction = 0){      
        if($isProduction == 1){
            return PhonepeConstantsPG::EVENTS_PUSH_URL_PROD;
        }else{
            return PhonepeConstantsPG::EVENTS_PUSH_URL_STAGING;            
        }
    }

    public static function pushEvents($apiURL, $requestParamList, $xVerify ) {
        $responseParamList = array();
        
        $jsonData = json_encode($requestParamList);
        
        $encodedPayload = base64_encode($jsonData);

        $reqParams = array("request" => $encodedPayload);

        $postData = json_encode($reqParams);

        $ch = curl_init($apiURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PhonepeConstantsPG::TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, PhonepeConstantsPG::TIMEOUT);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', 
            'Content-Length: ' . strlen($postData),
            'X-VERIFY: '. $xVerify , 
            //'X-OPENCART-PLUGIN: ' . '1.0.1'
        )
        );
        $jsonResponse = curl_exec($ch);   

        if (!curl_errno($ch)) {
            return json_decode($jsonResponse, true);
        } else {
            return false;
        }
    }

}

?>