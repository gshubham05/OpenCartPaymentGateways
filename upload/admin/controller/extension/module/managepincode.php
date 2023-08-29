<?php
class ControllerExtensionModuleManagePincode extends Controller {
	private $error = array();

	public function __construct($registry) {
		parent :: __construct($registry);
		/*$this->load->model('mpblog/mpbloginstall');
		$this->model_mpblog_mpbloginstall->install();*/

	}
	public function index(){
		$this->load->language('delhivery_lastmile/managepincode');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/managepincode');
		
		$this->getList();
	}
	
	public function getList() {
		
		$this->load->language('extension/module/delhivery_lastmile');
		
		$filterData = $this->getFilterableData();
		$filterDataUrl = $this->getFilterableDataUrl();
		
		$sort = 'pincode_id';
		$order = 'ASC';
		
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pincode_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		
		/* Breadcrumb. */
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => 'javascript:;void(0)'
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('pincode_heading_title'),
			'href' => 'javascript:;void(0)'
		);
		
		
		$data['delhiveryLastmile_managePincode'] = array();
		
		$filter_data = array(
			'filter_data' => $filterData,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * 20,
			'limit' => 20
		);
		
		$delhiveryLastmileAwb_total = $this->model_extension_module_managepincode->getTotalDelhiveryLastmilePincode($filter_data);
		
		$results = $this->model_extension_module_managepincode->getDelhiveryLastmilePincodes($filter_data);
		$url = "";
		foreach ($results as $result) {
			$data['delhiveryLastmile_managePincode'][] = array(
				'pincode_id' => $result['pincode_id'],
				'district' => $result['district'],
				'pin'  => $result['pin'],
				'pre_paid'  => $this->model_extension_module_managepincode->getOptionsText($result['pre_paid']),
				//'state'  => $this->model_delhivery_lastmile_manageawb->getAllAwbState($result['state']),
				'cash'  => $this->model_extension_module_managepincode->getOptionsText($result['cash']),
				'pickup'  => $this->model_extension_module_managepincode->getOptionsText($result['pickup']),
				'cod'  => $this->model_extension_module_managepincode->getOptionsText($result['cod']),
				'state_code'  => $result['state_code']
			);
		}
		
		$data['heading_title'] = $this->language->get('awb_heading_title');
		
		$data['column_pincode_id'] = $this->language->get('column_pincode_id');
		$data['column_district'] = $this->language->get('column_district');
		$data['column_pin'] = $this->language->get('column_pin');
		$data['column_pre_paid'] = $this->language->get('column_pre_paid');
		$data['column_cash'] = $this->language->get('column_cash');
		$data['column_pickup'] = $this->language->get('column_pickup');
		$data['column_cod'] = $this->language->get('column_cod');
		$data['column_state_code'] = $this->language->get('column_state_code');
		$data['button_download_pincode'] = $this->language->get('button_download_pincode');
		$data['text_no_results'] = $this->language->get('text_no_results');
		
		$url = '';
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if($filterDataUrl){
			$url .= $filterDataUrl;
		}
		$data['sort_pincode_id'] = $this->url->link('extension/module/managepincode', 'user_token=' . $this->session->data['user_token'] . '&sort=pincode_id' . $url, true);
		$data['download_pincode'] = $this->url->link('extension/module/managepincode/downloadPincode', 'user_token=' . $this->session->data['user_token'], true);
		
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if($filterDataUrl){
			$url .= $filterDataUrl;
		}
		
		$pagination = new Pagination();
		$pagination->total = $delhiveryLastmileAwb_total;
		$pagination->page = $page;
		$pagination->limit = 20;
		$pagination->url = $this->url->link('extension/module/managepincode', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($delhiveryLastmileAwb_total) ? (($page - 1) * 20) + 1 : 0, ((($page - 1) * 20) > ($delhiveryLastmileAwb_total - 20)) ? $delhiveryLastmileAwb_total : ((($page - 1) * 20) + 20), $delhiveryLastmileAwb_total, ceil($delhiveryLastmileAwb_total / 20));
		
