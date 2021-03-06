<?php
/**
 * Created by PhpStorm.
 * User: Huy
 * Date: 6/30/2015
 * Time: 10:39 AM
 */

class SM_XPos_Block_Adminhtml_XPos_Receipt_Fore extends Mage_Core_Block_Template {
    const DEFAULT_FORE_MESSAGE = '';

    public function _construct() {
        $this->setTemplate('sm/xpos/receipt/fore.phtml');
    }

    public function getForeMessage() {
        if ($this->getRequest()->getParam('xpos_receipt_fore_message') != null) {
            /*
             * For previewing
             */
            $message = $this->getRequest()->getParam('xpos_receipt_fore_message');
        } else {
            $storeConfig = Mage::getStoreConfig('general/store_information', $this->getOrder()->getStoreId());
            $defaultStoreName = $storeConfig['name'];
            $message = Mage::getStoreConfig('xpos/receipt/fore_message');
        }

        return empty($message) ? self::DEFAULT_FORE_MESSAGE : $message;

    }
    public function getId(){
        return $this->getOrderId();
    }
}
