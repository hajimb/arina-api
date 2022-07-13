<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Design_model extends MY_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        $this->table = 'design_master';
        $this->_condition = array();
    }    

    public function getDesign(){
        $this->db->select("*");
        $this->db->from($this->table);
        // $this->db->where('id', 1);
        $this->db->order_by("title", "ASC");
        $sql = $this->db->get();
        $result= $sql->result_array();
       
        $images = $this->getRelativeImages($result);
        return $images;
    }
    
    

    private function getRelativeImages($result){
        $data = array();
        foreach ($result as $key => $row){
            // echo 'key '.$row['id']."\n";
            $design_id = $row['id'];
            $this->db->select("id,image,is_default")->from('design_images')->where('design_id', $design_id);
            $sql = $this->db->get();
            $img = $sql->result_array();
            $row['images'] = $img;
            $data[] = $row; 
            // $data[] = array('product' => $row, 'images' => $img);
            // $data[$row['id']]['images'] = $img;
        }
        return $data;
        
    }
}