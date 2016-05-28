<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Sales_Order_Item_Renderer_Package extends Mage_Sales_Block_Order_Item_Renderer_Default {

    public function getChildren() {
        $children = array();
        $cartItems = $this->getItem()->getOrder()->getItemsCollection();
        foreach ($cartItems as $item) {
            if ($item->getParentItemId() == $this->getItem()->getId())
                $children[] = $item;
        }
        return $children;
    }
    
    public function getSelectionAttributes($item) {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }
        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }
        return null;
    }

    public function getBundleValueHtml($item)
    {
        if ($attributes = $this->getSelectionAttributes($item)) {
            return sprintf('%d', $attributes['qty']) . ' x ' .
                $this->htmlEscape($item->getName());
        } else {
            return $this->htmlEscape($item->getName());
        }
    }

}
