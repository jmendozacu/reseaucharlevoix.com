<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Adminhtml_Sales_Order_View_Items_Renderer extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default {

    public function getChildren() {
        $children = array();
        $cartItems = $this->getItem()->getOrder()->getItemsCollection();
        foreach ($cartItems as $item) {
            if ($item->getParentItemId() == $this->getItem()->getId())
                $children[] = $item;
        }
        return $children;
    }

}
