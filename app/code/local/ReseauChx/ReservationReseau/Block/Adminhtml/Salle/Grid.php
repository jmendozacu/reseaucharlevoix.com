<?php
/**
 * ReseauChx_ReservationReseau extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       ReseauChx
 * @package        ReseauChx_ReservationReseau
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Salle admin grid block
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Block_Adminhtml_Salle_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * constructor
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct(){
        parent::__construct();
        $this->setId('salleGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    /**
     * prepare collection
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Salle_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection(){
        $collection = Mage::getModel('reseauchx_reservationreseau/salle')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * prepare grid collection
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Salle_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns(){
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Id'),
            'index'        => 'entity_id',
            'type'        => 'number'
        ));
        $this->addColumn('nom', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Nom'),
            'align'     => 'left',
            'index'     => 'nom',
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Status'),
            'index'        => 'status',
            'type'        => 'options',
            'options'    => array(
                '1' => Mage::helper('reseauchx_reservationreseau')->__('Enabled'),
                '0' => Mage::helper('reseauchx_reservationreseau')->__('Disabled'),
            )
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Created at'),
            'index'     => 'created_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Updated at'),
            'index'     => 'updated_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));
        $this->addColumn('action',
            array(
                'header'=>  Mage::helper('reseauchx_reservationreseau')->__('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('reseauchx_reservationreseau')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('reseauchx_reservationreseau')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('reseauchx_reservationreseau')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('reseauchx_reservationreseau')->__('XML'));
        return parent::_prepareColumns();
    }
    /**
     * prepare mass action
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Salle_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction(){
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('salle');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('reseauchx_reservationreseau')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('reseauchx_reservationreseau')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('reseauchx_reservationreseau')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'status' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('reseauchx_reservationreseau')->__('Status'),
                        'values' => array(
                                '1' => Mage::helper('reseauchx_reservationreseau')->__('Enabled'),
                                '0' => Mage::helper('reseauchx_reservationreseau')->__('Disabled'),
                        )
                )
            )
        ));
        return $this;
    }
    /**
     * get the row url
     * @access public
     * @param ReseauChx_ReservationReseau_Model_Salle
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    /**
     * get the grid url
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    /**
     * after collection load
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Salle_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection(){
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
