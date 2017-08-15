<?php

class ModelPaymentIyzicoCheckoutForm extends Model {

        public function getMethod($address, $total) {
                $this->load->language('payment/iyzico_checkout_form');

                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('iyzico_checkout_form_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

                if ($this->config->get('iyzico_checkout_form_total') > 0 && $this->config->get('iyzico_checkout_form_total') > $total) {
                        $status = false;
                } elseif (!$this->config->get('iyzico_checkout_form_geo_zone_id')) {
                        $status = true;
                } elseif ($query->num_rows) {
                        $status = true;
                } else {
                        $status = false;
                }
                $method_data = array();
                if ($status) {
                        $method_data = array(
                            'code' => 'iyzico_checkout_form',
                            'title' => $this->language->get('text_title'),
                            'terms' => '',
                            'sort_order' => $this->config->get('iyzico_checkout_form_sort_order')
                        );
                }

                return $method_data;
        }

        public function createRefundItemEntry($data) {

                $query_string = "INSERT INTO " . DB_PREFIX . "iyzico_order_refunds SET";
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

		public function updateCustomer($customer_id, $card_key, $iyzico_api) {
		
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET card_key ='" . $this->db->escape($card_key) . "', iyzico_api='" . $this->db->escape($iyzico_api) . "' WHERE customer_id = '" . (int)$customer_id . "'");
			
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

}
