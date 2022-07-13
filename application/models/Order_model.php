<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Order_model extends MY_Model
{

    public $table;
    public $_primary_key;
    public $_condition;
    public function __construct() {
        parent::__construct();
        
    }    

    public function getOrder(int $customer_id){
        $this->db->select("o.order_id, o.name, o.date_added, o.total, o.currency_code, o.currency_value, os.name as status, (SELECT count(ocp.order_product_id ) FROM oc_order_product ocp where order_id=o.order_id) as totalProduct ");
        $this->db->from("oc_order o");
        $this->db->join('oc_order_status os','o.order_status_id = os.order_status_id','left');
        $this->db->where("o.customer_id", $customer_id);
        // $this->db->where("o.order_status_id >", 0);
        // $this->db->where("o.store_id", STORE_ID);
        // $this->db->where("os.language_id", LANGUAGE_ID);
        $this->db->order_by("o.order_id", "DESC");
        $sql = $this->db->get();
        // print $this->db->last_query();
        return $sql->result_array();
    }
    
    public function getOrderProducts(int $order_id){
        $this->db->select("name, model, quantity, price, total, tax");
        $this->db->from("oc_order_product");
        $this->db->where("order_id ", $order_id);
        $sql = $this->db->get();
        return $sql->result_array();
    }


    public function create(array $order, array $products, array $discount){
        $this->db->trans_begin();
        $this->db->insert("oc_order", $order);
        $this->db->last_query();
        $order_id = $this->db->insert_id();
        log_message('error', $order_id = $this->db->insert_id());
        $parray=array();
        $total = 0;
        foreach($products as $product){

            $pdatetil = getProduct($product['id']);
            // print_r($pdatetil);

            if($pdatetil['status'] === FALSE){
                return array('msg' => 'Invalid Product Id', 'status' => true, 'success' => true,'address_id' => $address_id);

            }
            // $total = $total +$pdatetil['data']->price * $product['qty'];
            $parray[] = array(
                "order_id" => $order_id ,
                "product_id"=>$product['id'],
                "name" => $pdatetil['data']->name,
                "model" => $pdatetil['data']->model,
                "quantity" => $product['qty'],
                "price" => $pdatetil['data']->price,
                "total" => $pdatetil['data']->price * $product['qty']
            );
        }
        $this->db->insert_batch("oc_order_product", $parray);

        $taara=array(
            array('order_id'=> $order_id, 'code'=>'sub_total', 'title'=>'Sub-Total', 'value'=>$order['total']),
            array('order_id'=> $order_id, 'code'=>'shipping', 'title'=>'Flat Shipping Rate', 'value'=>0.0000),
            array('order_id'=> $order_id, 'code'=>'total', 'title'=>'Total', 'value'=>$order['total']),
        );
        $this->db->insert_batch("oc_order_total", $taara);
        $discount['order_id'] = $order_id;
        $this->db->insert("oc_coupon_history", $discount);

        $error = $this->db->error();
        // print_r($error);
        if($error['code'] == 0){
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return array('status' => false, 'msg' => $error['message']);
            }
            else{
                $this->db->trans_commit();
                return array('status' => true, 'msg' => "Order Successfully Placed");
            }
        }
        // return $sql->result_array();
    }

    public function getOrderById(array $condition){
        $this->db->select("payment_firstname, payment_lastname, payment_address_1, payment_address_2, payment_city, payment_postcode, payment_country, payment_zone, payment_method, payment_code, shipping_firstname, shipping_lastname, shipping_company, shipping_address_1, shipping_address_2, shipping_city, shipping_postcode, shipping_country, shipping_zone, shipping_method, comment, total, currency_code, name, telephone, governate_id, governate, block, street, building, floor, flat, paci, address_note, shipping_name, shipping_governate_id, shipping_governate, shipping_block, shipping_street, shipping_building, shipping_floor, shipping_flat, shipping_paci, payment_name, payment_governate_id, payment_governate, payment_block, payment_street, payment_building, payment_floor, payment_flat, payment_paci");
        $this->db->from("oc_order");
        $this->db->where($condition);
        // $this->db->where("order_status_id >", 0);
        $sql = $this->db->get();
        return $sql->row_array();
    }

    public function getOrderHistory($order_id) {
		$this->db->select("date_added, os.name AS status, oh.comment, oh.notify");
        $this->db->from("oc_order_history oh");
        $this->db->join('oc_order_status os','oh.order_status_id = os.order_status_id','left');
        $this->db->where("oh.order_id", $order_id);
        $this->db->where("os.language_id", LANGUAGE_ID);
        $this->db->order_by("oh.date_added", "ASC");
        $sql = $this->db->get();
        return $sql->result_array();
	}
}