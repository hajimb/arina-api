<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_model extends MY_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        $this->table = 'customer_master';
        $this->_condition = array();
    }    

    public function profile($id){
        $this->db->select("*");
        $this->db->from($this->table);
        $this->db->where("id", $id);
        // $this->db->order_by("name", "ASC");
        $sql = $this->db->get();
        $result= $sql->result_array();
        unset($result[0]['id']);
        unset($result[0]['delete_flag']);
        unset($result[0]['createdAt']);
        unset($result[0]['updatedAt']);
        return $result;
    }

    public function getZones($language){
        $this->db->select("zone_id, name");
        $this->db->from("oc_zone");
        $this->db->where("country_id", 114);
        $this->db->where("language_id", $language);
        $this->db->where("status", 1);
        $this->db->order_by("name", "ASC");
        $sql = $this->db->get();
        // print $this->db->last_query();
        return $sql->result_array();
    }


    public function deleteAddress($where){

        $this->db->select("*");
        $this->db->from("oc_address");
        $this->db->where("address_id", $where['address_id']);
        $this->db->where("customer_id", $where['customer_id']);
        $sql = $this->db->get();
        $row = $sql->num_rows();
        if($row==0){
            return array('msg' => 'Invalid Address id or token', 'status' => false, 'success' => true);
        }else{
            $this->db->select("*");
            $this->db->from("oc_customer");
            $this->db->where("address_id", $where['address_id']);
            $this->db->where("customer_id", $where['customer_id']);
            $sql = $this->db->get();
            $row = $sql->num_rows();
            // print $this->db->last_query();
            if($row > 0){
                return array('msg' => 'You can not delete your default address!', 'status' => false, 'success' => true);
            }else{
                $this->db->where($where);
                $this->db->delete($this->table);
                $error = $this->db->error();
                if($error['code'] == 0){
                    return array('msg' => 'Address has been successfully Deleted', 'status' => true, 'success' => true);
                }else{
                    return array('msg' => 'Error On deleting Address', 'status' => false, 'success' => true);
                }
            }
        }
    }


    public function getTotalAddresses($id) {
        $this->db->select(" COUNT(*) AS total");
        $this->db->from($this->table);
        $this->db->where('customer_id', $id);
        $sql = $this->db->get();
        $row = $sql->row();
        return $row->total;
    }

    public function getAddressById($where) {
        $this->db->select(" * ");
        $this->db->from($this->table);
        $this->db->where( $where);
        $sql = $this->db->get();
        $data = $sql->row_array();
        unset($data['customer_id']);
        return $data;
    }

    public function getAddress($id) {
        $this->db->select(" oca.*, occ.name AS country, occ.iso_code_2, occ.iso_code_3, ocz.name AS zone, ocz.code AS zone_code");
        $this->db->from("oc_address oca");
        $this->db->join('oc_country occ','oca.country_id = occ.country_id','left');
        $this->db->join('oc_zone ocz','oca.zone_id = ocz.zone_id','left');
        $this->db->where('oca.customer_id', $id);
        $sql = $this->db->get();
        return $sql->result_array();
    }

    public function addAddress($data, $where, $address_id=0) {
        $default = $data['default'];
        unset($data['default']);
        // check if addressid is exist or not
        if($address_id > 0){
            $this->db->select("*");
            $this->db->from($this->table);
            $this->db->where("address_id", $address_id);
            $this->db->where("customer_id", $data['customer_id']);
            $sql = $this->db->get();
            $row = $sql->num_rows();
            if($row > 0){
                $this->db->where('address_id', $address_id);
                $this->db->where('customer_id', $data['customer_id']);
                $this->db->update($this->table, $data);
            }else{
                return array('msg' => 'Invalid Address id', 'status' => false, 'success' => true ,'address_id' => $address_id);
            }
        }else{
            $this->db->insert($this->table, $data);
            $address_id = $this->db->insert_id();
        }
        // print $this->db->last_query();
        if ($default) {
            $update = array('address_id' => $address_id);
            $this->db->where($where);
            $this->db->update('oc_customer', $update);
            // print $this->db->last_query();
            $error = $this->db->error();
            if($error['code'] == 0){
                return array('msg' => 'Address has been successfully added', 'status' => true, 'success' => true, 'address_id' => $address_id);
            }else{
                return array('msg' => 'Error On adding Address', 'status' => false, 'success' => true);
            }
        }else{
            $this->db->select("*");
            $this->db->from("oc_customer");
            $this->db->where("address_id", $address_id);
            $this->db->where("customer_id", $data['customer_id']);
            $sql = $this->db->get();
            $row = $sql->num_rows();
            if($row > 0){
                $update = array('address_id' => '');
                $this->db->where("customer_id", $data['customer_id']);
                $this->db->update('oc_customer', $update);
            }
            return array('msg' => 'Address has been successfully added', 'status' => true, 'success' => true,'address_id' => $address_id);
        }
    }

    public function checkemail($email)   {
        $this->db->select('*')->from('oc_customer')->where('email',$email);
        $query = $this->db->get();
        $rows  = $query->num_rows();
        if($rows > 0){
            return $query->row_array();
        }else{
            return array();
        }
    }
    
    public function updatetoken($email,$token)  {
        $data = ['code' =>$token];
        $this->db->where('email', $email);
        $this->db->update('oc_customer',$data);
        $error = $this->db->error();
        if($error['code'] == 0){
            return array('msg' => 'Password link Successfully Sent', 'status' => true);
        }else{
            return array('msg' => 'Error While Sending Resend Password link ', 'status' => false);
        }
    }
}