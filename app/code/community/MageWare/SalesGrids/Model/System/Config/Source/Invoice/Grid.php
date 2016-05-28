<?php

class MageWare_SalesGrids_Model_System_Config_Source_Invoice_Grid
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'increment_id', 'label' => Mage::helper('sales')->__('Invoice #')),
            array('value' => 'created_at', 'label' => Mage::helper('sales')->__('Invoice Date')),
            array('value' => 'order_increment_id', 'label' => Mage::helper('sales')->__('Order #')),
            array('value' => 'order_created_at', 'label' => Mage::helper('sales')->__('Order Date')),
            array('value' => 'billing_name', 'label' => Mage::helper('sales')->__('Bill to Name')),
            array('value' => 'customer_email', 'label' => Mage::helper('customer')->__('Email')),
            array('value' => 'product_sku', 'label' => Mage::helper('catalog')->__('SKU')),
            array('value' => 'product_name', 'label' => Mage::helper('catalog')->__('Product')),
            array('value' => 'state', 'label' => Mage::helper('sales')->__('Status')),
            array('value' => 'grand_total', 'label' => Mage::helper('customer')->__('Amount')),
            array('value' => 'action', 'label' => Mage::helper('sales')->__('Action')),
        );
    }
}
