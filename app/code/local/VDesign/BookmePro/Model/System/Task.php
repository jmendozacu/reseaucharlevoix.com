<?php


class VDesign_BookmePro_Model_System_Task
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
				array('value' => 1, 'label'=>Mage::helper('bookmepro')->__('1 hour')),
				array('value' => 2, 'label'=>Mage::helper('bookmepro')->__('2 hours')),
				array('value' => 3, 'label'=>Mage::helper('bookmepro')->__('6 hours')),
				array('value' => 4, 'label'=>Mage::helper('bookmepro')->__('12 hours')),
				array('value' => 5, 'label'=>Mage::helper('bookmepro')->__('1 day')),
				array('value' => 6, 'label'=>Mage::helper('bookmepro')->__('2 days')),
				array('value' => 7, 'label'=>Mage::helper('bookmepro')->__('4 days'))
		);
	}
	
	
} 