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
class Dotsquares_Taxrate_Block_Adminhtml_Taxrate_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	
	public function __construct()
    {
        parent::__construct();       
        $this->setTemplate('taxrate/rate/form.phtml');
    }
	
	
	
	/**
	 * prepare the form
	 * @access protected
	 * @return Taxrate_Taxrate_Block_Adminhtml_Taxrate_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		//$form->setHtmlIdPrefix('taxrate_');
		//$form->setFieldNameSuffix('taxrate');
		$this->setForm($form);
		
		
		   $rateObject = new Varien_Object(Mage::getSingleton('tax/calculation_rate')->getData());
		   
		   $countries = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
			unset($countries[0]);
	
			if (!$rateObject->hasTaxCountryId()) {
				$rateObject->setTaxCountryId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY));
			}
	
			if (!$rateObject->hasTaxRegionId()) {
				$rateObject->setTaxRegionId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION));
			}
	
			$regionCollection = Mage::getModel('directory/region')
				->getCollection()
				->addCountryFilter($rateObject->getTaxCountryId());
	
			$regions = $regionCollection->toOptionArray();		
			
			
			if ($regions) {
				$regions[0]['label'] = '*';
			} else {
				$regions = array(array('value' => '', 'label' => '*'));
			}
		 
		
		 $fdata = Mage::registry('current_taxrate')->getData();		
		
		$fieldset = $form->addFieldset('taxrate_form', array('legend'=>Mage::helper('taxrate')->__('Tax Rate Information')));
		
			if (isset($fdata['entity_id']) && $fdata['entity_id'] > 0) {
				$fieldset->addField('entity_id', 'hidden', array(
					'name'  => 'entity_id',
					'value' => $fdata['entity_id']
				));
			}
	
			$fieldset->addField('taxrate_title', 'text', array(
				'label' => Mage::helper('taxrate')->__('Tax Rate Title'),
				'name'  => 'taxrate_title',
				'required'  => true,
				'class' => 'required-entry',
	
			));
	
			$fieldset->addField('tax_rate', 'text', array(
				'label' => Mage::helper('taxrate')->__('Rate Percent'),
				'name'  => 'tax_rate',
				'required'  => true,
				'class' => 'required-entry',
	
			));		
		
		   $fieldset->addField('tax_country_id', 'multiselect', array(
				'name'     => 'tax_country_id',
				'label'    => Mage::helper('tax')->__('Country'),
				'required' => true,
				'values'   => $countries
			));
	
			$fieldset->addField('tax_region_id', 'select', array(
				'name'   => 'tax_region_id',
				'label'  => Mage::helper('tax')->__('State'),
				'values' => $regions
			));
	
			$fieldset->addField('zip_is_range', 'select', array(
				'name'    => 'zip_is_range',
				'label'   => Mage::helper('tax')->__('Zip/Post is Range'),
				'options' => array(
					'0' => Mage::helper('tax')->__('No'),
					'1' => Mage::helper('tax')->__('Yes'),
				)
			));
	
			if (!$rateObject->hasTaxPostcode()) {
				$rateObject->setTaxPostcode(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_POSTCODE));
			}
	
			$fieldset->addField('tax_postcode', 'text', array(
				'name'  => 'tax_postcode',
				'label' => Mage::helper('tax')->__('Zip/Post Code'),
				'note'  => Mage::helper('tax')->__("'*' - matches any; 'xyz*' - matches any that begins on 'xyz' and not longer than %d.", Mage::helper('tax')->getPostCodeSubStringLength()),
			));
	
			$fieldset->addField('zip_from', 'text', array(
				'name'      => 'zip_from',
				'label'     => Mage::helper('tax')->__('Range From'),
				'required'  => true,
				'maxlength' => 9,
				'class'     => 'validate-digits'
			));
	
			$fieldset->addField('zip_to', 'text', array(
				'name'      => 'zip_to',
				'label'     => Mage::helper('tax')->__('Range To'),
				'required'  => true,
				'maxlength' => 9,
				'class'     => 'validate-digits'
			));
		
		
		if (Mage::getSingleton('adminhtml/session')->getTaxrateData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getTaxrateData());
			Mage::getSingleton('adminhtml/session')->setTaxrateData(null);
						
			
		}
		elseif (Mage::registry('current_taxrate')){
					
			$form->setValues(Mage::registry('current_taxrate')->getData());
		}
		
		$this->setChild(
            'form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap('zip_is_range', 'zip_is_range')
                ->addFieldMap('tax_postcode', 'tax_postcode')
                ->addFieldMap('zip_from', 'zip_from')
                ->addFieldMap('zip_to', 'zip_to')
                ->addFieldDependence('zip_from', 'zip_is_range', '1')
                ->addFieldDependence('zip_to', 'zip_is_range', '1')
                ->addFieldDependence('tax_postcode', 'zip_is_range', '0')
        );
		
		return parent::_prepareForm();
	}
}