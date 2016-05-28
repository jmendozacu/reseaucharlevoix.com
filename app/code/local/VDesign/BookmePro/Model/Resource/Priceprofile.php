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

class VDesign_BookmePro_Model_Resource_Priceprofile
extends Mage_Core_Model_Resource_Db_Abstract{
	protected function _construct()
	{
		$this->_init('bookmepro/priceprofile', 'profile_id');
	}
	
	public function delete(Mage_Core_Model_Abstract $profile)
	{
		$profile->setData('entity_id', 0);
		$profile->save();
	}
}