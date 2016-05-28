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
class VDesign_Bookme_Helper_Data extends Mage_Core_Helper_Abstract{
	
	public function formatDate($date, $format)
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
		return $date->toString($format);
	}
	
}