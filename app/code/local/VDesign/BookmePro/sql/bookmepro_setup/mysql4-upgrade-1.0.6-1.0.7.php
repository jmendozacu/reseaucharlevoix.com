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
->newTable($installer->getTable('bookmepro/session'))
->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true
), 'Prepared email ID')
->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
), 'Product ID')
->addColumn('customsession_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
), 'Session Definition ID')
->addColumn('time_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
), 'Session Time ID')
->addColumn('date_from', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
		'unsigned'  => true,
), 'date from')
->addColumn('time_from', Varien_Db_Ddl_Table::TYPE_TIME, null, array(
		'unsigned'  => true,
), 'time from')
->addColumn('date_to', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
		'nullable'  => false
), 'date to')
->addColumn('time_to', Varien_Db_Ddl_Table::TYPE_TIME, null, array(
		'nullable'  => false
), 'time to')
->addColumn('max_quantity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false
), 'maximum quantity')
->addColumn('booked_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false
), 'reserved quantity')
->addColumn('book_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'nullable'  => false
), 'billable period');
		
$installer->getConnection()->createTable($table);

$installer->endSetup();
