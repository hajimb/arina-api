<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class User extends REST_Controller{

    public function __construct(){
        parent::__construct();
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');

        $this->load->model('User_model' ,'user');
        $this->load->library('Authorization_Token','authorization_token');
    }
    
    public function index_options(){
        $this->response(null, REST_Controller::HTTP_OK);
    }


    /*
    1.  HTTP_OK
    2.  HTTP_BAD_REQUEST
    2.  HTTP_NOT_FOUND
    */
        // Array ( [firstname] => Sheikh [lastname] => Arshad [address_1] => fsdfdsf sdfsd fdsf [address_2] => [city] => Thane [postcode] => 401107 [country_id] => 114 [zone_id] => 1788 [default] => 0 )

    public function profile_post(){
        $verror  = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        
        $token   = $this->authorization_token->validateToken();
        if($token['status']){
            $userData = $token['data'];
            $id       = $userData->id;
            $result   = $this->user->profile($id);
            $this->response( [
                    'status'   => TRUE,
                    'validate' => TRUE,
                    'data'      => $result,
            ], 200 );
           
        }else{
            $this->response( [
                'status'   => FALSE,
                'validate' => TRUE,
                'message'  => 'User '.$token['message'],
            ], 200 );
        }
    }

    public function deleteAddress_post(){
        $verror  = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        $token   = $this->authorization_token->validateToken();
        if($token['status']){
            $userData = $token['data'];
            $id       = $userData->id;

            $this->form_validation->set_data($post_data); 
            $this->form_validation->set_rules('address_id', 'address_id', 'required|trim');
            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_message('required', 'Enter %s');
            if ($this->form_validation->run()) {
                $address_id = $post_data['address_id'];
                $where      = array( 'customer_id' => $id, 'address_id'=> $address_id);
                $result     = $this->user->deleteAddress($where);
                $this->response( [
                    'status'   => TRUE,
                    'validate' => TRUE,
                    'data'      => $result,
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
        }else{
            $this->response( [
                'status'   => FALSE,
                'validate' => TRUE,
                'message'  => 'User '.$token['message'],
            ], 200 );
        }
    }

   


}