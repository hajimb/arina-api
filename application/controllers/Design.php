<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Design extends REST_Controller{

    public function __construct(){
        parent::__construct();
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');

        $this->load->model('Design_model' ,'design');
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

    public function index_post(){
       
        $verror  = array();
        // $post_data = json_decode(file_get_contents("php://input"), true);
        
        $token   = $this->authorization_token->validateToken();
        if($token['status']){
            $userData = $token['data'];
           
            $result   = $this->design->getDesign();
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

  

   


}