<?php

/**
 * Created by PhpStorm.
 * User: Nguyen
 * Date: 4/2/2016
 * Time: 10:05 PM
 */
class Benova_ToolboxDesigner_Block_Render_Info extends Varien_Data_Form_Element_Text
{
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        return $html."  <script>
        				$('".$this->getHtmlId()."').up().up().hide();
        				</script>";
    }

}