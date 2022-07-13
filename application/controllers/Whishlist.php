<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Whishlist extends REST_Controller{

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: * ");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('whishlist_model' ,'whishlist');
        $this->load->library('Authorization_Token','authorization_token');

    }

    /*
    1.  HTTP_OK
    2.  HTTP_BAD_REQUEST
    2.  HTTP_NOT_FOUND
    */

    public function index_post(){
        $verror     = array();
        $post_data  = json_decode(file_get_contents("php://input"), true);
        
        $token   = $this->authorization_token->validateToken();
                
        if($token['status']){
            $userData   = $token['data'];
            $id         = $userData->id;
            $this->form_validation->set_data($post_data);
            $this->form_validation->set_rules('product_id', 'product_id', 'required|numeric|trim');
            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_message('required', 'Enter %s');
            if ($this->form_validation->run()) {
                $product_id = $post_data['product_id'];
                $this->whishlist->_condition['customer_id']  = $id;
                $this->whishlist->_condition['product_id']  = $product_id;
                $post_data['customer_id'] = $id;
                $result = $this->whishlist->saveData($post_data);
                $this->response( [
                        'status'   => $result['status'],
                        'validate' => TRUE,
                        'message'  => $result['msg'],
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

    public function getData_get(){
        $token   = $this->authorization_token->validateToken();
        if($token['status']){
            $userData   = $token['data'];
            $id         = $userData->id;
            $result     = $this->whishlist->get($id);
            $this->response( [
                'status'   => TRUE,
                'validate' => TRUE,
                'data'     => $result,
            ], 200 );
           
        }else{
            $this->response( [
                'status'   => FALSE,
                'validate' => TRUE,
                'message'  => 'User '.$token['message'],
            ], 200 );
        }
    }

    public function deletelist_post(){
        $verror     = array();
        $post_data  = json_decode(file_get_contents("php://input"), true);
        $token      = $this->authorization_token->validateToken();
        if($token['status']){
            $userData   = $token['data'];
            $id         = $userData->id;
            $this->form_validation->set_data($post_data);
            $this->form_validation->set_rules('product_id', 'product_id', 'required|numeric|trim');
            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_message('required', 'Enter %s');
            if ($this->form_validation->run()) {
                $result = $this->whishlist->deleteData($post_data);
                $this->response( [
                        'status'   => $result['status'],
                        'validate' => TRUE,
                        'message'  => $result['msg'],
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