<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Coupon extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        header("Access-Control-Allow-Origin: * ");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('Coupon_model', 'coupon');
        $this->load->library('Authorization_Token', 'authorization_token');
    }

    /*
    1.  HTTP_OK
    2.  HTTP_BAD_REQUEST
    2.  HTTP_NOT_FOUND
    */
    // Array ( [firstname] => Sheikh [lastname] => Arshad [address_1] => fsdfdsf sdfsd fdsf [address_2] => [city] => Thane [postcode] => 401107 [country_id] => 114 [zone_id] => 1788 [default] => 0 )

    public function index_post()
    {
        $verror     = array();
        $post_data  = json_decode(file_get_contents("php://input"), true);
        // print_r($post_data);
        $header     = $this->input->request_headers();
        if (array_key_exists("Authorization", $header)) {
            $token   = $this->authorization_token->validateToken();
            if ($token['status']) {
                $userData    = $token['data'];
                $customer_id = $userData->id;
            } else {
                $this->response([
                    'status'   => FALSE,
                    'validate' => TRUE,
                    'message'  => 'User ' . $token['message'],
                ], 200);
            }
        } else {
            $customer_id = 0;
        }
        $this->form_validation->set_data($post_data);
        $this->form_validation->set_rules('coupon_code', 'Coupon Code', 'required|trim');
        $this->form_validation->set_rules('subtotal', 'Sub Total', 'required|trim');
        if ($this->form_validation->run()) {
            $post_data['userid']=$customer_id;
            $resultData = $this->coupon->getCoupon($post_data);
            $this->response( [
                'validate'  => TRUE,
                'status'    => $resultData['status'],
                'msg'       => $resultData['msg'],
                'data'      => $resultData['data']
            ], 200 );
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
    }
}
