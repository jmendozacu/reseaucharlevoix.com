<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Sales_Order_Email_Items_Order_Package extends Mage_Sales_Block_Order_Email_Items_Order_Default {

    public function getChildren() {
        $children = array(); 
        $orderItem = $this->getOrderItem();                                           
        $cartItems = $orderItem->getOrder()->getItemsCollection();
        foreach ($cartItems as $item) { 
            if ($item->getParentItemId() == $orderItem->getId())
                $children[] = $item;
        }
        return $children;
    }
    
    public function getOrderItem()
    {
        if ($this->getItem() instanceof Mage_Sales_Model_Order_Item) {
            return $this->getItem();
        } else {
            return $this->getItem()->getOrderItem();
        }
    }

}
