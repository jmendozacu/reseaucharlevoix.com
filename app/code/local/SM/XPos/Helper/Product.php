<?php

    /**
     * Created by PhpStorm.
     * User: NguyenCT
     * Date: 3/28/14
     * Time: 10:38 AM
     */
    class SM_XPos_Helper_Product extends Mage_Core_Helper_Abstract
    {
        private   $_isOnlineMode          = false;
        protected $_store;
        private   $_bassCurrency;
        private   $_currentCurrency;
        private   $_billingAdd            = null;
        private   $_shippingAdd           = null;
        private   $_defaultCustomerId     = null;
        private   $_currentStoreIdSession = null;

        public function __construct()
        {
            $this->_store = Mage::app()->getStore(Mage::helper('xpos')->getXPosStoreId());
        }

        public function getCategoryList()
        {
            $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToFilter('xpos_enable', true)
                ->addAttributeToSelect('*');

            return $categories;
        }

        /**
         * huypq
         * 01/04/2015
         *
         */
        public function getCountAllProducts($warehouseId)
        {
            Mage::helper('catalog/product')->setSkipSaleableCheck(true);
//        $storeId = Mage::helper('xpos/product')->getCurrentSessionStoreId();
            $storeId = $this->getCurrentSessionStoreId();

            $products = Mage::getResourceModel('catalog/product_collection')
                ->addStoreFilter($storeId);
            if ($warehouseId) {
                $products->getSelect()->joinLeft(Mage::getConfig()->getTablePrefix() . 'sm_product_warehouses', 'entity_id =' . Mage::getConfig()->getTablePrefix() . 'sm_product_warehouses.product_id', array("warehouse_id", "enable"))
                    ->where(Mage::getConfig()->getTablePrefix() . "sm_product_warehouses.warehouse_id = " . $warehouseId . " AND " . Mage::getConfig()->getTablePrefix() . "sm_product_warehouses.enable = 1");
            }

            return $products->getSize();
        }

        public function getProductList($controller, $page = 1, $warehouseId)
        {
            $this->_isOnlineMode = false;
            Mage::helper('catalog/product')->setSkipSaleableCheck(true);

//        $storeId = Mage::helper('xpos/product')->getCurrentSessionStoreId();
            /*store ID get From Session*/
            $storeId = Mage::getSingleton('adminhtml/session')->getCurrentStoreView();
            $limit = Mage::helper('xpos/configXPOS')->getProdPerRequest();

            $productInfo = array();

            $productCollection = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->addAttributeToSelect('*')
                ->setPageSize($limit)
                ->setCurPage($page);

            if (Mage::helper('xpos/configXPOS')->getSearchingInStock() == 1) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
            }

            if ($warehouseId) {
                $productCollection->getSelect()->joinLeft(Mage::getConfig()->getTablePrefix() . 'sm_product_warehouses', 'entity_id =' . Mage::getConfig()->getTablePrefix() . 'sm_product_warehouses.product_id', array("warehouse_id", "enable"))
                    ->where(Mage::getConfig()->getTablePrefix() . "sm_product_warehouses.warehouse_id = " . $warehouseId . " AND " . Mage::getConfig()->getTablePrefix() . "sm_product_warehouses.enable = 1");
            }

            $productCollection = $this->queryProduct($productCollection);

            if ($productCollection->getLastPageNumber() < $page) {
                return array('productInfo' => $productInfo, 'totalProduct' => $productCollection->getSize(), 'totalLoad' => 0);
            }

            $productCollection->load();

            //        set Store
            Mage::app()->setCurrentStore($this->getCurrentSessionStoreId());
            $this->_bassCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
            $this->_currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();

            $allowProduct = array();
            if (!empty($warehouseId)) {
                $allowProduct = $this->getWarehouseProduct($warehouseId);
            }
            //$price_display_type_config = Mage::app()->getStore()->getConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE);
            //if ($price_display_type_config == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX) {
            //    Mage::app()->getStore()->setConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH);
            //}

            foreach ($productCollection as $product) {

                if (!in_array($product->getId(), $allowProduct) && !empty($warehouseId) && $product->getTypeId() == 'simple') {
                    continue;
                }


                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

                if (!$this->_billingAdd || !$this->_shippingAdd) {
                    $addDefaultCustomer = Mage::getModel('xpos/guest')->getAddDefaultCustomer();
                    $billingAdd = $this->_billingAdd = $addDefaultCustomer['billingAdd'];
                    $shippingAdd = $this->_shippingAdd = $addDefaultCustomer['shippingAdd'];

                } else {
                    $billingAdd = $this->_billingAdd;
                    $shippingAdd = $this->_shippingAdd;
                }
                $this->_isOnlineMode = true;
                if (!$this->_defaultCustomerId) {
                    $this->_defaultCustomerId = Mage::helper('xpos/configXPOS')->getDefaultCustomerId(Mage::helper('xpos/product')->getCurrentSessionStoreId());
                }
                $pInfo = $this->extractData($controller, $product, $billingAdd, $shippingAdd, $this->_defaultCustomerId);

                if ($warehouseId) {
                    $collection_qty = Mage::getModel('xwarehouse/warehouse_product')->getCollection()
                        ->addFieldToFilter('warehouse_id', array('eq' => $warehouseId))
                        ->addFieldToFilter('product_id', array('eq' => $product->getId()));
                    $info_array = $collection_qty->getData();

                    $pInfo['qty'] = $info_array[0]['qty'];

                } else
                    $pInfo['qty'] = $stock->getQty();

                $pInfo['is_qty_decimal'] = $stock->getData('is_qty_decimal');

                /** XPOS 2091: Refine product data to do save into localStorage
                 * Quantity SHOULD be Integer instead of current Float
                 * @temporary
                 */
                $pInfo['qty'] = $pInfo['is_qty_decimal'] == '1' ? $pInfo['qty'] : (int)$pInfo['qty'];

                $productInfo[$pInfo['id']] = $pInfo;
            }
            //if ($price_display_type_config == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX) {
            //    Mage::app()->getStore()->setConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX);
            //}

            return array('productInfo' => $productInfo, 'totalProduct' => $productCollection->getSize(), 'totalLoad' => sizeof($productInfo));
        }


        public function getWarehouseProduct($warehouseId)
        {
            $allowProduct = array();
            if (!empty($warehouseId)) {
                $currentUserId = Mage::getSingleton('admin/session')->getUser()->getId();
                if (!empty($currentUserId)) {
                    $warehouseItems = Mage::getModel('xwarehouse/warehouse_product')->getCollection();
                    $warehouseItems->addFieldToFilter('warehouse_id', array('eq' => $warehouseId));
                    foreach ($warehouseItems as $item) {
                        if ($item->getData('enable') == 1) {
                            $allowProduct[] = $item->getData('product_id');
                        }
                    }
                }
            }

            return $allowProduct;
        }

        public function getCategoryProduct($category, $page, $limit)
        {
            $result = array();
            $result = $this->getProductofCategory($category, $page, $limit);
            $result = array_unique($result);
            $limit -= count($result);
            //show child category's product too
            $parent_category = Mage::getModel('catalog/category')->load($category->getId());
            $subcats = $parent_category->getChildren();
            foreach (explode(',', $subcats) as $subCatid) {
                if ($limit <= 0) break;
                $_category = Mage::getModel('catalog/category')->load($subCatid);
                if ($_category->getIsActive()) {
                    $result_child = $this->getCategoryProduct($_category, $page, $limit);
                    $limit -= count($result_child);
                    $result = array_merge($result, $result_child);
                    $result = array_unique($result);
                }
            }
            // $new_result = array_merge($result,$result_child);
            //$new_result = array_unique($result);
            return $result;
        }

        public function getProductofCategory($category, $page, $limit)
        {
//        $storeId = Mage::helper('xpos/product')->getCurrentSessionStoreId();
            $storeId = $this->getCurrentSessionStoreId();
            $productCollection = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($storeId)
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('in' => $category->getId()))
                ->setOrder('created_at', 'asc')->setCurPage($page)->setPageSize($limit);

            if (Mage::helper('xpos/configXPOS')->getSearchingInStock() == 1) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
            }

            $productCollection = $this->queryProduct($productCollection);
            $productCollection->load();

            $result = array();
            foreach ($productCollection as $product) {
                $result[] = $product->getId();
            }
            $new_result = array_unique($result);

            return $new_result;
        }

        public function searchProduct($controller, $query, $warehouseId, $billingAdd = null, $shippingAdd = null, $customerId = null)
        {
            $this->_isOnlineMode = true;
            $number_result = Mage::helper('xpos/configXPOS')->getNumberResult();
            $collection = Mage::helper('catalogsearch')->getQuery()->getSearchCollection()
                ->addAttributeToSelect('*')
                ->addTaxPercents()
                ->setCurPage(0)
                ->setPageSize($number_result);
            if (Mage::helper('xpos/configXPOS')->getSearchingInStock() == 1) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
            }

            $searchBy = Mage::helper('xpos/configXPOS')->getSearchBy();
            if ($searchBy != '') {
                $result = array();
                $attributes = explode(",", $searchBy);
                foreach ($attributes as $attribute) {
                    $result[] = array('attribute' => $attribute, 'like' => '%' . $query . '%');
                }
            } else {
                $result = array();
                $result[] = array('attribute' => 'entity_id', 'eq' => $query);
            }
            $collection->addAttributeToFilter($result, null, 'left');

            $collection = $this->queryProduct($collection);

            $collection->load();

            //        set Store
            if (Mage::getSingleton('adminhtml/session')->getCurrentStoreView() != 'false' && Mage::getSingleton('adminhtml/session')->getCurrentStoreView() != null) {
                Mage::app()->setCurrentStore(Mage::getSingleton('adminhtml/session')->getCurrentStoreView());
            }
            $this->_bassCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
            $this->_currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();

            $allowProduct = array();
            if (!empty($warehouseId)) {
                $allowProduct = $this->getWarehouseProduct($warehouseId);
            }
            $productInfo = array();
            foreach ($collection as $product) {
                if (!in_array($product->getId(), $allowProduct) && !empty($warehouseId) && $product->getTypeId() == 'simple') {
                    continue;
                }
                if (!empty($warehouseId)) {
                    $pWarehouse = Mage::helper('xwarehouse')->getWarehouseItem($product->getId(), $warehouseId);
                    if ($pWarehouse->getFirstItem()->getEnable() != 1) {
                        continue;
                    }
                }

                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                Mage::helper('catalog/product')->setSkipSaleableCheck(true);
                $pInfo = $this->extractData($controller, $product, $billingAdd, $shippingAdd, $customerId);

                if ($warehouseId) {
                    $collection_qty = Mage::getModel('xwarehouse/warehouse_product')->getCollection()
                        ->addFieldToFilter('warehouse_id', array('eq' => $warehouseId))
                        ->addFieldToFilter('product_id', array('eq' => $product->getId()));
                    $info_array = $collection_qty->getData();

                    $pInfo['qty'] = $info_array[0]['qty'];

                } else
                    $pInfo['qty'] = $stock->getQty();
                $pInfo['is_qty_decimal'] = $stock->getData('is_qty_decimal');
                $productInfo[$pInfo['id']] = $pInfo;
            }

            return array('productInfo' => $productInfo);

        }

        function queryProduct($productCollection)
        {
            if (Mage::helper('xpos/configXPOS')->getSearchStatus() == 1) {
                $productCollection->addAttributeToFilter('status', array('eq' => 1));
            }

            if ($product_types = Mage::helper('xpos/configXPOS')->getSearchingPruductTypes()) {
                $product_types = explode(',', $product_types);
                if (is_array($product_types) && count($product_types) > 0) {
                    $productCollection->addFieldToFilter('type_id', array('IN', $product_types));
                }
            }

            if ($visibility = Mage::helper('xpos/configXPOS')->getSearchingProductVisibility()) {
                $visibility = explode(',', $visibility);
                if (is_array($visibility) && count($visibility) > 0) {
                    $productCollection->addFieldToFilter('visibility', array('IN', $visibility));
                }
            }

            return $productCollection;
        }

        function extractData($controller, $product, $billingAdd = null, $shippingAdd = null, $customerId = null)
        {
            $productType = $product->getTypeId();
            $hasOption = false;
            if (($product->hasCustomOptions()) && $productType == 'simple' || in_array($productType,array('booking',))) {
//            foreach ($product->getProductOptionsCollection() as $option) {
//                $option->setProduct($product);
//                $product->addOption($option);
//            }
                $hasOption = true;
            }

            $image = Mage::helper('catalog/image');
            $tax = Mage::helper('tax');
            $calcTax = true;
            $options = null;

            $smallImage = $product->getData('small_image');
            if ($smallImage != null && $smallImage != 'no_selection') {
                try {
                    $smallImage = $image->init($product, 'small_image')->resize(75)->__toString();
                } catch (Exception $e) {
                    $smallImage = null;
                }

            } else {
                $smallImage = null;
            }

            //another search data
            $searchBy = Mage::helper('xpos/configXPOS')->getSearchBy();
            $anotherData = '';
            if ($searchBy != '') {
                $result = array();
                $attributes = explode(",", $searchBy);
                foreach ($attributes as $attribute) {
                    if ($attribute != 'apparel_type') {
                        $anotherData .= $product->getResource()->getAttribute($attribute)->getFrontend()->getValue($product) . ' ';
                    } else {
                        $anotherData .= $product->getTypeID() . ' ';
                    }
                }
            }

            //additional field show in search
//            $additional = Mage::getStoreConfig('xpos/search/additional_field');
//            $additionalData = $product->getResource()->getAttribute($additional)->getFrontend()->getValue($product) . ' ';

            if ($this->getIayzModel()->ultraBootLoad()) {
            } else {

                Mage::unregister('current_product');
                Mage::unregister('product');
                Mage::register('current_product', $product);
                Mage::register('product', $product);
                $update = $controller->getLayout()->getUpdate();
                $type = 'LAYOUT_GENERAL_CACHE_TAG';
                Mage::app()->getCacheInstance()->cleanType($type); // Clean cache //Mage::app()->cleanCache();
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
            }
//        $finalPriceValue = $product->getFinalPrice();
//        $finalPriceWithTax = $tax->getPrice($product, $finalPriceValue, $calcTax);


//        $price_includes_tax = Mage::getStoreConfig('tax/calculation/price_includes_tax');
//        $tax_display_type = Mage::getStoreConfig('tax/display/type');
//        $tax_cart_display = Mage::getStoreConfig('tax/cart_display/price');

            if ($productType == 'giftcard') {

                $temp = $product->getData('giftcard_amounts');
                if (isset($temp[0])) {
                    $price = (float)$temp[0]['value'];
                } else $price = 0;
                //$price = $product->getData('giftcard_amounts')[0]['value'];
//            $finalPrice = (float)$price;
//            $finalPriceWithTax = (float)$price;
            }

            $lstBundleId = null;
            /*TODO: remove qty bundle product and configurable. Now, X-Pos only check simple product qty*/
//        if ($product->getTypeId() == 'bundle') {
//            $lstBundleId = array();
//            if ($product->getId() == 138) {
//                $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
//                    $product->getTypeInstance(true)->getOptionsIds($product), $product
//                );
//                foreach ($selectionCollection as $selection) {
//                    $childId = $selection->getData('product_id');
//                    $childProduct = Mage::getModel('catalog/product')->load($childId);
//                    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct);
//
//                    $lstBundleId[$selection->getData('selection_id')] = $stock->getQty();
//                }
//            }
//            // $lstBundleId = $product->getTypeInstance(true)->getChildrenIds($product->getId(), false); //list items id without selection_id
//        }
            if ($product->getTypeId() == 'configurable') {
                $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);

                $simple_collection = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();

                foreach ($simple_collection as $simplePro) {
                    $childId = $simplePro->getData('entity_id');
                    $childProduct = Mage::getModel('catalog/product')->load($childId);
                    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct);
                    $lstBundleId[$simplePro->getData('entity_id')] = $stock->getQty();
                }
            }

            /* BEGIN RE CAL TAX - END Price
             * Perfect behavior: in System > Configuration > Tax > Calculation Settings > Tax Calculation Based On
             *  If Tax Calculation Based On is "Shipping Origin": tax amount of product in local data is calculated from address in System > Configuration > Shipping Address > Origin
             * If Tax Calculation Based On is "Shipping Adress": tax amount of product in local data is calculated from address in System > Configuration > Tax > Default Tax Destination Calculation
           */
            if ($this->_isOnlineMode == false) {
                $product->setTaxPercent(null);
                //$fPrice = $tax->getPrice($product, $product->getFinalPrice(), null, /*$shippingAddress =*/ false, /*$billingAddress = */ false, /*ctc = */ false);
                $fPrice = $tax->getPrice($product, $product->getFinalPrice(), false, null, null, null, null, null, true);
                $product->setTaxPercent(null);
                $fPriceInclTax = $tax->getPrice($product, $product->getFinalPrice(), true, null, null, null, null, null, true);
                $percent = $product->getTaxPercent();
                $includingPercent = null;
                $taxClassId = $product->getTaxClassId();
                if (is_null($percent)) {
                    if ($taxClassId) {
                        $request = Mage::getSingleton('tax/calculation')
                            ->getRateRequest(null, null, null, null);
                        $percent = Mage::getSingleton('tax/calculation')
                            ->getRate($request->setProductClassId($taxClassId));
                    }
                }
                if (!$percent) {
                    $percent = 0;
                }
            } else {
                $product->setTaxPercent(null);
//            $currentStoreId = $this->getIayzModel()->getCurrentStoreId();
                $currentCustomer = Mage::getModel('customer/customer')->load($customerId);
                $customerTaxClassId = $this->getIayzModel()->getCustomerTaxClassId($currentCustomer);
                //$fPrice = $tax->getPrice($product, $product->getFinalPrice(), null, /*$shippingAddress =*/ false, /*$billingAddress = */ false, /*ctc = */ false);
                SM_XPos_Model_Tax_Calculation::$_getFromXpos = true;
                $fPrice = $tax->getPrice($product, $product->getFinalPrice(), false, $shippingAdd, $billingAdd, $customerTaxClassId, null, null, true);
                $product->setTaxPercent(null);
                SM_XPos_Model_Tax_Calculation::$_getFromXpos = true;
                $fPriceInclTax = $tax->getPrice($product, $product->getFinalPrice(), true, $shippingAdd, $billingAdd, $customerTaxClassId, null, null, true);
                $percent = $product->getTaxPercent();
                $includingPercent = null;
                $taxClassId = $product->getTaxClassId();
                if (is_null($percent)) {
                    if ($taxClassId) {
                        $request = Mage::getSingleton('tax/calculation')
                            ->getRateRequest(null, null, null, null);
                        $percent = Mage::getSingleton('tax/calculation')
                            ->getRate($request->setProductClassId($taxClassId));
                    }
                }
                if (!$percent) {
                    $percent = 0;
                }
            }
            /*end - recal TAX AND PRICE*/


            /*
             * Get current (singleton) tax calculation
             */
            $taxCalc = Mage::getSingleton('tax/calculation');

            /*
             * Get Tax Percent.
             * Apply for case that it can't load tax percent when setting Catalog Prices to Excluding and Display Product Prices In Catalog to Excluding
             *
             */
