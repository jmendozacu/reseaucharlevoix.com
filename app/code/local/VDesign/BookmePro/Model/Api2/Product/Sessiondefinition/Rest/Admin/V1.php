<?php

class VDesign_BookmePro_Model_Api2_Product_Sessiondefinition_Rest_Admin_V1 extends VDesign_BookmePro_Model_Api2_Product
{


	protected function _retrieveCollection()
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$product_id = $this->getRequest()->getParam('product_id');
		$product = Mage::getModel('catalog/product')->load($product_id);
		
		return $product->getData('custom_session');
	}
}