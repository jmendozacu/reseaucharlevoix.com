<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to vdesign.support@outlook.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @copyright  Copyright (c) 2014 VDesign
 * @license    End User License Agreement (EULA)
 */
class VDesign_BookmePro_Model_Product_Attribute_Backend_Customsession
extends VDesign_Bookme_Model_Product_Attribute_Backend_Customsession{
	

	public function afterSave($object)
	{
		$customSessions = $object->getData('custom_session');
		
		
		foreach ($customSessions as $session){
			
			$session_object = Mage::getModel('bookme/book_session');
			
			if(isset($session['session_id'])){
				$session_object = $session_object->load($session['session_id']);
				if(!$session_object->getId()){
					unset($session['session_id']);
					$session_object = Mage::getModel('bookme/book_session');
				}else{
					if($session['deleted'] == 1){
						$session_object->delete();
						continue;
					}
				}
			}
			
			if(isset($session['deleted']))
				if($session['deleted'] == 1){
					continue;
				}
			
			$session_object->setProduct($object);
			//$session_object->setSessionDay((isset($session['session_day']))? $session['session_day'] : $session['day']);
			if($object->getAttributeText('billable_period') == 'Session')
			{
				$session_object->setDayStart($session['day_start']);
				$session_object->setDayEnd($session_object->getDayStart());
				$date = (isset($session['specific_date_start']))? $session['specific_date_start'] : '';
				$date = $this->formatDate($date);
				$session_object->setSpecificDateStart($date);
				$session_object->setSpecificDateEnd($date);
			}else
			{
				$session_object->setDayStart($session['day_start']);
				$session_object->setDayEnd($session['day_end']);
				$date = (isset($session['specific_date_start']))? $session['specific_date_start'] : '';
				$date = $this->formatDate($date);
				$session_object->setSpecificDateStart($date);
				$date = (isset($session['specific_date_end']))? $session['specific_date_end'] : '';
				$date = $this->formatDate($date);
				$session_object->setSpecificDateEnd($date);
			}

			$session_object->save();
			//from classic saving, it comes as session_times
			if(isset($session['session_times'])){
				foreach ($session['session_times'] as $time){
					$time_object = Mage::getModel('bookme/book_session_time');
					if(isset($time['session_time_id'])){
						$time_object = $time_object->load($time['session_time_id']);
						
						if(!$time_object->getId()){
							unset($time['session_time_id']);
							$time_object = Mage::getModel('bookme/book_session_time');
						}else{
							if($time['deleted'] == 1){
								$time_object->delete();
								continue;
							}
						}
					}
					
					if($time['deleted'] == 1){
						continue;
					}
					
					$time_object->setSession($session_object);
					if($object->getAttributeText('billable_period') == 'Session')
					{
						$time_object->setStartHour($time['start_hour']);
						$time_object->setEndHour($time['start_hour']);
						$time_object->setStartMinute($time['start_minute']);
						$time_object->setEndMinute($time['start_minute']);
					}else
					{
						$time_object->setStartHour($time['start_hour']);
						$time_object->setEndHour($time['end_hour']);
						$time_object->setStartMinute($time['start_minute']);
						$time_object->setEndMinute($time['end_minute']);
					}
					
					$time_object->setQty($time['qty']);
					$time_object->save();
				}
			
			}
			
			//from duplicating operation, it comes as sessions
			if(isset($session['sessions'])){
				foreach ($session['sessions'] as $time){
					$time_object = Mage::getModel('bookme/book_session_time');
					if(isset($time['session_time_id'])){
						$time_object = $time_object->load($time['session_time_id']);
			
						if(!$time_object->getId()){
							unset($time['session_time_id']);
							$time_object = Mage::getModel('bookme/book_session_time');
						}else{
							if($time['deleted'] == 1){
								$time_object->delete();
								continue;
							}
						}
					}
						
					if(isset($session['deleted']))
						if($time['deleted'] == 1){
							continue;
						}
						
					$time_object->setStartHour($time['start_hour']);
					$time_object->setEndHour($time['start_hour']);
					$time_object->setStartMinute($time['start_minute']);
					$time_object->setEndMinute($time['start_minute']);
					$time_object->setSession($session_object);
					$time_object->save();
				}
					
			}
			
			$session_helper = Mage::helper('bookmepro/session');
			$session_helper->generateSessions($object);
		}
		return $this;
	}
	
	/**
	 * Prepare date for save in DB
	 *
	 * string format used from input fields (all date input fields need apply locale settings)
	 * int value can be declared in code (this meen whot we use valid date)
	 *
	 * @param   string | int $date
	 * @return  string
	 */
	public function formatDate($date)
	{
		if (empty($date)) {
			return null;
		}
		// unix timestamp given - simply instantiate date object
		if (preg_match('/^[0-9]+$/', $date)) {
			$date = new Zend_Date((int)$date);
		}
		// international format
		else if (preg_match('#^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$#', $date)) {
			$zendDate = new Zend_Date();
			$date = $zendDate->setIso($date);
		}
		// parse this date in current locale, do not apply GMT offset
		else {
			$date = Mage::app()->getLocale()->date($date,
					Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
					null, false
			);
		}
		return $date->toString(Varien_Date::DATE_INTERNAL_FORMAT);
	}

}