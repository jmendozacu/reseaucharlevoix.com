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
 * Salle admin controller
 *
 * @category    ReseauChx
 * @package     ReseauChx_ReservationReseau
 * @author      Ultimate Module Creator
 */
class ReseauChx_ReservationReseau_Adminhtml_Reservationreseau_SalleController
    extends ReseauChx_ReservationReseau_Controller_Adminhtml_ReservationReseau {
    /**
     * init the salle
     * @access protected
     * @return ReseauChx_ReservationReseau_Model_Salle
     */
    protected function _initSalle(){
        $salleId  = (int) $this->getRequest()->getParam('id');
        $salle    = Mage::getModel('reseauchx_reservationreseau/salle');
        if ($salleId) {
            $salle->load($salleId);
        }
        Mage::register('current_salle', $salle);
        return $salle;
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
             ->_title(Mage::helper('reseauchx_reservationreseau')->__('Salles'));
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
     * edit salle - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction() {
        $salleId    = $this->getRequest()->getParam('id');
        $salle      = $this->_initSalle();
        if ($salleId && !$salle->getId()) {
            $this->_getSession()->addError(Mage::helper('reseauchx_reservationreseau')->__('This salle no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getSalleData(true);
        if (!empty($data)) {
            $salle->setData($data);
        }
        Mage::register('salle_data', $salle);
        $this->loadLayout();
        $this->_title(Mage::helper('reseauchx_reservationreseau')->__('Reservation'))
             ->_title(Mage::helper('reseauchx_reservationreseau')->__('Salles'));
        if ($salle->getId()){
            $this->_title($salle->getNom());
        }
        else{
            $this->_title(Mage::helper('reseauchx_reservationreseau')->__('Add salle'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }
    /**
     * new salle action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction() {
        $this->_forward('edit');
    }
    /**
     * save salle - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('salle')) {
            try {
                $salle = $this->_initSalle();
                $salle->addData($data);
                $salle->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Salle was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $salle->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSalleData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was a problem saving the salle.'));
                Mage::getSingleton('adminhtml/session')->setSalleData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Unable to find salle to save.'));
        $this->_redirect('*/*/');
    }
    /**
     * delete salle - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $salle = Mage::getModel('reseauchx_reservationreseau/salle');
                $salle->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Salle was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error deleting salle.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Could not find salle to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete salle - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction() {
        $salleIds = $this->getRequest()->getParam('salle');
        if(!is_array($salleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select salles to delete.'));
        }
        else {
            try {
                foreach ($salleIds as $salleId) {
                    $salle = Mage::getModel('reseauchx_reservationreseau/salle');
                    $salle->setId($salleId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reseauchx_reservationreseau')->__('Total of %d salles were successfully deleted.', count($salleIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error deleting salles.'));
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
        $salleIds = $this->getRequest()->getParam('salle');
        if(!is_array($salleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('Please select salles.'));
        }
        else {
            try {
                foreach ($salleIds as $salleId) {
                $salle = Mage::getSingleton('reseauchx_reservationreseau/salle')->load($salleId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d salles were successfully updated.', count($salleIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reseauchx_reservationreseau')->__('There was an error updating salles.'));
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
        $fileName   = 'salle.csv';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_salle_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as MsExcel - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction(){
        $fileName   = 'salle.xls';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_salle_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as xml - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction(){
        $fileName   = 'salle.xml';
        $content    = $this->getLayout()->createBlock('reseauchx_reservationreseau/adminhtml_salle_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Check if admin has permissions to visit related pages
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('sales/bookme/reseauchx_reservationreseau/salle');
    }
}
