<?php

class Vijaystore_Emailnotify_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
	{
		$data = Mage::getModel('emailnotify/emailnotify')->getCollection()->getData();		
		echo "<pre>";
		print_r($data);	
		echo "</pre>";
	}

	public function emailnotifyAction()
	{
		if($this->getRequest()->getParams()) {
			$notify_data = $this->getRequest()->getParams();			
			$insertId = $this->getLayout()->createBlock('emailnotify/notify')->insert_notify($notify_data);			
			$_data = array(
				'insert_id' => $insertId,			
				'success' => 200,
			);
		}
		else {
			$_data = array(
				'fail' => 201,
			);
		}
		$this->getResponse()->setBody(json_encode( $_data));
	}
}