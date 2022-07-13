<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Register extends REST_Controller{

    public function __construct(){
        parent::__construct();
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        
        $this->load->model('Register_model' ,'register');
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

    public function index_post(){
        $verror = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        $this->form_validation->set_data($post_data);
        $id     = isset($post_data['id']) && 0;
        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('telephone', 'Contact Number', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        $this->form_validation->set_rules('confirm', 'Confirm Password', 'required|trim');
        $this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
        $this->form_validation->set_rules('newsletter', 'Confirm Password', 'required|trim');
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_message('required', 'Enter %s');
        if ($this->form_validation->run()) {
            $salt       = token(9);
            $password   = $post_data['password'];
            $telephone  = $post_data['telephone'];
            $hashpassword = sha1($salt . sha1($salt . sha1($password)));
            $master = array(
                'customer_group_id' => CUSTOMER_GROUP_ID,
                'store_id'          => STORE_ID,
                'LANGUAGE_ID'       => LANGUAGE_ID,
                'firstname'         => $post_data['firstname'],
                'lastname'          => $post_data['lastname'],
                'email'             => $post_data['email'],
                'telephone'         => $telephone,
                'custom_field'      => CUSTOM_FIELD,
                'salt'              => $salt,
                'password'          => $hashpassword,
                'newsletter'        => $post_data['newsletter'],
                'ip'                => getIPAddress(),
                'status'            => STATUS,
                'date_added'        => currDate(),
            );
            $this->register->_condition['telephone']  = $telephone;
            $result = $this->register->saveData($master, $id);
            $this->response( [
                'status'   => $result['status'],
                'validate' => TRUE,
                'message'  => 'User '.$result['msg'],
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

    public function update_post(){
        $verror  = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        $token = $this->authorization_token->validateToken();
        if($token['status']){
            $userData = $token['data'];
            $id = $userData->id;
            // $this->_apiConfig(['methods' => ['POST']]); 
            $this->form_validation->set_data($post_data);
            $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
            $this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
            $this->form_validation->set_rules('telephone', 'Contact Number', 'required|trim');

            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_message('required', 'Enter %s');

            if ($this->form_validation->run()) {
                $salt   = token(9);
                $password       = $post_data['password'];
                $telephone      = $post_data['telephone'];
                $hashpassword   = sha1($salt . sha1($salt . sha1($password)));
                $master = array(
                    'firstname' => $post_data['firstname'],
                    'lastname'  => $post_data['lastname'],
                    'email'     => $post_data['email'],
                    'telephone' => $telephone
                );
                $result = $this->register->saveData($master, $id);
                $this->response( [
                    'status'   => $result['status'],
                    'validate' => TRUE,
                    'message'  => 'User '.$result['msg'],
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