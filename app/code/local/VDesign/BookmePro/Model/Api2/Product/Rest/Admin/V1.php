<?php

class VDesign_BookmePro_Model_Api2_Product_Rest_Admin_V1 extends VDesign_BookmePro_Model_Api2_Product
{
    
    protected function _create($couponData)
    {
        $productId = $this->getRequest()->getParam('product_id');
        $couponData['product_id'] = $productId;
		
        $product = Mage::getModel('catalog/product')->load($productId);
        
        if(!$product->getId())
        	$this->_critical(Mage::helper('bookmepro')->__('BookmePRO API: Product not found.'),
        			Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        
        $product->setData('bookable_from', $couponData['bookable_from']);
        $product->setData('bookable_to', $couponData['bookable_to']);
        $product->setData('bookable_qty', $couponData['bookable_qty']);
        $product->setData('include_shipping', $this->attributeValueExists('include_shipping', (!$couponData['include_shipping'])? 'disabled' : 'enabled'));
        $product->setData('billable_period', $this->attributeValueExists('billable_period', $couponData['billable_period']));
        $product->setData('display_timezone', $this->attributeValueExists('display_timezone', (!$couponData['display_timezone'])? 'disabled' : 'enabled'));
        $product->setData('one_day_book', $this->attributeValueExists('one_day_book', (!$couponData['one_day_book'])? 'disabled' : 'enabled'));
        
        $product->setData('custom_session', $couponData['custom_session']);
        $product->setData('exclude_day', $couponData['exclude_day']);
        $product->setData('price_profile', $couponData['price_profile']);
        $product->setData('price_rule', $couponData['price_rule']);
        try {
        	$product->save();
        }catch (Exception $e){
        	$this->_critical(Mage::helper('bookmepro')->__('BookmePRO API: '.$e->getMessage()),
        			Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }
    
    public function attributeValueExists($arg_attribute, $arg_value)
    {
    	$attribute_model        = Mage::getModel('eav/entity_attribute');
    	$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
    
    	$attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
    	$attribute              = $attribute_model->load($attribute_code);
    
    	$attribute_table        = $attribute_options_model->setAttribute($attribute);
    	$options                = $attribute_options_model->getAllOptions(false);
    
    	foreach($options as $option)
    	{
    		if ($option['label'] == $arg_value)
    		{
    			return $option['value'];
    		}
    	}
    
    	return false;
    }

    
    protected function _retrieveCollection()
    {
        return array('result' => true);
    }

    
}