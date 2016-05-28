<?php
/**
 * dotsquares.com
 *
 * Dotsquares_Taxrate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Dotsquares
 * @package		Dotsquares_Taxrate
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @author      Jagdish Ram <jagdish.ram@dotsquares.com>
 */
class Dotsquares_Taxrate_Block_Adminhtml_Taxrate_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * constructor
	 * @access public
	 * @return void	 
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('taxrateGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	/**
	 * prepare collection
	 * @access protected
	 * @return Dotsquares_Taxrate_Block_Adminhtml_Taxrate_Grid	 
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('taxrate/taxrate')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	/**
	 * prepare grid collection
	 * @access protected
	 * @return Dotsquares_Taxrate_Block_Adminhtml_Taxrate_Grid	 
	 */
	protected function _prepareColumns(){
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('taxrate')->__('Id'),
			'index'		=> 'entity_id',
			'type'		=> 'number'
		));
		$this->addColumn('taxrate_title', array(
			'header'=> Mage::helper('taxrate')->__('Tax Rate Title'),
			'index' => 'taxrate_title',
			'type'	 	=> 'text',

		));
		$this->addColumn('tax_country_id', array(
			'header'=> Mage::helper('taxrate')->__('Tax Rate Country'),
			'index' => 'tax_country_id',
			'type'	 	=> 'text',

		));
		$this->addColumn('tax_rate', array(
			'header'=> Mage::helper('taxrate')->__('Tax Rate'),
			'index' => 'tax_rate',
			'type'	 	=> 'text',

		));
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('taxrate')->__('Created at'),
			'index' 	=> 'created_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('updated_at', array(
			'header'	=> Mage::helper('taxrate')->__('Updated at'),
			'index' 	=> 'updated_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('action',
			array(
				'header'=>  Mage::helper('taxrate')->__('Action'),
				'width' => '100',
				'type'  => 'action',
				'getter'=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('taxrate')->__('Edit'),
						'url'   => array('base'=> '*/*/edit'),
						'field' => 'id'
					)
				),
				'filter'=> false,
				'is_system'	=> true,
				'sortable'  => false,
		));
		$this->addExportType('*/*/exportCsv', Mage::helper('taxrate')->__('CSV'));
		$this->addExportType('*/*/exportExcel', Mage::helper('taxrate')->__('Excel'));
		$this->addExportType('*/*/exportXml', Mage::helper('taxrate')->__('XML'));
		return parent::_prepareColumns();
	}
	/**
	 * prepare mass action
	 * @access protected
	 * @return Dotsquares_Taxrate_Block_Adminhtml_Taxrate_Grid	 
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('taxrate');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('taxrate')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('taxrate')->__('Are you sure?')
		));
		return $this;
	}
	/**
	 * get the row url
	 * @access public
	 * @param Dotsquares_Taxrate_Model_Taxrate
	 * @return string	 
	 */
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	/**
	 * get the grid url
	 * @access public
	 * @return string	 
	 */
	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
}