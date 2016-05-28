<?php

class MageWare_SalesGrids_Block_Adminhtml_Widget_Grid_Column_Renderer_List
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return nl2br($this->escapeHtml(parent::_getValue($row)));
    }

    public function renderExport(Varien_Object $row)
    {
        return str_replace("\n", ' ', parent::_getValue($row));
    }
}
