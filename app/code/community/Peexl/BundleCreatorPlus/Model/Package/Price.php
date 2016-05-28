<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Package_Price extends Mage_Catalog_Model_Product_Type_Price {

    public function getFinalPrice($qty = null, $product) {
        $finalPrice = $product->getTypeInstance()->getPrice();
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        return $finalPrice;
    }

    public function getChildFinalPrice($product, $productQty, $childProduct, $childProductQty) {
        $finalPrice = $childProduct->getFinalPrice();

        $buyRequest = unserialize($product->getCustomOption('info_buyRequest')->getValue());
        foreach ($buyRequest['items'] as $itemProduct) {
            if ($itemProduct['product_id'] == $childProduct->getId()) {
                $rp = $itemProduct['request_params'];
                if (isset($rp['super_attribute']) && ($super = $rp['super_attribute'])) {
                    $_confProduct = Mage::getModel('catalog/product')->load($rp['product']);
                    $_confProduct->setRequestParams($rp);

                    $request = new Varien_Object($rp);
                    $_confProduct->getTypeInstance(true)->prepareForCartAdvanced($request, $_confProduct);

                    $childProduct->setFinalPrice($_confProduct->getFinalPrice());
                } else {
                    $childProduct->setRequestParams($rp);

                    $request = new Varien_Object($rp);
                    $childProduct->getTypeInstance(true)->prepareForCartAdvanced($request, $childProduct);
                }

                $itemOption = Mage::helper('bundlecreatorplus')->getOptionByProductId($itemProduct['request_params']['item'], $itemProduct['request_params']['product']);
                if (is_numeric($itemOption->getOverridePrice())) {
                    $finalPrice = $itemOption->getOverridePrice() * $childProductQty;
                } else {
                    $finalPrice = $childProduct->getFinalPrice() * $childProductQty;
                }
            }
        }

        return $finalPrice;
    }
    
    /**
     * Retrieve Price considering tier price
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  string|null                $which
     * @param  bool|null                  $includeTax
     * @param  bool                       $takeTierPrice
     * @return decimal|array
     */
    public function getTotalPrices($product, $which = null, $includeTax = null, $takeTierPrice = true)
    {                         
        $packageProduct = $product;
        $minimalPrice = $maximalPrice = 0;
        /**
         * Check if product price is fixed
         */
        if ($product->getData('price_type') == Mage::helper('bundlecreatorplus')->getPriceType('fixed')) { 
            foreach ($product->getTypeInstance(true)->getItems(null, $product) as $item) {
                $stepPrice = $item->getPrice() * $item->getQty();
                foreach($item->getOptions() as $option) {
                    if (is_numeric($option->getOverridePrice())) {
                        $stepPrice = $option->getOverridePrice() * $item->getQty();
                    }
                }
                if(!$item->getOptional()) {
                    $minimalPrice += $stepPrice;
                }
                $maximalPrice += $stepPrice;
            }
            $minimalPrice = Mage::helper('tax')->getPrice($product, $minimalPrice, $includeTax);
            $maximalPrice = Mage::helper('tax')->getPrice($product, $maximalPrice, $includeTax);
        } else {
            $itemsOptions = array();
            $itemsQty = array();
            $allProductsIds = array();
            $optionalItemsIds = array();
            foreach ($product->getTypeInstance(true)->getItems(null, $product) as $item) {
                $itemsOptions[$item->getId()] = $item->getOptions();
                $itemsQty[$item->getId()] = $item->getQty();
                if(1 || $item->getType() == 'products') {
                    foreach($itemsOptions[$item->getId()] as $option) {
                        if($option->getProductId()) {
                            $allProductsIds[] = $option->getProductId();
                        }
                    }
                }
                if($item->getOptional()) {
                    $optionalItemsIds[] = $item->getId();
                }
            }
            
            $allProductsIds = array_unique($allProductsIds);
            
            if(count($allProductsIds)) {            
                $storeId = $product->getStoreId();
                $productsCollection = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addAttributeToSelect('tax_class_id') //used for calculation item taxes in Bundle with Dynamic Price
                    ->setFlag('require_stock_items', true)
                    ->setFlag('product_children', true)
                    ->setStoreId($storeId);

                $productsCollection->getSelect()->where('entity_id IN (?)', $allProductsIds);

                $productsPrices = array();

                foreach($productsCollection as $product) {
                    if($product->getTypeId() == 'bundle') {
                        $_priceModel  = $product->getPriceModel();
                        $productsPrices[$product->getId()] = $_priceModel->getTotalPrices($product, null, null, false);
                    } else {
                        $minPrice = $maxPrice = $product->getFinalPrice();
                        $productsPrices[$product->getId()] = array($minPrice, $maxPrice);
                    }
                }
            }
            
            $minItemPrices = array();
            $maxItemPrices = array();
            
            foreach($itemsOptions as $itemId => $itemOptions) {
                if(!isset($minItemPrices[$itemId])) {
                    $minItemPrices[$itemId] = null;
                }
                if(!isset($minItemPrices[$itemId])) {
                    $maxItemPrices[$itemId] = null;
                }
                foreach($itemOptions as $option) {
                    if($option->getType() == 'option') {
                        $minPrice = $maxPrice = $option->getOverridePrice();
                    } else {
                        if(isset($productsPrices[$option->getProductId()])) {
                            $minPrice = $productsPrices[$option->getProductId()][0] * $itemsQty[$itemId];
                            $maxPrice = $productsPrices[$option->getProductId()][1] * $itemsQty[$itemId];
                        }
                    }
                    if(!in_array($itemId, $optionalItemsIds) && isset($minPrice) && ($minPrice < $minItemPrices[$itemId] || is_null($minItemPrices[$itemId]))) {
                        $minItemPrices[$itemId] = $minPrice;
                    }
                    if(isset($maxPrice) && ($maxPrice > $maxItemPrices[$itemId] || is_null($maxItemPrices[$itemId]))) {
                        $maxItemPrices[$itemId] = $maxPrice;
                    }
                }
            }
            
            $minimalPrice = array_sum($minItemPrices);
            $maximalPrice = array_sum($maxItemPrices);
        }
        

        

        $customOptions = $product->getOptions();
        if ($product->getData('price_type') == Mage::helper('bundlecreatorplus')->getPriceType('fixed') && $customOptions) {
            foreach ($customOptions as $customOption) {
                /* @var $customOption Mage_Catalog_Model_Product_Option */
                $values = $customOption->getValues();
                if ($values) {
                    $prices = array();
                    foreach ($values as $value) {
                        /* @var $value Mage_Catalog_Model_Product_Option_Value */
                        $valuePrice = $value->getPrice(true);

                        $prices[] = $valuePrice;
                    }
                    if (count($prices)) {
                        if ($customOption->getIsRequire()) {
                            $minimalPrice += Mage::helper('tax')->getPrice($product, min($prices), $includeTax);
                        }

                        $multiTypes = array(
                            //Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN,
                            Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX,
                            Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE
                        );

                        if (in_array($customOption->getType(), $multiTypes)) {
                            $maximalValue = array_sum($prices);
                        } else {
                            $maximalValue = max($prices);
                        }
                        $maximalPrice += Mage::helper('tax')->getPrice($product, $maximalValue, $includeTax);
                    }
                } else {
                    $valuePrice = $customOption->getPrice(true);

                    if ($customOption->getIsRequire()) {
                        $minimalPrice += Mage::helper('tax')->getPrice($product, $valuePrice, $includeTax);
                    }
                    $maximalPrice += Mage::helper('tax')->getPrice($product, $valuePrice, $includeTax);
                }
            }
        }

        if($packageProduct->load()->getDiscount()) {
            $minimalPrice *= (100 - $packageProduct->getDiscount()) / 100;
            $maximalPrice *= (100 - $packageProduct->getDiscount()) / 100;
        }

        if ($which == 'max') {
            return $maximalPrice;
        } elseif ($which == 'min') {
            return $minimalPrice;
        }

        return array($minimalPrice, $maximalPrice);
    }        

}
