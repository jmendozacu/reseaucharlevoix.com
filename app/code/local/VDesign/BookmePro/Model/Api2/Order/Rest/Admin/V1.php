<?php

class VDesign_BookmePro_Model_Api2_Order_Rest_Admin_V1 extends Mage_Sales_Model_Api2_Order_Item_Rest
{


	protected function _retrieveCollection()
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$orderId = $this->getRequest()->getParam('order_id');
		$items = $this->_getCollectionForRetrieve();
		$output = array();
		foreach ($items as $item)
		{
			$product = $item->getProduct();
			if($product->getTypeId() == 'booking'){
					$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
					
					$poptions = $item->getProductOptions();
					$options = array();
					if(is_array($poptions) && isset($poptions['options']))
						$options = $poptions['options'];
					else{
						return true;
					}
					
					foreach ($options as $option){
						if($option['option_type'] == 'multidate_type'){
							
							$data = unserialize($option['value']);
							$reserved_times = array();
							
							if($product->getAttributeText('billable_period') == 'Day'){
								$reserved_times = explode(",", substr($data['values'], 0, strlen($data['values']) - 1));
							}
							if($product->getAttributeText('billable_period') == 'Session'){
								$ids = explode("#", $data['values']);
								foreach ($ids as $id)
								{
									$session = Mage::getModel('bookmepro/session')->load($id);
									if($session->getId())
									{
										$ctime = explode(":", $session->getTimeFrom());
										$date = explode("-", $session->getDateFrom());
										$reserved_times[] = mktime($ctime[0],$ctime[1],0,$date[1],$date[2],$date[0]);
									}
								}
							}
							if($product->getAttributeText('billable_period') == 'Adventure'){
								$reserved_times = explode("-", $data['values'])[0];
							}
							
							$qty = array();
							if(count($data['profiles']) > 0){
								foreach ($data['profiles'] as $key => $amount)
								{
									$p_id = explode("#", $key)[1];
									$profile = Mage::getModel('bookmepro/priceprofile')->load($p_id);
									$qty[$profile->getName()] = $amount;
								} 
							}
							else 
								$qty = $item->getQtyOrdered();
							
							$itemData = array(
									'product_id' => $product->getId(),
									'reserved_times' => $reserved_times,
									'offset' => $data['offset'],
									'qty' => $qty
							);
							
							$output[$item->getId()] = $itemData;
						}
					}
			}
		}
		return $output;
	}
	
	
	
	
	
	
	
	
	
}