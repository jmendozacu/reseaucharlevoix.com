<?php
class SM_VPayment_Model_Master extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'vpayment_mastercardpayment';
	protected $_canUseInternal = true;
	protected $_canUseCheckout = false; 
	protected $_canUseForMultishipping = false;
    //protected $_isGateway = true;
    //protected $_canAuthorize = true;

    public function authorize(Varien_Object $payment, $amount) {
        Mage::log("Dummypayment\tIn authorize");
        return $this;
    }
}
