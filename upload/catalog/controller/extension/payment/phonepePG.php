<?php
require_once(DIR_SYSTEM . 'library/phonepePG/TransactionUtils.php');
require_once(DIR_SYSTEM . 'library/phonepePG/ChecksumUtils.php');

class ControllerExtensionPaymentPhonepePG extends Controller {

    public function index() {
        
        $this->load->language('extension/payment/phonepePG');
        $this->load->model('extension/payment/phonepePG');
        $this->load->model('checkout/order');

        $this->session->data['error'] = "Your Transaction has been Cancelled";

        $data['site_url'] = $this->config->get('config_url');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $order_id = PhonepeHelperPG::getPhonepeOrderId($order_info['order_id']);
        $this->session->data['txn_id'] = $order_id;

        $cust_id = $email = $mobile_no = "";
        if(isset($order_info['telephone'])){
            $mobile_no = preg_replace('/\D/', '', $order_info['telephone']);
        }

        if(!empty($order_info['email'])){
            $cust_id = $email = trim($order_info['email']);
        } else if(!empty($order_info['customer_id'])){
            $cust_id = $order_info['customer_id'];
        }else{
            $cust_id = "CUST_".$order_id;
        }

        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $amountinpaisa = $amount * 100 ;

        //Pushing Event PAY_BUTTON_CLICKED_ON_MERCHANT_CHECKOUT
            $code = '';
            $this->callEventAPI('PAY_BUTTON_CLICKED_ON_MERCHANT_CHECKOUT', $this->config->get('payment_phonepePG_merchant_id'), $order_id, $amountinpaisa, $code);
        //End
        $parameters = array(
            "merchantId"        => $this->config->get('payment_phonepePG_merchant_id'),
            "merchantTransactionId"     => (string)$order_id,
            "amount"            => $amountinpaisa,
            "merchantUserId"     => "M123456789",
            "redirectUrl"       => $this->url->link('extension/payment/phonepePG/callback'),
            "callbackUrl"       => $this->config->get('config_url').'/index.php?route=extension/payment/phonepePG/pgcallback',
            "paymentInstrument"     => array("type"=> 'PAY_PAGE')
        );

        $myJSON = json_encode($parameters);
        $encodedPayload = base64_encode($myJSON);
        $reqParams = array("request" => $encodedPayload);

        $xverify      = PhonepeChecksumPG::phonepeCalculateChecksum($encodedPayload, $this->config->get('payment_phonepePG_merchant_key'),$this->config->get('payment_phonepePG_key_index'));

        //Pushing Event PAYMENT_REQUEST_TRIGGERED_FROM_PLUGIN
            $code = '';
            $this->callEventAPI('PAYMENT_REQUEST_TRIGGERED_FROM_PLUGIN', $this->config->get('payment_phonepePG_merchant_id'), $order_id, $amountinpaisa, $code);
        //End

        $xcallbackurl = "";
        //$xredirecturl =   $this->url->link('extension/payment/phonepe/callback');  //It is part of request body now
        $txn_url      = PhonepeHelperPG::getTransactionURL($this->config->get('payment_phonepePG_environment'));
        $scriptType   = PhonepeHelperPG::getScript($this->config->get('payment_phonepePG_environment'));

        //Accept Payment API
        //$resParams = PhonepeHelperPG::executeCurlPhonePe($txn_url,$parameters,$xverify,$xcallbackurl,$xredirecturl);
        $resParams = PhonepeHelperPG::executeCurlPhonePe($txn_url,$parameters,$xverify);

        //Pushing Event PAYMENT_RESPONSE_RECEIVED_AT_PLUGIN
            $code = $resParams['code'];
            $this->callEventAPI('PAYMENT_RESPONSE_RECEIVED_AT_PLUGIN', $this->config->get('payment_phonepePG_merchant_id'), $order_id, $amountinpaisa, $code);
        //End
        if($resParams['success']==false){
            //echo $resParams;
            if(!empty($resParams)){
                $this->session->data['error'] = 'Transaction could not be initiated because of '.$resParams['code']. '. Please try again.';
            }
            else{
                $this->session->data['error'] = 'Transaction could not be initiated because of Network issue. Please check network connectivity.';
            }

            $data['action'] = $this->url->link('checkout/cart');
            $data['button_confirm'] = $this->language->get('button_confirm');

            if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/phonepePG'))
            {
                return $this->load->view($this->config->get('config_template') . '/template/extension/payment/phonepePG', $data);
            }
            else
            {
                return $this->load->view('extension/payment/phonepePG', $data);
            }           
        }
        else{ 
            $redirectURL = $resParams['data']['instrumentResponse']['redirectInfo']['url'];
            $data['button_confirm'] = $this->language->get('button_confirm');
            $data['xverify']            = $xverify;
            #$data['xcallbackurl']      = $xcallbackurl;
            $data['xredirecturl']       = $this->url->link('extension/payment/phonepePG/callback');
            $data['fpfields']           = $parameters;
            $data['scriptType']         = $scriptType;
            $data['redirectOnCancel']   = $this->url->link('checkout/cart');
            $data['txn_url']            = $txn_url;
            $data['action']            = $redirectURL;
            $data['ajax']               = $this->url->link('extension/payment/phonepePG/events');


            if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/phonepePG'))
            {
                return $this->load->view($this->config->get('config_template') . '/template/extension/payment/phonepePG', $data);
            }
            else
            {
                return $this->load->view('extension/payment/phonepePGs', $data);
            }

        }
    }

