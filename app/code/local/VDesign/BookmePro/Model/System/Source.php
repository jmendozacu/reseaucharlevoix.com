<?php


class VDesign_BookmePro_Model_System_Source
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
				array('value' => 1, 'label'=>Mage::helper('bookmepro')->__('Count of days/sessions')),
				array('value' => 2, 'label'=>Mage::helper('bookmepro')->__('Date and time of events'))
		);
	}
	
	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
				0 => Mage::helper('bookmepro')->__('Select'),
				1 => Mage::helper('bookmepro')->__('Count'),
				2 => Mage::helper('bookmepro')->__('DateTime')
		);
	}
} 