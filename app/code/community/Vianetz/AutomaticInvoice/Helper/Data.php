<?php
/**
 * AutomaticInvoice Helper Class
 * 
 * @category Vianetz
 * @package AutomaticInvoice
 * @author Christoph Massmann <C.Massmann@vianetz.com>
 */
class Vianetz_AutomaticInvoice_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return active payment methods.
     *
     * @return array
     */
    public function getActivePaymentMethods()
	{
	   $payments = Mage::getSingleton('payment/config')->getActiveMethods();

	   $methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));

	   foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
	}

    /**
     * Sanitize string to filename by removing special chars.
     *
     * @param $filename
     *
     * @return string
     */
    public function stringToFilename($filename)
    {
        $dangeroursChars = array(' ', '"', "'", '&', "/", "\\", "?", "#", DS, ':');

        return str_replace($dangeroursChars, '_', $filename);
    }
}