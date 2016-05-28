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
/*
$table = $installer->getConnection()
->newTable($installer->getTable('bookmepro/priceprofile'))
->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
), 'Price rule ID')
->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
), 'Product ID')
->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Name')
->addColumn('move', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Move')
->addColumn('amount_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Type')
->addColumn('amount', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(), 'Amount');
$installer->getConnection()->createTable($table);*/

$installer->addAttribute('catalog_product', 'price_profile', array(
		'group' => 'Book Setup',
		'input' => 'text',
		'type' => 'decimal',
		'label' => 'Price Profiles',
		'backend' => 'bookmepro/product_attribute_backend_priceprofile',
		'frontend' => '',
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'searchable' => 0,
		'filterable' => 0,
		'comparable' => 0,
		'visible_on_front' => 0,
		'visible_in_advanced_search' => 0,
		'is_html_allowed_on_front' => 0,
		'apply_to' => 'booking',
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();
