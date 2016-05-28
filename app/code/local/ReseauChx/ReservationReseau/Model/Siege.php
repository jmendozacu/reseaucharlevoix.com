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
 * Siege model
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Model_Siege
    extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'reseauchx_reservationreseau_siege';
    const CACHE_TAG = 'reseauchx_reservationreseau_siege';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'reseauchx_reservationreseau_siege';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'siege';
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct(){
        parent::_construct();
        $this->_init('reseauchx_reservationreseau/siege');
    }
    /**
     * before save siege
     * @access protected
     * @return ReseauChx_ReservationReseau_Model_Siege
     * @author Ultimate Module Creator
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
     * save siege relation
     * @access public
     * @return ReseauChx_ReservationReseau_Model_Siege
     * @author Ultimate Module Creator
     */
    protected function _afterSave() {
        return parent::_afterSave();
    }
    /**
     * Retrieve  collection
     * @access public
     * @return ReseauChx_ReservationReseau_Model_Reservationsiege_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedReservationssiegesCollection(){
        if (!$this->hasData('_reservationsiege_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            }
            else {
                $collection = Mage::getResourceModel('reseauchx_reservationreseau/reservationsiege_collection')
                        ->addFieldToFilter('siege_id', $this->getId());
                $this->setData('_reservationsiege_collection', $collection);
            }
        }
        return $this->getData('_reservationsiege_collection');
    }
    /**
     * Retrieve parent 
     * @access public
     * @return null|ReseauChx_ReservationReseau_Model_Salle
     * @author Ultimate Module Creator
     */
    public function getParentSalle(){
        if (!$this->hasData('_parent_salle')) {
            if (!$this->getSalleId()) {
                return null;
            }
            else {
                $salle = Mage::getModel('reseauchx_reservationreseau/salle')->load($this->getSalleId());
                if ($salle->getId()) {
                    $this->setData('_parent_salle', $salle);
                }
                else {
                    $this->setData('_parent_salle', null);
                }
            }
        }
        return $this->getData('_parent_salle');
    }
    /**
     * get default values
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues() {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
}
