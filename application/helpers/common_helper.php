<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('getIPAddress')) 
{
    function getIPAddress() {  
        //whether ip is from the share internet  
         if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
                    $ip = $_SERVER['HTTP_CLIENT_IP'];  
            }  
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
         }  
    //whether ip is from the remote address  
        else{  
                 $ip = $_SERVER['REMOTE_ADDR'];  
         }  
         return $ip;  
    }  
}  


if(!function_exists('token')) 
{
    function token($length = 32) {
        // Create random token
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        
        $max = strlen($string) - 1;
        
        $token = '';
        
        for ($i = 0; $i < $length; $i++) {
            $token .= $string[mt_rand(0, $max)];
        }   
        
        return $token;
    }

} 

if(!function_exists('currDate')) 
{
    function currDate() {
        return date('Y-m-d H:i:s');
    }

}  


if (!function_exists('getcountry')){
    function getcountry($country_id){
        $ci=& get_instance();
        $ci->load->database();
        $query = $ci->db->query("SELECT name FROM oc_country WHERE country_id=".$country_id);
        $rows  = $query->row();
        return $rows->name;
    }
}

if (!function_exists('getzone')){
    function getzone($zone_id){
        $ci=& get_instance();
        $ci->load->database();
        $query = $ci->db->query("SELECT name FROM oc_zone WHERE zone_id=".$zone_id);
        $rows  = $query->row();
        return $rows->name;
    }
}


if (!function_exists('getProduct')){
    function getProduct($product_id){
        $ci=& get_instance();
        $ci->load->database();
        $ci->db->select("op.model, opd.name, op.price");
        $ci->db->from("oc_product op");
        $ci->db->join('oc_product_description opd','op.product_id = opd.product_id','left');
        if($product_id){
			$ci->db->where("op.product_id", $product_id);
		}
        $sql = $ci->db->get();
        $rows = $sql->num_rows();
        if($rows>0){
            $ret['status'] = TRUE;
            $ret['data'] =  $sql->row();
            
        }else{
            $ret['status'] = False;
        }
        return $ret;
        // print $ci->db->last_query();
        
    }
}