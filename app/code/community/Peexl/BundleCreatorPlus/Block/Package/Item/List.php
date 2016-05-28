<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Package_Item_List extends Mage_Catalog_Block_Product_List {

    protected $_productCollection = '';

    public function getCacheKey() {
        $params = $this->getRequest()->getParams();
        if (isset($params['reset'])) {
            unset($params['reset']);
        }
        if (!isset($params['limit'])) {
            if (Mage::getSingleton('catalog/session')->hasData('limit_page')) {
                $params['limit'] = Mage::getSingleton('catalog/session')->getLimitPage();
            }
        }
        if (!isset($params['item'])) {
            $params['item'] = $this->getActiveItem()->getId();
        }
        ksort($params);
        $filters = "";
        foreach ($params as $key => $value) {
            $filters .= "_" . $key . ":" . $value;
        }
        $cacheKey = "catalog_product_package_" . $filters;
        $cacheKey = strtoupper($cacheKey);
        return $cacheKey;
    }

    public function getCacheTags() {
        return array(Mage_Catalog_Model_Product::CACHE_TAG, 'package_product');
    }

    public function getCacheLifetime() {
        return false;
    }

    public function getPackageProduct() {
        $product = Mage::registry('current_product');
        if (!$product || $product->getTypeId() != 'extendedbundle') {
            $packageId = Mage::app()->getRequest()->getParam('package');
            if ($packageId) {
                $product = Mage::getModel('catalog/product')->load($packageId);
            }
        }
        return $product;
    }

    public function getPackageSession() {
        return Mage::helper('bundlecreatorplus')->getPackageSession();
    }

    public function getActiveItem() {
        return $this->getPackageSession()->getActiveItem();
    }

    public function getLoadedProductCollection() {
        return $this->_getProductCollection();
    }

    protected function _beforeToHtml() {
        $this->getToolbarBlock()
                ->removeOrderFromAvailableOrders('position')
                ->removeOrderFromAvailableOrders('price')
                ->setCollection($this->getLoadedProductCollection());

        return parent::_beforeToHtml();
    }

    protected function _getProductCollection() {
        if (!$this->_productCollection) {
            $this->_productCollection = Mage::getSingleton('bundlecreatorplus/layer')->getProductCollection();
        }
        return $this->_productCollection;
    }

}