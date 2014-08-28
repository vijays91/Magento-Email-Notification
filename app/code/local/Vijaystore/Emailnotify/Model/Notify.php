<?php

class Vijaystore_Emailnotify_Model_Notify
{
	const EMAIL_NOTIFY_SEND 		=	'1';
	const EMAIL_NOTIFY_NOT_SEND		=	'0';
	
	public function insert_notify_data($notify_data)
	{
		$notify_data['created_at'] = date('Y-m-d H:i:s');
		$email_notify = Mage::getModel('emailnotify/emailnotify')->setData($notify_data);
		
		return $email_notify->save()->getId();
	}
	
	public function retrieve_notify_data($product_id)
	{
		$email_notify = Mage::getModel('emailnotify/emailnotify')->getCollection();
		$email_notify->addFieldtoFilter('product_id', $product_id);
		$email_notify->addFieldtoFilter('send_email', self::EMAIL_NOTIFY_NOT_SEND);
		
		return $email_notify->getData();	
	}
	
	public function update_notify_data($id, $data)
	{
		$email_notify = Mage::getModel('emailnotify/emailnotify')->load($id)->addData($data);
		try {
			$email_notify->setId($id)->save();

		} catch (Exception $e){
			//echo $e->getMessage();
		}
		return $id;
	}
	
	public function resend_mail($emailnotify_id)
	{
		$email_notify = Mage::getModel('emailnotify/emailnotify')->load($emailnotify_id);
		$product_id = $email_notify->getProductId();
		$email = $email_notify->getEmail();		
		$product_name = Vijaystore_Emailnotify_Model_Observer::getUpdateProductName($product_id);
		$product_url = Vijaystore_Emailnotify_Model_Observer::getUpdateProductUrl($product_id);		
		$qtyStock_item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
		$qty_stock = $qtyStock_item->getQty();
		$stock_in = $qtyStock_item->getIsInStock();

		$send = self::EMAIL_NOTIFY_NOT_SEND;
		if($qty_stock > 0 && $stock_in == 1)
		{
			$send = self::EMAIL_NOTIFY_SEND;
			$update_data = array('send_email' => $send, 'updated_at' => date('Y-m-d H:i:s'));
			$email_notify = Mage::getModel('emailnotify/emailnotify');				
			$data = array(
				"product_name" => $product_name,
				"product_id" => $product_id,
				"product_url" => $product_url
			);
			$notify_alet = Mage::helper('emailnotify/data')->customer_email_notify($data, $email);
			$update_id = Mage::getModel('emailnotify/notify')->update_notify_data($emailnotify_id, $update_data);

		}
		return $send;
	}
}

?>