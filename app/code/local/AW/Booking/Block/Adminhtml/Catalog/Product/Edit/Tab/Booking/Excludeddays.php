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

class AW_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Booking_Excludeddays extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface{


    protected $_allowedFormats = array(
        Mage_Core_Model_Locale::FORMAT_TYPE_FULL,
        Mage_Core_Model_Locale::FORMAT_TYPE_LONG,
        Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
        Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
    );

    public function __construct(){
        $this->setTemplate('booking/product/edit/tab/booking/excludeddays.phtml');
    }
    public function getProduct(){
        return Mage::registry('product');
    }
	public function render(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->toHtml();
	}
    
    
    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Rule'),
                    'onclick'   => 'excludedDaysControl.addItem()',
                    'class' => 'add'
                )));
                
        

                
        return parent::_prepareLayout();
    }
    

	/**
	 * Creates element HTML for dates input
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
    public function createDateBlock($name, $type){
		
		$type = $type != 'to' ? 'from' : $type;
		
		$element = $this->getLayout()->createBlock('core/html_date')
                ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
                ->setFormat(Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
                ;


        
        if($type == 'from'){
			$days_select = $this->getLayout()->createBlock('core/html_select')
					->setName($name.'[__index__][recurrent_day]')
					->setId('excluded_days_row___index___recurrent_day')
					->setOptions(
						array(
							1 => $this->__('Monday'),
							2 => $this->__('Tuesday'),
							3 => $this->__('Wednesday'),
							4 => $this->__('Thursday'),
							5 => $this->__('Friday'),
							6 => $this->__('Saturday'),
							7 => $this->__('Sunday')
						)
					);
			
			$element
				->setName($name.'[__index__][period_from]')
				->setId('excluded_days_row___index___period_from')
				->setClass('product-custom-option datetime-picker input-text require booking-date');
				
			return $days_select->toHtml().$element->toHtml();	
		
		}else{
			$element
				->setName($name.'[__index__][period_to]')
				->setId('excluded_days_row___index___period_to') 
				->setClass('product-custom-option datetime-picker input-text require booking-date');
		}        
                
        return $element->toHtml();
	}

    
    public function setElement(Varien_Data_Form_Element_Abstract $element){
        $this->_element = $element;
        return $this;
    }

    public function getElement(){
        return $this->_element;
    }    
    
    
    public function getWebsites()
    {
		
		//var_dump($this->getProduct()->getStoreId());die();
		
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name'      => $this->__('All Websites'),
            'currency'  => Mage::app()->getBaseCurrencyCode()
        );
        if (Mage::app()->isSingleStoreMode() || $this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            return $websites;
        }
        elseif ($storeId = $this->getProduct()->getStoreId()) {
            $website = Mage::app()->getStore($storeId)->getWebsite();
            $websites[$website->getId()] = array(
                'name'      => $website->getName(),
                'currency'  => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            );
        }
        else {
            $websites[0] = array(
                'name'      => $this->__('All Websites'),
                'currency'  => Mage::app()->getBaseCurrencyCode()
            );
            foreach (Mage::app()->getWebsites() as $website) {
                if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                    continue;
                }
                $websites[$website->getId()] = array(
                    'name'      => $website->getName(),
                    'currency'  => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                );
            }
        }
        $this->_websites = $websites;
        return $this->_websites;
    }
    
    public function getValues(){
		return Mage::getModel('booking/excludeddays')->getCollection()
					->addEntityIdFilter($this->getProduct()->getId())
					->addStoreIdFilter($this->getProduct()->getStoreId())
					->getItems();
	}


    /**
     * Format date using current locale options and time zone.
     *
     * @param   date|Zend_Date|null $date
     * @param   string              $format   See Mage_Core_Model_Locale::FORMAT_TYPE_* constants
     * @param   bool                $showTime Whether to include time
     * @return  string
     */
    public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        if (!in_array($format, $this->_allowedFormats, true)) {
            return $date;
        }
        if (!($date instanceof Zend_Date) && $date && !strtotime($date)) {
            return '';
        }
        if (is_null($date)) {
            $date = Mage::app()->getLocale()->date(Mage::getSingleton('core/date')->gmtTimestamp(), null, null, false);
        } else if (!$date instanceof Zend_Date) {
            $date = Mage::app()->getLocale()->date(strtotime($date), null, null, false);
        }

        if ($showTime) {
            $format = Mage::app()->getLocale()->getDateTimeFormat($format);
        } else {
            $format = Mage::app()->getLocale()->getDateFormat($format);
        }

        return $date->toString($format);
    }


}
