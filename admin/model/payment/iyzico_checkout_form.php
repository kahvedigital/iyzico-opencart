<?php

class ModelPaymentIyzicoCheckoutForm extends Model {

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

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "iyzico_order`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "iyzico_order_refunds`;");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP COLUMN card_key;");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP COLUMN iyzico_api;");
    }

    public function logger($message) {
        $log = new Log('iyzico_checkout_form.log');
        $log->write($message);
    }

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

    public function versionCheck($opencart, $iyzico) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://iyzico.kahvedigital.com/version');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "opencart=$opencart&iyzico=$iyzico&type=opencart");
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        return $response;
    }

    public function update($version_updatable) {

        function recurse_copy($src, $dst) {
            $dir = opendir($src);
            @mkdir($dst);
            while (false !== ( $file = readdir($dir))) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if (is_dir($src . '/' . $file)) {
                        recurse_copy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

        function rrmdir($dir) {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir . "/" . $object) == "dir")
                            rrmdir($dir . "/" . $object);
                        else
                            unlink($dir . "/" . $object);
                    }
                }
                reset($objects);
                rmdir($dir);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://iyzico.kahvedigital.com/update');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        -
                curl_setopt($ch, CURLOPT_POSTFIELDS, "new_version=$version_updatable");
        $response = curl_exec($ch);
        $response = json_decode($response, true);

        $serveryol = $_SERVER['DOCUMENT_ROOT'];
        $ch = curl_init();
        $source = $response['file_dest'];
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        $foldername = $response['version_name'];
        $fullfoldername = $serveryol . '/' . $foldername;
        mkdir($fullfoldername);
        if (file_exists($fullfoldername)) {
            $unzipfilename = 'iyzicoupdated.zip';
            $file = fopen($fullfoldername . '/' . $unzipfilename, "w+");
            fputs($file, $data);
            fclose($file);

            $path = pathinfo(realpath($fullfoldername . '/' . $unzipfilename), PATHINFO_DIRNAME);
            $zip = new ZipArchive;
            $res = $zip->open($fullfoldername . '/' . $unzipfilename);
            if ($res === TRUE) {
                $zip->extractTo($path);
                $zip->close();
                $zip_name_folder = $response['zip_name_folder'];

                recurse_copy($fullfoldername . '/' . $zip_name_folder . '/admin', DIR_APPLICATION);
                recurse_copy($fullfoldername . '/' . $zip_name_folder . '/catalog', DIR_CATALOG);
                recurse_copy($fullfoldername . '/' . $zip_name_folder . '/system', DIR_SYSTEM);

                rrmdir($fullfoldername);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
        return 1;
    }

}
