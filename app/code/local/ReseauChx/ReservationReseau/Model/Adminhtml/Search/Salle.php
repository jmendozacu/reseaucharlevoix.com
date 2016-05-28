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
 * Admin search model
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Model_Adminhtml_Search_Salle
    extends Varien_Object {
    /**
     * Load search results
     * @access public
     * @return ReseauChx_ReservationReseau_Model_Adminhtml_Search_Salle
     * @author Ultimate Module Creator
     */
    public function load(){
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('reseauchx_reservationreseau/salle_collection')
            ->addFieldToFilter('nom', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $salle) {
            $arr[] = array(
                'id'=> 'salle/1/'.$salle->getId(),
                'type'  => Mage::helper('reseauchx_reservationreseau')->__('Salle'),
                'name'  => $salle->getNom(),
                'description'   => $salle->getNom(),
                'url' => Mage::helper('adminhtml')->getUrl('*/reservationreseau_salle/edit', array('id'=>$salle->getId())),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
