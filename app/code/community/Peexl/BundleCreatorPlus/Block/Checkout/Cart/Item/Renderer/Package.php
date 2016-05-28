<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Checkout_Cart_Item_Renderer_Package extends Mage_Checkout_Block_Cart_Item_Renderer {

    public function getChildren() {
        $children = array();
        $cartItems = $this->getItem()->getQuote()->getItemsCollection();
        foreach ($cartItems as $item) {
            if ($item->getParentItemId() == $this->getItem()->getId())
                $children[] = $item;
        }
        return $children;
    }

}
