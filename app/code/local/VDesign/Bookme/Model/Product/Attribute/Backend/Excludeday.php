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

class VDesign_Bookme_Model_Product_Attribute_Backend_Excludeday
extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract{
	
	const TYPE_PERIOD = '1';
	const TYPE_DATE = '2';
	const TYPE_MONTH = '3';
	const TYPE_WEEK = '4';
	
	/**
	 * Retrieve resource instance
	 *
	 */
	protected function _getResource()
	{
		return Mage::getResourceSingleton('bookme/product_attribute_backend_excludeday');
	}
	
	
	/**
	 * Error message when duplicates
	 *
	 * @return string
	 */
	protected function _getDuplicateErrorMessage()
	{
		return Mage::helper('bookme')->__('Duplicate setting for exlcude days.');
	}
	
	public function validate($object){
	}

	public function afterSave($object)
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$exRows = $object->getData($this->getAttribute()->getName());
		
		foreach ($exRows as $row){
			$exObject = Mage::getModel('bookme/book_excludeday');
			
			if(isset($row['exday_id'])){
				$exObject = $exObject->load($row['exday_id']);
				if(!$exObject->getId()){
					unset($row['exday_id']);
					$exObject = Mage::getModel('bookme/book_excludeday');
				}else{
					if(isset($row['deleted']))
						if($row['deleted'] == 1){
							$exObject->delete();
							continue;
						}
				}
			}
			if(isset($row['deleted']))
				if($row['deleted'] == 1){
					continue;
				}
			
			$exObject->setProduct($object);
			$exObject->setPeriodType($row['period_type']);
			if($row['period_type'] == VDesign_Bookme_Model_Product_Attribute_Backend_Excludeday::TYPE_PERIOD){
				$exObject->setFromDate($this->formatDate($row['from_date']));
				$exObject->setToDate($this->formatDate($row['to_date']));
			}else{
				$exObject->setValue($row['value']);
			}
			if(isset($row['refresh_day']))
				$exObject->setRefreshDay(true);
			else
				$exObject->setRefreshDay(false);
			$exObject->save();
		}
    
		return $this;
	}
	
	public function afterLoad($object){
		
		$data = array();
		$collection = Mage::getModel('bookme/book_excludeday')->getCollection()->addFilter('entity_id', $object->getId());
		foreach ($collection as $item){
			$pom = array();
			foreach ($item->getData() as $key => $value){
				if($value)
					$pom[$key] = $value;
			}
			$data[] = $pom;
		}
		
		$object->setData($this->getAttribute()->getAttributeCode(), $data);
		$object->setOrigData($this->getAttribute()->getAttributeCode(), $data);
		
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
		return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	}

}