<?php

class Vianetz_AutomaticInvoice_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    /** @var Vianetz_AutomaticInvoice_Helper_Data */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = Mage::helper('automaticinvoice');
    }

    public function testInvoicePrefixWithDirSeparatorCharCorrectlySanitized()
    {
        $this->assertEquals('16_20130402', $this->_helper->stringToFilename('16/20130402'));
    }

    public function testInvoicePrefixWithSpecialCharsCorrectlySanitized()
    {
        $this->assertEquals('1__$_001_', $this->_helper->stringToFilename('1:_$&001#'));
    }
}