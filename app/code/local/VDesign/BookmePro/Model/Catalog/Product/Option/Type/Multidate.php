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
class VDesign_BookmePro_Model_Catalog_Product_Option_Type_Multidate
    extends VDesign_Bookme_Model_Catalog_Product_Option_Type_Multidate
{
    public function isCustomizedView()
    {
        return true;
    }
 
    public function getCustomizedView($optionInfo)
    {
        $customizeBlock = new VDesign_BookmePro_Block_Adminhtml_Options_Type_Customview_Multidate();
        $customizeBlock->setInfo($optionInfo);
        return $customizeBlock->toHtml();
    }
    
    public function getOptionPrice($optionValue, $basePrice)
    {
    	
    	if (strpos($optionValue['values'], '-') !== FALSE){
    		return $basePrice;
    	}
    	if (strpos($optionValue['values'], ',') !== FALSE){
    		return (count(explode(",", $optionValue['values'])) - 1) * $basePrice;
    	}
    	if (strpos($optionValue['values'], '#') !== FALSE){
    		return (count(explode("#", $optionValue['values'])) - 1) * $basePrice;
    	}
    }
    
    public function validateUserValue($values)
    {
    	$option = $this->getOption();
    	Mage::getSingleton('checkout/session')->setUseNotice(false);
    	$this->setIsValid(false);
    	
    	if (strpos($values[$option->getId()]['val']['value'], '-') !== FALSE){
    		$offset = $values[$option->getId()]['val']['offset'];
    		unset($values[$option->getId()]['val']['offset']);
    		
    		$profiles = array();
    		foreach ($values[$option->getId()]['val'] as $key => $value)
    		if($key != 'value')
    		{
    			$profiles[$key] = $value;
    		}
    		
    		$values[$option->getId()] = serialize(array('values' => $values[$option->getId()]['val']['value'], 'profiles' => $profiles, 'offset' => $offset));
    		$this->setUserValue($values[$option->getId()]);
    		$this->setIsValid(true);
    		return $this;
    	}
    	if (strpos($values[$option->getId()]['val']['value'], ',') !== FALSE){
    			$values[$option->getId()]['val']['value'] = ($values[$option->getId()]['val']['value'] != '')? substr($values[$option->getId()]['val']['value'], 0, strlen($values[$option->getId()]['val']['value']) - 1) : '';
    			 
    			$dates = explode(",", $values[$option->getId()]['val']['value']);
    			$offset = $values[$option->getId()]['val']['offset'];
    			unset($values[$option->getId()]['val']['offset']);
    			
    			$n_dates = ''; 
    			
    			foreach ($dates as $date){
    				date_default_timezone_set("$offset");
    				$client_date = date('Y-m-d H:i:s', $date / 1000);
    				
    				date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
    				$n_dates .= (strtotime($client_date) * 1000).',';
    			}
    			
    			
    			$profiles = array();
    			foreach ($values[$option->getId()]['val'] as $key => $value)
    			if($key != 'value')
    				$profiles[$key] = $value;
    			
    			$values[$option->getId()] = '';
    			$values[$option->getId()] = serialize(array('values' => $n_dates, 'profiles' => $profiles, 'offset' => $offset));
    			 
    			if (!isset($values[$option->getId()]) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
    				Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s).'));
    			} elseif (isset($values[$option->getId()])) {
    				$this->setUserValue($values[$option->getId()]);
    				$this->setIsValid(true);
    			}	
    			return $this;
    	}
    	if (strpos($values[$option->getId()]['val']['value'], '#') !== FALSE){
    		$values[$option->getId()]['val']['value'] = ($values[$option->getId()]['val']['value'] != '')? substr($values[$option->getId()]['val']['value'], 0, strlen($values[$option->getId()]['val']['value']) - 1) : '';
    		$offset = $values[$option->getId()]['val']['offset'];
    		unset($values[$option->getId()]['val']['offset']);
    		
    		$profiles = array();
    		foreach ($values[$option->getId()]['val'] as $key => $value)
    		if($key != 'value')
    			$profiles[$key] = $value;
    		
    		$values[$option->getId()] = serialize(array('values' => $values[$option->getId()]['val']['value'].'#', 'profiles' => $profiles, 'offset' => $offset));
    	
    		if (!isset($values[$option->getId()]) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
    			Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s).'));
    		} elseif (isset($values[$option->getId()])) {
    			$this->setUserValue($values[$option->getId()]);
    			$this->setIsValid(true);
    		}
    	}
    	return $this;
    }
}