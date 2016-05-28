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
 * Reservation siege admin edit tabs
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * Initialize Tabs
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct() {
        parent::__construct();
        $this->setId('reservationsiege_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('reseauchx_reservationreseau')->__('Reservation siege'));
    }
    /**
     * before render html
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml(){
        $this->addTab('form_reservationsiege', array(
            'label'        => Mage::helper('reseauchx_reservationreseau')->__('Reservation siege'),
            'title'        => Mage::helper('reseauchx_reservationreseau')->__('Reservation siege'),
            'content'     => $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_reservationsiege_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
    /**
     * Retrieve reservation siege entity
     * @access public
     * @return ReseauChx_ReservationReseau_Model_Reservationsiege
     * @author Ultimate Module Creator
     */
    public function getReservationsiege(){
        return Mage::registry('current_reservationsiege');
    }
}
