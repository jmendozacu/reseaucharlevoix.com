<?php

$installer = $this;

$flatColumns = array_keys($installer->getConnection()->describeTable($installer->getTable('sales/order')));
$gridColumns = array_keys($installer->getConnection()->describeTable($installer->getTable('sales/order_grid')));

$flatColumnsToSelect = array_intersect($flatColumns, $gridColumns);

$select = $installer->getConnection()->select()
    ->from($installer->getTable('sales/order'), $flatColumnsToSelect)
    ->joinLeft(
        $installer->getTable('sales/order_payment'),
        $installer->getTable('sales/order').'.entity_id='.$installer->getTable('sales/order_payment').'.parent_id',
        array('payment_method' => $installer->getTable('sales/order_payment').'.method')
    )
    ->joinLeft(
        $installer->getTable('sales/order_item'),
        $installer->getTable('sales/order').'.entity_id='.$installer->getTable('sales/order_item').'.order_id',
        array(
            'product_sku'  => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '.$installer->getTable('sales/order_item').'.sku ORDER BY '.$installer->getTable('sales/order_item').'.sku ASC SEPARATOR "\n")'),
            'product_name' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '.$installer->getTable('sales/order_item').'.name ORDER BY '.$installer->getTable('sales/order_item').'.name ASC SEPARATOR "\n")')
        )
    )
    ->group($installer->getTable('sales/order').'.entity_id');

$flatColumnsToSelect[] = 'payment_method';
$flatColumnsToSelect[] = 'product_sku';
$flatColumnsToSelect[] = 'product_name';

$installer->getConnection()->query($select->insertFromSelect($installer->getTable('sales/order_grid'), $flatColumnsToSelect, true));

$flatColumns = array_keys($installer->getConnection()->describeTable($installer->getTable('sales/invoice')));
$gridColumns = array_keys($installer->getConnection()->describeTable($installer->getTable('sales/invoice_grid')));

$flatColumnsToSelect = array_intersect($flatColumns, $gridColumns);

$select = $installer->getConnection()->select()
    ->from($installer->getTable('sales/invoice'), $flatColumnsToSelect)
    ->joinLeft(
        $installer->getTable('sales/order'),
        $installer->getTable('sales/invoice').'.order_id='.$installer->getTable('sales/order').'.entity_id',
        array('customer_email' => $installer->getTable('sales/order').'.customer_email')
    )
    ->joinLeft(
        $installer->getTable('sales/invoice_item'),
        $installer->getTable('sales/invoice').'.entity_id='.$installer->getTable('sales/invoice_item').'.parent_id',
        array(
            'product_sku'  => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '.$installer->getTable('sales/invoice_item').'.sku ORDER BY '.$installer->getTable('sales/invoice_item').'.sku ASC SEPARATOR "\n")'),
            'product_name' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '.$installer->getTable('sales/invoice_item').'.name ORDER BY '.$installer->getTable('sales/invoice_item').'.name ASC SEPARATOR "\n")')
        )
    )
    ->group($installer->getTable('sales/invoice').'.entity_id');

$flatColumnsToSelect[] = 'customer_email';
$flatColumnsToSelect[] = 'product_sku';
$flatColumnsToSelect[] = 'product_name';

$installer->getConnection()->query($select->insertFromSelect($installer->getTable('sales/invoice_grid'), $flatColumnsToSelect, true));

$flatColumns = array_keys($installer->getConnection()->describeTable($installer->getTable('sales/shipment')));
$gridColumns = array_keys($installer->getConnection()->describeTable($installer->getTable('sales/shipment_grid')));

$flatColumnsToSelect = array_intersect($flatColumns, $gridColumns);

$select = $installer->getConnection()->select()
    ->from($installer->getTable('sales/shipment'), $flatColumnsToSelect)
    ->joinLeft(
        $installer->getTable('sales/order'),
        $installer->getTable('sales/shipment').'.order_id='.$installer->getTable('sales/order').'.entity_id',
        array('customer_email' => $installer->getTable('sales/order').'.customer_email')
    )
    ->joinLeft(
        $installer->getTable('sales/shipment_item'),
        $installer->getTable('sales/shipment').'.entity_id='.$installer->getTable('sales/shipment_item').'.parent_id',
        array(
            'product_sku'  => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '.$installer->getTable('sales/shipment_item').'.sku ORDER BY '.$installer->getTable('sales/shipment_item').'.sku ASC SEPARATOR "\n")'),
            'product_name' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '.$installer->getTable('sales/shipment_item').'.name ORDER BY '.$installer->getTable('sales/shipment_item').'.name ASC SEPARATOR "\n")')
        )
    )
    ->group($installer->getTable('sales/shipment').'.entity_id');

$flatColumnsToSelect[] = 'customer_email';
$flatColumnsToSelect[] = 'product_sku';
$flatColumnsToSelect[] = 'product_name';

$installer->getConnection()->query($select->insertFromSelect($installer->getTable('sales/shipment_grid'), $flatColumnsToSelect, true));

$flatColumns = array_keys($installer->getConnection()->describeTable($installer->getTable('sales/creditmemo')));
$gridColumns = array_keys($installer->getConnection()->describeTable($installer->getTable('sales/creditmemo_grid')));

$flatColumnsToSelect = array_intersect($flatColumns, $gridColumns);

$select = $installer->getConnection()->select()
    ->from($installer->getTable('sales/creditmemo'), $flatColumnsToSelect)
    ->joinLeft(
        $installer->getTable('sales/order'),
        $installer->getTable('sales/creditmemo').'.order_id='.$installer->getTable('sales/order').'.entity_id',
        array('customer_email' => $installer->getTable('sales/order').'.customer_email')
    )
    ->joinLeft(
        $installer->getTable('sales/creditmemo_item'),
        $installer->getTable('sales/creditmemo').'.entity_id='.$installer->getTable('sales/creditmemo_item').'.parent_id',
        array(
            'product_sku'  => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '.$installer->getTable('sales/creditmemo_item').'.sku ORDER BY '.$installer->getTable('sales/creditmemo_item').'.sku ASC SEPARATOR "\n")'),
            'product_name' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT '.$installer->getTable('sales/creditmemo_item').'.name ORDER BY '.$installer->getTable('sales/creditmemo_item').'.name ASC SEPARATOR "\n")')
        )
    )
    ->group($installer->getTable('sales/creditmemo').'.entity_id');

$flatColumnsToSelect[] = 'customer_email';
$flatColumnsToSelect[] = 'product_sku';
$flatColumnsToSelect[] = 'product_name';

$installer->getConnection()->query($select->insertFromSelect($installer->getTable('sales/creditmemo_grid'), $flatColumnsToSelect, true));