    public function events(){

        if($_POST['event']=="PLUGIN_USER_CANCEL"){
            $this->session->data['error'] = "Your Transaction has been Cancelled";
        }
        
        //$this->load->language('extension/payment/phonepePG');
        //$this->load->model('extension/payment/phonepePG');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $order_id = PhonepeHelperPG::getPhonepeOrderId($order_info['order_id']);

        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $amountinpaisa = $amount * 100 ;

        $eventType = $_POST['event'];
        //Pushing Event for Iframe
            $code = '';
            $this->callEventAPI($eventType, $this->config->get('payment_phonepePG_merchant_id'), $this->session->data['txn_id'], $amountinpaisa, $code);
        //End
    }
    public function pgcallback(){
       
        $this->load->model('extension/payment/phonepePG');
        $this->load->language('extension/payment/phonepePG');
        $this->load->model('checkout/order');
        
        // $order_id    = $this->session->data['txn_id'];

        $merchantId  = $this->config->get('payment_phonepePG_merchant_id');
        //$txnid       = (string)$order_id;
        $saltkey     = $this->config->get('payment_phonepePG_merchant_key');
        $saltindex   = $this->config->get('payment_phonepePG_key_index');

        $payload = file_get_contents('php://input');
        $headers = $_SERVER;
        
        $payload = json_decode($payload, true);
        $decoded_payload = $payload['request'];
        
        $data = PhonepeHelperPG::handleCallback($decoded_payload, $headers, $saltkey, $saltindex);

        try{ 
            $this->logPGOrder($data); 
        }catch(Exception $message) {

        }
    }

