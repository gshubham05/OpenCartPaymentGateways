<?php
class ControllerExtensionModuleManageAwb extends Controller {
	private $error = array();

	public function __construct($registry) {
		parent :: __construct($registry);
		/*$this->load->model('mpblog/mpbloginstall');
		$this->model_mpblog_mpbloginstall->install();*/

	}
	public function index(){
		$this->load->language('delhivery_lastmile/manageawb');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/manageawb');
		
		$this->getList();
	}
	
	public function getList() {
		
		$this->load->language('extension/module/delhivery_lastmile');
		
		$filterData = $this->getFilterableData();
		$filterDataUrl = $this->getFilterableDataUrl();
		
		$sort = 'entity_id';
		$order = 'DESC';
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'entity_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
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
		
		
		/* -- Dropdown Data -- */
		$data['data_state'] = array();
		$data['data_state'][] = array('values'=>'1', 'label'=>'Used');
		$data['data_state'][] = array('values'=>'2', 'label'=>'Unused');
		$data['data_state'][] = array('values'=>'4', 'label'=>'Expired');
		
		$dataStatus = array('Assigned', 'InTransit', 'Dispatched', 'Pending', 'Preload', 'Delivered', 'Returned', 'Cancelled', 'Collected', 'Picked-UP', 'Manifested', 'NotPicked', 'RTO', 'DL', 'UD', 'RT');
		$data['data_status'] = array();
		foreach($dataStatus as $_status){
			$data['data_status'][] = array('values'=>$_status);
		}
		
		$data['data_status_type'] = array();
		$data['data_status_type'][] = array('values'=>'DL');
		$data['data_status_type'][] = array('values'=>'RT');
		$data['data_status_type'][] = array('values'=>'UD');
		
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
			'text' => $this->language->get('awb_heading_title'),
			'href' => 'javascript:;void(0)'
		);
		
		$data['delhiveryLastmile_manageAwb'] = array();
		
		$filter_data = array(
			'filter_data' => $filterData,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * 20,
			'limit' => 20
		);
		
		$delhiveryLastmileAwb_total = $this->model_extension_module_manageawb->getTotalDelhiveryLastmileAwb($filter_data);
		
		$results = $this->model_extension_module_manageawb->getDelhiveryLastmileAwbs($filter_data);
		$url = "";
		foreach ($results as $result) {
			$data['delhiveryLastmile_manageAwb'][] = array(
				'entity_id' => $result['entity_id'],
				'awb' => $result['awb'],
				'shipment_id'  => $result['shipment_id'],
				'shipment_to'  => $result['shipment_to'],
				'state'  => $this->model_extension_module_manageawb->getAllAwbState($result['state']),
				'status'  => $result['status'],
				'status_type'  => $result['status_type'],
				'order_increment_id'  => $result['order_increment_id'],
				'edit' => $this->url->link('extension/module/manageawb/edit', 'user_token=' . $this->session->data['user_token'] . '&entity_id=' . $result['entity_id'] . $url, true)
			);
		}
		
		$data['heading_title'] = $this->language->get('awb_heading_title');
		
		$data['column_entity_id'] = $this->language->get('column_entity_id');
		$data['column_awb'] = $this->language->get('column_awb');
		$data['column_shipment_id'] = $this->language->get('column_shipment_id');
		$data['column_shipment_to'] = $this->language->get('column_shipment_to');
		$data['column_state'] = $this->language->get('column_state');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_status_type'] = $this->language->get('column_status_type');
		$data['column_orderid'] = $this->language->get('column_orderid');
		$data['column_action'] = $this->language->get('column_action');
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
		
