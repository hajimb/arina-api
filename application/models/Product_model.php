<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Product_model extends CI_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        
    }    

//     public function getProducts($manufacturer_id){
// 		$this->db->select("DISTINCT(op.product_id) ,op.model, op.quantity, op.manufacturer_id, ocm.name as manufecturer, op.image, truncate(op.price, 3) price, op.weight,owcd.unit, opd.name, opd.description, opd.meta_title, opd.meta_description");
//         $this->db->from("oc_product op");
//         $this->db->join('oc_product_description opd','op.product_id = opd.product_id','left');
//         $this->db->join('oc_weight_class_description owcd','op.weight_class_id = owcd.weight_class_id','left');
//         $this->db->join('oc_manufacturer ocm','op.manufacturer_id = ocm.manufacturer_id','left');
// 		if($manufacturer_id){
// 			$this->db->where("op.manufacturer_id", $manufacturer_id);
// 		}
//         $sql = $this->db->get();
//         // print $this->db->last_query();
//         return $sql->result_array();
// 	}
	
	
	/*
	
	SELECT DISTINCT(op.product_id), `op`.`model`, `op`.`quantity`, `op`.`manufacturer_id`, `ocm`.`name` as `manufecturer`, `op`.`image`, 
truncate(op.price,3) AS special_price, 
CASE
    WHEN ops.price > 0 THEN ops.price 
    
    ELSE (IFNULL(truncate(ops.price, 3),0))
END  AS price
, `op`.`weight`, `owcd`.`unit`, `opd`.`name`, `opd`.`description`, `opd`.`meta_title`, `opd`.`meta_description`
FROM `oc_product` `op`
LEFT JOIN `oc_product_special` `ops` ON `op`.`product_id` = `ops`.`product_id`
LEFT JOIN `oc_product_description` `opd` ON `op`.`product_id` = `opd`.`product_id`
LEFT JOIN `oc_weight_class_description` `owcd` ON `op`.`weight_class_id` = `owcd`.`weight_class_id`
LEFT JOIN `oc_manufacturer` `ocm` ON `op`.`manufacturer_id` = `ocm`.`manufacturer_id`
*/

    public function getProducts($manufacturer_id,$language=1){
		$this->db->select("DISTINCT(op.product_id) ,op.model, op.quantity, op.manufacturer_id, ocm.name as manufecturer, op.image, truncate(op.price, 3) AS special_price,  op.weight,owcd.unit,CASE
    WHEN ops.price > 0 THEN ops.price   ELSE (IFNULL(truncate(op.price, 3),0)) END AS price, opd.name, opd.description, opd.meta_title, opd.meta_description");
        $this->db->from("oc_product op");
        $this->db->join('oc_product_special ops','op.product_id = ops.product_id','left');
        $this->db->join('oc_product_description opd','op.product_id = opd.product_id','left');
        $this->db->join('oc_weight_class_description owcd','op.weight_class_id = owcd.weight_class_id','left');
        $this->db->join('oc_manufacturer ocm','op.manufacturer_id = ocm.manufacturer_id','left');
		if($manufacturer_id){
			$this->db->where("op.manufacturer_id", $manufacturer_id);
		}
		$this->db->where("opd.language_id", $language);
// 		$this->db->group_by('op.product_id');
        $sql = $this->db->get();
        // print $this->db->last_query();
        return $sql->result_array();
	}
	
    public function getProductsByCategory($category_id,$language=1){
		$this->db->select("DISTINCT(op.product_id) ,op.model, op.quantity, op.manufacturer_id, ocm.name as manufecturer, op.image, truncate(op.price, 3) AS special_price,  op.weight,owcd.unit,CASE
    WHEN ops.price > 0 THEN ops.price   ELSE (IFNULL(truncate(op.price, 3),0)) END AS price, opd.name, opd.meta_title, opd.meta_description");
        $this->db->from("oc_product op");
        $this->db->join('oc_product_special ops','op.product_id = ops.product_id','left');
        $this->db->join('oc_product_description opd','op.product_id = opd.product_id','left');
        $this->db->join('oc_weight_class_description owcd','op.weight_class_id = owcd.weight_class_id','left');
        $this->db->join('oc_manufacturer ocm','op.manufacturer_id = ocm.manufacturer_id','left');
        $this->db->join('oc_product_to_category ptc','op.product_id = ptc.product_id','left');
        $this->db->join('oc_category_description ocd','ocd.category_id = ptc.category_id','left');
		if($category_id){
			$this->db->where("ptc.category_id", $category_id);
		}
		$this->db->where("opd.language_id", $language);
// 		$this->db->group_by('op.product_id');
        $sql = $this->db->get();
        // print $this->db->last_query();
        return $sql->result_array();
	}
	
    public function getNewProduct($language=1){
        $this->db->select("*");
        $this->db->from("oc_module");
        $this->db->where("module_id",539);
        $this->db->where("code",'ocproduct');
        $query = $this->db->get();
        $result = $query->row();
        $setting = json_decode($result->setting, true);
        $count = count($setting['product']);
        $title = $setting['title_lang']['en-gb']['title'];
        if($count > 0){
            	$this->db->select("DISTINCT(op.product_id) ,op.model, op.quantity, op.manufacturer_id, ocm.name as manufecturer, op.image, truncate(op.price, 3) AS special_price,  op.weight,owcd.unit,CASE
    WHEN ops.price > 0 THEN ops.price   ELSE (IFNULL(truncate(op.price, 3),0)) END AS price, opd.name, opd.description, opd.meta_title, opd.meta_description");
        $this->db->from("oc_product op");
        $this->db->join('oc_product_special ops','op.product_id = ops.product_id','left');
        $this->db->join('oc_product_description opd','op.product_id = opd.product_id','left');
        $this->db->join('oc_weight_class_description owcd','op.weight_class_id = owcd.weight_class_id','left');
        $this->db->join('oc_manufacturer ocm','op.manufacturer_id = ocm.manufacturer_id','left');
        $this->db->where("opd.language_id", $language);
            $this->db->where_in("op.product_id", $setting['product']);
            $sql = $this->db->get();
            $data = $sql->result_array();
        }else{
            $data = '';
        }
		$return['count'] = $count;
		$return['data'] = $data;
		$return['title'] = $title;
        return $return;
    }
    
    public function getOfferProduct($language)
    {
		$this->db->select("DISTINCT(op.product_id) ,op.model, op.quantity, op.manufacturer_id, ocm.name as manufecturer, op.image, truncate(op.price, 3) AS special_price,  op.weight,owcd.unit,CASE
    WHEN ops.price > 0 THEN ops.price   ELSE (IFNULL(truncate(op.price, 3),0)) END AS price, opd.name, opd.meta_title, opd.meta_description");
        $this->db->from("oc_product op");
        $this->db->join('oc_product_special ops','op.product_id = ops.product_id','left');
        $this->db->join('oc_product_description opd','op.product_id = opd.product_id','left');
        $this->db->join('oc_weight_class_description owcd','op.weight_class_id = owcd.weight_class_id','left');
        $this->db->join('oc_manufacturer ocm','op.manufacturer_id = ocm.manufacturer_id','left');
        $this->db->join('oc_product_to_category ptc','op.product_id = ptc.product_id','left');
        $this->db->join('oc_category_description ocd','ocd.category_id = ptc.category_id','left');
		$this->db->where("opd.language_id", $language);
		$this->db->where("ops.product_id <> ", "");
		$this->db->where("truncate(op.price, 3) <> " ,"truncate(ops.price, 3)");
// 		$this->db->group_by('op.product_id');
        $sql = $this->db->get();
        // print $this->db->last_query();
        return $sql->result_array();
        
    }
    
    public function getProductByID($product_id,$language_id) {
        // echo "SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)CUSTOMER_GROUP_ID . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)CUSTOMER_GROUP_ID . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)CUSTOMER_GROUP_ID . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . $language_id . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . $language_id . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . $language_id . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . $language_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)STORE_ID . "'";
        $query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)CUSTOMER_GROUP_ID . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)CUSTOMER_GROUP_ID . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)CUSTOMER_GROUP_ID . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . $language_id . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . $language_id . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . $language_id . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . $language_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)STORE_ID . "'");
       
        // echo $query->num_rows();exit;
        if ($query->num_rows()) {
             $data = $query->row_array();
            return array(
                'product_id'       => $data['product_id'],
                'name'             => $data['name'],
                // 'description'      => html_entity_decode($data['description'], ENT_QUOTES, 'UTF-8'),
                'description'      => $data['description'],
                'meta_title'       => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                'meta_keyword'     => $data['meta_keyword'],
                'tag'              => $data['tag'],
                'model'            => $data['model'],
                'sku'              => $data['sku'],
                'upc'              => $data['upc'],
                'ean'              => $data['ean'],
                'jan'              => $data['jan'],
                'isbn'             => $data['isbn'],
                'mpn'              => $data['mpn'],
                'location'         => $data['location'],
                'quantity'         => $data['quantity'],
                'stock_status'     => $data['stock_status'],
                'image'            => $data['image'],
                'manufacturer_id'  => $data['manufacturer_id'],
                'manufacturer'     => $data['manufacturer'],
                'price'            => ($data['discount'] ? $data['discount'] : $data['price']),
                'special'          => $data['special'],
                'reward'           => $data['reward'],
                'points'           => $data['points'],
                'tax_class_id'     => $data['tax_class_id'],
                'date_available'   => $data['date_available'],
                'weight'           => $data['weight'],
                'weight_class_id'  => $data['weight_class_id'],
                'length'           => $data['length'],
                'width'            => $data['width'],
                'height'           => $data['height'],
                'length_class_id'  => $data['length_class_id'],
                'subtract'         => $data['subtract'],
                'rating'           => round($data['rating']),
                'reviews'          => $data['reviews'] ? $data['reviews'] : 0,
                'minimum'          => $data['minimum'],
                'sort_order'       => $data['sort_order'],
                'status'           => $data['status'],
                'date_added'       => $data['date_added'],
                'date_modified'    => $data['date_modified'],
                'viewed'           => $data['viewed']
            );
        } else {
            return false;
        }
    }

    public function getProductOptions($product_id,$language_id) {
        $product_option_data = array();
       // echo "SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . $language_id . "' ORDER BY o.sort_order";
        $product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . $language_id . "' ORDER BY o.sort_order");
        foreach ($product_option_query->result_array() as $product_option) {
            $product_option_value_data = array();
            $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . $language_id . "' ORDER BY ov.sort_order");
            foreach ($product_option_value_query->result_array() as $product_option_value) {
                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'option_value_id'         => $product_option_value['option_value_id'],
                    'name'                    => $product_option_value['name'],
                    'image'                   => $product_option_value['image'],
                    'quantity'                => $product_option_value['quantity'],
                    'subtract'                => $product_option_value['subtract'],
                    'price'                   => $product_option_value['price'],
                    'price_prefix'            => $product_option_value['price_prefix'],
                    'weight'                  => $product_option_value['weight'],
                    'weight_prefix'           => $product_option_value['weight_prefix']
                );
            }
    
            $product_option_data[] = array(
                'product_option_id'    => $product_option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id'            => $product_option['option_id'],
                'name'                 => $product_option['name'],
                'type'                 => $product_option['type'],
                'value'                => $product_option['value'],
                'required'             => $product_option['required']
            );
        }
    
        return $product_option_data;
    }

}