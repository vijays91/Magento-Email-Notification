<?php

class Vijaystore_Emailnotify_Block_Adminhtml_Renderer_Productsku extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$productid = $row->getData('product_id');
		$model = Mage::getModel('catalog/product');
		$_product = $model->load($productid);
		
		return $_product->getSku();
	}
}