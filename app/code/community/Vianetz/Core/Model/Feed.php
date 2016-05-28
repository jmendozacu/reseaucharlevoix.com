<?php
/**
 * AdminNotification Class
 * 
 * @category Vianetz
 * @package AdminNotification
 * @author Christoph Massmann <C.Massmann@vianetz.com>
 */
class Vianetz_Core_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_PATH_SETTING_FEED_URL = 'vianetz_core/settings/feedurl';

    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
            . Mage::getStoreConfig(self::XML_PATH_SETTING_FEED_URL);
        }
        return $this->_feedUrl;
    }
    
    public function observe() {
       $model  = Mage::getModel('vianetz_core/feed');
       $model->checkUpdate();
    }

    public function getLastUpdate()
        {
	        return Mage::app()->loadCache('vianetz_core_notifications_lastcheck');
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'vianetz_core_notifications_lastcheck');
         return $this;
    }

}
