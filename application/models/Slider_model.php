<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Slider_model extends CI_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        
    }    

    public function getBrand(){
        $sql        = "SELECT manufacturer_id ,name, image FROM oc_manufacturer";
        $runQuery   = $this->db->query($sql);
        return $runQuery->result_array();
    }
}