//        $xPOSStore = Mage::app()->getStore(Mage::getStoreConfig('xpos/store_id'));
//        $rateRequest = $taxCalc->getRateRequest(null, null, null, $xPOSStore);
//        $taxPercent = $taxCalc->getRate($rateRequest->setProductClassId($product->getData('tax_class_id')));

            /*
             * Get Tax Amount
             * Use Magento's round method
             */


            $taxAmount = $taxCalc->calcTaxAmount($fPrice, $percent, false, $round = true);

            if ($this->_currentCurrency) {
                $baseCurrencyCode = $this->_bassCurrency;
                $currentCurrencyCode = $this->_currentCurrency;
            } else {
                $this->_bassCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
                $this->_currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();
                $baseCurrencyCode = $this->_bassCurrency;
                $currentCurrencyCode = $this->_currentCurrency;
            }

            $priceFixCurrency = Mage::helper('directory')->currencyConvert($fPrice, $baseCurrencyCode, $currentCurrencyCode);
            $priceInclTaxFixCurrency = Mage::helper('directory')->currencyConvert($fPriceInclTax, $baseCurrencyCode, $currentCurrencyCode);

            $pInfo = array(
                'id'           => $product->getId(),
                'type'         => $product->getTypeId(),
                'name'         => $product->getData('name'),
                'price'        => $priceFixCurrency,
                //            'finalPrice' => $this->_store->convertPrice($finalPrice),
                //            'finalPrice' => $priceFixCurrency,
                //            'priceInclTax' => $this->_store->convertPrice($fPriceInclTax),
                'priceInclTax' => $priceInclTaxFixCurrency,
                'small_image'  => $smallImage,
                'sku'          => $product->getSku(),
                'tax'          => $percent,
                'searchString' => trim($anotherData),
                //            'includeTax' => $price_includes_tax,
                //            'productPrice' => $this->_store->convertPrice($fPriceInclTax),
                //            'productPrice' => $priceInclTaxFixCurrency,
                'tax_amount'   => $taxAmount,
            );
            if ($hasOption == true) {
                $pInfo['h'] = '1';
            }


            /*TODO: get setting to allow the stock to be managed via the backend. Reduce perfomance*/
            if ($this->getIayzModel()->isEnableManageStockbyMangento() == true) {
                $pInfo['ms'] = $this->getIayzModel()->getManageStockByProductId($pInfo['id']);
            }


            if ($this->getIayzModel()->getAddData() == true) {
                /** Fixed localStorage limit issues
                 *
                 */

                $pData = array();
                try {
                    $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')
                        ->getParentIdsByChild($product->getId());
                    foreach ($parentIds as $_parentId) {
                        $_parentProduct = Mage::getModel('catalog/product')->load($_parentId);
                        $_superAttributes = $_parentProduct->getTypeInstance(true)->getConfigurableAttributes($_parentProduct);
                        foreach ($_superAttributes as $_superAttribute) {
                            $_superAttributeCode = $_superAttribute->getProductAttribute()->getAttributeCode();
                            $pData[$_superAttributeCode] = $product->getData($_superAttributeCode);
                        }
                    }
                } catch (Exception $ex) {
                    Mage::log('Error load product: ' . $product->getId());
                }

                /*
                 * XPOS 2091: Refine product data to do save into localStorage
                 * Only load "list_bundle", "options" for non simple products
                 */
                if ($lstBundleId != null)
                    $pData['list_bundle'] = $lstBundleId;
                if ($options != null)
                    $pData['options'] = $options;

                /*
                 * XPOS 1463: User can enable attribute(s) to display in product listing
                 * This punch will make the load action slow down
                 */
                $additionalAttributesForDisplayOnHover = Mage::helper('xpos/configXPOS')->getAttributeForDisplay();
                if (!empty($additionalAttributesForDisplayOnHover)) {
                    $additionalInformation = array();
                    $_attributes = explode(',', $additionalAttributesForDisplayOnHover);
                    foreach ($_attributes as $_attribute) {
                        $_attributeValue = $product->getAttributeText($_attribute);
                        if ($_attributeValue == false) {
                            $_attributeValue = $product[$_attribute];
                        }
                        if ($_attributeValue)
                            $additionalInformation[$_attribute] = $_attributeValue;
                    }
                }

                if (isset($additionalInformation) && !empty($additionalInformation)) {
                    $pData['additional_info'] = $additionalInformation;
                }

                //for displaying child products SKU
                $pInfo = array_merge($pData, $pInfo);
            }

            //$pInfo = array_filter( $pInfo, 'strlen' );
            return $pInfo;
        }


        function getCategoryData($product, $catData)
        {
            $productCategory = '';
            foreach ($product->getCategoryIds() as $cid) {
                if (isset($catData[$cid])) {
                    $productCategory .= $catData[$cid] . ',';
                }
            }

            return $productCategory;
        }

        public function getCurrentSessionStoreId()
        {
            if (Mage::getSingleton('adminhtml/session')->getCurrentStoreView() != null) {
                if (is_null($this->_currentStoreIdSession)) {
                    return $this->_currentStoreIdSession = Mage::getSingleton('adminhtml/session')->getCurrentStoreView();
                } else {
                    return $this->_currentStoreIdSession;
                }
            } else {

                Mage::log(var_export('not found store in helper xpos/product', TRUE), NULL, 'vjcspy_product.log', true);

                return Mage::getStoreConfig('xpos/general/storeid');
            }

        }

        public function createDummyProduct($sku, $numOfDumy)
        {
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            $product = Mage::getModel('catalog/product');
//    if(!$product->getIdBySku('testsku61')):

            try {
                for ($i = 1; $i < $numOfDumy + 1; $i++) {
                    $sku .= $i;
                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); //
                    $product = Mage::getModel('catalog/product');
                    $rand = rand(1, 9999);
                    $product
                        ->setTypeId('simple')
                        ->setAttributeSetId(4)// default attribute set
                        ->setSku($sku)// generate a random SKU
                        ->setWebsiteIDs(array(1));

                    $product
                        ->setCategoryIds(array(2, 3))
                        ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                        ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH); // visible in catalog and search

                    $product->setStockData(array(
                        'use_config_manage_stock' => 0, // use global config?
                        'manage_stock'            => 1, // should we manage stock or not?
                        'is_in_stock'             => 1,
                        'qty'                     => 5,
                    ));

                    $product
                        ->setName('Test Product #' . $rand)// add string attribute
                        ->setShortDescription('Description')// add text attribute

                        // set up prices
                        ->setPrice(24.50)
                        ->setSpecialPrice(19.99)
                        ->setTaxClassId(2)// Taxable Goods by default
                        ->setWeight(87);
                    $product->setIsMassupdate(true)->setExcludeUrlRewrite(true);
                    $product->save();
                }

