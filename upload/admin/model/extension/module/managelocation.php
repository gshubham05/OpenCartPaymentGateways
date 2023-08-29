<?php
class ModelExtensionModuleManageLocation extends Model {
	
	public function getTotalDelhiveryLastmileLocation($data = array()) {
		
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "delhivery_lastmile_location";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function getDelhiveryLastmileLocation($data = array()){
		
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_location WHERE 1";
		
		$sort_data = array(
			'location_id',
			'status',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY location_id";
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
	
	public function getLocation($location_id) {
		
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_location Where location_id =".$location_id;
		$query = $this->db->query($sql);

		return $query->row;
	}
	
	public function getLocationByPin($postcode) {
		
		$sql = "SELECT * FROM " . DB_PREFIX . "delhivery_lastmile_pincode Where pin =".$postcode;
		$query = $this->db->query($sql);
		return $query->row;
	}
	
	public function Executecurl($apiurl, $token, $postData){
		$curls = curl_init($apiurl);
		$headr = array();
		$headr[] = 'Authorization: Token '.$token;
		$headr[] = 'Accept: application/json';
		
		curl_setopt($curls, CURLOPT_FAILONERROR, 0);
		curl_setopt($curls, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curls, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curls, CURLOPT_POST, true);
		curl_setopt($curls, CURLOPT_POSTFIELDS, http_build_query($postData));
		curl_setopt($curls, CURLOPT_HTTPHEADER, $headr);
		$result = curl_exec($curls);
		if(curl_error($curls)) {
			$result.=curl_error($curls);
		}
		curl_close($curls);
		return $result;
	}
	
}
?>