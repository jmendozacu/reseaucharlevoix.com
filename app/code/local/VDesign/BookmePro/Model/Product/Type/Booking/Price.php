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
class VDesign_BookmePro_Model_Product_Type_Booking_Price
extends VDesign_Bookme_Model_Product_Type_Booking_Price
{
	/**
	 * Multidate Group Instance
	 *
	 * @var VDesign_Bookme_Model_Catalog_Product_Option_Type_Multidate
	 */
	
	protected $_mgroup;
	
	/*
	 * Option Value of Multidate
	 */
	protected $_optionValue;
	

//     public function getFinalPrice($qty = null, $product)
//     {
//         if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
//             return $product->getCalculatedFinalPrice();
//         }

//         $finalPrice = $this->getBasePrice($product, $qty);
//         $product->setFinalPrice($finalPrice);

//         Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));

//         $finalPrice = $product->getData('final_price');
//         $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
//         $finalPrice = max(0, $finalPrice);
//         $product->setFinalPrice($finalPrice);

//         return $finalPrice;
//     }
    

	
	protected function _applyOptionsPrice($product, $qty, $finalPrice)
	{
		
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		if ($optionIds = $product->getCustomOption('option_ids')) {
			$basePrice = $finalPrice;
			
			foreach (explode(',', $optionIds->getValue()) as $optionId) {
				if ($option = $product->getOptionById($optionId)) {
					$confItemOption = $product->getCustomOption('option_'.$option->getId());
					Mage::log('Option: ' . $product->getName() . ' - '. $option->getId());
					$group = $option->groupFactory($option->getType())
					->setOption($option)
					->setConfigurationItemOption($confItemOption);
					
					if($group instanceof VDesign_Bookme_Model_Catalog_Product_Option_Type_Multidate)
					{
						$group->setProduct($product);
						$this->_mgroup = $group;
						$this->_optionValue = unserialize($confItemOption->getValue());
						$value = unserialize($confItemOption->getValue());
						
						$_product = Mage::getModel('catalog/product')->load($product->getId());
						$from = 0;
						$to = 0;
						$count = 0;
						
						if($_product->getAttributeText('billable_period') == 'Adventure'){
							$data = explode('#', $this->_optionValue['values']);
							$session = Mage::getModel('bookmepro/session')->load($data[1]);
							$data = $session;
							$from = strtotime($session['date_from']);
							$to = strtotime($session['date_to']);
							$count = 1;
						}
						if($_product->getAttributeText('billable_period') == 'Day'){
							$data = explode(',', $this->_optionValue['values']);
							$from = $data[0] / 1000;
							$to = $data[count($data) - 2] / 1000;
							$count = count($data);
						}
						if($_product->getAttributeText('billable_period') == 'Session'){
							$data = explode('#', $this->_optionValue['values']);
							$session = Mage::getModel('bookmepro/session')->load($data[0]);
							$from = strtotime($session['date_from']);
							$to = strtotime($session['date_to']);
							$count = count($data);
						}
						
						
						$discount1 = $this->applyFirstMoment($_product, $from, $finalPrice);
						$discount2 = $this->applyLastMinute($_product, $to, $finalPrice);
						$discount3 = $this->applyPeriodQty($_product, $count - 1, $finalPrice);
						$discount4 = 0;
						if($_product->getAttributeText('billable_period') == 'Session'){
							$discount4 = $this->applyDayType($_product, $from, $finalPrice);
						}
						$finalPrice = $finalPrice - $discount1 - $discount2 - $discount3 - $discount4;
						
						if($_product->getData('price_profile'))
						{
							$sum_qty = 0;
							$profiles = $_product->getData('price_profile');
							foreach ($profiles as $profile){
								if($value['profiles']['p#'.$profile['profile_id']] > 0)
								{
									$qty = $value['profiles']['p#'.$profile['profile_id']];
									$sum_qty += $qty;
									$profile_price = 0;
									
									if($profile['move'] == "1"){
										if($profile['amount_type'] == "1")
											$profile_price = $finalPrice + ($finalPrice * ($profile['amount'] / 100));
										else
											$profile_price = ($finalPrice + $profile['amount']);
									}else{
										if($profile['amount_type'] == "1")
											$profile_price = $finalPrice - ($finalPrice * ($profile['amount'] / 100));
										else
											$profile_price = $finalPrice - $profile['amount'];
									}
									
									$value['profiles']['p#'.$profile['profile_id']] = $profile_price * $value['profiles']['p#'.$profile['profile_id']];
									
								}
									
							}
							$finalPrice = 0;
							
							$this->validateQtyPro($_product, $sum_qty, $data);
							foreach ($value['profiles'] as $price)
								$finalPrice += $price;
						}
						Mage::log('Final price : ' . $finalPrice);
					}
					else{
						//Eric dont add product base price, this is per option price.
						//$finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
						$finalPrice = $group->getOptionPrice($confItemOption->getValue(), $basePrice);
					}
				}
			}
			//$finalPrice = $this->_mgroup->getOptionPrice($this->_optionValue, $finalPrice);
		}
		Mage::log('Final price _mgroup: ' . $finalPrice);
		return $finalPrice;
	}
	
	public function validateQtyPro($product, $qty, $data)
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		if($qty == 0)
			Mage::throwException("Please set quantity of reservations that you want to buy.");
	
		$helper = Mage::helper('bookmepro/availible');
		if($product->getAttributeText('billable_period') == 'Adventure')
			$a_qty = $data->getMaxQuantity() - $data->getBookedQty();
		else
			if($product->getAttributeText('billable_period') == 'Session')
			{
				$a_qty = PHP_INT_MAX;
				foreach ($data as $d)
				{
					$session = Mage::getModel('bookmepro/session')->load($d);
					if(!$session->getId())
						continue;
					$_a_qty = $session->getMaxQuantity() - $session->getBookedQty();
					$a_qty = ($a_qty > $_a_qty)? $_a_qty : $a_qty;
				}
			}
			else{
				$a_qty = PHP_INT_MAX;
				
				foreach ($data as $d)
				{
					if(empty($d)){
						$_a_qty = $helper->getAvailibleQty($product, date('Y-m-d H:i:s', $d / 1000));
						$a_qty = ($a_qty > $_a_qty)? $_a_qty : $a_qty;
					}
				}
			}
			
		if($qty > $a_qty)
			Mage::throwException("This count of reservations is not available. Maximum is ".$a_qty.'.');
		return $this;
	}
	
	
	public function applyFirstMoment($product, $data, $price){
		$rules = $product->getData('price_rule');
		
		foreach ($rules as $rule){
			if($rule['type'] == 1){
				$diff = $this->getDifferention($rule);
				if(strtotime(date('Y-m-d')) + $diff <= $data){
					return $this->applyRule($rule, $price);
				}
			}
		}
		return 0;
	}
	
	public function applyLastMinute($product, $data, $price){
		
		$rules = $product->getData('price_rule');
		foreach ($rules as $rule){
			if($rule['type'] == 2){
				$diff = $this->getDifferention($rule);
				if(strtotime(date('Y-m-d')) + $diff >= $data){
					return $this->applyRule($rule, $price);
				}
			}
		}
		return 0;
	}
	
	public function applyPeriodQty($product, $data, $price){
		$rules = $product->getData('price_rule');
		foreach ($rules as $rule){
			if($rule['type'] == 3){
				
				if($rule['value'] <= $data){
					return $this->applyRule($rule, $price);
				}
			}
		}
		return 0;
	}
	
	public function applyRule($rule, $price)
	{
		if($rule['move'] == "1"){
			if($rule['amount_type'] == "1")
				return 0 - ($price * ($rule['amount'] / 100));
			else
				return (0 - $rule['amount']);
		}else{
			if($rule['amount_type'] == "1")
				return $price * ($rule['amount'] / 100);
			else
				return $rule['amount'];
		}
	}
	
	
	
	
	public static function getDifferention($rule){
		switch ($rule['value_type']){
			case '1' : return $rule['value'] * 24 * 60 * 60; //days
			case '2' : return $rule['value'] * 7 * 24 * 60 * 60; //weeks
			case '3' : return $rule['value'] * 30 * 24 * 60 * 60; //months
		}
	}
	
	public function applyDayType($product, $data, $price){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		
		$rules = $product->getData('price_rule');
		foreach ($rules as $rule){
			if($rule['type'] == "4"){
				$dw = date("N", $data);
				
				if($rule['value'] == $dw)
				{
					return $this->applyRule($rule, $price);
				}
			}
		}
		return 0;
	}

}