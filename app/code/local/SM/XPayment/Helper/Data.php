<?php
class SM_XPayment_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getPaymentAllowSplit(){
        return $payments = array('xpayment_cashpayment', 'xpayment_ccpayment','cashondelivery','xpayment_mastercardpayment','xpayment_interactpayment','xpayment_visapayment');
    }
    public function setConfig($path, $string)
    {
        try {
            Mage::getModel('core/config_data')
                ->load($path, 'path')
                ->setValue($string)
                ->setPath($path)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the config data with path ' . $path));
        }
    }
}
