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

$installer->run(
		"ALTER TABLE ".$installer->getTable('bookmepro/session')." MODIFY `time_from` TIME;"
);

$installer->run(
		"ALTER TABLE ".$installer->getTable('bookmepro/session')." MODIFY `time_to` TIME;"
);

$table = $installer->getConnection()
->newTable($installer->getTable('bookmepro/priceprofile_store'))
->addColumn('trans_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
), 'Translation ID')
->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
), 'Profile ID')
->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Name')
->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Code');
$installer->getConnection()->createTable($table);



$products = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('type_id', array('eq' => 'booking'));
$helper = Mage::helper('bookmepro/session');

foreach ($products as $product)
{
	$helper->generateSessions($product, false);
}


$installer->endSetup();