		foreach($filterData as $_key => $_values){
			$data['filter_'.$_key] = $_values;
		}
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['reset_filter'] = $this->url->link('extension/module/managepincode', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['filter_action'] = $this->url->link('extension/module/managepincode');
		$data['user_token'] = $this->session->data['user_token'];
		
		/* Render some output. */
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}
		
		
		$this->response->setOutput($this->load->view('extension/module/managepincode', $data));
		
	}
	
	public function downloadPincode(){
		$modeIs = $this->config->get("delhivery_lastmile_is_production");
		if($modeIs){
			$apiurl='https://track.delhivery.com/c/api/pin-codes/';
		}else{
			$apiurl='http://test.delhivery.com/c/api/pin-codes/';
		}
		$cl = $this->config->get("delhivery_lastmile_login_id");
		$token = $this->config->get("delhivery_lastmile_license_key");
		if($apiurl && $token)
		{
			$url = $apiurl.'json/?token='.$token.'&pre-paid=Y';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Token '.$token.''));
			$retValue = curl_exec($ch);
			$error_msg = '';
			if (curl_error($ch)) {
				$error_msg = curl_error($ch);
			}
			$errorLogData = "Request Url: ".$url."\r\n"."Response from API: ".$retValue.", ".$error_msg;
			$codes = json_decode($retValue);
			if(sizeof($codes))
			{
				if($codes->delivery_codes ){
					$this->db->query('TRUNCATE TABLE '.DB_PREFIX.'delhivery_lastmile_pincode');
						
					foreach ($codes->delivery_codes as $item) {
						try {
							$data = array();
							$data['district'] = $item->postal_code->district;
							$data['pin'] = $item->postal_code->pin;
							$data['pre_paid'] = ($item->postal_code->pre_paid=="Y")?1:0;
							$data['cash'] = ($item->postal_code->cash=="Y")?1:0;;
							$data['pickup'] = ($item->postal_code->pickup=="Y")?1:0;;
							$data['cod'] = ($item->postal_code->cod=="Y")?1:0;;
							$data['is_oda'] = ($item->postal_code->is_oda=="Y")?1:0;
							$data['state_code'] = $item->postal_code->state_code;
							
							$sql = "INSERT INTO ".DB_PREFIX."delhivery_lastmile_pincode (district, pin, pre_paid, cash, pickup, cod, status, state_code)VALUES ('".$data['district']."', '".$data['pin']."', '".$data['pre_paid']."', '".$data['cash']."', '".$data['pickup']."', '".$data['cod']."', '1', '".$data['state_code']."')";
							$query = $this->db->query($sql);
						}
						catch (Exception $e) {
						  $this->messageManager->addErrorMessage(__('Something went wrong'));
						  $this->response->redirect($this->url->link('extension/module/managelocation', 'user_token=' . $this->session->data['user_token'], 'SSL')); 
					   }
					}
					$this->session->data['success']='Pincode downloaded Successfully';
				}else
				{
					$errorLog = "";
					if($this->config->get("delhivery_lastmile_debug_mode")){
						$this->log->write($errorLogData);
						$errorLog = "&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
					}
					$this->session->data['error']='Something went wrong please try again.'.$errorLog; 
				}
				
			}
			else
			{
				$errorLog = "";
				if($this->config->get("delhivery_lastmile_debug_mode")){
					$this->log->write($errorLogData);
					$errorLog = "&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
				}
				$this->session->data['error']='Something went wrong please try again.'.$errorLog; 
			}
		}
		$this->response->redirect($this->url->link('extension/module/managepincode', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}
	
	protected function getFilterableData() {
		$datas = array();
		if (isset($this->request->get['filter_district'])) {
			$datas['district'] = $this->request->get['filter_district'];
		}
		if (isset($this->request->get['filter_pin'])) {
			$datas['pin'] = $this->request->get['filter_pin'];
		}
		return $datas;
	}
	
	protected function getFilterableDataUrl() {
		$urls = '';
		if (isset($this->request->get['filter_district'])) {
			$urls.= '&district='.$this->request->get['filter_district'];
		}
		if (isset($this->request->get['filter_pin'])) {
			$urls.= '&pin='.$this->request->get['filter_pin'];
		}
		return $urls;
	}
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/managepincode')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

}