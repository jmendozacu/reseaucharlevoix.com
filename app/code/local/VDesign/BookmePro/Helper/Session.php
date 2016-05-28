<?php


class VDesign_BookmePro_Helper_Session extends Mage_Core_Helper_Abstract{
	
	
	public function generateSessions($product, $interval = null){
		
		$helper = Mage::helper('bookme');
		
		if($product->getAttributeText('billable_period') == 'Day')
			return $this;
		
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$product = Mage::getModel('catalog/product')->load($product->getId());
		
		if($product->getData('bookable_from')){
			if(strtotime($product->getData('bookable_from')) > strtotime(date('Y-m-d')))
				$date = strtotime($product->getData('bookable_from'));
			else
				$date = strtotime(date('Y-m-d'));
		}else{
			$date = strtotime(date('Y-m-d'));
		}
		
		$year = 365 * 24 * 60 * 60;
		if($interval)
		{
			$time = Mage::getModel('bookmepro/session')->getCollection()
			->addFieldToFilter('product_id', array('eq' => $product->getId()))
			->setOrder('date_from', 'DESC')->getFirstItem();
			$today = strtotime(date('Y-m-d H:i:s')) + $year;
			
			if($today - strtotime($time['date_from']) >= $interval)
				$date = strtotime($time['date_from']) + 24 * 60 * 60;
			else
				return;
		}else{
			$this->cleanUnbookedSessions($product);
		}
		
		
		if($product->getData('bookable_to')){
			if(strtotime($product->getData('bookable_to')) < strtotime(date('Y-m-d')) + $year)
				$date_to = strtotime($product->getData('bookable_to'));
			else
				$date_to = strtotime(date('Y-m-d')) + $year;
		}else{
			$date_to = strtotime(date('Y-m-d')) + $year;
		}
		
		$session_objects = Mage::getResourceModel('bookmepro/session_collection')
		->addFieldToFilter('product_id', array('eq' => $product->getId()))
		->addFieldToFilter('date_from', array('gteq' => $helper->formatDate(date('Y-m-d', $date), Varien_Date::DATE_INTERNAL_FORMAT)));
		
		foreach ($session_objects as $session)
		{
			$session = Mage::getModel('bookmepro/session')->load($session['session_id']);
			$session->setBookType($product->getAttributeText('billable_period'));
			$session->save();
		}
		
		$sessions = $product->getData('custom_session');
		$every_day = null;
		$business_day = null;
		$weekend = null;
		$spec_day = array();
		$week_day = array();
		foreach ($sessions as $session)
		{
			if($session['day_start'] == 8)
				$business_day = $session;
			if($session['day_start'] == 10)
				$every_day = $session;
			if($session['day_start'] == 9)
				$weekend = $session;
			if($session['day_start'] < 8)
				$week_day[$session['day_start']] = $session;
			if($session['day_start'] == 11)
				$spec_day[$helper->formatDate(($session['specific_date_start']), Varien_Date::DATE_INTERNAL_FORMAT)] = $session;
		}
		
		
		for($today_time = $date; $today_time < $date_to; $today_time = $this->increaseByOneDay($today_time))
		{
			
			if(Mage::helper('bookmepro/availible')->isInExcluded($product, date('Y-m-d', $today_time)))
				continue;
			
			$dw = date("N", $today_time);
			
			
				if($every_day != null){ //10 is for every day session
					$session = $every_day;
					foreach ($session['sessions'] as $session_time){
			
						$start_timestamp = $today_time + ($session_time['start_hour'] * 60 * 60) + ($session_time['start_minute'] * 60);
						$end_timestamp = $today_time + ($session_time['end_hour'] * 60 * 60) + ($session_time['end_minute'] * 60);
						
						if($session_time['qty'] > 0) $qty = $session_time['qty'];
						else $qty = $product->getData('bookable_qty');
						
						$session_object = Mage::getModel('bookmepro/session');
						$session_object->setData('product_id', $product->getId());
						$session_object->setData('customsession_id', $session['session_id']);
						$session_object->setData('time_id', $session_time['time_id']);
						$session_object->setDateFrom($helper->formatDate(date('Y-m-d', $start_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
						$session_object->setDateTo($helper->formatDate(date('Y-m-d', $end_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
						$session_object->setTimeFrom(date('H:i:s', $start_timestamp));
						$session_object->setTimeTo(date('H:i:s', $end_timestamp));
						$session_object->setMaxQuantity($qty);
						$session_object->setBookedQty(0);
						$session_object->setBookType($product->getAttributeText('billable_period'));
						
						if(!$this->isReady($session_objects, $session_object))
							$session_object->save();
					}
				}
			
			
				if($dw > 0 && $dw < 6){ //business day
					
						if($business_day != null){
							$session = $business_day;
							foreach ($session['sessions'] as $session_time){
								$start_timestamp = $today_time + ($session_time['start_hour'] * 60 * 60) + ($session_time['start_minute'] * 60);
								$end_timestamp = $today_time + ($session_time['end_hour'] * 60 * 60) + ($session_time['end_minute'] * 60);
								
								if($session_time['qty'] > 0) $qty = $session_time['qty'];
								else $qty = $product->getData('bookable_qty');
								
								$session_object = Mage::getModel('bookmepro/session');
								$session_object->setData('product_id', $product->getId());
								$session_object->setData('customsession_id', $session['session_id']);
								$session_object->setData('time_id', $session_time['time_id']);
								$session_object->setDateFrom($helper->formatDate(date('Y-m-d', $start_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
								$session_object->setDateTo($helper->formatDate(date('Y-m-d', $end_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
								$session_object->setTimeFrom(date('H:i:s', $start_timestamp));
								$session_object->setTimeTo(date('H:i:s', $end_timestamp));
								$session_object->setMaxQuantity($qty);
								$session_object->setBookedQty(0);
								$session_object->setBookType($product->getAttributeText('billable_period'));
								
								if(!$this->isReady($session_objects, $session_object))
									$session_object->save();
							}
						}
					
				}else{ //weekend
					
						if($weekend != null){
							$session = $weekend;
							foreach ($session['sessions'] as $session_time){
								
								$start_timestamp = $today_time + ($session_time['start_hour'] * 60 * 60) + ($session_time['start_minute'] * 60);
								$end_timestamp = $today_time + ($session_time['end_hour'] * 60 * 60) + ($session_time['end_minute'] * 60);
								
								if($session_time['qty'] > 0) $qty = $session_time['qty'];
								else $qty = $product->getData('bookable_qty');
								
								$session_object = Mage::getModel('bookmepro/session');
								$session_object->setData('product_id', $product->getId());
								$session_object->setData('customsession_id', $session['session_id']);
								$session_object->setData('time_id', $session_time['time_id']);
								$session_object->setDateFrom($helper->formatDate(date('Y-m-d', $start_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
								$session_object->setDateTo($helper->formatDate(date('Y-m-d', $end_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
								$session_object->setTimeFrom(date('H:i:s', $start_timestamp));
								$session_object->setTimeTo(date('H:i:s', $end_timestamp));
								$session_object->setMaxQuantity($qty);
								$session_object->setBookedQty(0);
								$session_object->setBookType($product->getAttributeText('billable_period'));
								
								if(!$this->isReady($session_objects, $session_object)){
									$session_object->save();
								}
								
							}
						}
					
				}
			
				/*
				 * next search for specific day in week
				*/
				
				if(isset($week_day[$dw])){
					$session = $week_day[$dw];
					foreach ($session['sessions'] as $session_time){
						$d_diff = ($session['day_end'] >= $session['day_start'])? $session['day_end'] - $session['day_start'] : 7 - $session['day_start'] + $session['day_end'];
						$start_timestamp = $today_time + ($session_time['start_hour'] * 60 * 60) + ($session_time['start_minute'] * 60);
						$end_timestamp = $today_time + ($d_diff * 24 * 60 * 60) + ($session_time['end_hour'] * 60 * 60) + ($session_time['end_minute'] * 60);
						
						if($session_time['qty'] > 0) $qty = $session_time['qty'];
						else $qty = $product->getData('bookable_qty');
						
						$session_object = Mage::getModel('bookmepro/session');
						$session_object->setData('product_id', $product->getId());
						$session_object->setData('customsession_id', $session['session_id']);
						$session_object->setData('time_id', $session_time['time_id']);
						$session_object->setDateFrom($helper->formatDate(date('Y-m-d', $start_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
						$session_object->setDateTo($helper->formatDate(date('Y-m-d', $end_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
						$session_object->setTimeFrom(date('H:i:s', $start_timestamp));
						$session_object->setTimeTo(date('H:i:s', $end_timestamp));
						$session_object->setMaxQuantity($qty);
						$session_object->setBookedQty(0);
						$session_object->setBookType($product->getAttributeText('billable_period'));
						
						if(!$this->isReady($session_objects, $session_object))
							$session_object->save();
					}
				}
				
			
				/*
				 * at last check if there is secific day setted
				*/
				$day = $helper->formatDate(date('Y-m-d', $today_time), Varien_Date::DATE_INTERNAL_FORMAT);
				if(isset($spec_day[$day])){
					
						$session = $spec_day[$day];
						foreach ($session['sessions'] as $session_time){
		
							$start_timestamp = $today_time + ($session_time['start_hour'] * 60 * 60) + ($session_time['start_minute'] * 60);
							$end_timestamp = strtotime($session['specific_date_end']) +  ($session_time['end_hour'] * 60 * 60) + ($session_time['end_minute'] * 60);
							
							if($session_time['qty'] > 0) $qty = $session_time['qty'];
							else $qty = $product->getData('bookable_qty');
							
							$session_object = Mage::getModel('bookmepro/session');
							$session_object->setData('product_id', $product->getId());
							$session_object->setData('customsession_id', $session['session_id']);
							$session_object->setData('time_id', $session_time['time_id']);
							$session_object->setDateFrom($helper->formatDate(date('Y-m-d', $start_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
							$session_object->setDateTo($helper->formatDate(date('Y-m-d', $end_timestamp), Varien_Date::DATE_INTERNAL_FORMAT));
							$session_object->setTimeFrom(date('H:i:s', $start_timestamp));
							$session_object->setTimeTo(date('H:i:s', $end_timestamp));
							$session_object->setMaxQuantity($qty);
							$session_object->setBookedQty(0);
							$session_object->setBookType($product->getAttributeText('billable_period'));
								
							if(!$this->isReady($session_objects, $session_object))
								$session_object->save();
						}
				}
			
		}
		
	}
	
	public function isReady($objects, $session){
		foreach ($objects as $object)
		{
			if($object->getDateFrom() == $session->getDateFrom())
				if($object->getTimeFrom() == $session->getTimeFrom())
					if($object->getDateTo() == $session->getDateTo())
						if($object->getTimeTo() == $session->getTimeTo())
							if($object->getMaxQuantity() == $session->getMaxQuantity())
								if($object->getCustomsessionId() == $session->getCustomsessionId())
									if($object->getTimeId() == $session->getTimeId())
										if($object->getBookType() == $session->getBookType())
											return true;
		}
		return false;
	}
	
	
	public function increaseByOneDay($time)
	{
		$out = strtotime(date('Y-m-d', $time + (25*60*60)));
		if(date('Y-m-d', $out) == date('Y-m-d', $time))
			$out += 2 * 60 * 60;
		return 	strtotime(date('Y-m-d', $out));
	}
	
	
	public function cleanUnbookedSessions($product){
		$id = $product->getId();
		
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		
		$sessionTable = $resource->getTableName('bookmepro/session');
		$itemTable = $resource->getTableName('bookme/book_item');
		
		$sql = "Delete from $sessionTable where product_id = $id AND session_id NOT IN (select session_id from $itemTable where session_id is not null)";
		
		$writeConnection->query($sql);
	}
}