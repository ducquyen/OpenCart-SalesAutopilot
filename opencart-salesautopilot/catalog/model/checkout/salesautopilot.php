<?php
class ModelCheckoutSalesAutopilot extends Model {
	public function getOrderInfo($order_id) {
		$order_info = false;
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		if (!empty($order_query->row)) {
			$items = array();
			$tax = 0;
		
			$product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
		
			foreach ($product_query->rows as $product) {
				$category_query = $this->db->query("SELECT ptc.category_id, cd.name FROM `" . DB_PREFIX . "product_to_category` ptc LEFT JOIN `" . DB_PREFIX . "category_description` cd ON ptc.category_id = cd.category_id WHERE ptc.product_id = '" . (int)$product['product_id']  . "' LIMIT 1");
			
				if(!empty($category_query->row)) {
					$category_id = $category_query->row['category_id'];
					$category_name = $category_query->row['name'];
				} else {
					$category_id = '999';
					$category_name = 'No Category';
				}
			
				if ($product['price'] > 0) {
					$taxPercent = round($product['tax'] / $product['price']);
				} else {
					$taxPercent = 0;
				}
			
				$items[] = array(
					'prod_id'		=> $product['product_id'],
					'prod_name'		=> $product['name'],
					'category_id'	=> $category_id,
					'category_name'	=> $category_name,
					'qty'			=> $product['quantity'],
					'tax'			=> $taxPercent,
					'prod_price'	=> round($product['price'],2)
				);
			}
			
			$order_info = array(
				'order_id'		  	=> (int)$order_id,
				'email'		  		=> $order_query->row['email'],
				'mssys_lastname'  	=> $order_query->row['lastname'],
				'mssys_firstname'  	=> $order_query->row['firstname'],
				'mssys_phone'  		=> $order_query->row['telephone'],
				'mssys_fax'  		=> $order_query->row['fax'],
				'shipping_method'	=> $order_query->row['shipping_method'],
				'payment_method'	=> $order_query->row['payment_method'],
				'currency'			=> $order_query->row['currency_code'],
				'mssys_bill_company'	=> $order_query->row['payment_company'],
				'mssys_bill_country'	=> strtolower($order_query->row['payment_iso_code_2']),
				'mssys_bill_state'		=> $order_query->row['payment_zone'],
				'mssys_bill_zip'		=> $order_query->row['payment_postcode'],
				'mssys_bill_city'		=> $order_query->row['payment_city'],
				'mssys_bill_address'	=> $order_query->row['payment_address_1'].' '.$order_query->row['payment_address_2'],
				'mssys_postal_company'	=> $order_query->row['shipping_company'],
				'mssys_postal_country'	=> strtolower($order_query->row['shipping_iso_code_2']),
				'mssys_postal_state'		=> $order_query->row['shipping_zone'],
				'mssys_postal_zip'		=> $order_query->row['shipping_postcode'],
				'mssys_postal_city'		=> $order_query->row['shipping_city'],
				'mssys_postal_address'	=> $order_query->row['shipping_address_1'].' '.$order_query->row['shipping_address_2'],
				//'total'		 		=> round($order_query->row['total'],2),
				'netshippingcost'	=> round($this->session->data['sap_shipping'],2),
				'grossshippingcost'	=> round($this->session->data['sap_shipping'],2),
				'products'	  => $items
			);
		}
		return $order_info;
	}
}
?>