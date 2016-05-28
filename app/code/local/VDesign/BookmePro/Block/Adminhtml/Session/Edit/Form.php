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

class VDesign_BookmePro_Block_Adminhtml_Session_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		
		$form = new Varien_Data_Form(array(
				'id' => 'sessions_editForm',
				'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('item_id'))),
				'method' => 'post',
				'enctype' => 'multipart',
		));
		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}

}