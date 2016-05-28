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
class Dotsquares_Taxrate_Block_Adminhtml_Taxrate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	/**
	 * constuctor
	 * @access public
	 * @return void	 
	 */
	public function __construct(){
		parent::__construct();
		$this->_blockGroup = 'taxrate';
		$this->_controller = 'adminhtml_taxrate';
		$this->_updateButton('save', 'label', Mage::helper('taxrate')->__('Save Tax Rate'));
		$this->_updateButton('delete', 'label', Mage::helper('taxrate')->__('Delete Tax Rate'));
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('taxrate')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);
		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	/**
	 * get the edit form header
	 * @access public
	 * @return string	 
	 */
	public function getHeaderText(){
		if( Mage::registry('taxrate_data') && Mage::registry('taxrate_data')->getId() ) {
			return Mage::helper('taxrate')->__("Edit Tax rate '%s'", $this->htmlEscape(Mage::registry('taxrate_data')->getTaxrateTitle()));
		} 
		else {
			return Mage::helper('taxrate')->__('Add Tax Rate');
		}
	}
}