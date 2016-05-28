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
//Eric: New Class
class VDesign_Bookme_Model_Catalog_Product_Option_Type_Selectionsiege
    extends Mage_Catalog_Model_Product_Option_Type_Default
{
    public function isCustomizedView()
    {
    	Mage::log('isCustomizedView selectionsiege');
        return true;
    }
 
    public function getCustomizedView($optionInfo)
    {
    	Mage::log('getCustomizedView Selectionsiege');
        $customizeBlock = new VDesign_Bookme_Block_Adminhtml_Options_Type_Customview_Selectionsiege();
        $customizeBlock->setInfo($optionInfo);
        return $customizeBlock->toHtml();
    }
    
    public function getOptionPrice($optionValue, $basePrice)
    {
    	Mage::log('getOptionPrice Selectionsiege:' . $optionValue . ' ' . $basePrice);
    	$totalPrice = 0;
    	$option = $this->getOption();
    	//return ($basePrice);
    	
    	foreach (explode(',', $optionValue) as $option) {
    		$totalPrice += $basePrice;
    		$siegeId =  ltrim($option, "s");
    		Mage::log('$siegeId:' . $siegeId . ' ' . $totalPrice);
    		$siege = Mage::getModel('reseauchx_reservationreseau/siege')->load($siegeId);
    		
    		if(substr($siege->getData('code'), -1) == 'c' || substr($siege->getData('code'), -1) == 'd'){
    			$totalPrice += 4.35;
    		}
    		
    	}
    	return ($totalPrice);
    	/*return (count(explode(",", $optionValue)) - 1) * $basePrice;
    	return $this->_getChargableOptionPrice(
    			$option->getPrice(),
    			$option->getPriceType() == 'percent',
    			$basePrice
    	);*/
    }
    
    public function validateUserValue($values)
    {
    	Mage::log('validateUserValue Selectionsiege');
    	
    	Mage::getSingleton('checkout/session')->setUseNotice(false);
    	 
    	$this->setIsValid(false);
    	 
    	$option = $this->getOption();
    	 
    	$values[$option->getId()]['val'] = ($values[$option->getId()]['val'] != '')? substr($values[$option->getId()]['val'], 0, strlen($values[$option->getId()]['val']) - 1) : '';
    	 
    	$dates = explode(",", $values[$option->getId()]['val']);
    	$values[$option->getId()] = $values[$option->getId()]['val'];
    	 
    	
    	 
    	if (!isset($values[$option->getId()]) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
    		Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s).'));
    	} elseif (isset($values[$option->getId()])) {
    		$this->setUserValue($values[$option->getId()]);
    		$this->setIsValid(true);
    	}
    	return $this;
    }
}