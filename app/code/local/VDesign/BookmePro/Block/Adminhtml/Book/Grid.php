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
class VDesign_BookmePro_Block_Adminhtml_Book_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		parent::__construct();
		$this->setId('adminhtml_book_grid');
		$this->setDefaultSort('book_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('item_id');
		$this->getMassactionBlock()->setFormFieldName('item');
		
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
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('bookme/book_item')->getResourceCollection();
		$collection
		->addFieldToFilter('book_type', array('eq' => 'Day'))
		->join(array('p' => 'catalog/product'), 'main_table.product_id = p.entity_id', array(
				'Sku' => 'sku',
		))
		->join(array('b' => 'bookme/book'), 'main_table.book_id = b.book_id', array(
			'customer_firstname' => 'customer_firstname',
			'customer_lastname' => 'customer_lastname',
			'customer_email' => 'customer_email',
			'customer_phone' => 'customer_phone',
			'created' => 'created',
			'order_id' => 'order_id'
		))
		->join(array('oi' => 'sales/order_item'), 'oi.item_id = main_table.order_item_id', array(
				'p_name' => 'name'
		))
		->join(array('o' => 'sales/order'), 'b.order_id = o.entity_id', array(
			'status' => 'status',
			'increment_id' => 'increment_id'
		))
		->addExpressionFieldToSelect(
				'fullname',
				'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
				array('customer_firstname' => 'b.customer_firstname', 'customer_lastname' => 'b.customer_lastname'))
		;
		$this->setCollection($collection);
		parent::_prepareCollection();
		
		return $this;
	}
	
	
	protected function _prepareColumns()
	{
		$helper = Mage::helper('bookme');
		$currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
		$this->addColumn('created_on', array(
				'header' => $helper->__('Created On'),
				'type'   => 'datetime',
				'index'  => 'created'
		));
		$this->addColumn('Sku', array(
				'header' => $helper->__('Product Sku'),
				'index'  => 'Sku'
		));
		$this->addColumn('p_name', array(
				'header' => $helper->__('Product Name'),
				'index'  => 'p_name'
		));
		$this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
		$this->addColumn('fullname', array(
				'header'       => $helper->__('Customer Name'),
				'index'        => 'fullname',
				'filter_index' => 'CONCAT(customer_firstname, \' \', customer_lastname)'
		));
		$this->addColumn('email', array(
				'header' => $helper->__('Customer Email'),
				'index'  => 'customer_email'
		));
		$this->addColumn('phone', array(
				'header' => $helper->__('Customer Phone'),
				'index'  => 'customer_phone'
		));
		$this->addColumn('booked_from', array(
				'header' => $helper->__('Booked From'),
				'type'   => 'date',
				'index'  => 'booked_from'
		));
		$this->addColumn('booked_to', array(
				'header' => $helper->__('Booked To'),
				'type'   => 'date',
				'index'  => 'booked_to'
		));
		
		$this->addColumn('quantity', array(
				'header' => $helper->__('Quantity'),
				'width' => '20px',
				'index'  => 'qty'
		));
		if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
			$this->addColumn('action',
					array(
							'header'    => Mage::helper('sales')->__('Action'),
							'width'     => '120px',
							'type'      => 'action',
							'getter'     => 'getItemId',
							'actions'   => array(
									array(
											'caption' => Mage::helper('sales')->__('Edit Reserved Dates'),
											'url'     => array('base' => 'bookmepro/adminhtml_book/edit'),
											'field'   => 'item_id'
									)
							),
							'filter'    => false,
							'sortable'  => false,
							'index'     => 'stores',
							'is_system' => true,
					));
		}
		
		$this->addExportType('*/sales_order/exportCsv', $helper->__('CSV'));
		$this->addExportType('*/sales_order/exportExcel', $helper->__('Excel XML'));
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
			return $this->getUrl('bookme/sales_order/view', array('order_id' => $row->getOrderId()));
		}
		return false;
	}
	
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
}