<?php
class ModelExtensionModuleManageAwb extends Model {
	
	public function getTotalDelhiveryLastmileAwb($data = array()) {
		
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "delhivery_lastmile_awb";
		$sqlOther = array();
		if($data['filter_data']){
			$filter_data = $data['filter_data'];
			foreach($filter_data as $key => $_values){
				if(trim($_values)!=''){
					$sqlOther[]=$key." LIKE '%".trim($_values)."%'";
				}
			}
		}
		if(sizeof($sqlOther)){
			$sql.=" WHERE (".implode(" AND ",$sqlOther).")";
		}
		
		$query = $this->db->query($sql);

		return @$query->row['total'];
	}
	
	public function getDelhiveryLastmileAwbs($data = array()){
		
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_awb";
		
		$sqlOther = array();
		if($data['filter_data']){
			$filter_data = $data['filter_data'];
			foreach($filter_data as $key => $_values){
				if(trim($_values)!=''){
					$sqlOther[]=$key." LIKE '%".trim($_values)."%'";
				}
			}
		}
		if(sizeof($sqlOther)){
			$sql.=" WHERE (".implode(" AND ",$sqlOther).")";
		}else{
			$sql.=' WHERE 1';
		}
		
		$sort_data = array(
			'entity_id',
			'status',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY entity_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getDelhiveryLastmileAllLocations(){
		$locData = array();
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_location";
		$query = $this->db->query($sql);
		$results = $query->rows;
		
		foreach ($results as $result) {
			$locData[] = array('values' => $result['location_id'], "label" => $result['name']);
		}
		
		return $locData;
		
	}
	
	public function getAllAwbState($state){
		$data = array(1=>"Used", 2=>"Unused", 4=>"Expired");
		if(in_array($state,array(1,2,4))){
			return $data[$state];
		}else{
			return "";
		}
	}
	public function getAwbdata($entity_id) {
		
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_awb Where entity_id ='".$entity_id."'";
		$query = $this->db->query($sql);

		return $query->row;
	}
	
	public function getAwbdataByAwbNumber($awbs) {
		
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_awb Where awb ='".$awbs."'";
		$query = $this->db->query($sql);

		return $query->row;
	}
	
	public function waybill() {
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_awb Where state = 2 ORDER BY entity_id DESC LIMIT 1";
		$query = $this->db->query($sql);

		$data=$query->row;
		if($data['awb'])
		{
			return "success@".$data['awb'];
		}else{
			return "error@AWB number is not available. Please download more AWB.";
		}
	}
	public function getAwbdataByawb($awb) {
		
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_awb Where awb ='".$awb."'";
		$query = $this->db->query($sql);

		return $query->row;
	}
	public function Executecurl($url, $type, $params){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$url");
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		if($type == 'post'):
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		endif;	
		$retValue = curl_exec($ch);
		//print_r($retValue);
		if(curl_error($ch)) {
			$retValue.=curl_error($ch);
		}
		curl_close($ch);
		return $retValue;
	}
	public function Executecurl2($apiurl,$token){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiurl);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Token '.$token.''));
		$result = curl_exec($ch);
		if(curl_error($ch)) {
			$result.=curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}
	public function cancelPackageExecuteUrl($url,$params,$token)
	{
		$curll = curl_init($url);
		$headr = array();
		$headr[] = 'Authorization: Token '.$token.'';
		$headr[] = 'Content-Type: application/json';
		$headr[] = 'Accept: application/json';
		
		curl_setopt($curll, CURLOPT_FAILONERROR, 1);
		curl_setopt($curll, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curll, CURLOPT_TIMEOUT, 60);
		//curl_setopt($curll, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curll, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curll, CURLOPT_POST, true);
		curl_setopt($curll, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($curll, CURLOPT_HTTPHEADER, $headr);
		
		$curl_responsee = curl_exec($curll);
		if(curl_error($curll)) {
			$curl_responsee.=curl_error($curll);
		}
		curl_close($curll);
		
		return $curl_responsee;
	}
	public function saveUpdateCurl($url,$params,$token)
		{
			$datas=$params;
			$curll = curl_init($url);						
			$headr = array();
			$headr[] = 'Authorization: Token '.$token;
			$headr[] = 'Content-Type: application/json';
			$headr[] = 'Accept: application/json';
			curl_setopt($curll, CURLOPT_FAILONERROR, 1);
			curl_setopt($curll, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curll, CURLOPT_TIMEOUT, 60);
			curl_setopt($curll, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curll, CURLOPT_POST, true);
			//curl_setopt($curll, CURLOPT_POSTFIELDS, json_encode($datas));
			curl_setopt($curll, CURLOPT_POSTFIELDS, $datas);
			curl_setopt($curll, CURLOPT_HTTPHEADER, $headr);
			$curl_responsee = curl_exec($curll);
			if(curl_error($curll)) {
				$curl_responsee.=curl_error($curll);
			}
			curl_close($curll);
			return $curl_responsee;
		}
	
}
?>