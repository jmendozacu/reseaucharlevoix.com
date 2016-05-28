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
class VDesign_BookmePro_Block_Adminhtml_Sessions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		parent::__construct();
		$this->setId('adminhtml_sessions_grid');
		$this->setDefaultSort('session_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		
		
	}
	protected function _prepareCollection()
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$today = date('Y-m-d');
		$collection = Mage::getModel('bookmepro/session')->getResourceCollection();
		$bookItem = Mage::getSingleton('core/resource')->getTableName('bookme/book_item');
		
		
		$collection
		->joinEavTables()
 		->join(array('p' => 'catalog/product'), 'main_table.product_id = p.entity_id', array(
 						'Sku' => 'sku',
 				))
		->getSelect()
 		->where("main_table.book_type = 'Session' AND main_table.date_from >= '$today'");
		
		$this->setCollection($collection);
		parent::_prepareCollection();
		
		return $this;
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('session_id');
		$this->getMassactionBlock()->setFormFieldName('session');
		$this->getMassactionBlock()->addItem('increase', array(
				'label'=> Mage::helper('bookmepro')->__('Increase capacity'),
				'url'  => $this->getUrl('*/*/increase', array('' => '')),
				'additional' => array(
						'amount' => array(
								'name' => 'amount',
								'type' => 'text',
								'class' => 'required-entry',
								'label' => Mage::helper('catalog')->__('Amount')
						)
				)
		));
	
		$this->getMassactionBlock()->addItem('decrease', array(
				'label'=> Mage::helper('bookmepro')->__('Decrease capacity'),
				'url'  => $this->getUrl('*/*/decrease', array('' => '')),
				'additional' => array(
						'amount' => array(
								'name' => 'amount',
								'type' => 'text',
								'class' => 'required-entry',
								'label' => Mage::helper('catalog')->__('Amount')
						)
				)
		));
	
		$this->getMassactionBlock()->addItem('send_mail', array(
				'label'=> Mage::helper('bookmepro')->__('Send Reminder'),
				'url'  => $this->getUrl('*/*/reminder', array('' => '')),
				'additional' => array(
						'template' => array(
								'name' => 'template',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper('catalog')->__('Mail Template'),
								'values' => Mage::helper('bookmepro')->getMailTemplates()
						)
				)
		));
	
		return $this;
	}
	
	
	protected function _prepareColumns()
	{
		$helper = Mage::helper('bookmepro');
		$currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
		
		$this->addColumn('date_from', array(
				'header' => $helper->__('Booked from date'),
				'type' => 'date',
				'index'  => 'date_from'
		));
		$this->addColumn('time_from', array(
				'header' => $helper->__('Booked from time'),
				'type'  => 'options',
				'index'  => 'time_from',
				'options' => Mage::getSingleton('bookmepro/item_options')->getTimes()
		));
		$this->addColumn('sku', array(
				'header' => $helper->__('Product SKU'),
				'index'  => 'Sku'
		));
		$this->addColumn('name', array(
				'header' => $helper->__('Product Name'),
				'index'  => 'name'
		));
		$this->addColumn('booked_count', array(
				'header' => $helper->__('Booked Quantity'),
				'index'  => 'booked_qty',
				'type'   =>'number'
		));
		$this->addColumn('bookable_qty', array(
				'header' => $helper->__('Available quantity'),
				'index'  => 'max_quantity',
				'type'   =>'number'
		));
		
		$this->addExportType('*/adminhtml_sessions/exportCsv', $helper->__('CSV'));
		$this->addExportType('*/adminhtml_sessions/exportExcel', $helper->__('Excel XML'));
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/adminhtml_session/index', array('session_id' => $row->getSessionId()));
	}
	
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
	
	protected function _addColumnFilterToCollection($column)
	{
		if($this->getCollection() && $column->getFilter()->getValue())
		{
			$value = $column->getFilter()->getValue();
			
			if ($column->getId() == 'sku') {
				$this->getCollection()->addFieldToFilter('p.sku', array('like' => "%$value%"));
				return $this;
			}
	
			if($column->getId() == 'p_name'){
				$this->getCollection()->addFieldToFilter('oi.name', array('like' => "%$value%"));
				return $this;
			}
		}
		return parent::_addColumnFilterToCollection($column);
	}
}