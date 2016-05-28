<?php
/**
 * Core License Controller Class
 * 
 * @category Vianetz
 * @package Core
 * @author Christoph Massmann <C.Massmann@vianetz.com>
 */

class Vianetz_Core_VianetzController extends Mage_Adminhtml_Controller_Action
{
	const API_URL = "http://api.vianetz.com";
	
    public function checkLicenseAction()
    {
    	$lang = $this->getRequest()->getParam('locale');
    	$configId = $this->getRequest()->getParam('configid');
    	$productId = $this->getRequest()->getParam('productid');
    	$moduleName = $this->getRequest()->getParam('modulename');
    	$className = $this->getRequest()->getParam('classname');
		$result = Mage::helper('vianetz_core/license')->validateKey($configId, $lang);
        	
	    $this->loadLayout();
      	#$this->getLayout()->getBlock('root')->setTemplate("page/empty.phtml"); 
	    $this->getLayout()
		    ->getBlock('content')->append(
				$this->getLayout()->createBlock('vianetz_core/update')->setData('result', $result)->setData('lang', $lang)
					->setData('productid', $productId)->setData('modulename', $moduleName)->setData('classname',$className));
      	$this->renderLayout();
    }
    
    public function downloadSetupAction()
    {
    	$lang = $this->getRequest()->getParam('locale');
    	$productId = $this->getRequest()->getParam('productid');
    	$moduleName = $this->getRequest()->getParam('modulename');
    	$className = $this->getRequest()->getParam('classname');
		$result = Mage::helper('vianetz_core/license')->validateKey($productId, $moduleName, $className, $lang);

    	return $this->_prepareDownloadResponse($className . '_' . $result->version . '.zip', base64_decode($result->zipFile), 'application/zip');
    }
    
    protected function installUpgradeAction()
    {
    	$this->loadLayout();
      	#$this->getLayout()->getBlock('root')->setTemplate("page/empty.phtml");
      	
    	$url = self::API_URL . "/" . $this->getRequest()->getParam('session') . "/" . $this->getRequest()->getParam('file');
		
    	$output = "";
        try {
            ini_set('error_reporting', 0);
            $paths = explode(':', ini_get('include_path'));
            array_unshift($paths, './downloader');
            array_unshift($paths, './downloader/pearlib/php');
            ini_set('include_path', implode(':', $paths));

            require_once 'PEAR/Frontend.php';
            require_once 'Maged/Pear/Frontend.php';
            require_once 'Maged/Pear.php';
        } catch(Exception $e) {
            $output .= "ERROR: Failed to prepare PEAR: ".$e->getMessage() . "<br />";
            $this->getLayout()
		    ->getBlock('content')->append(
				$this->getLayout()->createBlock('vianetz_core/pear')->setData('result', $output));
      		$this->renderLayout();
      		return false;
        }

        $pear = new Maged_Pear();
        $params = array(
            'command' => 'install',
            'options' => array('force' => 1),
            'params' => array($url),
        );

        try {
            $run = new Maged_Model_Pear_Request($params);
            if($command = $run->get('command')) {
                $cmd = PEAR_Command::factory($command, $pear->getConfig());
                $result = $cmd->run($command, $run->get('options'), $run->get('params'));
                if(is_bool($result)) {
                	$output .= "Installation ";
                	$output .= ($result == true) ? 'successful.' : 'failed.';
                } else {
                	$output .= "ERROR: PEAR result: " . (string)$result . "<br />";
                }
                
                Mage::getConfig()->removeCache();
            } else {
                $output .= "ERROR: Maged_Model_Pear_Request failed.<br />";
            }
            $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('vianetz_core/pear')->setData('result', $output));
      		$this->renderLayout();
      		return true;
        } catch(Exception $e) {
            $output .= "ERROR: Failed to initialize PEAR: " . $e->getMessage() . "<br />";
            $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('vianetz_core/pear')->setData('result', $output));
      		$this->renderLayout();
            return false;
        }

        $output .= "ERROR: Upgrade failed by unknown error.";
        
        $this->getLayout()
		    ->getBlock('content')->append(
				$this->getLayout()->createBlock('vianetz_core/pear')->setData('result', $output));
      	$this->renderLayout();
        
        return false;
    }
    
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null)
    {
        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"')
            ->setHeader('Last-Modified', date('r'));

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }
}