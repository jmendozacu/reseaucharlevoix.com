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
class VDesign_BookmePro_Block_Adminhtml_Book_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'item_id';
        $this->_controller = 'adminhtml_book';
        $this->_blockGroup = 'bookmepro';
        
        parent::__construct();
        $this->_addButton('myback', array(
        		'label'     => Mage::helper('bookmepro')->__('back'),
        		'onclick'   => 'setLocation(\'' . $this->getUrl('*/adminhtml_adventure/index' , array('item_id' => $this->getRequest()->getParam('item_id'))) .'\')',
        		'class'     => 'back',
        ), -100);
        
        $this->_removeButton('back');
        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        
        
        $this->_addButton('save', array(
        		'label'     => Mage::helper('bookmepro')->__('Update Reservation'),
        		'onclick'   => "book_editForm.submit()",//'setLocation(\'' . $this->getUrl('*/adminhtml_session/save' , array('item_id' => $this->getRequest()->getParam('item_id'))) .'\')',
        		'class'     => 'save',
        ), -100);
        
        $this->_addButton('delete', array(
        		'label'     => Mage::helper('bookmepro')->__('Delete Reservation'),
        		'onclick'   => "if(confirm('Do you really want to delete this reservation?')) setLocation('" . $this->getUrl('*/adminhtml_book/delete' , array('item_id' => $this->getRequest()->getParam('item_id'))) ."');",
        		'class'     => 'delete',
        ), -100);
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('bookmepro')->__("Check/Edit Reservation");
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return true;
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
//     protected function _prepareLayout()
//     {
    	
//         $tabsBlock = $this->getLayout()->getBlock('bookmepro/survey_edit_tabs');
//         if ($tabsBlock) {
//             $tabsBlockJsObject = $tabsBlock->getJsObjectName();
//             $tabsBlockPrefix   = $tabsBlock->getId() . '_';
//         } else {
//             $tabsBlockJsObject = 'survey_tabsJsTabs';
//             $tabsBlockPrefix   = 'survey_tabs_';
//         }
//         $this->_formScripts[] = "
//             function toggleEditor() {
//                 if (tinyMCE.getInstanceById('survey_content') == null) {
//                     tinyMCE.execCommand('mceAddControl', false, 'survey_content');
//                 } else {
//                     tinyMCE.execCommand('mceRemoveControl', false, 'survey_content');
//                 }
//             }

//             function saveAndContinueEdit(urlTemplate) {
//                 var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
//                 var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
//                 if (tabsIdValue.startsWith(tabsBlockPrefix)) {
//                     tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
//                 }
//                 var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
//                 var url = template.evaluate({tab_id:tabsIdValue});
//                 editForm.submit(url);
//             }
//         ";
//         return parent::_prepareLayout();
//     }
}