    private function logPGOrder($data){         
        $this->load->model('extension/payment/phonepePG'); 
        $this->load->model('checkout/order'); 
        
        $dashboard_order_id = explode("_", $data['data']['merchantTransactionId']);
        $merchant_transaction_id = $data['data']['merchantTransactionId']; 
        $order = $this->model_checkout_order->getOrder($dashboard_order_id[0]);
        
        $transaction_id = $data['data']['transactionId'];
        
        $payment_details = []; 
       
        $order_amount = (float)($data['data']['amount']) / 100;
        $order_state = $data['data']['responseCode']; 

        //$address = $data['address']; 
        try{ 
                if($order_state == "PAYMENT_SUCCESS"){ 
                    //$this->model_extension_payment_phonepePG->editOrder($merchant_transaction_id, $shipping_title, $shipping_code, $shipping_charges, $payment_title, $payment_code, $total, $transaction_id, $address); 
                    $this->model_checkout_order->addOrderHistory($order, $this->config->get('payment_phonepePG_order_success_status_id'), 'Payment successful through PhonePe Checkout'); 
                    //echo "success"; 
                    return;
                }
                else if($order_state == "PAYMENT_FAILED"){ 
                    $this->model_checkout_order->addOrderHistory($order, $this->config->get('payment_phonepePG_order_failed_status_id'), 'Payment failed through PhonePe Checkout - ' + $data['responseCode']); 
                    return "Failed";
                } 
            }catch(Exception $message){ 
                return $message;
            }  
            return $data; 
    }
    /**
     * phonepe sends response to callback
     */
    public function callback(){

        // if(file_get_contents('php://input')){
        //     $this->load->model('extension/payment/phonepePG');
        //     $this->load->language('extension/payment/phonepePG');
        //     $this->load->model('checkout/order');
            
        //    // $order_id    = $this->session->data['txn_id'];

        //     $merchantId  = $this->config->get('payment_phonepePG_merchant_id');
        //     //$txnid       = (string)$order_id;
        //     $saltkey     = $this->config->get('payment_phonepePG_merchant_key');
        //     $saltindex   = $this->config->get('payment_phonepePG_key_index');

        //     $payload = file_get_contents('php://input');
        //     $headers = $_SERVER;
            
        //     $payload = json_decode($payload, true);
        //     $decoded_payload = $payload['request'];
           
        //     $data = PhonepeHelperPG::handleCallback($decoded_payload, $headers, $saltkey, $saltindex);

        //     try{ 
        //         $this->logPGOrder($data); 
        //     }catch(Exception $message) {

        //     }
        // }else{
            $this->load->model('extension/payment/phonepePG');
            $this->load->language('extension/payment/phonepePG');
            $this->load->model('checkout/order');

            $data['title']          = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
            $data['heading_title']  = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
            $data['direction']      = $this->language->get('direction');
            $data['language']       = $this->language->get('code');

            $order_info  = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $order_id    = $this->session->data['txn_id'];

            $merchantId  = $this->config->get('payment_phonepePG_merchant_id');
            $txnid       = (string)$order_id;
            $saltkey     = $this->config->get('payment_phonepePG_merchant_key');
            $saltindex   = $this->config->get('payment_phonepePG_key_index');

            $xverify     = PhonepeChecksumPG::phonepeStatusCalculateChecksum($merchantId, $txnid, $saltkey, $saltindex);
            $txn_url     = PhonepeHelperPG::getTransactionStatusURL($this->config->get('payment_phonepePG_environment'));

            $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
            $amountinpaisa = $amount * 100 ;
            $amount_returned = 0;
            $mycounter=0;
            $dataMsg = '';

            do{
                $resStatus= PhonepeHelperPG::executeCurlPhonePeStatus($txn_url,$xverify,$merchantId,$txnid );
                $mycounter++ ;
                sleep(2);

                //Pushing Event PLUGIN_STATUS_CHECK
                $code = $resStatus['code'] ;
                $this->callEventAPI('PLUGIN_STATUS_CHECK', $merchantId , $this->session->data['txn_id'], $amountinpaisa, $code );
                //End   
            } while(($resStatus['code']==PhonepeConstantsPG::PENDING && $mycounter < PhonepeConstantsPG::MAX_RETRY_COUNT) || ($resStatus['code']==PhonepeConstantsPG::SERVER_ERROR && $mycounter < PhonepeConstantsPG::MAX_RETRY_COUNT));



            if($resStatus['code']==PhonepeConstantsPG::TXN_NOT_FOUND){
                $data['text_response']  = sprintf($this->language->get('text_response'),$resStatus['message'] );
                $data['payment_status'] = $resStatus['code'];
            }
            else
            {
                $data['text_response']  = sprintf($this->language->get('text_response'),$resStatus['message'] );
                $data['payment_status'] = $resStatus['code'];
                $amount_returned = $resStatus['data']['amount'];

                /* save phonepe response in db */
                if(PhonepeConstantsPG::SAVE_PHONEPE_RESPONSE && !empty($resStatus['code'])){
                    $this->model_extension_payment_phonepePG->saveTxnResponse($resStatus, PhonepeHelperPG::getOrderId($resStatus['data']['transactionId']));
                }

            }

            if($resStatus['code'] == PhonepeConstantsPG::SUCCESS && $amountinpaisa==$amount_returned){

                $comment = sprintf($this->language->get('text_transaction_id'), $txnid) .'<br/>'. sprintf($this->language->get('text_phonepe_order_id'), $order_id);
                $statusId = $this->config->get('payment_phonepePG_order_success_status_id');
            }

            else if($resStatus['code'] == PhonepeConstantsPG::PENDING){
                $mycounter=0;
                do{
                $resStatus= PhonepeHelperPG::executeCurlPhonePeStatus($txn_url,$xverify,$merchantId,$txnid );
                $mycounter++ ;
                sleep(60);

                //Pushing Event PLUGIN_STATUS_CHECK
                    $code = $resStatus['code'] ;
                    $this->callEventAPI('PLUGIN_STATUS_CHECK1', $merchantId , $this->session->data['txn_id'] , $amountinpaisa, $code );
                    //End 
                } while(($resStatus['code']==PhonepeConstantsPG::PENDING && $mycounter < PhonepeConstantsPG::MAX_RETRY_COUNT) || ($resStatus['code']==PhonepeConstantsPG::SERVER_ERROR && $mycounter < PhonepeConstantsPG::MAX_RETRY_COUNT));

                $comment = '' ;
                $statusId = $this->config->get('payment_phonepePG_order_pending_status_id');
                $dataMsg = $this->language->get('text_pending');
            }

            else {
                $comment = '';
                $statusId = $this->config->get('payment_phonepePG_order_failed_status_id');
                $dataMsg = $this->language->get('text_failure');
            }

            //$this->addOrderHistory($order_id, $this->config->get('payment_phonepePG_order_success_status_id'), $comment);
            $this->addOrderHistory($order_id, $statusId, $comment);
            $this->session->data['error'] = $dataMsg;
            $this->preRedirect($data);
        //} end of of else
        // load language and model
        
    }


