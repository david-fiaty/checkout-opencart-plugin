<?php
class Controller_Methods_creditcard extends Controller_Methods_Abstract implements Controller_Interface
{

    public function getData()
    {
        $this->language->load('payment/checkoutapipayment');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $config['debug'] = false;
        $config['email'] =  $order_info['email'];
        $config['name'] = $order_info['firstname']. ' '.$order_info['lastname'];
        $config['currency'] =  $this->currency->getCode();
        $config['widgetSelector'] =  '.widget-container';
        $paymentTokenArray = $this->generatePaymentToken();
        $localPayment = $this->config->get('localpayment_enable');
        $mode = $this->config->get('test_mode');
        $amount = ($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false))*100;

        if($mode == 'live'){
            $url = 'https://cdn.checkout.com/js/checkout.js';
        } else {
            $url = 'https://cdn.checkout.com/sandbox/js/checkout.js';
        }

        if($localPayment == 'yes'){
            $paymentMode = 'mixed';
        } else {
            $paymentMode = 'card';
        }

        if($this->config->get('save_card') == 'yes'){

            if($this->customer->getId()){
                $this->session->data['customer_login'] = 'yes';

                $cardList = $this->getCustomerCardList($this->customer->getId());
            
                $this->session->data['cardLists']= '';
                
                if(!empty($cardList)){
                    foreach ($cardList as $key) {
                        $test[] = $key;
                    }

                    $this->session->data['cardLists'] = $test;
                }
            } else {
                $this->session->data['customer_login'] = 'no';
            }
        }

        $billingAddressConfig = array(
            'addressLine1'       =>  $order_info['payment_address_1'],
            'addressLine2'       =>  $order_info['payment_address_2'],
            'postcode'           =>  $order_info['payment_postcode'],
            'country'            =>  $order_info['payment_iso_code_2'],
            'city'               =>  $order_info['payment_city'],
            'phone'              =>  array('number' => $order_info['telephone']),

        );

        $toReturn = array(
            'text_card_details' =>  $this->language->get('text_card_details'),
            'text_wait'         =>  $this->language->get('text_wait'),
            'entry_public_key'  =>  $this->config->get('public_key'),
            'order_email'       =>  $order_info['email'],
            'order_currency'    =>  $this->currency->getCode(),
            'amount'            =>  $amount,
            'title'             =>  $this->config->get('config_name'),
            'publicKey'         =>  $this->config->get('public_key'),
            'url'               =>  $url,
            'paymentMode'       =>  $paymentMode,
            'email'             =>  $order_info['email'],
            'name'              =>  $order_info['firstname']. ' '.$order_info['lastname'],
            'paymentToken'      =>  $paymentTokenArray['token'],
            'message'           =>  $paymentTokenArray['message'],
            'success'           =>  $paymentTokenArray['success'],
            'eventId'           =>  $paymentTokenArray['eventId'],
            'textWait'          =>  $this->language->get('text_wait'),
            'logoUrl'           =>  $this->config->get('logo_url'),
            'themeColor'        =>  $this->config->get('theme_color'),
            'buttonColor'       =>  $this->config->get('button_color'),
            'iconColor'         =>  $this->config->get('icon_color'),
            'currencyFormat'    =>  $this->config->get('currency_format'),
            'button_confirm'    =>  $this->language->get('button_confirm'),
            'trackId'           =>  $order_info['order_id'],
            'addressLine1'      =>  $order_info['payment_address_1'],
            'addressLine2'      =>  $order_info['payment_address_2'],
            'postcode'          =>  $order_info['payment_postcode'],
            'country'           =>  $order_info['payment_iso_code_2'],
            'city'              =>  $order_info['payment_city'],
            'phone'             =>  $order_info['telephone'],
            'save_card'         =>  $this->config->get('save_card')
        );

        foreach ($toReturn as $key=>$val) {

            $this->data[$key] = $val;
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/checkoutapi/creditcard.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/checkoutapi/creditcard.tpl';
        } else {
            $this->template = 'default/template/payment/checkoutapi/creditcard.tpl';
        }
 
