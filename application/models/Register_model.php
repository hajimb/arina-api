<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Register_model extends MY_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        $this->table = 'oc_customer';
        $this->_primary_key = 'customer_id';
        $this->_condition = array();
    }    
    
    public function getCustomerGroup($customer_group_id){
        $this->db->select(" * ");
        $this->db->from('oc_customer_group cg');
        $this->db->join('oc_customer_group_description cgd','cg.customer_group_id = cgd.customer_group_id','left');
        $this->db->where('cg.customer_group_id', CUSTOMER_GROUP_ID);
        $this->db->where('cgd.language_id', LANGUAGE_ID);
        
        $query = $this->db->get();
        // print $this->db->last_query();
        return $query->row_array();
    }

    public function saveData($master, $id=0){
        
        $isEmail = $this->checkEmail($master['email'], $id);
        if($isEmail){
            return array('status' => false, 'msg' => 'Email Already Exists');
        }else{
            $this->db->trans_begin();
            if($id ==0){
                $customer_group_info = $this->getCustomerGroup($master['customer_group_id']);
                $this->db->insert($this->table, $master);
                $customer_id = $this->db->insert_id();
                if ($customer_group_info['approval']) {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'customer', date_added = NOW()");
                }
                $msg = "Registeration Successfully Completed";
            }else{
                $this->db->where('customer_id', $id);
                $this->db->update($this->table, $master);
                // print $this->db->last_query();
                $msg = "Profile Successfully Updated";
            }

            $error = $this->db->error();
            if($error['code'] == 0){
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    return array('status' => false, 'msg' => $msg);
                }
                else{
                    $this->db->trans_commit();
                    return array('status' => true, 'msg' => $msg);
                }
            }else{
                // print $this->db->last_query();
                return array('status' => false, 'msg' => $error['message']);
            }
        }
    }

    private function checkEmail($email, $id=0){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('email',$email);
        if($id>0){
            $this->db->where('customer_id !=',$id);
        }
        $query = $this->db->get();
        // print $this->db->last_query();
        return $query->num_rows(); 
    }

    
}