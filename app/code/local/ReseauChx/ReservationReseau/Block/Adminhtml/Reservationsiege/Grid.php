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
 * Reservation siege admin grid block
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * constructor
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct(){
        parent::__construct();
        $this->setId('reservationsiegeGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    /**
     * prepare collection
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection(){
    	$collection = Mage::getModel('reseauchx_reservationreseau/reservationsiege')->getCollection();
    	$collection->getSelect()->join('sales_flat_order', 'main_table.idorder = sales_flat_order.entity_id',array('entity_id as order_entity_id','increment_id'));
    	Mage::log('SQL: ' . $collection->getSelect());
        //$collection = Mage::getModel('reseauchx_reservationreseau/reservationsiege')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * prepare grid collection
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns(){
        
    	$this->addColumn('increment_id', array(
    			'header'    => Mage::helper('reseauchx_reservationreseau')->__('Order Id'),
    			'index'        => 'increment_id',
    			
    	));
    	$this->addColumn('entity_id', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Id'),
            'index'        => 'entity_id',
            'type'        => 'number'
        ));
        $this->addColumn('salle_id', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Salle'),
            'index'     => 'salle_id',
            'type'      => 'options',
            'options'   => Mage::getResourceModel('reseauchx_reservationreseau/salle_collection')->toOptionHash(),
            'renderer'  => 'reseauchx_reservationreseau/adminhtml_helper_column_renderer_parent',
            'params' => array(
                'id'=>'getSalleId'
            )/*,
            'base_link' => 'adminhtml/reservationreseau_salle/edit'*/
        ));
        $this->addColumn('siege_id', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Siege'),
            'index'     => 'siege_id',
            'type'      => 'options',
            'options'   => Mage::getResourceModel('reseauchx_reservationreseau/siege_collection')->toOptionHash(),
            'renderer'  => 'reseauchx_reservationreseau/adminhtml_helper_column_renderer_parent',
            'params' => array(
                'id'=>'getSiegeId'
            )/*,
            'base_link' => 'adminhtml/reservationreseau_siege/edit'*/
        ));
        
       /* $this->addColumn('status', array(
            'header'    => Mage::helper('reseauchx_reservationreseau')->__('Status'),
            'index'        => 'status',
            'type'        => 'options',
            'options'    => array(
                '1' => Mage::helper('reseauchx_reservationreseau')->__('Enabled'),
                '0' => Mage::helper('reseauchx_reservationreseau')->__('Disabled'),
            )
        ));*/
        $this->addColumn('dateheuredebut', array(
            'header'=> Mage::helper('reseauchx_reservationreseau')->__('Debut'),
            'index' => 'dateheuredebut',
            'type'=> 'date', //Eric: datetime au lieur de date
        	'format' => "yyy-M-dd H:mm",

        ));
        $this->addColumn('dateheurefin', array(
            'header'=> Mage::helper('reseauchx_reservationreseau')->__('Fin'),
            'index' => 'dateheurefin',
             'type'=> 'date', //Eric: datetime au lieur de date
        	'format' => "yyy-M-dd H:mm",

        ));
        /*
        $this->addColumn('confirme', array(
            'header'=> Mage::helper('reseauchx_reservationreseau')->__('Confirme'),
            'index' => 'confirme',
            'type'    => 'options',
            'options'    => array(
                '1' => Mage::helper('reseauchx_reservationreseau')->__('Yes'),
                '0' => Mage::helper('reseauchx_reservationreseau')->__('No'),
            )

        ));*/
        $this->addColumn('idorder', array(
            'header'=> Mage::helper('reseauchx_reservationreseau')->__('Order'),
            'index' => 'idorder',
            'type'=> 'number',

        ));
        $this->addColumn('idquote', array(
        		'header'    => Mage::helper('reseauchx_reservationreseau')->__('Quote'),
        		'align'     => 'left',
        		'index'     => 'idquote',
        ));
        $this->addColumn('book_id', array(
            'header'=> Mage::helper('reseauchx_reservationreseau')->__('Quote Item'),
            'index' => 'book_id',
            'type'=> 'number',

        ));/*
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
        ));*/
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
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction(){
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('reservationsiege');
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
        $this->getMassactionBlock()->addItem('confirme', array(
            'label'=> Mage::helper('reseauchx_reservationreseau')->__('Change Confirme'),
            'url'  => $this->getUrl('*/*/massConfirme', array('_current'=>true)),
            'additional' => array(
                'flag_confirme' => array(
                        'name' => 'flag_confirme',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('reseauchx_reservationreseau')->__('Confirme'),
                        'values' => array(
                                '1' => Mage::helper('reseauchx_reservationreseau')->__('Yes'),
                                '0' => Mage::helper('reseauchx_reservationreseau')->__('No'),
                            )

                )
            )
        ));
        $values = Mage::getResourceModel('reseauchx_reservationreseau/salle_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem('salle_id', array(
            'label'=> Mage::helper('reseauchx_reservationreseau')->__('Change Salle'),
            'url'  => $this->getUrl('*/*/massSalleId', array('_current'=>true)),
            'additional' => array(
                'flag_salle_id' => array(
                        'name' => 'flag_salle_id',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('reseauchx_reservationreseau')->__('Salle'),
                        'values' => $values
                )
            )
        ));
        $values = Mage::getResourceModel('reseauchx_reservationreseau/siege_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem('siege_id', array(
            'label'=> Mage::helper('reseauchx_reservationreseau')->__('Change Siege'),
            'url'  => $this->getUrl('*/*/massSiegeId', array('_current'=>true)),
            'additional' => array(
                'flag_siege_id' => array(
                        'name' => 'flag_siege_id',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('reseauchx_reservationreseau')->__('Siege'),
                        'values' => $values
                )
            )
        ));
        return $this;
    }
    /**
     * get the row url
     * @access public
     * @param ReseauChx_ReservationReseau_Model_Reservationsiege
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
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection(){
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
