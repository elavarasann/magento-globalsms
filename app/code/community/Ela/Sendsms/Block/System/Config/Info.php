<?php
class Ela_Sendsms_Block_System_Config_Info
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<div style="border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px;">
                    <h4>Magento SMS - Developed by Elavarasan N</h4>
					<p>Find out more <a href="http://about.me/Elavarasan" target="_blank">about me</a></p>
                    <p>If you have any questions or need any help, feel free to contact me at <a href="mailto:ela.youngking@gmail.com">Ela.youngking@gmail.com</a></p>
                </div>';

        return $html;
    }
}