<?php
error_reporting(0);
require_once DIR_SYSTEM . "library" . DIRECTORY_SEPARATOR . "iyzico" . DIRECTORY_SEPARATOR . "IyzipayBootstrap.php";

class ControllerPaymentIyzicoCheckoutForm extends Controller {

        private $error = array();
        private $base_url = "https://api.iyzipay.com";
        private $order_prefix = "opencart156_";
		private $iyzico_version = "1.5.0.1";

        public function index() {
                $this->language->load('payment/iyzico_checkout_form');
                $this->document->setTitle($this->language->get('heading_title'));

                $this->load->model('setting/setting');

                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                        $this->model_setting_setting->editSetting('iyzico_checkout_form', $this->request->post);
                        $this->session->data['success'] = $this->language->get('text_success');
                        $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
                }

                $this->data['heading_title'] = $this->language->get('heading_title');
                $this->data['text_edit'] = $this->language->get('heading_title');
                $this->data['link_title'] = $this->language->get('text_link');

                $this->data['text_enabled'] = $this->language->get('text_enabled');
                $this->data['text_disabled'] = $this->language->get('text_disabled');

                $this->data['entry_api_id_live'] = $this->language->get('entry_api_id_live');
                $this->data['entry_secret_key_live'] = $this->language->get('entry_secret_key_live');

                $this->data['entry_order_status'] = $this->language->get('entry_order_status');
                $this->data['entry_status'] = $this->language->get('entry_status');
                $this->data['entry_class'] = $this->language->get('entry_class');
                $this->data['entry_class_responsive'] = $this->language->get('entry_class_responsive');
                $this->data['entry_class_popup'] = $this->language->get('entry_class_popup');
                $this->data['entry_installment_options'] = $this->language->get('entry_installment_options');
                $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

                $this->data['button_save'] = $this->language->get('button_save');
                $this->data['button_cancel'] = $this->language->get('button_cancel');

                $this->data['order_status_after_payment_tooltip'] = $this->language->get('order_status_after_payment_tooltip');
                $this->data['order_status_after_cancel_tooltip'] = $this->language->get('order_status_after_cancel_tooltip');
                $this->data['entry_test_tooltip'] = $this->language->get('entry_test_tooltip');
                $this->data['entry_cancel_order_status'] = $this->language->get('entry_cancel_order_status');
                $this->data['text_iyzico_checkout_form_info'] = $this->language->get('text_iyzico_checkout_form_info');

                $this->data['message'] = '';
                $this->data['error_warning'] = '';
				$this->data['error_version'] = '';

                $error_data_array_key = array(
                    'api_id_live',
                    'secret_key_live'
                );

				if (isset($this->request->get['update_error'])) {
				$this->data['error_version'] = $this->language->get('entry_error_version_updated');
				} else {
					$this->load->model('payment/iyzico_checkout_form');
					$versionCheck = $this->model_payment_iyzico_checkout_form->versionCheck(VERSION, $this->iyzico_version);

					if (!empty($versionCheck['version_status']) AND $versionCheck['version_status'] == '1') {
                $this->data['error_version'] = $this->language->get('entry_error_version');
                $this->data['iyzico_or_text'] = $this->language->get('entry_iyzico_or_text');
                $this->data['iyzico_update_button'] = $this->language->get('entry_iyzico_update_button');
                $version_updatable = $versionCheck['new_version_id'];
                $this->data['version_update_link'] = $this->url->link('payment/iyzico_checkout_form/update', 'token=' . $this->session->data['token'] . "&version=$version_updatable", true);
						}
				}
				
                foreach ($error_data_array_key as $key) {
                        $this->data["error_{$key}"] = isset($this->error[$key]) ? $this->error[$key] : '';
                }

                $this->data['breadcrumbs'] = array();

