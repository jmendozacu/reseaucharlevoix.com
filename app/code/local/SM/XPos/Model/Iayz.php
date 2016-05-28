<?php

    /**
     * Created by PhpStorm.
     * User: vjcspy
     * Date: 6/23/15
     * Time: 4:17 PM
     */
    class SM_XPos_Model_Iayz extends Mage_Core_Model_Abstract
    {
        public function _construct()
        {
            parent::_construct();
            $this->_init('xpos/iayz');
        }

        public function getQuote()
        {
            /*can get many thing from current quote(grand total*/
            $session = Mage::getSingleton('adminhtml/session_quote');

            return $quote = $session->getQuote();
        }

        public function getQuoteId()
        {
            if ($this->getQuote() != null) {
                return $this->getQuote()->getId();
            } else {
                return null;
            }
        }

        public function getCurrentPaymentInQuote()
        {
            if ($this->getQuote() != null) {
                return $this->getQuote()->getPayment();
            } else {
                return null;
            }
        }

        public function getConfigDataPaymentMethod($code, $field)
        {
            $path = 'payment/' . $code . '/' . $field;

            return Mage::getStoreConfig($path);
        }

        /*TODO: CONFIG TO USE IN HELPER PRODUCT <affects to perfomance>*/

        public function ultraBootLoad()
        {
            return true;
        }

        public function getAddData()
        {
            if ($this->isMaxPerfomance())
                return false;

            return true;
        }

        public function isEnableManageStockbyMangento()
        {
            if ($this->isMaxPerfomance())
                return false;

            return true;
        }

        public function isMaxPerfomance()
        {
            return false;
        }


        /*________________________________________________________*/

        public function getCurrentStoreId()
        {
            $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
            $storeCode = (string)Mage::getSingleton('adminhtml/config_data')->getStore();
            $websiteCode = (string)Mage::getSingleton('adminhtml/config_data')->getWebsite();
            if ('' !== $storeCode) { // store level
                try {
                    $storeId = Mage::getModel('core/store')->load($storeCode)->getId();
                } catch (Exception $ex) {
                }
            } elseif ('' !== $websiteCode) { // website level
                try {
                    $storeId = Mage::getModel('core/website')->load($websiteCode)->getDefaultStore()->getId();
                } catch (Exception $ex) {
                }
            }

            return $storeId;
        }

        public function getCustomerTaxClassId($customer)
        {
            return $customer->getTaxClassId();
        }

        public function getProOp(Mage_Adminhtml_Controller_Action $controller, $product)
        {
            $options = null;
            Mage::helper('catalog/product')->setSkipSaleableCheck(true);
            Mage::unregister('current_product');
            Mage::unregister('product');
            Mage::register('current_product', $product);
            Mage::register('product', $product);
            $update = $controller->getLayout()->getUpdate();
            $update->resetHandles();
            $type = 'LAYOUT_GENERAL_CACHE_TAG';
            Mage::app()->getCacheInstance()->cleanType($type); // Clean cache //Mage::app()->cleanCache();
            $productType = $product->getTypeId();
            if ($product->getHasOptions() || $productType == "configurable" || $productType == "bundle" || $productType == "grouped" || $productType == "giftcard") {
                $update->resetHandles();
                $update->addHandle('ADMINHTML_XPOS_CATALOG_PRODUCT_COMPOSITE_CONFIGURE');
                $update->addHandle('XPOS_PRODUCT_TYPE_' . $productType);
                $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
                $options = $controller->getLayout()->getOutput();
                if (strlen($options) < 3) {
                    $options = null;
                }
            }

            return $options;
        }

        public function getCurrentShippingTitle($_codeIn)
        {
            $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
            foreach ($methods as $_ccode => $_carrier) {
                if ($_methods = $_carrier->getAllowedMethods()) {
                    foreach ($_methods as $_mcode => $_method) {
                        $_code = $_ccode . '_' . $_mcode;
                        if ($_codeIn == $_code) {
                            return $_method;
                        }
                    }
                }
            }

            return '';
        }


        /*Timing script execution with microtime()*/
        public static function microtime_float()
        {
            list($usec, $sec) = explode(" ", microtime());

            return ((float)$usec + (float)$sec);
        }

        public static function startTime()
        {
            SM_XPos_Model_Iayz::setTimeStart(SM_XPos_Model_Iayz::microtime_float());
        }

        public static function timeExecution()
        {
            $timeStart = Mage::getSingleton('adminhtml/session')->getTimeStart();

//        if (!$timeStart) {
            return SM_XPos_Model_Iayz::microtime_float() - (float)SM_XPos_Model_Iayz::getTimeStart();
//        } else {
//            return null;
//        }
        }

        private static function setTimeStart($time)
        {
            Mage::getSingleton('adminhtml/session')->setTimeStart($time);
        }

        private static function getTimeStart()
        {
            Mage::getSingleton('adminhtml/session')->getTimeStart();
            Mage::getSingleton('adminhtml/session')->setTimeStart(null);
        }

//    end TIMING------------------------------------------------------------

        public function addLog($string)
        {
            Mage::log($string, NULL, 'iayz.log', true);
        }

        public function addLogCronjob($string)
        {
            Mage::log($string, null, 'xpos_cronjob.log', true);
        }

        public function getCurrentAdmin()
        {
            $currentAdmin = Mage::getSingleton('admin/session')->getUser();
            if ($currentAdmin == null) {
                return null;
            }
            $id = $currentAdmin->getId();
            $lastName = $currentAdmin->getLastname();
            $firstName = $currentAdmin->getFirstName();
            $email = $currentAdmin->getEmail();
            $getUserName = $currentAdmin->getUsername();

            return array(
                'id'        => $id,
                'lastName'  => $lastName,
                'firstName' => $firstName,
                'emai'      => $email,
                'userName'  => $getUserName,
            );
        }

        public function getManageStockByProductId($productId)
        {
//            $sProductId = 284;
            $oStockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            if (!!$oStockItem->getData('use_config_manage_stock')) {
                if (!!Mage::getStoreConfig('cataloginventory/item_options/manage_stock')) {
                    if (!!$isInstock = $oStockItem->getData('is_in_stock'))
                        $data = '11';
                    else
                        $data = '10';
                } else
                    $data = '0';
            } else {
                if (!!$oStockItem->getData('manage_stock')) {
                    if (!!$isInstock = $oStockItem->getData('is_in_stock'))
                        $data = '11';
                    else
                        $data = '10';
                } else {
                    $data = '0';
                }
            }

            return $data;
        }

    }
