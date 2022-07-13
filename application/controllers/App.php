<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class App extends REST_Controller
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
       $this->load->model('App_model' ,'appmodel');
    }

    public function checkversion_post()
    {
       $verror = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        // echo $post_data;
        // print_r($_POST);
        // echo '<pre />';

        $this->form_validation->set_data($post_data);
        $this->form_validation->set_rules('devicetype', 'Device Type', 'required|trim');
        $this->form_validation->set_rules('version_code', 'Version Code', 'required|trim');
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_message('required', 'Enter %s');
        if ($this->form_validation->run()) {
            $result = $this->appmodel->checkversion($post_data);
            $this->response( [
                    'status'   => $result['status'],
                    'validate' => TRUE,
                    'message'  => $result['msg']
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

