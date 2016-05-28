<?php


class VDesign_BookmePro_Model_Resource_Session extends Mage_Core_Model_Resource_Db_Abstract{
	protected function _construct()
	{
		$this->_init('bookmepro/session', 'session_id');
	}
}