<?php

class Vianetz_AutomaticInvoice_Test_Model_Order_ObserverTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @loadFixture invoiceWithNotifyEnabled.yaml
     * @doNotIndex
     */
    public function testInvoiceEmailSentIfInvoiceIsPaid()
    {
        /** @var Vianetz_AutomaticInvoice_Model_Order_Observer $observer */
        $observer = Mage::getModel('automaticinvoice/order_observer');

        $invoiceMock = $this->getModelMock('sales/order_invoice', array('sendEmail', 'save', 'getState', 'getEmailSent'));
        $invoiceMock->expects($this->once())->method('sendEmail')->will($this->returnValue(null));
        $invoiceMock->expects($this->any())->method('save')->will($this->returnValue(null));
        $invoiceMock->expects($this->once())->method('getState')->will($this->returnValue(Mage_Sales_Model_Order_Invoice::STATE_PAID));
        $invoiceMock->expects($this->once())->method('getEmailSent')->will($this->returnValue(false));

        $event = $this->getMock('Varien_Event', array('getInvoice'));
        $event->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $eventObserver = $this->getMock('Varien_Event_Observer', array('getEvent'));
        $eventObserver->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $observer->sendInvoiceEmail($eventObserver);
    }

    /**
     * @loadFixture invoiceWithoutNotifyEnabled.yaml
     * @doNotIndex
     */
    public function testInvoiceEmailNotSentIfConfigDisabled()
    {
        /** @var Vianetz_AutomaticInvoice_Model_Order_Observer $observer */
        $observer = Mage::getModel('automaticinvoice/order_observer');

        $invoiceMock = $this->getModelMock('sales/order_invoice', array('sendEmail', 'save', 'getState', 'getEmailSent'));
        $invoiceMock->expects($this->never())->method('sendEmail')->will($this->returnValue(null));
        $invoiceMock->expects($this->any())->method('save')->will($this->returnValue(null));
        $invoiceMock->expects($this->once())->method('getState')->will($this->returnValue(Mage_Sales_Model_Order_Invoice::STATE_PAID));
        $invoiceMock->expects($this->never())->method('getEmailSent')->will($this->returnValue(false));

        $event = $this->getMock('Varien_Event', array('getInvoice'));
        $event->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $eventObserver = $this->getMock('Varien_Event_Observer', array('getEvent'));
        $eventObserver->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $observer->sendInvoiceEmail($eventObserver);
    }

    /**
     * @loadFixture invoiceWithoutNotifyEnabled.yaml
     * @doNotIndex
     */
    public function testInvoiceEmailNotSentIfInvoiceStateOpen()
    {
        /** @var Vianetz_AutomaticInvoice_Model_Order_Observer $observer */
        $observer = Mage::getModel('automaticinvoice/order_observer');

        $invoiceMock = $this->getModelMock('sales/order_invoice', array('sendEmail', 'save', 'getState', 'getEmailSent'));
        $invoiceMock->expects($this->never())->method('sendEmail')->will($this->returnValue(null));
        $invoiceMock->expects($this->any())->method('save')->will($this->returnValue(null));
        $invoiceMock->expects($this->once())->method('getState')->will($this->returnValue(Mage_Sales_Model_Order_Invoice::STATE_OPEN));
        $invoiceMock->expects($this->never())->method('getEmailSent')->will($this->returnValue(false));

        $event = $this->getMock('Varien_Event', array('getInvoice'));
        $event->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $eventObserver = $this->getMock('Varien_Event_Observer', array('getEvent'));
        $eventObserver->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $observer->sendInvoiceEmail($eventObserver);
    }
}