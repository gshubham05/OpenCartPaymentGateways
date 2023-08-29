<?php
class ControllerExtensionModuleManageLocation extends Controller {
	private $error = array();

	public function __construct($registry) {
		parent :: __construct($registry);
		/*$this->load->model('mpblog/mpbloginstall');
		$this->model_mpblog_mpbloginstall->install();*/

	}
	
	
	
	public function index(){
		$this->load->language('delhivery_lastmile/managelocation');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/managelocation');
		
		$this->getList();
	}
	
	public function getList() {
		
		$this->load->language('extension/module/delhivery_lastmile');
		
		$sort = 'location_id';
		$order = 'ASC';
		
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'location_id';
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
			'text' => $this->language->get('location_heading_title'),
			'href' => 'javascript:;void(0)'
		);
		
		
		$data['delhiveryLastmile_manageLocation'] = array();
		
		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * 20,
			'limit' => 20
		);
		
		$delhiveryLastmileAwb_total = $this->model_extension_module_managelocation->getTotalDelhiveryLastmileLocation($filter_data);
		
		$results = $this->model_extension_module_managelocation->getDelhiveryLastmileLocation($filter_data);
		$url = "";
		foreach ($results as $result) {
			$data['delhiveryLastmile_manageLocation'][] = array(
				'location_id' => $result['location_id'],
				'name' => $result['name'],
				'address'  => $result['address'],
				'contact_person'  => $result['contact_person'],
				'email'  => $result['email'],
				'pin'  => $result['pin'],
				'state'  => $result['state'],
				'expected_package_count'  => $result['expected_package_count']
			);
		}
		
		$data['heading_title'] = $this->language->get('location_heading_title');
		
		$data['column_location_id'] = $this->language->get('column_location_id');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_address'] = $this->language->get('column_address');
		$data['column_contact_person'] = $this->language->get('column_contact_person');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_pin'] = $this->language->get('column_pin');
		$data['column_state'] = $this->language->get('column_state');
		$data['column_expected_package_count'] = $this->language->get('column_expected_package_count');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['button_download_location'] = $this->language->get('button_download_location');
		
		$url = '';
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_location_id'] = $this->url->link('extension/module/managelocation', 'user_token=' . $this->session->data['user_token'] . '&sort=location_id' . $url, true);
		$data['download_location'] = $this->url->link('extension/module/managelocation/downloadLocation', 'user_token=' . $this->session->data['user_token'], true);
		
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $delhiveryLastmileAwb_total;
		$pagination->page = $page;
		$pagination->limit = 20;
		$pagination->url = $this->url->link('extension/module/managelocation', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($delhiveryLastmileAwb_total) ? (($page - 1) * 20) + 1 : 0, ((($page - 1) * 20) > ($delhiveryLastmileAwb_total - 20)) ? $delhiveryLastmileAwb_total : ((($page - 1) * 20) + 20), $delhiveryLastmileAwb_total, ceil($delhiveryLastmileAwb_total / 20));
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['massaction'] = $this->url->link('extension/module/managelocation/massActions', 'user_token=' . $this->session->data['user_token'], true);
		
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
		
		/* Render some output. */
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/module/managelocation', $data));
		
	}
	
	public function massActions() {
		if($this->request->post['massactions']=='create_pickup_request'){
			
			if($this->request->post['pickup_date'] and sizeof($this->request->post['selected'])){
				$this->load->model('extension/module/managelocation');
				$modeIs = $this->config->get("delhivery_lastmile_is_production");
				if($modeIs){
					$apiurl='https://track.delhivery.com/fm/request/new/';
				}else{
					$apiurl='http://test.delhivery.com/fm/request/new/';
				}
				$token = $this->config->get("delhivery_lastmile_license_key");
				$reqIds=array();
				if($token){
					$dateTime=strtotime($this->request->post['pickup_date']);
					$hr= ($this->request->post['hours'])?$this->request->post['hours']:"12";
					$min= ($this->request->post['minute'])?$this->request->post['minute']:"00";
					$pickupDate=date('Y-m-d',$dateTime);
					$pickupTime=$hr.":".$min.":00";
					$unsuccess=0;
					$success=0;
					foreach($this->request->post['selected'] as $location){
						$locationData=$this->model_extension_module_managelocation->getLocation($location);
						if(intval($locationData['expected_package_count']) > 0){
							$pickup_time=$pickupTime;
							$pickup_date=$pickupDate;
							//echo $pickup_date;die;
							$expected_package_count=$locationData['expected_package_count'];
							$pickup_location=$locationData['name'];
							$postData=array(
											'pickup_time'=>$pickup_time,
											'pickup_date'=>$pickup_date,
											'pickup_location'=>$pickup_location,
											'expected_package_count'=>$expected_package_count
											);
							
							$result = $this->model_extension_module_managelocation->Executecurl($apiurl,$token,$postData);
							
							$errorLogData = "Request Url: ".$apiurl." , Token:".$token." \r\nSend Data: ".json_encode($postData)."\r\n"."Response from API: ".$result;
							
							$result = json_decode($result);
							$objArray=@get_object_vars($result);
							if(@array_key_exists("pickup_id",$objArray) && $objArray['pickup_id']){
								$reqIds[]=$result->pickup_id;
								$sql = "UPDATE " . DB_PREFIX . "delhivery_lastmile_location SET expected_package_count = '0' WHERE location_id='".$locationData['location_id']."'";
								$query = $this->db->query($sql);
								$success++;
							}else{
								$errorLog = "";
								if($this->config->get("delhivery_lastmile_debug_mode")){
									$this->log->write($errorLogData);
									$errorLog = "&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
								}
								
								if(@array_key_exists("pickup_time",$objArray)){
									//$this->log->write($errorLogData);
									$this->session->data['error']=$this->session->data['error'].$objArray['pickup_time'].$errorLog."<br>";
								}
								
								if(@array_key_exists("pickup_date",$objArray)){
									//$this->log->write($errorLogData);
									$this->session->data['error']=$this->session->data['error'].$objArray['pickup_date'].$errorLog."<br>"; 
								}
								
								if(@array_key_exists("pickup_location",$objArray)){
									//$this->log->write($errorLogData);
									$this->session->data['error']=$this->session->data['error'].$objArray['pickup_location'].$errorLog."<br>";
								}
								
								$unsuccess++;
							}
						}else{
							$unsuccess++;
						}
					}
					if($success){
							$this->session->data['success']='Order Request Pickup # is : '.implode(',',$reqIds);
						}
					if($unsuccess){
						$this->session->data['error']=@$this->session->data['error'].$unsuccess.' Order Pickup Request Creation Failed.'.$errorLog; 
					}
				}else{
					$this->session->data['error']='Please add valid License Key in configuration.';
				}
				
				//$this->session->data['success'] = "All Set !!!!!!!!!";
				$this->response->redirect($this->url->link('extension/module/managelocation', 'user_token=' . $this->session->data['user_token'], 'SSL'));
				
			}else{
				
				$this->session->data['error'] = "Sorry! Something Went wrong or please select Location and Date or Time.";
				$this->response->redirect($this->url->link('extension/module/managelocation', 'user_token=' . $this->session->data['user_token'], 'SSL'));
				
			}
		}
	}
	
	public function downloadLocation(){
		$modeIs = $this->config->get("delhivery_lastmile_is_production");
		if($modeIs){
			$apiurl='https://track.delhivery.com/client/warehouses/eslist.json';
		}else{
			$apiurl='https://staging-express.delhivery.com/client/warehouses/eslist.json';
		}
		$cl = $this->config->get("delhivery_lastmile_login_id");
		$token = $this->config->get("delhivery_lastmile_license_key");
		if($apiurl && $token)
		{
			$url = $apiurl.'?token='.$token.'&cl='.urlencode($cl).'&limit=5000';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Token '.$token.''));
			$retValue = curl_exec($ch);
			$error_msg = '';
			if (curl_error($ch)) {
				$error_msg = curl_error($ch);
			}
			$codes = json_decode($retValue);
			
			$errorLogData = "Request Url: ".$url." \r\n"."Response from API: ".$retValue.", ".$error_msg;
			//echo "<pre>";
			//print_r($codes);
			if($codes)
			{
				if(@$codes->success)
				{
					$codesData=@$codes->data;
					$this->db->query('TRUNCATE TABLE '.DB_PREFIX.'delhivery_lastmile_location');
						
					foreach ($codesData as $item) {
						try {
							$sql = "INSERT INTO ".DB_PREFIX."delhivery_lastmile_location (name, address, contact_person, email, phone, pin, city, state, incoming_center, rto_center, dto_center, status)VALUES ('".$item->name."', '".$item->address."', '".$item->contact_person."', '".$item->email."', '".$item->phone."', '".$item->pin."', '".$item->city."', '".$item->state."', '".$item->incoming_center."', '".$item->rto_center."', '".$item->dto_center."', '1')";
							$query = $this->db->query($sql);
						}
						catch (Exception $e) {
						  $this->messageManager->addErrorMessage(__('Something went wrong'));
						  $this->response->redirect($this->url->link('extension/module/managelocation', 'user_token=' . $this->session->data['user_token'], 'SSL')); 
					   }
					}
					$this->session->data['success']='Location downloaded Successfully';
				}else{
					$errorLog = "";
					if($this->config->get("delhivery_lastmile_debug_mode")){
						$this->log->write($errorLogData);
						$errorLog = "&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
					}
					
					$this->session->data['error']='Something went wrong please try again.'.$errorLog; 
				}
				
			}else{
				$errorLog = "";
				if($this->config->get("delhivery_lastmile_debug_mode")){
					$this->log->write($errorLogData);
					$errorLog = "&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
				}
				$this->session->data['error']='Something went wrong please try again.'.$errorLog;
			}
		}
		$this->response->redirect($this->url->link('extension/module/managelocation', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/managelocation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

}