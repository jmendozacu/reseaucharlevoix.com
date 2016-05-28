<?php
/**
 * dotsquares.com
 *
 * Dotsquares_Taxrate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Dotsquares
 * @package		Dotsquares_Taxrate
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @author      Jagdish Ram <jagdish.ram@dotsquares.com>
 */
class Dotsquares_Taxrate_Model_Taxrate extends Mage_Core_Model_Abstract{
	/**
	 * Entity code.
	 * Can be used as part of method name for entity processing
	 */
	const ENTITY= 'taxrate_taxrate';
	const CACHE_TAG = 'taxrate_taxrate';
	/**
	 * Prefix of model events names
	 * @var string
	 */
	protected $_eventPrefix = 'taxrate_taxrate';
	
	/**
	 * Parameter name in event
	 * @var string
	 */
	protected $_eventObject = 'taxrate';
	/**
	 * constructor
	 * @access public
	 * @return void
	 */
	public function _construct(){
		parent::_construct();
		$this->_init('taxrate/taxrate');
	}
	/**
	 * before save taxrate
	 * @access protected
	 * @return Dotsquares_Taxrate_Model_Taxrate
	 */
	protected function _beforeSave(){
		parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if ($this->isObjectNew()){
			$this->setCreatedAt($now);
		}
		$this->setUpdatedAt($now);
		return $this;
	}
	/**
	 * save taxrate relation
	 * @access public
	 * @return Dotsquares_Taxrate_Model_Taxrate
	 */
	protected function _afterSave() {
		return parent::_afterSave();
	}
}