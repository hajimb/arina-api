<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Password_model extends MY_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        $this->table = 'customer_master';
        $this->_primary_key = 'id';
        $this->_condition = array();
    } 
    
    public function resetpassword($email, $code, $master){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('email', $email);
        $this->db->where('code', $code);
        $sql = $this->db->get();
        $rows = $sql->num_rows();
        if($rows ==0){
            return array('msg' => 'No Record Found', 'status' => false, 'success' => true);
        }else{
            $master['code'] = '';
            $this->db->where('email', $email);
            $this->db->where('code', $code);
            $this->db->update($this->table, $master);
            $error = $this->db->error();
            if($error['code'] == 0){
                return array('msg' => 'Password Successfully Reset', 'status' => true, 'success' => true);
            }else{
                return array('msg' => 'Error On Updating Passsword', 'status' => false, 'success' => true);
            }
        }
    }

     public function changepassword($master,$old_password,$userid){
        $this->db->where(array("id" => $userid));
        $query = $this->db->get($this->table);
        $row   = $query->row();
        // $newpassword = $this->bcrypt->hash_password($new_password);
        
        if($query->num_rows() === 1){
            $checkpassword = $this->checkpassword($old_password, $row->password);
            if($checkpassword == 1){
          
                $this->db->where('id' , $userid);
                $this->db->update($this->table, $master);
                return array('msg' => 'User Password Updated Succefully', 'status' => true, 'success' => true);
            }else{
                return array('msg' => 'Old Password Not Match', 'status' => false, 'success' => true);
            }
        }else{
            return array('msg' => 'No Record Found', 'status' => false, 'success' => true);
        }
    }   
    
    public function checkpassword($password, $stored_hash){
        if ($this->bcrypt->check_password($password, $stored_hash)) {
            return 1;
        } else {
            return 0;
        }
    }
}