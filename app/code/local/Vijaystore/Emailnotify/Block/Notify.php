<?php

class Vijaystore_Emailnotify_Block_Notify extends Mage_Core_Block_Template
{
    public function insert_notify($notify_data)
    {
		return Mage::getModel('emailnotify/notify')->insert_notify_data($notify_data);
    }
}