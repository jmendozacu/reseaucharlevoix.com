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
 * Reservation siege edit form tab
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return ReseauChx_ReservationReseau_Block_Adminhtml_Reservationsiege_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('reservationsiege_');
        $form->setFieldNameSuffix('reservationsiege');
        $this->setForm($form);
        $fieldset = $form->addFieldset('reservationsiege_form', array('legend'=>Mage::helper('reseauchx_reservationreseau')->__('Reservation siege')));
        $values = Mage::getResourceModel('reseauchx_reservationreseau/salle_collection')->toOptionArray();
        array_unshift($values, array('label'=>'', 'value'=>''));

        $html = '<a href="{#url}" id="reservationsiege_salle_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeSalleIdLink(){
                if ($(\'reservationsiege_salle_id\').value == \'\') {
                    $(\'reservationsiege_salle_id_link\').hide();
                }
                else {
                    $(\'reservationsiege_salle_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/reservationreseau_salle/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'reservationsiege_salle_id\').value);
                    $(\'reservationsiege_salle_id_link\').href = realUrl;
                    $(\'reservationsiege_salle_id_link\').innerHTML = text.replace(\'{#name}\', $(\'reservationsiege_salle_id\').options[$(\'reservationsiege_salle_id\').selectedIndex].innerHTML);
                }
            }
            $(\'reservationsiege_salle_id\').observe(\'change\', changeSalleIdLink);
            changeSalleIdLink();
            </script>';

        $fieldset->addField('salle_id', 'select', array(
            'label'     => Mage::helper('reseauchx_reservationreseau')->__('Salle'),
            'name'      => 'salle_id',
            'required'  => false,
            'values'    => $values,
            'after_element_html' => $html
        ));
        $values = Mage::getResourceModel('reseauchx_reservationreseau/siege_collection')->toOptionArray();
        array_unshift($values, array('label'=>'', 'value'=>''));

        $html = '<a href="{#url}" id="reservationsiege_siege_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeSiegeIdLink(){
                if ($(\'reservationsiege_siege_id\').value == \'\') {
                    $(\'reservationsiege_siege_id_link\').hide();
                }
                else {
                    $(\'reservationsiege_siege_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/reservationreseau_siege/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'reservationsiege_siege_id\').value);
                    $(\'reservationsiege_siege_id_link\').href = realUrl;
                    $(\'reservationsiege_siege_id_link\').innerHTML = text.replace(\'{#name}\', $(\'reservationsiege_siege_id\').options[$(\'reservationsiege_siege_id\').selectedIndex].innerHTML);
                }
            }
            $(\'reservationsiege_siege_id\').observe(\'change\', changeSiegeIdLink);
            changeSiegeIdLink();
            </script>';

        $fieldset->addField('siege_id', 'select', array(
            'label'     => Mage::helper('reseauchx_reservationreseau')->__('Siege'),
            'name'      => 'siege_id',
            'required'  => false,
            'values'    => $values,
            'after_element_html' => $html
        ));
		//Eric: datetime au lieur de date
        $fieldset->addField('dateheuredebut', 'datetime', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Debut'),
            'name'  => 'dateheuredebut',
            'required'  => true,
            'class' => 'required-entry',

            'image' => $this->getSkinUrl('images/grid-cal.gif'),
        		//Eric: datetime au lieur de date
            'format' => "yyy-M-dd H:mm",
        ));
        //Eric: datetime au lieur de date
        $fieldset->addField('dateheurefin', 'datetime', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Fin'),
            'name'  => 'dateheurefin',
            'required'  => true,
            'class' => 'required-entry',

            'image' => $this->getSkinUrl('images/grid-cal.gif'),
        	//Eric: datetime au lieur de date
            'format' => "yyy-M-dd H:mm",
        ));

        $fieldset->addField('confirme', 'select', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Confirme'),
            'name'  => 'confirme',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('reseauchx_reservationreseau')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('reseauchx_reservationreseau')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('idquote', 'text', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('idQuote'),
            'name'  => 'idquote',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('idorder', 'text', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('idOrder'),
            'name'  => 'idorder',
			'required'  => false,

        ));

        $fieldset->addField('book_id', 'text', array(
            'label' => Mage::helper('reseauchx_reservationreseau')->__('Book Id'),
            'name'  => 'book_id',
			'required'  => false,

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
        $formValues = Mage::registry('current_reservationsiege')->getDefaultValues();
        if (!is_array($formValues)){
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getReservationsiegeData()){
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getReservationsiegeData());
            Mage::getSingleton('adminhtml/session')->setReservationsiegeData(null);
        }
        elseif (Mage::registry('current_reservationsiege')){
            $formValues = array_merge($formValues, Mage::registry('current_reservationsiege')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
