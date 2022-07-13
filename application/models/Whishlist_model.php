<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Whishlist_model extends MY_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        $this->table = 'oc_customer_wishlist';
        $this->_primary_key = 'customer_id';
        $this->_condition = array();
    }    

    public function saveData($post_data){
        $this->db->trans_begin();
        $post_data['date_added'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $post_data);
        $error = $this->db->error();
        if($error['code'] == 0){
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return array('status' => false, 'msg' => 'Error while adding product to wishlist');
            }
            else{
                $this->db->trans_commit();
                return array('status' => true, 'msg' => 'Product Successfully added to wishlist');
            }
        }else{
            // print $this->db->last_query();
            return array('status' => false, 'msg' => $error['message']);
        }
    }

    public function get($id){
        $this->db->trans_begin();
        $post_data['date_added'] = date('Y-m-d H:i:s');
        $this->db->select('ocw.*, op.model, op.quantity,ocm.name as manufecturer, op.image, truncate(op.price, 3) AS special_price,  op.weight, owcd.unit, CASE
    WHEN ops.price > 0 THEN ops.price  ELSE (IFNULL(truncate(op.price, 3),0)) END AS price, opd.name');
        $this->db->from($this->table.' ocw');
        $this->db->join('oc_product op','op.product_id = ocw.product_id','left');
            $this->db->join('oc_product_special ops','ops.product_id = ocw.product_id','left');
        $this->db->join('oc_product_description opd','opd.product_id = ocw.product_id','left');
        $this->db->join('oc_weight_class_description owcd','op.weight_class_id = owcd.weight_class_id','left');
        $this->db->join('oc_manufacturer ocm','op.manufacturer_id = ocm.manufacturer_id','left');

        $this->db->where('customer_id', $id);
        $this->db->group_by('op.product_id');
        $query =$this->db->get();
        // print $this->db->last_query();
        $error = $this->db->error();
        if($error['code'] == 0){
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return array('status' => false, 'msg' => 'Error while getting wishlist', 'data' => '');
            }
            else{
                $this->db->trans_commit();
                return array('status' => true, 'data' => $query->result_array(), 'msg' =>'');
            }
        }else{
           
            return array('status' => false, 'msg' => $error['message'], 'data' => '');
        }
    }

    public function deleteData($post_data){
        $this->db->trans_begin();
        $this ->db->where($post_data);
        $this ->db->delete($this->table);
        $error = $this->db->error();
        if($error['code'] == 0){
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return array('status' => false, 'msg' => 'Error wihle remove product from wishlist');
            }
            else{
                $this->db->trans_commit();
                return array('status' => true, 'msg' => 'Product Successfully remove from wishlist');
            }
        }else{
            // print $this->db->last_query();
            return array('status' => false, 'msg' => $error['message']);
        }
    }
    
}