    private function addOrderHistory($order_id, $order_status_id, $comment = ''){
        try{
            $this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $comment);
        } catch(\Exception $e){
        }
    }

    /**
     * show template while response
     */
    private function preRedirect($data){

        $data['continue'] = $this->url->link('checkout/cart');

        $merchantId  = $this->config->get('payment_phonepePG_merchant_id');
        $txnid       = (string)$order_id;
        $saltkey     = $this->config->get('payment_phonepePG_merchant_key');
        $saltindex   = $this->config->get('payment_phonepePG_key_index');

        $xverify     = PhonepeChecksumPG::phonepeStatusCalculateChecksum($merchantId, $txnid, $saltkey, $saltindex);
        $txn_url     = PhonepeHelperPG::getTransactionStatusURL($this->config->get('payment_phonepePG_environment'));

        $order_info  = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $order_id    = $this->session->data['txn_id'];

        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $amountinpaisa = $amount * 100 ;

        if(!empty($data['payment_status'])){
            if($data['payment_status'] == PhonepeConstantsPG::SUCCESS){
                $data['continue']           = $this->url->link('checkout/success');
                $data['text_message']       = $this->language->get('text_success');
                $data['text_message_wait']  = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
            }else if($data['payment_status'] == PhonepeConstantsPG::PENDING){
                $data['text_message']       = $this->language->get('text_pending');
                $data['text_message_wait']  = sprintf($this->language->get('text_pending_wait'), $this->url->link('checkout/cart'));
            }else{
                $data['text_message']       = $this->language->get('text_failure');
                $data['text_message_wait']  = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
            }

        }

        //Pushing Event PLUGIN_HAS_GIVEN_CONTROL_BACK_TO_MERCHANT
            $code = '' ;
            $this->callEventAPI('PLUGIN_HAS_GIVEN_CONTROL_BACK_TO_MERCHANT', $merchantId , $this->session->data['txn_id'], $amountinpaisa, $code ); 
        $this->response->setOutput($this->load->view('extension/payment/phonepePG_response', $data));
    }

    function callEventAPI($evenType, $merchantId , $txnId, $amount, $code){
        $eventParameters = array(
                        "eventType"         => $evenType,
                        "platform"          => "Opencart",      
                        "platformVersion"   => "3.0",
                        "pluginVersion"     => "1.0.1",
                        "flowType"          => "B2B_PG",
                        "merchantId"        => $merchantId,
                        "transactionId"     => $txnId,
                        "amount"            => $amount,
                        "code"              => $code,
                        "groupingKey"       => $merchantId. "-" .$txnId,
                        "network"           => "",
                        "userOperatingSystem"   => $_SERVER['HTTP_USER_AGENT'],
                       );
       //print_r($txnId);
        $jsonData = json_encode($eventParameters);
        $encodedPayload = base64_encode($jsonData);
        //print_r($encodedPayload);
       
        $saltkey     = $this->config->get('payment_phonepePG_merchant_key');
        $saltindex   = $this->config->get('payment_phonepePG_key_index');
        $xVerify   = PhonepeHelperPG::calculateEventChecksum($encodedPayload, $saltkey, $saltindex);    
        //print_r($xVerify);
        $envtypeUrl = PhonepeHelperPG::getEventUrl($this->config->get('payment_phonepePG_environment'));
        
        $r=PhonepeHelperPG::pushEvents($envtypeUrl,$eventParameters,$xVerify);
        //print_r(json_encode($r));
    }

    /**
     * check cURL working or able to communicate with phonepe
     */
    public function curlTest(){

        $debug = array();

        if(!function_exists("curl_init")){
            $debug[0]["info"][] = "cURL extension is either not available or disabled. Check phpinfo for more info.";

            // if curl is enable then see if outgoing URLs are blocked or not
        } else {

            // if any specific URL passed to test for
            if(!empty($this->request->get["url"])){
                $testing_urls = array(urldecode($this->request->get["url"]));
            } else {

                // this site homepage URL
                $server = (!empty($this->request->server['HTTPS'])? HTTPS_SERVER : HTTP_SERVER);

                $testing_urls = array(
                    $server,
                    "https://www.gstatic.com/generate_204",
                    PhonepeConstantsPG::TRANSACTION_STATUS_URL_PRODUCTION,
                    PhonepeConstantsPG::TRANSACTION_STATUS_URL_STAGING
                );
            }

            // loop over all URLs, maintain debug log for each response received
            foreach($testing_urls as $key => $url){

                $debug[$key]["info"][] = "Connecting to <b>" . $url . "</b> using cURL";

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $res = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if (!curl_errno($ch)) {
                    $info1 = "cURL executed succcessfully.";
                    $info2 = "HTTP Response Code: <b>". $http_code . "</b>";
                    $info3 = "";
                } else {
                    $info1 = "Connection Failed !!";
                    $info2 = "Error Code: <b>" . curl_errno($ch) . "</b>";
                    $info3 = "Error: <b>" . curl_error($ch) . "</b>";
                }

                $debug[$key]["info"][] = $info1;
                $debug[$key]["info"][] = $info2;
                $debug[$key]["info"][] = $info3;

                if((!empty($this->request->get["url"])) || (in_array($url, array(PhonepeConstantsPG::TRANSACTION_STATUS_URL_PRODUCTION , PhonepeConstantsPG::TRANSACTION_STATUS_URL_STAGING)))){
                    $debug[$key]["info"][] = "Response: <br/><!----- Response Below ----->" . $res;
                }

                curl_close($ch);
            }
        }

        foreach($debug as $k => $v){
            echo "<ul>";
            foreach($v["info"] as $info){
                echo "<li>". $info ."</li>";
            }
            echo "</ul>";
            echo "<hr/>";
        }
    }
}
?>