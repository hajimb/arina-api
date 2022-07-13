<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Slider extends REST_Controller
{
    private $imgpath;
    public function __construct()
    {
        parent::__construct();
        header("Access-Control-Allow-Origin: * ");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $this
            ->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this
            ->output
            ->set_header('Pragma: no-cache');
        $this->imgpath = "https://api.sheeek.online/api/uploads/";
        $this->load->helper('directory');
    }

    public function main_get()
    {
        $result = $this->scanDirectoryImages($this->imgpath . 'main/', 'main');
        $this->response(['status' => true, 'validate' => true, 'data' => $result], 200);
    }

    public function brand_get()
    {
        $result = $this->scanDirectoryImages($this->imgpath . 'main/', 'brand');
        $this->response(['status' => true, 'validate' => true, 'data' => $result], 200);
    }

    private function scanDirectoryImages($directory, $cate)
    {
    
        // if (substr($directory, -1) == '/')
        // {

        //     $directory = substr($directory, 0, -1);
        // }
        if($cate=='brand'){
            // $imgParth= base_url().'/uploads/'.$cate.'/';
            $imgParth= 'https://sheeek.online/api/uploads/'.$cate.'/';
            $directory = FCPATH.'/uploads/'.$cate;
            $map = directory_map($directory);
            $count = count($map);
            $data=array();
            if($count > 0){
                foreach ($map as  $value) {
                    $data[]['image'] = $imgParth.$value;
                }
            }
        }else{

            $imgParth= "https://sheeek.online/image/catalog/slider/";
            $directory = '/home/sheeekon/public_html/image/catalog/slider/';
            $map = directory_map($directory);
            $count = count($map);
            $data=array();
            if($count > 0){
                foreach ($map as  $value) {
                    $data[]['image'] = $imgParth.$value;
                }
            }
        }
        return $data;
    }
}

