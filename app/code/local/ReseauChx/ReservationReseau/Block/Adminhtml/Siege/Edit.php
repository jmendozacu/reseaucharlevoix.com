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
 * Siege admin edit form
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Block_Adminhtml_Siege_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'reseauchx_reservationreseau';
        $this->_controller = 'adminhtml_siege';
        $this->_updateButton('save', 'label', Mage::helper('reseauchx_reservationreseau')->__('Save Siege'));
        $this->_updateButton('delete', 'label', Mage::helper('reseauchx_reservationreseau')->__('Delete Siege'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('reseauchx_reservationreseau')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    /**
     * get the edit form header
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText(){
        if( Mage::registry('current_siege') && Mage::registry('current_siege')->getId() ) {
            return Mage::helper('reseauchx_reservationreseau')->__("Edit Siege '%s'", $this->escapeHtml(Mage::registry('current_siege')->getCode()));
        }
        else {
            return Mage::helper('reseauchx_reservationreseau')->__('Add Siege');
        }
    }
}
