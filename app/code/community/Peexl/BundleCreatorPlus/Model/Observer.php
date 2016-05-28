<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2016 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Observer {

    /**
     * Setting attribute tab block for package
     *
     * @param Varien_Object $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function setAttributeTabBlock($observer) {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'extendedbundle') {
            Mage::helper('adminhtml/catalog')
                    ->setAttributeTabBlock('bundlecreatorplus/adminhtml_product_edit_tab_attributes');
        }
        return $this;
    }

    /**
     * Set correct price on the product page that is inside package
     *
     * @param Varien_Event_Observer $observer
     */
    public function setPriceOnProductPage(Varien_Event_Observer $observer)
    {
        $moduleName = Mage::app()->getRequest()->getModuleName();
        $actionName = Mage::app()->getRequest()->getActionName();
        $controllerName = Mage::app()->getRequest()->getControllerName();

        $frontName = Mage::getConfig()->getNode('frontend/routers/bundlecreatorplus/args/frontName');

        // Only for bcp/product/view pages
        if ($frontName != $moduleName || $controllerName != 'product' || $actionName != 'view') {
            return $this;
        }

        $item = Mage::helper('bundlecreatorplus')->getPackageSession()->getActiveItem();
        $itemOption = Mage::helper('bundlecreatorplus')->getOptionByProductId(
            $item->getId(),
            $observer->getEvent()->getProduct()->getId()
        );
        $overridePrice = $itemOption->getOverridePrice();
        $price = $item->getPrice();

        // Fixed price
        if (Mage::helper('bundlecreatorplus')->isPackagePriceFixed()) {
            if(is_numeric($itemOption->getOverridePrice())) {
                $finalPrice = $overridePrice + $price;
            } else {
                $finalPrice = $price;
            }

            $observer->getEvent()->getProduct()->setFinalPrice($finalPrice);
            $observer->getEvent()->getProduct()->setPrice($finalPrice);

        } else {
            if(is_numeric($itemOption->getOverridePrice())) {
                $observer->getEvent()->getProduct()->setFinalPrice($overridePrice);
                $observer->getEvent()->getProduct()->setPrice($overridePrice);
            }
        }
        
        return $this;
    }

    public function productView($observer) {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'extendedbundle') {
            // reset if called
            if (Mage::app()->getRequest()->getParam('reset')) {
                Mage::helper('bundlecreatorplus')->resetPackageSession($product);
            }

            if ($itemId = Mage::app()->getRequest()->getParam('item')) {
                Mage::helper('bundlecreatorplus')->getPackageSession($product)->setActiveItem($itemId);
            }

            if (Mage::app()->getRequest()->getParam('isAjax')) {
                
            }
        }
        return $this;
    }
    
    /**
     * Correct parent items (e.g. Bundle inside Extended Bundle)
     */
    public function setCorrectParentItems($observer) {
        $items = $observer->getItems();
   
        if($items[0]->getProduct()->getTypeId() == 'extendedbundle') {
            foreach($items as $item) {
                if($item->getProduct()->getParentProductId()) {
                    $parentItem = $productIdToItem[$item->getProduct()->getParentProductId()];
                    $item->setParentItem($parentItem);
                }
                $productIdToItem[$item->getProductId()] = $item;
            }
        } 
        
        return $this;
    }
    
    public function updateQtyOrdered($observer) {
        $item = $observer->getEvent()->getItem();
        $orderItem = $observer->getEvent()->getOrderItem(); 
        
        if ($item->getParentItem() && $item->getParentItem()->getParentItem()) { 
            if($item->getParentItem()->getProduct()->getTypeId() == 'bundle' && $item->getParentItem()->getParentItem()->getProduct()->getTypeId() == 'extendedbundle') {
                $orderItem->setQtyOrdered($orderItem->getQtyOrdered() * $item->getParentItem()->getParentItem()->getQty());
            }               
        }
        
        return $this;
    }

}
