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

$installer->addAttribute('catalog_product', 'refresh_days', array(
		'group' => 'Book Setup',
		'input' => 'text',
		'type' => 'int',
		'label' => 'Refresh days',
		'source' => 'eav/entity_attribute_source_table',
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
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
));

$table = $installer->getTable('bookme/book_excludeday');

$installer->run("ALTER TABLE $table ADD refresh_day ".Varien_Db_Ddl_Table::TYPE_BOOLEAN);


$installer->endSetup();