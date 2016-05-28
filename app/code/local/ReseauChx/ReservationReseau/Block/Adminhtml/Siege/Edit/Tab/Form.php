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
 * Siege edit form tab
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Block_Adminhtml_Siege_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Siege_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('siege_');
        $form->setFieldNameSuffix('siege');
        $this->setForm($form);
        $fieldset = $form->addFieldset('siege_form', array('legend'=>Mage::helper('reseauchx_reservationreseau')->__('Siege')));
        $values = Mage::getResourceModel('reseauchx_reservationreseau/salle_collection')->toOptionArray();
        array_unshift($values, array('label'=>'', 'value'=>''));

        $html = '<a href="{#url}" id="siege_salle_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeSalleIdLink(){
                if ($(\'siege_salle_id\').value == \'\') {
                    $(\'siege_salle_id_link\').hide();
                }
                else {
                    $(\'siege_salle_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/reservationreseau_salle/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'siege_salle_id\').value);
                    $(\'siege_salle_id_link\').href = realUrl;
                    $(\'siege_salle_id_link\').innerHTML = text.replace(\'{#name}\', $(\'siege_salle_id\').options[$(\'siege_salle_id\').selectedIndex].innerHTML);
                }
            }
            $(\'siege_salle_id\').observe(\'change\', changeSalleIdLink);
            changeSalleIdLink();
            </script>';

        $fieldset->addField('salle_id', 'select', array(
            'label'     => Mage::helper('reseauchx_reservationreseau')->__('Salle'),
            'name'      => 'salle_id',
            'required'  => false,
            'values'    => $values,
            'after_element_html' => $html
        ));

        $fieldset->addField('code', 'text', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Code'),
            'name'  => 'code',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('categorie', 'select', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Categorie'),
            'name'  => 'categorie',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> Mage::getModel('reseauchx_reservationreseau/siege_attribute_source_categorie')->getAllOptions(true),
        ));

        $fieldset->addField('posx', 'text', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('X'),
            'name'  => 'posx',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('posy', 'text', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Y'),
            'name'  => 'posy',
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
        $formValues = Mage::registry('current_siege')->getDefaultValues();
        if (!is_array($formValues)){
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getSiegeData()){
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getSiegeData());
            Mage::getSingleton('adminhtml/session')->setSiegeData(null);
        }
        elseif (Mage::registry('current_siege')){
            $formValues = array_merge($formValues, Mage::registry('current_siege')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
