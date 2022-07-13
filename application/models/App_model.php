<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class App_model extends MY_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        $this->table = 'oc_address';
        $this->_condition = array();
    }    

    
    public function checkversion($data)  {
        // $data = ['code' => $token];
        $this->db->select('*');
        $this->db->from('oc_app_version');
        $this->db->where($data);
        $query   = $this->db->get();
        $numrows = $query->num_rows(); 
        if ($numrows === 1) {
            return array('msg' => 'Device Version Match', 'status' => true);
        }else{
            return array('msg' => 'Device Version Not Match', 'status' => false);
        }            
    }
}