<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Coupon_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }    
    // coupon_code, product_id, category_id, subtotal, userid

    public function getCoupon($postData) {

        $pdata          = array();
        $status         = true;
        $errMsg         = "";
        $code           = $postData['coupon_code'];
        $subtotal       = $postData['subtotal'];
        $Query          = "SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $code. "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'";
		$coupon_query   = $this->db->query($Query);
		if ($coupon_query->num_rows()) {
            $couponData     = $coupon_query->row();
            // echo $couponData->total;
			if ($couponData->total > $subtotal) {
                $ramount = $couponData->total-$subtotal;
                $errMsg = "Minimum Amount required for coupon ".$couponData->total;
				$status = false;
			}

			$coupon_total = $this->getTotalCouponHistoriesByCoupon($code);

			if ($couponData->uses_total > 0 && ($coupon_total >= $couponData->uses_total)) {
                $errMsg = "Maximum usage Reached";
				$status = false;
			}

			if ($couponData->logged && !$postData['userid']) {
			    $errMsg = "Need Yo Login";
				$status = false;
			}

			if ($postData['userid']) {
				$customer_total = $this->getTotalCouponHistoriesByCustomerId($code, $postData['userid']);
				
				if ($coupon_query->row['uses_customer'] > 0 && ($customer_total >= $coupon_query->row['uses_customer'])) {
					$status = false;
				}
			}

			// Products
			$coupon_product_data = $this->getCouponProduct($couponData->coupon_id);			

			// Categories
			$coupon_category_data  = $this->getCouponCategory($couponData->coupon_id);

			$product_data = array();

			if ($coupon_product_data || $coupon_category_data) {
				foreach ($postData['products'] as $product) {
					if (in_array($product['product_id'], $coupon_product_data)) {
						$product_data[] = $product['product_id'];
						continue;
					}

					foreach ($coupon_category_data as $category_id) {
						$coupon_category_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product['product_id'] . "' AND category_id = '" . (int)$category_id . "'");
						if ($coupon_category_query->row['total']) {
							$product_data[] = $product['product_id'];
							continue;
						}
					}
				}

				if (!$product_data) {
					$status = false;
                    $errMsg = "This coupon code is not valid for selected products";
				}
			}
		} else {
			$status = false;
            $errMsg = "Invalid Coupon Or Expired";
		}




		if ($status) {
			$discount = $couponData->discount;
			if($couponData->type == 'F'){
				$discount_amount = $postData['subtotal'] - $discount;
				$discount_amount = number_format((float)$discount_amount, 3, '.', '');
			}else if($couponData->type == 'P'){
				$discount_amount = ($postData['subtotal'] * $discount) / 100;
				$discount_amount = number_format((float)$discount_amount, 3, '.', '');
			}
			$pdata = array(
				'coupon_id'     => $couponData->coupon_id,
				'code'          => $couponData->code,
				'name'          => $couponData->name,
				'type'          => $couponData->type,
				'discount'      => $discount_amount,
				'shipping'      => $couponData->shipping,
				'total'         => $couponData->total,
				'product'       => $product_data,
				'date_start'    => $couponData->date_start,
				'date_end'      => $couponData->date_end,
				'uses_total'    => $couponData->uses_total,
				'uses_customer' => $couponData->uses_customer,
				'status'        => $couponData->status,
				'date_added'    => $couponData->date_added
			);
		}
        $returnData['status'] = $status;
        $returnData['msg']    = $errMsg;
        $returnData['data']   = $pdata;
        return $returnData;
	}

    private function getTotalCouponHistoriesByCoupon($coupon){
        $Query     = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch LEFT JOIN `" . DB_PREFIX . "coupon` c ON (ch.coupon_id = c.coupon_id) WHERE c.code = '" . $coupon . "'";
		$Runquery  = $this->db->query($Query);
        $row       = $Runquery->row();
        return $row->total;
    }
   
    private function getTotalCouponHistoriesByCustomerId($coupon, $customer_id) {
        $Query     = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch LEFT JOIN `" . DB_PREFIX . "coupon` c ON (ch.coupon_id = c.coupon_id) WHERE c.code = '" . $coupon . "'";
		$Runquery  = $this->db->query($Query);
        $row       = $Runquery->row();
        return $row->total;
	}

    private function getCouponProduct($coupon_id){
        $Query     = "SELECT * FROM `" . DB_PREFIX . "coupon_product` WHERE coupon_id = '" . (int)$coupon_id . "'";
        $Runquery  = $this->db->query($Query);
        $coupon_product_data = array();
        foreach ($Runquery->result_array() as $product) {
            $coupon_product_data[] = $product['product_id'];
        }
        return $coupon_product_data;
    }

    private function getCouponCategory($coupon_id){
        $Query     = "SELECT * FROM `" . DB_PREFIX . "coupon_category` cc LEFT JOIN `" . DB_PREFIX . "category_path` cp ON (cc.category_id = cp.path_id) WHERE cc.coupon_id = '" . (int)$coupon_id . "'";
        $Runquery  = $this->db->query($Query);
        $coupon_category_data = array();
        foreach ($Runquery->result_array() as $category_data) {
            $coupon_category_data[] = $category_data['category_id'];
        }
        return $coupon_category_data;
    }
}