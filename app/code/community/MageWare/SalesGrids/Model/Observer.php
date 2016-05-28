<?php

class MageWare_SalesGrids_Model_Observer
{
    public function initOrderGridColumns($observer)
    {
        $resource = $observer->getEvent()->getResource();

        $resource->addVirtualGridColumn(
            'product_sku',
            'sales/order_item',
            array('entity_id' => 'order_id'),
            'GROUP_CONCAT(DISTINCT {{table}}.sku ORDER BY {{table}}.item_id ASC SEPARATOR "\n")'
        );

        $resource->addVirtualGridColumn(
            'product_name',
            'sales/order_item',
            array('entity_id' => 'order_id'),
            'GROUP_CONCAT(DISTINCT {{table}}.name ORDER BY {{table}}.item_id ASC SEPARATOR "\n")'
        );

        $resource->addVirtualGridColumn(
            'payment_method',
            'sales/order_payment',
            array('entity_id' => 'parent_id'),
            '{{table}}.method'
        );
    }

    public function initOrderInvoiceGridColumns($observer)
    {
        $resource = $observer->getEvent()->getResource();

        $resource->addVirtualGridColumn(
            'customer_email',
            'sales/order',
            array('order_id' => 'entity_id'),
            '{{table}}.customer_email'
        );

        $resource->addVirtualGridColumn(
            'product_sku',
            'sales/invoice_item',
            array('entity_id' => 'parent_id'),
            'GROUP_CONCAT(DISTINCT {{table}}.sku ORDER BY {{table}}.entity_id ASC SEPARATOR "\n")'
        );

        $resource->addVirtualGridColumn(
            'product_name',
            'sales/invoice_item',
            array('entity_id' => 'parent_id'),
            'GROUP_CONCAT(DISTINCT {{table}}.name ORDER BY {{table}}.entity_id ASC SEPARATOR "\n")'
        );
    }

    public function initOrderShipmentGridColumns($observer)
    {
        $resource = $observer->getEvent()->getResource();

        $resource->addVirtualGridColumn(
            'customer_email',
            'sales/order',
            array('order_id' => 'entity_id'),
            '{{table}}.customer_email'
        );

        $resource->addVirtualGridColumn(
            'product_sku',
            'sales/shipment_item',
            array('entity_id' => 'parent_id'),
            'GROUP_CONCAT(DISTINCT {{table}}.sku ORDER BY {{table}}.entity_id ASC SEPARATOR "\n")'
        );

        $resource->addVirtualGridColumn(
            'product_name',
            'sales/shipment_item',
            array('entity_id' => 'parent_id'),
            'GROUP_CONCAT(DISTINCT {{table}}.name ORDER BY {{table}}.entity_id ASC SEPARATOR "\n")'
        );
    }

    public function initOrderCreditmemoGridColumns($observer)
    {
        $resource = $observer->getEvent()->getResource();

        $resource->addVirtualGridColumn(
            'customer_email',
            'sales/order',
            array('order_id' => 'entity_id'),
            '{{table}}.customer_email'
        );

        $resource->addVirtualGridColumn(
            'product_sku',
            'sales/creditmemo_item',
            array('entity_id' => 'parent_id'),
            'GROUP_CONCAT(DISTINCT {{table}}.sku ORDER BY {{table}}.entity_id ASC SEPARATOR "\n")'
        );

        $resource->addVirtualGridColumn(
            'product_name',
            'sales/creditmemo_item',
            array('entity_id' => 'parent_id'),
            'GROUP_CONCAT(DISTINCT {{table}}.name ORDER BY {{table}}.entity_id ASC SEPARATOR "\n")'
        );
    }
}
