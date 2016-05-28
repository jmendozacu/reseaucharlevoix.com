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
 * Salle model
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Model_Salle
    extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'reseauchx_reservationreseau_salle';
    const CACHE_TAG = 'reseauchx_reservationreseau_salle';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'reseauchx_reservationreseau_salle';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'salle';
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct(){
        parent::_construct();
        $this->_init('reseauchx_reservationreseau/salle');
    }
    /**
     * before save salle
     * @access protected
     * @return ReseauChx_ReservationReseau_Model_Salle
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
     * save salle relation
     * @access public
     * @return ReseauChx_ReservationReseau_Model_Salle
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
                        ->addFieldToFilter('salle_id', $this->getId());
                $this->setData('_reservationsiege_collection', $collection);
            }
        }
        return $this->getData('_reservationsiege_collection');
    }
    //Eric: Add getCollectionByDate and
    public function getSelectedReservationssiegesCollectionByDateDebutConfirme($datedebut){
        if (!$this->hasData('_reservationsiege_collection')) {
    		if (!$this->getId()) {
    			return new Varien_Data_Collection();
    		}
    		else {
    			Mage::log('heureDateDebut: ' . date_format($datedebut,"Y/m/d H:i:s"));
    			
    			$collection = Mage::getResourceModel('reseauchx_reservationreseau/reservationsiege_collection')
    					->addFieldToFilter('salle_id', $this->getId())
    					->addFieldToFilter('confirme', 1)
    					->addFieldToFilter('dateheuredebut', array(
    						'from' => date_format($datedebut,"Y/m/d H:i:s"),
    						'to' => date_format($datedebut,"Y/m/d H:i:s"),
    						'datetime' => false))
    					;
    			Mage::log('heureDateDebut: ' . $collection->getSelect());

                var_dump($this->getId(),date_format($datedebut,"Y/m/d H:i:s"));
    			$this->setData('_reservationsiege_collection', $collection);
    		}
    	}

    	return $this->getData('_reservationsiege_collection');
    }
    
    //Eric: Add getCollectionByDate and
    public function getSelectedReservationssiegesCollectionByDateDebutNonConfirme($datedebut){
    	if (!$this->hasData('_reservationsiege_collection')) {
    		if (!$this->getId()) {
    			return new Varien_Data_Collection();
    		}
    		else {
    			Mage::log('heureDateDebut: ' . date_format($datedebut,"Y/m/d H:i:s"));
    			//date_default_timezone_set("America/New_York");
    			$updateMin = date("Y/m/d H:i:s", strtotime("-10 minutes"));
    			$updateMax = date("Y/m/d H:i:s", strtotime("NOW"));
    			Mage::log('$updateMax: ' . $updateMax);
    			$collection = Mage::getResourceModel('reseauchx_reservationreseau/reservationsiege_collection')
    			->addFieldToFilter('salle_id', $this->getId())
    			->addFieldToFilter('confirme', 0)
    			->addFieldToFilter('dateheuredebut', array(
    					'from' => date_format($datedebut,"Y/m/d H:i:s"),
    					'to' => date_format($datedebut,"Y/m/d H:i:s"),
    					'datetime' => false))
				->addFieldToFilter('updated_at', array(
    							'from' => $updateMin,
    							'to' => $updateMax,
    							'datetime' => false))
    					;
    					Mage::log('heureDateDebut: ' . $collection->getSelect());
    					$this->setData('_reservationsiege_collection', $collection);
    		}
    	}
    	return $this->getData('_reservationsiege_collection');
    }
    /**
     * Retrieve  collection
     * @access public
     * @return ReseauChx_ReservationReseau_Model_Siege_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedSiegesCollection(){
        if (!$this->hasData('_siege_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            }
            else {
                $collection = Mage::getResourceModel('reseauchx_reservationreseau/siege_collection')
                        ->addFieldToFilter('salle_id', $this->getId());
                $this->setData('_siege_collection', $collection);
            }
        }
        return $this->getData('_siege_collection');
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
