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
 * Siege admin controller
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Adminhtml_Reservationreseau_SiegeController
    extends ReseauChx_ReservationReseau_Controller_Adminhtml_ReservationReseau {
    /**
     * init the siege
     * @access protected
     * @return ReseauChx_ReservationReseau_Model_Siege
     */
    protected function _initSiege(){
        $siegeId  = (int) $this->getRequest()->getParam('id');
        $siege    = Mage::getModel('reseauchx_reservationreseau/siege');
        if ($siegeId) {
            $siege->load($siegeId);
        }
        Mage::register('current_siege', $siege);
        return $siege;
    }
     /**
     * default action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('reseauchx_reservationreseau')->__('Reservation'))
             ->_title(Mage::helper('reseauchx_reservationreseau')->__('Sieges'));
        $this->renderLayout();
    }
    /**
     * grid action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction() {
        $this->loadLayout()->renderLayout();
    }
    /**
     * edit siege - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction() {
        $siegeId    = $this->getRequest()->getParam('id');
        $siege      = $this->_initSiege();
        if ($siegeId && !$siege->getId()) {
            $this->_getSession()->addError(Mage::helper('reseauchx_reservationreseau')->__('This siege no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getSiegeData(true);
        if (!empty($data)) {
            $siege->setData($data);
        }
        Mage::register('siege_data', $siege);
        $this->loadLayout();
        $this->_title(Mage::helper('reseauchx_reservationreseau')->__('Reservation'))
             ->_title(Mage::helper('reseauchx_reservationreseau')->__('Sieges'));
        if ($siege->getId()){
            $this->_title($siege->getCode());
        }
        else{
            $this->_title(Mage::helper('reseauchx_reservationreseau')->__('Add siege'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }
    /**
     * new siege action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction() {
        $this->_forward('edit');
    }
    /**
     * save siege - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('siege')) {
            try {
                $siege = $this->_initSiege();
                $siege->addData($data);
                $siege->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Siege was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $siege->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSiegeData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was a problem saving the siege.'));
                Mage::getSingleton('adminhtml/session')->setSiegeData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Unable to find siege to save.'));
        $this->_redirect('*/*/');
    }
    /**
     * delete siege - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $siege = Mage::getModel('reseauchx_reservationreseau/siege');
                $siege->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Siege was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error deleting siege.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Could not find siege to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete siege - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction() {
        $siegeIds = $this->getRequest()->getParam('siege');
        if(!is_array($siegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select sieges to delete.'));
        }
        else {
            try {
                foreach ($siegeIds as $siegeId) {
                    $siege = Mage::getModel('reseauchx_reservationreseau/siege');
                    $siege->setId($siegeId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Total of %d sieges were successfully deleted.', count($siegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error deleting sieges.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass status change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction(){
        $siegeIds = $this->getRequest()->getParam('siege');
        if(!is_array($siegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select sieges.'));
        }
        else {
            try {
                foreach ($siegeIds as $siegeId) {
                $siege = Mage::getSingleton('reseauchx_reservationreseau/siege')->load($siegeId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d sieges were successfully updated.', count($siegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error updating sieges.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass Categorie change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massCategorieAction(){
        $siegeIds = $this->getRequest()->getParam('siege');
        if(!is_array($siegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select sieges.'));
        }
        else {
            try {
                foreach ($siegeIds as $siegeId) {
                $siege = Mage::getSingleton('reseauchx_reservationreseau/siege')->load($siegeId)
                            ->setCategorie($this->getRequest()->getParam('flag_categorie'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d sieges were successfully updated.', count($siegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error updating sieges.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass salle change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massSalleIdAction(){
        $siegeIds = $this->getRequest()->getParam('siege');
        if(!is_array($siegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select sieges.'));
        }
        else {
            try {
                foreach ($siegeIds as $siegeId) {
                $siege = Mage::getSingleton('reseauchx_reservationreseau/siege')->load($siegeId)
                            ->setSalleId($this->getRequest()->getParam('flag_salle_id'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d sieges were successfully updated.', count($siegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error updating sieges.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * export as csv - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction(){
        $fileName   = 'siege.csv';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_siege_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as MsExcel - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction(){
        $fileName   = 'siege.xls';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_siege_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as xml - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction(){
        $fileName   = 'siege.xml';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_siege_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Check if admin has permissions to visit related pages
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('sales/bookme/reseauchx_reservationreseau/siege');
    }
}
