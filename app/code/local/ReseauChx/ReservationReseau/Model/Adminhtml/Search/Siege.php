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
class ReseauChx_ReservationReseau_Model_Adminhtml_Search_Siege
    extends Varien_Object {
    /**
     * Load search results
     * @access public
     * @return ReseauChx_ReservationReseau_Model_Adminhtml_Search_Siege
     * @author Ultimate Module Creator
     */
    public function load(){
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('reseauchx_reservationreseau/siege_collection')
            ->addFieldToFilter('code', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $siege) {
            $arr[] = array(
                'id'=> 'siege/1/'.$siege->getId(),
                'type'  => Mage::helper('reseauchx_reservationreseau')->__('Siege'),
                'name'  => $siege->getCode(),
                'description'   => $siege->getCode(),
                'url' => Mage::helper('adminhtml')->getUrl('*/reservationreseau_siege/edit', array('id'=>$siege->getId())),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
