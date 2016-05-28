<?php

class DRIEWORKS_Tickets_Block_Checkout_Onepage_Tickets extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('tickets', array(
            'label'     => Mage::helper('checkout')->__('Tickets'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }
	
	// nagaan of virtueel product is
	public function isShow()
    {
		if ($this->getQuote()->getItemVirtualQty() > 0){
			Mage::Log('Meer als 0 is waar');
			return true;
		} else {
			Mage::Log('Meer als 0 is niet waar');
			return false;
		}
    }
}