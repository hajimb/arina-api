<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Order extends REST_Controller{

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: * ");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('Order_model' ,'order');
        $this->load->library('Authorization_Token','authorization_token');
        $this->load->library('user_agent');
    }

    /*
    1.  HTTP_OK
    2.  HTTP_BAD_REQUEST
    2.  HTTP_NOT_FOUND
    */

    

    public function create_post(){
        $verror         = array();
        $master_order  = array();
        $header     = $this->input->request_headers();
        
        
        // $explode    = explode(';', $header['Cookie']);
        // $lan        = explode('=', $explode[2]);
        // $language   = $lan[1];
        
        
        
        $language   = 'en-US,en;q=0.9';
        
        $post_data  = json_decode(file_get_contents("php://input"), true);
        log_message('error', json_encode($post_data));
        // print_r($post_data);
        $this->form_validation->set_data($post_data);
        $account      = $post_data['account'];
        $hasShipping  = $post_data['hasShipping'] && 0;
        $payment_method  = $post_data['payment_method'] ;
        $this->form_validation->set_rules('account', 'account', 'required|trim');
        
        $customer_id = 0;
        $master_order['user_agent']            = $header['User-Agent'];
        $master_order['accept_language']       = $language;
        $master_order['date_added']            = currDate();
        $master_order['date_modified']         = currDate();
        $master_order['affiliate_id']          = '0';
        $master_order['commission']            = '0';
        $master_order['marketing_id']          = '0';
        $master_order['tracking']              = '';
        $master_order['language_id']           = LANGUAGE_ID;
        $master_order['currency_id']           = CURRENCY_ID;
        $master_order['currency_code']         = CURRENCY_CODE;
        $master_order['currency_value']        = CURRENCY_VALUE;
        $master_order['customer_group_id']     = CUSTOMER_GROUP_ID;
        $master_order['store_id']              = STORE_ID;
        $master_order['store_name']            = STORE_NAME;
        $master_order['store_url']              = STORE_URL;
        $master_order['ip']                     = $this->input->ip_address();
        $master_order['forwarded_ip']           = $this->input->ip_address();
        $master_order['invoice_prefix']         = 'INV-2021-00';
        $master_order['custom_field']           = '[]';
        $master_order['payment_custom_field']   = $post_data['payment_custom_field'];
        $master_order['shipping_custom_field']  = '[]';
        $master_order['shipping_code']          = 'flat.flat';
        $master_order['payment_address_format'] = '';
        $master_order['payment_company'] = '';
        $master_order['shipping_company'] = '';
        $master_order['shipping_address_format'] ='';
        $master_order['shipping_method'] = 'Flat Shipping';
        $master_order['shipping_code'] = 'flat.shipping_code';
        $master_order['payment_address_format'] ='';
        $master_order['payment_method'] = $post_data['payment_method'];
        $master_order['payment_code']   = $post_data['payment_code'];
        $master_order['order_from']     = "app";
        
        // print_r($master_order);
        if (array_key_exists("Authorization", $header)) {
            $token   = $this->authorization_token->validateToken();
            if($token['status']){
                $userData    = $token['data'];
                $customer_id = $userData->id;
            }else{
                $this->response( [
                    'status'   => FALSE,
                    'validate' => TRUE,
                    'message'  => 'User '.$token['message'],
                ], 200 );
            }
        } else {
            $customer_id = 0;
            if($account == 'guest'){
                $this->form_validation->set_rules('name', 'name', 'required|trim');
                $this->form_validation->set_rules('telephone', 'telephone', 'required|trim');
                $this->form_validation->set_rules('email', 'email', 'required|valid_email|trim');
                $this->form_validation->set_rules('governate_id', 'governate_id', 'required|numeric|trim');
                $this->form_validation->set_rules('block', 'block', 'required|trim');
                $this->form_validation->set_rules('street', 'street', 'required|trim');
                $this->form_validation->set_rules('building', 'building', 'required|trim');
                $this->form_validation->set_rules('floor', 'floor', 'required|trim');
            }
        }
        
        // $discount['coupon_code'] = $post_data['discount']['coupon_code'];
        $discount['customer_id'] = $customer_id;
        $discount['coupon_id'] = $post_data['discount']['coupon_id'];
        $discount['amount'] = '-'.$post_data['discount']['coupon_amount'];

        $master_order['name'] = $post_data['name'];
        $master_order['telephone'] = $post_data['telephone'];
        $master_order['governate_id'] = $post_data['governate_id'];
        $master_order['governate'] = getzone($post_data['governate_id']);
        $master_order['block'] = $post_data['block'];
        $master_order['street'] = $post_data['street'];
        $master_order['building'] = $post_data['building'];
        $master_order['floor'] = $post_data['floor'];
        $master_order['flat'] = (isset($post_data['flat']) ? $post_data['flat'] : '');
        $master_order['paci'] = (isset($post_data['paci']) ? $post_data['paci'] : '');
        
        if($hasShipping==1){
            $this->form_validation->set_rules('shipping_name', 'name', 'required|trim');
            $this->form_validation->set_rules('shipping_governate_id', 'governate_id', 'required|numeric|trim');
            $this->form_validation->set_rules('shipping_block', 'block', 'required|trim');
            $this->form_validation->set_rules('shipping_street', 'street', 'required|trim');
            $this->form_validation->set_rules('shipping_building', 'building', 'required|trim');
            $this->form_validation->set_rules('shipping_floor', 'floor', 'required|trim');
            
            $master_order['shipping_name'] = $post_data['name'];
            $master_order['shipping_governate_id'] = $post_data['governate_id'];
            $master_order['shipping_governate'] = getzone($post_data['governate_id']);
            $master_order['shipping_block'] = $post_data['block'];
            $master_order['shipping_street'] = $post_data['street'];
            $master_order['shipping_building'] = $post_data['building'];
            $master_order['shipping_floor'] = $post_data['floor'];
            $master_order['shipping_flat'] = (isset($post_data['flat']) ? $post_data['flat'] : '');
            $master_order['shipping_paci'] = (isset($post_data['paci']) ? $post_data['paci'] : '');
        }else{
            $master_order['payment_name'] = $post_data['name'];
            $master_order['payment_governate_id'] = $post_data['governate_id'];
            $master_order['payment_governate'] = getzone($post_data['governate_id']);
            $master_order['payment_block'] = $post_data['block'];
            $master_order['payment_street'] = $post_data['street'];
            $master_order['payment_building'] = $post_data['building'];
            $master_order['payment_floor'] = $post_data['floor'];
            $master_order['payment_flat'] = $post_data['flat'];
            $master_order['payment_paci'] = $post_data['paci'];
            
            $master_order['shipping_name'] = $post_data['name'];
            $master_order['shipping_governate_id'] = $post_data['governate_id'];
            $master_order['shipping_governate'] = getzone($post_data['governate_id']);
            $master_order['shipping_block'] = $post_data['block'];
            $master_order['shipping_street'] = $post_data['street'];
            $master_order['shipping_building'] = $post_data['building'];
            $master_order['shipping_floor'] = $post_data['floor'];
            $master_order['shipping_flat'] = (isset($post_data['flat']) ? $post_data['flat'] : '');
            $master_order['shipping_paci'] = (isset($post_data['paci']) ? $post_data['paci'] : '');
        }
        // print_r($master_order);

        if ($this->form_validation->run()) {
            $master_order['customer_id'] = $customer_id;
            $master_order['name'] = $post_data['name'];
            $master_order['email'] = $post_data['email'];
            $master_order['telephone'] = $post_data['telephone'];
            $master_order['comment'] = $post_data['comment'];
            $master_order['total'] = $post_data['total'];
            $products = $post_data['products'];
            // print_r($products);exit;
            // print_r($master_order);exit;
            $result      = $this->order->create($master_order, $products, $discount);
            $this->response( [
                'status'   => TRUE,
                'validate' => TRUE,
                'message'  => $result['msg'],
            ], 200 );
        }else{
            foreach ($post_data as $key => $value) {
                $verror[$key] = form_error($key);
            }
            $this->response( [
                'status'   => FALSE,
                'validate' => FALSE,
                'message'  => $verror,
            ], 200 );
        }
    }

    public function index_get(){
        $verror  = array();
        $token = $this->authorization_token->validateToken();
        if($token['status']){
            $userData = $token['data'];
            $customer_id = $userData->id;
            $result = $this->order->getOrder($customer_id);
            $this->response( [
                'status'   => TRUE,
                'validate' => TRUE,
                'message'  => $result,
            ], 200 );
        }else{
            $this->response( [
                'status'   => FALSE,
                'validate' => TRUE,
                'message'  => 'User '.$token['message'],
            ], 200 );
        }
    }

    public function detail_post(){
        $verror  = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        // print_r($post_data);
        $token = $this->authorization_token->validateToken();
        if($token['status']){
            $userData = $token['data'];
            $customer_id = $userData->id;
            // $this->_apiConfig(['methods' => ['POST']]); 
            $this->form_validation->set_data($post_data);
            $this->form_validation->set_rules('order_id', 'Order Id', 'required|numeric|trim');
            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_message('required', 'Enter %s');

            if ($this->form_validation->run()) {
                $order_id = $post_data['order_id'];

                $condition = array(
                    'order_id' => $order_id,
                    'customer_id' => $customer_id
                );
                $result['orderDetail']   = $this->order->getOrderById($condition);
                $result['orderProducts'] = $this->order->getOrderProducts($order_id);
                $result['orderHistory'] = $this->order->getOrderHistory($order_id);
                if(count($result['orderDetail']) >0){
                    $this->response( [
                        'status'   => TRUE,
                        'validate' => TRUE,
                        'message'  => $result
                    ], 200 );
                }else{
                    $this->response( [
                        'status'   => FALSE,
                        'validate' => TRUE,
                        'message'  => 'Invalid Order Id'
                    ], 200 );
                }
               
            } else {
                foreach ($post_data as $key => $value) {
                    $verror[$key] = form_error($key);
                }
                $this->response( [
                    'status'   => FALSE,
                    'validate' => FALSE,
                    'message'  => $verror,
                ], 200 );
            }
        }else{
            $this->response( [
                'status'   => FALSE,
                'validate' => TRUE,
                'message'  => 'User '.$token['message'],
            ], 200 );
        }
    }
}