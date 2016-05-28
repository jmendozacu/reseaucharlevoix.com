<?php

/**
 * Created by PhpStorm.
 * User: vjcpsy
 * Date: 3/18/2015
 * Time: 14:36
 */
class SM_XPos_Model_Customer extends Mage_Core_Model_Config_Data
{
    public static $customerId = null;
    public static $storeXposId = null;
    public function save()
    {
        $defaultCustomerId = $this->getValue();
        $this::$customerId = $defaultCustomerId;
        $defaultCustomerId = preg_replace('#[^0-9]#', '', $defaultCustomerId);
        $this->setValue($defaultCustomerId);
        $customer = Mage::getModel('customer/customer')->load($defaultCustomerId);
        if ($customer->getEmail() == '') {
            Mage::throwException("You must chose default customer in guest setting!");
        }
        if ($customer->getEmail() == null) {
            //            exit("Customer not found!.");
            Mage::throwException("Customer not found!");
        }

        if (!$customer->getDefaultBillingAddress()) {
            $_custom_address = array(
                'firstname' => 'No Information',
                'lastname' => 'No Information',
                'street' => array(
                    '0' => 'No Information',
                    '1' => '',
                ),
                'city' => 'No Information',
                'region_id' => 'No Information',
                'region' => 'No Information',
                'postcode' => 'No Information',
                'country' => 'Viet Nam',
                'country_id' => 'VN',
                'telephone' => 'No Information',
            );

            $customAddress = Mage::getModel('customer/address');
            $customAddress->setData($_custom_address)
                ->setCustomerId($customer->getId())
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1');
            $customAddress->save();
        }

        $customer = Mage::getModel('customer/customer')->load($defaultCustomerId);
        $billingAdd = $this->getDefaultBillingAdd($customer);
        $guestName = $customer->getFirstname() . $customer->getLastname();
        $guestStreet = $billingAdd->getStreet();
        $street = $guestStreet[0];
        $guestCity = $billingAdd->getCity();
        $guestCountryId = $billingAdd->getCountry(); /*designedly*/
        $guestRegionId = $billingAdd->getRegionId();
        $guestPostalCode = $billingAdd->getPostcode();
        $guestPhone = $billingAdd->getTelephone();

        Mage::getModel('core/config')->saveConfig('xpos/guest/guest_name', $guestName);
        Mage::getModel('core/config')->saveConfig('xpos/guest/guest_street', $street);
        Mage::getModel('core/config')->saveConfig('xpos/guest/guest_city', $guestCity);
        Mage::getModel('core/config')->saveConfig('xpos/guest/country_id', $guestCountryId);
        Mage::getModel('core/config')->saveConfig('xpos/guest/region_id', $guestRegionId);
        Mage::getModel('core/config')->saveConfig('xpos/guest/guest_postal_code', $guestPostalCode);
        Mage::getModel('core/config')->saveConfig('xpos/guest/guest_phone', $guestPhone);

        return parent::save();
    }

    public function getDefaultBillingAdd($customer)
    {
        $add = $customer->getDefaultBilling();
        return $this->_getCustomerAddCreateModel()->load($add);
    }

    public function getDefaultShippingAdd($customer)
    {
        $add = $customer->getDefaultShipping();
        return $this->_getCustomerAddCreateModel()->load($add);
    }

    public function _getCustomerAddCreateModel()
    {
        return Mage::getModel('customer/address');
    }

    public function checkDefaultCustomerWithXposWebsite($customerId, $storeId)
    {
//        $storeId = Mage::getStoreConfig('xpos/general/storeid');
        $accountShare = Mage::getStoreConfig('customer/account_share/scope');
        $websiteXposId = Mage::app()->getStore($storeId)->getWebsiteId();
        if (!!$customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerWebsite = $customer->getData('website_id');
            if ($customerWebsite == 0) {
                return false;
            }
            if ($accountShare == 0) {
                return true;
            } else if ($accountShare == 1 && $customerWebsite != $websiteXposId) {
                return false;
            } else {
                return true;
            }
        }
    }


}
