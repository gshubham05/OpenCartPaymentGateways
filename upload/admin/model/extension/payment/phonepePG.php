<?php
class ModelExtensionPaymentPhonepePG extends Model {

	public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "phonepePG_payment_data` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`order_id` int(11) NOT NULL,
				`phonepe_order_id` VARCHAR(255) NOT NULL,
				`transaction_id` VARCHAR(255) NOT NULL,
				`status` ENUM('PAYMENT_SUCCESS','PAYMENT_PENDING','PAYMENT_ERROR')  DEFAULT 'PAYMENT_ERROR' NOT NULL,
				`phonepe_response` TEXT,
				`date_added` DATETIME NOT NULL,
				`date_modified` DATETIME NOT NULL,
				PRIMARY KEY (`id`)
			);");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "phonepePG_payment_data`;");
	}

	public function getPhonepeOrderData($order_id) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "phonepePG_payment_data` WHERE `order_id` = '" . (int)$order_id . "' ORDER BY `id` DESC LIMIT 1");
		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function saveTxnResponse($data  = array(), $id = false){
		if(empty($data['code'])) return false;

		$status 			= (!empty($data['code']) && $data['code'] =='PAYMENT_SUCCESS') ? PhonepeConstantsPG::SUCCESS : PhonepeConstantsPG::ERROR;
		$phonepe_order_id 	= (!empty($data['data']['providerReferenceId'])? $data['data']['providerReferenceId']:'');
		$transaction_id 	= (!empty($data['data']['transactionId'])? $data['data']['transactionId']:'');

		if($phonepe_order_id && $id){
			$sql =  "UPDATE `" . DB_PREFIX . "phonepePG_payment_data` SET `transaction_id` = '" . $this->db->escape($transaction_id) . "', `status` = '" . $status . "', `phonepe_response` = '" . $this->db->escape(json_encode($data['message'])) . "', `date_modified` = NOW() WHERE `phonepe_order_id` = '" . $phonepe_order_id . "' AND `id` = '" . (int)$id . "'";
			$this->db->query($sql);
			return $data;
		}		
		return false;
	}

}