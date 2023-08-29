<?php

require_once(DIR_SYSTEM . 'library/phonepePG/TransactionUtils.php');
require_once(DIR_SYSTEM . 'library/phonepePG/ChecksumUtils.php');

class ControllerExtensionPaymentPhonepePG extends Controller {

	private $error 	= array();

	/**
	* create `phonepe_order_data` table and install this module.
	*/
	
	public function install() {
		$this->load->model('extension/payment/phonepePG');
		$this->model_extension_payment_phonepePG->install();
	}

	/**
	* drop `phonepe_order_data` table and uninstall this module.
	*/

	public function uninstall() {
		$this->load->model('extension/payment/phonepePG');
		$this->model_extension_payment_phonepePG->uninstall();
	}

	public function index() {

		// load all language variables
		$data = $this->load->language('extension/payment/phonepePG');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');		

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->request->post = array_map('trim', $this->request->post);
			$this->model_setting_setting->editSetting('payment_phonepePG', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if(!PhonepeHelperPG::validateCurl(PhonepeHelperPG::getTransactionStatusURL($this->request->post['payment_phonepePG_environment']))){
				$this->session->data['warning'] = $this->language->get('error_curl_warning');
				$this->response->redirect($this->url->link('extension/payment/phonepePG', 'user_token=' . $this->session->data['user_token'], true));
			}

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$data['warning'] = '';
		}

		if (isset($this->error['merchant_id'])) {
			$data['error_merchant_id'] = $this->error['merchant_id'];
		} else {
			$data['error_merchant_id'] = '';
		}

		if (isset($this->error['merchant_key'])) {
			$data['error_merchant_key'] = $this->error['merchant_key'];
		} else {
			$data['error_merchant_key'] = '';
		}

		if (isset($this->error['key_index'])) {
			$data['error_key_index'] = $this->error['key_index'];
		} else {
			$data['error_key_index'] = '';
		}

		if (isset($this->error['environment'])) {
			$data['error_environment'] = $this->error['environment'];
		} else {
			$data['error_environment'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'	  => $this->language->get('text_home'),
			'href'	  => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
		);

		$data['breadcrumbs'][] = array(
			'text'	  => $this->language->get('text_extension'),
			'href'	  => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true),
		);

		$data['breadcrumbs'][] = array(
			'text'	  => $this->language->get('text_payments'),
			'href'	  => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true),
		);

		$data['breadcrumbs'][] = array(
			'text'	  => $this->language->get('heading_title'),
			'href'	  => $this->url->link('extension/payment/phonepePG', 'user_token=' . $this->session->data['user_token'], true),
		);

		$data['action'] = $this->url->link('extension/payment/phonepePG', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);


		if (isset($this->request->post['payment_phonepePG_merchant_id'])) {
			$data['payment_phonepePG_merchant_id'] = $this->request->post['payment_phonepePG_merchant_id'];
		} else {
			$data['payment_phonepePG_merchant_id'] = $this->config->get('payment_phonepePG_merchant_id');
		}

		if (isset($this->request->post['payment_phonepePG_merchant_key'])) {
			$data['payment_phonepePG_merchant_key'] = $this->request->post['payment_phonepePG_merchant_key'];
		} else {
			$data['payment_phonepePG_merchant_key'] = $this->config->get('payment_phonepePG_merchant_key');
		}
		
		if (isset($this->request->post['payment_phonepePG_key_index'])) {
			$data['payment_phonepePG_key_index'] = $this->request->post['payment_phonepePG_key_index'];
		} else {
			$data['payment_phonepePG_key_index'] = $this->config->get('payment_phonepePG_key_index');
		}

		if (isset($this->request->post['payment_phonepePG_environment'])) {
			$data['payment_phonepePG_environment'] = $this->request->post['payment_phonepePG_environment'];
		} else if ($this->config->get('payment_phonepePG_environment')) {
			$data['payment_phonepePG_environment'] = $this->config->get('payment_phonepePG_environment');
		}else{
			$data['payment_phonepePG_environment'] = 0;
		}

		if (isset($this->request->post['payment_phonepePG_order_success_status_id'])) {
			$data['payment_phonepePG_order_success_status_id'] = $this->request->post['payment_phonepePG_order_success_status_id'];
		} else {
			$data['payment_phonepePG_order_success_status_id'] = $this->config->get('payment_phonepePG_order_success_status_id');
		}

		if (isset($this->request->post['payment_phonepePG_order_pending_status_id'])) {
			$data['payment_phonepePG_order_pending_status_id'] = $this->request->post['payment_phonepePG_order_pending_status_id'];
		} else {
			$data['payment_phonepePG_order_pending_status_id'] = $this->config->get('payment_phonepePG_order_pending_status_id');
		}

