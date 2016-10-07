<?php

class ModelExtensionPaymentIyzicoCheckoutForm extends Model {

    /**
     * Create tables
     */
    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "iyzico_order` (
			`iyzico_order_id` INT(11) NOT NULL AUTO_INCREMENT,
			`order_id` INT(11) NOT NULL,
                        `item_id` INT(11) NOT NULL DEFAULT 0,
			`transaction_status` VARCHAR(50),
			`date_created` DATETIME NOT NULL,
                        `date_modified` DATETIME NOT NULL,
                        `processing_timestamp` DATETIME NOT NULL,
                        `api_request` TEXT,
                        `api_response` TEXT,
                        `request_type` VARCHAR(50),
                        `note` TEXT,
			PRIMARY KEY (`iyzico_order_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "iyzico_order_refunds` (
			  `iyzico_order_refunds_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `order_id` INT(11) NOT NULL,
			  `item_id` INT(11) NOT NULL,
			  `payment_transaction_id` INT(11) NOT NULL,
			  `paid_price` VARCHAR(50),
			  `total_refunded` VARCHAR(50),
			  PRIMARY KEY (`iyzico_order_refunds_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("				
				ALTER TABLE `" . DB_PREFIX . "customer`
				ADD COLUMN `card_key` VARCHAR(50),
				ADD COLUMN `iyzico_api` VARCHAR(100),
				ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
    }

    /**
     * Drop tables
     */
    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "iyzico_order`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "iyzico_order_refunds`;");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP COLUMN card_key;");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP COLUMN iyzico_api;");
    }

    /**
     * Log
     * 
     */
    public function logger($message) {
        $log = new Log('iyzico_checkout_form.log');
        $log->write($message);
    }

    /**
     * Create order entry for iyzico transactions
     * 
     */
    public function createOrderEntry($data) {

        $query_string = "INSERT INTO " . DB_PREFIX . "iyzico_order SET";
        $data_array = array();
        foreach ($data as $key => $value) {
            $data_array[] = "`$key` = '" . $this->db->escape($value) . "'";
        }
        $data_string = implode(", ", $data_array);
        $query_string .= $data_string;
        $query_string .= ";";
        $this->db->query($query_string);
        return $this->db->getLastId();
    }

    /**
     * Update order details for iyzico transactions
     * 
     */
    public function updateOrderEntry($data, $id) {

        $query_string = "UPDATE " . DB_PREFIX . "iyzico_order SET";
        $data_array = array();
        foreach ($data as $key => $value) {
            $data_array[] = "`$key` = '" . $this->db->escape($value) . "'";
        }
        $data_string = implode(", ", $data_array);
        $query_string .= $data_string;
        $query_string .= " WHERE `iyzico_order_id` = {$id};";
        return $this->db->query($query_string);
    }

    /**
     * Check Version
     *
     */
    public function versionCheck($opencart, $iyzico) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://iyzico.kahvedigital.com/version');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "opencart=$opencart&iyzico=$iyzico&type=opencart");
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * update version
     */
    public function update($version_updatable) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://iyzico.kahvedigital.com/update');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        -
                curl_setopt($ch, CURLOPT_POSTFIELDS, "new_version=$version_updatable");
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        $arrlength = count($response);

        for ($x = 0; $x < $arrlength; $x++) {
            $status = $response[$x]['status'];
            if ($status == 1) {
                $def_dir = $response[$x]['def_dir'];

                switch ($def_dir) {
                    case 'catalog':
                        $def = DIR_CATALOG;
                        break;
                    case 'admin':
                        $def = DIR_APPLICATION;
                        break;
                    case 'system':
                        $def = DIR_SYSTEM;
                        break;
					default :
						 $def ='';
						 break;
                }
                $file_dest = $response[$x]['file_dest'];
                $new_file = $response[$x]['new_file'];

                if ($new_file == 0) {
                    if (file_exists($def.$file_dest)) {
                        $fh = fopen($def.$file_dest, 'w');
                        $content = $response[$x]['content'];
                        fwrite($fh, $content);
                        fclose($fh);
                    }else{
						return 0;
					}
                } else if ($new_file == 1) {
                    $fh = fopen($def.$file_dest, 'wb');
                    $content = $response[$x]['content'];
                    fwrite($fh, $content);
                    fclose($fh);
                }
            }
        }
        return 1;
    }

}
