<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()
          ->addColumn($installer->getTable('sales/order_grid'), 'customer_email', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Customer Email',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/order_grid'), 'customer_is_guest', array(
              'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
              'comment'  => 'Customer Is Guest',
              'unsigned' => true
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/order_grid'), 'customer_group_id', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_SMALLINT,
              'comment' => 'Customer Group Id'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/order_grid'), 'product_sku', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Product Sku',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/order_grid'), 'product_name', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Product Name',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/order_grid'), 'payment_method', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Payment Method',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/order_grid'),
                     $installer->getIdxName('sales/order_grid', array('customer_email')),
                     array('customer_email'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/order_grid'),
                     $installer->getIdxName('sales/order_grid', array('customer_is_guest')),
                     array('customer_is_guest'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/order_grid'),
                     $installer->getIdxName('sales/order_grid', array('customer_group_id')),
                     array('customer_group_id'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/order_grid'),
                     $installer->getIdxName('sales/order_grid', array('product_sku')),
                     array('product_sku'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/order_grid'),
                     $installer->getIdxName('sales/order_grid', array('product_name')),
                     array('product_name'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/order_grid'),
                     $installer->getIdxName('sales/order_grid', array('payment_method')),
                     array('payment_method'));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/invoice_grid'), 'customer_email', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Customer Email',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/invoice_grid'), 'product_sku', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Product Sku',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/invoice_grid'), 'product_name', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Product Name',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/invoice_grid'),
                     $installer->getIdxName('sales/invoice_grid', array('customer_email')),
                     array('customer_email'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/invoice_grid'),
                     $installer->getIdxName('sales/invoice_grid', array('product_sku')),
                     array('product_sku'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/invoice_grid'),
                     $installer->getIdxName('sales/invoice_grid', array('product_name')),
                     array('product_name'));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/shipment_grid'), 'customer_email', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Customer Email',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/shipment_grid'), 'product_sku', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Product Sku',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/shipment_grid'), 'product_name', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Product Name',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/shipment_grid'),
                     $installer->getIdxName('sales/shipment_grid', array('customer_email')),
                     array('customer_email'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/shipment_grid'),
                     $installer->getIdxName('sales/shipment_grid', array('product_sku')),
                     array('product_sku'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/shipment_grid'),
                     $installer->getIdxName('sales/shipment_grid', array('product_name')),
                     array('product_name'));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/creditmemo_grid'), 'customer_email', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Customer Email',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/creditmemo_grid'), 'product_sku', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Product Sku',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addColumn($installer->getTable('sales/creditmemo_grid'), 'product_name', array(
              'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'Product Name',
              'length'  => '255'
          ));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/creditmemo_grid'),
                     $installer->getIdxName('sales/creditmemo_grid', array('customer_email')),
                     array('customer_email'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/creditmemo_grid'),
                     $installer->getIdxName('sales/creditmemo_grid', array('product_sku')),
                     array('product_sku'));

$installer->getConnection()
          ->addIndex($installer->getTable('sales/creditmemo_grid'),
                     $installer->getIdxName('sales/creditmemo_grid', array('product_name')),
                     array('product_name'));

$installer->endSetup();
