<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Login extends REST_Controller{

    public function __construct(){
        parent::__construct();
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        // header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
        // header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        $this->load->model('Login_model' ,'login');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        // $method = $_SERVER['REQUEST_METHOD'];
        // if($method == "OPTIONS") {
        //     die();
        // }
    }
    
    public function index_options(){
        $this->response(null, REST_Controller::HTTP_OK);
    }
    
    public function index_post(){
       
        $verror = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        // echo $client;
        // print_r($post_data);
        // echo '<pre />';
        // exit;
       
        $this->form_validation->set_data($post_data);
        $this->form_validation->set_rules('username', 'Username', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]|trim');
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_message('required', 'Enter %s');
        if ($this->form_validation->run()) {
            $result = $this->login->index($post_data);
            
            $this->response( [
                    'status'   => $result['status'],
                    'validate' => TRUE,
                    'message'  => $result['msg'],
                    'data'     => $result['data'],
            ], 200 );
        } else {
            foreach ($post_data as $key => $value) {
                $verror[$key] = form_error($key);
            }
            $this->response( [
                'status'   => FALSE,
                'validate' => FALSE,
                'message'  => $verror,
                'error'  => validation_errors(),
            ], 200 );
        }
    }
}