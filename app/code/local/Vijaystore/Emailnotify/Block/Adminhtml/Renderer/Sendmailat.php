<?php

class Vijaystore_Emailnotify_Block_Adminhtml_Renderer_Sendmailat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$updated_at = $row->getData('updated_at');
		$ret = ($updated_at == "0000-00-00 00:00:00") ? '---' : $updated_at;
		return $ret;
	}
}