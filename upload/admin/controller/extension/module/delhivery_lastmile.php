<?php

class ControllerExtensionModuleDelhiveryLastmile extends Controller {
	
	
	/*const DEFAULT_MODULE_SETTINGS = [
		'status' => 1,
		'is_production' => 0,
		'method_name' => 'Delhivery Lastmile',
		'carrier_title' => 'Delhivery (www.delhivery.com)',
		'login_id' => '',
		'client_id' => '',
		'license_key' => '',
		'error_message' => '',
		'cod_method' => '','return_address' => '','gst_no' => '','consignee_tin' => '','cst_no' => '','sale_tax_form' => '',
		'heavy_shipment' => ''
	];*/
	
	private $error = array();
	
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "delhivery_lastmile_awb` (
		  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Manage AWB ID',
		  `awb` varchar(255) DEFAULT NULL COMMENT 'Manage AWB AWB',
		  `shipment_id` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Shipment Id',
		  `shipment_to` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Shipment To',
		  `state` int(11) DEFAULT NULL COMMENT 'Manage AWB State',
		  `status` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Status',
		  `status_type` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Status Type',
		  `pickup_location_id` int(11) DEFAULT NULL COMMENT 'Manage AWB Pickup Location Id',
		  `return_address` text COMMENT 'Manage AWB Return Address',
		  `shipment_length` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Shipment Length',
		  `shipment_width` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Shipment Width',
		  `shipment_height` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Shipment Height',
		  `status_date_time` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Status Date Time',
		  `upl` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Upl',
		  `orderid` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Order Id',
		  `order_increment_id` varchar(255) DEFAULT NULL COMMENT 'Manage AWB Order Increment Id',
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Manage AWB Created At',
		  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Manage AWB Updated At',
		  PRIMARY KEY (`entity_id`)
		)");
		
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "delhivery_lastmile_location` (
			  `location_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Manage Location ID',
			  `name` varchar(255) DEFAULT NULL COMMENT 'Manage Location Name',
			  `address` text COMMENT 'Manage Location Address',
			  `contact_person` varchar(255) DEFAULT NULL COMMENT 'Manage Location Contact Person',
			  `email` varchar(255) DEFAULT NULL COMMENT 'Manage Location Email',
			  `phone` varchar(255) DEFAULT NULL COMMENT 'Manage Location Phone',
			  `pin` varchar(255) DEFAULT NULL COMMENT 'Manage Location Pin',
			  `city` varchar(255) DEFAULT NULL COMMENT 'Manage Location City',
			  `state` varchar(255) DEFAULT NULL COMMENT 'Manage Location State',
			  `incoming_center` varchar(255) DEFAULT NULL COMMENT 'Manage Location Incoming Center',
			  `rto_center` varchar(255) DEFAULT NULL COMMENT 'Manage Location Rto Center',
			  `dto_center` varchar(255) DEFAULT NULL COMMENT 'Manage Location Dto Center',
			  `status` int(11) DEFAULT NULL COMMENT 'Manage Location Status',
			  `expected_package_count` varchar(255) DEFAULT NULL COMMENT 'Manage Location Expected Package Count',
			  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Manage Location Created At',
			  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Manage Location Updated At',
			  PRIMARY KEY (`location_id`)
			)");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "delhivery_lastmile_pincode` (
			  `pincode_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Manage Pincode ID',
			  `district` varchar(255) DEFAULT NULL COMMENT 'Manage Pincode District',
			  `pin` varchar(255) DEFAULT NULL COMMENT 'Manage Pincode Pincode',
			  `pre_paid` int(11) DEFAULT NULL COMMENT 'Manage Pincode Pre Paid',
			  `cash` int(11) DEFAULT NULL COMMENT 'Manage Pincode Cash',
			  `pickup` int(11) DEFAULT NULL COMMENT 'Manage Pincode Pickup',
			  `cod` int(11) DEFAULT NULL COMMENT 'Manage Pincode COD',
			  `state_code` varchar(255) DEFAULT NULL COMMENT 'Manage Pincode State Code',
			  `status` int(11) DEFAULT NULL COMMENT 'Manage Pincode Status',
			  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Manage Pincode Created At',
			  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Manage Pincode Updated At',
			  PRIMARY KEY (`pincode_id`)
			)");
		
		if (strpos(VERSION, '3.') === 0) {
			$this->load->model('setting/event');
			$modelEvent = $this->model_setting_event;
			
			$this->load->model('setting/module');
			$this->model_setting_module->addModule('delhivery_lastmile', array('name'=>'Delhivery Lastmile', 'status'=>1));
		}
		$this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/manageawb');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/manageawb');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/managepincode');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/managepincode');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/managelocation');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/managelocation');
		
		/*if (strpos(VERSION, '2.0.') === 0 || strpos(VERSION,'2.1.') === 0) {
			$modelEvent->addEvent('delhivery_lastmile', 'post.order.add', 'extension/module/delhivery_lastmile/order_add');
			$modelEvent->addEvent('delhivery_lastmile', 'post.order.history.add', 'extension/module/delhivery_lastmile/order_history_add');
			$modelEvent->addEvent('delhivery_lastmile', 'post.order.delete', 'extension/module/delhivery_lastmile/order_delete');
		}
		else {
			$modelEvent->addEvent('delhivery_lastmile', 'catalog/model/checkout/order/addOrder/after' , 'extension/module/delhivery_lastmile/order_add_2_2');
			$modelEvent->addEvent('delhivery_lastmile', 'catalog/model/checkout/order/editOrder/after' , 'extension/module/delhivery_lastmile/order_update_2_2');
			$modelEvent->addEvent('delhivery_lastmile', 'catalog/model/checkout/order/addOrderHistory/after' , 'extension/module/delhivery_lastmile/order_update_2_2');
		}*/
		
	}
	
	public function uninstall() {
		if (strpos(VERSION, '3.') === 0) {
			$this->load->model('setting/event');
			$modelEvent = $this->model_setting_event;
		}
		else if (strpos(VERSION,'2.0.0.0') === false) {
			$this->load->model('extension/event');
			$modelEvent = $this->model_extension_event;
		}
		else {
			$this->load->model('tool/event');
			$modelEvent = $this->model_tool_event;
		}
		$modelEvent->deleteEvent('delhivery_lastmile');
		$this->db->query("SET foreign_key_checks = 0;DROP TABLE `" . DB_PREFIX . "delhivery_lastmile_awb`, `" . DB_PREFIX . "delhivery_lastmile_location`, `" . DB_PREFIX . "delhivery_lastmile_pincode`;");
		
	}
	
	public function customErrorHandler($errno, $errstr, $errfile, $errline) {
		if (!(error_reporting() & $errno)) return;
		
		throw new \Exception($errstr);
	}
	
	public function index() {
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		$this->load->language('extension/module/delhivery_lastmile');
		
		$this->load->model('setting/setting');
		
		if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()){
			$this->model_setting_setting->editSetting('delhivery_lastmile', $this->request->post);
			$this->session->data['success'] = "Configuration saved successfully.";
			$this->response->redirect($this->url->link('extension/module/delhivery_lastmile', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success_msg'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success_msg'] = '';
		}
			
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');		
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_is_production'] = $this->language->get('entry_is_production');
		$data['entry_method_name'] = $this->language->get('entry_method_name');
		$data['entry_carrier_title'] = $this->language->get('entry_carrier_title');
		$data['entry_login_id'] = $this->language->get('entry_login_id');
		$data['entry_client_id'] = $this->language->get('entry_client_id');
		$data['entry_license_key'] = $this->language->get('entry_license_key');
		
		$data['entry_auto_manifest'] = $this->language->get('entry_auto_manifest');
		$data['entry_debug_mode'] = $this->language->get('entry_debug_mode');
		
		//$data['entry_error_message'] = $this->language->get('entry_error_message');
		$data['entry_cod_method'] = $this->language->get('entry_cod_method');
		$data['entry_return_address'] = $this->language->get('entry_return_address');
		
		$data['entry_store_name'] = $this->language->get('entry_store_name');
		$data['entry_store_address'] = $this->language->get('entry_store_address');
		
		$data['entry_gst_no'] = $this->language->get('entry_gst_no');
		$data['entry_consignee_tin'] = $this->language->get('entry_consignee_tin');
		$data['entry_cst_no'] = $this->language->get('entry_cst_no');
		$data['entry_sale_tax_form'] = $this->language->get('entry_sale_tax_form');
		$data['entry_heavy_shipment'] = $this->language->get('entry_heavy_shipment');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		}
		else {
			$data['error_warning'] = '';
		}
		
		//if (!function_exists('curl_exec')) $data['error_warning'] .= $this->language->get('curl_disable');
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => 'javascript:;void(0)'
		);
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['action'] = $this->url->link('extension/module/delhivery_lastmile', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		
		if (isset($this->request->post['delhivery_lastmile_status'])) {
			$data['delhivery_lastmile_status'] = $this->request->post['delhivery_lastmile_status'];
		}
		else {
			$data['delhivery_lastmile_status'] = $this->config->get('delhivery_lastmile_status');
		}
		
		if (isset($this->request->post['delhivery_lastmile_is_production'])) {
			$data['delhivery_lastmile_is_production'] = $this->request->post['delhivery_lastmile_is_production'];
		}
		else {
			$data['delhivery_lastmile_is_production'] = $this->config->get('delhivery_lastmile_is_production');
		}
		
		if (isset($this->request->post['delhivery_lastmile_method_name'])) {
			$data['delhivery_lastmile_method_name'] = $this->request->post['delhivery_lastmile_method_name'];
		}
		else {
			$data['delhivery_lastmile_method_name'] = $this->config->get('delhivery_lastmile_method_name');
		}
		
		if (isset($this->request->post['delhivery_lastmile_carrier_title'])) {
			$data['delhivery_lastmile_carrier_title'] = $this->request->post['delhivery_lastmile_carrier_title'];
		}
		else {
			$data['delhivery_lastmile_carrier_title'] = $this->config->get('delhivery_lastmile_carrier_title');
		}
		
		if (isset($this->request->post['delhivery_lastmile_login_id'])) {
			$data['delhivery_lastmile_login_id'] = $this->request->post['delhivery_lastmile_login_id'];
		}
		else {
			$data['delhivery_lastmile_login_id'] = $this->config->get('delhivery_lastmile_login_id');
		}
		
		if (isset($this->request->post['delhivery_lastmile_client_id'])) {
			$data['delhivery_lastmile_client_id'] = $this->request->post['delhivery_lastmile_client_id'];
		}
		else {
			$data['delhivery_lastmile_client_id'] = $this->config->get('delhivery_lastmile_client_id');
		}
		
		if (isset($this->request->post['delhivery_lastmile_auto_manifest'])) {
			$data['delhivery_lastmile_auto_manifest'] = $this->request->post['delhivery_lastmile_auto_manifest'];
		}
		else {
			$data['delhivery_lastmile_auto_manifest'] = $this->config->get('delhivery_lastmile_auto_manifest');
		}
		
		if (isset($this->request->post['delhivery_lastmile_debug_mode'])) {
			$data['delhivery_lastmile_debug_mode'] = $this->request->post['delhivery_lastmile_debug_mode'];
		}
		else {
			$data['delhivery_lastmile_debug_mode'] = $this->config->get('delhivery_lastmile_debug_mode');
		}
		if (isset($this->request->post['delhivery_lastmile_license_key'])) {
			$data['delhivery_lastmile_license_key'] = $this->request->post['delhivery_lastmile_license_key'];
		}
		else {
			$data['delhivery_lastmile_license_key'] = $this->config->get('delhivery_lastmile_license_key');
		}
		
		/* if (isset($this->request->post['delhivery_lastmile_error_message'])) {
			$data['delhivery_lastmile_error_message'] = $this->request->post['delhivery_lastmile_error_message'];
		}
		else {
			$data['delhivery_lastmile_error_message'] = $this->config->get('delhivery_lastmile_error_message');
		} */
		
		if (isset($this->request->post['delhivery_lastmile_cod_method'])) {
			$data['delhivery_lastmile_cod_method'] = $this->request->post['delhivery_lastmile_cod_method'];
		}
		else {
			$data['delhivery_lastmile_cod_method'] = $this->config->get('delhivery_lastmile_cod_method');
		}
		
		if (isset($this->request->post['delhivery_lastmile_return_address'])) {
			$data['delhivery_lastmile_return_address'] = $this->request->post['delhivery_lastmile_return_address'];
		}
		else {
			$data['delhivery_lastmile_return_address'] = $this->config->get('delhivery_lastmile_return_address');
		}
		if (isset($this->request->post['delhivery_lastmile_store_name'])) {
			$data['delhivery_lastmile_store_name'] = $this->request->post['delhivery_lastmile_store_name'];
		}
		else {
			$data['delhivery_lastmile_store_name'] = $this->config->get('delhivery_lastmile_store_name');
		}
		
		if (isset($this->request->post['delhivery_lastmile_store_address'])) {
			$data['delhivery_lastmile_store_address'] = $this->request->post['delhivery_lastmile_store_address'];
		}
		else {
			$data['delhivery_lastmile_store_address'] = $this->config->get('delhivery_lastmile_store_address');
		}
		
		if (isset($this->request->post['delhivery_lastmile_gst_no'])) {
			$data['delhivery_lastmile_gst_no'] = $this->request->post['delhivery_lastmile_gst_no'];
		}
		else {
			$data['delhivery_lastmile_gst_no'] = $this->config->get('delhivery_lastmile_gst_no');
		}
		
		if (isset($this->request->post['delhivery_lastmile_consignee_tin'])) {
			$data['delhivery_lastmile_consignee_tin'] = $this->request->post['delhivery_lastmile_consignee_tin'];
		}
		else {
			$data['delhivery_lastmile_consignee_tin'] = $this->config->get('delhivery_lastmile_consignee_tin');
		}
		
		if (isset($this->request->post['delhivery_lastmile_cst_no'])) {
			$data['delhivery_lastmile_cst_no'] = $this->request->post['delhivery_lastmile_cst_no'];
		}
		else {
			$data['delhivery_lastmile_cst_no'] = $this->config->get('delhivery_lastmile_cst_no');
		}
		
		if (isset($this->request->post['delhivery_lastmile_sale_tax_form'])) {
			$data['delhivery_lastmile_sale_tax_form'] = $this->request->post['delhivery_lastmile_sale_tax_form'];
		}
		else {
			$data['delhivery_lastmile_sale_tax_form'] = $this->config->get('delhivery_lastmile_sale_tax_form');
		}
		
		if (isset($this->request->post['delhivery_lastmile_heavy_shipment'])) {
			$data['delhivery_lastmile_heavy_shipment'] = $this->request->post['delhivery_lastmile_heavy_shipment'];
		}
		else {
			$data['delhivery_lastmile_heavy_shipment'] = $this->config->get('delhivery_lastmile_heavy_shipment');
		}
		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/module/delhivery_lastmile', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/delhivery_lastmile')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}
