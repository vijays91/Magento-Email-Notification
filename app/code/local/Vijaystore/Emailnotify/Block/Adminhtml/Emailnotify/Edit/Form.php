<?php
class Vijaystore_Emailnotify_Block_Adminhtml_Emailnotify_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {
		$viewForm = new Varien_Data_Form(array(
            'id' => 'view_form',
        ));
		$viewForm->setUseContainer(true);
		
        $this->setForm($viewForm);
		
		$fieldset = $viewForm->addFieldset('emailnotifyview_form', array(
            'legend'      => Mage::helper('emailnotify')->__('View Records'),
            'class'        => 'fieldset-wide',
            )
        );
		
		$value =   Mage::registry('emailnotify_data')->getData();
		
        $fieldset->addField('Product Id', 'note', array(
            'label'     => Mage::helper('emailnotify')->__('Product Id'),
            'text'      => $value['product_id'],
        ));
		
		$product_name = Vijaystore_Emailnotify_Model_Observer::getUpdateProductName($value['product_id']);
        $fieldset->addField('Product Name', 'note', array(
            'label'     => Mage::helper('emailnotify')->__('Product Id'),
            'text'      => $product_name,
        ));
		
		$product_sku = Vijaystore_Emailnotify_Model_Observer::getProductSku($value['product_id']);
        $fieldset->addField('Product SKU', 'note', array(
            'label'     => Mage::helper('emailnotify')->__('Product SKU'),
            'text'      => $product_sku,
        ));
		
        $fieldset->addField('Email Id', 'note', array(
            'label'     => Mage::helper('emailnotify')->__('Email Id'),
            'text'      => $value['email'],
        ));
		
		$sendMail = ($value['send_email'] == 1) ? "Sent" : "Not Send";
		$fieldset->addField('send_email', 'note', array(
			'label'     => Mage::helper('emailnotify')->__('Send Email'),
			'text'      => $sendMail,
		));

		$fieldset->addField('Created At', 'note', array(
			'label'     => Mage::helper('emailnotify')->__('Created At'),
			'text'      => $value['created_at'],
		));
		
		$updated_at = ($value['updated_at'] == "0000-00-00 00:00:00") ? '---' : $value['updated_at'];
		$fieldset->addField('Updated At', 'note', array(
			'label'     => Mage::helper('emailnotify')->__('Updated At'),
			'text'      => $updated_at,
		));

		if ( Mage::getSingleton('adminhtml/session')->getemailnotifyData() )
		{
		  $viewForm -> setValues(Mage::getSingleton('adminhtml/session')->getemailnotifyData());
		  Mage::getSingleton('adminhtml/session')->getemailnotifyData(null);
		} elseif ( Mage::registry('emailnotify_data') ) {
		  $viewForm-> setValues(Mage::registry('emailnotify_data')->getData());
		}
		return parent::_prepareForm();
	}
	
}