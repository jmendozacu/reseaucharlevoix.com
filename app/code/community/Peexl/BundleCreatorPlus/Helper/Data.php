<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Helper_Data extends Mage_Catalog_Helper_Data {

    const XML_NODE_PACKAGE_PRODUCT_TYPE = 'global/catalog/product/type/extendedbundle';
    const XML_NODE_FRONTEND_FRONTNAME = 'frontend/routers/bundlecreatorplus/args';
    const FIXED_PRICE = 10;
    const DYNAMIC_PRICE = 20;

    protected $_session;

    public function getProduct() {
        $product = Mage::registry('product');
        if (!$product || $product->getTypeId() != 'extendedbundle') {
            $packageId = Mage::app()->getRequest()->getParam('package');
            if ($packageId) {
                $product = Mage::getModel('catalog/product')->load($packageId);
            }
        }
        return $product;
    }

    public function getPackageSession($product = null) {
        if (!$this->_session) {
            if (!$product) {
                $product = $this->getProduct();
            }
            $this->_session = Mage::getSingleton('bundlecreatorplus/package_session')->getPackageSession($product);
        }
        return $this->_session;
    }

    public function resetPackageSession($product) {
        Mage::getSingleton('bundlecreatorplus/package_session')->resetSession($product);
        $this->_session = null;
    }

    public function getPackageSessionById($packageId) {
        if (!$this->_session) {
            $this->_session = Mage::getSingleton('bundlecreatorplus/package_session')->getPackageSessionById($packageId);
        }
        return $this->_session;
    }

    public function getPackagesSession() {
        return Mage::getSingleton('bundlecreatorplus/package_session');
    }

    public function getComponents() {
        return $this->getPackageSession()->getComponents();
    }

    public function getActiveComponent() {
        return $this->getPackageSession()->getActiveComponent();
    }

    public function getItemOptionViewUrl($productId, $package = "", $item = "") {
        return Mage::getUrl(
                        $this->getModuleFrontname() . '/product/view', array(
                    'id' => $productId,
                    'package' => ($package) ? $package : $this->getProduct()->getId(),
                    'item' => ($item) ? $item : $this->getPackageSession()->getActiveItem()->getId(),
                        )
        );
    }

    public function getPackageAddItemOptionUrl($productId) {
        return Mage::getUrl(
                        $this->getModuleFrontname() . '/package/add', array(
                    'product' => $productId,
                    'package' => $this->getProduct()->getId(),
                    'item' => $this->getPackageSession()->getActiveItem()->getId(),
                    'item_id' => Mage::app()->getRequest()->getParam('item_id')
                        )
        );
    }

    public function getAllowedSelectionTypes() {
        $config = Mage::getConfig()->getNode(self::XML_NODE_PACKAGE_PRODUCT_TYPE);
        return array_keys($config->allowed_selection_types->asArray());
    }

    public function getModuleFrontname() {
        $config = Mage::getConfig()->getNode(self::XML_NODE_FRONTEND_FRONTNAME);
        return $config->frontName;
    }

    public function getOptionByProductId($itemId, $productId) {
        $options = Mage::getModel('bundlecreatorplus/item_option')
                ->getCollection()
                ->addFieldToFilter('item_id', $itemId)
                ->addFieldToFilter('product_id', $productId);
        return $options->getFirstItem();
    }

    public function isPackage() {
        $product = Mage::registry('current_product');
        if ($product && $product->getId()) {
            if ($product->getTypeId() == 'package') {
                return true;
            }
        }
    }

    public function getPackageProduct() {
        $packageId = (int) Mage::app()->getRequest()->getParam('package');

        if (!$packageId) {
            return false;
        }

        $package = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($packageId);

        return $package;
    }

    public function isPackagePriceFixed() {
        return ($this->getPackageProduct()->getPriceType() == self::FIXED_PRICE);
    }

    public function getPriceType($type) {
        switch ($type) {
            case 'fixed': {
                    return self::FIXED_PRICE;
                }
            case 'dynamic': {
                    return self::DYNAMIC_PRICE;
                }
        }
    }

}

