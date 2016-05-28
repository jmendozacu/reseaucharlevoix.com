<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Package extends Mage_Catalog_Model_Product_Type_Abstract {

    const TYPE_CODE = 'package';
    
    const PRICE_TYPE_FIXED = 10;
    const PRICE_TYPE_DYNAMIC = 20;

    protected $_isComposite = true;
    protected $_old_version = false;
    
    public function beforeSave($product = null) {
        parent::beforeSave($product);
        $product = $this->getProduct($product);
        if ($product->getData('price_type') == Mage::helper('bundlecreatorplus')->getPriceType('fixed')) {
            $product->setPrice($product->getTypeInstance()->getPrice());
        } else {
            $product->setPrice(null);
        }
        return $this;
    }
    public function save($product = null) {
        if(Mage::app()->getRequest()->getActionName() != 'save' || Mage::app()->getRequest()->getControllerName() != 'catalog_product') return;
        $item_ids = array();
        if ($items = $product->getItems()) {
            $item_options = $product->getItemOptions();

            foreach ($items as $key => $item) {
                $options = isset($item_options[$key]) ? $item_options[$key] : array();
                $product_ids = array();
                foreach ($options as $option) {
                    $product_ids[] = $option['product_id'];
                }
                if(!isset($item['optional']))$item['optional'] = 0;
                $item = new Varien_Object($item);
                $model = Mage::getModel('bundlecreatorplus/item')
                        ->setData($item->getData())
                        ->setIsOptional((int) $item->getIsOptional())
                        ->setProductId($product->getId())
                        ->setOptions($options)
                        ->setStoreId($product->getStoreId());

                if($item->getDeletePreviewBaseImage()) {
                    //$basePrevieImagePath = Mage::getBaseDir('media') . DS . 'packagebuilder' . DS . 'preview' . DS . $model->getPreviewBaseImage();
                    //if(file_exists($basePrevieImagePath)) {
                    //    unlink($basePrevieImagePath);
                    //}
                    $model->setPreviewBaseImage('');    
                }       
                if (isset($_FILES['product_items_' . $key . '_preview_base_image']['name']) && $_FILES['product_items_' . $key . '_preview_base_image']['name'] != '') {
                    try {
                        $uploader = new Varien_File_Uploader('product_items_' . $key . '_preview_base_image');
    
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
    
                        $basePath  = Mage::getBaseDir('media') . DS . 'packagebuilder' . DS . 'preview' . DS;
    
                        $filename = $_FILES['product_items_' . $key . '_preview_base_image']['name'];
                        $actualName = pathinfo($filename, PATHINFO_FILENAME);
                        $originalName = $actualName;
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);
    
                        $i = 1;
                        while (file_exists($basePath . $actualName . '.' . $extension)) {
                            $actualName = (string)$originalName . '_' . $i;
                            $filename = $actualName . '.' . $extension;
                            $i++;
                        }
    
                        // Upload the image
                        $result = $uploader->save($basePath, $filename);
                        $imageFilename = 'packagebuilder' . DS . 'preview' . DS . $result['file'];
                        $model->setPreviewBaseImage($imageFilename);
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        if ($model && $model->getId()) {
                            $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        } else {
                            $this->_redirect('*/*/');
                        }
                    }
                }
                
                if ($item->getItemId()) {
                    $model->setItemId($item->getItemId());
                }
                else {
                    $model->setItemId(null);
                }
                $model->save();
                $item_ids[] = $model->getItemId();
            }
        }
        $items = Mage::getModel('bundlecreatorplus/item')->getCollection()
            ->setStoreId($product->getStoreId())
            ->addFieldToFilter('product_id', $product->getId());
        if ($items) {
            foreach ($items as $item) {
                if (!in_array($item->getItemId(), $item_ids)) {
                    $item->setOptions(array())->save();
                    $item->delete();
                }
            }
        }
    }

    public function getItems($storeId = null, $product = null) {
        $product = $this->getProduct($product);
        if ($product->getId()) {
            $collection = Mage::getModel('bundlecreatorplus/item')->getCollection()
                ->addFieldToFilter('product_id', $product->getId())->setOrder('sort_order', 'ASC');
            if (!is_null($storeId)) {
                $collection->setStoreId($storeId);
            }
            if (count($collection) > 0) {
                return $collection;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getOrderOptions($product = null) {
        $optionArr = parent::getOrderOptions($product);
        $optionArr['product_calculations'] = self::CALCULATE_PARENT;
        return $optionArr;
    }

    public function prepareForCart(Varien_Object $buyRequest, $product = null) {
        $this->_old_version = true;
        return $this->prepareForCartAdvanced($buyRequest, $product);
    }

    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null) {
        $package = $this->getProduct($product);

        if ($this->_old_version) {
            $candidates = parent::prepareForCart($buyRequest, $product);
        } else {
            $candidates = parent::prepareForCartAdvanced($buyRequest, $product);
        }

        if (is_string($candidates)) {
            Mage::getSingleton('core/session')->addError($candidates);
            return $candidates;
        }

        //$candidates[0]->setCartQty(1);

        if (!isset($buyRequest['items']) && !is_array($buyRequest['items'])) {
            throw new Exception("A package must have selected components");
            return;
        }

        $items = Mage::getModel('bundlecreatorplus/item')->getCollection()
                ->addFieldToFilter('product_id', $product->getId());
        if (count($items) == 0) {
            throw new Exception("Package product is not sellable.");
            return;
        }

        foreach ($items as $item) {
            if (!$item->getOptional() && !isset($buyRequest['items'][$item->getCode()])) {
                throw new Exception("Missing required item options");
                return;
            }
        }

        $price = 0.00;
        $totalWeight = 0.00;
        $packageVirtual = true;
        foreach ($buyRequest['items'] as $code => $itemProduct) {
                $product = Mage::getModel('catalog/product')->load($itemProduct['product_id']);
                if ($product->getId()) {
                    $product->setParentProductId($package->getId());
                    
                    if($product->getTypeId() == 'bundle') {
                        $_result = $product->getTypeInstance(true)->prepareForCartAdvanced(new Varien_Object($itemProduct['request_params']), $product);
                        if (is_string($_result) && !is_array($_result)) {
                            throw new Exception($_result);
                            return;
                        }

                        if (!isset($_result[0])) {
                            throw new Exception('Cannot add item to the shopping cart.');
                            return;
                        }
                    } else {
                        $rp = $itemProduct['request_params'];
                        if (isset($rp['super_attribute']) && ($super = $rp['super_attribute'])) {
                            $_confProduct = Mage::getModel('catalog/product')->load($rp['product']);
                            $_confProduct->setRequestParams($rp);
                        
                            $request = new Varien_Object($rp);
                            $_confProduct->getTypeInstance(true)->prepareForCartAdvanced($request, $_confProduct);

                            $product->setFinalPrice($_confProduct->getFinalPrice());
                        } else {
                            $product->setRequestParams($rp);
                        
                            $request = new Varien_Object($rp);
                            $product->getTypeInstance(true)->prepareForCartAdvanced($request, $product);                        
                        }                     
                    }
                    
                    foreach ($items as $item) {
                        if ($item->getCode() == $code) {
                            $product->setCartQty($item->getQty())
                                    ->setRequestParams($itemProduct['request_params'])
                                    ->addCustomOption('unique', rand(0, 999999999));
                            if ($package->getData('price_type') == Mage::helper('bundlecreatorplus')->getPriceType('fixed')) {
                                $price += $item->getPrice() * $item->getQty();
                            }
                        }
                    }

                    $totalWeight += $product->getWeight() * $product->getCartQty();
                    $itemOption = Mage::helper('bundlecreatorplus')->getOptionByProductId($itemProduct['request_params']['item'], $itemProduct['request_params']['product']);
                    if (is_numeric($itemOption->getOverridePrice())) {
                        $price += $itemOption->getOverridePrice() * $product->getCartQty();
                    } elseif ($package->getData('price_type') != Mage::helper('bundlecreatorplus')->getPriceType('fixed')) {
                        $price += $product->getFinalPrice() * $product->getCartQty();
                    }
                
                    if ($product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                        $preparedLinks = array();
                        foreach ($product->getTypeInstance()->getLinks() as $link) {
                            $preparedLinks[] = $link->getId();
                        }
                        if ($preparedLinks) {
                            $product->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
                        }
                    } else {
                        $packageVirtual = false;
                    }
                    $isCandidate = false;
                    foreach ($candidates as $candidate) {
                        if ($candidate->getId() == $product->getId()) {
                            $candidate = $product;
                            $isCandidate = true;
                            break;
                        }
                    }
                    if (!$isCandidate) {
                        if ($product->getTypeId() == 'bundle') {
                            $_result[0]->setParentProductId($package->getId())
                                    ->addCustomOption('info_buyRequest', serialize($itemProduct['request_params']));
                            //->addCustomOption('bundle_option_ids', serialize(array_map('intval', $optionIds)))
                            //->addCustomOption('bundle_selection_attributes', serialize($attributes))
                            ;
                            //echo $_result[1]->getProductId() . $_result[1]->getParentProductId();die;
                            $candidates = array_merge($candidates, $_result);
                        } else {                   
                            //$product->addCustomOption('info_buyRequest', serialize(array()));
                            $candidates[] = $product;
                        }
                    }
                } else {
                    throw new Exception("Invalid component option");
                    return;
                }
        }

        // loop through all additional components
        if (0 && isset($buyRequest['additional'])) {
            foreach ($buyRequest['additional'] as $addon) {
                $product = Mage::getModel('catalog/product')->load($addon['product_id']);
                if ($product->getId()) {
                    $product->setParentProductId($package->getId())
                            ->setCartQty($addon['quantity'])
                            ->addCustomOption('unique', rand(0, 999999999));
                    $totalWeight += $product->getWeight() * $addon['quantity'];
                    if (is_numeric($itemOption->getOverridePrice())) {
                        $price += $itemOption->getOverridePrice();
                    } else {
                        $price += $product->getFinalPrice();
                    }
                    if ($product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                        $preparedLinks = array();
                        foreach ($product->getTypeInstance()->getLinks() as $link) {
                            $preparedLinks[] = $link->getId();
                        }
                        if ($preparedLinks) {
                            $product->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
                        }
                    } else {
                        $packageVirtual = false;
                    }
                    // add product to $components
                    $candidates[] = $product;
                } else {
                    throw new Exception("Invalid component option");
                    return;
                }
            }
        }
        $candidates[0]->addCustomOption('unique', rand(0, 999999999));
        $candidates[0]->addCustomOption('package_weight', $totalWeight);
        $candidates[0]->addCustomOption('package_price', $price);
        $candidates[0]->addCustomOption('package_virtual', $packageVirtual);
        $candidates[0]->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

        return $candidates;
    }

    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode) {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        $package = $this->getProduct($product);

        $items = Mage::getModel('bundlecreatorplus/item')->getCollection()
                ->addFieldToFilter('product_id', $product->getId());

        $price = 0.00;

        foreach ($buyRequest['items'] as $code => $itemProduct) {
                $product = Mage::getModel('catalog/product')->load($itemProduct['product_id']);
                if ($product->getId()) {
                    $request = new Varien_Object($itemProduct['request_params']);
                    $products = $product->getTypeInstance(true)->prepareForCartAdvanced($request, $product);
                    $product = $products[0];
                    $product->setParentProductId($package->getId());
                    
                    if ($product->getTypeId() == 'bundle') {
                        $_result = $product->getTypeInstance(true)->prepareForCartAdvanced(new Varien_Object($itemProduct['request_params']), $product);
                        if (is_string($_result) && !is_array($_result)) {
                            throw new Exception($_result);
                            return;
                        }

                        if (!isset($_result[0])) {
                            throw new Exception('Cannot add item to the shopping cart.');
                            return;
                        }
                    } else {
                        $rp = $itemProduct['request_params'];
                        if (isset($rp['super_attribute']) && ($super = $rp['super_attribute'])) {
                            $_confProduct = Mage::getModel('catalog/product')->load($rp['product']);
                            $_confProduct->setRequestParams($rp);
                        
                            $request = new Varien_Object($rp);
                            $_confProduct->getTypeInstance(true)->prepareForCartAdvanced($request, $_confProduct);

                            $product->setFinalPrice($_confProduct->getFinalPrice());
                        } else {
                            $product->setRequestParams($rp);
                        
                            $request = new Varien_Object($rp);
                            $product->getTypeInstance(true)->prepareForCartAdvanced($request, $product);                        
                        }                     
                    }
                    
                        
                    foreach ($items as $item) {
                        if ($item->getCode() == $code) {
                            $product->setCartQty($item->getQty())
                                    ->setRequestParams($itemProduct['request_params'])
                                    ->addCustomOption('unique', rand(0, 999999999));
                            if ($package->getData('price_type') == Mage::helper('bundlecreatorplus')->getPriceType('fixed')) {
                                $price += $item->getPrice() * $item->getQty();
                            }
                        }
                    }
                    
                    $totalWeight += $product->getWeight() * $product->getCartQty();
    
                    $itemOption = Mage::helper('bundlecreatorplus')->getOptionByProductId($itemProduct['request_params']['item'], $itemProduct['request_params']['product']);
                    if (is_numeric($itemOption->getOverridePrice())) {
                        $price += $itemOption->getOverridePrice() * $product->getCartQty();
                    } elseif ($package->getData('price_type') != Mage::helper('bundlecreatorplus')->getPriceType('fixed')) {
                        $price += $product->getFinalPrice() * $product->getCartQty();
                    }
                    if ($product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                        $preparedLinks = array();
                        foreach ($product->getTypeInstance()->getLinks() as $link) {
                            $preparedLinks[] = $link->getId();
                        }
                        if ($preparedLinks) {
                            $product->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
                        }
                    } else {
                        $packageVirtual = false;
                    }
                    if ($product->getTypeId() == 'bundle') {
                        $_result[0]->setParentProductId($package->getId())
                                ->addCustomOption('info_buyRequest', serialize($itemProduct['request_params']));
                        //->addCustomOption('bundle_option_ids', serialize(array_map('intval', $optionIds)))
                        //->addCustomOption('bundle_selection_attributes', serialize($attributes))
                        ;
                        //echo $_result[1]->getProductId() . $_result[1]->getParentProductId();die;
                        $result = array_merge($result, $_result);
                    } else {                   
                        //$product->addCustomOption('info_buyRequest', serialize(array()));
                        $result[] = $product;
                    }
                } else {
                    throw new Exception("Invalid component option");
                    return;
                }
        }

        $result[0]->addCustomOption('unique', rand(0, 999999999));
        $result[0]->addCustomOption('package_weight', $totalWeight);
        $result[0]->addCustomOption('package_price', $price);
        $result[0]->addCustomOption('package_virtual', $packageVirtual);
        $result[0]->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

        return $result;
    }

    public function processBuyRequest($product, $buyRequest) {
        Mage::helper('bundlecreatorplus')->resetPackageSession($product);
        $session = Mage::helper('bundlecreatorplus')->getPackageSession($product);
        $requestItems = $buyRequest->getItems();
        
        Mage::register('package', $product);

        $setActive = false;
        $isComplete = true;
        foreach ($session->getItems() as $item) {
            if (isset($requestItems[$item->getCode()])) {
                $itemProduct = Mage::getModel('catalog/product')->load($requestItems[$item->getCode()]['product_id']);
                $itemProduct->setRequestParams($requestItems[$item->getCode()]['request_params']);
                $item->setProduct($itemProduct);
                $item->setIsActive(false);
            } else {
                if (!$setActive) {
                    $item->setIsActive(true);
                    $setActive = true;
                    if(!$item->getOptional()) {
                        $isComplete = false;
                    }
                }
            }
        }
        if ($isComplete) {
            $session->setIsComplete();
        }
        
        $session->setQty($buyRequest->getQty());
        
        if ($cartItemId = Mage::app()->getRequest()->getParam('id')) {
            $request = Mage::app()->getRequest();
            if ($request->getControllerName() == 'cart' && $request->getActionName() == 'configure') {
                $session->setCartItemId($cartItemId);
            }
        }

        return array();
    }

    /**
     * Check product's options configuration
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  Varien_Object $buyRequest
     * @return array
     */
    public function checkProductConfiguration($product, $buyRequest) {
        return array();
    }

    public function isSalable($product = null) {
        return true;
    }

    public function getWeight($product = null) {
        return $this->getProduct($product)->getCustomOption('package_weight')->getValue();
    }

    public function getPrice() {
        $product = $this->getProduct();
        $price = 0.00;
        if ($product->getCustomOption('package_price')) {
            $price = $product->getCustomOption('package_price')->getValue();
        } else {
            if ($product->getData('price_type') == Mage::helper('bundlecreatorplus')->getPriceType('fixed')) {
                if (Mage::app()->getStore()->isAdmin()) {
                    $items = Mage::getModel('bundlecreatorplus/item')->getCollection()
                            ->addFieldToFilter('product_id', $product->getId());
                    foreach ($items as $item) {
                        $price += $item->getPrice() * $item->getQty();
                    }
                } else {
                    foreach (Mage::helper('bundlecreatorplus')->getPackageSession($product)->getItems() as $item) {
                        if (!$item->getIsComplete())
                            continue;
                        $price += $item->getPrice() * $item->getQty();
                        if (is_numeric($item->getOption()->getOverridePrice())) {
                            $price += $item->getOption()->getOverridePrice() * $item->getQty();
                        }
                    }
                }
            } else {
                foreach (Mage::helper('bundlecreatorplus')->getPackageSession($product)->getItems() as $item) {
                    if (!$item->getIsComplete())
                        continue;
                    if (is_numeric($item->getOption()->getOverridePrice())) {
                        $price += $item->getOption()->getOverridePrice() * $item->getQty();
                    } else {
                        $rp = $item->getProduct()->getRequestParams();
                        if (isset($rp['super_attribute']) && ($super = $rp['super_attribute'])) {
                            $_product = Mage::getModel('catalog/product')->load($rp['product']);
                        } else {
                            $_product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                        }
                        $_product->setRequestParams($rp);
                        
                        $request = new Varien_Object($rp);
                        $_product->getTypeInstance(true)->prepareForCartAdvanced($request, $_product);
                        
                        $price += $_product->getFinalPrice() * $item->getQty();
                    }
                }
            }
        }
        
        $discount = Mage::getModel('catalog/product')->load($product->getId())->getDiscount();
        if($discount && $discount <= 100) {
            $price *= (100 - $discount)/100;
        }
        return $price;
    }
    
    public function getIsFixed($product = null) {
        $product = $this->getProduct($product);
        return $product->getPriceType() == self::PRICE_TYPE_FIXED;
    }

    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null) {
        $product = $this->getProduct($product);
        if ($product->getCustomOption('package_virtual')) {
            return $product->getCustomOption('package_virtual')->getValue();
        }
    }

}