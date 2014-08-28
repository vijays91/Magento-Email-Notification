<?php

class Vijaystore_Emailnotify_Block_Adminhtml_Emailnotify_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'emailnotify';
        $this->_controller = 'adminhtml_emailnotify';
		$this->_removeButton('save');
		$this->_removeButton('reset');
    }
 
    public function getHeaderText()
    {
        if( Mage::registry('emailnotify_data') && Mage::registry('emailnotify_data')->getId() ) 
		{
            return Mage::helper('emailnotify/data')->__("Email Notify Record &nbsp;'%s'  ", $this->htmlEscape(Mage::registry('emailnotify_data')->getId()));
        }
    }
}