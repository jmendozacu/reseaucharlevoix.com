<?php
/**
 * Core License Block Class
 * 
 * @category Vianetz
 * @package Core
 * @author Christoph Massmann <C.Massmann@vianetz.com>
 */

class Vianetz_Core_Block_Pear extends Mage_Adminhtml_Block_Template
{ 
	protected function _toHtml()
	{
		$result = $this->getData('result');
		$html = '<div class="content-header"><h3 class="icon-head head-system-account">Vianetz Software Update</h3></div>';
		$html .= '<div>' . $result . '</div>';
		
		return $html;
	}
}