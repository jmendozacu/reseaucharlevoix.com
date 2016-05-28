<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to vdesign.support@outlook.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @copyright  Copyright (c) 2014 VDesign
 * @license    End User License Agreement (EULA)
 */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
->newTable($installer->getTable('bookmepro/book_mailreminder_preparation'))
->addColumn('preparation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true
), 'Prepared email ID')
->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
), 'Product ID')
->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
), 'Order')
->addColumn('reminder_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
), 'Reminder')
->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
		'nullable'  => false
), 'from_date')
->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
		'nullable'  => false
), 'to_date')
->addColumn('itemdata', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'nullable'  => false
), 'data');
		
$installer->getConnection()->createTable($table);



$installer->endSetup();
