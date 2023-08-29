<?php
class ModelExtensionModuleManagePincode extends Model {
	
	public function getTotalDelhiveryLastmilePincode($data = array()) {
		
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "delhivery_lastmile_pincode`";

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

		return $query->row['total'];
	}
	
	public function getDelhiveryLastmilePincodes($data = array()){
		
		$sql = "SELECT * FROM `" . DB_PREFIX . "delhivery_lastmile_pincode`";
		
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
			'pincode_id',
			'status',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pincode_id";
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
	
	
	public function getOptionsText($vals){
		$data = array(1=>"Yes", 0=>"No");
		if(in_array($vals,array(0,1))){
			return $data[$vals];
		}else{
			return "";
		}
		
	}
	
	
	
}
?>