<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Booking
 * @version    1.3.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Booking_Block_Js extends Mage_Adminhtml_Block_Template
{
    /* Customer's block of JS helpers */

    public function getProduct()
    {
        // Returns Magento product
        //return Mage::getModel('booking/booking')->load($this->getRequest()->getParam('id'));
        return Mage::getModel('booking/booking')->load(Mage::registry('current_product')->getId());
    }

    public function getCalendarQtyAvailable($product)
    {
        $dates = array();
        $collection = Mage::getModel('booking/order')->getCollection();
        $collection->addProductIdFilter($product->getId());
        $collection->groupByOrderId();
        $collection->load();
        foreach ($collection as $item) {
            $From = new Zend_Date($item->getBindStart(), AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
            $To = new Zend_Date($item->getBindEnd(), AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
            while ($From->compare($To) <= 0) {
                $this->skipTimeCheck = true;
                $dates[date(AW_Booking_Helper_Yui::DAY_FORMAT, strtotime($From->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT)))] = Mage::getModel('booking/checker_bind')->isQtyAvailableForDate($product->getId(), $From, $item->getTotalItems(), false);
                $From = $From->addDayOfYear(1);
            }
            unset($From, $To);
        }

        return Zend_Json::encode($dates);
    }

}
