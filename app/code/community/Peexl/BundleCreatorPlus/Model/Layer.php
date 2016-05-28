<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Layer extends Mage_Catalog_Model_Layer {

    /**
     * Retrieve product collection
     */
    public function getProductCollection() {
        $package = Mage::registry('product');
        if (!$package || $package->getTypeId() != 'extendedbundle') {
            return parent::getProductCollection();
        }
        $packageSession = Mage::helper('bundlecreatorplus')->getPackageSessionById($package->getId());
        if (!$packageSession->getActiveItem() || !$packageSession->getItems()) {
            return Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('entity_id', array('in' => '-1'));
        }
        $id = $package->getId() . '_' . $packageSession->getActiveItem()->getId();
        if (isset($this->_productCollections[$id])) {
            $collection = $this->_productCollections[$id];
        } else {
            $productIds = $packageSession->getActiveItem()->getInstance()->getProductIds(true);
            $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addFieldToFilter('entity_id', array('in' => $productIds));
            $this->preparePackageProductCollection($collection);
            $this->_productCollections[$id] = $collection;
        }

        return $collection;
    }

    public function preparePackageProductCollection($collection) {
        $collection
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite($this->getCurrentCategory()->getId());

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);

        if (!Mage::registry('product')->getShowInvisible()) {
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        }

        return $this;
    }

}
