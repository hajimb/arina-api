<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Brand extends REST_Controller{

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: * ");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('Brand_model' ,'brand');
    }

    public function index_get(){
        $result = $this->brand->getBrand($_POST);
        $this->response( [
            'status'   => TRUE,
            'validate' => TRUE,
            'data'     => $result
        ], 200 );
    }

    public function category_get(){
        $language = $_SERVER['HTTP_LANGUAGE'] ?? '';
        if($language=='english' || $language == ''){
            $language =1;
        }else{
            $language = 2;
        }
        $result = $this->brand->getCategory($language);
        $this->response( [
            'status'   => TRUE,
            'validate' => TRUE,
            'data'     => $result
        ], 200 );
    }
}