        $toReturn['tpl'] =   $this->render();
        return $toReturn;
    }

    protected function _createCharge($order_info)
    { 
        $config = array();
        $scretKey = $this->config->get('secret_key');
        $config['authorization'] = $scretKey  ;
        $config['timeout'] =  $this->config->get('gateway_timeout');
        $config['paymentToken']  = $this->request->post['cko_cc_paymenToken'];

        $config = $this->getConfigData();

        if($this->request->post['cko-payment'] == 'new_card' && !empty($this->request->post['cko-card-token'])){
            $config['postedParam']['cardToken'] = $this->request->post['cko-card-token'];

        } elseif($this->request->post['cko-payment'] == 'saved_card' && !empty($this->request->post['entity_id'])){

            $getCardId = $this->getCardId($this->request->post['entity_id']);
            $config['postedParam']['cardId'] = $getCardId[0]['card_id'];
        }

        $Api = CheckoutApi_Api::getApi(array('mode'=> $this->config->get('test_mode')));

        return $Api->createCharge($config);
    }

    public function getConfigData(){
        $config = array();
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $productsLoad= $this->cart->getProducts();
        $scretKey = $this->config->get('secret_key');
        $orderId = $this->session->data['order_id'];
        $amountCents = ($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false))*100;
        $config['authorization'] = $scretKey  ;
        $config['mode'] = $this->config->get('test_mode');
        $config['timeout'] =  $this->config->get('gateway_timeout');

        if($this->config->get('payment_action') =='capture') {
            $config = array_merge($config, $this->_captureConfig());

        }else {

            $config = array_merge($config,$this->_authorizeConfig());
        }

        $products = array();
        foreach ($productsLoad as $item ) {

            $products[] = array (
                'name'       =>     $item['name'],
                'sku'        =>     $item['key'],
                'price'      =>     $this->currency->format($item['price'], $this->currency->getCode(), false, false),
                'quantity'   =>     $item['quantity']
            );
        }

        $billingAddressConfig = array(
            'addressLine1'       =>  $order_info['payment_address_1'],
            'addressLine2'       =>  $order_info['payment_address_2'],
            'postcode'           =>  $order_info['payment_postcode'],
            'country'            =>  $order_info['payment_iso_code_2'],
            'city'               =>  $order_info['payment_city'],
            'phone'              =>  array('number' => $order_info['telephone']),

        );

        $shippingAddressConfig = array(
            'addressLine1'       =>  $order_info['shipping_address_1'],
            'addressLine2'       =>  $order_info['shipping_address_2'],
            'postcode'           =>  $order_info['shipping_postcode'],
            'country'            =>  $order_info['shipping_iso_code_2'],
            'city'               =>  $order_info['shipping_city'],
            'phone'              =>  array('number' => $order_info['telephone']),

        );

        $config['postedParam'] = array_merge($config['postedParam'],array (
            'customerName'       =>  $order_info['payment_firstname']. ' ' .$order_info['payment_lastname'],
            'email'              =>  $order_info['email'],
            'value'              =>  $amountCents,
            'trackId'            =>  $orderId,
            'currency'           =>  $this->currency->getCode(),
            'chargeMode'         =>  $this->config->get('is_3d'),
            'description'        =>  "Order number::$orderId",
            'shippingDetails'    =>  $shippingAddressConfig,
            'products'           =>  $products,
            'billingDetails'     =>  $billingAddressConfig,
            'metadata'           => array(
                'server'            => $this->config->get('config_url'),
                'quoteId'           => $orderId,
                'opencart_version'  => VERSION,
                'plugin_version'    => PLUGIN_VERSION,
                'lib_version'       => CheckoutApi_Client_Constant::LIB_VERSION,
                'integration_type'  => 'CheckoutJs',
                'time'              => date('Y-m-d H:i:s')
            ),

        ));

        return $config;
    }

    public function generatePaymentToken()
    {
        $config = array();
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $productsLoad= $this->cart->getProducts();
        $scretKey = $this->config->get('secret_key');
        $orderId = $this->session->data['order_id'];
        $amountCents = ($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false))*100;
        $config['authorization'] = $scretKey  ;
        $config['mode'] = $this->config->get('test_mode');
        $config['timeout'] =  $this->config->get('gateway_timeout');

        if($this->config->get('payment_action') =='capture') {
            $config = array_merge($config, $this->_captureConfig());

        }else {

            $config = array_merge($config,$this->_authorizeConfig());
        }

        $products = array();
        foreach ($productsLoad as $item ) {

            $products[] = array (
                'name'       =>     $item['name'],
                'sku'        =>     $item['key'],
                'price'      =>     $this->currency->format($item['price'], $this->currency->getCode(), false, false),
                'quantity'   =>     $item['quantity']
            );
        }

        $billingAddressConfig = array(
            'addressLine1'       =>  $order_info['payment_address_1'],
            'addressLine2'       =>  $order_info['payment_address_2'],
            'postcode'           =>  $order_info['payment_postcode'],
            'country'            =>  $order_info['payment_iso_code_2'],
            'city'               =>  $order_info['payment_city'],
            'phone'              =>  array('number' => $order_info['telephone']),

        );

        $shippingAddressConfig = array(
            'addressLine1'       =>  $order_info['shipping_address_1'],
            'addressLine2'       =>  $order_info['shipping_address_2'],
            'postcode'           =>  $order_info['shipping_postcode'],
            'country'            =>  $order_info['shipping_iso_code_2'],
            'city'               =>  $order_info['shipping_city'],
            'phone'              =>  array('number' => $order_info['telephone']),
            'state'              =>  $order_info['shipping_zone'],          

        );

        $config['postedParam'] = array_merge($config['postedParam'],array (
            'customerName'       =>  $order_info['payment_firstname']. ' ' .$order_info['payment_lastname'],
            'email'              =>  $order_info['email'],
            'value'              =>  $amountCents,
            'trackId'            =>  $orderId,
            'currency'           =>  $this->currency->getCode(),
            'description'        =>  "Order number::$orderId",
            'shippingDetails'    =>  $shippingAddressConfig,
            'products'           =>  $products,
            'billingDetails'     =>  $billingAddressConfig,
            'metadata'           => array(
                'server'            => $this->config->get('config_url'),
                'quoteId'           => $orderId,
                'opencart_version'  => VERSION,
                'plugin_version'    => PLUGIN_VERSION,
                'lib_version'       => CheckoutApi_Client_Constant::LIB_VERSION,
                'integration_type'  => 'FramesJs',
                'time'              => date('Y-m-d H:i:s')
            ),

        ));

        $Api = CheckoutApi_Api::getApi(array('mode' => $this->config->get('test_mode')));
        $paymentTokenCharge = $Api->getPaymentToken($config);

        $paymentTokenArray    =   array(
            'message'   =>    '',
            'success'   =>    '',
            'eventId'   =>    '',
            'token'     =>    '',
        );

        if($paymentTokenCharge->isValid()){

            $paymentTokenArray['token'] = $paymentTokenCharge->getId();
            $paymentTokenArray['success'] = true;

        }else {

            $paymentTokenArray['message']    =    $paymentTokenCharge->getExceptionState()->getErrorMessage();
            $paymentTokenArray['success']    =    false;
            $paymentTokenArray['eventId']    =    $paymentTokenCharge->getEventId();
        }

        return $paymentTokenArray;
    }

    public function getCustomerCardList($customerId) {
        $sql = "SELECT * FROM ".DB_PREFIX."checkout_customer_cards WHERE customer_id = '".$customerId."' AND card_enabled = '1'";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getCardId($entityId){
        $sql = 'SELECT card_id FROM '.DB_PREFIX."checkout_customer_cards WHERE entity_id = '".$entityId."'";

        $query = $this->db->query($sql);

        return $query->rows;
    }
}