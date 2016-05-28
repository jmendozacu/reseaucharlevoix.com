<?php
/**
 * Core License Helper Class
 * 
 * @category Vianetz
 * @package Core
 * @author Christoph Massmann <C.Massmann@vianetz.com> 
 */

class Vianetz_Core_Helper_License extends Mage_Core_Helper_Abstract
{
	const LICENSE_SERVER_URL = "http://api.vianetz.com/";
		
	public function validateKey($configId, $lang) 
	{
	   	$client = new SoapClient(null, array(
      		'location' => self::LICENSE_SERVER_URL,
      		'uri'      => "urn://www.vianetz.com/LicenseCheck"));
	   	
	   	$configPath = str_replace('_', '/', $configId);
	   	$licenseConfig = explode(':', Mage::getStoreConfig($configPath));
	   	
	   	$configPath = $configPath . 'key';

		$version = explode(".", Mage::getConfig()->getNode('modules/' . $licenseConfig[1] . '/version'));
		$key = Mage::getStoreConfig($configPath);
    	return $client->__soapCall("validate",array($licenseConfig[0], $key, $_SERVER['HTTP_HOST'], $version, 'stable', 'en'));
	}
}