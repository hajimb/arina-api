<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Password extends REST_Controller{

    public function __construct(){
        parent::__construct();

        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0,pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');

        $this->load->model('Password_model' ,'password');
        $this->load->model('User_model' ,'users');
        $this->load->library('bcrypt');
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

    // Reset Password
    public function reset_post(){ 
        $verror     = array();
        $post_data  = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_data($post_data);
        $this->form_validation->set_rules('code', 'Token', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'First Name', 'required|trim');
        $this->form_validation->set_rules('confirm', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('confirm', 'Confirm Password', 'required|matches[password]');

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_message('required', 'Enter %s');

        if ($this->form_validation->run()) {
            $salt           = token(9);
            $email          = $post_data['email'];
            $password       = $post_data['password'];
            $code           = $post_data['code'];
            $hashpassword   = sha1($salt . sha1($salt . sha1($password)));
            $master = array(
                'password' => $hashpassword,
                'salt'     => $salt
            );
            
            $result = $this->password->resetpassword($email, $code, $master);
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
   
    // Forgot Password
    public function forgot_post(){ 
        $verror = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        $this->form_validation->set_data($post_data);
        $this->form_validation->set_rules('username', 'User Name', 'required|valid_email|trim');
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_message('required', 'Enter %s');
        // echo $post_data['username'];
        if ($this->form_validation->run()) {
            $email   = trim($post_data['username']);
            $token   = token(32);
            $result  = $this->users->checkemail($email);
            // print_r($result);
            if(count($result) > 0){
                $updatetoken = $this->users->updatetoken($email, $token);
                if($updatetoken['status'] == true){
                    $this->sendEmail($token, $email);
                } 
                 $this->response([
                    'status'    => $updatetoken['status'],
                    'validate'  => TRUE,
                    'message'   => $updatetoken['msg']
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status'    => false,
                    'validate'  => TRUE,
                    'message'   => 'Invalid Email'
                ], REST_Controller::HTTP_OK);
            }
        } else {
            foreach ($post_data as $key => $value) {
                $verror[$key] = form_error($key);
            }
            $this->response([
                'status'    => FALSE,
                'validate'  => FALSE,
                'message'   => $verror,
            ], REST_Controller::HTTP_OK);
        }
    }
    
    public function sendEmail($token,$receiver){
        $data['code'] = $token;
        $subject     = 'Reset Password';  
        $body        = $this->load->view('reset-password', $data, true);
        $this->load->library('email'); 
        $config              = array();
        $config['useragent'] = "CodeIgniter";
        $config['mailpath']  = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
        $config['protocol']  = "smtp";
        $config['smtp_host'] = "mail.sheeek.online";
        $config['smtp_port'] = "25";
        $config['smtp_user'] = 'no-reply@sheeek.online';
        $config['smtp_pass'] = 'G9#ZoRm1=LjX';
        $config['mailtype']  = 'html';
        $config['charset']   = 'utf-8';
        $config['newline']   = "\r\n";
        $config['crlf']      = "\r\n";
        $this->email->initialize($config);
        $this->email->from('no-reply@sheeek.online', 'Sheeek - Password reset request');
       
        $this->email->to($receiver);
        $this->email->subject($subject);
        // $body = $this->load->view('email/new-contact', $master, true);
        $this->email->message($body);

        $tst = $this->email->send();
        if($tst){
            return true;
            exit;
        }else{
            echo $this->email->print_debugger();
            exit;
        }
        // return $tst;
    }

    public function changePassword_post(){
        $verror  = array();
        $post_data = json_decode(file_get_contents("php://input"), true);
        $token   = $this->authorization_token->validateToken();
        if($token['status']){
            $userData = $token['data'];
            $id       = $userData->id;
          

            $this->form_validation->set_data($post_data);
            $this->form_validation->set_rules('oldpassword', 'First Name', 'required|trim');
            $this->form_validation->set_rules('newpassword', 'Last Name', 'required|trim');
            $this->form_validation->set_rules('confirmpassword', 'Confirm Password', 'required|matches[newpassword]');

            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_message('required', 'Enter %s');

            if ($this->form_validation->run()) {
              
                $hashpassword  =  $this->bcrypt->hash_password($post_data['newpassword']);
                $master = array(
                    'password' => $hashpassword
                );
                
                $result = $this->password->changepassword($master, $post_data['oldpassword'], $id);
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