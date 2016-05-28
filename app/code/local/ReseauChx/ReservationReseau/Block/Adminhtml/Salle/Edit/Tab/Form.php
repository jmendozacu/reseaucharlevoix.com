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
 * Salle edit form tab
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Block_Adminhtml_Salle_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Salle_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('salle_');
        $form->setFieldNameSuffix('salle');
        $this->setForm($form);
        $fieldset = $form->addFieldset('salle_form', array('legend'=>Mage::helper('reseauchx_reservationreseau')->__('Salle')));

        $fieldset->addField('nom', 'text', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Nom'),
            'name'  => 'nom',
            'required'  => true,
            'class' => 'required-entry',

        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Status'),
            'name'  => 'status',
            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('reseauchx_reservationreseau')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('reseauchx_reservationreseau')->__('Disabled'),
                ),
            ),
        ));
        $formValues = Mage::registry('current_salle')->getDefaultValues();
        if (!is_array($formValues)){
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getSalleData()){
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getSalleData());
            Mage::getSingleton('adminhtml/session')->setSalleData(null);
        }
        elseif (Mage::registry('current_salle')){
            $formValues = array_merge($formValues, Mage::registry('current_salle')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