//endif;
            } catch (Exception $e) {
                die($e->getMessage());
            }
            die('Created ' . $numOfDumy . ' product(s)');
        }

        public function getProduct($controller, $productId, $storeId, $warehouseId = null)
        {
            if (is_null($storeId)) {
                $storeId = $this->getCurrentSessionStoreId();
            }
            $this->_isOnlineMode = false;
            Mage::helper('catalog/product')->setSkipSaleableCheck(true);


            $productInfo = array();


            $productCollection = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->addAttributeToFilter('entity_id', array('eq' => $productId))
                ->addAttributeToSelect('*');
            //->load($productId);

            if (Mage::helper('xpos/configXPOS')->getSearchingInStock() == 1) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
            }
            if ($warehouseId) {
                $productCollection->getSelect()->joinLeft(Mage::getConfig()->getTablePrefix() . 'sm_product_warehouses', 'entity_id =' . Mage::getConfig()->getTablePrefix() . 'sm_product_warehouses.product_id', array("warehouse_id", "enable"))
                    ->where(Mage::getConfig()->getTablePrefix() . "sm_product_warehouses.warehouse_id = " . $warehouseId . " AND " . Mage::getConfig()->getTablePrefix() . "sm_product_warehouses.enable = 1");
            }

            $productCollection = $this->queryProduct($productCollection);

            $productCollection->load();

            Mage::app()->setCurrentStore($this->getCurrentSessionStoreId());
            $this->_bassCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
            $this->_currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();

            $allowProduct = array();
            if (!empty($warehouseId)) {
                $allowProduct = $this->getWarehouseProduct($warehouseId);
            }
            $price_display_type_config = Mage::app()->getStore()->getConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE);
            if ($price_display_type_config == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX) {
                Mage::app()->getStore()->setConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH);
            }

            foreach ($productCollection as $product) {

                if (!in_array($product->getId(), $allowProduct) && !empty($warehouseId) && $product->getTypeId() == 'simple') {
                    continue;
                }


                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

                if (!$this->_billingAdd || !$this->_shippingAdd) {
                    $addDefaultCustomer = Mage::getModel('xpos/guest')->getAddDefaultCustomer();
                    $billingAdd = $this->_billingAdd = $addDefaultCustomer['billingAdd'];
                    $shippingAdd = $this->_shippingAdd = $addDefaultCustomer['shippingAdd'];

                } else {
                    $billingAdd = $this->_billingAdd;
                    $shippingAdd = $this->_shippingAdd;
                }
                $this->_isOnlineMode = true;
                if (!$this->_defaultCustomerId) {
                    $this->_defaultCustomerId = Mage::helper('xpos/configXPOS')->getDefaultCustomerId(Mage::helper('xpos/product')->getCurrentSessionStoreId());
                }
                $pInfo = $this->extractData($controller, $product, $billingAdd, $shippingAdd, $this->_defaultCustomerId);

                if ($warehouseId) {
                    $collection_qty = Mage::getModel('xwarehouse/warehouse_product')->getCollection()
                        ->addFieldToFilter('warehouse_id', array('eq' => $warehouseId))
                        ->addFieldToFilter('product_id', array('eq' => $product->getId()));
                    $info_array = $collection_qty->getData();

                    $pInfo['qty'] = $info_array[0]['qty'];

                } else
                    $pInfo['qty'] = $stock->getQty();

                $pInfo['is_qty_decimal'] = $stock->getData('is_qty_decimal');
                $pInfo['qty'] = $pInfo['is_qty_decimal'] == '1' ? $pInfo['qty'] : (int)$pInfo['qty'];

                $productInfo[$pInfo['id']] = $pInfo;
            }
            if ($price_display_type_config == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX) {
                Mage::app()->getStore()->setConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX);
            }

            return array('productInfo' => $productInfo, 'totalProduct' => $productCollection->getSize(), 'totalLoad' => sizeof($productInfo));
        }

        private function getIayzModel()
        {
            return Mage::getSingleton('xpos/iayz');
        }

        private function getInventory()
        {
            return Mage::getSingleton('cataloginventory/stock_item');
        }
    }
