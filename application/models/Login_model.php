<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login_model extends MY_Model
{
    public $table;
    public function __construct() {
        parent::__construct();
        // Load Authorization Library or Load in autoload config file
        $this->load->library('Authorization_Token','authorization_token');
        $this->load->helper('string');
        $this->load->library('bcrypt');
        $this->table = 'customer_master';
    }

    public function index(array $data){
        
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('contact_email', $data['username']);

        $query   = $this->db->get();
        $numrows = $query->num_rows(); 
        // print $this->db->last_query();
        if ($numrows === 1) {
            $row = $query->row();
            
            // $salt           = $row->salt;
            $password       = $data['password'];
            //  $checkpassword = $this->checkpassword($old_password, $row->password);

                $checkpassword = $this->checkpassword($password, $row->password);
                if ($checkpassword == 1) {
                    // you user authentication code will go here, you can compare the user with the database or whatever
                    $secretKey = $this->bcrypt->hash_password(random_string('alnum', 8));
                    $payload = [
                        'id'         => $row->id,
                        'email'      => $row->contact_email,
                        'secretKey'  => $secretKey,
                        'mobile'     => $row->contact_phone,
                    ];
                    $token = $this->authorization_token->generateToken($payload);
                    $sessionData = array(
                        'email'         => $row->contact_email,
                        'company_name'  => $row->company_name,
                        'contact_name'  => $row->contact_name,
                        'contact_number'  => $row->company_phone,
                        'logo_image'  => $row->logo_image,
                        'product_image_path' => $this->config->item('image_url').'assets/uploads/designs/',
                        'customer_image_path' => $this->config->item('image_url').'assets/uploads/customer/',
                        'token'     => $token,
                    );

                    $result = array('msg' => 'Successfully Login!', 'status' => true, 'success' => true, 'data'=> $sessionData);
                } else {
                    $result = array('msg' => 'Incorrect Password!', 'status' => false, 'success' => true, 'data'=> '');
                }
           
        } else {
            $result = array('msg' => 'No Record Found!', 'status' => false, 'success' => true, 'data' => '');
        }
        return $result;
    }  

    

    public function checkpassword($password, $stored_hash){
        if ($this->bcrypt->check_password($password, $stored_hash)) {
            return 1;
        } else {
            return 0;
        }
    }
}
