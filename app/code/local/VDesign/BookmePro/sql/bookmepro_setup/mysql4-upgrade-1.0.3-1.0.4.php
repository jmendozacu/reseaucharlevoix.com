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

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session'),
		'day_start',
		'int'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session'),
		'day_end',
		'int'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session'),
		'specific_date_start',
		'date'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session'),
		'specific_date_end',
		'date'
);

$sessions = Mage::getModel('bookme/book_session')->getCollection();

foreach ($sessions as $session)
{
	$session->setData('day_start', $session->getSessionDay());
	$session->setData('day_end', $session->getSessionDay());
	$session->setData('specific_date_start', $session->getSpecDay());
	$session->setData('specific_date_end', $session->getSpecDay());
	$session->save();
}




$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session_time'),
		'start_hour',
		'int'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session_time'),
		'end_hour',
		'int'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session_time'),
		'start_minute',
		'int'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session_time'),
		'end_minute',
		'int'
);

$installer->getConnection()->addColumn(
		$this->getTable('bookme/book_session_time'),
		'qty',
		'int'
);


$times = Mage::getModel('bookme/book_session_time')->getCollection();

foreach ($times as $time)
{
	$time->setData('start_hour', $time->getHour());
	$time->setData('end_hour', $time->getHour());
	$time->setData('start_minute', $time->getMinute());
	$time->setData('end_minute', $time->getMinute());
	$time->save();
}


$installer->endSetup();
