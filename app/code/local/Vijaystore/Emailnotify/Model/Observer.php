<?php

class Vijaystore_Emailnotify_Model_Observer
{
	public function sendNotify($observer) {
		$product = $observer->getProduct();
		$product_id = $product->getData('entity_id');
		$product_name = self::getUpdateProductName($product_id);
		$product_url = self::getUpdateProductUrl($product_id);
		$qtyStock_item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
		$qty_stock = $qtyStock_item->getQty();
		$stock_in = $qtyStock_item->getIsInStock();

		if($qty_stock > 0 && $stock_in == 1)
		{
			$notify_data = Mage::getModel('emailnotify/notify')->retrieve_notify_data($product_id);
			if(count($notify_data))
			{
				$send = Vijaystore_Emailnotify_Model_Notify::EMAIL_NOTIFY_SEND;
				$update_data = array('send_email' => $send, 'updated_at' => date('Y-m-d H:i:s'));
				$email_notify = Mage::getModel('emailnotify/emailnotify');				
				foreach($notify_data as $key => $value)
				{
					$data = array(
						"product_name" => $product_name,
						"product_id" => $product_id,
						"product_url" => $product_url
					);
					$notify_alet = Mage::helper('emailnotify/data')->customer_email_notify($data, $value['email']);
					$update_id = Mage::getModel('emailnotify/notify')->update_notify_data($value['id'], $update_data);
				}
			}
		}
    }
	
	public function getUpdateProductName($product_id) {
		$_product = Mage::getModel('catalog/product')->load($product_id);
		return $_product->getName();
	}
	
	public function getUpdateProductUrl($product_id)
	{
		$_product = Mage::getModel('catalog/product')->load($product_id); 
		return $_product->getProductUrl();
	}
	
	public function getProductSku($product_id)
	{
		$_product = Mage::getModel('catalog/product')->load($product_id);
		return $_product->getSku();
	}
}
?>