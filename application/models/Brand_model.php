<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Brand_model extends CI_Model
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

    public function getCategory($language){
        $sql        = "SELECT ocd.category_id ,ocd.name,ocd.is_lenses, oc.image FROM oc_category_description ocd LEFT JOIN oc_category oc ON ocd.category_id = oc.category_id WHERE ocd.language_id = ".$language;
        $runQuery   = $this->db->query($sql);
        return $runQuery->result_array();
    }
}