<?php

class MageWare_SalesGrids_Model_System_Config_Source_Order_Grid
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'increment_id', 'label' => Mage::helper('sales')->__('Order #')),
            array('value' => 'store_id', 'label' => Mage::helper('sales')->__('Purchased From (Store)')),
            array('value' => 'created_at', 'label' => Mage::helper('sales')->__('Purchased On')),
            array('value' => 'billing_name', 'label' => Mage::helper('sales')->__('Bill to Name')),
            array('value' => 'shipping_name', 'label' => Mage::helper('sales')->__('Ship to Name')),
            array('value' => 'customer_email', 'label' => Mage::helper('customer')->__('Email')),
            array('value' => 'customer_is_guest', 'label' => Mage::helper('customer')->__('Guest')),
            array('value' => 'customer_group_id', 'label' => Mage::helper('customer')->__('Customer Group')),
            array('value' => 'product_sku', 'label' => Mage::helper('catalog')->__('SKU')),
            array('value' => 'product_name', 'label' => Mage::helper('catalog')->__('Product')),
            array('value' => 'payment_method', 'label' => Mage::helper('payment')->__('Payment Method')),
            array('value' => 'base_grand_total', 'label' => Mage::helper('sales')->__('G.T. (Base)')),
            array('value' => 'grand_total', 'label' => Mage::helper('sales')->__('G.T. (Purchased)')),
            array('value' => 'status', 'label' => Mage::helper('sales')->__('Status')),
            array('value' => 'action', 'label' => Mage::helper('sales')->__('Action')),
        );
    }
}
