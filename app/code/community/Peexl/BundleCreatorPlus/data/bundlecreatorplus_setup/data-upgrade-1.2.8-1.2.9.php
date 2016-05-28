<?php
$attributes = Peexl_BundleCreatorPlus_Model_Resource_Item::$ATTRIBUTES;
$items = Mage::getModel('bundlecreatorplus/item')->getCollection()->setUseAttributes(false);

$storesIds = array(0);
foreach (Mage::app()->getStores() as $store) {
    $storesIds[] = $store->getId();
}
$storesIds = array_unique($storesIds);

foreach ($items as $item) {
    foreach ($storesIds as $storeId) {
        foreach ($attributes as $attributeCode) {
            $attributeValue = Mage::getModel('bundlecreatorplus/item_attribute_value')->getCollection()
                ->addFieldToFilter('attribute_code', $attributeCode)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('item_id', $item->getId())
                ->getFirstItem();
            if (!$attributeValue->getId()) {
                $attributeValue
                    ->setAttributeCode($attributeCode)
                    ->setStoreId($storeId)
                    ->setItemId($item->getId())
                    ->setValue($item->getData($attributeCode))
                    ->save();
            }
        }
    }
}