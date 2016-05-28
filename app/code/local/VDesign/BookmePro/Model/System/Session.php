<?php


class VDesign_BookmePro_Model_System_Session
{

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
				array('value' => 0, 'label'=>Mage::helper('bookmepro')->__('-- please select --')),
				array('value' => 1, 'label'=>Mage::helper('bookmepro')->__('every day')),
				array('value' => 2, 'label'=>Mage::helper('bookmepro')->__('once a week')),
				array('value' => 3, 'label'=>Mage::helper('bookmepro')->__('every 14. days')),
				array('value' => 4, 'label'=>Mage::helper('bookmepro')->__('once a month'))
		);
	}
	
	
} 