<?php

class MageWare_SalesGrids_Model_System_Config_Source_Shipment_Grid
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'increment_id', 'label' => Mage::helper('sales')->__('Shipment #')),
            array('value' => 'created_at', 'label' => Mage::helper('sales')->__('Date Shipped')),
            array('value' => 'order_increment_id', 'label' => Mage::helper('sales')->__('Order #')),
            array('value' => 'order_created_at', 'label' => Mage::helper('sales')->__('Order Date')),
            array('value' => 'shipping_name', 'label' => Mage::helper('sales')->__('Ship to Name')),
            array('value' => 'customer_email', 'label' => Mage::helper('customer')->__('Email')),
            array('value' => 'product_sku', 'label' => Mage::helper('catalog')->__('SKU')),
            array('value' => 'product_name', 'label' => Mage::helper('catalog')->__('Product')),
            array('value' => 'total_qty', 'label' => Mage::helper('sales')->__('Total Qty')),
            array('value' => 'action', 'label' => Mage::helper('sales')->__('Action')),
        );
    }
}
