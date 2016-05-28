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
class Dotsquares_Taxrate_Adminhtml_Taxrate_TaxrateController extends Dotsquares_Taxrate_Controller_Adminhtml_Taxrate{
	
	/**
     * Init layout, menu and breadcrumb
     *
     * @return Dotsquares_Taxrate_Adminhtml_Taxrate_TaxrateController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/tax');
        return $this;
    }
	/**
	 * init the taxrate
	 * @access protected
	 * @return Dotsquares_Taxrate_Model_Taxrate
	 */
	protected function _initTaxrate(){
		$taxrateId  = (int) $this->getRequest()->getParam('id');
		$taxrate	= Mage::getModel('taxrate/taxrate');
		if ($taxrateId) {
			$taxrate->load($taxrateId);
		}
		
		Mage::register('current_taxrate', $taxrate);
		return $taxrate;
	}
 	/**
	 * default action
	 * @access public
	 * @return void	 
	 */
	public function indexAction() {

		$this->_initAction();
		$this->renderLayout();
	}
	/**
	 * grid action
	 * @access public
	 * @return void	 
	 */
	public function gridAction() {
		$this->loadLayout()->renderLayout();
	}
	/**
	 * edit taxrate - action
	 * @access public
	 * @return void	 
	 */
	public function editAction() {
		$taxrateId	= $this->getRequest()->getParam('id');
		$taxrate  	= $this->_initTaxrate();
		if ($taxrateId && !$taxrate->getId()) {
			$this->_getSession()->addError(Mage::helper('taxrate')->__('This tax rate no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$taxrate->setData($data);
		}
		Mage::register('taxrate_data', $taxrate);

		$this->_initAction();
		
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new taxrate action
	 * @access public
	 * @return void	
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save taxrate - action
	 * @access public
	 * @return void	 
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			try { 			  
			     //----------Add to Tax Rate Table------------------	
			   $taxCalcIdsAry = array();
			   if(isset($data['entity_id']) && $data['entity_id'] >0)
			    {			 
				  $entity_id = $data['entity_id'];
				   $taxrateDataObj = Mage::getSingleton('taxrate/taxrate')->load($entity_id);
				   $taxrateData = $taxrateDataObj->getData();				   
					 $taxCountryIdsAry = explode(",",$taxrateData['tax_country_id']);
					 $taxCalcIdsAry = explode(",",$taxrateData['tax_calculation_rate_ids']);					 			 
				}	
				 
				 $countriesIDs = $data['tax_country_id'];
				 $newRateIds = array();				 
				 $editedRateIds = array();
				 
				 foreach($countriesIDs as $countryCode)
				  { 				  			  	  
				    $ratePost = array();					
				 $ratePost['tax_country_id'] = $countryCode;
				 $ratePost['tax_region_id'] = 0;
				 $regionCode = '*';
				 if(isset($data['tax_region_id']) && $data['tax_region_id'] > 0)
				  {
				    $ratePost['tax_region_id'] = $data['tax_region_id'];
					$region = Mage::getModel('directory/region')->load($data['tax_region_id']);
                    $regionCode = $region->getCode();
				  }
				 $ratePost['rate'] = $data['tax_rate'];
				 
				 $ratePost['tax_postcode'] = '*';
				 if(isset($data['tax_postcode']) && $data['tax_postcode']!="")
				  { 				 
				   $ratePost['tax_postcode'] = $data['tax_postcode'];				 
				  } 
				 
				 if($data['zip_is_range']==1)
				  { 
				   $ratePost['zip_is_range'] = $data['zip_is_range'];
				   $ratePost['zip_from'] = $data['zip_from'];
				   $ratePost['zip_to'] = $data['zip_to'];
				   $ratePost['tax_postcode'] = 	 $data['zip_from']."-".$data['zip_to'];
				  }
				  		  
				  $ratecode = $countryCode."-".$regionCode."-".$ratePost['tax_postcode']."-".$data['taxrate_title'];
				  $ratePost['code'] = $ratecode;			  
				  
				  if(count($taxCalcIdsAry))
				   {				   
					$taxCalcDataObj = Mage::getModel('tax/calculation_rate')->getCollection()
                                      ->addFieldToFilter('tax_country_id', $countryCode)
									  ->addFieldToFilter('tax_calculation_rate_id', $taxCalcIdsAry);
				
					 if(count($taxCalcDataObj))
					  {
						foreach($taxCalcDataObj as $tvalObj)
						 {
						   $tCalcData = $tvalObj->getData();
						   $tax_calculation_rate_id = $tCalcData['tax_calculation_rate_id']; 
						   $codeTaxRate = $tCalcData['code'];						   
						 }
						 $editedRateIds[] = $tax_calculation_rate_id;
						 
						 $ratePost['tax_calculation_rate_id'] = $tax_calculation_rate_id;
						 $ratePost['code'] = $codeTaxRate;
					  }					  
					} 
													
				 $rateModel = Mage::getModel('tax/calculation_rate')->setData($ratePost);
				 $rateModel->save();
				 $newRateId = $rateModel->getId();				  
				 $newRateIds[] = $newRateId;
				 
				}
				 
				 if(count($taxCalcIdsAry))
				 {  
				   foreach($taxCalcIdsAry as $tcalcId)
					{
					   if(!in_array($tcalcId, $editedRateIds))
						{
						   $rateModelDel = Mage::getModel('tax/calculation_rate')->load($tcalcId);
						   if ($rateModelDel->getId()) {									
							 $rateModelDel->delete();
						   }
						}
					}					 
				 } 
				 
				$data['tax_calculation_rate_ids'] = implode(",", $newRateIds);
				$data['tax_country_id'] = implode(",",$data['tax_country_id']);
				//----------------------------
					
				$taxrate = $this->_initTaxrate();
				$taxrate->addData($data);
				$taxrate->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('taxrate')->__('The Tax rate has been saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $taxrate->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} 
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('taxrate')->__('There was a problem saving the tax rate.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('taxrate')->__('Unable to find tax rate to save.'));
		$this->_redirect('*/*/');
	}
	
	/**
	 * delete tax rate - action
	 * @access public
	 * @return void	
	 */
	public function _deleteTaxCalcRate($entity_id) { 
	  
	  $taxrateDataObj = Mage::getSingleton('taxrate/taxrate')->load($entity_id);
	  $taxrateData = $taxrateDataObj->getData();
	  $taxCalcIdsAry = explode(",",$taxrateData['tax_calculation_rate_ids']);
	  foreach($taxCalcIdsAry as $rateId)
	   {	
		 if ($rateId) {
            $rateModel = Mage::getModel('tax/calculation_rate')->load($rateId);			
            if ($rateModel->getId()) {
			               
			   $rateModel->delete();   
			}			
		  }		  
	   }
	}
	
	
	/**
	 * delete taxrate - action
	 * @access public
	 * @return void	 
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$taxrate = Mage::getModel('taxrate/taxrate');
				$this->_deleteTaxCalcRate($this->getRequest()->getParam('id'));	
				$taxrate->setId($this->getRequest()->getParam('id'))->delete();										
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('taxrate')->__('Tax rate has been successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('taxrate')->__('There was an error deleteing tax rate.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('taxrate')->__('Could not find tax rate to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete taxrate - action
	 * @access public
	 * @return void	 
	 */
	public function massDeleteAction() {
		$taxrateIds = $this->getRequest()->getParam('taxrate');
		if(!is_array($taxrateIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('taxrate')->__('Please select tax rates to delete.'));
		}
		else {
			try {
				foreach ($taxrateIds as $taxrateId) {
					$taxrate = Mage::getModel('taxrate/taxrate');
					 $this->_deleteTaxCalcRate($taxrateId);
					 $taxrate->setId($taxrateId)->delete();					
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('taxrate')->__('Total of %d tax rates were successfully deleted.', count($taxrateIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('taxrate')->__('There was an error deleteing tax rates.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * export as csv - action
	 * @access public
	 * @return void	 
	 */
	public function exportCsvAction(){
		$fileName   = 'taxrate.csv';
		$content	= $this->getLayout()->createBlock('taxrate/adminhtml_taxrate_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 * @return void	 
	 */
	public function exportExcelAction(){
		$fileName   = 'taxrate.xls';
		$content	= $this->getLayout()->createBlock('taxrate/adminhtml_taxrate_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 * @return void	 
	 */
	public function exportXmlAction(){
		$fileName   = 'taxrate.xml';
		$content	= $this->getLayout()->createBlock('taxrate/adminhtml_taxrate_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}