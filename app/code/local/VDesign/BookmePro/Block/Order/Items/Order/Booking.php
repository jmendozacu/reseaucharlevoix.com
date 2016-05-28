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
class VDesign_BookmePro_Block_Order_Items_Order_Booking extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        
        for ($i = 0; $i < count($result); $i++)
        {
            if($result[$i]['option_type'] == 'multidate_type'){
        		$value = $this->getBookValue($result[$i]['value']);
        		$result[$i]['value'] = $value;
        		$result[$i]['print_value'] = $value;
        		$result[$i]['option_value'] = $value;
        	}
        }
		
        return $result;
    }
    
    
	public function getBookValue($data){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
    	$config = Mage::getStoreConfig('bookmepro_sales/sales/order_print', Mage::app()->getStore()->getId());
    	$day_notice = Mage::getStoreConfig('bookmepro_sales/sales/day_notice', Mage::app()->getStore()->getId());
    	$session_notice = Mage::getStoreConfig('bookmepro_sales/sales/session_notice', Mage::app()->getStore()->getId());
    	$adv_notice = Mage::getStoreConfig('bookmepro_sales/sales/adv_notice', Mage::app()->getStore()->getId());
    	$helper = Mage::helper('bookmepro');
    	
    	$data = unserialize($data);
    	$values = explode(",", $data['values']);
    	$from = date("d-m-Y", $values[0]/1000);
    	$p_out = "";
    	$out = "";
    	
    	$profiles = $data['profiles'];
    	foreach ($profiles as $key => $value)
			if($value > 0)
			{
				$id = explode("#", $key);
				$p_out .= $helper->getProfileTranslation($id[1], Mage::app()->getStore()->getCode())." : ".$value."
						";
			}
    			
    	if (strpos($data['values'], '-') !== FALSE){ //adventure
    		$values = explode("#", $data['values']);
    		$session = Mage::getModel('bookmepro/session')->load($values[1]);
    		
    		$from = strtotime($session['date_from'].' '.date('H:i:s', strtotime($session['time_from'])));
    		$to = strtotime($session['date_to'].' '.date('H:i:s', strtotime($session['time_to'])));
    		
    		if($config == 1)
    			$out .= Mage::helper('bookmepro')->__('1 reserved event.')."
    					";
    		else
    			$out .= Mage::helper('core')->formatDate(date("d-m-Y H:i:s",$from), 'medium', true)." - ".Mage::helper('core')->formatDate(date("d-m-Y H:i:s",$to), 'medium', true)."
    					";
    	}
    	
    	
    	if (strpos($data['values'], ',') !== FALSE){ //date
    		$values = explode(",", $data['values']);
    		$from = date("d-m-Y", $values[0]/1000);
    		$to = date("d-m-Y", $values[count($values)-2]/1000);
    	
    		if($config == 1)
    			$out .= (count($values) - 1).Mage::helper('bookmepro')->__(' reserved days.')."
    					";
    		else
    			$out .= Mage::helper('core')->formatDate($from, 'medium', false)." - ".Mage::helper('core')->formatDate($to, 'medium', false)."
    					";
    	}
    	
    	
    	if (strpos($data['values'], '#') !== FALSE && strpos($data['values'], '-') == FALSE){ //session
    		$values = explode("#", $data['values']);
    	
    		if($config == 1)
    			$out .= (count($values) - 1).Mage::helper('bookmepro')->__(' reserved sessions.')."
    					";
    		else
    			foreach ($values as $id)
    			{
    				$session = Mage::getModel('bookmepro/session')->load($id);
    				if($session->getId())
    					$out .= Mage::helper('core')->formatDate($session['date_from']." ".date('H:i:s', strtotime($session['time_from'])), 'medium', true)."
    							";
    			}
    	}
    	

    	if (strpos($data['values'], '-') !== FALSE){
    		if($adv_notice)
    			$out .= $adv_notice."
    					";
    	}
    	if (strpos($data['values'], ',') !== FALSE){
    		if($day_notice)
    			$out .= $day_notice."
    					";
    	}
    	if (strpos($data['values'], '#') !== FALSE && strpos($data['values'], '-') == FALSE){
    		if($session_notice)
    			$out .= $session_notice."
    					";
    	}
    	
     	return $out.$p_out;

    }
}
