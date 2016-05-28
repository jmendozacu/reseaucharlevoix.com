<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Item extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('bundlecreatorplus/item');
    }

    protected function _afterSave() {
        if (is_array($options = $this->getData('options'))) {
            $this->getResource()->saveOptions($this->getId(), $options);
        }

        parent::_afterSave();
    }
    
    public function getOptions($returnProducts = false, $applyStockFilter = false) {
        $options = Mage::getModel('bundlecreatorplus/item_option')
                ->getCollection()
                ->addFieldToFilter('item_id', $this->getId());
        if ($applyStockFilter) {
            $options->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());
            $options->getSelect()->join(array('s' => Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_status')), 'main_table.product_id = s.product_id AND s.stock_status = 1');
        } 
        if ($returnProducts) {
            $product_ids = array();
            foreach ($options as $option) {
                $product_ids[] = $option->getProductId();
            }
            $collection = Mage::getModel('catalog/product')->getCollection()
                    ->setStore($this->getStore())
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addAttributeToSelect('special_price')
                    ->addIdFilter($product_ids)
                    ->joinField('product_id', Mage::getSingleton('core/resource')->getTableName('catalog_product_entity'), 'entity_id', 'entity_id=entity_id', null, 'right')
                    ->joinField('preview_image', 'bundlecreatorplus/item_option', 'preview_image', 'product_id=entity_id', '{{table}}.item_id='.$this->getId(), 'right')
                    ->joinField('override_price', 'bundlecreatorplus/item_option', 'override_price', 'product_id=entity_id', '{{table}}.item_id='.$this->getId(), 'right')
                    ->addStoreFilter();
            return $collection;
        }    
        return $options;
    }

    public function getProductIds($applyStockFilter = false) {
        $productIds = array();
        if ($options = $this->getOptions(false, $applyStockFilter)) {
            foreach ($options as $option) {
                $productIds[] = $option->getProductId();
            }
        }
        return $productIds;
    }

    protected function cleanIds($ids = array()) {
        $clean = array();
        if (count($ids)) {
            foreach ($ids as $id) {
                $clean[] = trim($id);
            }
        }
        return $clean;
    }

}