		$data['sort_entity_id'] = $this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'] . '&sort=entity_id' . $url, true);
		$data['download_awb'] = $this->url->link('extension/module/manageawb/downloadAwb', 'user_token=' . $this->session->data['user_token'], true);
		
		
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
		$pagination->url = $this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($delhiveryLastmileAwb_total) ? (($page - 1) * 20) + 1 : 0, ((($page - 1) * 20) > ($delhiveryLastmileAwb_total - 20)) ? $delhiveryLastmileAwb_total : ((($page - 1) * 20) + 20), $delhiveryLastmileAwb_total, ceil($delhiveryLastmileAwb_total / 20));
		
		
		foreach($filterData as $_key => $_values){
			$data['filter_'.$_key] = $_values;
		}
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['reset_filter'] = $this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['filter_action'] = $this->url->link('extension/module/manageawb');
		$data['user_token'] = $this->session->data['user_token'];
		
		$data['location_datas'] = $this->model_extension_module_manageawb->getDelhiveryLastmileAllLocations();
		$data['massaction'] = $this->url->link('extension/module/manageawb/massActions', 'user_token=' . $this->session->data['user_token'], true);
		
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
		
		$this->response->setOutput($this->load->view('extension/module/manageawb', $data));
		
	}
	
	public function massActions(){
		$flag=true;
		//print_r($this->request);
		if($this->request->post['massactions']=='submit_manifest'){
			
			if($this->request->post['manifest_location'] and sizeof($this->request->post['selected'])){
				
				$this->submitManifestAction($this->request->post['selected'], $this->request->post['manifest_location']);
				
			}else{
				
				$this->session->data['error'] = "Sorry! Something Went wrong. Please select Location or Awb Records.";
				$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
				
			}
			
		}elseif($this->request->post['massactions']=='update_awb_status'){
			
			if($this->request->post['selected']){
				$modeIs = $this->config->get("delhivery_lastmile_is_production");
				if($modeIs=='production'){
					$apiurl='https://track.delhivery.com/api/v1/packages/';
				}else{
					$apiurl='http://test.delhivery.com/api/v1/packages/';
				}
				$cl = $this->config->get("delhivery_lastmile_client_id");
				$token = $this->config->get("delhivery_lastmile_license_key");
				$this->load->model('extension/module/manageawb');
				$this->load->model('extension/module/managelocation');
				if($cl && $token){
					$awbArray=array();
					foreach($this->request->post['selected'] as $awb)
					{
						$awbmodel=$this->model_extension_module_manageawb->getAwbdata($awb);
						if($awbmodel['state'] == 1)
						{
							array_push($awbArray,$awbmodel['awb']);
						}
					}
					if($awbArray){
						$apiurl =  $apiurl.'json/?verbose=0&token='.$token.'&waybill='.implode(',',$awbArray);
						$resultData=$this->model_extension_module_manageawb->Executecurl2($apiurl,$token);
						$resultData = json_decode($resultData);
						$objArray=get_object_vars($resultData);
						if(array_key_exists("ShipmentData",$objArray) && $objArray['ShipmentData']){
							foreach ($resultData->ShipmentData as $item) {			   		   
							   $sql = "UPDATE " . DB_PREFIX . "delhivery_lastmile_awb SET status = '".preg_replace('/\s+/', '', $item->Shipment->Status->Status)."', status_type = '".$item->Shipment->Status->StatusType."', status_date_time = '". $item->Shipment->Status->StatusDateTime."' WHERE awb='".$item->Shipment->AWB."'";
								$query = $this->db->query($sql);
								$this->session->data['success'] = $this->session->data['success']."#".$item->Shipment->AWB." AWB Updated Successfully.<br>";
							}
						}else{
							$this->session->data['error'] = "Sorry! Something Went wrong.";
							$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
						}
					}else{
						$this->session->data['error'] = "Sorry! Something Went wrong. Please select used Awb Records.";
						$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
					}
				}else{
					$this->session->data['error'] = $this->session->data['error']."#".$awbmodel['awb']." Please add valid License Key and Gateway URL in plugin configuration";
					$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
				}
				
			}else{
				$this->session->data['error'] = "Sorry! Something Went wrong. Please select Awb Records.";
				$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
			
		}elseif($this->request->post['massactions']=='cancel_package'){
			
			if($this->request->post['selected']){
				$modeIs = $this->config->get("delhivery_lastmile_is_production");
				if($modeIs){
					$apiurl='https://track.delhivery.com/api/p/edit';
				}else{
					$apiurl='http://test.delhivery.com/api/p/edit';
				}
				$cl = $this->config->get("delhivery_lastmile_client_id");
				$token = $this->config->get("delhivery_lastmile_license_key");
				$this->load->model('extension/module/manageawb');
				if($cl && $token){
					$allowStatus=array('InTransit','Pending','Open','Scheduled');
					foreach($this->request->post['selected'] as $awb)
					{
						$awbmodel=$this->model_extension_module_manageawb->getAwbdata($awb);
						if(in_array($awbmodel['status'],$allowStatus)){
							$postData=array('waybill'=>$awbmodel['awb'],'cancellation'=>true);
							$result=$this->model_extension_module_manageawb->cancelPackageExecuteUrl($apiurl,$postData,$token);
							$result = json_decode($result);
							if($result->status){
								$sql = "UPDATE " . DB_PREFIX . "delhivery_lastmile_awb SET status = 'Cancelled' WHERE awb='".$awbmodel['awb']."'";
								$query = $this->db->query($sql);
								$this->session->data['success'] = $this->session->data['success']."#".$awbmodel['awb']." Cancellation Successfully<br>";
							}else{
								$this->session->data['error'] = $this->session->data['error'].'#'.$awbmodel['awb'].'  Failed to Submit Cancellation.<br>';
							}
						}else{
							$this->session->data['error'] = $this->session->data['error'].'#'.$awbmodel['awb'].' Sorry! You Can Cancel (In Transit, Pending, Open, Scheduled) Status packages only.<br>';
						}
					}
				}else{
					$this->session->data['error'] = $this->session->data['error']."#".$awbmodel['awb']." Please add valid License Key and Gateway URL in plugin configuration";
					$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
				}
			}else{
				$this->session->data['error'] = "Sorry! Something Went wrong. Please select Awb Records.";
				$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
			
		}elseif($this->request->post['massactions']=='print_shipping_label'){
			if($this->request->post['selected']){
				$modeIs = $this->config->get("delhivery_lastmile_is_production");
				if($modeIs){
					$apiurl='https://track.delhivery.com/api/p/packing_slip';
				}else{
					$apiurl='http://test.delhivery.com/api/p/packing_slip';
				}
				$cl = $this->config->get("delhivery_lastmile_client_id");
				$token = $this->config->get("delhivery_lastmile_license_key");
				$this->load->model('extension/module/manageawb');
				$this->load->model('extension/module/managelocation');
				$this->load->model('sale/order');
				if($cl && $token){
					$awbArray=array();
					foreach($this->request->post['selected'] as $awb)
					{
						$awbmodel=$this->model_extension_module_manageawb->getAwbdata($awb);
						if($awbmodel['state'] == 1)
						{
							array_push($awbArray,$awbmodel['awb']);
						}
					}
					if($awbArray){
						$apiurl =  $apiurl.'?wbns='.implode(',',$awbArray);
						$resultData=$this->model_extension_module_manageawb->Executecurl2($apiurl,$token);
						$resultData = json_decode($resultData);
						$objArray=get_object_vars($resultData);
						
						$labeldata=array();
						$label=array();
						$page_break=false;
						if(array_key_exists("packages_found",$objArray) && $objArray['packages_found']){
							foreach($resultData->packages as $codes){
								$subtotal=0;
								$awbModel=$this->model_extension_module_manageawb->getAwbdataByawb($codes->wbn);
								$LocationModel =$this->model_extension_module_managelocation->getLocation($awbModel['pickup_location_id']);
								$orderModel=$this->model_sale_order->getOrder($codes->oid);
								$methodcode = (strtolower($this->config->get("delhivery_lastmile_cod_method")) == strtolower($orderModel['payment_code']) || $order['payment_code']=="cod") ?"COD":"Pre-Paid";
								$products = $this->model_sale_order->getOrderProducts($codes->oid);
								$r= $this->currency->getSymbolRight($orderModel['currency_code']);
								$l= $this->currency->getSymbolLeft($orderModel['currency_code']);
								$productOrdered=array();
								$shipping_address=$orderModel['shipping_firstname'];
								$shipping_address.=($orderModel['shipping_lastname'])?" ".$orderModel['shipping_lastname']:"";
								$shipping_address.="<br>".$orderModel['shipping_address_1'];
								$shipping_address.=($orderModel['shipping_address_2'])?", ".$orderModel['shipping_address_2']:"";
								$shipping_address.=($orderModel['shipping_postcode'])?", ".$orderModel['shipping_postcode']:"";
								$shipping_address.=($orderModel['shipping_city'])?", ".$orderModel['shipping_city']:"";
								$shipping_address.=($orderModel['shipping_zone'])?", ".$orderModel['shipping_zone']:"";
								$shipping_address.=($orderModel['shipping_country'])?", ".$orderModel['shipping_country']:"";
								$shipping_address.=($orderModel['telephone'])?"<br>T: ".$orderModel['telephone']:"";
								foreach($products as $product){
									$subtotal=$subtotal + floatval($product['total']);
									array_push($productOrdered,array("name"=>$product['name'],"qty"=>$product['quantity'],"total"=>$l.number_format($product['total'],2).$r,"price"=>$l.number_format($product['price'],2).$r));
								}
								$totals = $this->model_sale_order->getOrderTotals($codes->oid);
								$Ototals=array();
								foreach($totals as $total){
									array_push($Ototals,array("title"=>$total['title'],"value"=>$l.number_format($total['value'],2).$r));
								}
								//echo "<pre>";
								//print_r($orderModel);die;
								$labeldata['items']=$productOrdered;
								$labeldata['page_break']=$page_break;
								$labeldata['delhivery_logo']=$codes->delhivery_logo;
								$labeldata['barcode']=$codes->barcode;
								$labeldata['order_id']=$codes->oid;
								$labeldata['order']=$orderModel;
								$labeldata['shipping_address']=$shipping_address;
								$labeldata['grandTotle']=$l.number_format($orderModel['total'],2).$r;
								$labeldata['methodcode']=$methodcode;
								$labeldata['subtotal']=$l.$subtotal.$r;
								$labeldata['shippingAmount']=$l.number_format($orderModel['total']-$subtotal,2).$r;
								$labeldata['gst']=$this->config->get("delhivery_lastmile_gst_no");
								$labeldata['cst']=$this->config->get("delhivery_lastmile_cst_no");
								$labeldata['tin']=$this->config->get("delhivery_lastmile_consignee_tin");
								$labeldata['store_name']=$this->config->get("delhivery_lastmile_store_name");
								$labeldata['store_address']=$this->config->get("delhivery_lastmile_store_address");
								$labeldata['return_address']=$this->config->get("delhivery_lastmile_return_address");
								$labeldata['O_totals']=$Ototals;
								$label["delhiveryLastmile_manageLabel"][]=$labeldata;
								$page_break=true;
							}
							$flag=false;
							$this->response->setOutput($this->load->view('extension/module/printlabel', $label));
						}else{
							$this->session->data['error'] = "No Shipping label available at this moment.";
							$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
						}
					}else{
						$this->session->data['error'] = "Sorry! Something Went wrong. Please select used Awb Records.";
						$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
					}
				}else{
					$this->session->data['error'] = $this->session->data['error']."#".$awbmodel['awb']." Please add valid License Key and Gateway URL in plugin configuration";
					$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
				}
			}else{
				$this->session->data['error'] = "Sorry! Something Went wrong. Please select Awb Records.";
				$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}

			
		}else{
			$this->session->data['error'] = "Sorry! Something Went wrong. Please try again.";
			$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}
		if($flag){
			$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}
		
	}
	
	
	public function saveTrackingInformation(){
		$postedData = $this->request->post;
		if(trim($postedData['order_id']) and trim($postedData['order_delhivery_lastmile']) and trim($postedData['awbs'])){
			$this->load->model('sale/order');
			$order_info = $this->model_sale_order->getOrder($postedData['order_id']);
			
			$sql = "UPDATE " . DB_PREFIX . "delhivery_lastmile_awb SET state = 1,status='Assigned',orderid='".$postedData['order_id']."',order_increment_id='".$postedData['order_id']."',shipment_id='".$postedData['order_id']."',shipment_to='".$order_info['shipping_firstname']." ".$order_info['shipping_lastname']."' WHERE awb='".$postedData['awbs']."'";
   			$query = $this->db->query($sql);
			
			
			$orderComment = "Order assigned to Delhivery Lastmile.<br />Order Tracking No: ".$postedData['awbs']."<br />https://www.delhivery.com/track/package/".$postedData['awbs']." to track Order.";
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$postedData['order_id'] . "', order_status_id = '" . (int)$order_info['order_status_id'] . "', notify = '" . (int)0 . "', comment = '" . $orderComment . "', date_added = NOW()");
			
			$this->session->data['success_msg'] = "Order assigned to Delhivery Lastmile successfully. Order Tracking Awb: ".$postedData['awbs'];
			
			if(@$postedData['delhivery_lastmile_location']){
				$this->load->model('extension/module/manageawb');
				$awbmodel = $this->model_extension_module_manageawb->getAwbdataByAwbNumber(trim($postedData['awbs']));
				if(sizeof($awbmodel)){
					$this->submitManifestAction(array($awbmodel['entity_id']), $postedData['delhivery_lastmile_location']);
				}
			}
			
		}
		$this->response->redirect($this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token']."&order_id=".$postedData['order_id'], 'SSL'));
		
	}
	
	protected function submitManifestAction($selectedAwb,$location) {
		$modeIs = $this->config->get("delhivery_lastmile_is_production");
		if($modeIs){
			$apiurl='https://track.delhivery.com/';
		}else{
			$apiurl='http://test.delhivery.com/';
		}
		$cl = $this->config->get("delhivery_lastmile_client_id");
		$token = $this->config->get("delhivery_lastmile_license_key");
		if($cl && $token)
		{
			$token = "$token"; // replace this with your token key
			$apiurl .= "cmu/push/json/?token=".$token;
			$this->load->model('extension/module/managelocation');
			$this->load->model('sale/order');
			$this->load->model('extension/module/manageawb');
			$locationData=$this->model_extension_module_managelocation->getLocation($location);
			$totalQtyOrdered=0;
			//echo "<pre>";
			foreach($selectedAwb as $awb)
			{
				$awbmodel = $this->model_extension_module_manageawb->getAwbdata($awb);
				if($awbmodel['status'] == 'Assigned'){
					$params = array(); // this will contain request meta and the package feed
					$package_data = array(); // package data feed
					$shipments = array();
					$order=$this->model_sale_order->getOrder($awbmodel['orderid']);
					$products = $this->model_sale_order->getOrderProducts($awbmodel['orderid']);
					$Ototals = $this->model_sale_order->getOrderTotals($awbmodel['orderid']);
					$tax_value=0;
					foreach($Ototals as $Ototal)
					{
						if($Ototal['code'] == "tax")
						{
							$tax_value+=floatval($Ototal['value']);
						}
					}
					$order_qty=0;
					$item_desc=array();
					foreach($products as $product)
					{
						$order_qty = $order_qty+(int)$product['quantity'];
						$item_desc[]= $product['name'];
					}
	
					$methodcode = (strtolower($this->config->get("delhivery_lastmile_cod_method")) == strtolower($order['payment_code']) || $order['payment_code']=="cod") ?"COD":"Pre-Paid";
					$codamount = (strtolower($this->config->get("delhivery_lastmile_cod_method")) == strtolower($order['payment_code']) || $order['payment_code']=="cod") ? $order['total'] : "00.00";
					
					
					$shipment = array();
						$shipment['client'] = $cl;
						$shipment['name'] = $order['shipping_firstname']." ".$order['shipping_lastname']; // consignee name
						$shipment['order'] = $order['order_id']; // client order number
						$shipment['products_desc'] =implode(' ',array_unique($item_desc));
						$shipment['order_date'] = $order['date_modified']; // ISO Format
						$shipment['payment_mode'] = $methodcode;
						$shipment['total_amount'] = $order['total']; // in INR
						$shipment['cod_amount'] = $codamount; // amount to be collected, required for COD
						$shipment['add'] = array($order['shipping_address_1']); // consignee address
						$shipment['city'] = $order['shipping_city'];
						$shipment['state'] = $order['shipping_zone'];
						$shipment['waybill'] = $awbmodel['awb'];
						$shipment['shipment_length'] = ($awbmodel['shipment_length'])?$awbmodel['shipment_length']:"";
						$shipment['shipment_width'] = ($awbmodel['shipment_width'])?$awbmodel['shipment_width']:"";
						$shipment['shipment_height'] = ($awbmodel['shipment_height'])?$awbmodel['shipment_height']:"";
						$shipment['country'] = $order['shipping_country'];
	
						$shipment['phone'] = $order['telephone'];
						$shipment['pin'] = $order['shipping_postcode'];
						$shipment['return_add'] = $this->config->get("delhivery_lastmile_return_address");
						$order_qty = floor($order_qty); //get qty of all items rounded to full number (without 3.0000 or so)
						$shipment['quantity'] = $order_qty; #quantity of goods, it has to be positive integer .
						$shipment['consignee_tin'] = $this->config->get("delhivery_lastmile_consignee_tin");; #consignee tin no
						$shipment['commodity_value'] = $order['total']; #commodity value
						$shipment['tax_value'] = $tax_value; #tax value
						$shipment['sales_tax_form_ack_no'] = $this->config->get("delhivery_lastmile_sale_tax_form"); #Sale Tax Form Acknowledge No.
						$shipment['category_of_goods'] = '';//implode(',',array_unique($categoryName)); #Category of goods
						$shipment['source'] = 'OpenCart';
						//print_r($shipment);die;
						$shipments = array($shipment);
						
						//------------ Add Pickup Location
						
						$pickupLocation = array();
						$pickupLocation['add']=$locationData['address'];
						$pickupLocation['city']=$locationData['city'];
						$pickupLocation['country']='India';
						$pickupLocation['name']=$locationData['name'];
						$pickupLocation['phone']=$locationData['phone'];
						$pickupLocation['pin']=$locationData['pin'];
						
						$package_data['pickup_location'] = $pickupLocation;
						$package_data['shipments'] = $shipments;
						//echo "<pre>";print_r($package_data);
						$params['format'] = 'json';
						$params['data'] = json_encode($package_data);
						
						//$results=$this->model_extension_module_manageawb->Executecurl($apiurl,'post',$params);
						//$result = json_decode($results);
						
						$chan = curl_init();
						curl_setopt($chan, CURLOPT_URL, "$apiurl");
						curl_setopt($chan, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($chan, CURLOPT_ENCODING, '');
						curl_setopt($chan, CURLOPT_MAXREDIRS, 10);
						curl_setopt($chan, CURLOPT_TIMEOUT, 60);
						curl_setopt($chan, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
						curl_setopt($chan, CURLOPT_CUSTOMREQUEST, "POST");
						curl_setopt($chan, CURLOPT_POSTFIELDS,http_build_query($params));
						//curl_setopt($chan, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
							
						$results = curl_exec($chan);
						
						if(curl_error($chan)) {
							$results.=curl_error($chan);
						}
						curl_close($chan);
						
						$result = json_decode($results);
						
						$objArray=@get_object_vars($result);
						if(@array_key_exists("success",$objArray) && $objArray['success']){
							$sql = "UPDATE " . DB_PREFIX . "delhivery_lastmile_awb SET pickup_location_id = '".$locationData['location_id']."', return_address = '".$this->config->get("delhivery_lastmile_return_address")."', status_type = 'UD', status='Manifested' WHERE entity_id='".$awbmodel['entity_id']."'";
							$query = $this->db->query($sql);
							$totalQtyOrdered+=intval($objArray['package_count']);
							$this->session->data['success'] = @$this->session->data['success']." Awb #".$awbmodel['awb']." manifest submit successfully.<br>";
						}else
						{
							$errorLogData = "Request Url: ".$apiurl." \r\nSend Data: ".json_encode($params)."\r\n"."Response from API: ".$results;
							$errorLog = "";
							if($this->config->get("delhivery_lastmile_debug_mode")){
								$this->log->write($errorLogData);
								$errorLog = ".&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
							}
							
							if(@array_key_exists("rmk",$objArray) && $objArray['rmk'])
							{
								$this->session->data['error'] = @$this->session->data['error']." Unable to submit manifest AWB #".$awbmodel['awb']." ".@$objArray['rmk'].$errorLog."<br>";
							}else
							{
								$this->session->data['error'] = @$this->session->data['error']." Unable to submit manifest AWB #".$awbmodel['awb'].$errorLog."<br>";
							}
							
						}
				}else{
					if($awbmodel['state'] == "1")
					{
						$this->session->data['error'] = @$this->session->data['error']." #".$awbmodel['awb']." AWB manifest already submitted.<br>";
					}else
					{
						$this->session->data['error'] = @$this->session->data['error']." #".$awbmodel['awb']." Unused AWB can not be submit.<br>";
					}
					
				}
			}
			$totalQtyOrdered+=intval($locationData['expected_package_count']);
			$sql = "UPDATE " . DB_PREFIX . "delhivery_lastmile_location SET expected_package_count = '".$totalQtyOrdered."' WHERE location_id='".$locationData['location_id']."'";
			$query = $this->db->query($sql);
		}else
		{
			$this->session->data['error'] =" Please add valid License Key and Gateway URL in plugin configuration";
		}
	}
	public function edit(){
		if($this->request->get['entity_id']){
			$this->load->model('extension/module/manageawb');
			$awbmodel=$this->model_extension_module_manageawb->getAwbdata($this->request->get['entity_id']);
			if(empty($awbmodel))
			{
				$this->session->data['error'] = "This lastmile package no longer exists.";
				$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
			$allowStatus=array('InTransit','Pending','Scheduled');
			if($awbmodel['state']==1 && in_array($awbmodel['status'],$allowStatus)){
				$this->load->model('sale/order');
				$orderModel=$this->model_sale_order->getOrder($awbmodel['orderid']);
				$shipping_address=$orderModel['shipping_address_1'];
				$shipping_address.=($orderModel['shipping_address_2'])?", ".$orderModel['shipping_address_2']:"";
				$shipping_address.=($orderModel['shipping_postcode'])?", ".$orderModel['shipping_postcode']:"";
				$shipping_address.=($orderModel['shipping_city'])?", ".$orderModel['shipping_city']:"";
				$shipping_address.=($orderModel['shipping_zone'])?", ".$orderModel['shipping_zone']:"";
				$shipping_address.=($orderModel['shipping_country'])?", ".$orderModel['shipping_country']:"";
				$data=array();
				$data['form_data']=array(
					"awb"=>$awbmodel['awb'],
					"phone"=>$orderModel['telephone'],
					"name"=>$orderModel['shipping_firstname']." ".$orderModel['shipping_lastname'],
					"address"=>$shipping_address,
					"shipment_length"=>$awbmodel['shipment_length'],
					"shipment_width"=>$awbmodel['shipment_width'],
					"shipment_height"=>$awbmodel['shipment_height'],
					"action"=>$this->url->link('extension/module/manageawb/updatePackage', 'user_token=' . $this->session->data['user_token'], 'SSL')
				);
				$data['heading_title']="Edit package";
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
				$this->response->setOutput($this->load->view('extension/module/editpackage', $data));
			}else{
				$this->session->data['error'] = "Sorry! You Can Edit (In Transit, Pending, Scheduled) Status packages only.";
				$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
			
		}else{
			$this->session->data['error'] = "Sorry! Something Went wrong.";
			$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}
	}
	public function updatePackage(){
		$modeIs = $this->config->get("delhivery_lastmile_is_production");
		if($modeIs){
			$apiurl='https://track.delhivery.com/api/p/edit';
		}else{
			$apiurl='http://test.delhivery.com/api/p/edit';
		}
		$cl = $this->config->get("delhivery_lastmile_login_id");
		$token = $this->config->get("delhivery_lastmile_license_key");
		if($cl && $token){
			if ($this->request->post['awb'] && $this->request->post['address']){
				$data=$this->request->post;
				$datas='{"waybill":"'.$data["awb"].'",
						"phone":"'.$data["phone"].'",
						"name":"'.$data["name"].'",
						"add":"'.$data["address"].'",
						"shipment_length":'.number_format((float)$data["shipment_length"],1).',
						"shipment_width":'.number_format((float)$data["shipment_width"],1).',
						"shipment_height":'.number_format((float)$data["shipment_height"],1).',
						"gm":'.number_format((float)$data["weight"],1).'}';
				$this->load->model('extension/module/manageawb');
				$result=$this->model_extension_module_manageawb->saveUpdateCurl($apiurl,$datas,$token);
				$curl_response = json_decode($result);
				$errorLogData = "Request Url: ".$apiurl." \r\nSend Data: ".@$datas."\r\n"."Response from API: ".$result;
				
				
				$objArray=get_object_vars($curl_response);
				if(array_key_exists("status",$objArray) && $objArray['status']==1){
					$this->session->data['success'] = "#".$objArray['waybill']."  AWB(s) Package Update Successfully.";
				}elseif(array_key_exists("error",$objArray) && $objArray['error']){
					
					$errorLog = "";
					if($this->config->get("delhivery_lastmile_debug_mode")){
						$this->log->write($errorLogData);
						$errorLog = ".&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
					}
					
					$this->session->data['error'] = $objArray['error'].".".$errorLog;
				}else{
					$errorLog = "";
					if($this->config->get("delhivery_lastmile_debug_mode")){
						$this->log->write($errorLogData);
						$errorLog = ".&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
					}
					
					$this->session->data['error'] = "There was a problem saving the package request.".$errorLog; 
				}
				$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}else{
				$this->session->data['error'] = "Something went wrong with AWB or Address.";
				$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
		}else
		{
			$this->session->data['error'] = "Please add valid License Key and Gateway URL in plugin configuration";
			$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}
	}
	public function downloadAwb(){
		$available=$this->db->query("SELECT * FROM ".DB_PREFIX."delhivery_lastmile_awb WHERE STATE = 2");
		if(count($available->rows)< 100)
		{
			$modeIs = $this->config->get("delhivery_lastmile_is_production");
			if($modeIs){
				$apiurl='https://track.delhivery.com/waybill/api/bulk/';
			}else{
				$apiurl='http://test.delhivery.com/waybill/api/bulk/';
			}
			$cl = $this->config->get("delhivery_lastmile_login_id");
			$token = $this->config->get("delhivery_lastmile_license_key");
			if($apiurl && $token)
			{
				$url = $apiurl.'json/?token='.$token.'&count=100&cl='.urlencode($cl);
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
				$errorLogData = "Request Url: ".$url." \r\nResponse from API: ".$retValue.", ".$error_msg;
				
				if(sizeof($codes))
				{
					$awbs = explode(',',$codes);
					$sql = "INSERT INTO ".DB_PREFIX."delhivery_lastmile_awb (awb, state)VALUES";
					foreach ($awbs as $item) {
						$sql = $sql."('".$item."', '2'),";
					}
						try {
							
							$query = $this->db->query(substr($sql, 0, -1));
						}
						catch (Exception $e) {
						  $this->messageManager->addErrorMessage(__('Something went wrong'));
						  $this->response->redirect($this->url->link('extension/module/managelocation', 'user_token=' . $this->session->data['user_token'], 'SSL')); 
					   }
					$this->session->data['success']='AWB downloaded Successfully.';
				}
				else
				{
					$errorLog = "";
					if($this->config->get("delhivery_lastmile_debug_mode")){
						$this->log->write($errorLogData);
						$errorLog = "&nbsp;&nbsp;<a href='".$this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], 'SSL')."' target='_blank'>Click Here</a> to View error Logs.";
					}
					
					$this->session->data['error']='Something went wrong.'.$errorLog; 
				}
			}else
			{
				$this->session->data['error']='Please add valid License Key and Gateway URL in plugin configuration'; 
			}
		}else
		{
			$this->session->data['error']='More than 100 AWB still available to use.'; 
		}
		$this->response->redirect($this->url->link('extension/module/manageawb', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}
	
	protected function getFilterableData() {
		$datas = array();
		if (isset($this->request->get['filter_awb'])) {
			$datas['awb'] = $this->request->get['filter_awb'];
		}
		if (isset($this->request->get['filter_shipment_id'])) {
			$datas['shipment_id'] = $this->request->get['filter_shipment_id'];
		}
		if (isset($this->request->get['filter_shipment_to'])) {
			$datas['shipment_to'] = $this->request->get['filter_shipment_to'];
		}
		if (isset($this->request->get['filter_state'])) {
			$datas['state'] = $this->request->get['filter_state'];
		}
		if (isset($this->request->get['filter_status'])) {
			$datas['status'] = $this->request->get['filter_status'];
		}
		if (isset($this->request->get['filter_status_type'])) {
			$datas['status_type'] = $this->request->get['filter_status_type'];
		}
		if (isset($this->request->get['filter_order_increment_id'])) {
			$datas['order_increment_id'] = $this->request->get['filter_order_increment_id'];
		}
		return $datas;
	}
	
	protected function getFilterableDataUrl() {
		$urls = '';
		if (isset($this->request->get['filter_awb'])) {
			$urls.= '&awb='.$this->request->get['filter_awb'];
		}
		if (isset($this->request->get['filter_shipment_id'])) {
			$urls.= '&shipment_id='.$this->request->get['filter_shipment_id'];
		}
		if (isset($this->request->get['filter_shipment_to'])) {
			$urls.= '&shipment_to='.$this->request->get['filter_shipment_to'];
		}
		if (isset($this->request->get['filter_state'])) {
			$urls.= '&state='.$this->request->get['filter_state'];
		}
		if (isset($this->request->get['filter_status'])) {
			$urls.= '&status='.$this->request->get['filter_status'];
		}
		if (isset($this->request->get['filter_status_type'])) {
			$urls.= '&status_type='.$this->request->get['filter_status_type'];
		}
		if (isset($this->request->get['filter_order_increment_id'])) {
			$urls.= '&order_increment_id='.$this->request->get['filter_order_increment_id'];
		}
		return $urls;
	}
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/manageawb')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
	public function generateAwbs(){
		if($this->request->post['order_id']){
			$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_awb Where orderid ='".$this->request->post['order_id']."'";
			$query = $this->db->query($sql);
			if(sizeof($query->row))
			{
				$awbdat=$query->row;
				echo "error@Awb already assigned for this order. AWB:".$awbdat["awb"];
			}else{
				$this->load->model('sale/order');
				$this->load->model('extension/module/managelocation');
				
				
				$orderModel=$this->model_sale_order->getOrder($this->request->post['order_id']);
				$zipcode=$orderModel['shipping_postcode'];
				$zipdeatils=$this->model_extension_module_managelocation->getLocationByPin($zipcode);
				
				$payment_method_code=$orderModel['payment_code'];
				if((!sizeof($zipdeatils) ) || ( $zipdeatils['pre_paid'] != 1) || (($payment_method_code == $this->config->get("delhivery_lastmile_cod_method")) && $zipdeatils['cod'] != 1)){
					echo "error@Order PinCode is not serviceable by Delhivery.";
				}else{
					$this->load->model('extension/module/manageawb');
					echo $awbmodel=$this->model_extension_module_manageawb->waybill();
				}
			}
		}else{
			echo "error@PinCode is not serviceable by Delhivery.";
		}
	}
	/* 
		* Calculate the Shipping Charges, origin to Destination
		* Function Call from Order Traction Section
	*/
	public function shippingCalculation(){
		$orderId = $this->request->post['order_id'];
		$md = $this->request->post['md'];
		$pt = $this->request->post['pt'];
		$ss = $this->request->post['ss'];
		$cgm = $this->request->post['cgm'];
		$oc = $this->request->post['oc'];
		
		if(trim($orderId) and trim($oc)){
			$this->load->model('sale/order');
			$orderModel=$this->model_sale_order->getOrder($this->request->post['order_id']);
			$zipcode = $orderModel['shipping_postcode'];
			
			$cl = $this->config->get("delhivery_lastmile_login_id");
			$token = $this->config->get("delhivery_lastmile_license_key");
			if(trim($cl) and trim($token)){
				
				$modeIsProduction = $this->config->get("delhivery_lastmile_is_production");
				if($modeIsProduction){
					$url = "https://test.delhivery.com/api/kinko/v1/invoice/charges/.json";
				}else{
					$url = "https://test.delhivery.com/api/kinko/v1/invoice/charges/.json";
				}
				
				$url.= "?cl=".$cl."&ss=".$ss."&md=".$md."&pt=".$pt."&pin=".$zipcode."&oc=".$oc."&cgm=".$cgm;
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
						"authorization: Token ".$token
					)
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				if($err) {
					echo '<div class="alert alert-danger">Error #:"'.$err.'"</div>';
				}else{
					$responseData = json_decode($response,true);
					if(array_key_exists('error', $responseData)){
						echo '<div class="alert alert-danger">Error: '.$responseData['error'].'</div>';
					}else{
						if(!@$responseData[0]){
							echo '<div class="alert alert-danger">Error: '.$response.'</div>';
						}else{
							$responseData = $responseData[0];
							$outputResponse = '<style>table.thisTable tr td {padding: 5px 8px;}</style><table width="100%" class="table table-bordered thisTable">
								<thead>
									<tr>
										<th style="width: 20%;">Services</th>
										<th style="width: 30%;">Charges</th>
										<th style="width: 20%;">Services</th>
										<th style="width: 30%;">Charges</th>
									</tr>
								</thead>
								<tr>
									<td class="col-rd">Charge ROV</td>
									<td class="col-pd">'.$responseData['charge_ROV'].'</td>
									<td class="col-rd">Charge RTO</td>
									<td class="col-pd">'.$responseData['charge_RTO'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Charge MPS</td>
									<td class="col-pd">'.$responseData['charge_MPS'].'</td>
									<td class="col-rd">Charge Pickup</td>
									<td class="col-pd">'.$responseData['charge_pickup'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Charge AWB</td>
									<td class="col-pd">'.$responseData['charge_AWB'].'</td>
									<td class="col-rd">Zone</td>
									<td class="col-pd">'.$responseData['zone'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Charge DTO</td>
									<td class="col-pd">'.$responseData['charge_DTO'].'</td>
									<td class="col-rd">Charge COD</td>
									<td class="col-pd">'.$responseData['charge_COD'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Charge CCOD</td>
									<td class="col-pd">'.$responseData['charge_CCOD'].'</td>
									<td class="col-rd">Gross Amount</td>
									<td class="col-pd">'.$responseData['gross_amount'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">WT Rule Id</td>
									<td class="col-pd">'.$responseData['wt_rule_id'].'</td>
									<td class="col-rd">Charge DL</td>
									<td class="col-pd">'.$responseData['charge_DL'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Total Amount</td>
									<td class="col-pd">'.$responseData['total_amount'].'</td>
									<td class="col-rd">Charge FOD</td>
									<td class="col-pd">'.$responseData['charge_FOD'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Charge WOD</td>
									<td class="col-pd">'.$responseData['charge_WOD'].'</td>
									<td class="col-rd">Charge INS</td>
									<td class="col-pd">'.$responseData['charge_INS'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Charge FS</td>
									<td class="col-pd">'.$responseData['charge_FS'].'</td>
									<td class="col-rd">Charge CNC</td>
									<td class="col-pd">'.$responseData['charge_CNC'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Charge FOV</td>
									<td class="col-pd">'.$responseData['charge_FOV'].'</td>
									<td class="col-rd">Charged weight</td>
									<td class="col-pd">'.$responseData['charged_weight'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Swacch Bharat Tax</td>
									<td class="col-pd">'.$responseData['tax_data']['swacch_bharat_tax'].'</td>
									<td class="col-rd">IGST</td>
									<td class="col-pd">'.$responseData['tax_data']['IGST'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">SGST</td>
									<td class="col-pd">'.$responseData['tax_data']['SGST'].'</td>
									<td class="col-rd">CGST</td>
									<td class="col-pd">'.$responseData['tax_data']['CGST'].'</td>
								  </tr>
								  <tr>
									<td class="col-rd">Service Tax</td>
									<td class="col-pd">'.$responseData['tax_data']['service_tax'].'</td>
									<td class="col-rd">Krishi Kalyan Cess</td>
									<td class="col-pd">'.$responseData['tax_data']['krishi_kalyan_cess'].'</td>
								  </tr>
								</table>
								';
							echo $outputResponse;
						}
					}
				}
			}else{
				echo '<div class="alert alert-danger">Something went wrong. Please configure your credentials.</div>';
			}
		}else{
			echo '<div class="alert alert-danger">Something went wrong. Please check Postcode of origin center and try again.</div>';
		}
	}
	
}