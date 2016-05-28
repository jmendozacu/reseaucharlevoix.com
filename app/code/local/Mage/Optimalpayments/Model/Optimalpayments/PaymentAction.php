<?php

class Mage_Optimalpayments_Model_Optimalpayments_PaymentAction
{
	public function toOptionArray()
	{
		return array(
			array(
				'value' => Mage_Optimalpayments_Model_PaymentMethod::ACTION_AUTHORIZE_CAPTURE, //'ccPurchase', //
				'label' => Mage::helper('paygate')->__('Authorize and Capture')
			),
			array(
				'value' => Mage_Optimalpayments_Model_PaymentMethod::ACTION_AUTHORIZE, //'ccAuthorize', //
				'label' => Mage::helper('paygate')->__('Authorize')
			)
		);
	}
}