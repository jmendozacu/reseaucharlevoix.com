<?php

class VDesign_BookmePro_Model_Api2_Product_Session_Rest_Admin_V1 extends VDesign_BookmePro_Model_Api2_Product
{


	protected function _retrieveCollection()
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$product_id = $this->getRequest()->getParam('product_id');
		$sessions = Mage::getModel('bookmepro/session')->getCollection()
		->addFieldToFilter('product_id', $product_id);
		
		$data = array();
		foreach ($sessions as $session){
			$data[] = $session->getData();
		}
		return $data;
	}
}