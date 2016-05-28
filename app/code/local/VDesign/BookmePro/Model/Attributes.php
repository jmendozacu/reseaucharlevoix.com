<?php


class VDesign_BookmePro_Model_Attributes extends VDesign_Bookme_Model_Attributes
{
	public function getAllOptions(){
		
		$array = parent::getAllOptions();
		
		return array_merge($array, array(
				'price_profile' => 'bookmepro/adminhtml_catalog_product_edit_tab_price_profile',
				'mail_reminder' => 'bookmepro/adminhtml_catalog_product_edit_tab_mailreminder'
		));
	}
}