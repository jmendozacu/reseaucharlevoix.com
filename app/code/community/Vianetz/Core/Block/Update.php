<?php
/**
 * Core License Block Class
 * 
 * @category Vianetz
 * @package Core
 * @author Christoph Massmann <C.Massmann@vianetz.com>
 */

class Vianetz_Core_Block_Update extends Mage_Adminhtml_Block_Template
{ 
	protected function _toHtml()
	{
		$result = $this->getData('result');
		$html = '<div class="content-header"><h3 class="icon-head head-system-account">Vianetz License Check</h3></div>';
		$html .= '<table border="0" cellpadding="2" cellspacing="2" style=""><tr><td valign="top"><img src="' . $result->gfx . '" /></td><td> ' . $result->message;
		if ( $result->session != '' ) {
			$params = array('session' => urlencode($result->session), 'file' => urlencode($result->file));
			$html .= ' <a href="' . Mage::helper('adminhtml')->getUrl('*/vianetz/installUpgrade', $params) . '">Click here</a> to install the new setup package.';
		}
		$html .= '</td></tr></table>';
		
		return $html;
	}
}