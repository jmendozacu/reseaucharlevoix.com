<?php

class Vianetz_AutomaticInvoice_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testCodePoolCommunity()
    {
        $this->assertModuleCodePool('community');
    }

    public function testModuleVersion()
    {
        $this->assertModuleVersion('1.0.7');
    }

    public function testModuleDependencies()
    {
        $this->assertModuleDepends('Mage_Core');
        $this->assertModuleDepends('Mage_Sales');
        $this->assertModuleDepends('Vianetz_Core');
    }

    public function testHelperAliases()
    {
        $this->assertHelperAlias('automaticinvoice', 'Vianetz_AutomaticInvoice_Helper_Data');
    }

    public function testObserverDefinedForGeneratingInvoicesAndShipments()
    {
        $this->assertEventObserverDefined('global', 'sales_order_save_after', 'automaticinvoice/order_observer', 'generateInvoice', 'vianetz_automaticinvoice_order_observer_invoice');
    }

    public function testObserverDefinedForSendingInvoiceEmailToCustomer()
    {
        $this->assertEventObserverDefined('global', 'sales_order_invoice_pay', 'automaticinvoice/order_observer', 'sendInvoiceEmail', 'vianetz_automaticinvoice_order_observer_invoice_send_email');
    }
}