		if (isset($this->request->post['payment_phonepePG_order_failed_status_id'])) {
			$data['payment_phonepePG_order_failed_status_id'] = $this->request->post['payment_phonepePG_order_failed_status_id'];
		} else {
			$data['payment_phonepePG_order_failed_status_id'] = $this->config->get('payment_phonepePG_order_failed_status_id');
		}
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_phonepePG_total'])) {
			$data['payment_phonepePG_total'] = $this->request->post['payment_phonepePG_total'];
		} else {
			$data['payment_phonepePG_total'] = $this->config->get('payment_phonepePG_total');
		}

		if (isset($this->request->post['payment_phonepePG_geo_zone_id'])) {
			$data['payment_phonepePG_geo_zone_id'] = $this->request->post['payment_phonepePG_geo_zone_id'];
		} else {
			$data['payment_phonepePG_geo_zone_id'] = $this->config->get('payment_phonepePG_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_phonepePG_status'])) {
			$data['payment_phonepePG_status'] = $this->request->post['payment_phonepePG_status'];
		} else {
			$data['payment_phonepePG_status'] = $this->config->get('payment_phonepePG_status');
		}

		if (isset($this->request->post['payment_phonepePG_sort_order'])) {
			$data['payment_phonepePG_sort_order'] = $this->request->post['payment_phonepePG_sort_order'];
		} else {
			$data['payment_phonepePG_sort_order'] = (int)$this->config->get('payment_phonepePG_sort_order');
		}		

		// Check cUrl is enabled or not
		$data['curl_version'] = PhonepeHelperPG::getCurlVersion();

		if(empty($data['curl_version'])){
			$data['error_warning'] = $this->language->get('text_curl_disabled');
		}

		$data['last_updated'] 		= date("d F Y", strtotime(PhonepeConstantsPG::LAST_UPDATED)) .' - '.PhonepeConstantsPG::PLUGIN_VERSION;
		$data['opencart_version'] 	= VERSION;
		$data['php_version'] 		= PHP_VERSION;

		$data['header'] 			= $this->load->controller('common/header');
		$data['column_left'] 		= $this->load->controller('common/column_left');
		$data['footer'] 			= $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/phonepePG', $data));
	}
	
	/**
	* create tab with phonepe response at Order Detail page
	*/
	public function order() {
		if ($this->config->get('payment_phonepePG_status')) {
			$this->load->model('extension/payment/phonepePG');
			$this->load->language('extension/payment/phonepePG');

			if(!empty($this->request->get['order_id'])){			
				$phonepe_order_data = $this->model_extension_payment_phonepePG->getPhonepeOrderData($this->request->get['order_id']);
				$data = array();
				$data['user_token'] = $this->session->data['user_token'];
				$data['savePhonepeResponse'] = PhonepeConstantsPG::SAVE_PHONEPE_RESPONSE;
				if($phonepe_order_data){
					$data['transaction_id']			= $phonepe_order_data['transaction_id'];
					$this->session->data['trxn_id']  = $phonepe_order_data['transaction_id'];
					$data['phonepe_order_id']		= $phonepe_order_data['phonepe_order_id'];
					$data['order_data_id']			= $phonepe_order_data['id'];
					$data['phonepe_response'] 		= json_decode($phonepe_order_data['phonepe_response'],true);
					return $this->load->view('extension/payment/phonepePG_order', $data);
				}
			}
		}
	}

	/**
	* ajax - fetch and save transaction status in db
	*/

	public function savetxnstatus() {

		$this->load->model('extension/payment/phonepePG');
		$this->load->language('extension/payment/phonepePG');

		$json = array("success" => false, "response" => '', 'message' => $this->language->get('text_response_error'));

		if(!empty($this->session->data['trxn_id']) && PhonepeConstantsPG::SAVE_PHONEPE_RESPONSE){
		    
		    $order_id    = $this->session->data['trxn_id'];
		    $merchantId  = $this->config->get('payment_phonepePG_merchant_id');
		    $txnid       = (string)$order_id;
		    $saltkey     = $this->config->get('payment_phonepePG_merchant_key');
		    $saltindex   = $this->config->get('payment_phonepePG_key_index');

		    $xverify     = PhonepeChecksumPG::phonepeStatusCalculateChecksum($merchantId, $txnid, $saltkey, $saltindex);	
		    $txn_url     = phonepeHelperPG::getTransactionStatusURL($this->config->get('payment_phonepePG_environment'));

		    $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		    $amountinpaisa = $amount * 100 ;
		    $amount_returned = 0;
		    $mycounter=0;
  
            do{	
		        $resStatus= PhonepeHelperPG::executeCurlPhonePeStatus($txn_url,$xverify,$merchantId,$txnid );
		        $mycounter++ ;	
                sleep(2);
		      } while(($resStatus['code']==PhonepeConstantsPG::PENDING && $mycounter < 10) || ($resStatus['code']==PhonepeConstantsPG::SERVER_ERROR && $mycounter < 10));

		    if(!empty($resStatus['code']) && $resStatus['code'] == PhonepeConstantsPG::SUCCESS){
					$response	=	$this->model_extension_payment_phonepePG->saveTxnResponse($resStatus, $this->request->post['order_data_id']); 
					if($response){
						$message = $this->language->get('text_response_success');					
						$json = array("success" => true, "response" => $response, 'message' => $message);
					}
				}  
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {

		$this->request->post = array_map('trim', $this->request->post);

		if (!$this->user->hasPermission('modify', 'extension/payment/phonepePG')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_phonepePG_merchant_id']) {
			$this->error['merchant_id'] = $this->language->get('error_merchant_id');
		}

		if (!$this->request->post['payment_phonepePG_merchant_key']) {
			$this->error['merchant_key'] = $this->language->get('error_merchant_key');
		}

		if (!$this->request->post['payment_phonepePG_key_index']) {
			$this->error['key_index'] = $this->language->get('error_key_index');
		}

		if (!in_array($this->request->post['payment_phonepePG_environment'], array("1","0"))) {
			$this->error['environment'] = $this->language->get('error_environment');
		}

		if(PhonepeHelperPG::getcURLversion() == false){
			$this->error['warning'] = $this->language->get('text_curl_disabled');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}