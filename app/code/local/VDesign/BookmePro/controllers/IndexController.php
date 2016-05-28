<?php


class VDesign_BookmePro_IndexController extends Mage_Core_Controller_Front_Action
{

	
	public function adventuresAction(){
		
		$helper = Mage::helper('bookmepro/availible');
		
		$date = Mage::app()->getRequest()->getParam('date');
		$id = Mage::app()->getRequest()->getParam('id');
		$pm = Mage::app()->getRequest()->getParam('pm');
		
		$_product = Mage::getModel('catalog/product')->load($id);
		if(!$_product->getId())
		{
			$isAjax = Mage::app()->getRequest()->isAjax();
			if ($isAjax) {
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('outputHtml' => '')));
			}
		}
		
		$date = explode("/", $date);
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$date = (mktime(0,0,0,$date[1] + 1,$date[2],$date[0]) * 1000);
		
		$output = '';
		
		for($i = 0; $i <= $pm; $i++){
			$output .= $helper->getAdventures($_product, $date + ($i * 24 * 60 * 60 * 1000), $i);
		}
		
		for($i = 1; $i <= $pm; $i++){
			
			$today = strtotime(date('Y-m-d'));
			if((($date / 1000) - ($i * 24 * 60 * 60)) < $today)
				continue;
			
			$output .= $helper->getAdventures($_product, $date - ($i * 24 * 60 * 60 * 1000), 0 - $i);
		}
		
		if($output == '')
			$output .= $helper->getNearestAdventures($_product, $date, 5);
		
		$isAjax = Mage::app()->getRequest()->isAjax();
		if ($isAjax) {
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('outputHtml' => $output)));
		}
	}
	
	
	public function sessionAction(){
		$product_id = $this->getRequest()->getParam('id');
		$date = $this->getRequest()->getParam('date');
	
		$date = explode("/", $date);
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$date = (mktime(0,0,0,$date[1] + 1,$date[2],$date[0]) * 1000);
		
		$helper = Mage::helper('bookmepro/availible');
	
		$isAjax = Mage::app()->getRequest()->isAjax();
		if ($isAjax) {
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('outputHtml' => $helper->getSessions($product_id, $date))));
		}
	}
	
	public function maxqtyAction(){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$helper = Mage::helper('bookme/availible');
		$maxqty = 0;
		$dates = $this->getRequest()->getParam('dates');
		$offset = $this->getRequest()->getParam('offset');
		$product_id = $this->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($product_id);
		
		if($product->getAttributeText('billable_period') == 'Day')
		{
			$dates = ($dates != '')? substr($dates, 0, strlen($dates) - 1) : '';
			
			$dates = explode(",", $dates);
			$new_dates = '';
				
			foreach ($dates as $date){
				date_default_timezone_set($offset);
				$client_date = date('Y-m-d H:i:s', $date / 1000);
				date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
				$new_dates = $new_dates.(strtotime($client_date) * 1000).',';
			}
			$dates = $new_dates;
			
			if($dates != ''){
				$dates = substr($dates, 0, strlen($dates) - 1);
					
			
				if(!$product->getId())
					$maxqty = 0;
				else
					$maxqty = $product->getData('bookable_qty');
			
				$date_array = explode(",", $dates);
			
				foreach ($date_array as $date){
					$qty = $helper->getAvailibleQty($product, date('Y-m-d H:i:s', $date / 1000));
					$maxqty = ($qty < $maxqty)? $qty : $maxqty;
				}
			}
			$isAjax = Mage::app()->getRequest()->isAjax();
			if ($isAjax) {
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('outputHtml' => $maxqty)));
			}
		}else{
			if($dates != ''){
				
				$id_string = substr($dates, 0, strlen($dates) - 1);
				$helper = Mage::helper('bookmepro/availible');
				
				if(!$product->getId())
					$maxqty = 0;
				
				$maxqty = PHP_INT_MAX;
				
				$ids = explode(",", $id_string);
				
				foreach ($ids as $id){
					$session = Mage::getModel('bookmepro/session')->load($id);
					if(!$session->getId())
						continue;
					$available_qty = $session->getMaxQuantity() - $session->getBookedQty();//$helper->getSessionBookedQty($session);
					$maxqty = ($available_qty < $maxqty)? $available_qty : $maxqty;
				}
			}
			$isAjax = Mage::app()->getRequest()->isAjax();
			if ($isAjax) {
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('outputHtml' => $maxqty)));
			}
		}
	
		
	}
}