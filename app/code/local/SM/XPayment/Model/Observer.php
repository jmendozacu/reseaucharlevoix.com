<?php

/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 7/24/15
 * Time: 10:49 AM
 */
class SM_XPayment_Model_Observer extends Mage_Core_Model_Abstract
{
    const STRING_PATH_MASTERCARD = 'payment/xpayment_mastercardpayment/';
    const STRING_PATH_INTERACT = 'payment/xpayment_interactpayment/';
    const STRING_PATH_CASHPAYMENT = 'payment/xpayment_cashpayment/';
    const STRING_PATH_CCPAYMENT = 'payment/xpayment_ccpayment/';
    const STRING_PATH_VISAPAYMENT = 'payment/xpayment_visapayment/';
    const STRING_PATH_SPAYMENT = 'payment/xpaymentMultiple/';

    public function handle_adminSystemConfigChangedSectionSPayment()
    {
        $masterCardConfig = Mage::getStoreConfig('xpayment/xpayment_mastercardpayment');
        $this->setDataToPaymentConfig(self::STRING_PATH_MASTERCARD, $masterCardConfig);

        $interactConfig = Mage::getStoreConfig('xpayment/xpayment_interactpayment');
        $this->setDataToPaymentConfig(self::STRING_PATH_INTERACT, $interactConfig);

        $cashConfig = Mage::getStoreConfig('xpayment/xpayment_cashpayment');
        $this->setDataToPaymentConfig(self::STRING_PATH_CASHPAYMENT, $cashConfig);

        $ccCashConfig = Mage::getStoreConfig('xpayment/xpayment_ccpayment');
        $this->setDataToPaymentConfig(self::STRING_PATH_CCPAYMENT, $ccCashConfig);

        $visaConfig = Mage::getStoreConfig('xpayment/xpayment_visapayment');
        $this->setDataToPaymentConfig(self::STRING_PATH_VISAPAYMENT, $visaConfig);

        $multipleConfig = Mage::getStoreConfig('xpayment/xpaymentMultiple');
        $this->setDataToPaymentConfig(self::STRING_PATH_SPAYMENT, $multipleConfig);
    }

    protected function setDataToPaymentConfig($path, $data)
    {
        $this->setConfig($path . 'active', $data['active']);
        $this->setConfig($path . 'title', $data['title']);
        $this->setConfig($path . 'order_status', $data['order_status']);
        $this->setConfig($path . 'allowspecific', $data['allowspecific']);
        $this->setConfig($path . 'sort_order', $data['sort_order']);
        if (isset($data['specificcountry'])) {
            $this->setConfig($path . 'specificcountry', $data['specificcountry']);
        }
    }

    protected function getConfig($path)
    {
        return Mage::getModel('core/config_data')->load($path, 'path');
    }

    protected function setConfig($path, $string)
    {
        Mage::helper('xpayment/data')->setConfig($path, $string);
    }
}
