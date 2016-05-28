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

class VDesign_BookmePro_Block_Adminhtml_Adventure_Edit_Tabs_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
    	
        $model = Mage::registry('bookme_session');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('bookmepro')->__('Session Reservation')));

//         if ($model->getId()) {
//             $fieldset->addField('survey_id', 'hidden', array(
//                 'name' => 'survey_id',
//             ));
//         }

        $fieldset->addField('customer_name', 'text', array(
        		'name'      => 'customer_name',
        		'label'     => Mage::helper('bookmepro')->__('Customer Name'),
        		'title'     => Mage::helper('bookmepro')->__('Customer Name'),
        		'disabled'  => true
        ));
        
        $fieldset->addField('order_number', 'text', array(
        		'name'      => 'order_number',
        		'label'     => Mage::helper('bookmepro')->__('Order Number'),
        		'title'     => Mage::helper('bookmepro')->__('Order Number'),
        		'disabled'  => true
        ));
        
        $fieldset->addField('status', 'text', array(
        		'name'      => 'status',
        		'label'     => Mage::helper('bookmepro')->__('Order Status'),
        		'title'     => Mage::helper('bookmepro')->__('Order Status'),
        		'disabled'  => true
        ));
        
        $fieldset->addField('created_at', 'text', array(
        		'name'      => 'created_at',
        		'label'     => Mage::helper('bookmepro')->__('Reserved At'),
        		'title'     => Mage::helper('bookmepro')->__('Reserved At'),
        		'disabled'  => true
        ));
        
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('session_id', 'select', array(
        		'label'     => Mage::helper('bookmepro')->__('Session Time'),
        		'title'     => Mage::helper('bookmepro')->__('Session Time'),
        		'name'      => 'session_id',
        		'required'  => true,
        		'options'   => Mage::helper('bookmepro')->getAvailableSessionList($model),
        		'disabled'  => $isElementDisabled,
        ));
        
        $fieldset->addField('qty', 'text', array(
        		'name'      => 'qty',
        		'label'     => Mage::helper('bookmepro')->__('Quantity'),
        		'title'     => Mage::helper('bookmepro')->__('Quantity'),
        		'disabled'  => true
        ));
        
        $fieldset->addField('old_session_id', 'hidden', array(
        		'label' => Mage::helper('bookmepro')->__('Old Id'),
				'class' => 'required-entry',
				'required' => true,
        		'name' => 'old_session_id'
        ));


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
    	$model = Mage::registry('bookme_session');
        return Mage::helper('bookmepro')->__('Reservation of %s on %s', $model->getData('product'), $model->getData('booked_from'));
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        $model = Mage::registry('bookme_session');
        return Mage::helper('bookmepro')->__('Reservation of %s on %s', $model->getData('product'), $model->getData('booked_from'));
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
