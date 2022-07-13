<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Product extends REST_Controller{

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: * ");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('Product_model' ,'product');
    }

    
    public function index_get($brand=0){
        $language = $_SERVER['HTTP_LANGUAGE'] ?? '';
        if($language=='english' || $language == ''){
            $language =1;
        }else{
            $language = 2;
        }
        $result = $this->product->getProducts($brand,$language);
        $group  = array();
         if($brand==0){
            foreach ( $result as $value ) {
                $group[$value['manufecturer']][] = $value;
            }
        }else{
            $group = $result;
        }
        // print_r($result);exit;
        $this->response( [
            'status'   => TRUE,
            'validate' => TRUE,
            'data'     => $group
        ], 200 );
    }
    
    public function category_get($category=0){
        $language = $_SERVER['HTTP_LANGUAGE'] ?? '';
        if($language=='english' || $language == ''){
            $language =1;
        }else{
            $language = 2;
        }
        $result = $this->product->getProductsByCategory($category,$language);
        $group  = array();
         if($category==0){
            foreach ( $result as $value ) {
                $group[$value['manufecturer']][] = $value;
            }
        }else{
            $group = $result;
        }
        // print_r($result);exit;
        $this->response( [
            'status'   => TRUE,
            'validate' => TRUE,
            'data'     => $group
        ], 200 );
    }
    
    
    public function getNewProduct_get(){
        $language = $_SERVER['HTTP_LANGUAGE'] ?? '';
        if($language=='english' || $language == ''){
            $language =1;
        }else{
            $language = 2;
        }
        // $result = $this->product->getProducts($brand,$language);
        $result = $this->product->getNewProduct();
        // print_r($result);exit;
        $this->response( [
            'status'   => TRUE,
            'validate' => TRUE,
            'count'     => $result['count'],
            'title'     => $result['title'],
            'data'     => $result['data']
        ], 200 );
    }   
    
    
    public function getAccessoriesProduct_get(){
        $language = $_SERVER['HTTP_LANGUAGE'] ?? '';
        if($language=='english' || $language == ''){
            $language =1;
        }else{
            $language = 2;
        }
        $category_id = $this->db->query('select distinct category_id from oc_category_description where name = "Eyes Care"')->row()->category_id;
        $result = $this->product->getProductsByCategory($category_id, $language);
        // print_r($result);exit;
        $this->response( [
            'status'   => TRUE,
            'validate' => TRUE,
            'data'     => $result
        ], 200 );
    }
    
    
    public function getOfferProduct_get(){
        $language = $_SERVER['HTTP_LANGUAGE'] ?? '';
        if($language=='english' || $language == ''){
            $language =1;
        }else{
            $language = 2;
        }
        $result = $this->product->getOfferProduct($language);
        // print_r($result);exit;
        $this->response( [
            'status'   => TRUE,
            'validate' => TRUE,
            'data'     => $result
        ], 200 );
    }
    
    
    public function getproductbyid_post(){
        $verror     = array();
        $language = $_SERVER['HTTP_LANGUAGE'] ?? '';
        if($language=='english' || $language == ''){
            $language =1;
        }else{
            $language = 2;
        }
        $post_data  = json_decode(file_get_contents("php://input"), true);
        $this->form_validation->set_data($post_data);
        $this->form_validation->set_rules('product_id', 'Product Id Missing', 'required|numeric|trim');
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_message('required', ' %s');
        if ($this->form_validation->run()) {
            $productID = $post_data['product_id'];
            $result     = $this->product->getProductByID($productID, $language);
            $isOptions  = $this->product->getProductOptions($productID, $language);
            $resultData['data']     = $result;
            $resultData['options']  = $isOptions;
            $this->response( [
                'validate'  => TRUE,
                'data'      => $resultData,
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