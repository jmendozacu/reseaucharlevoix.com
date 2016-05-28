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
 * Reservation siege admin controller
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Adminhtml_Reservationreseau_ReservationsiegeController
    extends ReseauChx_ReservationReseau_Controller_Adminhtml_ReservationReseau {
    /**
     * init the reservationsiege
     * @access protected
     * @return ReseauChx_ReservationReseau_Model_Reservationsiege
     */
    protected function _initReservationsiege(){
        $reservationsiegeId  = (int) $this->getRequest()->getParam('id');
        $reservationsiege    = Mage::getModel('reseauchx_reservationreseau/reservationsiege');
        if ($reservationsiegeId) {
            $reservationsiege->load($reservationsiegeId);
        }
        Mage::register('current_reservationsiege', $reservationsiege);
        return $reservationsiege;
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
             ->_title(Mage::helper('reseauchx_reservationreseau')->__('Reservations sieges'));
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
     * edit reservation siege - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction() {
        $reservationsiegeId    = $this->getRequest()->getParam('id');
        $reservationsiege      = $this->_initReservationsiege();
        if ($reservationsiegeId && !$reservationsiege->getId()) {
            $this->_getSession()->addError(Mage::helper('reseauchx_reservationreseau')->__('This reservation siege no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getReservationsiegeData(true);
        if (!empty($data)) {
            $reservationsiege->setData($data);
        }
        Mage::register('reservationsiege_data', $reservationsiege);
        $this->loadLayout();
        $this->_title(Mage::helper('reseauchx_reservationreseau')->__('Reservation'))
             ->_title(Mage::helper('reseauchx_reservationreseau')->__('Reservations sieges'));
        if ($reservationsiege->getId()){
            $this->_title($reservationsiege->getIdquote());
        }
        else{
            $this->_title(Mage::helper('reseauchx_reservationreseau')->__('Add reservation siege'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }
    /**
     * new reservation siege action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction() {
        $this->_forward('edit');
    }
    /**
     * save reservation siege - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('reservationsiege')) {
            try {
            	//Eric: filterDateTime au lieu de filterDate
                $data = $this->_filterDateTime($data, array('dateheuredebut' ,'dateheurefin'));
                $reservationsiege = $this->_initReservationsiege();
                $reservationsiege->addData($data);
                $reservationsiege->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Reservation siege was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $reservationsiege->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setReservationsiegeData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was a problem saving the reservation siege.'));
                Mage::getSingleton('adminhtml/session')->setReservationsiegeData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Unable to find reservation siege to save.'));
        $this->_redirect('*/*/');
    }
    /**
     * delete reservation siege - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $reservationsiege = Mage::getModel('reseauchx_reservationreseau/reservationsiege');
                $reservationsiege->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Reservation siege was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error deleting reservation siege.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Could not find reservation siege to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete reservation siege - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction() {
        $reservationsiegeIds = $this->getRequest()->getParam('reservationsiege');
        if(!is_array($reservationsiegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select reservations sieges to delete.'));
        }
        else {
            try {
                foreach ($reservationsiegeIds as $reservationsiegeId) {
                    $reservationsiege = Mage::getModel('reseauchx_reservationreseau/reservationsiege');
                    $reservationsiege->setId($reservationsiegeId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Total of %d reservations sieges were successfully deleted.', count($reservationsiegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error deleting reservations sieges.'));
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
        $reservationsiegeIds = $this->getRequest()->getParam('reservationsiege');
        if(!is_array($reservationsiegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select reservations sieges.'));
        }
        else {
            try {
                foreach ($reservationsiegeIds as $reservationsiegeId) {
                $reservationsiege = Mage::getSingleton('reseauchx_reservationreseau/reservationsiege')->load($reservationsiegeId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d reservations sieges were successfully updated.', count($reservationsiegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error updating reservations sieges.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass Confirme change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massConfirmeAction(){
        $reservationsiegeIds = $this->getRequest()->getParam('reservationsiege');
        if(!is_array($reservationsiegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select reservations sieges.'));
        }
        else {
            try {
                foreach ($reservationsiegeIds as $reservationsiegeId) {
                $reservationsiege = Mage::getSingleton('reseauchx_reservationreseau/reservationsiege')->load($reservationsiegeId)
                            ->setConfirme($this->getRequest()->getParam('flag_confirme'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d reservations sieges were successfully updated.', count($reservationsiegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error updating reservations sieges.'));
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
        $reservationsiegeIds = $this->getRequest()->getParam('reservationsiege');
        if(!is_array($reservationsiegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select reservations sieges.'));
        }
        else {
            try {
                foreach ($reservationsiegeIds as $reservationsiegeId) {
                $reservationsiege = Mage::getSingleton('reseauchx_reservationreseau/reservationsiege')->load($reservationsiegeId)
                            ->setSalleId($this->getRequest()->getParam('flag_salle_id'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d reservations sieges were successfully updated.', count($reservationsiegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error updating reservations sieges.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass siege change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massSiegeIdAction(){
        $reservationsiegeIds = $this->getRequest()->getParam('reservationsiege');
        if(!is_array($reservationsiegeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select reservations sieges.'));
        }
        else {
            try {
                foreach ($reservationsiegeIds as $reservationsiegeId) {
                $reservationsiege = Mage::getSingleton('reseauchx_reservationreseau/reservationsiege')->load($reservationsiegeId)
                            ->setSiegeId($this->getRequest()->getParam('flag_siege_id'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d reservations sieges were successfully updated.', count($reservationsiegeIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error updating reservations sieges.'));
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
        $fileName   = 'reservationsiege.csv';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_reservationsiege_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as MsExcel - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction(){
        $fileName   = 'reservationsiege.xls';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_reservationsiege_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as xml - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction(){
        $fileName   = 'reservationsiege.xml';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_reservationsiege_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Check if admin has permissions to visit related pages
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('sales/bookme/reseauchx_reservationreseau/reservationsiege');
    }
}
