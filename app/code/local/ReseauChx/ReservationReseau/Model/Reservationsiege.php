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
 * Reservation siege model
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Model_Reservationsiege
    extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'reseauchx_reservationreseau_reservationsiege';
    const CACHE_TAG = 'reseauchx_reservationreseau_reservationsiege';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'reseauchx_reservationreseau_reservationsiege';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'reservationsiege';
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct(){
        parent::_construct();
        $this->_init('reseauchx_reservationreseau/reservationsiege');
    }
    /**
     * before save reservation siege
     * @access protected
     * @return ReseauChx_ReservationReseau_Model_Reservationsiege
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
     * save reservationsiege relation
     * @access public
     * @return ReseauChx_ReservationReseau_Model_Reservationsiege
     * @author Ultimate Module Creator
     */
    protected function _afterSave() {
        return parent::_afterSave();
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
     * Retrieve parent 
     * @access public
     * @return null|ReseauChx_ReservationReseau_Model_Siege
     * @author Ultimate Module Creator
     */
    public function getParentSiege(){
        if (!$this->hasData('_parent_siege')) {
            if (!$this->getSiegeId()) {
                return null;
            }
            else {
                $siege = Mage::getModel('reseauchx_reservationreseau/siege')->load($this->getSiegeId());
                if ($siege->getId()) {
                    $this->setData('_parent_siege', $siege);
                }
                else {
                    $this->setData('_parent_siege', null);
                }
            }
        }
        return $this->getData('_parent_siege');
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
