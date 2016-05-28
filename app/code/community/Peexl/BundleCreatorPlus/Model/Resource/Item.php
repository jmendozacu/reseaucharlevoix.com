<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Resource_Item extends Mage_Core_Model_Mysql4_Abstract {

    public static $ATTRIBUTES = array(
        'title',
        'description'
    );
    
    protected function _construct() {
        $this->_init('bundlecreatorplus/item', 'item_id');
    }

    public function saveOptions($itemId, $options) {
        $optionsTable = $this->getTable('item_option');
        
        if(!count($options)) {
            $this->_getWriteAdapter()->delete($optionsTable, $this->_getWriteAdapter()->quoteInto("item_id=?", $itemId));
            return $this;
        }
        
        $productIds = array();
        foreach($options as $option) {
          $productIds[] = trim($option['product_id']);
        }
        
        // Delete unused options
        $flatIds = implode(",", $productIds);
        $this->_getWriteAdapter()->delete($optionsTable, $this->_getWriteAdapter()->quoteInto("item_id=? AND product_id NOT IN ($flatIds)", $itemId));

        // Add options to package item
        $currentProductIds = $this->_getReadAdapter()->fetchCol("SELECT product_id FROM $optionsTable WHERE item_id = ?", $itemId);
        foreach ($options as $option) {
            $productId = trim($option['product_id']);
            $previewImage = trim($option['preview_image']);
            $overridePrice = trim($option['override_price']);
            if (!in_array($productId, $currentProductIds)) {
                $this->_getWriteAdapter()->insert($optionsTable, array(
                    'item_id' => $itemId,
                    'product_id' => $productId,
                    'preview_image' => $previewImage,
                    'override_price' => $overridePrice
                ));
            } else {
              $this->_getWriteAdapter()->update($optionsTable, array(
                    'preview_image' => $previewImage,
                    'override_price' => $overridePrice
                ),  "item_id = $itemId AND product_id = $productId");
            }
        }
        return $this;
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $currentStoreId = $object->getStoreId();          
        $storesIds = array(0);
        foreach (Mage::app()->getStores() as $store) {
            $storesIds[] = $store->getId();
        }
        
        $storesIds = array_unique($storesIds);
        
        foreach ($storesIds as $storeId) {
            foreach (self::$ATTRIBUTES as $attributeCode) {
                if ($currentStoreId > 0 && $storeId !== $currentStoreId) {
                    continue;
                }
                $attributeValue = Mage::getModel('bundlecreatorplus/item_attribute_value')->getCollection()
                    ->addFieldToFilter('attribute_code', $attributeCode)
                    ->addFieldToFilter('store_id', $storeId)
                    ->addFieldToFilter('item_id', $object->getId())
                    ->getFirstItem();
                if (!$attributeValue->getId() || $currentStoreId === $storeId) {
                    $attributeValue
                        ->setAttributeCode($attributeCode)
                        ->setStoreId($storeId)
                        ->setItemId($object->getId())
                        ->setValue($object->getData($attributeCode))
                        ->save();
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $storeId = $object->getStoreId();          
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
        foreach (self::$ATTRIBUTES as $attributeCode) {
            $attributeValue = Mage::getModel('bundlecreatorplus/item_attribute_value')->getCollection()
                    ->addFieldToFilter('attribute_code', $attributeCode)
                    ->addFieldToFilter('store_id', $storeId)
                    ->addFieldToFilter('item_id', $object->getId())
                    ->getFirstItem();
            $object->setData($attributeCode, $attributeValue->getValue());
        }
        
        return $this;
    }

}