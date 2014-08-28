<?php
class Vijaystore_Emailnotify_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ENF_ENABLE   = 'emailnotify_tab/emailnotify_setting/emailnotify_active';
	const XML_PATH_ENF_SUBJECT  = 'emailnotify_tab/emailnotify_setting/emailnotify_subject';
	
	public function conf($code, $store = null) {
        return Mage::getStoreConfig($code, $store);
    }
	
	public function email_notify_enable() {
		return $this->conf(self::XML_PATH_ENF_ENABLE, $store);
	}

	public function dynamic_subject() {
		return $this->conf(self::XML_PATH_ENF_SUBJECT, $store);
	}
	
	public function admin_notify_emailId() {
		$admin = Mage::getStoreConfig('emailnotify_tab/emailnotify_setting/emailnotify_administrator');     
		return Mage::getStoreConfig('trans_email/ident_'.$admin.'/email', $store);
	}
	
	public function admin_notify_name() {
		$admin = Mage::getStoreConfig('emailnotify_tab/emailnotify_setting/emailnotify_administrator');     
		return Mage::getStoreConfig('trans_email/ident_'.$admin.'/name', $store);
	}
	
	public function customer_email_notify($data, $customer_email_id) 
	{
		$email_nofity_template  = Mage::getModel('core/email_template')->loadDefault('emailnotify_tab_emailnotify_setting_template');
		$notify_template = $email_nofity_template->getProcessedTemplate($data);

		$adminSalesRepEmail = $this->admin_notify_emailId();
		$adminSalesRepName = $this->admin_notify_name();
		
		$subject = $this->dynamic_subject();
		
		// Send the Mail to Customer
		$mail = Mage::getModel('core/email')
			->setToName('customer')
			->setToEmail($customer_email_id)
			->setBody($notify_template)
			->setSubject($subject)
			->setFromEmail($adminSalesRepEmail)
			->setFromName($adminSalesRepName)
			->setType('html');

		try {
			$mail->send();
		}
		catch(Exception $error) {
			Mage::getSingleton('core/session')->addError($error->getMessage());
			//echo $error->getMessage();
		}
	}
}

?>