<?php

/**
 * Created by PhpStorm.
 * User: xxx
 * Date: 3/17/2015
 * Time: 11:23
 */
class SM_XPos_Model_Guest extends Mage_Core_Model_Abstract
{
    private $_customerShippingAdd;
    private $_customerBillingAdd;
    private $_sourceCustomer;

    public function _construct()
    {
        parent::_construct();
        $this->_init('xpos/guest');
    }

    public function getDefaultCustomer()
    {
        $defaultCustomerId = Mage::getStoreConfig('xpos/guest/default_customer_id');
//        $defaultCustomerId = 1172;
        $this->_sourceCustomer = $this->_getCustomerCreateModel()->load($defaultCustomerId);
        $this->getDefaultBillingAdd($this->_sourceCustomer);
        $this->getDefaultShippingAdd($this->_sourceCustomer);
        $customerData = array(
            'account' => array(
                'id' => $defaultCustomerId,
                'type' => 'Customer',
                'group_id' => $this->_groupId,
                'email' => $this->_sourceCustomer->getEmail()
            ),
            'billing_address' => array(
                'customer_address_id' => $this->_sourceCustomer->getCustomerAddressId(),
                'prefix' => '',
                'firstname' => $this->_sourceCustomer->getFirstname(),/*s*/
                'middlename' => '',
                'lastname' => $this->_sourceCustomer->getLastname(),/*s*/
                'suffix' => '',
                'company' => '',
                'street' => $this->_customerBillingAdd->getStreet(),/*s*/
                'city' => $this->_customerBillingAdd->getCity(),/*s*/
                'country' => $this->_getCountryCreateModel()->load($this->_customerBillingAdd->getCountryId())->getName(),
                'country_id' => $this->_customerBillingAdd->getCountryId(),/*s*/
                'region' => $this->_customerBillingAdd->getRegion(),/*s*/
                'region_id' => $this->_customerBillingAdd->getRegionId(),/*s*/
                'postcode' => $this->_customerBillingAdd->getPostcode(),/*s*/
                'telephone' => $this->_customerBillingAdd->getTelephone(),/*s*/
                'fax' => '',
            ),
            'shipping_address' => array(
                'customer_address_id' => $this->_sourceCustomer->getCustomerAddressId(),
                'prefix' => '',
                'firstname' => $this->_sourceCustomer->getFirstname(),
                'middlename' => '',
                'lastname' => $this->_sourceCustomer->getLastname(),
                'suffix' => '',
                'company' => '',
                'street' => $this->_customerShippingAdd->getStreet(),/*s*/
                'city' => $this->_customerShippingAdd->getCity(),/*s*/
                'country_id' => $this->_customerShippingAdd->getCountryId(),/*s*/
                'country' => $this->_getCountryCreateModel()->load($this->_customerShippingAdd->getCountryId())->getName(),
                'region' => $this->_customerShippingAdd->getRegion(),/*s*/
                'region_id' => $this->_customerShippingAdd->getRegionId(),/*s*/
                'postcode' => $this->_customerShippingAdd->getPostcode(),/*s*/
                'telephone' => $this->_customerShippingAdd->getTelephone(),/*s*/
                'fax' => '',
            ),
        );
        return $customerData;


    }

    public function _getCustomerCreateModel()
    {
        return Mage::getModel('customer/customer');
    }

    public function _getCustomerAddCreateModel()
    {
        return Mage::getModel('customer/address');
    }

    public function getDefaultShippingAdd($customer)
    {
        $add = $customer->getDefaultShipping();
        $this->_customerShippingAdd = $this->_getCustomerAddCreateModel()->load($add);
    }

    public function getDefaultBillingAdd($customer)
{
    $add = $customer->getDefaultBilling();
    $this->_customerBillingAdd = $this->_getCustomerAddCreateModel()->load($add);
}

    public function _getCountryCreateModel()
    {
        return Mage::getModel('directory/country');
    }

}
