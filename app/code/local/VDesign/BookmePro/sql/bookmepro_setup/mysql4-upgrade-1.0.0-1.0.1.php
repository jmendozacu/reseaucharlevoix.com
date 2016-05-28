// <?php
// /**
//  * Magento
//  *
//  * NOTICE OF LICENSE
//  *
//  * This source file is subject to the End User License Agreement (EULA)
//  * that is bundled with this package in the file LICENSE.txt.
//  * If you did not receive a copy of the license and are unable to
//  * obtain it through the world-wide-web, please send an email
//  * to vdesign.support@outlook.com so we can send you a copy immediately.
//  *
//  * @category   Mage
//  * @copyright  Copyright (c) 2014 VDesign
//  * @license    End User License Agreement (EULA)
//  */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_item'),
		'book_type',
		'text'
);

$installer->getConnection()->addColumn(
			$this->getTable('bookme/book_item'),
			'from_date',
			'date'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_item'),
		'to_date',
		'date'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_item'),
		'from_time',
		'time'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_item'),
		'to_time',
		'time'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_item'),
		'session_id',
		'int'
);


//add adventure attribute option
$arg_attribute = 'billable_period';

$attr_model = Mage::getModel('catalog/resource_eav_attribute');
$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
$attr_id = $attr->getAttributeId();

$option['attribute_id'] = $attr_id;
$option['value']['adventure'][0] = 'Adventure';

$installer->addAttributeOption($option);



$book_item = Mage::getModel('bookme/book_item')->getCollection();

foreach ($book_item as $item)
{
	$product = Mage::getModel('catalog/product')->load($item->getProductId());
	
	$item->setBookType($product->getAttributeText('billable_period'));
	$item->setFromDate(date('Y-m-d', strtotime($item->getBookedFrom())));
	$item->setFromTime(date('H:i:s', strtotime($item->getBookedFrom())));
	$item->setToDate(date('Y-m-d', strtotime($item->getBookedTo())));
	$item->setToTime(date('H:i:s', strtotime($item->getBookedTo())));
	
	$item->save();
}

 $installer->endSetup();