                $this->data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_home'),
                    'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
                    'separator' => false
                );

                $this->data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_payment'),
                    'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
                    'separator' => ' :: '
                );

                $this->data['breadcrumbs'][] = array(
                    'text' => $this->language->get('heading_title'),
                    'href' => $this->url->link('payment/iyzico_checkout_form', 'token=' . $this->session->data['token'], 'SSL'),
                    'separator' => ' :: '
                );

                $this->data['action'] = $this->url->link('payment/iyzico_checkout_form', 'token=' . $this->session->data['token'], 'SSL');

                $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

                $merchant_keys_name_array = array(
                    'iyzico_checkout_form_api_id_live',
                    'iyzico_checkout_form_secret_key_live',
                    'iyzico_checkout_form_order_status_id',
                    'iyzico_checkout_form_status',
                    'iyzico_checkout_form_sort_order',
                    'iyzico_checkout_form_form_class',
                    'iyzico_checkout_form_cancel_order_status_id'
                );

                foreach ($merchant_keys_name_array as $key) {
                        $this->data[$key] = isset($this->request->post[$key]) ? $this->request->post[$key] : $this->config->get($key);
                }

                if (isset($this->request->post['iyzico_checkout_form_test'])) {
                        $this->data['iyzico_checkout_form_test'] = $this->request->post['iyzico_checkout_form_test'];
                } else {
                        $mode = $this->config->get('iyzico_checkout_form_test');
                        if (isset($mode) && $mode == 0) {
                                $this->data['iyzico_checkout_form_test'] = 0;
                        } else {
                                $this->data['iyzico_checkout_form_test'] = 1;
                        }
                }

                $this->load->model('localisation/order_status');
				if ($this->data['iyzico_checkout_form_order_status_id'] == '') {
					$this->data['iyzico_checkout_form_order_status_id'] = $this->config->get('config_order_status_id');
				}	
                $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

                $this->template = 'payment/iyzico_checkout_form.tpl';
                $this->children = array(
                    'common/header',
                    'common/footer'
                );
                $this->response->setOutput($this->render());
        }

        public function install() {
                $this->load->model('payment/iyzico_checkout_form');
                $this->model_payment_iyzico_checkout_form->install();
        }

        public function uninstall() {
                $this->load->model('payment/iyzico_checkout_form');
                $this->model_payment_iyzico_checkout_form->uninstall();
        }
		
		public function update() {
        $this->load->model('payment/iyzico_checkout_form');
        $this->load->language('payment/iyzico_checkout_form');
        $version_updatable = $this->request->get['version'];
        $updated = $this->model_payment_iyzico_checkout_form->update($version_updatable);
        if ($updated == 1) {
           $this->session->data['success'] = $this->language->get('text_success');
           $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        } else {
            $this->redirect($this->url->link('payment/iyzico_checkout_form', 'token=' . $this->session->data['token'] . "&update_error=$updated", 'SSL'));
			}
		}		

        public function orderAction() {

                $this->load->model('payment/iyzico_checkout_form');
                $this->language->load('payment/iyzico_checkout_form');
                $language_id = (int) $this->config->get('config_language_id');
                $this->data = array();
                $order_id = (int) $this->request->get['order_id'];
                $this->data['token'] = $this->request->get['token'];
                $this->data['heading_title'] = $this->language->get('heading_title');
                $this->data['text_payment_cancel'] = $this->language->get('text_payment_cancel');
                $this->data['text_order_cancel'] = $this->language->get('text_order_cancel');
                $this->data['text_items'] = $this->language->get('text_items');
                $this->data['text_transactions'] = $this->language->get('text_transactions');
                $this->data['text_processing'] = $this->language->get('text_processing');
                $this->data['text_item_name'] = $this->language->get('text_item_name');
                $this->data['text_paid_price'] = $this->language->get('text_paid_price');
                $this->data['text_total_refunded_amount'] = $this->language->get('text_total_refunded_amount');
                $this->data['text_action'] = $this->language->get('text_action');
                $this->data['text_refund'] = $this->language->get('text_refund');
                $this->data['text_date_added'] = $this->language->get('text_date_added');
                $this->data['text_type'] = $this->language->get('text_type');
                $this->data['text_status'] = $this->language->get('text_status');
                $this->data['text_note'] = $this->language->get('text_note');
                $this->data['text_are_you_sure'] = $this->language->get('text_are_you_sure');
                $this->data['text_please_enter_amount'] = $this->language->get('text_please_enter_amount');

                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iyzico_order` WHERE `order_id` = '{$order_id}' ORDER BY `iyzico_order_id` DESC");
                $this->data['successful_cancel_counts'] = 0;
                $this->data['successful_refund_counts'] = 0;
                $this->data['same_date'] = 'no';
                $this->data['iyzico_transactions'] = array();
                $this->data['order_id'] = $order_id;
                $this->data['payment_success'] = 'no';
                $this->data['display_cancel_option'] = 'no';
                $this->data['iyzico_transactions_refunds_data'] = array();
                foreach ($query->rows as $row) {
                        switch ($row['request_type']) {
                                case 'get_auth':
                                        $row['transaction_type'] = $this->language->get('payment_transaction_type');

                                        if ($row['transaction_status'] == "success") {

                                                if (date('Y-m-d', strtotime($row['date_modified'])) == date('Y-m-d')) {
                                                        $this->data['same_date'] = 'yes';
                                                }

                                                $this->data['payment_success'] = 'yes';
                                                
                                                if (!empty($row['api_response'])) {
                                                        $auth_response = json_decode($row['api_response'], true);
                                                        if (is_array($auth_response) && $auth_response['status'] == 'success' && $auth_response['paymentStatus'] == "SUCCESS") {
                                                            if (!empty($auth_response['paymentId'])) {
                                                                $this->data['display_cancel_option'] = 'yes';
                                                            }
                                                        }
                                                }
                                        }

                                        $this->data['iyzico_transactions'][] = $row;

                                        break;

                                case 'order_cancel':

                                        if ($row['transaction_status'] == "success") {
                                                $this->data['successful_cancel_counts'] ++;
                                        }

                                        $row['transaction_type'] = $this->language->get('cancel_transaction_type');
                                        $this->data['iyzico_transactions'][] = $row;

                                        break;

                                case 'order_refund':

                                        if ($row['transaction_status'] == "success") {
                                                $this->data['successful_refund_counts'] ++;
                                        }

                                        $row['transaction_type'] = $this->language->get('refund_transaction_type');
                                        $this->data['iyzico_transactions'][] = $row;

                                        break;
                        }
                }

                $this->load->model('sale/order');
                $order_details = $this->model_sale_order->getOrder($order_id);

                $refunded_transactions_query_string = "SELECT rf.*, pd.name FROM `" . DB_PREFIX . "iyzico_order_refunds` rf " .
                        "LEFT JOIN `" . DB_PREFIX . "product_description` pd  ON pd.product_id = rf.item_id " .
                        "WHERE `rf`.`order_id` = '{$order_id}' " .
                        "AND `pd`.`language_id` = {$language_id} " .
                        "AND (`rf`.`paid_price` != `rf`.`total_refunded`) " .
                        "ORDER BY `iyzico_order_refunds_id` ASC";

                $refunded_transactions_query = $this->db->query($refunded_transactions_query_string);
                foreach ($refunded_transactions_query->rows as $refunded_item) {
                        $refunded_item['paid_price_converted'] = $this->currency->format($refunded_item['paid_price'], $order_details['currency_code'], false);
                        $refunded_item['total_refunded_converted'] = $this->currency->format($refunded_item['total_refunded'], $order_details['currency_code'], false);
                        $refunded_item['remaining_refund_amount'] = $refunded_item['paid_price'] - $refunded_item['total_refunded'];
                        $refunded_item['full_refunded'] = ($refunded_item['paid_price'] == $refunded_item['total_refunded']) ? 'yes' : 'no';
                        $this->data['iyzico_transactions_refunds_data'][] = $refunded_item;
                }

                $this->template = 'payment/iyzico_checkout_form_order.tpl';

                $this->response->setOutput($this->render());
        }

        public function cancel() {

                $this->language->load('payment/iyzico_checkout_form');
                $order_id = $this->request->post['order_id'];
                $data = array(
                    'order_id' => $order_id,
                    'success' => 'false'
                );
                try {

                        if (!$order_id) {
                                throw new \Exception($this->language->get('error_invalid_order'));
                        }

                        $this->load->model('payment/iyzico_checkout_form');

                        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iyzico_order` WHERE `order_id` = '{$order_id}' AND `request_type` = 'get_auth' ORDER BY `iyzico_order_id` DESC");

                        if (!$query->row) {
                                throw new \Exception($this->language->get('error_invalid_order'));
                        }

                        $transaction = $query->row;

                        $auth_response = json_decode($transaction['api_response'], true);
                        if (empty($auth_response)) {
                                throw new \Exception($this->language->get('error_invalid_order'));
                        }

                        if ($auth_response['status'] == 'success' && $auth_response['paymentStatus'] == "SUCCESS") {
                                $payment_id = $auth_response['paymentId'];
                        } else {
                                throw new \Exception($this->language->get('error_invalid_order'));
                        }

                        
                        $this->load->model('sale/order');
                        $order_details = $this->model_sale_order->getOrder($order_id);

                        IyzipayBootstrap::init();

                        $merchant_api_id = $this->config->get('iyzico_checkout_form_api_id_live');
                        $merchant_secret_key = $this->config->get('iyzico_checkout_form_secret_key_live');

                        $options = new \Iyzipay\Options();
                        $options->setApiKey($merchant_api_id);
                        $options->setSecretKey($merchant_secret_key);
                        $options->setBaseUrl($this->base_url);

                        $request = new \Iyzipay\Request\CreateCancelRequest();
                        $locale = \Iyzipay\Model\Locale::EN;
                        $siteLang = explode('_', $order_details['language_code']);
                        if($siteLang[0] == 'tr'){
                            $locale = \Iyzipay\Model\Locale::TR;
                        }
                        $request->setLocale($locale);
                        $request->setConversationId(uniqid($this->order_prefix) . "_cancel_" . $order_id);
                        $request->setPaymentId($payment_id);
                        $request->setIp($this->request->server['REMOTE_ADDR']);

                        $save_data_array = array(
                            'order_id' => $order_id,
                            'transaction_status' => 'in process',
                            'date_created' => date('Y-m-d H:i:s'),
                            'date_modified' => date('Y-m-d H:i:s'),
                            'api_request' => $request->toJsonString(),
                            'api_response' => '',
                            'request_type' => 'order_cancel',
                            'note' => ''
                        );
                        $cancel_transaction_id = $this->model_payment_iyzico_checkout_form->createOrderEntry($save_data_array);

                        $response = \Iyzipay\Model\Cancel::create($request, $options);

                        $update_data_array = array(
                            'date_modified' => date('Y-m-d H:i:s'),
                            'api_response' => $response->getRawResult(),
                            'transaction_status' => $response->getStatus(),
                            'processing_timestamp' => date('Y-m-d H:i:s', $response->getSystemTime() / 1000),
                            'note' => ($response->getStatus() == "failure") ? $response->getErrorMessage() : $this->language->get('cancel_done_success')
                        );
                        $this->model_payment_iyzico_checkout_form->updateOrderEntry($update_data_array, $cancel_transaction_id);

                        if ($response->getStatus() == "failure") {
                                throw new \Exception($response->getErrorMessage());
                        }

                        $this->_addhistory($order_id, $this->config->get('iyzico_checkout_form_cancel_order_status_id'), $data['message']);

                        $data['message'] = $this->language->get('cancel_done_success');
                        $data['success'] = "true";
                } catch (\Exception $ex) {
                        $data['message'] = $ex->getMessage();
                }

                echo json_encode($data);
        }

        public function refund() {

                $this->language->load('payment/iyzico_checkout_form');
                $order_id = (int) $this->request->post['order_id'];
                $language_id = (int) $this->config->get('config_language_id');
                $data = array(
                    'order_id' => $order_id,
                    'success' => 'false'
                );

                try {

                        if (!$order_id) {
                                throw new \Exception($this->language->get('error_invalid_order'));
                        }

                        $this->load->model('sale/order');
                        $this->load->model('payment/iyzico_checkout_form');
                        $item_id = (int) $this->request->post['item_id'];
                        $amount = (double) $this->request->post['amount'];
                        $order_details = $this->model_sale_order->getOrder($order_id);

                        if (!$order_details) {
                                throw new \Exception($this->language->get('error_invalid_order'));
                        }

                        $refunded_transactions_query_string = "SELECT * FROM `" . DB_PREFIX . "iyzico_order_refunds` " .
                                "WHERE `order_id` = '{$order_id}' AND `item_id` = '{$item_id}'";

                        $refunded_transactions_query = $this->db->query($refunded_transactions_query_string);

                        if (!$refunded_transactions_query->num_rows) {
                                throw new \Exception($this->language->get('error_invalid_order'));
                        }

                        $refund_data = $refunded_transactions_query->row;
                        $remaining_amount = (double) $refund_data['paid_price'] - (double) $refund_data['total_refunded'];

                        $diff = (string) $amount - (string) $remaining_amount;
                        if ($diff > 0) {
                                throw new \Exception(sprintf($this->language->get('request_amount_not_greater_than'), $remaining_amount));
                        }

                        IyzipayBootstrap::init();

                        $merchant_api_id = $this->config->get('iyzico_checkout_form_api_id_live');
                        $merchant_secret_key = $this->config->get('iyzico_checkout_form_secret_key_live');

                        $options = new \Iyzipay\Options();
                        $options->setApiKey($merchant_api_id);
                        $options->setSecretKey($merchant_secret_key);
                        $options->setBaseUrl($this->base_url);

                        $request = new \Iyzipay\Request\CreateRefundRequest();
                        $locale = \Iyzipay\Model\Locale::EN;
                        $siteLang = explode('_', $order_details['language_code']);
                        if($siteLang[0] == 'tr'){
                            $locale = \Iyzipay\Model\Locale::TR;
                        }
                        $request->setLocale($locale);
                        $request->setConversationId(uniqid($this->order_prefix) . "_refund_{$order_id}_{$item_id}");
                        $request->setPaymentTransactionId($refund_data['payment_transaction_id']);
                        $request->setPrice($amount);
                        $request->setCurrency($this->getCurrencyConstant($order_details['currency_code']));
                        $request->setIp($this->request->server['REMOTE_ADDR']);

                        $product_data_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` " .
                                "WHERE `product_id` = '{$item_id}' " .
                                "AND `language_id` = '{$language_id}'");
                        $product_data = $product_data_query->row;

                        $amount_formated = $this->currency->format($amount, $order_details['currency_code'], "1");
                        $success_refund_message = sprintf($this->language->get('refund_done_success'), $item_id, $product_data['name'], $amount_formated);

                        $save_data_array = array(
                            'order_id' => $order_id,
                            'transaction_status' => 'in process',
                            'date_created' => date('Y-m-d H:i:s'),
                            'date_modified' => date('Y-m-d H:i:s'),
                            'api_request' => $request->toJsonString(),
                            'api_response' => '',
                            'item_id' => $item_id,
                            'request_type' => 'order_refund',
                            'note' => ''
                        );
                        $refund_transaction_id = $this->model_payment_iyzico_checkout_form->createOrderEntry($save_data_array);

                        $response = \Iyzipay\Model\Refund::create($request, $options);

                        $update_data_array = array(
                            'date_modified' => date('Y-m-d H:i:s'),
                            'api_response' => $response->getRawResult(),
                            'transaction_status' => $response->getStatus(),
                            'processing_timestamp' => date('Y-m-d H:i:s', $response->getSystemTime() / 1000),
                            'note' => ($response->getStatus() == "failure") ? $response->getErrorMessage() : $success_refund_message
                        );
                        $this->model_payment_iyzico_checkout_form->updateOrderEntry($update_data_array, $refund_transaction_id);

                        if ($response->getStatus() == "failure") {
                                throw new \Exception($response->getErrorMessage());
                        }

                        $new_refunded_total = (double) $refund_data['total_refunded'] + $amount;

                        $update_refund_query_string = "UPDATE `" . DB_PREFIX . "iyzico_order_refunds` " .
                                "SET `total_refunded` = '{$new_refunded_total}' " .
                                "WHERE `order_id` = '{$order_id}' AND `item_id` = '{$item_id}'";

                        $this->db->query($update_refund_query_string);
                        
                        $this->_addhistory($order_id, $order_details['order_status_id'], $success_refund_message);

                        $data['message'] = $success_refund_message;
                        $data['success'] = "true";
                } catch (\Exception $ex) {
                        $data['message'] = $ex->getMessage();
                }

                echo json_encode($data);
        }

        protected function validate() {
                if (!$this->user->hasPermission('modify', 'payment/iyzico_checkout_form')) {
                        $this->error['warning'] = $this->language->get('error_permission');
                }

                $validation_array = array(
                    'api_id_live',
                    'secret_key_live'
                );

                foreach ($validation_array as $key) {
                        if (empty($this->request->post["iyzico_checkout_form_{$key}"])) {
                                $this->error[$key] = $this->language->get("error_$key");
                        }
                }

                if (!$this->error) {
                        return true;
                } else {
                        return false;
                }
        }

        private function _addhistory($order_id, $order_status_id, $comment) {

            $this->load->model('sale/order');
            $this->model_sale_order->addOrderHistory($order_id, array(
                    'order_status_id' => $order_status_id,
                    'notify' => 1,
                    'comment' => $comment
                        ));

                return true;
        }

        private function getCurrencyConstant($currencyCode){
                $currency = \Iyzipay\Model\Currency::TL;
                switch($currencyCode){
                        case "TRY":
                                $currency = \Iyzipay\Model\Currency::TL;
                                break;
                        case "USD":
                                $currency = \Iyzipay\Model\Currency::USD;
                                break;
                        case "GBP":
                                $currency = \Iyzipay\Model\Currency::GBP;
                                break;
                        case "EUR":
                                $currency = \Iyzipay\Model\Currency::EUR;
                                break;
                        case "IRR":
                                $currency = \Iyzipay\Model\Currency::IRR;
                                break;
                }
                return $currency;
        }

}
