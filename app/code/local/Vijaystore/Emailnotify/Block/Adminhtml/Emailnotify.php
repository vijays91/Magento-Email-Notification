<?php

class Vijaystore_Emailnotify_Block_Adminhtml_Emailnotify extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_emailnotify';
        $this->_blockGroup = 'emailnotify';
        $this->_headerText = Mage::helper('emailnotify')->__('Email Notify Reports');
        parent::__construct();
		$this->_removeButton('add');
    }
}